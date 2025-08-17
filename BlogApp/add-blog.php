<?php
include 'includes/header.php'; 
include 'includes/db.php'; 

// Only logged-in users can access
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $image_name = '';

    // Validate title and content
    if(empty($title) || empty($content)){
        $error = "Title and content are required.";
    } else {
        // Handle image upload
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
            $allowed_ext = ['jpg','jpeg','png','gif'];
            $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            if(in_array(strtolower($file_ext), $allowed_ext)){
                $image_name = time().'_'.basename($_FILES['image']['name']);
                move_uploaded_file($_FILES['image']['tmp_name'], "assets/images/".$image_name);
            } else {
                $error = "Invalid image type. Only JPG, JPEG, PNG, GIF allowed.";
            }
        }

        if(empty($error)){
            // Insert blog into database
            $stmt = $conn->prepare("INSERT INTO posts (title, content, image, user_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $title, $content, $image_name, $user_id);
            if($stmt->execute()){
                $success = "Blog added successfully!";
            } else {
                $error = "Failed to add blog. Try again.";
            }
        }
    }
}
?>

<main>
    <section class="add-blog">
        <h2>Add New Blog</h2>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>
        <?php if($success) echo "<p class='success'>$success</p>"; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Blog Title" required>
            <textarea name="content" rows="8" placeholder="Blog Content" required></textarea>
            <input type="file" name="image" accept="image/*">
            <button type="submit">Add Blog</button>
        </form>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
