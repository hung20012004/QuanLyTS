<?php
// models/User.php

class User {
    private $conn;
    private $table_name = "users";
    public $user_id;
    public $email;
    public $ten;
    public $khoa;
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
        $query = "INSERT INTO " . $this->table_name . " SET email=:email, ten=:ten, password=:password, role=:role, khoa=:khoa";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->ten = htmlspecialchars(strip_tags($this->ten));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->khoa = htmlspecialchars(strip_tags($this->khoa));

        // bind values
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':ten', $this->ten);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':khoa', $this->khoa);

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

    public function readKyThuat() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE role = 'KyThuat' ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cập nhật thông tin người dùng
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET email = :email, ten = :ten, password = :password, khoa=:khoa,role = :role,avatar=:avatar WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->khoa = htmlspecialchars(strip_tags($this->khoa));
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
        $stmt->bindParam(':khoa', $this->khoa);
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

    public function getUsersByRole() {
    $sql = "SELECT role, COUNT(*) as total FROM users GROUP BY role";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $usersByRole = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Tính tổng số lượng người dùng
    $totalUsers = array_sum(array_column($usersByRole, 'total'));

    return [
        'usersByRole' => $usersByRole,
        'totalUsers' => $totalUsers
    ];
}

    // Phương thức lấy danh sách người dùng theo role sắp xếp theo tên
    public function getUsersByRoleSortedByName($role) {
        $sql = "SELECT * FROM users WHERE role = :role ORDER BY ten ASC"; // Sắp xếp theo tên (ten là trường lưu tên người dùng)
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function emailExists($email) {
        $query = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}
?>
