<?php
include 'cek_login.php';
include 'koneksi.php';
/* =========================
   SIMPAN TRANSAKSI
========================= */
if (isset($_POST['simpan'])) {
    $id_ps = $_POST['id_ps'];
    $id_pelanggan = $_POST['id_pelanggan'];
    $tgl_sewa = $_POST['tgl_sewa'];
    $lama_sewa = $_POST['lama_sewa'];
    $query_ps = mysqli_query(
        $koneksi,
        "SELECT harga_sewa
         FROM ps
         WHERE id_ps='$id_ps'"
    );
    $data_ps = mysqli_fetch_assoc($query_ps);
    if ($data_ps) {
        $total_bayar =
            $data_ps['harga_sewa'] * $lama_sewa;
        mysqli_query($koneksi, "
            INSERT INTO sewa
            (
                id_ps,
                id_pelanggan,
                tgl_sewa,
                lama_sewa,
                total_bayar
            )
            VALUES
            (
                '$id_ps',
                '$id_pelanggan',
                '$tgl_sewa',
                '$lama_sewa',
                '$total_bayar'
            )
        ");
    }
    header("Location: sewa.php");
    exit;
}
/* =========================
   UPDATE TRANSAKSI
========================= */
if (isset($_POST['update'])) {
    $id_sewa = $_POST['id_sewa'];
    $id_ps = $_POST['id_ps'];
    $id_pelanggan = $_POST['id_pelanggan'];
    $tgl_sewa = $_POST['tgl_sewa'];
    $lama_sewa = $_POST['lama_sewa'];
    $query_ps = mysqli_query(
        $koneksi,
        "SELECT harga_sewa
         FROM ps
         WHERE id_ps='$id_ps'"
    );
    $data_ps = mysqli_fetch_assoc($query_ps);
    if ($data_ps) {
        $total_bayar =
            $data_ps['harga_sewa'] * $lama_sewa;
        mysqli_query($koneksi, "
            UPDATE sewa SET
                id_ps='$id_ps',
                id_pelanggan='$id_pelanggan',
                tgl_sewa='$tgl_sewa',
                lama_sewa='$lama_sewa',
                total_bayar='$total_bayar'
            WHERE id_sewa='$id_sewa'
        ");
    }
    header("Location: sewa.php");
    exit;
}
/* =========================
   HAPUS TRANSAKSI
========================= */
if (isset($_GET['hapus'])) {
    $id_sewa = $_GET['hapus'];
    mysqli_query(
        $koneksi,
        "DELETE FROM sewa
         WHERE id_sewa='$id_sewa'"
    );
    header("Location: sewa.php");
    exit;
}
/* =========================
   DATA EDIT
========================= */
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_sewa = $_GET['edit'];
    $query = mysqli_query(
        $koneksi,
        "SELECT *
         FROM sewa
         WHERE id_sewa='$id_sewa'"
    );
    $edit_data = mysqli_fetch_assoc($query);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">
    <title>Transaksi Sewa - PS Rental</title>
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
            width: 94%;
            max-width: 1250px;
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
            grid-template-columns: repeat(4, 1fr);
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
        select,
        input {
            width: 100%;
            background: #080e19;
            border: 1px solid #22364a;
            color: white;
            padding: 11px 12px;
            border-radius: 7px;
            outline: none;
        }
        select:focus,
        input:focus {
            border-color: #00e5ff;
            box-shadow: 0 0 8px rgba(0, 229, 255, 0.12);
        }
        option {
            background: #0d1422;
            color: white;
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
            min-width: 900px;
        }
        th {
            text-align: left;
            color: #00e5ff;
            font-size: 12px;
            padding: 13px 10px;
            border-bottom: 1px solid #203245;
            white-space: nowrap;
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
        .status {
            display: inline-block;
            padding: 6px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            white-space: nowrap;
        }
        .berlangsung {
            background: #0b302d;
            color: #35f1c3;
            border: 1px solid #155a50;
        }
        .selesai {
            background: #321a22;
            color: #ff7187;
            border: 1px solid #5c2735;
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
        .total {
            color: #35e8ff;
            font-weight: bold;
            white-space: nowrap;
        }
        @media (max-width: 850px) {
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
                grid-template-columns: 1fr 1fr;
            }
        }
        @media (max-width: 550px) {
            .form-grid {
                grid-template-columns: 1fr;
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
        <a href="logout.php" class="logout">
            Logout
        </a>
    </div>
</nav>
<div class="container">
    <div class="title">
        <h1>Transaksi Sewa</h1>
        <p>
            Kelola transaksi rental PlayStation.
        </p>
    </div>
    <div class="card">
        <div class="card-title">
            <?= $edit_data
                ? 'EDIT TRANSAKSI'
                : 'TRANSAKSI BARU' ?>
        </div>
        <form method="POST">
            <?php if ($edit_data) { ?>
                <input
                    type="hidden"
                    name="id_sewa"
                    value="<?= $edit_data['id_sewa'] ?>"
                >
            <?php } ?>
            <div class="form-grid">
                <div class="form-group">
                    <label>
                        PlayStation
                    </label>
                    <select
                        name="id_ps"
                        required
                    >
                        <option value="">
                            -- Pilih PS --
                        </option>
                        <?php
                        $data_ps = mysqli_query(
                            $koneksi,
                            "SELECT * FROM ps
                             ORDER BY id_ps ASC"
                        );
                        while (
                            $ps = mysqli_fetch_assoc($data_ps)
                        ) {
                            $selected = '';
                            if (
                                $edit_data &&
                                $edit_data['id_ps']
                                == $ps['id_ps']
                            ) {
                                $selected = 'selected';
                            }
                        ?>
                            <option
                                value="<?= $ps['id_ps'] ?>"
                                <?= $selected ?>
                            >
                                <?= htmlspecialchars(
                                    $ps['jenis_ps']
                                ) ?>
                                -
                                <?= htmlspecialchars(
                                    $ps['type_ps']
                                ) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>
                        Pelanggan
                    </label>
                    <select
                        name="id_pelanggan"
                        required
                    >
                        <option value="">
                            -- Pilih Pelanggan --
                        </option>
                        <?php
                        $data_pelanggan = mysqli_query(
                            $koneksi,
                            "SELECT * FROM pelanggan
                             ORDER BY nama_pelanggan ASC"
                        );
                        while (
                            $pelanggan =
                            mysqli_fetch_assoc(
                                $data_pelanggan
                            )
                        ) {
                            $selected = '';
                            if (
                                $edit_data &&
                                $edit_data['id_pelanggan']
                                == $pelanggan['id_pelanggan']
                            ) {
                                $selected = 'selected';
                            }
                        ?>
                            <option
                                value="<?= $pelanggan['id_pelanggan'] ?>"
                                <?= $selected ?>
                            >
                                <?= htmlspecialchars(
                                    $pelanggan['nama_pelanggan']
                                ) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>
                        Tanggal Sewa
                    </label>
                    <input
                        type="date"
                        name="tgl_sewa"
                        required
                        value="<?= $edit_data['tgl_sewa'] ?? date('Y-m-d') ?>"
                    >
                </div>
                <div class="form-group">
                    <label>
                        Lama Sewa (Hari)
                    </label>
                    <input
                        type="number"
                        name="lama_sewa"
                        min="1"
                        required
                        value="<?= $edit_data['lama_sewa'] ?? 1 ?>"
                    >
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
                        href="sewa.php"
                        class="btn btn-cancel">
                        Batal
                    </a>
                <?php } else { ?>
                    <button
                        type="submit"
                        name="simpan"
                        class="btn btn-save">
                        + Simpan Transaksi
                    </button>
                <?php } ?>
            </div>
        </form>
    </div>
    <div class="card">
        <div class="card-title">
            DATA TRANSAKSI
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>PS</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th>Berakhir</th>
                        <th>Lama</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $data = mysqli_query(
                    $koneksi,
                    "
                    SELECT
                        sewa.*,
                        ps.jenis_ps,
                        ps.type_ps,
                        pelanggan.nama_pelanggan
                    FROM sewa
                    JOIN ps
                        ON sewa.id_ps = ps.id_ps
                    JOIN pelanggan
                        ON sewa.id_pelanggan =
                           pelanggan.id_pelanggan
                    ORDER BY sewa.id_sewa DESC
                    "
                );
                if (mysqli_num_rows($data) > 0) {
                    while (
                        $row =
                        mysqli_fetch_assoc($data)
                    ) {
                        /* =====================
                           HITUNG TANGGAL BERAKHIR
                        ===================== */
                        $tanggal_mulai =
                            new DateTime(
                                $row['tgl_sewa']
                            );
                        $tanggal_berakhir =
                            clone $tanggal_mulai;
                        $tanggal_berakhir->modify(
                            '+' .
                            $row['lama_sewa'] .
                            ' days'
                        );
                        /* =====================
                           STATUS
                        ===================== */
                        $hari_ini =
                            new DateTime();
                        if (
                            $hari_ini >=
                            $tanggal_berakhir
                        ) {
                            $status =
                                'Selesai';
                            $status_class =
                                'selesai';
                        } else {
                            $status =
                                'Sedang Berlangsung';
                            $status_class =
                                'berlangsung';
                        }
                ?>
                    <tr>
                        <td>
                            <?= $row['id_sewa'] ?>
                        </td>
                        <td>
                            <?= htmlspecialchars(
                                $row['jenis_ps']
                            ) ?>
                            <br>
                            <small
                                style="color:#68788b;">
                                <?= htmlspecialchars(
                                    $row['type_ps']
                                ) ?>
                            </small>
                        </td>
                        <td>
                            <?= htmlspecialchars(
                                $row['nama_pelanggan']
                            ) ?>
                        </td>
                        <td>
                            <?= date(
                                'd-m-Y',
                                strtotime(
                                    $row['tgl_sewa']
                                )
                            ) ?>
                        </td>
                        <td>
                            <?= $tanggal_berakhir
                                ->format('d-m-Y') ?>
                        </td>
                        <td>
                            <?= $row['lama_sewa'] ?>
                            hari
                        </td>
                        <td class="total">
                            Rp
                            <?= number_format(
                                $row['total_bayar'],
                                0,
                                ',',
                                '.'
                            ) ?>
                        </td>
                        <td>
                            <span
                                class="status <?= $status_class ?>"
                            >
                                <?= $status ?>
                            </span>
                        </td>
                        <td>
                            <div class="action">
                                <a
                                    href="sewa.php?edit=<?= $row['id_sewa'] ?>"
                                    class="btn edit">
                                    Edit
                                </a>
                                <a
                                    href="sewa.php?hapus=<?= $row['id_sewa'] ?>"
                                    class="btn delete"
                                    onclick="return confirm('Yakin ingin menghapus transaksi ini?')">
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
                            colspan="9"
                            class="empty">
                            Belum ada transaksi sewa.
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