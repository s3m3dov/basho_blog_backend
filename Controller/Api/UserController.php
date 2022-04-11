<?php
class UserController extends BaseController
{
    /**
     * "/users/" Endpoint - Get list of users
     */
    public function Action()
    {
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();

        $errorMessage = '';
        $errorCode = '';
        $responseData = null;

        if ($requestMethod == 'GET') {
            try {
                $userModel = new UserModel();

                if (isset($arrQueryStringParams['id'])) {
                    $rawData = $userModel->getUser($arrQueryStringParams['id']);
                } else {
                    $limit = $this->getLimit($arrQueryStringParams);
                    $offset = $this->getOffset($arrQueryStringParams);
                    $rawData = $userModel->getAllUsers(limit:$limit, offset:$offset);
                }
                $responseData = empty($rawData) ? null : json_encode($rawData);
            } catch (Exception $e) {
                $errorMessage = $e->getMessage().' Something went wrong! Please contact support.';
                $errorCode = '500 Internal Server Error';
            }
        } else {
            $errorMessage = 'Method not supported';
            $errorCode = '422 Unprocessable Entity';
        }

        // send output
        if ($errorMessage != '') {
            $this->sendError($errorCode, $errorMessage);
        } elseif (!$responseData) {
            $this->sendError('404 Not Found', 'No data found');
        } else {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        }
    }
}