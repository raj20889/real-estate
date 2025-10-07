<?php
require_once __DIR__ . '/../db/Database.php';

class Feedback {
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

    // Submit feedback
    public function submitFeedback($property_id, $rating, $message) {
        try {
            $user_id = $this->getUserId();
            if (!$user_id) throw new Exception("User not logged in.");

            $stmt = $this->conn->prepare("INSERT INTO feedback (property_id, user_id, rating, comments, submitted_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("iiis", $property_id, $user_id, $rating, $message);

            if ($stmt->execute()) {
                return ['status' => true, 'message' => 'Feedback submitted successfully!'];
            } else {
                return ['status' => false, 'message' => 'Error submitting feedback.'];
            }

        } catch (Exception $e) {
            return ['status' => false, 'message' => $this->debug ? $e->getMessage() : 'Error occurred'];
        }
    }
}
?>
