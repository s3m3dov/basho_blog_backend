<?php

include_once PROJECT_ROOT_PATH . '/Utils/Token.php';

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
                    $rawData = $userModel->getUser(id:$arrQueryStringParams['id']);
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
     * "/users/register" Endpoint - Create a user
     */
    public function registerAction()
    {
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $errorMessage = '';
        $errorCode = '';

        if ($requestMethod == 'POST') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                $userModel = new UserModel();
                if($userModel->countUsersByEmail($input["email"]) > 0){
                    $this->sendError(
                        "409 Conflict",
                        "User with this email already exists"
                    );
                } else {
                    $userModel->insertUser($input);
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
                json_encode(array('message' => 'User created successfully')),
                array('Content-Type: application/json', 'HTTP/1.1 201 Created')
            );
        }
    }

    /**
     * "/users/login" Endpoint - Login
     */
    public function loginAction()
    {
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $errorMessage = '';
        $errorCode = '';

        if ($requestMethod == 'POST') {
            try {
                // get posted input
                $input = json_decode(file_get_contents('php://input'), true);
                if (empty($input['email']) || empty($input['password'])) {
                    throw new Exception('Email and password are required');
                }
                $email = $input['email'];
                $password = $input['password'];
                // instantiate user object
                $userModel = new UserModel();
                // set product property values
                if ($userModel->countUsersByEmail($email) > 0) {
                    if (password_verify($password, $userModel->getUserPassword($email))) {
                        $user = $userModel->getUser(email:$email);
                        $token = generateToken($user);
                        $this->sendOutput(
                            json_encode(
                                array(
                                    'message' => 'User logged in successfully',
                                    'token' => $token,
                                    'user' => $user
                                )
                            ),
                            array('Content-Type: application/json', 'HTTP/1.1 200 OK')
                        );
                    } else {
                        $this->sendError(
                            '401 Unauthorized',
                            'The password is wrong for this email'
                        );
                    }
                } else {
                    $this->sendError(
                        '400 Bad Request',
                        'User with this email does not exist'
                    );
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
                json_encode(array('message' => 'User logged in successfully')),
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
                $input = json_decode(file_get_contents('php://input'), true);
                $userModel = new UserModel();
                if (isset($arrQueryStringParams['id'])) {
                    $user = $userModel->getUser(id:$arrQueryStringParams['id']);
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
                    $user = $userModel->getUser(id:$arrQueryStringParams['id']);
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