<?php
namespace DieEwigen\DE2\Model\SpecialSystem;

/**
 * Spielerdaten eines Spezialsystems der Vergessenen Systeme (de_user_map.specialsystem_data, serialisiertes Array).
 *
 * Wie SiegelService::isUnlocked(): prepared statements und unserialize ohne Klassen. Die Spalte ist NULL
 * (automatische Erkundung) oder '' (manuelle Erkundung), solange das System noch keine Daten hat.
 */
class SpecialSystemData
{
    private \mysqli $db;
    private int $specialSystemId;
    private ?int $mapId = null;

    public function __construct(\mysqli $db, int $specialSystemId)
    {
        $this->db = $db;
        $this->specialSystemId = $specialSystemId;
    }

    public function getMapId(): int
    {
        if ($this->mapId === null) {
            $res = mysqli_execute_query($this->db, "SELECT id FROM de_map_objects WHERE system_typ = 5 AND system_subtyp = ?", [$this->specialSystemId]);
            $this->mapId = (int)(mysqli_fetch_assoc($res)['id'] ?? 0);
        }
        return $this->mapId;
    }

    /**
     * Zeile des Spielers, null wenn er das System noch nicht erkundet hat.
     */
    public function getRow(int $uid): ?array
    {
        $res = mysqli_execute_query($this->db, "SELECT known_since, specialsystem_data FROM de_user_map WHERE user_id = ? AND map_id = ?", [$uid, $this->getMapId()]);
        $row = mysqli_fetch_assoc($res);
        if (!$row || (int)$row['known_since'] <= 0 || (int)$row['known_since'] >= time()) {
            return null;
        }
        return $row;
    }

    public static function decode(?string $raw): array
    {
        $data = ($raw !== null && $raw !== '') ? unserialize($raw, ['allowed_classes' => false]) : [];
        return is_array($data) ? $data : [];
    }

    public function get(int $uid): array
    {
        $row = $this->getRow($uid);
        return $row ? self::decode($row['specialsystem_data']) : [];
    }

    /**
     * Kontakt hergestellt, also Phase 1 erreicht (wie bei Ares, Hephaistos und dem Siegel).
     */
    public function isUnlocked(int $uid): bool
    {
        return (int)($this->get($uid)['phase'] ?? 0) >= 1;
    }

    /**
     * Phase 0 -> 1. Gibt false zurück, wenn das System nicht erkundet ist.
     */
    public function unlock(int $uid, array $initData): bool
    {
        $row = $this->getRow($uid);
        if (!$row) {
            return false;
        }
        $data = self::decode($row['specialsystem_data']);
        if ((int)($data['phase'] ?? 0) >= 1) {
            return true;
        }
        $data = array_merge($initData, $data, ['phase' => 1]);
        return $this->replace($uid, $row['specialsystem_data'], $data);
    }

    /**
     * Schreibt die Daten nur, wenn sich der Rohwert seit dem Lesen nicht geändert hat.
     */
    public function replace(int $uid, ?string $oldRaw, array $data): bool
    {
        mysqli_execute_query($this->db, "UPDATE de_user_map SET specialsystem_data = ? WHERE user_id = ? AND map_id = ? AND specialsystem_data <=> ?", [serialize($data), $uid, $this->getMapId(), $oldRaw]);
        return mysqli_affected_rows($this->db) === 1;
    }

    /**
     * Daten aller Spieler, die das System erkundet haben, für den Wirtschaftstick.
     *
     * @return array<int,array> user_id => Daten
     */
    public function loadAll(): array
    {
        $res = mysqli_execute_query($this->db, "SELECT user_id, specialsystem_data FROM de_user_map WHERE map_id = ?", [$this->getMapId()]);
        $all = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $all[(int)$row['user_id']] = self::decode($row['specialsystem_data']);
        }
        return $all;
    }
}
