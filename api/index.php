<?php
use DieEwigen\Api\Model\GetAllUsers;
use DieEwigen\Api\Model\UserService;
use DieEwigen\Api\Model\ValidateGameFilename;

define("PROJECT_ROOT_PATH", __DIR__ . "/");
require PROJECT_ROOT_PATH.'../inccon.php';
include_once 'vendor/autoload.php';

// Check if the user has API access
$headers = getallheaders();
$apiKey=$headers['de_api_key'] ?? '';

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
    echo json_encode(['status' => 'error', 'message' => 'Leerer JSON-Body']);
    exit;
}

$data = json_decode($input, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['status' => 'error', 'message' => 'Ungültiges JSON: ' . json_last_error_msg()]);
    exit;
}

if(isset($data['action']) && !empty($data['action'])) {
    

    switch ($data['action']) {
        case 'getAllNpcUsers':
            $userModel = new GetAllUsers();
            $users = $userModel->getAllNpcUsers();
            echo json_encode(['status' => 'success', 'data' => $users]);
            break;

        case 'openPage':
            //all fields are required
            if (!isset($data['user_id']) || !isset($data['filename'])) {
                echo json_encode(['status' => 'error', 'message' => 'Fehlende Parameter: user_id oder filename']);
                exit;
            }
            //is user_id valid
            $userId = intval($data['user_id']);
            $userService = new UserService();
            if (!$userService->isAPIUser($userId)) {
                echo json_encode(['status' => 'error', 'message' => 'Unberechtigter Zugriff']);
                exit;
            }

            //is filename valid
            $validGameFilename = new ValidateGameFilename();
            if(!$validGameFilename->isValid($data['filename'])) {
                echo json_encode(['status' => 'error', 'message' => 'Ungültiger Dateiname']);
                exit;
            }

            //open the page
            $filePath = '../' . $data['filename'];
            if (!file_exists($filePath)) {
                echo json_encode(['status' => 'error', 'message' => 'Datei nicht gefunden ('.$filePath.')']);
                exit;
            }

            chdir('../');
            $eftachatbotdefensedisable=1;
            $apiDisableHelper=1;
            //SESSSION-Variable setzen
            $_SESSION['ums_user_id'] = $userId;
            $_SESSION['de_frameset'] = 1; // Setze die Frameset-Variable, um das Layout zu ändern

            //damit man die Scriptfunktionen ansprechen kann, werden die in requestData übergebenen Parameter in $_REQUEST hinterlegt
            if (isset($data['requestData'])) {
                foreach($data['requestData'] as $parameter => $value){
                    $_REQUEST[$parameter]=$value;
                    $_POST[$parameter]=$value;
                    $_GET[$parameter]=$value;
                }
            }

            include_once $data['filename'];
            
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Unbekannte Aktion: ' . $data['action']]);
            exit;
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Keine Aktion angegeben']);
    exit;
}

/*
$authenticationService = new AuthenticationService();
if ($_SESSION['ums_user_id'] < 1) {
    header('HTTP/1.1 401 Unauthorized');
    exit();
}
if (!$authenticationService->isAllowedForAPIUsage($_SESSION['ums_user_id'])) {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );


//API endpoint registration
if ((isset($uri[3]) && $uri[3] != 'allTechs') || !isset($uri[3])) {
    header('HTTP/1.1 404 Not Found');
    exit();
}

//Tech Building Controller registration
$objFeedController = new TechBuildingController();
$strMethodName = $uri[3];
echo $strMethodName;
$objFeedController->{$strMethodName}();
*/
