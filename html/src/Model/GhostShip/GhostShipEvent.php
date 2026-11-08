<?php
namespace DieEwigen\DE2\Model\GhostShip;

/**
 * Geisterschiff „Van der Decken“: läuft jedes Jahr zu Halloween von selbst, ohne Ton und ohne Wirkung aufs Spiel.
 *
 * Am 31.10. lost der erste WT ab 0 Uhr acht Sektoren mit aktiven Spielern aus, durch die das Schiff bis 18 Uhr zieht
 * (Zeile in sector.php, je Station ein Funkfetzen im Serverchat). Ab 18 Uhr liegt es vor Sektor 666, und der Funkspruch
 * wird eingeblendet (js/geisterschiff.js). Den ganzen Tag ist die Ansicht von Sektor 666 gestört. Nach Mitternacht
 * meldet der WT „Signal verloren“. Der Ablauf hängt am Kalender, damit er auf allen Servern gleich ist.
 *
 * Zum Testen in sv.inc.php überschreibbar: sv_geist_aktiv, sv_geist_tag ('10-31'), sv_geist_start, sv_geist_ankunft.
 * Route und gemeldete Station liegen in cache/geisterschiff/zustand.json (cache/.htaccess sperrt den Ordner).
 */
class GhostShipEvent
{
    public const ZIEL = 666;
    private const STATIONEN = 8;

    private \mysqli $db;
    private string $datei;
    private int $jetzt;
    private array $zustand;
    private ?array $texte = null;

    public function __construct(\mysqli $db, ?string $dir = null, ?int $jetzt = null)
    {
        $this->db = $db;
        $this->datei = ($dir ?? dirname(__DIR__, 3).'/cache/geisterschiff').'/zustand.json';
        $this->jetzt = $jetzt ?? time();
        $json = is_file($this->datei) ? json_decode((string)file_get_contents($this->datei), true) : null;
        $this->zustand = (is_array($json) ? $json : []) + ['tag' => '', 'route' => [], 'schritt' => -1, 'ende' => false];
    }

    //Texte aus inc/lang/<sprache>_geisterschiff.lang.php
    public function texte(): array
    {
        if ($this->texte === null) {
            $geist_lang = [];
            include dirname(__DIR__, 3).'/inc/lang/'.($GLOBALS['sv_server_lang'] ?? 1).'_geisterschiff.lang.php';
            $this->texte = $geist_lang;
        }
        return $this->texte;
    }

    //Zeitpunkt am Event-Tag dieses Jahres; zeit(24) ist die Mitternacht danach
    private function zeit(int $stunde): int
    {
        [$monat, $tag] = explode('-', $GLOBALS['sv_geist_tag'] ?? '10-31');
        return mktime($stunde, 0, 0, (int)$monat, (int)$tag, (int)date('Y', $this->jetzt));
    }

    private function start(): int
    {
        return $this->zeit((int)($GLOBALS['sv_geist_start'] ?? 0));
    }

    private function laeuft(): bool
    {
        return ($GLOBALS['sv_geist_aktiv'] ?? true) && $this->jetzt >= $this->start() && $this->jetzt < $this->zeit(24);
    }

    //der Event-Tag läuft und der WT hat die Route dafür schon ausgelost
    public function isActive(): bool
    {
        return $this->laeuft() && $this->zustand['tag'] === date('Y-m-d', $this->start()) && !empty($this->zustand['route']);
    }

    //Station 0 bis 7, 8 heißt vor Sektor 666; -1 außerhalb des Events
    public function getStep(): int
    {
        if (!$this->isActive()) {
            return -1;
        }
        $anzahl = count($this->zustand['route']);
        $ankunft = $this->zeit((int)($GLOBALS['sv_geist_ankunft'] ?? 18));
        if ($this->jetzt >= $ankunft) {
            return $anzahl;
        }
        return min($anzahl - 1, intdiv($this->jetzt - $this->start(), max(1, intdiv($ankunft - $this->start(), $anzahl))));
    }

    public function hasArrived(): bool
    {
        return $this->isActive() && $this->getStep() === count($this->zustand['route']);
    }

    public function getCurrentSector(): ?int
    {
        $step = $this->getStep();
        return $step < 0 ? null : (int)($this->zustand['route'][$step] ?? self::ZIEL);
    }

    //Daten für js/geisterschiff.js; der Tag (z. B. 20261031) ist die Kennung für „schon gesehen“
    public function clientConfig(): array
    {
        $t = $this->texte();
        return [
            'signal' => (int)date('Ymd', $this->start()),
            'live' => $this->hasArrived(),
            'kopf' => $t['signal_kopf'],
            'kennung' => $t['signal_zeilen'],
            'nachricht' => $t['nachricht'],
            'hinweis' => $t['signal_beenden'],
        ];
    }

    /**
     * WT: lost am Event-Tag die Route aus, meldet jede neue Station im Chat (nur die aktuelle, falls WT ausgefallen
     * sind) und danach einmal „Signal verloren“.
     */
    public function processWt(): void
    {
        if ($this->laeuft()) {
            $tag = date('Y-m-d', $this->start());
            if ($this->zustand['tag'] !== $tag) {
                $this->speichern(['tag' => $tag, 'route' => $this->auslosen(), 'schritt' => -1, 'ende' => false]);
            }
            $step = $this->getStep();
            if ($step > $this->zustand['schritt']) {
                $this->speichern(['schritt' => $step]);
                $sektor = preg_replace('/(\d)/u', "$1\u{0337}", (string)$this->getCurrentSector(), 1);
                $this->chat(strtr($this->texte()['funk'][$step] ?? $this->texte()['funk_ziel'], ['{SEK}' => $sektor]));
            }
        } elseif ($this->zustand['tag'] !== '' && !$this->zustand['ende']) {
            $this->speichern(['ende' => true]);
            $this->chat($this->texte()['funk_ende']);
        }
    }

    //Sektoren mit aktiven Spielern (de_sector.npc reicht nicht, siehe Sektor666Service); bei weniger als acht
    //kommt das Schiff mehrmals vorbei, aber nie zweimal hintereinander in denselben
    private function auslosen(): array
    {
        $res = mysqli_execute_query($this->db, "SELECT DISTINCT d.sector FROM de_user_data d JOIN de_login l ON l.user_id = d.user_id WHERE d.npc = 0 AND l.status = 1 AND d.sector >= 1 AND d.sector <> ?", [self::ZIEL]);
        $sektoren = array_map('intval', array_column(mysqli_fetch_all($res, MYSQLI_ASSOC), 'sector'));
        shuffle($sektoren);
        $route = [];
        for ($i = 0; $i < self::STATIONEN && $sektoren; $i++) {
            $route[] = $sektoren[$i % count($sektoren)];
        }
        return $route;
    }

    //Serverchat ohne Discord; der Absender steht im Text, damit es keinen Spielerlink gibt
    private function chat(string $text): void
    {
        $html = '<span style="color: #9ff3d6; font-style: italic;">???: '.htmlspecialchars($text, ENT_QUOTES, 'UTF-8').'</span>';
        mysqli_execute_query($this->db, "INSERT INTO de_chat_msg (channel, channeltyp, spielername, message, timestamp, owner_id) VALUES (0, 2, '', ?, ?, 0)", [$html, $this->jetzt]);
    }

    //nur der WT schreibt; über eine Zwischendatei, damit Seitenaufrufe nie eine halbe Datei lesen
    private function speichern(array $aenderung): void
    {
        $this->zustand = $aenderung + $this->zustand;
        if (!is_dir(dirname($this->datei))) {
            mkdir(dirname($this->datei), 0775, true);
        }
        file_put_contents($this->datei.'.tmp', json_encode($this->zustand));
        rename($this->datei.'.tmp', $this->datei);
    }
}
