<div class="modal-header text-center">
    <h4 class=" ">Thêm sinh viên</h4>
</div>
<div class="modal-body row">
    <div class="col-2"></div>
    <form action="route.php?model=sinhvien&action=create" class="col-9" method="post">
        <div class="row py-1">
            <label class="col-2" for="MaSV">Mã sinh viên</label>
            <input class="col-8" type="text" id="MaSV" name="MaSV" required><br>
        </div>
        <div class="row py-1">
            <label class="col-2" for="Ten">Tên</label>
            <input class="col-8" type="Ten" id="Ten" name="Ten" required><br>
        </div>
        <div class="row py-1">
            <label class="col-2" for="NgaySinh">Ngày Sinh</label>
            <input class="col-8" type="date" id="NgaySinh" name="NgaySinh" required><br>
        </div>
        <div class="row py-1">
            <label class="col-2" for="DiemChuyenCan">Điểm chuyên cần</label>
            <input class="col-8" type="number" step="0.01" min="0" max="10" id="DiemChuyenCan" name="DiemChuyenCan" required><br>
        </div>
        <div class="row py-1">
            <label class="col-2" for="DiemGiuaKy">Điểm giữa kỳ</label>
            <input class="col-8" type="number" step="0.01"min="0" max="10" id="DiemGiuaKy" name="DiemGiuaKy" required><br>
        </div>
        <div class="row py-1">
            <label class="col-2" for="DiemCuoiKy">Điểm cuối kỳ</label>
            <input class="col-8" type="number"step="0.01" min="0" max="10" id="DiemCuoiKy" name="DiemCuoiKy" required><br>
        </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary">Add</button>
    </form>
</div>