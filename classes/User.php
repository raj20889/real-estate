<?php
require_once __DIR__ . '/../db/Database.php';

class User {
    private $conn;
    private $debug = true;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Logout user
     */
    public function logout() {
        session_destroy();
    }

    /**
     * Login user
     */
    public function login($email, $password) {
        try {
            if (empty($email) || empty($password)) {
                throw new Exception("Both fields are required!");
            }

            $stmt = $this->conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
            if (!$stmt) throw new Exception("Prepare statement failed: " . $this->conn->error);

            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['password'])) {
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
            return ['status' => false, 'message' => $this->debug ? "Error: " . $e->getMessage() : "Login failed"];
        }
    }

    /**
     * Register user
     */
    public function register($name, $email, $phone, $password, $confirm_password) {
        try {
            if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
                throw new Exception("All fields are required!");
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception("Invalid email format!");
            if (!preg_match('/^\+[0-9]{1,3}[0-9]{4,14}$/', $phone)) throw new Exception("Invalid phone number format!");
            if ($password !== $confirm_password) throw new Exception("Passwords do not match!");
            if (strlen($password) < 8) throw new Exception("Password must be at least 8 characters long!");
            if (!preg_match('/[A-Z]/', $password)) throw new Exception("Password must contain at least one uppercase letter!");
            if (!preg_match('/[a-z]/', $password)) throw new Exception("Password must contain at least one lowercase letter!");
            if (!preg_match('/[0-9]/', $password)) throw new Exception("Password must contain at least one number!");
            if (!preg_match('/[^A-Za-z0-9]/', $password)) throw new Exception("Password must contain at least one special character!");

            // Check existing email or phone
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ? OR phone = ?");
            $stmt->bind_param("ss", $email, $phone);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                throw new Exception("Email or phone number already registered!");
            }

            // Insert user
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $phone, $hashed_password);

            if ($stmt->execute()) {
                return ['status' => true, 'message' => 'Registration successful', 'redirect' => 'user_login.php'];
            } else {
                throw new Exception("Database error: " . $this->conn->error);
            }

        } catch (Exception $e) {
            return ['status' => false, 'message' => $this->debug ? $e->getMessage() : "Registration failed"];
        }
    }

    /**
     * Get user info by ID
     */
    public function getUserById($id) {
        try {
            $stmt = $this->conn->prepare("SELECT id, name, profile_pic FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } catch (Exception $e) {
            if ($this->debug) echo "Error: " . $e->getMessage();
            return null;
        }
    }

    /**
     * Search approved properties
     */
    public function searchProperties($search = '') {
        try {
            $sql = "SELECT * FROM properties WHERE (title LIKE ? OR location LIKE ?) AND status='approved' ORDER BY created_at DESC";
            $stmt = $this->conn->prepare($sql);
            $param = "%$search%";
            $stmt->bind_param("ss", $param, $param);
            $stmt->execute();
            return $stmt->get_result();
        } catch (Exception $e) {
            if ($this->debug) echo "Error: " . $e->getMessage();
            return [];
        }
    }

// Update name, email, and password
public function updateProfile($id, $name, $email, $password = null) {
    try {
        $sql = "UPDATE users SET name=?, email=?";
        $params = [$name, $email];

        if(!empty($password)){
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql .= ", password=?";
            $params[] = $hashed;
        }

        $sql .= " WHERE id=?";
        $params[] = $id;

        $stmt = $this->conn->prepare($sql);

        // Bind dynamically
        $types = str_repeat("s", count($params)-1) . "i";
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
    } catch(Exception $e){
        if($this->debug) echo $e->getMessage();
    }
}

// Update profile picture
public function updateProfilePic($id, $path){
    try {
        $stmt = $this->conn->prepare("UPDATE users SET profile_pic=? WHERE id=?");
        $stmt->bind_param("si", $path, $id);
        $stmt->execute();
    } catch(Exception $e){
        if($this->debug) echo $e->getMessage();
    }
}


}
?>
