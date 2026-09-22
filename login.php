<?php
require_once 'functions.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$sukses = '';

if (isset($_GET['success'])) {
    $sukses = $_GET['success'];
}
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (empty($email) || empty($password)) {
        $error = 'Email dan password tidak boleh kosong!';
    } else {
        $users = getUsers();
        $foundUser = null;

        foreach ($users as $u) {
            if (strtolower($u['email']) === strtolower($email)) {
                $foundUser = $u;
                break;
            }
        }

        if ($foundUser && password_verify($password, $foundUser['password'])) {
            $_SESSION['user_id'] = $foundUser['id'];
            $_SESSION['nama'] = $foundUser['nama'];
            $_SESSION['email'] = $foundUser['email'];

            if ($remember) {
                setRememberMeCookie($foundUser['id']);
            }

            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Email atau password yang Anda masukkan salah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pemrograman Web</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
            <h2>Selamat Datang</h2>
            <p>Silakan masuk ke akun Anda</p>
        </div>

        <?php if (!empty($sukses)): ?>
            <div class="alert alert-success">
                <?= sanitize($sukses) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?= sanitize($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="auth-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="nama@email.com" required value="<?= isset($_POST['email']) ? sanitize($_POST['email']) : '' ?>">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" required>
            </div>

            <div class="form-checkbox">
                <label>
                    <input type="checkbox" name="remember" id="remember"> Ingat Saya
                </label>
            </div>

            <button type="submit" class="btn btn-primary">Masuk</button>
        </form>

        <div class="auth-footer">
            <p>Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
        </div>
    </div>
</body>
</html>