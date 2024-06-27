<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=thanhly&action=index">Thanh lý</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tạo hóa đơn thanh lý</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Thêm hóa đơn thanh lý</h5>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <form action="index.php?model=thanhly&action=create" method="POST">
                        <div id="taisan-container">
                            <div class="form-group taisan-item">
                                <div class="form-group">
                                    <label for="taisan">Tài sản:</label>
                                    <select name="taisans[][taisan_id]" class="form-control taisan-select">
                                        <?php foreach($taisans as $taisan): ?>
                                            <option value="<?= $taisan['tai_san_id']?>"><?= $taisan['ten_tai_san']?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="quantity">Số lượng:</label>
                                    <input type="number" name="taisans[][quantity]" class="form-control quantity-input" min="1" oninput="updateTotalPrice(this)">
                                </div>
                                <div class="form-group">
                                    <label for="gia_thanh_ly">Giá thanh lý:</label>
                                    <input type="number" name="taisans[][gia_thanh_ly]" class="form-control gia-thanh-ly-input" min="0" >
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ngay_thanh_ly">Ngày thanh lý:</label>
                            <input type="date" name="ngay_thanh_ly" class="form-control" required>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="px-4 py-4 bg-white shadow-md mb-2 rounded">
                                    <table id="taisan-list" class="table">
                                        <thead>
                                            <tr>
                                                <th>Mã tài sản</th>
                                                <th>Tên tài sản</th>
                                                <th>Giá thanh lý</th>
                                                <th>Số lượng</th>
                                                <th>Tổng</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                    <div style="text-align: right"><strong>Tổng cộng: </strong><span id="total-amount">0</span> VND</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row justify-content-end">
                            <button type="button" class="btn btn-success" onclick="addTaiSan()">Thêm tài sản</button>
                            <button type="submit" class="btn btn-primary" name="btnThem">Tạo</button>
                        </div>
                        <input type="hidden" id="hidden-taisans" name="hidden_taisans">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    let totalAmount = 0;
    let taisansData = [];

    function addTaiSan() {
        const taisanSelect = $('.taisan-item:last .taisan-select');
        const taisanId = taisanSelect.val();
        const taisanName = taisanSelect.find('option:selected').text();
        const taisanPrice = parseFloat(taisanSelect.find('option:selected').data('price'));
        const quantity = parseInt($('.taisan-item:last .quantity-input').val());
        let giaThanhLy = parseFloat($('.taisan-item:last .gia-thanh-ly-input').val());

        // Kiểm tra nếu đã có tài sản này trong bảng và số tiền nhập không khớp
        if (isTaisanExists(taisanId)) {
            const existingRow = getExistingTaisanRow(taisanId);
            const existingGiaThanhLy = parseFloat(existingRow.find('td:eq(2)').text().replace(/\./g, "").replace(' VND', '').trim());

            if (existingGiaThanhLy !== giaThanhLy) {
                alert('Bạn đã nhập cùng mặt hàng với số tiền khác. Vui lòng nhập lại.');
                return;
            }

            let existingQuantity = parseInt(existingRow.find('td:eq(3)').text().trim());
            let newQuantity = existingQuantity + quantity;
            let newTotal = newQuantity * giaThanhLy;

            existingRow.find('td:eq(3)').text(newQuantity);
            existingRow.find('td:eq(4)').text(newTotal.toLocaleString('vi-VN', { style: 'currency', currency: 'VND' }));

            // Cập nhật dữ liệu tài sản trong mảng taisansData
            taisansData.forEach(taisan => {
                if (taisan.id == taisanId) {
                    taisan.quantity = newQuantity;
                    taisan.total = newTotal;
                }
            });

            totalAmount += quantity * giaThanhLy;
            $('#total-amount').text(totalAmount.toLocaleString('vi-VN', { style: 'currency', currency: 'VND' }));
            taisanSelect.val('');
            $('.quantity-input').val('');
            $('.gia-thanh-ly-input').val('');
            updateHiddenInput();
            return;
        }

        // Nếu giá thành lý chưa được điền thì lấy giá từ dữ liệu đã chọn
        if (!giaThanhLy) {
            giaThanhLy = taisanPrice; // Giá thành lý mặc định bằng giá đã chọn từ dropdown
        }

        if (!taisanId || quantity <= 0 || giaThanhLy < 0) {
            alert('Vui lòng chọn tài sản, nhập số lượng và giá thanh lý hợp lệ.');
            return;
        }

        let taisanTotal = quantity * giaThanhLy;
        const taisanData = {
            id: taisanId,
            name: taisanName,
            quantity: quantity,
            price: giaThanhLy,
            total: taisanTotal
        };
        taisansData.push(taisanData);

        const taisanRow = `
            <tr data-id="${taisanId}">
                <td>${taisanId}</td>
                <td>${taisanName}</td>
                <td>${giaThanhLy.toLocaleString('vi-VN', { style: 'currency', currency: 'VND' })}</td>
                <td>${quantity}</td>
                <td>${taisanTotal.toLocaleString('vi-VN', { style: 'currency', currency: 'VND' })}</td>
                <td><button class="btn btn-danger btn-sm" id="xoa-taisan">Xóa</button></td>
            </tr>
        `;
        $('#taisan-list tbody').append(taisanRow);

        totalAmount += taisanTotal;
        $('#total-amount').text(totalAmount.toLocaleString('vi-VN', { style: 'currency', currency: 'VND' }));
        taisanSelect.val('');
        $('.quantity-input').val('');
        $('.gia-thanh-ly-input').val('');
        updateHiddenInput();
    }

    // Cập nhật giá trị của hidden input
    function updateHiddenInput() {
        $('#hidden-taisans').val(JSON.stringify(taisansData));
    }

    // Hàm kiểm tra xem tài sản đã tồn tại trong bảng chưa
    function isTaisanExists(taisanId) {
        let exists = false;
        $('#taisan-list tbody tr').each(function() {
            if ($(this).data('id') == taisanId) {
                exists = true;
                return false; // Thoát vòng lặp
            }
        });
        return exists;
    }

    // Lấy hàng tài sản đã tồn tại trong bảng
    function getExistingTaisanRow(taisanId) {
        let existingRow = null;
        $('#taisan-list tbody tr').each(function() {
            if ($(this).data('id') == taisanId) {
                existingRow = $(this);
                return false; // Thoát vòng lặp
            }
        });
        return existingRow;
    }

    $(document).ready(function() {
        // Xử lý sự kiện khi nhấn nút Xóa trong form tài sản
        $(document).on('click', '#xoa-taisan', function(event) {
            event.preventDefault(); // Ngăn chặn hành động mặc định của nút

            const row = $(this).closest('tr');
            const giaThanhLy = parseFloat(row.find('td:eq(2)').text().replace(/\./g, "").replace(' VND', '').trim());
            const quantity = parseInt(row.find('td:eq(3)').text().trim());
            const taisanTotal = giaThanhLy * quantity;
            totalAmount -= taisanTotal;
            $('#total-amount').text(totalAmount.toLocaleString('vi-VN', { style: 'currency', currency: 'VND' }));
            row.remove();
            updateHiddenInput(); // Cập nhật lại giá trị hidden input
        });

        // Xử lý khi thêm tài sản vào bảng
        $(document).on('click', '#add-taisan-btn', function(event) {
            event.preventDefault(); // Ngăn chặn hành động mặc định của nút

            addTaiSan();
        });
    });
</script>