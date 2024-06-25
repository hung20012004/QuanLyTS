<div class="modal" id="showSinhvienModal_<?php echo $sinhvien['ID']; ?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Sửa thông tin</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row">
                <div class="col-2"></div>
                <form action="route.php?model=sinhvien&action=edit&id=<?php echo $sinhvien['ID']; ?>" class="col-9"
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
                    <?php
                    // Chuyển đổi ngày tháng từ định dạng khác sang 'YYYY-MM-DD' nếu cần
                        $ngaySinhDefault = date('Y-m-d', strtotime($sinhvien['NgaySinh']));
                    ?>
                    <div class="row py-1">
                        <label class="col-2" for="NgaySinh">Ngày Sinh</label>
                        <input class="col-8" type="date" id="NgaySinh" name="NgaySinh" required
                            value="<?php echo $ngaySinhDefault; ?>"><br>
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
                <button id="btnSubmit" type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>

        </div>
    </div>
</div>