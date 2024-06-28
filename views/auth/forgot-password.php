<!-- views/auth/forgot-password.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Quên Mật Khẩu - Quản lý tài sản</title>
    <link href="./vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href=".\assets\css\mystyle.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
</head>

<body class="bg-gradient-primary">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-9">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-password-image"></div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-2">Quên Mật Khẩu?</h1>
                                    <p class="mb-4">Nhập địa chỉ email của bạn bên dưới và chúng tôi sẽ gửi cho bạn liên kết để đặt lại mật khẩu!</p>
                                </div>
                                <form class="user" action="index.php?model=auth&action=forgot_password_request" method="POST">
                                    <div class="form-group">
                                        <input type="email" name="email" class="form-control form-control-user" id="exampleInputEmail" aria-describedby="emailHelp" placeholder="Nhập địa chỉ email..." required>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-user btn-block">Đặt lại mật khẩu</button>
                                </form>
                                <hr>
                                <div class="text-center">
                                    <a class="small" href="index.php?model=auth&action=register">Tạo tài khoản!</a>
                                </div>
                                <div class="text-center">
                                    <a class="small" href="index.php?model=auth&action=login">Đã có tài khoản? Đăng nhập!</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    </div>
    <script src="https://kit.fontawesome.com/1b233c9fdd.js" crossorigin="anonymous"></script>
    <script src="./vendor/jquery/jquery.min.js"></script>
    <script src="./vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="./vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="./assets/js/myscript.js"></script>
</body>
</html>
