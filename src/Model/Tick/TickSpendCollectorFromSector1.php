<?php
namespace DieEwigen\DE2\Model\Tick;

class TickSpendCollectorFromSector1
{
    private \mysqli $db;

    public function __construct(\mysqli $db)
    {
        $this->db = $db;
    }

    /**
     * Führt einen Kollektor-Transfer von Sektor 1 zu einem Spieler außerhalb von Sektor 1 aus.
     *
     * @return array{success:bool, from?:int, to?:int, reason?:string}
     */
    public function run(): array
    {
        // Kandidat aus Sektor 1 mit >25 Kollektoren und passendem Login-Status
        $sql = "SELECT d.* FROM `de_user_data` d
                LEFT JOIN `de_login` l ON(l.user_id = d.user_id)
                WHERE d.sector = 1 AND d.col > 25 AND l.status = 3 AND l.delmode = 2
                ORDER BY d.col DESC
                LIMIT 1";
        $res = mysqli_execute_query($this->db, $sql, []);
        if ($res === false || mysqli_num_rows($res) !== 1) {
            return ['success' => false, 'reason' => 'no_candidate'];
        }
        $from = mysqli_fetch_array($res);

        // Empfänger (Spieler außerhalb von Sektor 1 mit den wenigsten Kollektoren)
        $sql = "SELECT * FROM `de_user_data` WHERE sector > 1 AND npc = 0 ORDER BY col ASC LIMIT 1";
        $resx = mysqli_execute_query($this->db, $sql, []);
        if ($resx === false || mysqli_num_rows($resx) !== 1) {
            return ['success' => false, 'reason' => 'no_target'];
        }
        $to = mysqli_fetch_array($resx);

        // Abziehen, informieren
        mysqli_execute_query($this->db, "UPDATE de_user_data SET col = col - 1, newnews = 1 WHERE user_id = ?", [$from['user_id']]);
        $time = date('YmdHis');
        $msgFrom = 'Du verlierst einen Kollektor an einen anderen Spieler außerhalb von Sektor 1.';
        mysqli_execute_query($this->db, "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 3, ?, ?)", [$from['user_id'], $time, $msgFrom]);

        // Draufpacken, informieren
        mysqli_execute_query($this->db, "UPDATE de_user_data SET col = col + 1, newnews = 1 WHERE user_id = ?", [$to['user_id']]);
        $msgTo = 'Du erhältst einen Kollektor von einem anderen Spieler aus Sektor 1.';
        mysqli_execute_query($this->db, "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 3, ?, ?)", [$to['user_id'], $time, $msgTo]);

        return ['success' => true, 'fromUser' => (int)$from['user_id'], 'toUser' => (int)$to['user_id']];
    }
}
