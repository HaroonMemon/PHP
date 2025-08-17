<?php
include 'includes/header.php';
include 'includes/db.php'; // Database connection
?>

<main>
    <!-- Hero Section -->
    <section class="hero">
        <h1>Welcome to My Blog</h1>
        <p>Your daily dose of knowledge and insights!</p>
    </section>

    <!-- Latest Blogs Section -->
    <section class="latest-blogs">
        <h2>Latest Blogs</h2>
        <div class="blog-container">
            <?php
            // Fetch latest 5 blog posts
            $sql = "SELECT posts.id, posts.title, posts.content, posts.created_at, users.username 
                    FROM posts 
                    JOIN users ON posts.user_id = users.id 
                    ORDER BY posts.created_at DESC 
                    LIMIT 5";

            $result = $conn->query($sql);

            if ($result) {

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $excerpt = substr($row['content'], 0, 100) . "..."; // short preview
                        echo '
                    <div class="blog-card">
                        <h3>' . htmlspecialchars($row['title']) . '</h3>
                        <p>' . htmlspecialchars($excerpt) . '</p>
                        <p class="meta">By ' . htmlspecialchars($row['username']) . ' | ' . date("d M Y", strtotime($row['created_at'])) . '</p>
                        <a href="post.php?id=' . $row['id'] . '" class="read-more">Read More</a>
                    </div>
                    ';
                    }
                }
            } else {
                echo '<p>No blogs found.</p>';
            }
            ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>