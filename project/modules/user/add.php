<?php
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo "<p>Akses ditolak. Silakan login untuk menambah data.</p>";
    return;
}

error_reporting(E_ALL);
$message = '';
$upload_path_relative_to_root = 'assets/gambar/'; 

if (isset($_POST['submit'])) {
    if (!isset($conn)) {
        die("<p style='color:red;'>Error: Koneksi database tidak tersedia saat proses submit.</p>");
    }
    
    $nama = trim($_POST['nama']);
    $kategori = trim($_POST['kategori']);
    $harga_jual = (int) filter_var($_POST['harga_jual'], FILTER_SANITIZE_NUMBER_INT);
    $harga_beli = (int) filter_var($_POST['harga_beli'], FILTER_SANITIZE_NUMBER_INT);
    $stok = (int) filter_var($_POST['stok'], FILTER_SANITIZE_NUMBER_INT);
    
    $file_gambar = $_FILES['file_gambar'];
    $gambar = null; 

    if ($file_gambar['error'] == 0) {
        $filename = time() . '_' . str_replace(' ', '_', basename($file_gambar['name'])); 

        $absolute_upload_dir = dirname(__DIR__, 2) . '/' . $upload_path_relative_to_root; 

        if (!is_dir($absolute_upload_dir)) {
            mkdir($absolute_upload_dir, 0777, true);
        }

        $destination_path = $absolute_upload_dir . $filename;
        
        if (move_uploaded_file($file_gambar['tmp_name'], $destination_path)) {
            $gambar = $upload_path_relative_to_root . $filename;
        } else {
            $message .= "<p style='color:orange;'>Warning: Gagal mengupload file gambar. Cek izin folder.</p>";
        }
    }

    if (empty($nama) || empty($kategori) || $harga_jual <= 0 || $harga_beli <= 0 || $stok < 0) {
        $message .= "<p style='color:red;'>Semua field wajib diisi dan harus bernilai positif.</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO data_barang (nama, kategori, harga_jual, harga_beli, stok, gambar) VALUES (?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("ssiiis", $nama, $kategori, $harga_jual, $harga_beli, $stok, $gambar);

        if ($stmt->execute()) {
            $message = "<p style='color:green; font-weight:bold;'>Data barang **" . htmlspecialchars($nama) . "** berhasil ditambahkan.</p>";
            
            header('location: index.php?page=user/list'); 
            exit();
        } else {
            $message .= "<p style='color:red;'>Error: Data gagal disimpan. " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
}
?>

<h2>Tambah Data Barang Baru</h2>
<?php echo $message; ?>

<div class="login-box" style="max-width: 400px; padding: 20px 30px;">
    <form method="post" action="index.php?page=user/add" enctype="multipart/form-data">
        <div class="input">
            <label>Nama Barang</label>
            <input type="text" name="nama" required value="<?php echo isset($nama) ? htmlspecialchars($nama) : ''; ?>"/>
        </div>
        <div class="input">
            <label>Kategori</label>
            <select name="kategori" required>
                <?php $selected_kategori = isset($kategori) ? $kategori : ''; ?>
                <option value="Komputer" <?php echo ($selected_kategori == 'Komputer') ? 'selected' : ''; ?>>Komputer</option>
                <option value="Elektronik" <?php echo ($selected_kategori == 'Elektronik') ? 'selected' : ''; ?>>Elektronik</option>
                <option value="Hand Phone" <?php echo ($selected_kategori == 'Hand Phone') ? 'selected' : ''; ?>>Hand Phone</option>
            </select>
        </div>
        <div class="input">
            <label>Harga Jual</label>
            <input type="number" name="harga_jual" required value="<?php echo isset($harga_jual) ? htmlspecialchars($harga_jual) : ''; ?>"/>
        </div>
        <div class="input">
            <label>Harga Beli</label>
            <input type="number" name="harga_beli" required value="<?php echo isset($harga_beli) ? htmlspecialchars($harga_beli) : ''; ?>"/>
        </div>
        <div class="input">
            <label>Stok</label>
            <input type="number" name="stok" required value="<?php echo isset($stok) ? htmlspecialchars($stok) : ''; ?>"/>
        </div>
        <div class="input">
            <label>File Gambar (Opsional)</label>
            <input type="file" name="file_gambar" />
        </div>
        <div class="submit">
            <input type="submit" name="submit" value="Simpan" />
        </div>
    </form>
</div>