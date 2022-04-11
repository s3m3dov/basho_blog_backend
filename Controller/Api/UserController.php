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

        $strErrorMessage = '';
        $strErrorHeader = '';
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
                $responseData = json_encode($rawData);
            } catch (Exception $e) {
                $strErrorMessage = $e->getMessage().' Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorMessage = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }

        // send output
        if (!$strErrorMessage) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('message' => $strErrorMessage)),
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }
}