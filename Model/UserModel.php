<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";

class UserModel extends Database
{
    // DB INFO
    public static string $users_table = "users";
    public static string $user_id = "user_id";

    /**
     * @throws Exception
     */
    public function getAllUsers($limit, $offset)
    {
        $query = "SELECT * FROM " . self::$users_table . " ORDER BY " . self::$user_id . " LIMIT ? OFFSET ?";
        return $this->select(query:$query, types:"ii", params:[$limit, $offset]);
    }

    /**
     * @throws Exception
     */
    public function getUser($id)
    {
        $query = "SELECT * FROM " . self::$users_table . " WHERE " . self::$user_id . "=?";
        return $this->select(query:$query, types:"i", params:[$id]);
    }
}