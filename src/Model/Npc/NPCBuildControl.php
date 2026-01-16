<?php

namespace DieEwigen\DE2\Model\Npc;

use DieEwigen\DE2\Model\Npc\Types\FleetPreset;
use DieEwigen\DE2\Model\Npc\Types\FleetPresetConfig;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class NPCBuildControl
{

    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $GLOBALS['sv_npc_base_url'],
            'timeout' => 10.0,
            'headers' => [
                'X-API-Key' => $GLOBALS['sv_npc_api_key'],
                'Accept' => 'application/json',
            ]]);
    }

    /**
     * Get ship build presets for a given NPC ID.
     * @throws Exception
     */
    public function getShipBuildPresets($npcId): FleetPresetConfig
    {
        try {
            $res = $this->client->request('GET', 'build/v1/presets/' . $npcId);
            $resBody = $res->getBody()->getContents();
            $data = json_decode($resBody, true);
            $presets = [];
            foreach ($data['presets'] as $preset) {
                $presets[] = new FleetPreset(
                    $preset['id'],
                    $preset['name'],
                    $preset['unitRatios'],
                    $preset['unitRatiosByName'],
                );
            }
            return new FleetPresetConfig($data['currentPresetId'], $presets);
        } catch (GuzzleException $e) {
            throw new Exception("Error fetching NPC ship build presets: " . $e->getMessage());
        }
    }

    /**
     * Save ship build preset for a given NPC ID.
     * @param int $npcId the NPC ID
     * @param string $presetId the ID of the preset to set
     * @throws Exception if the request fails
     */
    public function setShipBuildPreset(int $npcId, string $presetId): void
    {
        try {
            $this->client->request('POST', 'build/v1/fleet-build-preset', [
                'json' => [
                    'presetName' => $presetId,
                    'npcId' => $npcId
                ]
            ]);
        } catch (GuzzleException $e) {
            throw new Exception("Error setting NPC ship build preset: " . $e->getMessage());
        }
    }

    /**
     * Get maximum fleet points percentage in relation to sector average points for a given NPC ID.
     * @throws Exception
     */
    public function getMaxFleetPoints($npcId): int
    {
        try {
            $res = $this->client->request('GET', 'build/v1/max-fp/' . $npcId);
            $resBody = $res->getBody()->getContents();
            $data = json_decode($resBody, true);
            $value = $data['value'];
            if ($value === null) {
                return -10; // minimum value of range input
            }
            return $value;
        } catch (GuzzleException $e) {
            throw new Exception("Error fetching NPC max fleet points: " . $e->getMessage());
        }
    }

    /**
     * Set maximum fleet points percentage in relation to sector average points for a given NPC ID.
     * @param int $npcId
     * @param int $maxFpPercentage
     * @return void
     * @throws Exception
     */
    public function setMaxFleetPoints(int $npcId, int $maxFpPercentage): void
    {
        try {
            $this->client->request('POST', 'build/v1/max-fp/'.$npcId, [
                'json' => [
                    'value' => $maxFpPercentage
                ]
            ]);
        } catch (GuzzleException $e) {
            throw new Exception("Error setting NPC ship build preset: " . $e->getMessage());
        }
    }
}