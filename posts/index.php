<?php

session_start();

require_once "../config/database.php";

// 1. Authentication check
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

// 2. Generate CSRF token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// 3. Search input validation
$search = "";

if (isset($_GET["search"]) && is_string($_GET["search"])) {
    $search = trim($_GET["search"]);
}

// Limit search length
if (strlen($search) > 200) {
    $search = substr($search, 0, 200);
}

// 4. Pagination validation
$postsPerPage = 5;

$page = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);

if ($page === false || $page === null || $page < 1) {
    $page = 1;
}

// 5. Count posts
if ($search !== "") {
    $searchTerm = "%" . $search . "%";

    $countStmt = $conn->prepare(
        "SELECT COUNT(*) AS total
         FROM posts
         WHERE title LIKE ? OR content LIKE ?"
    );

    $countStmt->bind_param(
        "ss",
        $searchTerm,
        $searchTerm
    );

    $countStmt->execute();

    $countResult = $countStmt->get_result();
    $totalPosts = (int) $countResult->fetch_assoc()["total"];

    $countStmt->close();

} else {

    $countResult = $conn->query(
        "SELECT COUNT(*) AS total FROM posts"
    );

    $totalPosts = (int) $countResult->fetch_assoc()["total"];
}

// 6. Calculate total pages
$totalPages = (int) ceil($totalPosts / $postsPerPage);

// Keep requested page within valid range
if ($totalPages > 0 && $page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $postsPerPage;

// 7. Fetch posts using prepared statements
if ($search !== "") {

    $stmt = $conn->prepare(
        "SELECT id, title, content, created_at
         FROM posts
         WHERE title LIKE ? OR content LIKE ?
         ORDER BY created_at DESC, id DESC
         LIMIT ? OFFSET ?"
    );

    $stmt->bind_param(
        "ssii",
        $searchTerm,
        $searchTerm,
        $postsPerPage,
        $offset
    );

} else {

    $stmt = $conn->prepare(
        "SELECT id, title, content, created_at
         FROM posts
         ORDER BY created_at DESC, id DESC
         LIMIT ? OFFSET ?"
    );

    $stmt->bind_param(
        "ii",
        $postsPerPage,
        $offset
    );
}

$stmt->execute();
$result = $stmt->get_result();

// Helper for safely displaying HTML text
function e($value) {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        "UTF-8"
    );
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Posts - ApexPlanet Task 4</title>

    <link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="container">

    <h1>My Posts</h1>

    <p>
        Welcome,
        <strong>
            <?php echo e($_SESSION["username"] ?? "User"); ?>
        </strong>
    </p>

    <!-- Navigation -->
    <nav>
        <a href="create.php">Create New Post</a> |
        <a href="../auth/logout.php">Logout</a>
    </nav>

    <hr>

    <!-- Search Form -->
    <form method="GET" action="index.php">

        <label for="search">Search Posts</label>

        <input
            type="text"
            id="search"
            name="search"
            maxlength="200"
            placeholder="Search by title or content..."
            value="<?php echo e($search); ?>"
        >

        <button type="submit">Search</button>

        <?php if ($search !== ""): ?>
            <a href="index.php">Clear Search</a>
        <?php endif; ?>

    </form>

    <hr>

    <!-- Posts -->
    <?php if ($result->num_rows > 0): ?>

        <?php while ($post = $result->fetch_assoc()): ?>

            <article>

                <h3>
                    <?php echo e($post["title"]); ?>
                </h3>

                <p>
                    <?php
                    echo nl2br(e($post["content"]));
                    ?>
                </p>

                <small>
                    Created: <?php echo e($post["created_at"]); ?>
                </small>

                <br><br>

                <a href="edit.php?id=<?php echo (int) $post["id"]; ?>">
                    Edit
                </a>

                |

                <!-- CSRF-protected Delete Form -->
                <form
                    method="POST"
                    action="delete.php"
                    onsubmit="return confirm('Are you sure you want to delete this post?');"
                >

                    <input
                        type="hidden"
                        name="id"
                        value="<?php echo (int) $post["id"]; ?>"
                    >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?php echo e($_SESSION["csrf_token"]); ?>"
                    >

                    <button type="submit">Delete</button>

                </form>

            </article>

            <hr>

        <?php endwhile; ?>

    <?php else: ?>

        <p>
            <?php if ($search !== ""): ?>

                No posts found for:
                <strong><?php echo e($search); ?></strong>

            <?php else: ?>

                No posts available.

            <?php endif; ?>
        </p>

    <?php endif; ?>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>

        <div class="pagination">

            <?php if ($page > 1): ?>

                <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page - 1; ?>">
                    &laquo; Previous
                </a>

            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                <a
                    href="?search=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>"
                    class="<?php echo ($i === $page) ? 'active' : ''; ?>"
                    <?php echo ($i === $page) ? 'aria-current="page"' : ''; ?>
                >
                    <?php echo $i; ?>
                </a>

            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>

                <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page + 1; ?>">
                    Next &raquo;
                </a>

            <?php endif; ?>

        </div>

    <?php endif; ?>

</div>

</body>
</html>

<?php
// 8. Close database resources
$stmt->close();
$conn->close();
?>
