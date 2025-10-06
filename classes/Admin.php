<?php
require_once __DIR__ . '/../db/Database.php';

class Admin {
    private $conn;
    private $debug = true;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
        session_start();
    }

    public function login($email, $password) {
        try {
            if (empty($email) || empty($password)) {
                throw new Exception("Both fields are required!");
            }

            $stmt = $this->conn->prepare("SELECT id, full_name, password_hash FROM admins WHERE email = ?");
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $this->conn->error);
            }

            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                // Initialize variables to satisfy Intelephense
                $id = null;
                $full_name = null;
                $hashed_password = '';

                $stmt->bind_result($id, $full_name, $hashed_password);
                $stmt->fetch();

                if (password_verify($password, $hashed_password)) {
                    $_SESSION["admin_id"] = $id;
                    $_SESSION["admin_name"] = $full_name;
                    return ['status' => true, 'message' => 'Login successful', 'redirect' => 'admin_dashboard.php'];
                } else {
                    throw new Exception("Invalid email or password!");
                }
            } else {
                throw new Exception("No account found with this email!");
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
