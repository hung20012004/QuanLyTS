<?php
require_once 'database.php';

// Khởi tạo đối tượng Database
$database = new Database();
$db = $database->getConnection();

// Dữ liệu cần chèn vào bảng users
$email = 'kt@gmail.com';
$ten = 'kt';
$password = 'kt'; // Đây là mật khẩu gốc, bạn cần mã hóa trước khi lưu vào database
$hashed_password = password_hash($password, PASSWORD_BCRYPT); // Mã hóa mật khẩu
$role = 'KyThuat';

// Câu lệnh SQL INSERT
$sql = "INSERT INTO users (email, ten, password, role) VALUES (:email, :ten, :password, :role)";

// Chuẩn bị câu lệnh SQL và gắn các giá trị
$stmt = $db->prepare($sql);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':ten', $ten);
$stmt->bindParam(':password', $hashed_password);
$stmt->bindParam(':role', $role);

// Thực thi câu lệnh
if ($stmt->execute()) {
    echo "Tạo tài khoản kt thành công!";
} else {
    echo "Lỗi khi tạo tài khoản kt: " . $stmt->errorInfo()[2];
}
?>
