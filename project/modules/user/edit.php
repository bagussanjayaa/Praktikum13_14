<?php
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo "<p>Akses ditolak. Silakan login.</p>";
    return;
}

$id_barang = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$upload_path_relative_to_root = 'assets/gambar/';

$stmt_select = $conn->prepare("SELECT id_barang, kategori, nama, gambar, harga_beli, harga_jual, stok FROM data_barang WHERE id_barang = ?");
$stmt_select->bind_param("i", $id_barang);
$stmt_select->execute();
$result = $stmt_select->get_result();
$data = $result->fetch_assoc();
$stmt_select->close();

if (!$data) {
    header('location: index.php?page=user/list');
    exit();
}

if (isset($_POST['submit'])) {
    $nama = trim($_POST['nama']);
    $kategori = trim($_POST['kategori']);
    $harga_jual = (int) $_POST['harga_jual'];
    $harga_beli = (int) $_POST['harga_beli'];
    $stok = (int) $_POST['stok'];
    $file_gambar = $_FILES['file_gambar'];
    $gambar_lama = $data['gambar']; 

    $gambar = $gambar_lama;

    if ($file_gambar['error'] == 0) {
        $filename = time() . '_' . str_replace(' ', '_', basename($file_gambar['name']));
        $absolute_upload_dir = dirname(__DIR__, 2) . '/' . $upload_path_relative_to_root;
        $destination_path = $absolute_upload_dir . $filename;
        
        if (move_uploaded_file($file_gambar['tmp_name'], $destination_path)) {
            $gambar = $upload_path_relative_to_root . $filename;
            
            // Hapus gambar lama jika ada
            if ($gambar_lama && file_exists(dirname(__DIR__, 2) . '/' . $gambar_lama)) {
                unlink(dirname(__DIR__, 2) . '/' . $gambar_lama);
            }
        } else {
            $message .= "<p style='color:orange;'>Warning: Gagal mengupload file gambar baru. Cek izin folder.</p>";
        }
    }

    $stmt_update = $conn->prepare("UPDATE data_barang SET kategori=?, nama=?, gambar=?, harga_beli=?, harga_jual=?, stok=? WHERE id_barang=?");
    $stmt_update->bind_param("sssiiis", $kategori, $nama, $gambar, $harga_beli, $harga_jual, $stok, $id_barang);

    if ($stmt_update->execute()) {
        $message = "<p style='color:green; font-weight:bold;'>Data barang **" . htmlspecialchars($nama) . "** berhasil diperbarui.</p>";
        
        $stmt_select_new = $conn->prepare("SELECT kategori, nama, gambar, harga_beli, harga_jual, stok FROM data_barang WHERE id_barang = ?");
        $stmt_select_new->bind_param("i", $id_barang);
        $stmt_select_new->execute();
        $data = $stmt_select_new->get_result()->fetch_assoc();
        $stmt_select_new->close();

    } else {
        $message = "<p style='color:red;'>Error: Gagal memperbarui data. " . $stmt_update->error . "</p>";
    }
    $stmt_update->close();
}

$kategori_options = ['Komputer', 'Elektronik', 'Hand Phone'];
?>

<h2>Ubah Data Barang</h2>
<?php echo $message; ?>

<div class="login-box" style="max-width: 450px;">
    <form method="post" action="index.php?page=user/edit&id=<?php echo $id_barang; ?>" enctype="multipart/form-data">
        <div class="input">
            <label>Nama Barang</label>
            <input type="text" name="nama" value="<?php echo htmlspecialchars($data['nama']); ?>" required/>
        </div>
        <div class="input">
            <label>Kategori</label>
            <select name="kategori" required>
                <?php foreach ($kategori_options as $opt): ?>
                    <option value="<?php echo $opt; ?>" <?php echo ($data['kategori'] == $opt) ? 'selected' : ''; ?>>
                        <?php echo $opt; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="input">
            <label>Harga Jual</label>
            <input type="number" name="harga_jual" value="<?php echo htmlspecialchars($data['harga_jual']); ?>" required/>
        </div>
        <div class="input">
            <label>Harga Beli</label>
            <input type="number" name="harga_beli" value="<?php echo htmlspecialchars($data['harga_beli']); ?>" required/>
        </div>
        <div class="input">
            <label>Stok</label>
            <input type="number" name="stok" value="<?php echo htmlspecialchars($data['stok']); ?>" required/>
        </div>
        <div class="input">
            <label>File Gambar (Kosongkan jika tidak diubah)</label>
            <?php if ($data['gambar']): 
                echo '<div style="margin-bottom: 10px;">Gambar Saat Ini:</div>';
                echo '<img src="' . htmlspecialchars($data['gambar']) . '" alt="Gambar ' . htmlspecialchars($data['nama']) . '" class="edit-img-thumbnail" style="width: 150px; height: auto; display: block; margin-bottom: 10px;">';
            ?>
                <p style="font-size: 0.8em; color: #666; margin-bottom: 5px;">File: <?php echo htmlspecialchars(basename($data['gambar'])); ?></p>
            <?php endif; ?>
            <input type="file" name="file_gambar" />
        </div>
        <div class="submit">
            <input type="submit" name="submit" value="Simpan Perubahan" />
            <a href="index.php?page=user/list" style="display: block; text-align: center; margin-top: 15px; color: #4f46e5; text-decoration: none; font-weight: 600;">Batal</a>
        </div>
    </form>
</div>