<?php
const PROJECT_ROOT_PATH = __DIR__ . "/../";

// include main configuration file
require_once PROJECT_ROOT_PATH . "/Inc/Config/db_config.php";
require_once PROJECT_ROOT_PATH . "/Inc/Config/jwt_config.php";

// include the base controller file
require_once PROJECT_ROOT_PATH . "/Controller/Api/BaseController.php";

// include the use model file
require_once PROJECT_ROOT_PATH . "/Model/UserModel.php";
