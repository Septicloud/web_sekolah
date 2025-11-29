<?php
session_start();

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Akhiri sesi
session_destroy();

// 4. Arahkan pengguna kembali ke halaman login atau halaman utama
header("Location: login.php");
exit;
?>