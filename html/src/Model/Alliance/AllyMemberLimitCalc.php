<?php
namespace DieEwigen\DE2\Model\Alliance;

class AllyMemberLimitCalc
{
    private \mysqli $db;
    private int $base;
    private int $usersPerStep;

    public function __construct(\mysqli $db, int $base = 5, int $usersPerStep = 20)
    {
        $this->db = $db;
        $this->base = $base;
        $this->usersPerStep = $usersPerStep;
    }

    public function calculateMemberLimit(): int
    {
        $userCount = $this->getUserCount();
        $maxFromDb = $this->getMaxMemberLimit();

        $memberlimit = (int) round($this->base + $userCount / $this->usersPerStep);
        if ($memberlimit < $maxFromDb) {
            $memberlimit = $maxFromDb;
        }

        return $memberlimit;
    }

    /**
     * Aktualisiert die Tabelle de_allys und gibt die Anzahl betroffener Zeilen zurück.
     */
    public function updateAlliesMemberLimit(): array
    {
        $memberlimit = $this->calculateMemberLimit();
        $sql = "UPDATE de_allys SET memberlimit = ?";
        // helper aus dem Projekt verwenden; gibt bei UPDATE/DELETE/INSERT kein Result-Objekt zurück
        mysqli_execute_query($this->db, $sql, [$memberlimit]);
     
        return ['memberlimit' => $memberlimit];
    }

    private function getUserCount(): int
    {
        $res = $this->db->query("SELECT COUNT(*) AS anzahl FROM de_user_data WHERE npc = 0 AND sector > 1");
        if ($res === false) {
            throw new \RuntimeException('Query failed: ' . $this->db->error);
        }
        $row = $res->fetch_assoc();
        return (int)($row['anzahl'] ?? 0);
    }

    private function getMaxMemberLimit(): int
    {
        $res = $this->db->query("SELECT MAX(memberlimit) AS max FROM de_allys");
        if ($res === false) {
            throw new \RuntimeException('Query failed: ' . $this->db->error);
        }
        $row = $res->fetch_assoc();
        return (int)($row['max'] ?? 0);
    }
}