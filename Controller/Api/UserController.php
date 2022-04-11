<?php
class UserController extends BaseController
{
    /**
     * "/user/list" Endpoint - Get list of users
     */
    public function listAction()
    {
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();

        $strErrorMessage = '';
        $strErrorHeader = '';
        $responseData = null;

        if (strtoupper($requestMethod) == 'GET') {
            try {
                $userModel = new UserModel();

                $limit = $this->getLimit($arrQueryStringParams);
                $offset = $this->getOffset($arrQueryStringParams);
                $arrUsers = $userModel->getUsers(limit:$limit, offset:$offset);
                $responseData = json_encode($arrUsers);
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