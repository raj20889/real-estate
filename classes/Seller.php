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
            if ($this->debug) {
                return ['status' => false, 'message' => "Error: " . $e->getMessage()];
            } else {
                return ['status' => false, 'message' => "Login failed"];
            }
        }
    }
}
?>
