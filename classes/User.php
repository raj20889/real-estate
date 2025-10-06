<?php
require_once '../db/Database.php';

class User {
    private $conn;
    private $debug = true; // Set false in production

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Login user with email and password
     */
    public function login($email, $password) {
        try {
            // Input validation
            if (empty($email) || empty($password)) {
                throw new Exception("Both fields are required!");
            }

            $stmt = $this->conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $this->conn->error);
            }

            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    session_start();
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['user_name'] = $row['name'];
                    return ['status' => true, 'message' => 'Login successful', 'redirect' => 'user_dashboard.php'];
                } else {
                    throw new Exception("Incorrect password!");
                }
            } else {
                throw new Exception("Email not registered!");
            }

        } catch (Exception $e) {
            if ($this->debug) {
                return ['status' => false, 'message' => "Error: " . $e->getMessage()];
            } else {
                return ['status' => false, 'message' => "Login failed"];
            }
        }
    }
}
?>
