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

    /**
     * Login admin
     */
    public function login($email, $password) {
        try {
            if (empty($email) || empty($password)) {
                throw new Exception("Both fields are required!");
            }

            $stmt = $this->conn->prepare("SELECT id, full_name, password_hash FROM admins WHERE email = ?");
            if (!$stmt) throw new Exception("Prepare statement failed: " . $this->conn->error);

            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $id = null;
                $full_name = null;
                $password_hash = '';
                $stmt->bind_result($id, $full_name, $password_hash);
                $stmt->fetch();

                if (password_verify($password, $password_hash)) {
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
            return ['status' => false, 'message' => $this->debug ? "Error: " . $e->getMessage() : "Login failed"];
        }
    }

    /**
     * Register admin
     */
    public function register($full_name, $email, $phone, $password, $confirm_password) {
        try {
            if (empty($full_name) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
                throw new Exception("All fields are required!");
            }

            if ($password !== $confirm_password) {
                throw new Exception("Passwords do not match!");
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format!");
            }

            if (!preg_match('/^[0-9]{10}$/', $phone)) {
                throw new Exception("Phone number must be 10 digits!");
            }

            // Check if email or phone already exists
            $stmt = $this->conn->prepare("SELECT id FROM admins WHERE email = ? OR phone = ?");
            $stmt->bind_param("ss", $email, $phone);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                throw new Exception("Email or phone already exists!");
            }

            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->conn->prepare("INSERT INTO admins (full_name, email, phone, password_hash, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssss", $full_name, $email, $phone, $password_hash);

            if ($stmt->execute()) {
                return ['status' => true, 'message' => 'Registration successful!', 'redirect' => 'adminlogin.php'];
            } else {
                throw new Exception("Database error: " . $this->conn->error);
            }

        } catch (Exception $e) {
            return ['status' => false, 'message' => $this->debug ? $e->getMessage() : "Registration failed"];
        }
    }
}
?>
