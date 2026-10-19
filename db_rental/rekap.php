
<?php
include 'cek_login.php';
include 'koneksi.php';

$daftar_bulan = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];

$tahun = isset($_GET['tahun']) ? (int) $_GET['tahun'] : (int) date('Y');

if ($tahun < 2000 || $tahun > 2100) {
    $tahun = (int) date('Y');
}

$awal = $tahun . '-01-01';
$akhir = ($tahun + 1) . '-01-01';

$rekap = [];

for ($i = 1; $i <= 12; $i++) {
    $rekap[$i] = [
        'bulan' => $daftar_bulan[$i],
        'transaksi' => 0,
        'pendapatan' => 0
    ];
}

$total_transaksi = 0;
$total_pendapatan = 0;

$sql = "SELECT MONTH(tgl_sewa) AS bulan,
               COUNT(*) AS transaksi,
               SUM(total_bayar) AS pendapatan
        FROM sewa
        WHERE tgl_sewa >= ? AND tgl_sewa < ?
        GROUP BY MONTH(tgl_sewa)";

$stmt = mysqli_prepare($koneksi, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ss", $awal, $akhir);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);

    while ($data = mysqli_fetch_assoc($hasil)) {
        $bulan = (int) $data['bulan'];

        $rekap[$bulan]['transaksi'] = (int) $data['transaksi'];
        $rekap[$bulan]['pendapatan'] = (float) $data['pendapatan'];

        $total_transaksi += (int) $data['transaksi'];
        $total_pendapatan += (float) $data['pendapatan'];
    }

    mysqli_stmt_close($stmt);
}

function rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Bulanan</title>

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
        }

        nav {
            background: #0c1220;
            padding: 18px 5%;
            border-bottom: 1px solid #173044;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .logo {
            color: #00e5ff;
            font-weight: bold;
            font-size: 20px;
        }

        nav a {
            color: #aab8c7;
            text-decoration: none;
            margin-left: 15px;
            font-size: 14px;
        }

        nav a:hover {
            color: #00e5ff;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 35px auto;
        }

        h1 {
            font-size: 27px;
            margin-bottom: 8px;
        }

        h1 span {
            color: #00e5ff;
        }

        .deskripsi {
            color: #718096;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .panel {
            background: #0d1422;
            border: 1px solid #17283a;
            border-radius: 10px;
            padding: 22px;
            margin-bottom: 20px;
        }

        h2 {
            color: #00e5ff;
            font-size: 17px;
            margin-bottom: 18px;
        }

        .filter {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: end;
        }

        .filter div {
            flex: 1;
            min-width: 180px;
        }

        label {
            display: block;
            color: #aab8c7;
            font-size: 13px;
            margin-bottom: 8px;
        }

        input, select {
            width: 100%;
            padding: 11px;
            background: #080e19;
            color: white;
            border: 1px solid #294157;
            border-radius: 6px;
        }

        button {
            padding: 12px 18px;
            border: none;
            border-radius: 6px;
            background: #00c5df;
            color: #07111b;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #55eaff;
        }

        .ringkasan {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .angka {
            color: #00e5ff;
            font-size: 25px;
            font-weight: bold;
        }

        .label-angka {
            color: #718096;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 500px;
        }

        th, td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #1b2a3b;
            font-size: 13px;
        }

        th {
            color: #00e5ff;
            background: #101d2c;
        }

        td {
            color: #cbd5e1;
        }

        .kanan {
            text-align: right;
            white-space: nowrap;
        }

        tfoot td {
            color: #00e5ff;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            color: #465568;
            font-size: 11px;
            padding: 20px 0;
        }

        @media (max-width: 600px) {
            nav {
                flex-direction: column;
            }

            nav a {
                margin: 0 6px;
            }

            .ringkasan {
                grid-template-columns: 1fr;
            }

            .container {
                margin-top: 25px;
            }
        }
    </style>
</head>

<body>

<nav>
    <div class="logo">PS RENTAL</div>
    <div>
        <a href="index.php">Dashboard</a>
        <a href="rekap.php">Rekap Bulanan</a>
        <a href="logout.php">Logout</a>
    </div>
</nav>

<div class="container">

    <h1>Rekap <span>Bulanan</span></h1>
    <p class="deskripsi">Ringkasan transaksi dan pendapatan rental PlayStation.</p>

    <div class="panel">
        <h2>Pilih Tahun dan Cari Bulan</h2>

        <form method="GET" action="rekap.php" class="filter">
            <div>
                <label for="tahun">Tahun</label>
                <select name="tahun" id="tahun">
                    <?php
                    $tahun_awal = min(2020, $tahun);
                    $tahun_akhir = max((int) date('Y'), $tahun);

                    for ($t = $tahun_akhir; $t >= $tahun_awal; $t--) {
                        $pilih = ($t === $tahun) ? 'selected' : '';
                        echo "<option value='$t' $pilih>$t</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit">Tampilkan</button>

            <div>
                <label for="cari">Cari Bulan</label>
                <input type="text" id="cari" placeholder="Contoh: Maret">
            </div>
        </form>
    </div>

    <div class="ringkasan">
        <div class="panel">
            <div class="label-angka">TOTAL TRANSAKSI TAHUN <?= $tahun ?></div>
            <div class="angka" id="totalTransaksi">
                <?= number_format($total_transaksi, 0, ',', '.') ?>
            </div>
        </div>

        <div class="panel">
            <div class="label-angka">TOTAL PENDAPATAN TAHUN <?= $tahun ?></div>
            <div class="angka" id="totalPendapatan">
                <?= rupiah($total_pendapatan) ?>
            </div>
        </div>
    </div>

    <div class="panel">
        <h2>Rekap Januari - Desember <?= $tahun ?></h2>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Bulan</th>
                        <th class="kanan">Jumlah Transaksi</th>
                        <th class="kanan">Pendapatan</th>
                    </tr>
                </thead>

                <tbody id="isiRekap">
                    <?php foreach ($rekap as $no => $data): ?>
                    <tr
                        class="baris"
                        data-bulan="<?= strtolower($data['bulan']) ?>"
                        data-transaksi="<?= $data['transaksi'] ?>"
                        data-pendapatan="<?= $data['pendapatan'] ?>"
                    >
                        <td><?= $no ?></td>
                        <td><?= $data['bulan'] ?></td>
                        <td class="kanan">
                            <?= number_format($data['transaksi'], 0, ',', '.') ?>
                        </td>
                        <td class="kanan">
                            <?= rupiah($data['pendapatan']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>

                <tfoot>
                    <tr id="barisTotal">
                        <td colspan="2">TOTAL</td>
                        <td class="kanan" id="jumlahTabel">
                            <?= number_format($total_transaksi, 0, ',', '.') ?>
                        </td>
                        <td class="kanan" id="pendapatanTabel">
                            <?= rupiah($total_pendapatan) ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="footer">
        PS RENTAL - SISTEM INFORMASI RENTAL PLAYSTATION
    </div>

</div>

<script>
    const cari = document.getElementById('cari');
    const baris = document.querySelectorAll('.baris');

    cari.addEventListener('input', function() {
        let transaksi = 0;
        let pendapatan = 0;
        let jumlah = 0;
        const kata = cari.value.toLowerCase().trim();

        baris.forEach(function(row) {
            const cocok = row.dataset.bulan.includes(kata);
            row.hidden = !cocok;

            if (cocok) {
                jumlah++;
                transaksi += Number(row.dataset.transaksi);
                pendapatan += Number(row.dataset.pendapatan);
            }
        });

        const rupiah = 'Rp ' + Math.round(pendapatan).toLocaleString('id-ID');

        document.getElementById('totalTransaksi').textContent =
            transaksi.toLocaleString('id-ID');

        document.getElementById('totalPendapatan').textContent = rupiah;
        document.getElementById('jumlahTabel').textContent =
            transaksi.toLocaleString('id-ID');

        document.getElementById('pendapatanTabel').textContent = rupiah;

        document.getElementById('barisTotal').hidden = jumlah === 0;
    });
</script>

</body>
</html>
