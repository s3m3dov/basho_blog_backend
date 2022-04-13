<?php
class Database
{
    protected ?mysqli $connection = null;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        try {
            $this->connection = new mysqli(
                DB_HOST,
                DB_USERNAME,
                DB_PASSWORD,
                DB_DATABASE_NAME,
                DB_PORT
            );

            if (mysqli_connect_errno()) {
                throw new Exception("Could not connect to database.");
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function select($query = "" , $types = "", $params = [])
    {
        try {
            $stmt = $this->executeStatement($query , $types, $params);
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $result;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage() );
        }
    }

    /**
     * @throws Exception
     */
    public function selectTopRow($query = "", $types = "", $params = []): bool|array|null
    {
        try {
            $stmt = $this->executeStatement($query , $types, $params);
            $result = $stmt->get_result()->fetch_row();
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            throw New Exception( $e->getMessage() );
        }
    }

    /**
     * @throws Exception
     */
    protected function executeStatement($query = "" , $types = "", $params = []): mysqli_stmt
    {
        try {
            $stmt = $this->connection->prepare($query);

            if($stmt === false) {
                throw New Exception("Unable to do prepared statement: " . $query);
            }

            if($params) {
                 $stmt->bind_param($types, ...$this->escaped($params));
            }

            $stmt->execute();

            return $stmt;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage() );
        }
    }

    protected function escaped(array $params): array
    {
        $escapedParams = [];

        foreach($params as $param) {
            $escapedParams[] = $this->connection->real_escape_string($param);
        }

        return $escapedParams;
    }
}