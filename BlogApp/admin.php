<?php
include 'includes/header.php';
include 'includes/db.php';

// Only allow admin access
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

// Fetch stats
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_blogs = $conn->query("SELECT COUNT(*) as count FROM posts")->fetch_assoc()['count'];
$total_comments = $conn->query("SELECT COUNT(*) as count FROM comments")->fetch_assoc()['count'];

// Fetch all users
$users_result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");

// Fetch all blogs
$blogs_result = $conn->query("SELECT posts.id, posts.title, posts.created_at, users.username 
                              FROM posts JOIN users ON posts.user_id = users.id
                              ORDER BY posts.created_at DESC");
?>

<main>
    <section class="admin-dashboard">
        <h2>Admin Panel</h2>

        <!-- Dashboard Stats -->
        <div class="stats">
            <div class="stat-card">
                <h3>Total Users</h3>
                <p><?php echo $total_users; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Blogs</h3>
                <p><?php echo $total_blogs; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Comments</h3>
                <p><?php echo $total_comments; ?></p>
            </div>
        </div>

        <!-- Users Table -->
        <h3>All Users</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($user = $users_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo $user['role']; ?></td>
                        <td><?php echo date("d M Y", strtotime($user['created_at'])); ?></td>
                        <td>
                            <!-- Example actions: Delete or Block -->
                            <a href="delete-user.php?id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Blogs Table -->
        <h3>All Blogs</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($blog = $blogs_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $blog['id']; ?></td>
                        <td><?php echo htmlspecialchars($blog['title']); ?></td>
                        <td><?php echo htmlspecialchars($blog['username']); ?></td>
                        <td><?php echo date("d M Y", strtotime($blog['created_at'])); ?></td>
                        <td>
                            <a href="edit-blog.php?id=<?php echo $blog['id']; ?>">Edit</a> |
                            <a href="delete-blog.php?id=<?php echo $blog['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
