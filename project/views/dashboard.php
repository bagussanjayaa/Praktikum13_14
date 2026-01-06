<?php
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('location: index.php?page=auth/login');
    exit();
}
?>

<div class="container text-center">
    <h2>Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h2>
    <p>Silakan pilih menu di atas untuk mengelola Data Barang.</p>
    
</div>