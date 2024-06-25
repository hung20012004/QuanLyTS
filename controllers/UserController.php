<?php
include_once 'config/database.php';
include_once 'models/User.php';

class UserController extends Controller {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    public function index() {
        $stmt = $this->user->read();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $content = 'views/users/index.php';
        include('views/layouts/base.php');
    }

    public function create() {
        if ($_POST) {
            $this->user->email = $_POST['email'];
            $this->user->ten = $_POST['ten'];
            $this->user->password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $this->user->role = $_POST['role'];
            if ($this->user->create()) {
                header("Location: index.php?model=user");
            }
        }
        $content = 'views/users/create.php';
        include('views/layouts/base.php');
    }

    public function edit($id) {
        if ($_POST) {
            $this->user->user_id = $id;
            $this->user->email = $_POST['email'];
            $this->user->ten = $_POST['ten'];
            $this->user->password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $this->user->role = $_POST['role'];
            if ($this->user->update()) {
                header("Location: index.php?model=user");
            }
        } else {
            $user = $this->user->readById($id);
            $content = 'views/users/edit.php';
            include('views/layouts/base.php');
        }
    }

    public function delete($id) {
        if ($this->user->delete($id)) {
            header("Location: index.php?model=user");
        }
    }
}
?>
