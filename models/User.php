<?php
// models/User.php

class User {
    private $conn;
    private $table_name = "users";
    public $user_id;
    public $email;
    public $ten;
    public $password;
    public $role;
    public $avatar;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Đọc tất cả người dùng
    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Tạo người dùng mới
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET email=:email, ten=:ten, password=:password, role=:role";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->ten = htmlspecialchars(strip_tags($this->ten));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->role = htmlspecialchars(strip_tags($this->role));

        // bind values
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':ten', $this->ten);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':role', $this->role);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Đọc thông tin người dùng theo ID
    public function readById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật thông tin người dùng
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET email = :email, ten = :ten, password = :password, role = :role,avatar=:avatar WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->ten = htmlspecialchars(strip_tags($this->ten));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->avatar = htmlspecialchars(strip_tags($this->avatar));

        // bind values
        $stmt->bindParam(':avatar', $this->avatar);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':ten', $this->ten);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':user_id', $this->user_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Xóa người dùng
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
