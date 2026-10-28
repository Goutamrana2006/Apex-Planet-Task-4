<?php

// Configure session cookies before starting the session.
ini_set("session.use_strict_mode", "1");

session_set_cookie_params([
    "httponly" => true,
    "secure" => !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off",
    "samesite" => "Lax",
    "path" => "/"
]);

session_start();

require_once "../config/database.php";

$message = "";

// Generate a CSRF token.
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $csrfToken = $_POST["csrf_token"] ?? "";

    // Validate CSRF token.
    if (
        !is_string($csrfToken) ||
        !hash_equals($_SESSION["csrf_token"], $csrfToken)
    ) {
        http_response_code(403);
        exit("Invalid request. Please refresh the page and try again.");
    }

    if (
        !is_string($username) ||
        !is_string($password) ||
        $username === "" ||
        $password === ""
    ) {

        $message = "Please fill all fields.";

    } elseif (strlen($username) > 50 || strlen($password) > 4096) {

        $message = "Invalid username or password.";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, username, password FROM users WHERE username = ?"
        );

        if (!$stmt) {

            error_log("Login query preparation failed.");
            http_response_code(500);
            exit("A server error occurred. Please try again later.");

        }

        $stmt->bind_param("s", $username);

        if (!$stmt->execute()) {

            error_log("Login query execution failed.");
            $stmt->close();

            http_response_code(500);
            exit("A server error occurred. Please try again later.");

        }

        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {

                // Regenerate the session ID after successful login.
                if (!session_regenerate_id(true)) {
                    error_log("Session ID regeneration failed.");
                    http_response_code(500);
                    exit("A server error occurred. Please try again later.");
                }

                $_SESSION["user_id"] = (int) $user["id"];
                $_SESSION["username"] = $user["username"];

                // Rotate the CSRF token after authentication.
                $_SESSION["csrf_token"] = bin2hex(random_bytes(32));

                $stmt->close();

                // Redirect to the InkSpace Home page.
                header("Location: ../index.php");
                exit();

            }

        }

        $stmt->close();

        // Use the same message for unknown usernames and incorrect passwords.
        $message = "Invalid username or password.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | InkSpace</title>

    <meta
        name="description"
        content="Log in to InkSpace to explore and manage your articles."
    >

    <link rel="stylesheet" href="../style.css?v=2">
</head>

<body>

    <main class="container">

        <h1>Welcome Back</h1>

        <p style="text-align: center; margin-bottom: 24px;">
            Log in to continue to InkSpace.
        </p>

        <?php if (!empty($message)): ?>
            <p role="alert" aria-live="polite">
                <?php
                echo htmlspecialchars(
                    $message,
                    ENT_QUOTES,
                    "UTF-8"
                );
                ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="login.php">

            <input
                type="hidden"
                name="csrf_token"
                value="<?php
                echo htmlspecialchars(
                    $_SESSION["csrf_token"],
                    ENT_QUOTES,
                    "UTF-8"
                );
                ?>"
            >

            <label for="username">Username</label>

            <input
                type="text"
                id="username"
                name="username"
                placeholder="Enter your username"
                maxlength="50"
                autocomplete="username"
                required
            >

            <label for="password">Password</label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                maxlength="4096"
                autocomplete="current-password"
                required
            >

            <button type="submit">Login to InkSpace</button>

        </form>

        <p style="text-align: center;">
            Don't have an account?
            <a href="register.php">Create an account</a>
        </p>

    </main>

</body>

</html>