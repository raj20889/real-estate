<?php
/**
 * Database Connection Class
 * Handles connection to MySQL using OOP (mysqli)
 * Includes debugging info for easier troubleshooting.
 */
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "real_state";
    private $conn;
    private $debug = true; // Set false in production

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);

            if ($this->conn->connect_error) {
                throw new Exception("Database Connection Failed: " . $this->conn->connect_error);
            }

            if ($this->debug) {
                echo "<p style='color:green;'>✅ Database connected successfully.</p>";
            }

        } catch (Exception $e) {
            $this->logError($e->getMessage());
            die("<p style='color:red;'>❌ " . $e->getMessage() . "</p>");
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    private function logError($message) {
        $logFile = __DIR__ . '/db_error_log.txt';
        $timestamp = date("Y-m-d H:i:s");
        file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
    }

    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
            if ($this->debug) {
                echo "<p style='color:orange;'>🔒 Database connection closed.</p>";
            }
        }
    }
}
?>
