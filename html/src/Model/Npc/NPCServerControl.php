<?php

namespace DieEwigen\DE2\Model\Npc;

use DieEwigen\DE2\Model\Npc\Types\FleetPreset;
use DieEwigen\DE2\Model\Npc\Types\FleetPresetConfig;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class NPCServerControl
{

    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $GLOBALS['sv_npc_base_url'],
            'timeout' => 120.0,
            'headers' => [
                'X-API-Key' => $GLOBALS['sv_npc_api_key'],
                'Accept' => 'application/json',
            ]]);
    }

    /**
     * Reset the round on NPC server side.
     * @throws Exception if the request fails
     */
    public function resetRound(): void
    {
        try {
            $this->client->request('POST', 'server/reset');
        } catch (GuzzleException $e) {
            throw new Exception("Error resetting round on NPC server: " . $e->getMessage());
        }
    }
}