<?php
if (!isset($conn)) {
    die("<p style='color:red;'>Error: Koneksi database tidak tersedia.</p>");
}

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo "<p>Akses ditolak. Silakan login.</p>";
    return;
}

$base_image_path = 'assets/gambar/';

/* =====================
   PENCARIAN
   ===================== */
$nama = isset($_GET['nama']) ? $_GET['nama'] : '';

/* =====================
   PAGINATION
   ===================== */
$per_page = 5; // jumlah data per halaman
$page_num = isset($_GET['hal']) ? (int)$_GET['hal'] : 1;
$offset = ($page_num - 1) * $per_page;

/* =====================
   HITUNG TOTAL DATA
   ===================== */
if ($nama != '') {
    $sql_count = "SELECT COUNT(*) AS total 
                  FROM data_barang 
                  WHERE nama LIKE '$nama%'";
} else {
    $sql_count = "SELECT COUNT(*) AS total 
                  FROM data_barang";
}

$result_count = $conn->query($sql_count);
$row_count = $result_count->fetch_assoc();
$total_data = $row_count['total'];
$total_page = ceil($total_data / $per_page);

/* =====================
   QUERY DATA
   ===================== */
if ($nama != '') {
    $sql = "SELECT id_barang, kategori, nama, gambar, harga_beli, harga_jual, stok
            FROM data_barang
            WHERE nama LIKE '$nama%'
            ORDER BY id_barang DESC
            LIMIT $offset, $per_page";
} else {
    $sql = "SELECT id_barang, kategori, nama, gambar, harga_beli, harga_jual, stok
            FROM data_barang
            ORDER BY id_barang DESC
            LIMIT $offset, $per_page";
}

$result = $conn->query($sql);
?>

<h2>Data Barang</h2>

<p>
    <a href="index.php?page=user/add">Tambah Barang</a>
</p>

<!-- FORM PENCARIAN -->
<form method="GET" action="index.php" style="margin-bottom:15px;">
    <input type="hidden" name="page" value="user/list">
    <input type="text" name="nama" placeholder="Cari nama barang"
           value="<?= htmlspecialchars($nama); ?>">
    <button type="submit">Cari</button>
    <?php if ($nama != '') { ?>
        <a href="index.php?page=user/list">Reset</a>
    <?php } ?>
</form>

<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Gambar</th>
            <th>Nama</th>
            <th>Kategori</th>
            <th>Harga Jual</th>
            <th>Harga Beli</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {

                $gambar = 'Tidak ada gambar';
                if (!empty($row['gambar'])) {
                    $path = strpos($row['gambar'], $base_image_path) === 0
                        ? $row['gambar']
                        : $base_image_path . basename($row['gambar']);
                    $gambar = "<img src='$path' width='70'>";
                }

                echo "<tr>
                        <td align='center'>$gambar</td>
                        <td>{$row['nama']}</td>
                        <td>{$row['kategori']}</td>
                        <td>Rp. " . number_format($row['harga_jual'], 0, ',', '.') . "</td>
                        <td>Rp. " . number_format($row['harga_beli'], 0, ',', '.') . "</td>
                        <td align='center'>{$row['stok']}</td>
                        <td align='center'>
                            <a href='index.php?page=user/edit&id={$row['id_barang']}'>Ubah</a> | 
                            <a href='index.php?page=user/delete&id={$row['id_barang']}'
                               onclick='return confirm(\"Yakin hapus?\")'>Hapus</a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='7' align='center'>Data tidak ditemukan</td></tr>";
        }
        ?>
    </tbody>
</table>

<!-- PAGINATION -->
<div style="margin-top:15px;">
    <?php
    if ($page_num > 1) {
        echo "<a href='index.php?page=user/list&hal=" . ($page_num - 1) . "&nama=$nama'>Previous</a> ";
    }

    for ($i = 1; $i <= $total_page; $i++) {
        if ($i == $page_num) {
            echo "<strong>$i</strong> ";
        } else {
            echo "<a href='index.php?page=user/list&hal=$i&nama=$nama'>$i</a> ";
        }
    }

    if ($page_num < $total_page) {
        echo "<a href='index.php?page=user/list&hal=" . ($page_num + 1) . "&nama=$nama'>Next</a>";
    }
    ?>
</div>
