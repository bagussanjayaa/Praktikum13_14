<?php
session_start();

$base_path = 'modules/';

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$is_auth_page = ($page == 'auth/login' || $page == 'auth/logout');

if (!$is_logged_in && !$is_auth_page) {
    header('Location: index.php?page=auth/login');
    exit();
} 

if ($is_logged_in && $page == 'auth/login') {
    header('Location: index.php?page=dashboard');
    exit();
}

$content_file = '';
if ($page === 'dashboard') {
    $content_file = 'views/dashboard.php';
} else {
    $safe_page = preg_replace('/[^a-z0-9\/\-_]/i', '', $page);
    $content_file = $base_path . $safe_page . '.php';
}

require_once('views/header.php');

echo '<div class="content-wrapper">'; 

if (file_exists($content_file)) {
    require_once('config/database.php');
    require_once($content_file);
} else {
    echo '<h2>404 Not Found</h2>';
    echo '<p>Halaman <code>' . htmlspecialchars($page) . '</code> tidak ditemukan.</p>';
}

echo '</div>'; 

require_once('views/footer.php');
?>