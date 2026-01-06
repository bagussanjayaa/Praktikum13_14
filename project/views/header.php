<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Aplikasi Inventori Modular</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container text-center">
    <header>
        <h1>Aplikasi CRUD Data Barang</h1>
    </header>

    <nav style="margin-bottom: 20px;">
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
            <a href="index.php?page=dashboard">Dashboard</a> |
            <a href="index.php?page=user/list">Data Barang</a> | 
            <a href="index.php?page=user/add">Tambah Barang</a> |
            <a href="index.php?page=auth/logout" style="color: red;">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
        <?php else: ?>
            <a href="index.php?page=auth/login">Login</a>
        <?php endif; ?>
    </nav>
    
    <div id="main-content">
    <div class="content-wrapper">