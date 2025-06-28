USE quanlytaisan;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(50) NOT NULL UNIQUE,
    ten VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('NhanVien','QuanLy','KyThuat','NhanVienQuanLy') NOT NULL,
    khoa ENUM('HTTT','CNTT','KT','Co khi','Cong trinh','Moi truong-ATGT'),
    chuc_vu ENUM('Truong khoa','Truong phong','Giang vien','Nhan vien hanh chinh'), 
    avatar VARCHAR(255)
);

-- tài sản
CREATE TABLE loai_tai_san (
    loai_tai_san_id INT AUTO_INCREMENT PRIMARY KEY,
    ten_loai_tai_san VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE tai_san (
    tai_san_id INT AUTO_INCREMENT PRIMARY KEY,
    ten_tai_san VARCHAR(100) NOT NULL,
    mo_ta TEXT,
    loai_tai_san_id INT NOT NULL,
    CONSTRAINT fk_loai_tai_san_id FOREIGN KEY (loai_tai_san_id) REFERENCES loai_tai_san(loai_tai_san_id)
);

-- Vị trí
CREATE TABLE vi_tri (
    vi_tri_id INT AUTO_INCREMENT PRIMARY KEY,
    ten_vi_tri VARCHAR(100) NOT NULL,
    khoa ENUM('HTTT','CNTT','KT','Co khi','Cong trinh','Moi truong-ATGT') NOT NULL
);

CREATE TABLE vi_tri_chi_tiet (
    vi_tri_chi_tiet_id INT PRIMARY KEY AUTO_INCREMENT,    
    vi_tri_id INT,
    so_luong INT,
    tai_san_id INT,
    CONSTRAINT fk_vi_tri_chi_tiet_tai_san_id FOREIGN KEY (tai_san_id) REFERENCES tai_san(tai_san_id),
    CONSTRAINT fk_vi_tri_chi_tiet_vi_tri_id FOREIGN KEY (vi_tri_id) REFERENCES vi_tri(vi_tri_id)
);

-- Phiếu nhập
CREATE TABLE phieu_nhap_tai_san (
    phieu_nhap_tai_san_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    user_duyet_id INT,
    ngay_tao DATE NOT NULL,
    ngay_xac_nhan DATE,
    ngay_nhap DATE,
    ghi_chu TEXT,
    trang_thai ENUM('KhongDuyet','DangChoPheDuyet','DaPheDuyet','DaNhap') NOT NULL,
    CONSTRAINT fk_phieu_nhap_user_id FOREIGN KEY (user_id) REFERENCES `users`(user_id) ON UPDATE CASCADE,
    CONSTRAINT fk_phieu_nhap_user_duyet_id FOREIGN KEY (user_duyet_id) REFERENCES `users`(user_id) ON UPDATE CASCADE
);

CREATE TABLE chi_tiet_phieu_nhap_tai_san (
    chi_tiet_id INT AUTO_INCREMENT PRIMARY KEY,
    phieu_nhap_tai_san_id INT,
    tai_san_id INT,
    so_luong INT NOT NULL,
    CONSTRAINT fk_chi_tiet_phieu_nhap_id FOREIGN KEY (phieu_nhap_tai_san_id) REFERENCES phieu_nhap_tai_san(phieu_nhap_tai_san_id) ON DELETE CASCADE,
    CONSTRAINT fk_chi_tiet_phieu_nhap_tai_san_id FOREIGN KEY (tai_san_id) REFERENCES tai_san(tai_san_id) ON UPDATE CASCADE
);

-- Phiếu bàn giao
CREATE TABLE phieu_ban_giao (
    phieu_ban_giao_id INT AUTO_INCREMENT PRIMARY KEY,
    user_ban_giao_id INT,
    user_nhan_id INT,
    user_duyet_id INT,
    vi_tri_id INT,
    ghi_chu TEXT,

    ngay_gui DATE,
    ngay_kiem_tra DATE,
    ngay_duyet DATE,
    ngay_ban_giao DATE,

    trang_thai ENUM('DaHuy','DaLuu','DaGui','DaKiemTra','DangChoPheDuyet','DaPheDuyet','DaGiao','KhongDuyet') NOT NULL,
    CONSTRAINT fk_phieu_ban_giao_user_ban_giao_id FOREIGN KEY (user_ban_giao_id) REFERENCES `users`(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_phieu_ban_giao_user_nhan_id FOREIGN KEY (user_nhan_id) REFERENCES `users`(user_id) ON UPDATE CASCADE,
    CONSTRAINT fk_phieu_ban_giao_vi_tri_id FOREIGN KEY (vi_tri_id) REFERENCES vi_tri(vi_tri_id),
    CONSTRAINT fk_phieu_ban_giao_user_duyet_id FOREIGN KEY (user_duyet_id) REFERENCES `users`(user_id) ON UPDATE CASCADE
);

CREATE TABLE phieu_ban_giao_chi_tiet (
    phieu_ban_giao_chi_tiet_id INT PRIMARY KEY AUTO_INCREMENT,    
    phieu_ban_giao_id INT,
    tinh_trang ENUM('Moi','Tot','Kha','TrungBinh','Kem','Hong') NOT NULL,
    so_luong INT,
    tai_san_id INT,
    CONSTRAINT fk_phieu_ban_giao_chi_tiet_tai_san_id FOREIGN KEY (tai_san_id) REFERENCES tai_san(tai_san_id),
    CONSTRAINT fk_phieu_ban_giao_chi_tiet_phieu_ban_giao_id FOREIGN KEY (phieu_ban_giao_id) REFERENCES phieu_ban_giao(phieu_ban_giao_id)
);

CREATE TABLE phieu_tra (
    phieu_tra_id INT AUTO_INCREMENT PRIMARY KEY,
    user_tra_id INT,
    user_nhan_id INT,
    user_duyet_id INT,

    ngay_gui DATE,
    ngay_kiem_tra DATE,
    ngay_duyet DATE,

    ngay_tra DATE,
    ghi_chu TEXT,
    trang_thai ENUM('DaLuu','DaGui','DaHuy','DangChoPheDuyet','DaPheDuyet','DaTra','KhongDuyet') NOT NULL,
    CONSTRAINT fk_phieu_tra_user_tra_id FOREIGN KEY (user_tra_id) REFERENCES `users`(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_phieu_tra_user_nhan_id FOREIGN KEY (user_nhan_id) REFERENCES `users`(user_id) ON UPDATE CASCADE,
    CONSTRAINT fk_phieu_tra_user_duyet_id FOREIGN KEY (user_duyet_id) REFERENCES `users`(user_id) ON UPDATE CASCADE
);

CREATE TABLE phieu_tra_chi_tiet (
    phieu_tra_chi_tiet_id INT PRIMARY KEY AUTO_INCREMENT,    
    phieu_tra_id INT,
    tinh_trang ENUM('Moi','Tot','Kha','TrungBinh','Kem','Hong'),
    so_luong INT,
    vi_tri_id INT,
    tai_san_id INT,
    CONSTRAINT fk_phieu_tra_chi_tiet_tai_san_id FOREIGN KEY (tai_san_id) REFERENCES tai_san(tai_san_id),
    CONSTRAINT fk_phieu_tra_chi_tiet_vi_tri_id FOREIGN KEY (vi_tri_id) REFERENCES vi_tri(vi_tri_id),
    CONSTRAINT fk_phieu_tra_chi_tiet_phieu_tra_id FOREIGN KEY (phieu_tra_id) REFERENCES phieu_tra(phieu_tra_id)
);

-- Phiếu thanh lý
CREATE TABLE phieu_thanh_ly (
    phieu_thanh_ly_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    user_duyet_id INT,
    ngay_tao DATE NOT NULL,
    ngay_xac_nhan DATE,
    ngay_thanh_ly DATE,
    ghi_chu TEXT,
    trang_thai ENUM('KhongDuyet','DangChoPheDuyet','DaPheDuyet','DaThanhLy') NOT NULL,
    CONSTRAINT fk_phieu_thanh_ly_user_id FOREIGN KEY (user_id) REFERENCES `users`(user_id) ON UPDATE CASCADE,
    CONSTRAINT fk_phieu_thanh_ly_user_duyet_id FOREIGN KEY (user_duyet_id) REFERENCES `users`(user_id) ON UPDATE CASCADE
);

CREATE TABLE chi_tiet_phieu_thanh_ly (
    chi_tiet_id INT AUTO_INCREMENT PRIMARY KEY,
    phieu_thanh_ly_id INT,
    tai_san_id INT,
    tinh_trang ENUM('Moi','Tot','Kha','TrungBinh','Kem','Hong') ,
    so_luong INT NOT NULL,
    CONSTRAINT fk_chi_tiet_phieu_thanh_ly_tai_san_id FOREIGN KEY (tai_san_id) REFERENCES tai_san(tai_san_id) ON UPDATE CASCADE,
    CONSTRAINT fk_chi_tiet_phieu_thanh_ly_id FOREIGN KEY (phieu_thanh_ly_id) REFERENCES phieu_thanh_ly(phieu_thanh_ly_id) ON DELETE CASCADE
);  

-- Bảo trì
CREATE TABLE phieu_sua(
    phieu_sua_id INT AUTO_INCREMENT PRIMARY KEY,
    ngay_yeu_cau DATE NOT NULL,
    ngay_sua_chua DATE,
    ngay_hoan_thanh DATE,
    mo_ta TEXT,
    vi_tri_id INT NOT NULL,
    user_sua_chua_id INT,
    user_yeu_cau_id INT,
    user_duyet_id INT,
    trang_thai ENUM('DaGui','DaNhan','DaHoanThanh','YeuCauHuy','Huy') NOT NULL,
    CONSTRAINT fk_phieu_sua_user_sua_id FOREIGN KEY (user_sua_chua_id) REFERENCES `users`(user_id) ON UPDATE CASCADE,
    CONSTRAINT fk_phieu_sua_user_yeu_cau_id FOREIGN KEY (user_yeu_cau_id) REFERENCES `users`(user_id) ON UPDATE CASCADE,
    CONSTRAINT fk_phieu_sua_user_duyet_id FOREIGN KEY (user_duyet_id) REFERENCES `users`(user_id) ON UPDATE CASCADE,
    CONSTRAINT fk_phieu_sua_vi_tri_id FOREIGN KEY (vi_tri_id) REFERENCES `vi_tri`(vi_tri_id) ON UPDATE CASCADE
);

CREATE TABLE chi_tiet_phieu_sua(
    chi_tiet_phieu_sua_id INT AUTO_INCREMENT PRIMARY KEY,
    phieu_sua_id INT,
    tai_san_id INT,
    noi_dung_sua TEXT NOT NULL,
    tinh_trang ENUM('Moi','Tot','Kha','TrungBinh','Kem','Hong'),
    CONSTRAINT fk_chi_tiet_phieu_sua_id FOREIGN KEY (phieu_sua_id) REFERENCES phieu_sua(phieu_sua_id) ON DELETE CASCADE,
    CONSTRAINT fk_chi_tiet_phieu_sua_tai_san_id FOREIGN KEY (tai_san_id) REFERENCES tai_san(tai_san_id) ON UPDATE CASCADE
);

CREATE TABLE lich_bao_tri (
    lich_bao_tri_id INT AUTO_INCREMENT PRIMARY KEY,
    ngay_bao_tri DATE NOT NULL,
    tai_san_id INT,
    noi_dung TEXT,
    ghi_chu TEXT,
    CONSTRAINT fk_lich_bao_tri_tai_san_id FOREIGN KEY (tai_san_id) REFERENCES tai_san(tai_san_id)
);
