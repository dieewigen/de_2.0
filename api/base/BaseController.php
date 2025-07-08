<?php

class BaseController
{
    public function __call($name, $arguments) {
        $this->sendOutput('', array('HTTP/1.1 404 Not Found'));
    }

    protected function sendOutput($data, $httpHeaders=array())
    {
        header_remove('Set-Cookie');
        if (is_array($httpHeaders) && count($httpHeaders)) {
            foreach ($httpHeaders as $httpHeader) {
                header($httpHeader);
            }
        }
        echo $data;
        exit;
    }

    protected function getQueryStringParams()
    {
        return parse_str($_SERVER['QUERY_STRING'], $query);
    }

    protected function getUriSegments()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode( '/', $uri );
        return $uri;
    }
}