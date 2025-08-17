<?php
include 'includes/header.php';
include 'includes/db.php';

// Check if blog ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p>Invalid Blog ID.</p>";
    include 'includes/footer.php';
    exit;
}

$id = (int) $_GET['id']; // Sanitize the ID

// Fetch the blog post
$id_escaped = $conn->real_escape_string($id);
$result = $conn->query("
    SELECT posts.*, users.username 
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    WHERE posts.id = $id_escaped
");

// Check if post exists
if (!$result || $result->num_rows == 0) {
    echo "<p>Blog not found.</p>";
    include 'includes/footer.php';
    exit;
}

$row = $result->fetch_assoc();
?>

<main>
    <section class="single-blog">
        <h1><?php echo htmlspecialchars($row['title']); ?></h1>
        <p class="meta">By <?php echo htmlspecialchars($row['username']); ?> |
            <?php echo date("d M Y", strtotime($row['created_at'])); ?>
        </p>

        <?php if (!empty($row['image'])): ?>
            <img src="assets/images/<?php echo htmlspecialchars($row['image']); ?>"
                alt="<?php echo htmlspecialchars($row['title']); ?>" class="blog-image">
        <?php endif; ?>

        <div class="blog-content">
            <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
        </div>

        <!-- Comments Section -->
        <div class="comments">
            <h3>Comments</h3>

            <?php
            // Handle new comment
            if (isset($_POST['submit_comment'])) {
                if (!isset($_SESSION['user_id'])) {
                    echo "<p>Please <a href='login.php'>login</a> to comment.</p>";
                } else {
                    $comment_text = trim($_POST['comment']);
                    if (!empty($comment_text)) {
                        $post_id = (int) $id;
                        $user_id = (int) $_SESSION['user_id'];
                        $comment_text_escaped = $conn->real_escape_string($comment_text);

                        $insert_sql = "INSERT INTO comments (post_id, user_id, comment) 
                                       VALUES ($post_id, $user_id, '$comment_text_escaped')";
                        if ($conn->query($insert_sql)) {
                            echo "<p class='success'>Comment added successfully!</p>";
                        } else {
                            echo "<p class='error'>Failed to add comment. Error: " . $conn->error . "</p>";
                        }
                    } else {
                        echo "<p class='error'>Comment cannot be empty.</p>";
                    }
                }
            }

            // Fetch comments
            $comments_result = $conn->query("
                SELECT comments.*, users.username 
                FROM comments 
                JOIN users ON comments.user_id = users.id 
                WHERE post_id = $id_escaped 
                ORDER BY created_at DESC
            ");

            if ($comments_result && $comments_result->num_rows > 0) {
                while ($comment = $comments_result->fetch_assoc()) {
                    echo "<div class='comment'>";
                    echo "<p><strong>" . htmlspecialchars($comment['username']) . "</strong> | " . date("d M Y H:i", strtotime($comment['created_at'])) . "</p>";
                    echo "<p>" . nl2br(htmlspecialchars($comment['comment'])) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>No comments yet.</p>";
            }
            ?>

            <!-- Comment Form -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" action="">
                    <textarea name="comment" placeholder="Write your comment..." required></textarea>
                    <button type="submit" name="submit_comment">Add Comment</button>
                </form>
            <?php else: ?>
                <p>Please <a href="login.php">login</a> to add a comment.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>