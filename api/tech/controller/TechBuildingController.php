<?php

require PROJECT_ROOT_PATH.'api/tech/service/TechBuildingService.php';

class TechBuildingController extends BaseController
{
    public function allTechs()
    {
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) == 'GET') {
            try {
                $service = new TechBuildingService();
                $techDataDTO = $service->getTechTree();
                $response = json_encode($techDataDTO);
                $this->sendOutput(
                    $response,
                    array('Content-Type: application/json', 'HTTP/1.1 200 OK')
                );
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong!';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                $this->sendOutput(json_encode(array('error' => $strErrorDesc)),
                    array('Content-Type: application/json', $strErrorHeader)
                );
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 405 Method Not Allowed';
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)),
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }
}
