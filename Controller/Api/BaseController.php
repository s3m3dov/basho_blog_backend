<?php

use JetBrains\PhpStorm\NoReturn;

class BaseController
{
    /**
     * __call magic method.
     */
    #[NoReturn] public function __call($name, $arguments)
    {
        $this->sendOutput('', array('HTTP/1.1 404 Not Found'));
    }

    /**
     * Get URI elements.
     *
     * @return array
     */
    protected function getUriSegments(): array
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        return explode( '/', $uri );
    }

    /**
     * Get querystring params.
     *
     * @return array
     */
    protected function getQueryStringParams(): array
    {
        $query = array();
        parse_str($_SERVER['QUERY_STRING'], $query);
        return $query;
    }

    protected function getLimit(array $queryStringParams): int
    {
        $limit = 10;
        if (isset($queryStringParams['limit']) && $queryStringParams['limit']) {
            $limit = (int)$queryStringParams['limit'];
        }
        return $limit;
    }

    protected function getOffset(array $queryStringParams): int
    {
        $offset = 0;
        if (isset($queryStringParams['page']) && $queryStringParams['page']) {
            $offset = (int)$queryStringParams['page'];
        }
        return $offset;
    }

    /**
     * Send API output.
     *
     * @param mixed $data
     * @param array $httpHeaders
     */
    #[NoReturn] protected function sendOutput(mixed $data, array $httpHeaders=array())
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

    /**
     * Send API error.
     *
     * @param string $errorCode
     * @param string $errorMessage
     */
    #[NoReturn] protected function sendError(
        string $errorCode="500 Internal Server Error",
        string $errorMessage="Internal Server Error"
    )
    {
        $this->sendOutput(
            json_encode(array('message' => $errorMessage)),
            array('HTTP/1.1 ' . $errorCode)
        );
    }
}