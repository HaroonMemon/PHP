<?php
include 'includes/header.php';
include 'includes/db.php';

// Fetch all blogs
$sql = "SELECT posts.id, posts.title, posts.content, posts.image, posts.created_at, users.username 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        ORDER BY posts.created_at DESC";
$result = $conn->query($sql);
?>

<main>
    <section class="all-blogs">
        <h2>All Blogs</h2>

        <?php if($result->num_rows > 0): ?>
            <div class="blogs-grid">
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="blog-card">
                        <?php if(!empty($row['image'])): ?>
                            <img src="assets/images/<?php echo $row['image']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p class="meta">By <?php echo htmlspecialchars($row['username']); ?> | <?php echo date("d M Y", strtotime($row['created_at'])); ?></p>
                        <p><?php echo nl2br(substr(htmlspecialchars($row['content']), 0, 150)); ?>...</p>
                        <a href="post.php?id=<?php echo $row['id']; ?>" class="read-more">Read More</a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No blogs found.</p>
        <?php endif; ?>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
