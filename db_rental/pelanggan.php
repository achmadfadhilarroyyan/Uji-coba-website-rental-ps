<?php
include 'cek_login.php';
include 'koneksi.php';
/* =========================
   SIMPAN DATA
========================= */
if (isset($_POST['simpan'])) {
    $nama_pelanggan = mysqli_real_escape_string(
        $koneksi,
        $_POST['nama_pelanggan']
    );
    $no_hp = mysqli_real_escape_string(
        $koneksi,
        $_POST['no_hp']
    );
    $alamat = mysqli_real_escape_string(
        $koneksi,
        $_POST['alamat']
    );
    mysqli_query($koneksi, "
        INSERT INTO pelanggan
        (nama_pelanggan, no_hp, alamat)
        VALUES
        ('$nama_pelanggan', '$no_hp', '$alamat')
    ");
    header("Location: pelanggan.php");
    exit;
}
/* =========================
   UPDATE DATA
========================= */
if (isset($_POST['update'])) {
    $id_pelanggan = $_POST['id_pelanggan'];
    $nama_pelanggan = mysqli_real_escape_string(
        $koneksi,
        $_POST['nama_pelanggan']
    );
    $no_hp = mysqli_real_escape_string(
        $koneksi,
        $_POST['no_hp']
    );
    $alamat = mysqli_real_escape_string(
        $koneksi,
        $_POST['alamat']
    );
    mysqli_query($koneksi, "
        UPDATE pelanggan SET
            nama_pelanggan='$nama_pelanggan',
            no_hp='$no_hp',
            alamat='$alamat'
        WHERE id_pelanggan='$id_pelanggan'
    ");
    header("Location: pelanggan.php");
    exit;
}
/* =========================
   HAPUS DATA
========================= */
if (isset($_GET['hapus'])) {
    $id_pelanggan = $_GET['hapus'];
    $cek = mysqli_query(
        $koneksi,
        "SELECT * FROM sewa
         WHERE id_pelanggan='$id_pelanggan'"
    );
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>
                alert('Pelanggan tidak bisa dihapus karena masih memiliki transaksi sewa.');
                window.location='pelanggan.php';
              </script>";
        exit;
    }
    mysqli_query(
        $koneksi,
        "DELETE FROM pelanggan
         WHERE id_pelanggan='$id_pelanggan'"
    );
    header("Location: pelanggan.php");
    exit;
}
/* =========================
   DATA EDIT
========================= */
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_pelanggan = $_GET['edit'];
    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM pelanggan
         WHERE id_pelanggan='$id_pelanggan'"
    );
    $edit_data = mysqli_fetch_assoc($query);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Pelanggan - PS Rental</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            background: #070b14;
            color: #e8faff;
            min-height: 100vh;
        }
        .navbar {
            min-height: 65px;
            background: #0c1220;
            border-bottom: 1px solid #173044;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 35px;
        }
        .brand {
            color: #00e5ff;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .nav-menu {
            display: flex;
            gap: 8px;
        }
        .nav-menu a {
            color: #aab8c7;
            text-decoration: none;
            padding: 9px 14px;
            border-radius: 7px;
            font-size: 14px;
        }
        .nav-menu a:hover,
        .nav-menu a.active {
            color: #00e5ff;
            background: #101d2c;
        }
        .logout {
            color: #ff6b81 !important;
        }
        .container {
            width: 92%;
            max-width: 1150px;
            margin: 35px auto;
        }
        .title {
            margin-bottom: 25px;
        }
        .title h1 {
            font-size: 28px;
            margin-bottom: 7px;
        }
        .title p {
            color: #718096;
            font-size: 14px;
        }
        .card {
            background: #0d1422;
            border: 1px solid #17283a;
            border-radius: 12px;
            padding: 22px;
            margin-bottom: 25px;
            box-shadow: 0 0 20px rgba(0, 229, 255, 0.04);
        }
        .card-title {
            color: #00e5ff;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 18px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }
        .full {
            grid-column: 1 / -1;
        }
        label {
            color: #8998aa;
            font-size: 13px;
        }
        input,
        textarea {
            width: 100%;
            background: #080e19;
            border: 1px solid #22364a;
            color: white;
            padding: 11px 12px;
            border-radius: 7px;
            outline: none;
            font-family: Arial, sans-serif;
        }
        textarea {
            min-height: 85px;
            resize: vertical;
        }
        input:focus,
        textarea:focus {
            border-color: #00e5ff;
            box-shadow: 0 0 8px rgba(0, 229, 255, 0.12);
        }
        .buttons {
            margin-top: 18px;
            display: flex;
            gap: 10px;
        }
        button,
        .btn {
            border: none;
            padding: 10px 17px;
            border-radius: 7px;
            text-decoration: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: bold;
        }
        .btn-save {
            background: #00d9ef;
            color: #031017;
        }
        .btn-cancel {
            background: #202b3b;
            color: #c7d1dc;
        }
        .table-wrap {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            text-align: left;
            color: #00e5ff;
            font-size: 12px;
            padding: 13px 10px;
            border-bottom: 1px solid #203245;
        }
        td {
            padding: 13px 10px;
            color: #c5d0dc;
            border-bottom: 1px solid #152334;
            font-size: 13px;
        }
        tr:hover td {
            background: #101a29;
        }
        .action {
            display: flex;
            gap: 7px;
        }
        .edit {
            background: #172c3b;
            color: #00e5ff;
        }
        .delete {
            background: #351a24;
            color: #ff7187;
        }
        .empty {
            text-align: center;
            color: #68788b;
            padding: 25px;
        }
        @media (max-width: 750px) {
            .navbar {
                padding: 15px;
                flex-direction: column;
                gap: 12px;
            }
            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
            .full {
                grid-column: auto;
            }
        }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="brand">
        ⚡ PS RENTAL
    </div>
    <div class="nav-menu">
        <a href="index.php">
            Dashboard
        </a>
        <a href="logout.php" class="logout">
            Logout
        </a>
    </div>
</nav>
<div class="container">
    <div class="title">
        <h1>Data Pelanggan</h1>
        <p>
            Kelola data pelanggan rental PlayStation.
        </p>
    </div>
    <div class="card">
        <div class="card-title">
            <?= $edit_data
                ? 'EDIT DATA PELANGGAN'
                : 'TAMBAH PELANGGAN' ?>
        </div>
        <form method="POST">
            <?php if ($edit_data) { ?>
                <input
                    type="hidden"
                    name="id_pelanggan"
                    value="<?= $edit_data['id_pelanggan'] ?>"
                >
            <?php } ?>
            <div class="form-grid">
                <div class="form-group">
                    <label>
                        Nama Pelanggan
                    </label>
                    <input
                        type="text"
                        name="nama_pelanggan"
                        placeholder="Masukkan nama"
                        required
                        value="<?= $edit_data['nama_pelanggan'] ?? '' ?>"
                    >
                </div>
                <div class="form-group">
                    <label>
                        No. HP
                    </label>
                    <input
                        type="text"
                        name="no_hp"
                        placeholder="08xxxxxxxxxx"
                        required
                        value="<?= $edit_data['no_hp'] ?? '' ?>"
                    >
                </div>
                <div class="form-group full">
                    <label>
                        Alamat
                    </label>
                    <textarea
                        name="alamat"
                        placeholder="Masukkan alamat"
                        required
                    ><?= $edit_data['alamat'] ?? '' ?></textarea>
                </div>
            </div>
            <div class="buttons">
                <?php if ($edit_data) { ?>
                    <button
                        type="submit"
                        name="update"
                        class="btn btn-save">
                        Simpan Perubahan
                    </button>
                    <a
                        href="pelanggan.php"
                        class="btn btn-cancel">
                        Batal
                    </a>
                <?php } else { ?>
                    <button
                        type="submit"
                        name="simpan"
                        class="btn btn-save">
                        + Simpan Data
                    </button>
                <?php } ?>
            </div>
        </form>
    </div>
    <div class="card">
        <div class="card-title">
            DATA PELANGGAN
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>No. HP</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $data = mysqli_query(
                    $koneksi,
                    "SELECT * FROM pelanggan
                     ORDER BY id_pelanggan DESC"
                );
                if (mysqli_num_rows($data) > 0) {
                    while ($row = mysqli_fetch_assoc($data)) {
                ?>
                    <tr>
                        <td>
                            <?= $row['id_pelanggan'] ?>
                        </td>
                        <td>
                            <?= htmlspecialchars(
                                $row['nama_pelanggan']
                            ) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars(
                                $row['no_hp']
                            ) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars(
                                $row['alamat']
                            ) ?>
                        </td>
                        <td>
                            <div class="action">
                                <a
                                    href="pelanggan.php?edit=<?= $row['id_pelanggan'] ?>"
                                    class="btn edit">
                                    Edit
                                </a>
                                <a
                                    href="pelanggan.php?hapus=<?= $row['id_pelanggan'] ?>"
                                    class="btn delete"
                                    onclick="return confirm('Yakin ingin menghapus pelanggan ini?')">
                                    Hapus
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php
                    }
                } else {
                ?>
                    <tr>
                        <td
                            colspan="5"
                            class="empty">
                            Belum ada data pelanggan.
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>