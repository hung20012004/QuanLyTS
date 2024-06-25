<?php
include_once 'config/database.php';

class ThanhLyController {

    public function __construct() {
    }

    public function index() {
        $content = 'views/welcome.php';
        include('views/layouts/auth.php');
    }

    public function create() {

    }

    public function edit($id) {
        
    }

    public function delete($id) {
        
    }
    public function saveMultiple() {
        
    }
}
?>
