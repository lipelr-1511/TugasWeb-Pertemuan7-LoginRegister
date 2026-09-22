<?php
require_once 'functions.php';

requireLogin();

$users = getUsers();
$currentUser = null;

foreach ($users as $u) {
    if ($u['id'] == $_SESSION['user_id']) {
        $currentUser = $u;
        break;
    }
}

if (!$currentUser) {
    header('Location: logout.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - Pemrograman Web</title>
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
                <span>Halo, <strong><?= sanitize($currentUser['nama']) ?></strong></span>
                <a href="logout.php" class="btn btn-outline-danger">Logout</a>
            </div>
        </header>

        <main class="dashboard-content">
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?= sanitize($_GET['success']) ?>
                </div>
            <?php endif; ?>

            <div class="welcome-banner">
                <h2>Selamat Datang di Dashboard!</h2>
                <p>Halaman ini diproteksi menggunakan State Management Session PHP. Hanya pengguna yang terautentikasi yang dapat mengaksesnya.</p>
            </div>

            <div class="profile-card">
                <div class="profile-header">
                    <h3>Informasi Akun Anda</h3>
                    <a href="edit_profile.php" class="btn btn-secondary">Edit Profil</a>
                </div>
                <div class="profile-details">
                    <div class="detail-item">
                        <span class="label">ID User:</span>
                        <span class="value">#<?= sanitize($currentUser['id']) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Nama Lengkap:</span>
                        <span class="value"><?= sanitize($currentUser['nama']) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Alamat Email:</span>
                        <span class="value"><?= sanitize($currentUser['email']) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Tanggal Bergabung:</span>
                        <span class="value"><?= sanitize($currentUser['created_at']) ?></span>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>