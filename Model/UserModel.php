<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";

class UserModel extends Database
{
    // Columns
    public static string $user_id = "user_id";

    /**
     * @throws Exception
     */
    public function getUsers($limit, $offset)
    {
        $user_id = self::$user_id;
        return $this->select("SELECT * FROM users ORDER BY $user_id LIMIT $limit OFFSET $offset");
    }
}