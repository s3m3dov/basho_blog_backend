<?php
class UserController extends BaseController
{
    /**
     * "/users/" Endpoint - Get list of users or specific user
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
    /**
     * "/users/create" Endpoint - Create a user
     */
    public function createAction()
    {
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $errorMessage = '';
        $errorCode = '';

        if ($requestMethod == 'POST') {
            try {
                $input = file_get_contents('php://input');
                $input = array_values(json_decode($input, true));
                $userModel = new UserModel();
                $userModel->insertUser($input);
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
        } else {
            $this->sendOutput(
                json_encode(array('message' => 'User created successfully')),
                array('Content-Type: application/json', 'HTTP/1.1 201 Created')
            );
        }
    }

    /**
     * "/users/update" Endpoint - Update a user
     */
    public function updateAction()
    {

        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();
        $errorMessage = '';
        $errorCode = '';

        if ($requestMethod == 'PUT') {
            try {
                $input = file_get_contents('php://input');
                $input = array_values(json_decode($input, true));
                $userModel = new UserModel();
                if (isset($arrQueryStringParams['id'])) {
                    $user = $userModel->getUser($arrQueryStringParams['id']);
                    if (empty($user)) {
                        $this->sendError('404 Not Found', 'No data found');
                    } else {
                        $userModel->updateUser($arrQueryStringParams['id'], $input);
                    }
                } else {
                    $this->sendError('400 Bad Request', 'User ID is required');
                }

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
        } else {
            $this->sendOutput(
                json_encode(array('message' => 'User updated successfully')),
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        }
    }

    public function deleteAction()
    {
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();
        $errorMessage = '';
        $errorCode = '';

        if ($requestMethod == 'DELETE') {
            try {
                $userModel = new UserModel();
                if (isset($arrQueryStringParams['id'])) {
                    $user = $userModel->getUser($arrQueryStringParams['id']);
                    if (empty($user)) {
                        $this->sendError('404 Not Found', 'No data found');
                    } else {
                        $userModel->deleteUser($arrQueryStringParams['id']);
                    }
                } else {
                    $this->sendError('400 Bad Request', 'User ID is required');
                }

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
        } else {
            $this->sendOutput(
                null,
                array('Content-Type: application/json', 'HTTP/1.1 204 No Content')
            );
        }
    }
}