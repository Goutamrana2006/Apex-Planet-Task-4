<?php

ini_set("session.use_strict_mode", "1");

session_start();

require_once "../config/database.php";

// 1. Check login session
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

// 2. Generate CSRF token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$message = "";
$title = "";
$content = "";

// 3. Handle article submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $submitted_token = $_POST["csrf_token"] ?? "";

    // Validate CSRF token
    if (
        !is_string($submitted_token) ||
        !hash_equals($_SESSION["csrf_token"], $submitted_token)
    ) {
        http_response_code(403);

        $message = "Invalid security token. Please refresh the page and try again.";

    } else {

        // Retrieve and normalize submitted values
        $title = trim($_POST["title"] ?? "");
        $content = trim($_POST["content"] ?? "");

        // Validate required fields
        if ($title === "" || $content === "") {

            $message = "Please enter both the article title and content.";

        // Validate title length using Unicode characters
        } elseif (mb_strlen($title, "UTF-8") > 255) {

            $message = "The article title must not exceed 255 characters.";

        // Limit article content length
        } elseif (mb_strlen($content, "UTF-8") > 20000) {

            $message = "Article content must not exceed 20,000 characters.";

        } else {

            // 4. Validate the logged-in user's ID
            $user_id = filter_var(
                $_SESSION["user_id"],
                FILTER_VALIDATE_INT,
                ["options" => ["min_range" => 1]]
            );

            if ($user_id === false) {

                error_log("Create post failed: Invalid session user ID.");

                $message = "Your login session is invalid. Please log in again.";

            } else {

                // 5. Insert article using a prepared statement
                $stmt = $conn->prepare(
                    "INSERT INTO posts (user_id, title, content)
                     VALUES (?, ?, ?)"
                );

                if ($stmt) {

                    $stmt->bind_param(
                        "iss",
                        $user_id,
                        $title,
                        $content
                    );

                    if ($stmt->execute()) {

                        $stmt->close();
                        $conn->close();

                        header("Location: index.php?created=1");
                        exit();

                    } else {

                        error_log("Create post failed: " . $stmt->error);

                        $message = "Unable to publish your article. Please try again.";

                        $stmt->close();
                    }

                } else {

                    error_log("Create post prepare failed: " . $conn->error);

                    $message = "Something went wrong. Please try again.";
                }
            }
        }
    }
}

// 6. Escape output before displaying it
$safe_message = htmlspecialchars(
    $message,
    ENT_QUOTES,
    "UTF-8"
);

$safe_title = htmlspecialchars(
    $title,
    ENT_QUOTES,
    "UTF-8"
);

$safe_content = htmlspecialchars(
    $content,
    ENT_QUOTES,
    "UTF-8"
);

$safe_username = htmlspecialchars(
    $_SESSION["username"] ?? "User",
    ENT_QUOTES,
    "UTF-8"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="Write and publish your next article on InkSpace."
    >

    <title>Write an Article | InkSpace</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="editor.css">

</head>

<body>

    <!-- Navigation -->
    <header class="editor-navbar">

        <div class="editor-nav-inner">

            <a href="index.php" class="editor-brand">

                <span class="brand-icon">I</span>

                <span>
                    Ink<span class="brand-highlight">Space</span>
                </span>

            </a>

            <nav class="editor-nav-links" aria-label="Main navigation">

                <a href="index.php">Home</a>

                <a href="create.php">Write Article</a>

            </nav>

            <div class="editor-nav-right">

                <span class="writer-greeting">
                    Welcome, <?= $safe_username ?>
                </span>

                <a href="../auth/logout.php" class="logout-link">
                    Logout
                </a>

            </div>

        </div>

    </header>

    <!-- Main Content -->
    <main class="editor-main">

        <!-- Page Heading -->
        <section class="editor-heading">

            <a href="index.php" class="back-link">

                <span aria-hidden="true">←</span>

                Back to Articles

            </a>

            <div class="heading-row">

                <div>

                    <span class="editor-eyebrow">
                        YOUR CREATIVE SPACE
                    </span>

                    <h1>
                        Write an <span>Article.</span>
                    </h1>

                    <p>
                        Turn your ideas into meaningful stories.
                        Write, share your knowledge, and inspire
                        the InkSpace community.
                    </p>

                </div>

                <div class="heading-decoration" aria-hidden="true">
                    <span>✎</span>
                </div>

            </div>

        </section>

        <!-- Editor Card -->
        <section class="editor-card">

            <div class="editor-card-header">

                <div class="editor-card-title">

                    <div class="document-icon" aria-hidden="true">
                        ▤
                    </div>

                    <div>

                        <h2>Article Editor</h2>

                        <p>Create something worth reading.</p>

                    </div>

                </div>

                <div class="draft-badge">

                    <span></span>

                    NEW ARTICLE

                </div>

            </div>

            <!-- Error Message -->
            <?php if ($message !== ""): ?>

                <div class="form-alert" role="alert">

                    <span aria-hidden="true">!</span>

                    <p><?= $safe_message ?></p>

                </div>

            <?php endif; ?>

            <!-- Article Form -->
            <form
                id="articleForm"
                method="POST"
                action="create.php"
            >

                <!-- CSRF Token -->
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars(
                        $_SESSION["csrf_token"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>"
                >

                <!-- Article Title -->
                <div class="form-group">

                    <div class="label-row">

                        <label for="title">
                            Article Title
                        </label>

                        <span class="field-required">
                            REQUIRED
                        </span>

                    </div>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        maxlength="255"
                        placeholder="Give your article a compelling title..."
                        value="<?= $safe_title ?>"
                        required
                    >

                    <div class="field-hint-row">

                        <span>
                            Keep it clear, specific, and engaging.
                        </span>

                        <span id="titleCounter" aria-live="polite">
                            <?= mb_strlen($title, "UTF-8") ?>/255
                        </span>

                    </div>

                </div>

                <!-- Article Content -->
                <div class="form-group content-group">

                    <div class="label-row">

                        <label for="content">
                            Article Content
                        </label>

                        <span class="field-required">
                            REQUIRED
                        </span>

                    </div>

                    <div class="content-editor">

                        <div class="content-editor-top">

                            <div class="editor-dots" aria-hidden="true">

                                <span></span>
                                <span></span>
                                <span></span>

                            </div>

                            <span>YOUR WRITING CANVAS</span>

                            <span class="secure-indicator">

                                <span aria-hidden="true">✓</span>

                                Ready to write

                            </span>

                        </div>

                        <textarea
                            id="content"
                            name="content"
                            rows="12"
                            maxlength="20000"
                            placeholder="Start with your first idea...&#10;&#10;Share your thoughts, explain what you learned, or tell your story."
                            required
                        ><?= $safe_content ?></textarea>

                        <div class="content-editor-footer">

                            <span class="writing-tip">

                                <span aria-hidden="true">✦</span>

                                Every great article starts with an idea.

                            </span>

                            <span id="contentCounter" aria-live="polite">
                                <?= mb_strlen($content, "UTF-8") ?> characters
                            </span>

                        </div>

                    </div>

                    <p class="field-help">
                        Tip: Use short paragraphs and clear explanations
                        to make your article easier to read.
                        Maximum length: 20,000 characters.
                    </p>

                </div>

                <!-- Publishing Note -->
                <div class="publishing-note">

                    <div class="publishing-icon" aria-hidden="true">
                        ✓
                    </div>

                    <div>

                        <strong>Ready to share your ideas?</strong>

                        <p>
                            Review your title and content before publishing.
                            Your article will be saved to InkSpace.
                        </p>

                    </div>

                </div>

                <!-- Form Actions -->
                <div class="editor-actions">

                    <a href="index.php" class="cancel-button">
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="publish-button"
                        id="publishButton"
                    >

                        <span>Publish Article</span>

                        <span aria-hidden="true">→</span>

                    </button>

                </div>

            </form>

        </section>

        <div class="editor-footer-note">

            <span class="note-check" aria-hidden="true">✓</span>

            Your ideas matter. Keep writing and keep learning.

        </div>

    </main>

    <!-- Footer -->
    <footer class="editor-footer">

        <div class="editor-footer-inner">

            <a href="index.php" class="editor-brand footer-brand">

                <span class="brand-icon">I</span>

                <span>
                    Ink<span class="brand-highlight">Space</span>
                </span>

            </a>

            <p>
                &copy; <?= date("Y") ?> InkSpace.
                Built for people who love to share ideas.
            </p>

            <span>Write. Share. Inspire.</span>

        </div>

    </footer>

    <!-- Character Counters -->
    <script>

        const titleInput = document.getElementById("title");
        const contentInput = document.getElementById("content");

        const titleCounter = document.getElementById("titleCounter");
        const contentCounter = document.getElementById("contentCounter");

        const articleForm = document.getElementById("articleForm");
        const publishButton = document.getElementById("publishButton");

        function updateTitleCounter() {

            titleCounter.textContent =
                `${Array.from(titleInput.value).length}/255`;

        }

        function updateContentCounter() {

            contentCounter.textContent =
                `${Array.from(contentInput.value).length} characters`;

        }

        titleInput.addEventListener("input", updateTitleCounter);

        contentInput.addEventListener("input", updateContentCounter);

        articleForm.addEventListener("submit", function () {

            if (articleForm.checkValidity()) {

                publishButton.disabled = true;

                publishButton.querySelector("span:first-child").textContent =
                    "Publishing...";

            }

        });

        updateTitleCounter();
        updateContentCounter();

    </script>

</body>

</html>
