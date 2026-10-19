
<?php
include 'cek_login.php';
include 'koneksi.php';

/* =========================
   HITUNG DATA DASHBOARD
========================= */

// Total PlayStation
$query_ps = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total FROM ps"
);
$data_ps = mysqli_fetch_assoc($query_ps);
$total_ps = $data_ps['total'] ?? 0;

// Total pelanggan
$query_pelanggan = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total FROM pelanggan"
);
$data_pelanggan = mysqli_fetch_assoc($query_pelanggan);
$total_pelanggan = $data_pelanggan['total'] ?? 0;

// Total transaksi
$query_sewa = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total FROM sewa"
);
$data_sewa = mysqli_fetch_assoc($query_sewa);
$total_sewa = $data_sewa['total'] ?? 0;

/* =========================
   HITUNG TRANSAKSI AKTIF
========================= */

$query_aktif = mysqli_query(
    $koneksi,
    "SELECT tgl_sewa, lama_sewa FROM sewa"
);

$total_aktif = 0;
$hari_ini = new DateTime(date('Y-m-d'));

if ($query_aktif) {
    while ($row = mysqli_fetch_assoc($query_aktif)) {
        if (empty($row['tgl_sewa'])) {
            continue;
        }

        $tanggal_mulai = new DateTime($row['tgl_sewa']);
        $tanggal_berakhir = clone $tanggal_mulai;

        $lama_sewa = (int) $row['lama_sewa'];
        $tanggal_berakhir->modify('+' . $lama_sewa . ' days');

        if ($hari_ini < $tanggal_berakhir) {
            $total_aktif++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PS Rental</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background:
                radial-gradient(
                    circle at top left,
                    rgba(0, 229, 255, 0.08),
                    transparent 35%
                ),
                radial-gradient(
                    circle at bottom right,
                    rgba(125, 70, 255, 0.08),
                    transparent 35%
                ),
                #070b14;
            color: #e8faff;
            min-height: 100vh;
        }

        /* =========================
           NAVBAR
        ========================= */
        .navbar {
            min-height: 65px;
            background: #0c1220;
            border-bottom: 1px solid #173044;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 35px;
            gap: 15px;
        }

        .brand {
            color: #00e5ff;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
            white-space: nowrap;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .nav-menu a {
            color: #aab8c7;
            text-decoration: none;
            padding: 9px 14px;
            border-radius: 7px;
            font-size: 14px;
            transition: 0.2s;
        }

        .nav-menu a:hover,
        .nav-menu a.active {
            color: #00e5ff;
            background: #101d2c;
        }

        .nav-menu .logout {
            color: #ff6b81;
        }

        .nav-menu .logout:hover {
            color: #ffffff;
            background: #6b2335;
        }

        /* =========================
           CONTAINER
        ========================= */
        .container {
            width: 92%;
            max-width: 1150px;
            margin: 40px auto;
        }

        /* =========================
           WELCOME
        ========================= */
        .welcome {
            margin-bottom: 30px;
        }

        .welcome h1 {
            font-size: 30px;
            margin-bottom: 8px;
        }

        .welcome h1 span {
            color: #00e5ff;
        }

        .welcome p {
            color: #718096;
            font-size: 14px;
            line-height: 1.6;
        }

        /* =========================
           STATISTIK
        ========================= */
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #0d1422;
            border: 1px solid #17283a;
            border-radius: 12px;
            padding: 20px;
            transition: 0.2s;
        }

        .stat-card:hover {
            border-color: #28506a;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 229, 255, 0.05);
        }

        .stat-icon {
            font-size: 25px;
            margin-bottom: 12px;
        }

        .stat-label {
            color: #718096;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .stat-number {
            font-size: 27px;
            font-weight: bold;
            color: #00e5ff;
            overflow-wrap: anywhere;
        }

        /* =========================
           MENU UTAMA
        ========================= */
        .section-title {
            color: #00e5ff;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .menu-card {
            display: block;
            text-decoration: none;
            background: #0d1422;
            border: 1px solid #17283a;
            border-radius: 12px;
            padding: 25px;
            transition: 0.2s;
        }

        .menu-card:hover {
            transform: translateY(-3px);
            border-color: #00a9c0;
            box-shadow: 0 0 20px rgba(0, 229, 255, 0.07);
        }

        .menu-icon {
            font-size: 32px;
            margin-bottom: 15px;
        }

        .menu-card h3 {
            color: #ffffff;
            font-size: 17px;
            margin-bottom: 7px;
        }

        .menu-card p {
            color: #68788b;
            font-size: 13px;
            line-height: 1.6;
        }

        .arrow {
            color: #00e5ff;
            margin-top: 17px;
            font-size: 13px;
        }

        /* =========================
           FOOTER
        ========================= */
        .footer {
            text-align: center;
            color: #465568;
            font-size: 11px;
            margin-top: 45px;
            padding-bottom: 20px;
        }

        /* =========================
           RESPONSIVE
        ========================= */
        @media (max-width: 850px) {
            .navbar {
                padding: 15px;
                flex-direction: column;
                gap: 12px;
            }

            .nav-menu {
                justify-content: center;
            }

            .stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .menu-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 500px) {
            .stats,
            .menu-grid {
                grid-template-columns: 1fr;
            }

            .container {
                width: 90%;
                margin-top: 25px;
            }

            .welcome h1 {
                font-size: 24px;
            }

            .stat-card,
            .menu-card {
                padding: 20px;
            }
        }
    </style>
</head>

<body>

<!-- =========================
     NAVBAR
========================= -->
<nav class="navbar">
    <div class="brand">
        ⚡ PS RENTAL
    </div>

    <div class="nav-menu">
        <a href="index.php" class="active">
            Dashboard
        </a>

        <a href="rekap.php">
            Rekap Bulanan
        </a>

        <a href="logout.php" class="logout">
            Logout
        </a>
    </div>
</nav>

<div class="container">

    <!-- =========================
         WELCOME
    ========================= -->
    <div class="welcome">
        <h1>
            Halo,
            <span>
                <?= htmlspecialchars(
                    $_SESSION['nama_petugas'] ?? 'Admin',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </span>
            👋
        </h1>

        <p>
            Selamat datang di sistem manajemen rental PlayStation.
        </p>
    </div>

    <!-- =========================
         STATISTIK
    ========================= -->
    <div class="stats">

        <div class="stat-card">
            <div class="stat-icon">🎮</div>
            <div class="stat-label">TOTAL PLAYSTATION</div>
            <div class="stat-number">
                <?= (int) $total_ps ?>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">👤</div>
            <div class="stat-label">TOTAL PELANGGAN</div>
            <div class="stat-number">
                <?= (int) $total_pelanggan ?>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">📋</div>
            <div class="stat-label">TOTAL TRANSAKSI</div>
            <div class="stat-number">
                <?= (int) $total_sewa ?>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">⚡</div>
            <div class="stat-label">SEWA AKTIF</div>
            <div class="stat-number">
                <?= (int) $total_aktif ?>
            </div>
        </div>

    </div>

    <!-- =========================
         MENU UTAMA
    ========================= -->
    <div class="section-title">
        MENU UTAMA
    </div>

    <div class="menu-grid">

        <!-- DATA PLAYSTATION -->
        <a href="ps.php" class="menu-card">
            <div class="menu-icon">🎮</div>
            <h3>Data PlayStation</h3>
            <p>
                Kelola unit PlayStation, jenis, tipe,
                dan harga sewa.
            </p>
            <div class="arrow">
                Kelola Data →
            </div>
        </a>

        <!-- DATA PELANGGAN -->
        <a href="pelanggan.php" class="menu-card">
            <div class="menu-icon">👤</div>
            <h3>Data Pelanggan</h3>
            <p>
                Kelola data pelanggan yang melakukan rental.
            </p>
            <div class="arrow">
                Kelola Data →
            </div>
        </a>

        <!-- TRANSAKSI SEWA -->
        <a href="sewa.php" class="menu-card">
            <div class="menu-icon">⚡</div>
            <h3>Transaksi Sewa</h3>
            <p>
                Kelola transaksi, pembayaran,
                dan status rental.
            </p>
            <div class="arrow">
                Kelola Transaksi →
            </div>
        </a>

        <!-- REKAP BULANAN -->
        <a href="rekap.php" class="menu-card">
            <div class="menu-icon">📊</div>
            <h3>Rekap Bulanan</h3>
            <p>
                Lihat ringkasan transaksi dan total
                pendapatan setiap bulan.
            </p>
            <div class="arrow">
                Lihat Rekap →
            </div>
        </a>

    </div>

    <!-- =========================
         FOOTER
    ========================= -->
    <div class="footer">
        © 2026 PS RENTAL • MANAGEMENT SYSTEM
    </div>

</div>

</body>
</html>
