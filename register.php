<?php
require_once 'functions.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$nama = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = cleanInput($_POST['nama'] ?? '');
    $email = cleanInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($nama) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Semua field wajib diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal harus 6 karakter!';
    } elseif ($password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok!';
    } else {
        $users = getUsers();

        $email_terdaftar = false;
        foreach ($users as $u) {
            if (strtolower($u['email']) === strtolower($email)) {
                $email_terdaftar = true;
                break;
            }
        }

        if ($email_terdaftar) {
            $error = 'Email sudah terdaftar! Gunakan email lain atau silakan login.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $new_id = count($users) > 0 ? max(array_column($users, 'id')) + 1 : 1;

            $new_user = [
                'id' => $new_id,
                'nama' => $nama,
                'email' => strtolower($email),
                'password' => $hashed_password,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $users[] = $new_user;

            if (saveUsers($users)) {
                header('Location: login.php?success=' . urlencode('Registrasi berhasil! Silakan login dengan akun Anda.'));
                exit;
            } else {
                $error = 'Gagal menyimpan data ke file JSON.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun - Pemrograman Web</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
            <h2>Buat Akun Baru</h2>
            <p>Silakan isi form di bawah untuk mendaftar</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?= sanitize($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php" class="auth-form">
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" value="<?= sanitize($nama) ?>" placeholder="Contoh: Felipe Maranatha" required>
            </div>

            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input type="email" id="email" name="email" value="<?= sanitize($email) ?>" placeholder="nama@email.com" required>
            </div>

            <div class="form-group">
                <label for="password">Password (min. 6 karakter)</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi password" required>
            </div>

            <button type="submit" class="btn btn-primary">Daftar Sekarang</button>
        </form>

        <div class="auth-footer">
            <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
        </div>
    </div>
</body>
</html>