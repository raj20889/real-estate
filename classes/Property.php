<?php
require_once __DIR__ . '/../db/Database.php';

class Property {
    private $conn;
    private $debug = true;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Check if user is logged in
    public function isUserLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    // Get logged-in user ID
    public function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    // Submit inquiry
    public function submitInquiry($property_id, $message) {
        try {
            $user_id = $this->getUserId();
            if (!$user_id) throw new Exception("User not logged in.");

            $inquiry_date = date("Y-m-d");
            $stmt = $this->conn->prepare("INSERT INTO inquiries (property_id, user_id, inquiry_date, message, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("iiss", $property_id, $user_id, $inquiry_date, $message);
            
            if ($stmt->execute()) {
                return ['status' => true, 'message' => 'Inquiry submitted successfully!'];
            } else {
                return ['status' => false, 'message' => 'Error submitting inquiry.'];
            }

        } catch(Exception $e) {
            return ['status' => false, 'message' => $this->debug ? $e->getMessage() : 'Error occurred'];
        }
    }
}
?>
