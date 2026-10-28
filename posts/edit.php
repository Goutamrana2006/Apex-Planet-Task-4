<?php

ini_set("session.use_strict_mode", "1");
session_start();

require_once "../config/database.php";

// 1. Check authentication
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

// 2. Validate the logged-in user's ID
$user_id = filter_var(
    $_SESSION["user_id"],
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 1]]
);

if ($user_id === false || $user_id === null) {
    http_response_code(401);
    exit("Invalid login session. Please log in again.");
}

// 3. Validate article ID
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id < 1) {
    header("Location: index.php");
    exit();
}

// 4. Generate CSRF token
if (
    !isset($_SESSION["csrf_token"]) ||
    !is_string($_SESSION["csrf_token"]) ||
    $_SESSION["csrf_token"] === ""
) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$message = "";
$title = "";
$content = "";

// 5. Fetch only an article owned by the logged-in user
$stmt = $conn->prepare(
    "SELECT id, title, content
     FROM posts
     WHERE id = ? AND user_id = ?"
);

if (!$stmt) {
    error_log("Edit post fetch prepare failed: " . $conn->error);
    http_response_code(500);
    exit("Unable to load the article.");
}

$stmt->bind_param("ii", $id, $user_id);

if (!$stmt->execute()) {
    error_log("Edit post fetch failed: " . $stmt->error);
    $stmt->close();
    http_response_code(500);
    exit("Unable to load the article.");
}

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    http_response_code(404);
    exit("Article not found or you do not have permission to edit it.");
}

$post = $result->fetch_assoc();
$stmt->close();

$title = $post["title"];
$content = $post["content"];

// 6. Process update request
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (
        !isset($_POST["csrf_token"], $_SESSION["csrf_token"]) ||
        !is_string($_POST["csrf_token"]) ||
        !is_string($_SESSION["csrf_token"]) ||
        !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
    ) {
        http_response_code(403);
        exit("Invalid security token. Please refresh the page and try again.");
    }

    if (
        !isset($_POST["title"], $_POST["content"]) ||
        !is_string($_POST["title"]) ||
        !is_string($_POST["content"])
    ) {
        $message = "Invalid form input.";
    } else {

        $title = trim($_POST["title"]);
        $content = trim($_POST["content"]);

if ($title === "" || $content === "") {
    $message = "Please fill in all fields.";

} elseif (mb_strlen($title, "UTF-8") > 255) {
    $message = "Title must not exceed 255 characters.";

} elseif (mb_strlen($content, "UTF-8") > 20000) {
    $message = "Content must not exceed 20,000 characters.";

} else {

            // 7. Update only the owner's article
            $stmt = $conn->prepare(
                "UPDATE posts
                 SET title = ?, content = ?
                 WHERE id = ? AND user_id = ?"
            );

            if (!$stmt) {
                error_log("Update post prepare failed: " . $conn->error);
                $message = "Unable to update the article. Please try again.";
            } else {

                $stmt->bind_param(
                    "ssii",
                    $title,
                    $content,
                    $id,
                    $user_id
                );

                if ($stmt->execute()) {
                    $stmt->close();
                    $conn->close();

                    header("Location: index.php?updated=1");
                    exit();
                }

                error_log("Update post execution failed: " . $stmt->error);
                $message = "Unable to update the article. Please try again.";
                $stmt->close();
            }
        }
    }
}

// 8. Escape output
function e($value) {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        "UTF-8"
    );
}

$username = (string) ($_SESSION["username"] ?? "Writer");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Article — InkSpace</title>
    <link rel="stylesheet" href="edit.css?v=1">
</head>

<body>

<header class="editor-navbar">
    <a href="index.php" class="editor-brand">
        <span class="brand-mark">i.</span>
        <span>InkSpace</span>
    </a>

    <div class="navbar-right">
        <span class="writer-name">
            Writing as <strong><?= e($username) ?></strong>
        </span>
        <a href="index.php" class="nav-back">Back to articles</a>
    </div>
</header>

<main class="editor-main">

    <div class="editor-heading">
        <span class="eyebrow">YOUR CREATIVE SPACE</span>
        <h1>Refine your story<span>.</span></h1>
        <p>
            Every great article deserves another look.
            Make your changes and save your work.
        </p>
    </div>

    <section class="editor-card">

        <div class="editor-card-top">
            <div>
                <span class="status-dot"></span>
                <span class="draft-label">ARTICLE EDITOR</span>
            </div>
            <span class="editor-badge">Editing article #<?= (int) $id ?></span>
        </div>

        <?php if ($message !== ""): ?>
            <div class="editor-alert" role="alert">
                <span class="alert-icon">!</span>
                <span><?= e($message) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="edit.php?id=<?= (int) $id ?>" id="articleForm">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($_SESSION["csrf_token"]) ?>"
            >

            <div class="form-group">
                <label for="title">ARTICLE TITLE</label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    class="title-input"
                    placeholder="Give your article a meaningful title..."
                    maxlength="255"
                    value="<?= e($title) ?>"
                    required
                >

                <div class="field-meta">
                    <span>Make it clear, compelling, and memorable.</span>
                    <span id="titleCount">0 / 255</span>
                </div>
            </div>

            <div class="form-group content-group">
                <label for="content">YOUR STORY</label>

                <textarea
                    id="content"
                    name="content"
                    class="content-input"
                    placeholder="Start writing your thoughts here..."
                    rows="12"
                    maxlength="20000"
                    required
                ><?= e($content) ?></textarea>

                <div class="field-meta">
                    <span>Your ideas deserve to be expressed.</span>
                    <span id="contentCount">0 / 20000</span>
                </div>
            </div>

            <div class="editor-footer">

                <div class="editor-note">
                    <span class="note-icon" aria-hidden="true">✓</span>
                    Your changes will be saved to your article.
                </div>

                <div class="editor-actions">
                    <a href="index.php" class="btn-cancel">Cancel</a>

                    <button type="submit" class="btn-publish" id="saveButton">
                        <span>Save Changes</span>
                        <span aria-hidden="true">→</span>
                    </button>
                </div>

            </div>

        </form>

    </section>

    <p class="editor-tip">
        <span aria-hidden="true">✦</span>
        Great writing gets better with every revision.
    </p>

</main>

<footer class="editor-page-footer">
    <p>© <?= date("Y") ?> InkSpace. A space for your ideas.</p>
</footer>

<script>
    const titleInput = document.getElementById("title");
    const contentInput = document.getElementById("content");
    const titleCount = document.getElementById("titleCount");
    const contentCount = document.getElementById("contentCount");
    const articleForm = document.getElementById("articleForm");
    const saveButton = document.getElementById("saveButton");

function updateCounters() {
    titleCount.textContent =
        `${Array.from(titleInput.value).length} / 255`;

    contentCount.textContent =
        `${Array.from(contentInput.value).length} / 20000`;
}

    titleInput.addEventListener("input", updateCounters);
    contentInput.addEventListener("input", updateCounters);
    updateCounters();

    articleForm.addEventListener("submit", function (event) {
        if (
            titleInput.value.trim() === "" ||
            contentInput.value.trim() === ""
        ) {
            event.preventDefault();
            alert("Please fill in both the title and content.");
            return;
        }

        if (!articleForm.checkValidity()) {
            return;
        }

        saveButton.disabled = true;
        saveButton.querySelector("span").textContent = "Saving...";
    });
</script>

</body>
</html>

<?php
$conn->close();
?>