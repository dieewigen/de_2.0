<?php
/**
 * Alien Dialog Bridge – server-side proxy between the DE2 browser client
 * and the NPC Spring communication API.
 *
 * All calls are authenticated via the player session. The NPC API key
 * never leaves the server. Browser clients POST JSON to this file.
 *
 * Accepted actions (JSON body field "action"):
 *   listDialogTypes   – return available dialog types with translated labels
 *   createRequest     – start a new communication request
 *   getRequest        – fetch current request state for a npcId
 *   cancelRequest     – cancel a WAITING_FOR_NPC request
 *
 * Response format:
 *   { "ok": true,  "data": { ... } }
 *   { "ok": false, "error": "...", "code": "..." }
 */

declare(strict_types=1);

mb_internal_encoding('UTF-8');

include 'inc/header.inc.php';
include 'functions.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed', 'code' => 'METHOD_NOT_ALLOWED']);
    exit;
}

// Parse JSON body
$body = json_decode(file_get_contents('php://input'), true);
if (!is_array($body)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid JSON body', 'code' => 'BAD_REQUEST']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

$playerId = (int) $_SESSION['ums_user_id'];
$action   = $body['action'] ?? '';

require_once 'vendor/autoload.php';

use DieEwigen\DE2\Model\Npc\NPCCommunication;

// ---------------------------------------------------------------
// Helper: resolve player locale for NPC response messages
// ---------------------------------------------------------------
function resolveLocale(): string
{
    return ($GLOBALS['sv_server_lang'] ?? 1) === 1 ? 'de' : 'en';
}

// ---------------------------------------------------------------
// Helper: verify that the given NPC is either a meta or ally or sector mate of the requesting player.
// ---------------------------------------------------------------
function isSameSectorAllyOrMetaAlien(int $npcSector, int $playerSector, $npcAllyId, $playerAllyId): bool
{
    return $npcSector === $playerSector || isMetaOrAlly($playerAllyId, $npcAllyId);
}

// ---------------------------------------------------------------
// Load the requesting player and npc for security validation
// ---------------------------------------------------------------
$playerData = mysqli_execute_query(
    $GLOBALS['dbi'],
    'SELECT sector, ally_id FROM de_user_data WHERE user_id=?',
    [$playerId]
);
$playerRow = mysqli_fetch_assoc($playerData);
if (!$playerRow) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Player not found', 'code' => 'FORBIDDEN']);
    exit;
}
$playerSector = (int) $playerRow['sector'];
$playerAllyId = (int) $playerRow['ally_id'];

$npc = new NPCCommunication();

// ---------------------------------------------------------------
// Whitelist only the fields the browser actually needs from a
// DialogResponse. Strips internal fields (requestId, tone,
// npcMessageKey, executedAction, executedActionResult) and ensures
// tone never reaches the browser.
// ---------------------------------------------------------------
function sanitizeDialogResponse(?array $r): ?array
{
    if ($r === null) {
        return null;
    }
    return [
        'status'     => $r['status']     ?? null,
        'dialogType' => $r['dialogType'] ?? null,
        'npcMessage' => $r['npcMessage'] ?? null,
    ];
}

// ---------------------------------------------------------------
// Helper: fetch NPC sector and ally_id from database
// ---------------------------------------------------------------
function getNpcSectorAndAlly(int $npcId): ?array
{
    $npcData = mysqli_execute_query(
        $GLOBALS['dbi'],
        'SELECT sector, ally_id FROM de_user_data WHERE user_id=? AND npc=2',
        [$npcId]
    );
    $npcRow = mysqli_fetch_assoc($npcData);
    if (!$npcRow) {
        return null;
    }
    return [
        'sector' => (int) $npcRow['sector'],
        'allyId' => (int) $npcRow['ally_id']
    ];
}

// ---------------------------------------------------------------
// Dispatch
// ---------------------------------------------------------------
try {
    switch ($action) {

        // ----------------------------------------------------------
        case 'listDialogTypes':
            $npcId = isset($body['npcId']) ? (int) $body['npcId'] : 0;
            if ($npcId <= 0) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'error' => 'Missing npcId', 'code' => 'BAD_REQUEST']);
                break;
            }
            $types = $npc->getDialogTypes($playerId, $npcId, resolveLocale());
            echo json_encode(['ok' => true, 'data' => $types]);
            break;

        // ----------------------------------------------------------
        case 'createRequest':
            $npcId     = isset($body['npcId'])     ? (int) $body['npcId']     : 0;
            $dialogType = $body['dialogType']       ?? '';

            if ($npcId <= 0 || $dialogType === '') {
                http_response_code(400);
                echo json_encode(['ok' => false, 'error' => 'Missing npcId or dialogType', 'code' => 'BAD_REQUEST']);
                break;
            }
            $npcInfo = getNpcSectorAndAlly($npcId);
            if (!$npcInfo) {
                http_response_code(403);
                echo json_encode(['ok' => false, 'error' => 'Alien not found', 'code' => 'FORBIDDEN']);
                exit;
            }
            $npcSector = $npcInfo['sector'];
            $npcAllyId = $npcInfo['allyId'];

            $availableTypes     = $npc->getDialogTypes($playerId, $npcId, resolveLocale());
            $allowedDialogTypes = array_column($availableTypes, 'type');
            if (!in_array($dialogType, $allowedDialogTypes, true)) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'error' => 'Invalid dialogType', 'code' => 'BAD_REQUEST']);
                break;
            }

            if (!isSameSectorAllyOrMetaAlien($npcSector, $playerSector, $npcAllyId, $playerAllyId)) {
                http_response_code(403);
                echo json_encode(['ok' => false, 'error' => 'Invalid alien target', 'code' => 'FORBIDDEN']);
                break;
            }

            try {
                $response = $npc->createRequest($npcId, $playerId, $dialogType, resolveLocale());
                echo json_encode(['ok' => true, 'data' => sanitizeDialogResponse($response)]);
            } catch (Exception $e) {
                if ($e->getMessage() === 'CONFLICT') {
                    echo json_encode(['ok' => false, 'error' => 'A waiting request already exists. Cancel it first.', 'code' => 'CONFLICT']);
                } elseif ($e->getMessage() === 'NOT_FOUND') {
                    echo json_encode(['ok' => false, 'error' => 'Alien not found.', 'code' => 'NOT_FOUND']);
                } else {
                    throw $e;
                }
            }
            break;

        // ----------------------------------------------------------
        case 'getRequest':
            $npcId = isset($body['npcId']) ? (int) $body['npcId'] : 0;

            if ($npcId <= 0) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'error' => 'Missing npcId', 'code' => 'BAD_REQUEST']);
                break;
            }

            $npcInfo = getNpcSectorAndAlly($npcId);
            if (!$npcInfo) {
                http_response_code(403);
                echo json_encode(['ok' => false, 'error' => 'Alien not found', 'code' => 'FORBIDDEN']);
                exit;
            }
            $npcSector = $npcInfo['sector'];
            $npcAllyId = $npcInfo['allyId'];

            if (!isSameSectorAllyOrMetaAlien($npcSector, $playerSector, $npcAllyId, $playerAllyId)) {
                http_response_code(403);
                echo json_encode(['ok' => false, 'error' => 'Invalid alien target', 'code' => 'FORBIDDEN']);
                break;
            }

            $response = $npc->getRequest($npcId, $playerId);
            if ($response === null) {
                echo json_encode(['ok' => true, 'data' => null]);
            } else {
                echo json_encode(['ok' => true, 'data' => sanitizeDialogResponse($response)]);
            }
            break;

        // ----------------------------------------------------------
        case 'cancelRequest':
            $npcId = isset($body['npcId']) ? (int) $body['npcId'] : 0;

            if ($npcId <= 0) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'error' => 'Missing npcId', 'code' => 'BAD_REQUEST']);
                break;
            }

            $npcInfo = getNpcSectorAndAlly($npcId);
            if (!$npcInfo) {
                http_response_code(403);
                echo json_encode(['ok' => false, 'error' => 'Alien not found', 'code' => 'FORBIDDEN']);
                exit;
            }
            $npcSector = $npcInfo['sector'];
            $npcAllyId = $npcInfo['allyId'];

            if (!isSameSectorAllyOrMetaAlien($npcSector, $playerSector, $npcAllyId, $playerAllyId)) {
                http_response_code(403);
                echo json_encode(['ok' => false, 'error' => 'Invalid alien target', 'code' => 'FORBIDDEN']);
                break;
            }

            $existing       = $npc->getRequest($npcId, $playerId);
            $existingStatus = $existing['status'] ?? '';
            if ($existing === null || !in_array($existingStatus, ['WAITING_FOR_NPC', 'CREATED'], true)) {
                echo json_encode(['ok' => false, 'error' => 'No cancellable request found.', 'code' => 'NOT_FOUND']);
                break;
            }

            try {
                $response = $npc->cancelRequest($npcId, $playerId);
                echo json_encode(['ok' => true, 'data' => sanitizeDialogResponse($response)]);
            } catch (Exception $e) {
                if ($e->getMessage() === 'CONFLICT') {
                    echo json_encode(['ok' => false, 'error' => 'Only waiting requests can be cancelled.', 'code' => 'CONFLICT']);
                } elseif ($e->getMessage() === 'NOT_FOUND') {
                    echo json_encode(['ok' => false, 'error' => 'No open request found.', 'code' => 'NOT_FOUND']);
                } else {
                    throw $e;
                }
            }
            break;

        // ----------------------------------------------------------
        default:
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Unknown action: ' . htmlspecialchars($action, ENT_QUOTES, 'UTF-8'), 'code' => 'UNKNOWN_ACTION']);
            break;
    }
} catch (Exception $e) {
    error_log('alien_dialog.php error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Internal server error', 'code' => 'INTERNAL_ERROR']);
}
