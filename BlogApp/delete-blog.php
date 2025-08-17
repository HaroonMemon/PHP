<?php
include 'includes/header.php';
include 'includes/db.php';

// Only logged-in users
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get blog ID
if(!isset($_GET['id']) || empty($_GET['id'])){
    echo "<p>Invalid Blog ID.</p>";
    include 'includes/footer.php';
    exit;
}

$blog_id = (int)$_GET['id'];

// Verify ownership
$stmt = $conn->prepare("SELECT image FROM posts WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $blog_id, $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($image_name);

if($stmt->num_rows == 0){
    echo "<p>Blog not found or you don't have permission to delete it.</p>";
    include 'includes/footer.php';
    exit;
}

$stmt->fetch();
$stmt->close();

// Delete blog image if exists
if(!empty($image_name) && file_exists("assets/images/".$image_name)){
    unlink("assets/images/".$image_name);
}

// Delete blog from database
$stmt = $conn->prepare("DELETE FROM posts WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $blog_id, $user_id);
if($stmt->execute()){
    header("Location: profile.php?msg=deleted");
    exit;
} else {
    echo "<p>Failed to delete blog. Try again.</p>";
}

include 'includes/footer.php';
?>
