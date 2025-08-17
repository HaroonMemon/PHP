<?php
include 'includes/header.php';
include 'includes/db.php';

// Only logged-in users
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Get blog ID
if(!isset($_GET['id']) || empty($_GET['id'])){
    echo "<p>Invalid Blog ID.</p>";
    include 'includes/footer.php';
    exit;
}

$blog_id = (int)$_GET['id'];

// Fetch blog and verify ownership
$stmt = $conn->prepare("SELECT title, content, image FROM posts WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $blog_id, $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($title, $content, $image_name);
if($stmt->num_rows == 0){
    echo "<p>Blog not found or you don't have permission to edit it.</p>";
    include 'includes/footer.php';
    exit;
}
$stmt->fetch();
$stmt->close();

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $new_title = trim($_POST['title']);
    $new_content = trim($_POST['content']);
    $new_image = $image_name; // keep old image by default

    if(empty($new_title) || empty($new_content)){
        $error = "Title and content cannot be empty.";
    } else {
        // Handle image upload
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
            $allowed_ext = ['jpg','jpeg','png','gif'];
            $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            if(in_array(strtolower($file_ext), $allowed_ext)){
                $new_image = time().'_'.basename($_FILES['image']['name']);
                move_uploaded_file($_FILES['image']['tmp_name'], "assets/images/".$new_image);
            } else {
                $error = "Invalid image type. Only JPG, JPEG, PNG, GIF allowed.";
            }
        }

        if(empty($error)){
            $stmt = $conn->prepare("UPDATE posts SET title=?, content=?, image=? WHERE id=? AND user_id=?");
            $stmt->bind_param("sssii", $new_title, $new_content, $new_image, $blog_id, $user_id);
            if($stmt->execute()){
                $success = "Blog updated successfully!";
                $title = $new_title;
                $content = $new_content;
                $image_name = $new_image;
            } else {
                $error = "Failed to update blog. Try again.";
            }
        }
    }
}
?>

<main>
    <section class="edit-blog">
        <h2>Edit Blog</h2>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>
        <?php if($success) echo "<p class='success'>$success</p>"; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" placeholder="Blog Title" required>
            <textarea name="content" rows="8" placeholder="Blog Content" required><?php echo htmlspecialchars($content); ?></textarea>
            
            <?php if(!empty($image_name)): ?>
                <p>Current Image:</p>
                <img src="assets/images/<?php echo $image_name; ?>" alt="Blog Image" style="max-width:200px; margin-bottom:1rem;">
            <?php endif; ?>

            <input type="file" name="image" accept="image/*">
            <button type="submit">Update Blog</button>
        </form>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
