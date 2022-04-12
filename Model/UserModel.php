<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";

class UserModel extends Database
{
    // DB INFO
    private static string $users_table = "users";
    private static string $user_id = "id";
    private static string $user_fullname = "fullname";
    private static string $user_email = "email";
    private static string $user_password = "password";
    private static string $user_status = "status";

    /**
     * @throws Exception
     */
    public function getAllUsers($limit, $offset)
    {
        $query = "SELECT * FROM " . self::$users_table . " ORDER BY " . self::$user_id . " LIMIT ? OFFSET ?";
        return $this->select(query: $query, types: "ii", params: [$limit, $offset]);
    }

    /**
     * @throws Exception
     */
    public function getUser($id)
    {
        $query = "SELECT * FROM " . self::$users_table . " WHERE " . self::$user_id . "=?";
        return $this->select(query: $query, types: "i", params: [$id]);
    }

    /**
     * @throws Exception
     */
    public function insertUser(array $input): mysqli_stmt
    {
        $query = "INSERT INTO " . self::$users_table .
            "(" . self::$user_fullname . ", " . self::$user_email . ", " . self::$user_password . ")" .
            " VALUES(?, ?, ?)";
        $types = str_repeat("s", count($input));
        return $this->executeStatement(query: $query, types: $types, params: $input);
    }

    /**
     * @throws Exception
     */
    public function updateUser($id, array $input)
    {
        $query = "UPDATE " . self::$users_table .
            " SET " . self::$user_fullname . "=?, " . self::$user_email . "=?, " . self::$user_password .
            "=? WHERE " . self::$user_id . "=?";
        $types = str_repeat("s", count($input)) . "i";
        $params = array_merge($input, [$id]);

        return $this->executeStatement(query: $query, types: $types, params: $params);
    }

    /**
     * @throws Exception
     */
    public function deleteUser($id)
    {
        // set active state to 0
        $query = "
            DELETE FROM " . self::$users_table . "
            WHERE " . self::$user_id . "=?
        ";
        return $this->executeStatement(query: $query, types: "i", params: [$id]);
    }
}