<?php
ini_set("session.use_strict_mode", "1");
// Start the existing session.
session_start();

// Clear all session variables.
$_SESSION = [];

// Expire the session cookie using its existing parameters.
if (ini_get("session.use_cookies")) {

    $params = session_get_cookie_params();

    setcookie(session_name(), "", [
        "expires" => time() - 42000,
        "path" => $params["path"],
        "domain" => $params["domain"],
        "secure" => $params["secure"],
        "httponly" => $params["httponly"],
        "samesite" => "Lax"
    ]);
}

// Destroy the server-side session.
session_destroy();

// Redirect to the login page.
header("Location: login.php");
exit();

?>