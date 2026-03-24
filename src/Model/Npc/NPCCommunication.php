<?php

namespace DieEwigen\DE2\Model\Npc;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Client wrapper for the NPC communication API exposed by the Spring NPC service.
 *
 * All requests are authenticated server-to-server via the X-API-Key header.
 * The game server acts as proxy; browser clients never call the NPC service directly.
 *
 * Available dialog types in v1:
 *   - RECALL_DEF   : ask the NPC to recall a defending fleet
 *   - ONLINE_TIME  : ask about the NPC's typical online time
 *
 * Request lifecycle states returned by the API:
 *   CREATED, WAITING_FOR_NPC, ANSWERED, CANCELLED, EXPIRED
 */
class NPCCommunication
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => rtrim($GLOBALS['sv_npc_base_url'], '/') . '/',
            'timeout'  => 10.0,
            'headers'  => [
                'X-API-Key' => $GLOBALS['sv_npc_api_key'],
                'Accept'    => 'application/json',
            ],
        ]);
    }

    /**
     * Get all publicly available dialog types with their translated labels.
     * Labels are returned in the language matching the given locale (de or en).
     *
     * @param string $locale  BCP-47 locale string, e.g. 'de' or 'en'
     * @return array<array{type: string, label: string}>
     * @throws Exception
     */
    public function getDialogTypes(string $locale = 'de'): array
    {
        try {
            $res  = $this->client->request('GET', 'communication/v1/requests/dialog-types', [
                'headers' => ['Accept-Language' => $locale],
            ]);
            $data = json_decode($res->getBody()->getContents(), true);
            return is_array($data) ? $data : [];
        } catch (GuzzleException $e) {
            throw new Exception('Error fetching NPC dialog types: ' . $e->getMessage());
        }
    }

    /**
     * Create a new communication request on behalf of a human player.
     *
     * Returns the initial dialog response, which may immediately be ANSWERED
     * or be in WAITING_FOR_NPC state if the NPC is offline.
     *
     * @param int    $npcId      Target NPC player ID
     * @param int    $playerId   Human player ID
     * @param string $dialogType RECALL_DEF or ONLINE_TIME
     * @param string $locale     BCP-47 locale for response message text
     * @return array             Decoded dialog response
     * @throws Exception         On communication failure or conflict (waiting request exists)
     */
    public function createRequest(int $npcId, int $playerId, string $dialogType, string $locale = 'de'): array
    {
        try {
            $res = $this->client->request('POST', 'communication/v1/requests', [
                'headers' => ['Accept-Language' => $locale],
                'json'    => [
                    'npcId'      => $npcId,
                    'playerId'   => $playerId,
                    'dialogType' => $dialogType,
                    'context'    => null,
                ],
            ]);
            return json_decode($res->getBody()->getContents(), true);
        } catch (ClientException $e) {
            $status = $e->getResponse()->getStatusCode();
            if ($status === 409) {
                throw new Exception('CONFLICT');
            }
            if ($status === 404) {
                throw new Exception('NOT_FOUND');
            }
            throw new Exception('Error creating NPC dialog request: ' . $e->getMessage());
        } catch (GuzzleException $e) {
            throw new Exception('Error creating NPC dialog request: ' . $e->getMessage());
        }
    }

    /**
     * Get the most recent open or answered request for the given NPC + player pair.
     *
     * Returns WAITING_FOR_NPC or ANSWERED state if one exists.
     * Returns null when no current request exists (404 from the NPC service).
     *
     * @param int $npcId    NPC player ID
     * @param int $playerId Human player ID
     * @return array|null   Decoded dialog response or null if none exists
     * @throws Exception    On unexpected communication failure
     */
    public function getRequest(int $npcId, int $playerId): ?array
    {
        if ($npcId <= 0 || $playerId <= 0) {
            throw new \InvalidArgumentException('npcId and playerId must be positive integers');
        }
        try {
            $res = $this->client->request(
                'GET',
                'communication/v1/requests/' . rawurlencode((string)$npcId) . '/players/' . rawurlencode((string)$playerId)
            );
            return json_decode($res->getBody()->getContents(), true);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                return null;
            }
            throw new Exception('Error fetching NPC dialog request: ' . $e->getMessage());
        } catch (GuzzleException $e) {
            throw new Exception('Error fetching NPC dialog request: ' . $e->getMessage());
        }
    }

    /**
     * Cancel the current open (WAITING_FOR_NPC) request for the given NPC + player pair.
     *
     * Only waiting requests can be cancelled; calling this on an already answered
     * request will throw an exception with code CONFLICT.
     *
     * @param int $npcId    NPC player ID
     * @param int $playerId Human player ID
     * @return array        Updated dialog response with CANCELLED status
     * @throws Exception    On communication failure, 404, or 409 conflict
     */
    public function cancelRequest(int $npcId, int $playerId): array
    {
        if ($npcId <= 0 || $playerId <= 0) {
            throw new \InvalidArgumentException('npcId and playerId must be positive integers');
        }
        try {
            $res = $this->client->request(
                'POST',
                'communication/v1/requests/' . rawurlencode((string)$npcId) . '/players/' . rawurlencode((string)$playerId) . '/cancel'
            );
            return json_decode($res->getBody()->getContents(), true);
        } catch (ClientException $e) {
            $status = $e->getResponse()->getStatusCode();
            if ($status === 409) {
                throw new Exception('CONFLICT');
            }
            if ($status === 404) {
                throw new Exception('NOT_FOUND');
            }
            throw new Exception('Error cancelling NPC dialog request: ' . $e->getMessage());
        } catch (GuzzleException $e) {
            throw new Exception('Error cancelling NPC dialog request: ' . $e->getMessage());
        }
    }
}
