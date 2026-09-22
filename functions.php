<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('USER_FILE', __DIR__ . '/users.json');

function getUsers() {
    if (!file_exists(USER_FILE)) {
        file_put_contents(USER_FILE, json_encode([], JSON_PRETTY_PRINT));
        return [];
    }
    $json = file_get_contents(USER_FILE);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function saveUsers($users) {
    return file_put_contents(USER_FILE, json_encode($users, JSON_PRETTY_PRINT), LOCK_EX);
}

function sanitize($data) {
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

function cleanInput($data) {
    return trim((string) $data);
}

function setRememberMeCookie($userId) {
    $selector = bin2hex(random_bytes(8));
    $validator = bin2hex(random_bytes(32));
    $hashedValidator = hash('sha256', $validator);
    $expires = time() + (7 * 24 * 60 * 60);

    $users = getUsers();
    foreach ($users as $index => $user) {
        if ($user['id'] == $userId) {
            $users[$index]['remember_selector'] = $selector;
            $users[$index]['remember_validator_hash'] = $hashedValidator;
            $users[$index]['remember_expires'] = $expires;
            saveUsers($users);
            break;
        }
    }

    setcookie('remember_user', $selector . ':' . $validator, $expires, '/', '', false, true);
}

function clearRememberMeCookie($userId = null) {
    if ($userId !== null) {
        $users = getUsers();
        foreach ($users as $index => $user) {
            if ($user['id'] == $userId) {
                unset($users[$index]['remember_selector'], $users[$index]['remember_validator_hash'], $users[$index]['remember_expires']);
                saveUsers($users);
                break;
            }
        }
    }
    setcookie('remember_user', '', time() - 3600, '/');
}


function checkRememberMe() {
    if (isset($_SESSION['user_id']) || !isset($_COOKIE['remember_user'])) {
        return;
    }

    $parts = explode(':', $_COOKIE['remember_user']);
    if (count($parts) !== 2) {
        clearRememberMeCookie();
        return;
    }
    [$selector, $validator] = $parts;

    $users = getUsers();
    foreach ($users as $user) {
        if (!isset($user['remember_selector']) || !hash_equals($user['remember_selector'], $selector)) {
            continue;
        }

        if (isset($user['remember_expires']) && $user['remember_expires'] < time()) {
            clearRememberMeCookie($user['id']);
            return;
        }

        if (hash_equals($user['remember_validator_hash'], hash('sha256', $validator))) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['email'] = $user['email'];
        } else {
            clearRememberMeCookie($user['id']);
        }
        return;
    }
}

checkRememberMe();

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php?error=" . urlencode("Silakan login terlebih dahulu untuk mengakses halaman tersebut."));
        exit;
    }
}
?>