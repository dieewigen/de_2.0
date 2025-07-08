<?php
define("PROJECT_ROOT_PATH", __DIR__ . "/");
require PROJECT_ROOT_PATH.'inccon.php';
require_once PROJECT_ROOT_PATH.'/api/base/BaseController.php';
require PROJECT_ROOT_PATH.'/api/tech/controller/TechBuildingController.php';
require PROJECT_ROOT_PATH.'api/base/AuthenticationService.php';

if(!isset($_SESSION)){
    session_start();
}

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
