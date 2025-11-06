<?php
require_once 'config/database.php';

class Auth {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        
        // Start session only once
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    // Register new user
    public function register($firstName, $lastName, $email, $password, $phoneNumber) {
        try {
            // Check if email already exists
            if ($this->emailExists($email)) {
                return ['success' => false, 'message' => 'Email already exists. Please use a different email.'];
            }
            
            // Validate input
            $validation = $this->validateRegistration($firstName, $lastName, $email, $password, $phoneNumber);
            if (!$validation['valid']) {
                return ['success' => false, 'message' => $validation['message']];
            }
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $stmt = $this->db->prepare("
                INSERT INTO users (first_name, last_name, email, password, phone_number, created_at) 
                VALUES (?, ?, ?, ?, ?, UNIX_TIMESTAMP())
            ");
            
            $result = $stmt->execute([$firstName, $lastName, $email, $hashedPassword, $phoneNumber]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Account created successfully!'];
            } else {
                return ['success' => false, 'message' => 'Failed to create account. Please try again.'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error occurred.'];
        }
    }
    
    // Login user
    public function login($email, $password) {
        try {
            // Get user by email
            $stmt = $this->db->prepare("SELECT user_id, first_name, last_name, email, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return ['success' => false, 'message' => 'Account does not exist. Please check your email or create a new account.'];
            }
            
            // Verify password
            if (!password_verify($password, $user['password'])) {
                return ['success' => false, 'message' => 'Invalid password. Please try again.'];
            }
            
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['logged_in'] = true;
            
            return [
                'success' => true, 
                'message' => 'Login successful!',
                'user' => [
                    'id' => $user['user_id'],
                    'name' => $user['first_name'] . ' ' . $user['last_name'],
                    'email' => $user['email']
                ]
            ];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error occurred.'];
        }
    }
    
    // Check if email exists
    private function emailExists($email) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }
    
    // Validate registration data
    private function validateRegistration($firstName, $lastName, $email, $password, $phoneNumber) {
        $errors = [];
        
        if (empty($firstName) || strlen($firstName) < 2) {
            $errors[] = 'First name must be at least 2 characters long.';
        }
        
        if (empty($lastName) || strlen($lastName) < 2) {
            $errors[] = 'Last name must be at least 2 characters long.';
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }
        
        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters long.';
        }
        
        if (empty($phoneNumber) || strlen($phoneNumber) < 10) {
            $errors[] = 'Please enter a valid phone number.';
        }
        
        return [
            'valid' => empty($errors),
            'message' => implode(' ', $errors)
        ];
    }
    
    // Check if user is logged in
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    // Get current user
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            // Get full user data from database
            $stmt = $this->db->prepare("SELECT user_id, first_name, last_name, email, phone_number FROM users WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $user;
        }
        return null;
    }
    
    // Logout user
    public function logout() {
        session_destroy();
        return ['success' => true, 'message' => 'Logged out successfully.'];
    }
}
?>
