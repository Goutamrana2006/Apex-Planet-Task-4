<?php
session_start();

$isLoggedIn = isset($_SESSION["user_id"]);
$username = $isLoggedIn
    ? htmlspecialchars(
        (string) ($_SESSION["username"] ?? "User"),
        ENT_QUOTES | ENT_SUBSTITUTE,
        "UTF-8"
    )
    : "";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta
        name="description"
        content="A modern blogging platform to create, discover, and manage articles with a focus on usability and security."
    >

    <title>InkSpace — Your Ideas, Your Stories</title>

    <link rel="stylesheet" href="home.css">
</head>

<body>

    <!-- Navigation -->
    <header class="navbar">
        <div class="nav-container">

            <a href="index.php" class="brand">
                <span class="brand-icon">i.</span>
                <span>ink<span class="brand-highlight">space</span></span>
            </a>

            <nav class="nav-links" aria-label="Main navigation">
                <a href="index.php" class="nav-link active">Home</a>
                <a href="posts/index.php" class="nav-link">Explore Posts</a>
            </nav>

            <div class="nav-actions">

                <?php if ($isLoggedIn): ?>

                    <span class="welcome-user">
                        Hi, <?= $username ?>
                    </span>

                    <a href="posts/create.php" class="btn btn-primary btn-small">
                        Write a Post
                    </a>

                    <a href="auth/logout.php" class="logout-link">
                        Logout
                    </a>

                <?php else: ?>

                    <a href="auth/login.php" class="login-link">
                        Log in
                    </a>

                    <a href="auth/register.php" class="btn btn-primary btn-small">
                        Get Started <span aria-hidden="true">↗</span>
                    </a>

                <?php endif; ?>

            </div>

        </div>
    </header>


    <main>

        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-container">

                <div class="hero-content">

                    <div class="eyebrow">
                        <span class="status-dot"></span>
                        A SPACE FOR IDEAS THAT MATTER
                    </div>

                    <h1>
                        Every great idea
                        starts with a
                        <span class="gradient-text">story.</span>
                    </h1>

                    <p class="hero-description">
                        Discover fresh perspectives, share your knowledge,
                        and turn your thoughts into stories worth reading.
                        Your next great article starts here.
                    </p>

                    <div class="hero-actions">

                        <a href="posts/index.php" class="btn btn-primary btn-large">
                            Explore Articles
                            <span aria-hidden="true">→</span>
                        </a>

                        <?php if ($isLoggedIn): ?>

                            <a href="posts/create.php" class="btn btn-outline btn-large">
                                Start Writing
                            </a>

                        <?php else: ?>

                            <a href="auth/register.php" class="btn btn-outline btn-large">
                                Join the Community
                            </a>

                        <?php endif; ?>

                    </div>

                    <div class="hero-note">
                        <span class="note-icon">✦</span>
                        A simple, secure space for your ideas.
                    </div>

                </div>


                <!-- Editorial Preview Card -->
                <div class="hero-visual">

                    <div class="visual-glow"></div>

                    <div class="editor-card">

                        <div class="editor-topbar">
                            <div class="window-dots">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>

                            <span class="editor-label">YOUR NEXT STORY</span>
                        </div>

                        <div class="editor-body">

                            <div class="article-category">
                                <span class="category-mark"></span>
                                IDEAS &amp; CREATIVITY
                            </div>

                            <h2>
                                The art of
                                thinking differently.
                            </h2>

                            <p>
                                Great stories begin with curiosity.
                                Write about what inspires you,
                                what you've learned, and what
                                the world should know.
                            </p>

                            <div class="article-divider"></div>

                            <div class="editor-footer">

                                <div class="author-avatar">IS</div>

                                <div class="author-info">
                                    <strong>Your Name</strong>
                                    <span>Writer &amp; Creator</span>
                                </div>

                                <span class="read-time">✦ Your story</span>

                            </div>

                        </div>

                    </div>


                    <div class="floating-card floating-card-top">
                        <span class="floating-icon">✍</span>
                        <div>
                            <strong>Create freely</strong>
                            <span>Your voice matters</span>
                        </div>
                    </div>

                    <div class="floating-card floating-card-bottom">
                        <span class="secure-icon">✓</span>
                        <div>
                            <strong>Security first</strong>
                            <span>Built with care</span>
                        </div>
                    </div>

                </div>

            </div>

            <div class="hero-decoration decoration-one"></div>
            <div class="hero-decoration decoration-two"></div>

        </section>


        <!-- Feature Section -->
        <section class="features-section">

            <div class="section-container">

                <div class="section-heading">

                    <span class="section-kicker">WHAT YOU CAN DO</span>

                    <h2>
                        Everything you need
                        <span class="heading-muted">to tell your story.</span>
                    </h2>

                    <p>
                        A streamlined blogging experience built around
                        writing, discovery, and simplicity.
                    </p>

                </div>


                <div class="feature-grid">

                    <article class="feature-card">

                        <div class="feature-icon icon-blue">
                            <span>✎</span>
                        </div>

                        <span class="feature-number">01 / CREATE</span>

                        <h3>Write &amp; Create</h3>

                        <p>
                            Turn your ideas into articles with
                            straightforward tools for creating
                            and editing your posts.
                        </p>

                        <a href="posts/create.php" class="feature-link">
                            Create a post <span aria-hidden="true">→</span>
                        </a>

                    </article>


                    <article class="feature-card">

                        <div class="feature-icon icon-purple">
                            <span>⌕</span>
                        </div>

                        <span class="feature-number">02 / DISCOVER</span>

                        <h3>Search &amp; Explore</h3>

                        <p>
                            Find articles by title or content
                            and navigate posts through
                            organized pages.
                        </p>

                        <a href="posts/index.php" class="feature-link">
                            Explore articles <span aria-hidden="true">→</span>
                        </a>

                    </article>


                    <article class="feature-card">

                        <div class="feature-icon icon-green">
                            <span>✓</span>
                        </div>

                        <span class="feature-number">03 / PROTECT</span>

                        <h3>Security by Design</h3>

                        <p>
                            Experience a project built with
                            authentication, input validation,
                            and secure database practices.
                        </p>

                        <a href="auth/login.php" class="feature-link">
                            Access your account <span aria-hidden="true">→</span>
                        </a>

                    </article>

                </div>

            </div>

        </section>


        <!-- Call to Action -->
        <section class="cta-section">

            <div class="cta-container">

                <div>
                    <span class="section-kicker cta-kicker">
                        YOUR IDEAS DESERVE A PLACE
                    </span>

                    <h2>
                        Have a story to tell?
                        <br>
                        <span>Let's begin.</span>
                    </h2>

                    <p>
                        Take the first step. Create your next post
                        and share what's on your mind.
                    </p>
                </div>

                <div class="cta-actions">

                    <?php if ($isLoggedIn): ?>

                        <a href="posts/create.php" class="btn btn-white btn-large">
                            Write Your Story <span aria-hidden="true">→</span>
                        </a>

                    <?php else: ?>

                        <a href="auth/register.php" class="btn btn-white btn-large">
                            Create Your Account <span aria-hidden="true">→</span>
                        </a>

                        <a href="auth/login.php" class="cta-login">
                            Already a member? Log in
                        </a>

                    <?php endif; ?>

                </div>

                <div class="cta-orb"></div>

            </div>

        </section>

    </main>


    <!-- Footer -->
    <footer class="footer">

        <div class="footer-container">

            <a href="index.php" class="brand footer-brand">
                <span class="brand-icon">i.</span>
                <span>ink<span class="brand-highlight">space</span></span>
            </a>

            <p class="footer-description">
                A place for ideas, stories, and new perspectives.
            </p>

            <div class="footer-right">
                <span>Built with PHP &amp; MySQL</span>
                <span class="footer-dot">•</span>
                <span>© <?= date("Y") ?> InkSpace</span>
            </div>

        </div>

    </footer>

</body>
</html>