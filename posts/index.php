<?php

ini_set("session.use_strict_mode", "1");
session_start();

require_once "../config/database.php";

// 1. Authentication check
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

// 2. Validate logged-in user ID
$user_id = filter_var(
    $_SESSION["user_id"],
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 1]]
);

if ($user_id === false || $user_id === null) {
    http_response_code(401);
    exit("Invalid login session. Please log in again.");
}

// 3. Generate CSRF token
if (
    !isset($_SESSION["csrf_token"]) ||
    !is_string($_SESSION["csrf_token"]) ||
    $_SESSION["csrf_token"] === ""
) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// 4. Safely handle search input
$search = "";

if (isset($_GET["search"]) && is_string($_GET["search"])) {
    $search = trim($_GET["search"]);
}

if (strlen($search) > 200) {
    $search = substr($search, 0, 200);
}

// 5. Validate pagination
$postsPerPage = 6;

$page = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);

if ($page === false || $page === null || $page < 1) {
    $page = 1;
}

// 6. Count matching posts
if ($search !== "") {

    $searchTerm = "%" . $search . "%";

    $countStmt = $conn->prepare(
        "SELECT COUNT(*) AS total
         FROM posts
         WHERE title LIKE ? OR content LIKE ?"
    );

    if (!$countStmt) {
        error_log("Post count prepare failed: " . $conn->error);
        http_response_code(500);
        exit("Unable to load articles. Please try again later.");
    }

    $countStmt->bind_param("ss", $searchTerm, $searchTerm);

    if (!$countStmt->execute()) {
        error_log("Post count execution failed: " . $countStmt->error);
        $countStmt->close();
        http_response_code(500);
        exit("Unable to load articles. Please try again later.");
    }

    $countResult = $countStmt->get_result();
    $countRow = $countResult->fetch_assoc();
    $totalPosts = (int) ($countRow["total"] ?? 0);

    $countStmt->close();

} else {

    $countResult = $conn->query(
        "SELECT COUNT(*) AS total FROM posts"
    );

    if (!$countResult) {
        error_log("Post count query failed: " . $conn->error);
        http_response_code(500);
        exit("Unable to load articles. Please try again later.");
    }

    $countRow = $countResult->fetch_assoc();
    $totalPosts = (int) ($countRow["total"] ?? 0);

    $countResult->free();
}

// 7. Calculate pagination
$totalPages = (int) ceil($totalPosts / $postsPerPage);

if ($totalPages > 0 && $page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $postsPerPage;

// 8. Fetch articles
if ($search !== "") {

    $stmt = $conn->prepare(
        "SELECT id, user_id, title, content, created_at
         FROM posts
         WHERE title LIKE ? OR content LIKE ?
         ORDER BY created_at DESC, id DESC
         LIMIT ? OFFSET ?"
    );

    if (!$stmt) {
        error_log("Post fetch prepare failed: " . $conn->error);
        http_response_code(500);
        exit("Unable to load articles. Please try again later.");
    }

    $stmt->bind_param(
        "ssii",
        $searchTerm,
        $searchTerm,
        $postsPerPage,
        $offset
    );

} else {

    $stmt = $conn->prepare(
        "SELECT id, user_id, title, content, created_at
         FROM posts
         ORDER BY created_at DESC, id DESC
         LIMIT ? OFFSET ?"
    );

    if (!$stmt) {
        error_log("Post fetch prepare failed: " . $conn->error);
        http_response_code(500);
        exit("Unable to load articles. Please try again later.");
    }

    $stmt->bind_param("ii", $postsPerPage, $offset);
}

if (!$stmt->execute()) {
    error_log("Post fetch execution failed: " . $stmt->error);
    $stmt->close();
    http_response_code(500);
    exit("Unable to load articles. Please try again later.");
}

$result = $stmt->get_result();

// 9. HTML escaping helper
function e($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        "UTF-8"
    );
}

// 10. Format article date safely
function formatPostDate($value)
{
    $timestamp = strtotime((string) $value);

    if ($timestamp === false) {
        return "Date unavailable";
    }

    return date("M j, Y", $timestamp);
}

// 11. Pagination URL helper
function pageUrl($pageNumber, $search)
{
    return "?search=" . urlencode($search)
        . "&page=" . (int) $pageNumber;
}

$username = (string) ($_SESSION["username"] ?? "User");

// 12. Article action notifications
$actionMessage = "";
$actionType = "";

if (isset($_GET["created"]) && $_GET["created"] === "1") {

    $actionMessage = "Your article has been published successfully!";
    $actionType = "success";

} elseif (isset($_GET["updated"]) && $_GET["updated"] === "1") {

    $actionMessage = "Your article has been updated successfully!";
    $actionType = "success";

} elseif (isset($_GET["deleted"]) && $_GET["deleted"] === "1") {

    $actionMessage = "Article deleted successfully.";
    $actionType = "success";

} elseif (isset($_GET["deleted"]) && $_GET["deleted"] === "0") {

    $actionMessage = "The article could not be deleted. It may have already been removed or you may not own it.";
    $actionType = "error";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta
        name="description"
        content="Explore and manage articles on InkSpace, your space for ideas and stories."
    >

    <title>Explore Posts — InkSpace</title>

    <link rel="stylesheet" href="posts.css">
</head>

<body>

    <!-- Navigation -->
    <header class="posts-navbar">

        <div class="posts-nav-inner">

            <a href="../index.php" class="posts-brand">
                <span class="posts-brand-icon">i.</span>
                <span>ink<span class="brand-highlight">space</span></span>
            </a>

            <nav class="posts-nav-links" aria-label="Main navigation">
                <a href="../index.php">Home</a>
                <a href="index.php" class="current">Explore Posts</a>
            </nav>

            <div class="posts-nav-actions">

                <span class="user-greeting">
                    Hi, <?= e($username) ?>
                </span>

                <a href="create.php" class="posts-button posts-button-primary">
                    <span aria-hidden="true">+</span>
                    Write a Post
                </a>

                <a href="../auth/logout.php" class="posts-logout">
                    Logout
                </a>

            </div>

        </div>

    </header>

    <main>

        <!-- Dashboard Hero -->
        <section class="dashboard-hero">

            <div class="dashboard-hero-inner">

                <div class="dashboard-intro">

                    <span class="dashboard-eyebrow">
                        YOUR SPACE. YOUR STORIES.
                    </span>

                    <h1>
                        Ideas worth
                        <span>sharing.</span>
                    </h1>

                    <p>
                        Discover articles, revisit your ideas, and
                        keep the conversation going.
                    </p>

                </div>

                <div class="dashboard-stat">

                    <span class="stat-icon" aria-hidden="true">✦</span>

                    <div>
                        <strong><?= number_format($totalPosts) ?></strong>
                        <span>
                            <?= $totalPosts === 1 ? "Article" : "Articles" ?>
                            <?= $search !== "" ? "Found" : "Published" ?>
                        </span>
                    </div>

                </div>

            </div>

        </section>

        <!-- Posts Listing -->
        <section class="posts-section">

            <!-- Article Action Notification -->
            <?php if ($actionMessage !== ""): ?>

                <div
                    class="delete-notification <?= e($actionType) ?>"
                    role="<?= $actionType === "error" ? "alert" : "status" ?>"
                    aria-live="<?= $actionType === "error" ? "assertive" : "polite" ?>"
                >

                    <span class="notification-icon" aria-hidden="true">
                        <?= $actionType === "success" ? "✓" : "!" ?>
                    </span>

                    <span><?= e($actionMessage) ?></span>

                </div>

            <?php endif; ?>

            <div class="posts-content">

                <div class="section-top">

                    <div>
                        <span class="section-overline">THE INKSPACE LIBRARY</span>

                        <h2>
                            <?= $search !== "" ? "Search Results" : "Explore Articles" ?>
                        </h2>

                        <p>
                            <?= $search !== ""
                                ? "Articles matching your search."
                                : "Fresh ideas and stories from the community." ?>
                        </p>
                    </div>

                    <a href="create.php" class="posts-button posts-button-outline">
                        <span aria-hidden="true">+</span>
                        Create Article
                    </a>

                </div>

                <!-- Search -->
                <form method="GET" action="index.php" class="search-panel">

                    <div class="search-input-wrap">

                        <span class="search-icon" aria-hidden="true">⌕</span>

                        <label for="search" class="visually-hidden">
                            Search articles
                        </label>

                        <input
                            type="search"
                            id="search"
                            name="search"
                            maxlength="200"
                            placeholder="Search by article title or content..."
                            value="<?= e($search) ?>"
                        >

                        <?php if ($search !== ""): ?>

                            <a href="index.php" class="clear-search">
                                Clear
                            </a>

                        <?php endif; ?>

                    </div>

                    <button type="submit" class="posts-button posts-button-primary">
                        Search Articles
                    </button>

                </form>

                <?php if ($search !== ""): ?>

                    <div class="search-summary">
                        Showing results for
                        <strong><?= e($search) ?></strong>
                    </div>

                <?php endif; ?>

                <!-- Article Cards -->
                <?php if ($result->num_rows > 0): ?>

                    <div class="article-grid">

                        <?php while ($post = $result->fetch_assoc()): ?>

                            <?php

                            $postTitle = (string) $post["title"];
                            $postContent = (string) $post["content"];

                            $plainPreview = trim(
                                preg_replace(
                                    '/\s+/',
                                    ' ',
                                    strip_tags($postContent)
                                ) ?? ""
                            );

                            if (strlen($plainPreview) > 180) {
                                $plainPreview = substr($plainPreview, 0, 180) . "...";
                            }

                            $isOwner = (int) $post["user_id"] === $user_id;

                            ?>

                            <article class="article-card">

                                <div class="article-card-top">

                                    <span class="article-tag">
                                        <span class="tag-dot"></span>
                                        ARTICLE
                                    </span>

                                    <span class="article-id">
                                        #<?= (int) $post["id"] ?>
                                    </span>

                                </div>

                                <h3>
                                    <?= e($postTitle) ?>
                                </h3>

                                <p class="article-preview">

                                    <?php if ($plainPreview !== ""): ?>

                                        <?= e($plainPreview) ?>

                                    <?php else: ?>

                                        <span class="muted-text">
                                            This article doesn't contain a text preview.
                                        </span>

                                    <?php endif; ?>

                                </p>

                                <div class="article-meta">

                                    <div class="article-author-avatar">
                                        <?= e(strtoupper(substr($username, 0, 1))) ?>
                                    </div>

                                    <div class="article-date">

                                        <strong><?= e($username) ?></strong>

                                        <span>
                                            <?= e(formatPostDate($post["created_at"])) ?>
                                        </span>

                                    </div>

                                </div>

                                <!-- Only the owner can edit or delete the article -->
                                <?php if ($isOwner): ?>

                                    <div class="article-actions">

                                        <a
                                            class="action-link action-edit"
                                            href="edit.php?id=<?= (int) $post["id"] ?>"
                                        >
                                            <span aria-hidden="true">✎</span>
                                            Edit
                                        </a>

                                        <form
                                            method="POST"
                                            action="delete.php"
                                            class="delete-form"
                                            onsubmit="return confirm('Are you sure you want to delete this post? This action cannot be undone.');"
                                        >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int) $post["id"] ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= e($_SESSION["csrf_token"]) ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="action-link action-delete"
                                            >
                                                <span aria-hidden="true">⌫</span>
                                                Delete
                                            </button>

                                        </form>

                                    </div>

                                <?php endif; ?>

                            </article>

                        <?php endwhile; ?>

                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>

                        <nav class="pagination" aria-label="Article pages">

                            <?php if ($page > 1): ?>

                                <a
                                    class="page-button page-direction"
                                    href="<?= e(pageUrl($page - 1, $search)) ?>"
                                >
                                    &larr; Previous
                                </a>

                            <?php else: ?>

                                <span class="page-button page-direction disabled">
                                    &larr; Previous
                                </span>

                            <?php endif; ?>

                            <div class="page-numbers">

                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                                    <a
                                        href="<?= e(pageUrl($i, $search)) ?>"
                                        class="page-number <?= $i === $page ? 'active' : '' ?>"
                                        <?= $i === $page ? 'aria-current="page"' : '' ?>
                                    >
                                        <?= $i ?>
                                    </a>

                                <?php endfor; ?>

                            </div>

                            <?php if ($page < $totalPages): ?>

                                <a
                                    class="page-button page-direction"
                                    href="<?= e(pageUrl($page + 1, $search)) ?>"
                                >
                                    Next &rarr;
                                </a>

                            <?php else: ?>

                                <span class="page-button page-direction disabled">
                                    Next &rarr;
                                </span>

                            <?php endif; ?>

                        </nav>

                    <?php endif; ?>

                <?php else: ?>

                    <!-- Empty State -->
                    <div class="empty-state">

                        <div class="empty-icon" aria-hidden="true">
                            ✎
                        </div>

                        <?php if ($search !== ""): ?>

                            <h3>No matching articles</h3>

                            <p>
                                We couldn't find articles matching
                                <strong><?= e($search) ?></strong>.
                                Try another search term.
                            </p>

                            <a
                                href="index.php"
                                class="posts-button posts-button-outline"
                            >
                                Clear Search
                            </a>

                        <?php else: ?>

                            <h3>Your next story starts here.</h3>

                            <p>
                                There are no articles to display yet.
                                Create your first post and bring your ideas to life.
                            </p>

                            <a
                                href="create.php"
                                class="posts-button posts-button-primary"
                            >
                                + Create Your First Post
                            </a>

                        <?php endif; ?>

                    </div>

                <?php endif; ?>

                <div class="dashboard-bottom-note">
                    <span class="bottom-note-icon" aria-hidden="true">✓</span>
                    A space for ideas, built with care.
                </div>

            </div>

        </section>

    </main>

    <!-- Footer -->
    <footer class="posts-footer">

        <div class="posts-footer-inner">

            <a href="../index.php" class="posts-brand footer-logo">
                <span class="posts-brand-icon">i.</span>
                <span>ink<span class="brand-highlight">space</span></span>
            </a>

            <p>
                A place for ideas, stories, and new perspectives.
            </p>

            <span class="footer-copyright">
                &copy; <?= date("Y") ?> InkSpace · PHP &amp; MySQL
            </span>

        </div>

    </footer>

    <!-- Automatically dismiss notifications after four seconds -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const notification = document.querySelector(".delete-notification");

            if (!notification) {
                return;
            }

            window.setTimeout(function () {
                notification.classList.add("notification-hide");

                window.setTimeout(function () {
                    notification.remove();
                }, 400);
            }, 4000);
        });
    </script>

</body>
</html>

<?php

// 13. Close resources
$stmt->close();
$result->free();
$conn->close();

?>