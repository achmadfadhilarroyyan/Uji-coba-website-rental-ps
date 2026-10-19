<?php
include 'cek_login.php';
include 'koneksi.php';
/* =========================
   SIMPAN DATA
========================= */
if (isset($_POST['simpan'])) {
    $jenis_ps = mysqli_real_escape_string($koneksi, $_POST['jenis_ps']);
    $type_ps = mysqli_real_escape_string($koneksi, $_POST['type_ps']);
    $harga_sewa = $_POST['harga_sewa'];
    mysqli_query($koneksi, "
        INSERT INTO ps (jenis_ps, type_ps, harga_sewa)
        VALUES ('$jenis_ps', '$type_ps', '$harga_sewa')
    ");
    header("Location: ps.php");
    exit;
}
/* =========================
   UPDATE DATA
========================= */
if (isset($_POST['update'])) {
    $id_ps = $_POST['id_ps'];
    $jenis_ps = mysqli_real_escape_string($koneksi, $_POST['jenis_ps']);
    $type_ps = mysqli_real_escape_string($koneksi, $_POST['type_ps']);
    $harga_sewa = $_POST['harga_sewa'];
    mysqli_query($koneksi, "
        UPDATE ps SET
            jenis_ps='$jenis_ps',
            type_ps='$type_ps',
            harga_sewa='$harga_sewa'
        WHERE id_ps='$id_ps'
    ");
    header("Location: ps.php");
    exit;
}
/* =========================
   HAPUS DATA
========================= */
if (isset($_GET['hapus'])) {
    $id_ps = $_GET['hapus'];
    $cek = mysqli_query(
        $koneksi,
        "SELECT * FROM sewa WHERE id_ps='$id_ps'"
    );
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>
                alert('PS tidak bisa dihapus karena masih memiliki transaksi sewa.');
                window.location='ps.php';
              </script>";
        exit;
    }
    mysqli_query(
        $koneksi,
        "DELETE FROM ps WHERE id_ps='$id_ps'"
    );
    header("Location: ps.php");
    exit;
}
/* =========================
   DATA EDIT
========================= */
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_ps = $_GET['edit'];
    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM ps WHERE id_ps='$id_ps'"
    );
    $edit_data = mysqli_fetch_assoc($query);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data PS - PS Rental</title>
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
            height: 65px;
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
            color: white;
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
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }
        label {
            color: #8998aa;
            font-size: 13px;
        }
        input {
            background: #080e19;
            border: 1px solid #22364a;
            color: white;
            padding: 11px 12px;
            border-radius: 7px;
            outline: none;
        }
        input:focus {
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
        .btn-save:hover {
            background: #32eaff;
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
                height: auto;
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
        }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="brand">⚡ PS RENTAL</div>
    <div class="nav-menu">
        <a href="index.php">Dashboard</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</nav>
<div class="container">
    <div class="title">
        <h1>Data PlayStation</h1>
        <p>Kelola unit PlayStation dan harga sewa.</p>
    </div>
    <div class="card">
        <div class="card-title">
            <?= $edit_data ? 'EDIT DATA PS' : 'TAMBAH DATA PS' ?>
        </div>
        <form method="POST">
            <?php if ($edit_data) { ?>
                <input type="hidden" name="id_ps"
                       value="<?= $edit_data['id_ps'] ?>">
            <?php } ?>
            <div class="form-grid">
                <div class="form-group">
                    <label>Jenis PS</label>
                    <input
                        type="text"
                        name="jenis_ps"
                        placeholder=""
                        required
                        value="<?= $edit_data['jenis_ps'] ?? '' ?>"
                    >
                </div>
                <div class="form-group">
                    <label>Type PS</label>
                    <input
                        type="text"
                        name="type_ps"
                        placeholder=""
                        required
                        value="<?= $edit_data['type_ps'] ?? '' ?>"
                    >
                </div>
                <div class="form-group">
                    <label>Harga Sewa / Hari</label>
                    <input
                        type="number"
                        name="harga_sewa"
                        placeholder=""
                        required
                        value="<?= $edit_data['harga_sewa'] ?? '' ?>"
                    >
                </div>
            </div>
            <div class="buttons">
                <?php if ($edit_data) { ?>
                    <button type="submit"
                            name="update"
                            class="btn btn-save">
                        Simpan Perubahan
                    </button>
                    <a href="ps.php"
                       class="btn btn-cancel">
                        Batal
                    </a>
                <?php } else { ?>
                    <button type="submit"
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
            DATA PLAYSTATION
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Jenis PS</th>
                        <th>Type</th>
                        <th>Harga / Hari</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $data = mysqli_query(
                    $koneksi,
                    "SELECT * FROM ps ORDER BY id_ps DESC"
                );
                if (mysqli_num_rows($data) > 0) {
                    while ($row = mysqli_fetch_assoc($data)) {
                ?>
                    <tr>
                        <td><?= $row['id_ps'] ?></td>
                        <td><?= htmlspecialchars($row['jenis_ps']) ?></td>
                        <td><?= htmlspecialchars($row['type_ps']) ?></td>
                        <td>
                            Rp <?= number_format(
                                $row['harga_sewa'],
                                0,
                                ',',
                                '.'
                            ) ?>
                        </td>
                        <td>
                            <div class="action">
                                <a
                                    href="ps.php?edit=<?= $row['id_ps'] ?>"
                                    class="btn edit">
                                    Edit
                                </a>
                                <a
                                    href="ps.php?hapus=<?= $row['id_ps'] ?>"
                                    class="btn delete"
                                    onclick="return confirm('Yakin ingin menghapus data PS ini?')">
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
                        <td colspan="5" class="empty">
                            Belum ada data PlayStation.
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