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

            if ( mysqli_connect_errno()) {
                throw new Exception("Could not connect to database.");
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function select($query = "" , $params = [])
    {
        try {
            $stmt = $this->executeStatement( $query , $params );
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
    private function executeStatement($query = "" , $params = []): mysqli_stmt
    {
        try {
            $stmt = $this->connection->prepare( $query );

            if($stmt === false) {
                throw New Exception("Unable to do prepared statement: " . $query);
            }

            if( $params ) {
                $stmt->bind_param($params[0], $params[1]);
            }

            $stmt->execute();

            return $stmt;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage() );
        }
    }
}