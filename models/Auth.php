
<?php
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
        $stmt->bindParam(':id', $id);
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

    public function updateUserProfile($id, $email, $ten, $avatarPath = null, $password=null) {
        if ($avatarPath && $password) {
            $stmt = $this->db->prepare("UPDATE users SET email = :email, ten = :ten, avatar = :avatar,password = :password WHERE user_id = :id");
            $stmt->bindParam(':avatar', $avatarPath);
            $stmt->bindParam(':password', $password); 
        } else {
            if($password){
                $stmt = $this->db->prepare("UPDATE users SET email = :email, ten = :ten,password = :password WHERE user_id = :id");
                $stmt->bindParam(':password', $password); 
            }
            else if($avatarPath){
                $stmt = $this->db->prepare("UPDATE users SET email = :email, ten = :ten, avatar = :avatar WHERE user_id = :id");
                $stmt->bindParam(':avatar', $avatarPath);
            }
            else
                $stmt = $this->db->prepare("UPDATE users SET email = :email, ten = :ten WHERE user_id = :id");
        }  
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':ten', $ten);
        return $stmt->execute();
    }
}
