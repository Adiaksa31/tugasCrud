<?php
session_start();
include 'config.php';

$id = $_GET['id'];
$sql = "DELETE FROM users WHERE id = $id";

if ($connect->query($sql)) {
    $_SESSION['message'] = 'Data berhasil dihapus'; 
    header('Location: index.php');
} else {
    echo "Gagal menghapus data: " . $connect->error;
}
?>
