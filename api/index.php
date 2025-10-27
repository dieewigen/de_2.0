<?php

use DieEwigen\Api\Model\GetAlliances;
use DieEwigen\Api\Model\GetAllUsers;
use DieEwigen\Api\Model\GetAttackNews;
use DieEwigen\Api\Model\GetPlayerAttackInfo;
use DieEwigen\Api\Model\GetTopPlayers;
use DieEwigen\Api\Model\GetUserFleet;
use DieEwigen\Api\Model\GetSectorStatus;
use DieEwigen\Api\Model\GetServerData;
use DieEwigen\Api\Model\GetActiveBuilds;
use DieEwigen\Api\Model\UserService;
use DieEwigen\Api\Model\UserTechs;
use DieEwigen\Api\Model\ValidateGameFilename;
use DieEwigen\Api\Model\SetPlayerActivity;

define("PROJECT_ROOT_PATH", __DIR__ . "/");
require PROJECT_ROOT_PATH.'../inccon.php';
include_once 'vendor/autoload.php';
include_once '../inc/sv.inc.php';

$apiKey = getHeaderValue('X-DE-API-KEY');

if(empty($apiKey)){
    $apiKey=$_SERVER['X-DE-API-KEY'] ?? '';
}

if($apiKey != $GLOBALS['env_api_key']) {
    header('HTTP/1.1 401 Unauthorized');
    die('Invalid API Key');
}

if(!isset($_SESSION)){
    session_start();
}

//JSON Payload auswerten
$input = trim(file_get_contents("php://input"));

if (empty($input)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['message' => 'Leerer JSON-Body']);
    exit;
}

$data = json_decode($input, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['message' => 'Ungültiges JSON: ' . json_last_error_msg()]);
    exit;
}

if(isset($data['action']) && !empty($data['action'])) {
    header('Content-Type: application/json');
    try {
        $userService = new UserService();
        $userId = intval($data['user_id'] ?? -1);

        //set player activity if user_id is set and valid
        if (isset($userId) && $userId > 0 && $userService->isAPIUser($userId)) {
            $setPlayerActivity = new SetPlayerActivity();
            $setPlayerActivity->setPlayerActivity($userId);
        }

        switch ($data['action']) {
            case 'getAllNpcUsers':
                $userModel = new GetAllUsers();
                $users = $userModel->getAllNpcUsers();
                echo json_encode($users);
                break;
            case 'getAvailableTechs':
                if (isset($user_id) && !$userService->isAPIUser($userId)) {
                    header('HTTP/1.1 403 Forbidden');
                    echo json_encode(['message' => 'Unberechtigter Zugriff']);
                    exit;
                }
                $userTechs = new UserTechs();
                $users = $userTechs->getAvailableTechs($userId);
                echo json_encode($users);
                break;
            case 'getUserFleet':
                //is user_id valid
                $userService = new UserService();
                if (!$userService->isAPIUser($userId)) {
                    header('HTTP/1.1 403 Forbidden');
                    echo json_encode(['message' => 'Unberechtigter Zugriff']);
                    exit;
                }

                $userModel = new GetUserFleet();
                $fleets = $userModel->getUserFleet($userId);
                echo json_encode($fleets);
                break;
            case 'getSecStatus':
                include_once "../functions.php";
                include_once "../tickler/kt_einheitendaten.php";
                if (isset($userId) && !$userService->isAPIUser($userId)) {
                    header('HTTP/1.1 403 Forbidden');
                    echo json_encode(['message' => 'Unberechtigter Zugriff']);
                    exit;
                }
                $sectorStatus = new GetSectorStatus();
                $status = $sectorStatus->getSectorStatus($userId);
                echo json_encode($status);
                break;
            case 'getPlayerAttackInfo':
                if (isset($userId) && !$userService->isAPIUser($userId)) {
                    header('HTTP/1.1 403 Forbidden');
                    echo json_encode(['message' => 'Unberechtigter Zugriff']);
                    exit;
                }
                $playerAttackInfo = new GetPlayerAttackInfo();
                if (isset($data['player_id'])) {
                    $status = $playerAttackInfo->getPlayerAttackInfo($userId, $data['player_id']);
                    echo json_encode($status);
                } elseif (isset($data['sector']) && isset($data['system'])) {
                    $status = $playerAttackInfo->getPlayerAttackInfoByCoords($userId, $data['sector'], $data['system']);
                    echo json_encode($status);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['message' => 'Fehlende Parameter: player_id oder sector und system']);
                    exit;
                }
                break;
            case 'getActiveBuilds':
                if (isset($userId) && !$userService->isAPIUser($userId)) {
                    header('HTTP/1.1 403 Forbidden');
                    echo json_encode(['message' => 'Unberechtigter Zugriff']);
                    exit;
                }
                $getActiveBuilds = new GetActiveBuilds();
                echo json_encode($getActiveBuilds->getBuilds($userId));
                break;
            case 'getServerData':
                $serverModel = new GetServerData();
                $data = $serverModel->getServerData();
                echo json_encode($data);
                break;
            case 'getTopList':
                $sortType = $data['sortType'] ?? 'score';
                $topList = new GetTopPlayers();
                $result = $topList->getTopList($sortType);
                echo json_encode($result);
                break;
            case 'getAlliances':
                $alliances = new GetAlliances();
                $result = $alliances->getAlliances();
                echo json_encode($result);
                break;
            case 'getAttackNews':
                $playerId = $data['player_id'];
                if (isset($userId) && !$userService->isAPIUser($userId) && isset($playerId)) {
                    header('HTTP/1.1 403 Forbidden');
                    echo json_encode(['message' => 'Unberechtigter Zugriff']);
                    exit;
                }
                $sortType = $data['sortType'] ?? 'score';
                $attackNews = new GetAttackNews();
                $attackNews = $attackNews->getAttackNews($playerId, $userId);
                echo json_encode($attackNews);
                break;
            case 'getDefferFleet':
                // Parameter validieren
                if (!isset($userId) || !isset($data['target_sector']) || !isset($data['target_system'])) {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['message' => 'Fehlende Parameter: user_id, target_sector, target_system']);
                    exit;
                }
                
                $zielSec = intval($data['target_sector']);
                $zielSys = intval($data['target_system']);

                include_once "../functions.php";
                $userModel = new GetUserFleet();
                $fleets = $userModel->getDefferFleet($userId, $zielSec, $zielSys);
                echo json_encode($fleets);
                break;

            case 'openPage':
                //all fields are required
                if (!isset($userId) || !isset($data['filename'])) {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['message' => 'Fehlende Parameter: user_id oder filename']);
                    exit;
                }
                header('Content-Type: text/html');
                //is user_id valid
                if (!$userService->isAPIUser($userId)) {
                    header('HTTP/1.1 403 Forbidden');
                    echo json_encode(['message' => 'Unberechtigter Zugriff']);
                    exit;
                }

                //is filename valid
                $validGameFilename = new ValidateGameFilename();
                if (!$validGameFilename->isValid($data['filename'])) {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['message' => 'Ungültiger Dateiname']);
                    exit;
                }

                //open the page
                $filePath = '../' . $data['filename'];
                if (!file_exists($filePath)) {
                    header('HTTP/1.1 404 Not Found');
                    echo json_encode(['message' => 'Datei nicht gefunden ('.$filePath.')']);
                    exit;
                }

                chdir('../');
                $eftachatbotdefensedisable = 1;
                $apiDisableHelper = 1;
                //SESSSION-Variable setzen
                $_SESSION['ums_user_id'] = $userId;
                $_SESSION['de_frameset'] = 1; // Setze die Frameset-Variable, um das Layout zu ändern
                $_SESSION['ums_servid'] = $sv_servid;
                $_SESSION['ums_owner_id'] = 0;
                $_SESSION['ums_session_start'] = time();
                $_SESSION['ums_zeitstempel'] = time();
                $_SESSION['ums_vote'] = 0;
                $_SESSION['ums_rasse'] = -1;

                //damit man die Scriptfunktionen ansprechen kann, werden die in requestData übergebenen Parameter in $_REQUEST hinterlegt
                if (isset($data['requestData'])) {
                    foreach ($data['requestData'] as $parameter => $value) {
                        $_REQUEST[$parameter] = $value;
                        $_POST[$parameter] = $value;
                        $_GET[$parameter] = $value;
                    }
                }

                include_once $data['filename'];

                break;

            default:
                header('HTTP/1.1 400 Bad Request');
                echo json_encode(['message' => 'Unbekannte Aktion: ' . $data['action']]);
                exit;
        }
    } catch (Exception $e) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['message' => 'An technical error occurred']);
        throw $e;
    }

} else {
    echo json_encode(['message' => 'Keine Aktion angegeben']);
    exit;
}


function getHeaderValue($name) {
    // 1. Standard: $_SERVER mit HTTP_ Prefix
    $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
    if (isset($_SERVER[$key])) {
        return $_SERVER[$key];
    }

    // 2. Manchmal sind Header direkt vorhanden (z. B. 'x-de-api-key')
    foreach ($_SERVER as $k => $v) {
        if (strtolower($k) === strtolower($name)) {
            return $v;
        }
    }

    // 3. Als Fallback: getallheaders() falls verfügbar
    if (function_exists('getallheaders')) {
        $headers = getallheaders();
        foreach ($headers as $k => $v) {
            if (strtolower($k) === strtolower($name)) {
                return $v;
            }
        }
    }

    return null;
}