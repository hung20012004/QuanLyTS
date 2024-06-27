<?php
require_once 'database.php';

$database = new Database();
$db = $database->getConnection();

$ten_vi_tri = 'Kho';
$vi_tri_id =1;

// Câu lệnh SQL INSERT
$sql = "INSERT INTO vi_tri (vi_tri_id ,ten_vi_tri) VALUES (:vi_tri_id, :ten_vi_tri)";

// Chuẩn bị câu lệnh SQL và gắn các giá trị
$stmt = $db->prepare($sql);
$stmt->bindParam(':vi_tri_id', $vi_tri_id);
$stmt->bindParam(':ten_vi_tri', $ten_vi_tri);


// Thực thi câu lệnh
if ($stmt->execute()) {
    echo "Tạo vị trí thành công!";
} else {
    echo "Lỗi " . $stmt->errorInfo()[2];
}
?>
