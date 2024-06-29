USE quanlytaisan;

-- Thêm dữ liệu vào bảng loai_tai_san
INSERT INTO loai_tai_san (ten_loai_tai_san) VALUES
('Không xác định')
('Máy tính và thiết bị viễn thông'),
('Xây dựng, công cụ dụng cụ'),
('Đất đai, nhà xưởng'),
('Phương tiện vận tải'),
('Máy móc, thiết bị sản xuất'),
('Trang thiết bị văn phòng'),
('Thiết bị giảng dạy, nghiên cứu');

-- Thêm dữ liệu vào bảng tai_san
INSERT INTO tai_san (ten_tai_san, mo_ta,  loai_tai_san_id) VALUES
('Máy tính Dell', 'Máy tính để bàn', 2),
('Bàn học sinh', 'Bàn học sinh gỗ',  3),
('Máy chiếu Sony', 'Máy chiếu cao cấp',  8);

-- Thêm dữ liệu vào bảng vi_tri
INSERT INTO vi_tri (ten_vi_tri) VALUES
('Kho'),
('Phòng học 101'),
('Phòng học 102'),
('Phòng họp');

-- Thêm dữ liệu vào bảng nha_cung_cap
INSERT INTO nha_cung_cap (ten_nha_cung_cap,trang_thai) VALUES
('Công ty TNHH ABC', 1),
('Công ty CP XYZ', 1);

-- Thêm dữ liệu vào bảng hoa_don_mua
INSERT INTO hoa_don_mua (ngay_mua, tong_gia_tri, nha_cung_cap_id) VALUES
('2023-01-15', 15000000, 1),
('2023-02-20', 20000000, 2);

-- Thêm dữ liệu vào bảng chi_tiet_hoa_don_mua
INSERT INTO chi_tiet_hoa_don_mua (hoa_don_id, tai_san_id, so_luong, don_gia) VALUES
(1, 1, 5, 3000000),
(2, 2, 20, 500000),
(2, 3, 2, 8000000);

-- Thêm dữ liệu vào bảng vi_tri_chi_tiet
INSERT INTO vi_tri_chi_tiet (chi_tiet_id, vi_tri_id, so_luong) VALUES
(1, 1, 5),
(2, 2, 30),
(3, 3, 2);

-- Thêm dữ liệu vào bảng hoa_don_thanh_ly
INSERT INTO hoa_don_thanh_ly (ngay_thanh_ly, tong_gia_tri) VALUES
('2023-03-15', 5000000);

-- Thêm dữ liệu vào bảng chi_tiet_hoa_don_thanh_ly
INSERT INTO chi_tiet_hoa_don_thanh_ly (hoa_don_id, tai_san_id, so_luong, gia_thanh_ly) VALUES
(1, 1, 2, 2500000);

-- Thêm dữ liệu vào bảng maintenance_schedule
-- INSERT INTO maintenance_schedule (tai_san_id, ngay_bat_dau, ngay_ket_thuc, mo_ta) VALUES
-- (1, '2023-06-01', '2023-06-07', 'Bảo trì máy tính Dell');
