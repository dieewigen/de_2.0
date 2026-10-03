<?php
namespace DieEwigen\DE2\Model\Thanatos;

use DieEwigen\DE2\Model\SpecialSystem\SpecialSystemData;
use DieEwigen\DE2\Model\SpecialSystem\Storage;

/**
 * Der Pfad des Thanatos (Spezialsystem 7 der Vergessenen Systeme): persönlicher Aufstieg über die Runde.
 *
 * Spieler bringen Handelswaren V (Item 18) dar und steigen Stufe für Stufe auf. Jede Stufe zahlt einmalig
 * Palenium und gibt bis Rundenende mehr Industrie-Ertrag (tickler/wt_manage_map.php) und kürzere Bauzeit
 * (vs_duration_factor()) in den VS. Die Stufe steht in de_user_map.specialsystem_data und verschwindet mit
 * der Karte beim Rundenreset.
 *
 * Kein setLock(): map_system.php hält die Spielersperre für die ganze Seite.
 */
class ThanatosService
{
    public const SPECIAL_SYSTEM_ID = 7;
    public const ITEM_ID = 18;
    public const PALENIUM_ITEM_ID = 1;

    private \mysqli $db;
    private SpecialSystemData $data;

    public function __construct(\mysqli $db)
    {
        $this->db = $db;
        $this->data = new SpecialSystemData($db, self::SPECIAL_SYSTEM_ID);
    }

    //Konfiguration, überschreibbar in sv.inc.php
    public static function getMaxStufe(): int
    {
        return max(1, (int)($GLOBALS['sv_thanatos_max_stufe'] ?? 10));
    }

    public static function getKostenFaktor(): int
    {
        return max(1, (int)($GLOBALS['sv_thanatos_kosten_faktor'] ?? 3));
    }

    public static function getPaleniumProStufe(): int
    {
        return max(0, (int)($GLOBALS['sv_thanatos_palenium_pro_stufe'] ?? 50));
    }

    public static function getIndustrieProStufe(): int
    {
        return max(0, (int)($GLOBALS['sv_thanatos_industrie_pro_stufe'] ?? 5));
    }

    public static function getBauzeitProStufe(): int
    {
        return max(0, (int)($GLOBALS['sv_thanatos_bauzeit_pro_stufe'] ?? 3));
    }

    //Kosten und Belohnung für das Erreichen von Stufe $stufe
    public static function getKosten(int $stufe): int
    {
        return $stufe * self::getKostenFaktor();
    }

    public static function getPalenium(int $stufe): int
    {
        return $stufe * self::getPaleniumProStufe();
    }

    //Boni bis Rundenende bei Stufe $stufe
    public static function getIndustrieProzent(int $stufe): int
    {
        return max(0, $stufe) * self::getIndustrieProStufe();
    }

    public static function getBauzeitProzent(int $stufe): int
    {
        return max(0, $stufe) * self::getBauzeitProStufe();
    }

    public function getSpecialSystemData(): SpecialSystemData
    {
        return $this->data;
    }

    public function getStufe(int $uid): int
    {
        return (int)($this->data->get($uid)['stufe'] ?? 0);
    }

    public function getStock(int $uid): int
    {
        return Storage::getStock($this->db, $uid, self::ITEM_ID);
    }

    /**
     * Stufen aller Spieler, für den Wirtschaftstick.
     *
     * @return array<int,int> user_id => Stufe
     */
    public function loadAllStufen(): array
    {
        $stufen = [];
        foreach ($this->data->loadAll() as $uid => $d) {
            if ((int)($d['stufe'] ?? 0) > 0) {
                $stufen[$uid] = min((int)$d['stufe'], self::getMaxStufe());
            }
        }
        return $stufen;
    }

    /**
     * @return string leer, wenn erlaubt, sonst der Schlüssel der Fehlermeldung in $thanatos_lang
     */
    public function checkLevelUp(int $uid): string
    {
        $res = mysqli_execute_query($this->db, "SELECT l.status, d.npc FROM de_login l LEFT JOIN de_user_data d ON (d.user_id = l.user_id) WHERE l.user_id = ?", [$uid]);
        $row = mysqli_fetch_assoc($res);
        if (!$row || (int)$row['status'] !== 1 || (int)$row['npc'] !== 0) {
            return 'kein_zugang_npc';
        }
        if (!$this->data->isUnlocked($uid)) {
            return 'fehler_verbindung';
        }
        if ($this->getStufe($uid) >= self::getMaxStufe()) {
            return 'fehler_max';
        }
        return '';
    }

    /**
     * Handelswaren V darbringen und eine Stufe aufsteigen.
     *
     * @return array{ok:bool, msg:string, stufe:int}
     */
    public function levelUp(int $uid, array $lang): array
    {
        $err = $this->checkLevelUp($uid);
        if ($err !== '') {
            return ['ok' => false, 'msg' => $lang[$err], 'stufe' => $this->getStufe($uid)];
        }

        $row = $this->data->getRow($uid);
        $data = SpecialSystemData::decode($row['specialsystem_data']);
        $stufe = (int)($data['stufe'] ?? 0);
        $neu = $stufe + 1;
        $kosten = self::getKosten($neu);

        if (!Storage::take($this->db, $uid, self::ITEM_ID, $kosten)) {
            return ['ok' => false, 'msg' => strtr($lang['fehler_lager'], ['{KOSTEN}' => $kosten]), 'stufe' => $stufe];
        }

        //nur aufsteigen, wenn die Stufe sich seit dem Lesen nicht geändert hat, sonst zurückbuchen
        $data['stufe'] = $neu;
        if (!$this->data->replace($uid, $row['specialsystem_data'], $data)) {
            Storage::give($uid, self::ITEM_ID, $kosten);
            return ['ok' => false, 'msg' => $lang['fehler_erneut'], 'stufe' => $stufe];
        }

        $palenium = self::getPalenium($neu);
        Storage::give($uid, self::PALENIUM_ITEM_ID, $palenium);

        //das Ende des Pfades ist eine Meldung im Serverchat wert
        if ($neu >= self::getMaxStufe()) {
            $res = mysqli_execute_query($this->db, "SELECT spielername FROM de_user_data WHERE user_id = ?", [$uid]);
            $name = htmlspecialchars((string)(mysqli_fetch_assoc($res)['spielername'] ?? ''), ENT_QUOTES, 'UTF-8');
            \insert_chat_msg_admin(0, 2, '', '<span style="color: #802ec1;">'.strtr($lang['chat_max'], ['{NAME}' => $name]).'</span>', 0, $GLOBALS['sv_server_tag'] ?? '');
        }

        return [
            'ok' => true,
            'msg' => strtr($lang['erfolg'], ['{STUFE}' => $neu, '{PALENIUM}' => self::formatNumber($palenium)]),
            'stufe' => $neu,
        ];
    }

    public static function formatNumber(int $value): string
    {
        return number_format($value, 0, ',', '.');
    }
}
