<?php 
include 'includes/header.php'; 
include 'includes/db.php'; 

// Only logged-in users can access
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();

$success = "";
$error = "";

// Handle profile update
if(isset($_POST['update_profile'])){
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);
    $new_password = $_POST['password']; // optional

    if(empty($new_username) || empty($new_email)){
        $error = "Username and Email cannot be empty.";
    } else {
        // Check if email is already used by another user
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email=? AND id!=?");
        $stmt_check->bind_param("si", $new_email, $user_id);
        $stmt_check->execute();
        $stmt_check->store_result();

        if($stmt_check->num_rows > 0){
            $error = "Email is already taken by another user.";
        } else {
            if(!empty($new_password)){
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt_update = $conn->prepare("UPDATE users SET username=?, email=?, password=? WHERE id=?");
                $stmt_update->bind_param("sssi", $new_username, $new_email, $hashed_password, $user_id);
            } else {
                $stmt_update = $conn->prepare("UPDATE users SET username=?, email=? WHERE id=?");
                $stmt_update->bind_param("ssi", $new_username, $new_email, $user_id);
            }

            if($stmt_update->execute()){
                $success = "Profile updated successfully!";
                $_SESSION['username'] = $new_username;
                $username = $new_username;
                $email = $new_email;
            } else {
                $error = "Failed to update profile.";
            }
        }
        $stmt_check->close();
    }
}

// Fetch user's blogs
$blogs_sql = "SELECT id, title, created_at FROM posts WHERE user_id=$user_id ORDER BY created_at DESC";
$blogs_result = $conn->query($blogs_sql);
?>

<main>
    <section class="profile">
        <h2>My Profile</h2>

        <?php if($success) echo "<p class='success'>$success</p>"; ?>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>

        <!-- Profile Edit Form -->
        <form method="POST" action="">
            <label>Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

            <label>Password (leave blank to keep current):</label>
            <input type="password" name="password">

            <button type="submit" name="update_profile">Update Profile</button>
        </form>

        <!-- Add Blog Button (Always Visible) -->
        <div style="margin: 1.5rem 0;">
            <a href="add-blog.php" class="add-blog" style="padding: 0.5rem 1rem; display: inline-block;">Add New Blog</a>
        </div>

        <h3>My Blogs</h3>
        <?php if($blogs_result->num_rows > 0): ?>
            <ul class="user-blogs">
                <?php while($row = $blogs_result->fetch_assoc()): ?>
                    <li>
                        <?php echo htmlspecialchars($row['title']); ?> 
                        (<?php echo date("d M Y", strtotime($row['created_at'])); ?>) - 
                        <a href="edit-blog.php?id=<?php echo $row['id']; ?>">Edit</a> | 
                        <a href="delete-blog.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>You haven't posted any blogs yet.</p>
        <?php endif; ?>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
