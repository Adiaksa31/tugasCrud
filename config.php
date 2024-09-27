<?php
$connect = new mysqli('localhost','root','', 'db_crud');

if ($connect->connect_error) {
    die("Koneksi gagal: " . $connect->connect_error);
}
?>
