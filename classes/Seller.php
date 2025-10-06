<?php
require_once __DIR__ . '/../db/Database.php';

class Seller {
    private $conn;
    private $debug = true;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
        session_start();
    }

    /**
     * Login seller
     */
    public function login($email, $password) {
        try {
            if (empty($email) || empty($password)) {
                throw new Exception("Both fields are required!");
            }

            $stmt = $this->conn->prepare("SELECT id, name, password FROM sellers WHERE email = ?");
            if (!$stmt) throw new Exception("Prepare statement failed: " . $this->conn->error);

            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    $_SESSION['seller_id'] = $row['id'];
                    $_SESSION['seller_name'] = $row['name'];
                    return ['status' => true, 'message' => 'Login successful', 'redirect' => 'seller_dashboard.php'];
                } else {
                    throw new Exception("Incorrect password!");
                }
            } else {
                throw new Exception("Seller not found!");
            }

        } catch (Exception $e) {
            return ['status' => false, 'message' => $this->debug ? "Error: " . $e->getMessage() : "Login failed"];
        }
    }

    /**
     * Register seller
     */
    public function register($name, $email, $phone, $password) {
        try {
            // Validation
            if (empty($name) || empty($email) || empty($phone) || empty($password)) {
                throw new Exception("All fields are required!");
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format!");
            }
            if (!preg_match('/^\+[0-9]{1,3}[0-9]{4,14}$/', $phone)) {
                throw new Exception("Invalid phone number format! Must include country code (e.g., +1234567890)");
            }
            if (strlen($password) < 8) {
                throw new Exception("Password must be at least 8 characters long!");
            }
            if (!preg_match('/[A-Z]/', $password)) {
                throw new Exception("Password must contain at least one uppercase letter!");
            }
            if (!preg_match('/[a-z]/', $password)) {
                throw new Exception("Password must contain at least one lowercase letter!");
            }
            if (!preg_match('/[0-9]/', $password)) {
                throw new Exception("Password must contain at least one number!");
            }
            if (!preg_match('/[^A-Za-z0-9]/', $password)) {
                throw new Exception("Password must contain at least one special character!");
            }

            // Check if email or phone already exists
            $stmt = $this->conn->prepare("SELECT * FROM sellers WHERE email = ? OR phone = ?");
            $stmt->bind_param("ss", $email, $phone);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                throw new Exception("Email or phone number already registered!");
            }

            // Insert new seller
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->conn->prepare("INSERT INTO sellers (name, email, phone, password, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssss", $name, $email, $phone, $hashed_password);

            if ($stmt->execute()) {
                return ['status' => true, 'message' => 'Registration successful', 'redirect' => 'sellerlogin.php'];
            } else {
                throw new Exception("Database error: " . $this->conn->error);
            }

        } catch (Exception $e) {
            return ['status' => false, 'message' => $this->debug ? $e->getMessage() : "Registration failed"];
        }
    }
}
?>
