<?php
include_once 'config/database.php';

class Controller {

    public function __construct() {
    }

    public function index() {
        $content = 'views/welcome.php';
        include('views/layouts/base.php');
    }

    public function create() {

    }

    public function edit($id) {
        
    }

    public function delete($id) {
        
    }
    public function show($id) {
        
    }
    public function register() {}
    public function login() {}
    public function logout() {}
    public function profile() {}
    public function export(){}
    public function statistics(){}
    public function forgot_password_request(){}
    public function reset_password(){}
    public function detail($id){}
}
?>
