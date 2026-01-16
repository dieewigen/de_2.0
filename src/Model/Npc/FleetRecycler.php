<?php

namespace DieEwigen\DE2\Model\Npc;
include('tickler/kt_einheitendaten.php');

use DieEwigen\DE2\Model\Npc\Types\FleetPreset;
use Exception;

class FleetRecycler
{
    private \mysqli $dbi;

    /**
     * Ship ID to Unit ID mapping
     */
    private const SHIP_ID_TO_UNIT_ID = [
        'HUNTER' => 81,
        'HUNTING_BOAT' => 82,
        'DESTROYER' => 83,
        'CRUISER' => 84,
        'BATTLESHIP' => 85,
        'BOMBER' => 86,
        'TRANSMITTER' => 87,
        'CARRIER' => 88,
        'FREIGHTER' => 89,
    ];

    private const float RECYCLING_MALUS = 0.05;

    /**
     * Tech scores for each unit (from kt_einheitendaten.php)
     */
    private array $TECH_SCORES;

    /**
     * Build times in ticks (from kt_einheitendaten.php)
     */
    private array $BUILD_TIMES;

    public function __construct()
    {
        global $unit;
        $this->dbi = $GLOBALS['dbi'];
        // init DX tech scores
        $this->TECH_SCORES = [
            81 => $unit[4][0][4],
            82 => $unit[4][1][4],
            83 => $unit[4][2][4],
            84 => $unit[4][3][4],
            85 => $unit[4][4][4],
            86 => $unit[4][5][4],
            87 => $unit[4][6][4],
            88 => $unit[4][7][4],
            89 => $unit[4][8][4],
        ];
        // init DX build times
        $this->BUILD_TIMES = [
            81 => $unit[4][0]['bz'],
            82 => $unit[4][1]['bz'],
            83 => $unit[4][2]['bz'],
            84 => $unit[4][3]['bz'],
            85 => $unit[4][4]['bz'],
            86 => $unit[4][5]['bz'],
            87 => $unit[4][6]['bz'],
            88 => $unit[4][7]['bz'],
            89 => $unit[4][8]['bz'],
        ];
    }

    /**
     * Recycle NPC fleet to match a target preset configuration
     *
     * @param int $npcUserId The NPC user ID to recycle
     * @param FleetPreset $targetPreset The target preset with unit ratios
     * @return array ['total_build_orders' => int, 'total_points_recycled' => int]
     * @throws Exception
     */
    public function recycleFleetToPreset(int $npcUserId, FleetPreset $targetPreset): array
    {
        try {
            // Fetch current NPC fleet
            $currentFleet = $this->getCurrentFleet($npcUserId);

            // Calculate total fleet points before recycling
            $totalPoints = $this->calculateTotalFleetPoints($currentFleet);

            if ($totalPoints <= 0) {
                return ['total_build_orders' => 0, 'total_points_recycled' => 0];
            }

            // Apply recycling malus
            $recycledPoints = (int)($totalPoints * (1 - self::RECYCLING_MALUS));

            // Parse preset ratios
            $presetData = $this->parsePresetRatios($targetPreset);

            // Validate preset
            if (!$this->validatePreset($presetData)) {
                throw new Exception("Invalid preset configuration");
            }

            // Calculate conversions based on recycled points
            $conversions = $this->calculateConversions($recycledPoints, $presetData);

            // Insert all build orders
            $buildOrderCount = 0;
            foreach ($conversions['buildOrders'] as $order) {
                if ($this->insertBuildOrder($npcUserId, $order['unitId'], $order['amount'], $order['buildTime'])) {
                    $buildOrderCount++;
                }
            }

            // Clear NPC fleet
            $this->clearNpcFleet($npcUserId);

            return [
                'total_build_orders' => $buildOrderCount,
                'total_points_recycled' => $recycledPoints
            ];

        } catch (Exception $e) {
            throw new Exception("Fleet recycling failed: " . $e->getMessage());
        }
    }

    /**
     * Fetch current fleet composition from database
     *
     * @param int $npcUserId
     * @return array ['e81' => amount, 'e82' => amount, ...]
     * @throws Exception
     */
    private function getCurrentFleet(int $npcUserId): array
    {
        $fleetId = $npcUserId . '-0';
        $result = mysqli_execute_query(
            $this->dbi,
            "SELECT e81, e82, e83, e84, e85, e86, e87, e88, e89, e90 FROM de_user_fleet WHERE user_id = ?",
            [$fleetId]
        );

        if (!$result || $result->num_rows === 0) {
            return [
                'e81' => 0, 'e82' => 0, 'e83' => 0, 'e84' => 0, 'e85' => 0,
                'e86' => 0, 'e87' => 0, 'e88' => 0, 'e89' => 0, 'e90' => 0
            ];
        }

        $row = $result->fetch_assoc();
        return [
            'e81' => (int)$row['e81'],
            'e82' => (int)$row['e82'],
            'e83' => (int)$row['e83'],
            'e84' => (int)$row['e84'],
            'e85' => (int)$row['e85'],
            'e86' => (int)$row['e86'],
            'e87' => (int)$row['e87'],
            'e88' => (int)$row['e88'],
            'e89' => (int)$row['e89'],
            'e90' => (int)$row['e90']
        ];
    }

    /**
     * Calculate total fleet points (excluding TITAN)
     *
     * @param array $currentFleet
     * @return int
     */
    private function calculateTotalFleetPoints(array $currentFleet): int
    {
        $total = 0;

        // Iterate through all units except TITAN (e90)
        foreach ($this->TECH_SCORES as $unitId => $score) {
            $key = 'e' . $unitId;
            if (isset($currentFleet[$key]) && $currentFleet[$key] > 0) {
                $total += $currentFleet[$key] * $score;
            }
        }

        return $total;
    }

    /**
     * Parse preset unit ratios and find base unit
     *
     * @param FleetPreset $preset
     * @return array ['units' => [...], 'minRatio' => float, 'baseShipId' => string]
     * @throws Exception
     */
    private function parsePresetRatios(FleetPreset $preset): array
    {
        $shipRatios = $preset->getShipRatios();

        if (empty($shipRatios)) {
            throw new Exception("Preset contains no ship ratios");
        }

        // Filter out TITAN and invalid ships
        $validRatios = [];
        $minRatio = PHP_INT_MAX;
        $baseShipId = null;

        foreach ($shipRatios as $shipId => $ratio) {
            // Skip TITAN
            if ($shipId === 'TITAN') {
                continue;
            }

            // Validate ship ID exists in mapping
            if (!isset(self::SHIP_ID_TO_UNIT_ID[$shipId])) {
                error_log("Warning: Unknown ship ID in preset: $shipId");
                continue;
            }

            // Validate ratio is positive
            if ($ratio <= 0) {
                continue;
            }

            $validRatios[$shipId] = (float)$ratio;

            // Track minimum ratio and base ship
            if ($ratio < $minRatio) {
                $minRatio = $ratio;
                $baseShipId = $shipId;
            }
        }

        if (empty($validRatios)) {
            throw new Exception("Preset contains no valid ships (TITAN is excluded)");
        }

        return [
            'units' => $validRatios,
            'minRatio' => $minRatio,
            'baseShipId' => $baseShipId
        ];
    }

    /**
     * Calculate conversion amounts for each preset unit
     *
     * @param int $recycledPoints Total points available for recycling (budget constraint)
     * @param array $presetData Parsed preset data
     * @return array ['buildOrders' => [...], 'totalPoints' => int]
     */
    private function calculateConversions(int $recycledPoints, array $presetData): array
    {
        $units = $presetData['units'];
        $minRatio = $presetData['minRatio'];

        // Get base unit ID and score

        // Calculate the total cost of one complete fleet set
        // (all units scaled to their ratios relative to minRatio)
        $totalCostPerSet = 0;
        foreach ($units as $shipId => $ratio) {
            $unitId = self::SHIP_ID_TO_UNIT_ID[$shipId];
            $unitScore = $this->TECH_SCORES[$unitId];
            // Amount of this unit in one set: ratio / minRatio
            $amountInSet = $ratio / $minRatio;
            $totalCostPerSet += $amountInSet * $unitScore;
        }

        if ($totalCostPerSet <= 0) {
            return ['buildOrders' => [], 'totalPoints' => 0];
        }

        // Calculate how many complete fleet sets we can build with recycledPoints
        $numSets = (int)floor($recycledPoints / $totalCostPerSet);

        if ($numSets <= 0) {
            return ['buildOrders' => [], 'totalPoints' => 0];
        }

        // Calculate amounts for each preset unit based on number of complete sets
        $buildOrders = [];
        $totalPoints = 0;

        foreach ($units as $shipId => $ratio) {
            $unitId = self::SHIP_ID_TO_UNIT_ID[$shipId];
            $unitScore = $this->TECH_SCORES[$unitId];
            $buildTime = $this->BUILD_TIMES[$unitId];

            // Amount = numSets × (ratio / minRatio)
            $amount = (int)floor($numSets * ($ratio / $minRatio));

            if ($amount > 0) {
                $unitPoints = $amount * $unitScore;
                $totalPoints += $unitPoints;

                $buildOrders[] = [
                    'unitId' => $unitId,
                    'amount' => $amount,
                    'buildTime' => $buildTime,
                    'score' => $unitPoints
                ];
            }
        }

        return [
            'buildOrders' => $buildOrders,
            'totalPoints' => $totalPoints
        ];
    }

    /**
     * Validate preset structure
     *
     * @param array $presetData
     * @return bool
     */
    private function validatePreset(array $presetData): bool
    {
        if (empty($presetData['units'])) {
            return false;
        }

        if ($presetData['minRatio'] <= 0) {
            return false;
        }

        if (empty($presetData['baseShipId'])) {
            return false;
        }

        return true;
    }

    /**
     * Insert a build order into the database
     *
     * @param int $npcUserId
     * @param int $techId Unit ID (81-89)
     * @param int $amount Number of units to build
     * @param int $buildTime Build time in ticks
     * @return bool
     */
    private function insertBuildOrder(int $npcUserId, int $techId, int $amount, int $buildTime): bool
    {
        if ($amount <= 0 || $buildTime <= 0) {
            return false;
        }

        $score = $amount * $this->TECH_SCORES[$techId];

        $result = mysqli_execute_query(
            $this->dbi,
            "INSERT INTO de_user_build (user_id, tech_id, anzahl, verbzeit, score, recycling) 
             VALUES (?, ?, ?, ?, ?, 1)",
            [$npcUserId, $techId, $amount, $buildTime, $score]
        );

        return $result !== false;
    }

    /**
     * Clear NPC fleet by setting all units to 0
     *
     * @param int $npcUserId
     * @return bool
     */
    private function clearNpcFleet(int $npcUserId): bool
    {
        $fleetId = $npcUserId . '-0';

        $result = mysqli_execute_query(
            $this->dbi,
            "UPDATE de_user_fleet 
             SET e81=0, e82=0, e83=0, e84=0, e85=0, e86=0, e87=0, e88=0, e89=0 
             WHERE user_id = ?",
            [$fleetId]
        );

        return $result !== false;
    }
}
