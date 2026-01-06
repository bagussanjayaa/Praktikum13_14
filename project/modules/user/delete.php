<?php
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('location: index.php?page=auth/login');
    exit();
}

$id_barang = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$upload_path_relative_to_root = 'assets/gambar/';

if ($id_barang > 0) {
    $stmt_select = $conn->prepare("SELECT gambar FROM data_barang WHERE id_barang = ?");
    $stmt_select->bind_param("i", $id_barang);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    $data = $result->fetch_assoc();
    $stmt_select->close();

    if ($data) {
        $gambar_path = $data['gambar'];
        
        $stmt_delete = $conn->prepare("DELETE FROM data_barang WHERE id_barang = ?");
        $stmt_delete->bind_param("i", $id_barang);

        if ($stmt_delete->execute()) {
            if ($gambar_path && file_exists(dirname(__DIR__, 2) . '/' . $gambar_path)) {
                unlink(dirname(__DIR__, 2) . '/' . $gambar_path);
            }
            
            header('location: index.php?page=user/list');
            exit();
        }
        $stmt_delete->close();
    }
}

header('location: index.php?page=user/list');
exit();
?>