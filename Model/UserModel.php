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
    private static string $user_created = "created";
    private static string $user_modified = "modified";

    /**
     * @throws Exception
     */
    public function getAllUsers($limit, $offset)
    {
        $query = "SELECT " .
            self::$user_id . ", " . self::$user_fullname . ", " . self::$user_email . ", " .
            self::$user_status . ", " . self::$user_created . ", " . self::$user_modified .
            " FROM " . self::$users_table . " ORDER BY " . self::$user_id . " LIMIT ? OFFSET ?";
        return $this->select(query: $query, types: "ii", params: [$limit, $offset]);
    }

    /**
     * @throws Exception
     */
    public function getUser($id=null, $email=null)
    {
        $base_query = "SELECT " .
            self::$user_id . ", " . self::$user_fullname . ", " . self::$user_email . ", " .
            self::$user_status . ", " . self::$user_created . ", " . self::$user_modified .
            " FROM " . self::$users_table;
        if ($id != null) {
            $query = $base_query . " WHERE " . self::$user_id . " = ? LIMIT 1";
            return $this->select(query: $query, types: "i", params: [$id]);
        } elseif ($email != null) {
            $query = $base_query . " WHERE " . self::$user_email . " = ? LIMIT 1";
            return $this->select(query: $query, types: "s", params: [$email]);
        } else {
            throw new Exception("Invalid parameters");
        }
    }

    /**
     * @throws Exception
     */
    public function getUserPassword($email)
    {
        $query = "SELECT " . self::$user_password . " FROM " . self::$users_table . " WHERE " . self::$user_email . "=?";
        $result  = $this->select(query: $query, types: "s", params: [$email]);
        return $result[0][self::$user_password];
    }

    /**
     * @throws Exception
     */
    public function countUsersByEmail($email): bool|array|null
    {
        $query = "SELECT COUNT(*) AS count FROM " . self::$users_table . " WHERE " . self::$user_email . "=?";
        return $this->selectTopRow(query: $query, types: "s", params: [$email])[0];
    }

    /**
     * @throws Exception
     */
    public function insertUser(array $input): mysqli_stmt
    {
        $input["password"] = password_hash($input["password"], PASSWORD_DEFAULT);
        $query = "INSERT INTO " . self::$users_table .
            "(" . self::$user_fullname . ", " . self::$user_email . ", " . self::$user_password . ")" .
            " VALUES(?, ?, ?)";
        $types = str_repeat("s", count($input));
        return $this->executeStatement(query: $query, types: $types, params: array_values($input));
    }

    /**
     * @throws Exception
     */
    public function updateUser($id, array $input): mysqli_stmt
    {
        $input["password"] = password_hash($input["password"], PASSWORD_DEFAULT);
        $query = "UPDATE " . self::$users_table .
            " SET " . self::$user_fullname . "=?, " . self::$user_email . "=?, " . self::$user_password .
            "=? WHERE " . self::$user_id . "=?";
        $types = str_repeat("s", count($input)) . "i";
        $params = array_merge(array_values($input), [$id]);

        return $this->executeStatement(query: $query, types: $types, params: $params);
    }

    /**
     * @throws Exception
     */
    public function deleteUser($id): mysqli_stmt
    {
        // set active state to 0
        $query = "
            DELETE FROM " . self::$users_table . "
            WHERE " . self::$user_id . "=?
        ";
        return $this->executeStatement(query: $query, types: "i", params: [$id]);
    }
}