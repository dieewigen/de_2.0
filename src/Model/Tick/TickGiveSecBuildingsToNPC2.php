<?php
namespace DieEwigen\DE2\Model\Tick;

class TickGiveSecBuildingsToNPC2
{
    private \mysqli $db;

    public function __construct(\mysqli $db)
    {
        $this->db = $db;
    }

    /**
     * überprüft Sektoren die nur NPC Typ 2 Spieler haben und gibt
     * ihnen die Sektorgebäude
     *
     * @return array{success:bool, from?:int, to?:int, reason?:string}
     */
    public function run(): array
    {
        try {
            // 1. Finde alle Sektoren, die NICHT leer sind
            $sectorQuery = "
                SELECT DISTINCT sector 
                FROM de_user_data 
                WHERE sector > 1 
                  AND npc IN (0, 2)
                GROUP BY sector
            ";
            $result = mysqli_query($this->db, $sectorQuery);
            
            if (!$result) {
                return [
                    'success' => false,
                    'reason' => 'Fehler beim Laden der Sektoren: ' . mysqli_error($this->db)
                ];
            }

            $affectedSectors = [];
            $processedCount = 0;

            // 2. Für jeden Sektor überprüfen ob NUR NPC Typ 2 drin sind
            while ($row = mysqli_fetch_assoc($result)) {
                $sector = $row['sector'];

                // Zähle normale Spieler (npc=0) in diesem Sektor
                $checkQuery = "
                    SELECT COUNT(*) as normal_count 
                    FROM de_user_data 
                    WHERE sector = ? 
                      AND npc = 0
                ";
                $checkResult = mysqli_execute_query($this->db, $checkQuery, [$sector]);
                $checkRow = mysqli_fetch_assoc($checkResult);

                // Wenn es keine normalen Spieler gibt, dann hat dieser Sektor nur NPC Typ 2
                if ($checkRow['normal_count'] == 0) {
                    // Zähle auch NPC Typ 2 Spieler (zur Sicherheit)
                    $npc2Query = "
                        SELECT COUNT(*) as npc2_count 
                        FROM de_user_data 
                        WHERE sector = ? 
                          AND npc = 2
                    ";
                    $npc2Result = mysqli_execute_query($this->db, $npc2Query, [$sector]);
                    $npc2Row = mysqli_fetch_assoc($npc2Result);

                    // Nur wenn mindestens 1 NPC Typ 2 im Sektor ist
                    if ($npc2Row['npc2_count'] > 0) {
                        // 3. Setze Sektorgebäude-Techs für den Sektor
                        // Tech-IDs 120-124 sind Sektorgebäude
                        // techs ist ein String s000000000 wo Position 1-5 für Gebäude stehen
                        $updateQuery = "
                            UPDATE de_sector 
                            SET techs = 's111110000' 
                            WHERE sec_id = ?
                        ";
                        mysqli_execute_query($this->db, $updateQuery, [$sector]);

                        $affectedSectors[] = $sector;
                        $processedCount++;
                    }
                }
            }

            return [
                'success' => true,
                'from' => min($affectedSectors ?: [0]),
                'to' => max($affectedSectors ?: [0]),
                'count' => $processedCount,
                'sectors' => implode(', ', $affectedSectors)
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'reason' => 'Exception: ' . $e->getMessage()
            ];
        }
    }
}
