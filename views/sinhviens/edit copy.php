<div class="modal" id="showAuthorModal_<?php echo $author['id']; ?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Thông tin tác giả</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row">
                <div class="col-2"></div>
                <form action="route.php?model=sinhvien&action=edit&id=<?php echo $sinhvien['id']; ?>" class="col-9"
                    method="post">
                    <div class="row py-1">
                        <label class="col-2" for="MaSV">Mã sinh viên</label>
                        <input class="col-8" type="text" id="MaSV" name="MaSV" required
                            value="<?php echo $sinhvien['MaSV']; ?>"><br>
                    </div>
                    <div class="row py-1">
                        <label class="col-2" for="Ten">Tên</label>
                        <input class="col-8" type="Ten" id="Ten" name="Ten" required
                            value="<?php echo $sinhvien['Ten']; ?>"><br>
                    </div>
                    <div class="row py-1">
                        <label class="col-2" for="NgaySinh">Ngày Sinh</label>
                        <input class="col-8" type="date" id="NgaySinh" name="NgaySinh" required
                            value="<?php echo $sinhvien['NgaySinh']; ?>"><br>
                    </div>
                    <div class="row py-1">
                        <label class="col-2" for="DiemChuyenCan">Điểm chuyên cần</label>
                        <input class="col-8" type="number" step="0.01" min="0" max="10" id="DiemChuyenCan"
                            name="DiemChuyenCan" required value="<?php echo $sinhvien['DiemChuyenCan']; ?>"><br>
                    </div>
                    <div class="row py-1">
                        <label class="col-2" for="DiemGiuaKy">Điểm giữa kỳ</label>
                        <input class="col-8" type="number" step="0.01" min="0" max="10" id="DiemGiuaKy"
                            name="DiemGiuaKy" required value="<?php echo $sinhvien['DiemGiuaKy']; ?>"><br>
                    </div>
                    <div class="row py-1">
                        <label class="col-2" for="DiemCuoiKy">Điểm cuối kỳ</label>
                        <input class="col-8" type="number" step="0.01" min="0" max="10" id="DiemCuoiKy"
                            name="DiemCuoiKy" required value="<?php echo $sinhvien['DiemCuoiKy']; ?>"><br>
                    </div>
            </div>
            <div class="modal-footer">
                <button id="btnSubmit" type="submit" class="d-none btn btn-primary">Submit</button>
                </form>
                <div id="blockBtnOther">
                    <button id="btnEdit" class="btn btn-primary" type="button">Edit</button>
                    <button type="button" class=" btn btn-primary" onclick="confirmDelete()">
                        Delete
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
<form action="route.php?model=author&action=delete&id=<?php echo $author['id']; ?>" method="post" id="deleteForm"
    style="display: none;">
</form>
<script>
    function confirmDelete() {
        if (confirm("Are you sure you want to delete this item?")) {
            document.getElementById("deleteForm").submit();
        } else {
            console.log('Item not deleted');
        }
    }
</script>