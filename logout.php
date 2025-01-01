<?php
session_start(); // Memulai session
session_destroy(); // Menghapus session
header("Location: login.php"); // Redirect ke halaman login
exit;
?>
