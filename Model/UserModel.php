<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";

class UserModel extends Database
{
    /**
     * @throws Exception
     */
    public function getUsers($limit, $offset)
    {
        return $this->select("SELECT * FROM users ORDER BY user_id ASC LIMIT $limit OFFSET $offset");
    }
}