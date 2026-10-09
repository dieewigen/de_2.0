<?php
namespace DieEwigen\DE2\Model\Chat;

/**
 * Schreibkanal des Chats: welcher Kanal dem Spieler offensteht und welcher zuletzt gewählt war
 * (de_user_data.chatchannel). Genutzt von chat.php (Start) und de_ajaxrpc.php (Reiterklick).
 *
 * Verwendung:
 *   $wahl = new ChannelChoice($GLOBALS['dbi']);
 *   $info = $wahl->playerInfo($uid);
 *   $kanal = $wahl->sanitize($info, $info['chatchannel'], ChannelChoice::SEKTOR);
 */
class ChannelChoice
{
    //wie channeltyp in de_chat_msg
    public const SEKTOR = 0;
    public const ALLIANZ = 1;
    public const SERVER = 2;
    public const GLOBAL = 3;

    private \mysqli $db;

    public function __construct(\mysqli $db)
    {
        $this->db = $db;
    }

    /**
     * Chatdaten des Spielers in einer Abfrage.
     *
     * @return array{sector:int, allytag:string, chatoffallg:int, chatoffglobal:int, chatchannel:int}
     *         allytag ist '' ohne Mitgliedschaft (kein Tag oder noch Bewerber, status != 1)
     */
    public function playerInfo(int $uid): array
    {
        $res = mysqli_execute_query($this->db, "SELECT sector, allytag, status, chatoffallg, chatoffglobal, chatchannel FROM de_user_data WHERE user_id=?", [$uid]);
        $row = mysqli_fetch_assoc($res) ?: [];

        $allytag = (string)($row['allytag'] ?? '');
        if ($allytag !== '' && (int)($row['status'] ?? 0) !== 1) {
            $allytag = '';
        }

        return [
            'sector' => (int)($row['sector'] ?? 0),
            'allytag' => $allytag,
            'chatoffallg' => (int)($row['chatoffallg'] ?? 0),
            'chatoffglobal' => (int)($row['chatoffglobal'] ?? 0),
            'chatchannel' => (int)($row['chatchannel'] ?? self::SEKTOR),
        ];
    }

    /**
     * Gewünschten Kanal prüfen: Allianz nur als Mitglied, Server und Global nur, wenn nicht in den Optionen
     * abgeschaltet. Sonst $fallback; ein Wert außerhalb 0..3 ergibt Sektor.
     *
     * @param array $info aus playerInfo()
     */
    public function sanitize(array $info, int $wanted, int $fallback): int
    {
        if ($wanted < self::SEKTOR || $wanted > self::GLOBAL) {
            return self::SEKTOR;
        }
        if ($wanted === self::ALLIANZ && $info['allytag'] === '') {
            return $fallback;
        }
        if ($wanted === self::SERVER && $info['chatoffallg'] === 1) {
            return $fallback;
        }
        if ($wanted === self::GLOBAL && $info['chatoffglobal'] === 1) {
            return $fallback;
        }

        return $wanted;
    }

    /**
     * Die Wahl dauerhaft merken, damit sie den nächsten Login überdauert.
     */
    public function remember(int $uid, int $channel): void
    {
        mysqli_execute_query($this->db, "UPDATE de_user_data SET chatchannel=? WHERE user_id=?", [$channel, $uid]);
    }
}
