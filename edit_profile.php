<?php
require_once 'functions.php';

requireLogin();

$users = getUsers();
$userIndex = -1;
$currentUser = null;

foreach ($users as $index => $u) {
    if ($u['id'] == $_SESSION['user_id']) {
        $userIndex = $index;
        $currentUser = $u;
        break;
    }
}

if (!$currentUser) {
    header('Location: logout.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password_baru = $_POST['password_baru'] ?? '';
    $password_lama = $_POST['password_lama'] ?? '';

    if (empty($nama) || empty($email)) {
        $error = 'Nama dan email tidak boleh kosong!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } else {
        $email_terpakai = false;
        foreach ($users as $u) {
            if ($u['id'] != $currentUser['id'] && strtolower($u['email']) === strtolower($email)) {
                $email_terpakai = true;
                break;
            }
        }

        if ($email_terpakai) {
            $error = 'Email tersebut sudah digunakan oleh pengguna lain!';
        } else {
            $users[$userIndex]['nama'] = sanitize($nama);
            $users[$userIndex]['email'] = strtolower(sanitize($email));

            if (!empty($password_baru)) {
                if (empty($password_lama)) {
                    $error = 'Masukkan password lama Anda untuk konfirmasi perubahan password!';
                } elseif (!password_verify($password_lama, $currentUser['password'])) {
                    $error = 'Password lama Anda salah!';
                } elseif (strlen($password_baru) < 6) {
                    $error = 'Password baru minimal harus 6 karakter!';
                } else {
                    $users[$userIndex]['password'] = password_hash($password_baru, PASSWORD_DEFAULT);
                    unset($users[$userIndex]['remember_selector'], $users[$userIndex]['remember_validator_hash'], $users[$userIndex]['remember_expires']);
                }
            }

            if (empty($error)) {
                if (saveUsers($users)) {
                    $_SESSION['nama'] = $users[$userIndex]['nama'];
                    $_SESSION['email'] = $users[$userIndex]['email'];

                    header('Location: dashboard.php?success=' . urlencode('Profil berhasil diperbarui!'));
                    exit;
                } else {
                    $error = 'Gagal memperbarui file data JSON.';
                }
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
    <title>Edit Profil - Pemrograman Web</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <header class="navbar">
            <div class="brand">
                <h3>Portal Sederhana</h3>
            </div>
            <div class="nav-user">
                <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
            </div>
        </header>

        <main class="dashboard-content">
            <div class="auth-card" style="margin: 0 auto; max-width: 500px;">
                <div class="auth-header">
                    <h2>Edit Profil</h2>
                    <p>Perbarui informasi akun Anda</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <?= sanitize($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="edit_profile.php" class="auth-form">
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" value="<?= sanitize($currentUser['nama']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Alamat Email</label>
                        <input type="email" id="email" name="email" value="<?= sanitize($currentUser['email']) ?>" required>
                    </div>

                    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 10px 0;">
                    <p style="font-size: 0.8rem; color: #64748b;">Opsional: Isi kolom di bawah jika ingin mengganti password.</p>

                    <div class="form-group">
                        <label for="password_lama">Password Saat Ini</label>
                        <input type="password" id="password_lama" name="password_lama" placeholder="Wajib jika ganti password">
                    </div>

                    <div class="form-group">
                        <label for="password_baru">Password Baru</label>
                        <input type="password" id="password_baru" name="password_baru" placeholder="Minimal 6 karakter">
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>