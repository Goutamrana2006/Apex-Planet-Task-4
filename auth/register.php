<?php

session_start();

require_once "../config/database.php";

$message = "";

// Generate a CSRF token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $csrfToken = $_POST["csrf_token"] ?? "";

    // Validate CSRF token
    if (
        !is_string($csrfToken) ||
        !hash_equals($_SESSION["csrf_token"], $csrfToken)
    ) {
        http_response_code(403);
        exit("Invalid request. Please refresh the page and try again.");
    }

    // Validate required fields
    if ($username === "" || $password === "") {

        $message = "Please fill all fields.";

    } elseif (strlen($username) > 50) {

        $message = "Username must not exceed 50 characters.";

    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {

        $message = "Username must be 3–50 characters and contain only letters, numbers, and underscores.";

    } elseif (strlen($password) < 8) {

        $message = "Password must contain at least 8 characters.";

    } elseif (strlen($password) > 72) {

        $message = "Password must not exceed 72 bytes.";

    } else {

        // Check whether the username already exists
        $check = $conn->prepare(
            "SELECT id FROM users WHERE username = ?"
        );

        if (!$check) {
            error_log("Registration query preparation failed.");
            http_response_code(500);
            exit("A server error occurred. Please try again later.");
        }

       $check->bind_param("s", $username);

if (!$check->execute()) {
    error_log("Registration username check failed.");
    $check->close();

    http_response_code(500);
    exit("A server error occurred. Please try again later.");
}

$result = $check->get_result();

if (!$result) {
    error_log("Registration username result retrieval failed.");
    $check->close();

    http_response_code(500);
    exit("A server error occurred. Please try again later.");
}

        if ($result->num_rows > 0) {

            $message = "Username already exists.";

        } else {

            // Hash the password
            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            if ($hashedPassword === false) {
                error_log("Password hashing failed.");
                http_response_code(500);
                exit("A server error occurred. Please try again later.");
            }

            // Insert the new user
            $stmt = $conn->prepare(
                "INSERT INTO users (username, password) VALUES (?, ?)"
            );

            if (!$stmt) {
                error_log("Registration insert preparation failed.");
                http_response_code(500);
                exit("A server error occurred. Please try again later.");
            }

            $stmt->bind_param(
                "ss",
                $username,
                $hashedPassword
            );

            if ($stmt->execute()) {

                // Rotate the token after successful registration
                $_SESSION["csrf_token"] = bin2hex(random_bytes(32));

                $message = "Registration successful! You can now login.";

            } else {

                error_log("Registration insert failed.");
                $message = "Registration failed. Please try again.";

            }

            $stmt->close();
        }

        $check->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register - InkSpace</title>

    <link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="container">

    <h1>Create Account</h1>

    <?php if (!empty($message)): ?>
        <p role="status" aria-live="polite">
            <?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="register.php">

        <input
            type="hidden"
            name="csrf_token"
            value="<?php echo htmlspecialchars(
                $_SESSION["csrf_token"],
                ENT_QUOTES,
                "UTF-8"
            ); ?>"
        >

        <label for="username">Username</label>

        <input
            type="text"
            id="username"
            name="username"
            minlength="3"
            maxlength="50"
            pattern="[a-zA-Z0-9_]{3,50}"
            autocomplete="username"
            required
        >

        <label for="password">Password</label>

        <input
            type="password"
            id="password"
            name="password"
            minlength="8"
            maxlength="72"
            autocomplete="new-password"
            required
        >

        <button type="submit">Register</button>

    </form>

    <p>
        Already have an account?
        <a href="login.php">Login</a>
    </p>

</div>

</body>
</html>