<?php
// models/Auth.php

class Auth {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getUserByID($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = :id");
        $stmt->bindParam(':email', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function register($email, $password) {
        // Kiểm tra xem email đã tồn tại chưa
        $existingUser = $this->getUserByEmail($email);
        if ($existingUser) {
            return false; // Người dùng đã tồn tại
        }

        // Mã hóa mật khẩu trước khi lưu vào database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Thêm người dùng mới vào database
        $stmt = $this->db->prepare("INSERT INTO users (email, password, role) VALUES (:email, :password, 'user')");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        return $stmt->execute();
    }
}
?>
