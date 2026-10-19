<?php
session_start();
if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}
include 'koneksi.php';
$error = "";
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string(
        $koneksi,
        $_POST['username']
    );
    $password = $_POST['password'];
    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM petugas
         WHERE username='$username'
         LIMIT 1"
    );
    if (mysqli_num_rows($query) > 0) {
        $petugas = mysqli_fetch_assoc($query);
        if (
            password_verify(
                $password,
                $petugas['password']
            )
        ) {
            $_SESSION['login'] = true;
            $_SESSION['id_petugas'] =
                $petugas['id_petugas'];
            $_SESSION['nama_petugas'] =
                $petugas['nama_petugas'];
            $_SESSION['username'] =
                $petugas['username'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Username atau password salah!";
        }
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">
    <title>Login - PS Rental</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background:
                radial-gradient(
                    circle at top left,
                    rgba(0, 229, 255, 0.10),
                    transparent 35%
                ),
                radial-gradient(
                    circle at bottom right,
                    rgba(125, 70, 255, 0.10),
                    transparent 35%
                ),
                #070b14;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .login-container {
            width: 100%;
            max-width: 390px;
        }
        .logo {
            text-align: center;
            margin-bottom: 25px;
        }
        .logo-icon {
            font-size: 38px;
            margin-bottom: 8px;
        }
        .logo h1 {
            color: #00e5ff;
            font-size: 24px;
            letter-spacing: 2px;
        }
        .logo p {
            color: #68788b;
            font-size: 13px;
            margin-top: 6px;
        }
        .login-card {
            background: #0d1422;
            border: 1px solid #17283a;
            border-radius: 14px;
            padding: 28px;
            box-shadow:
                0 0 25px rgba(0, 229, 255, 0.06);
        }
        .login-title {
            margin-bottom: 22px;
        }
        .login-title h2 {
            font-size: 19px;
            margin-bottom: 6px;
        }
        .login-title p {
            color: #718096;
            font-size: 13px;
        }
        .form-group {
            margin-bottom: 17px;
        }
        label {
            display: block;
            color: #8998aa;
            font-size: 13px;
            margin-bottom: 7px;
        }
        input {
            width: 100%;
            padding: 12px;
            background: #080e19;
            border: 1px solid #22364a;
            border-radius: 7px;
            color: white;
            outline: none;
            font-size: 14px;
        }
        input:focus {
            border-color: #00e5ff;
            box-shadow:
                0 0 8px
                rgba(0, 229, 255, 0.12);
        }
        input::placeholder {
            color: #4f5f70;
        }
        .error {
            background: #321a22;
            border: 1px solid #5c2735;
            color: #ff7187;
            padding: 10px 12px;
            border-radius: 7px;
            font-size: 13px;
            margin-bottom: 17px;
            text-align: center;
        }
        .btn-login {
            width: 100%;
            border: none;
            padding: 12px;
            border-radius: 7px;
            background: #00d9ef;
            color: #031017;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-login:hover {
            background: #32eaff;
            box-shadow:
                0 0 15px
                rgba(0, 229, 255, 0.25);
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #465568;
            font-size: 11px;
        }
        @media (max-width: 450px) {
            .login-card {
                padding: 22px;
            }
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="logo">
        <div class="logo-icon">
            ⚡
        </div>
        <h1>
            PS RENTAL
        </h1>
        <p>
            PlayStation Rental Management System
        </p>
    </div>
    <div class="login-card">
        <div class="login-title">
            <h2>
                Login
            </h2>
            <p>
                Masuk ke sistem untuk melanjutkan.
            </p>
        </div>
        <?php if ($error != "") { ?>
            <div class="error">
                <?= $error ?>
            </div>
        <?php } ?>
        <form method="POST">
            <div class="form-group">
                <label>
                    Username
                </label>
                <input
                    type="text"
                    name="username"
                    placeholder="Masukkan username"
                    required
                    autocomplete="username"
                >
            </div>
            <div class="form-group">
                <label>
                    Password
                </label>
                <input
                    type="password"
                    name="password"
                    placeholder="Masukkan password"
                    required
                    autocomplete="current-password"
                >
            </div>
            <button
                type="submit"
                name="login"
                class="btn-login">
                MASUK
            </button>
        </form>
    </div>
    <div class="footer">
        © 2026 PS RENTAL • MANAGEMENT SYSTEM
    </div>
</div>
</body>
</html>