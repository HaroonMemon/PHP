<?php
include 'includes/header.php';
include 'includes/db.php';

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if(empty($email) || empty($password)){
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id, $username, $hashed_password, $role);

        if($stmt->num_rows == 0){
            $error = "No account found with this email.";
        } else {
            $stmt->fetch();
            if(password_verify($password, $hashed_password)){
                // Login successful
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;

                // Redirect admin to admin panel
                if($role == 'admin'){
                    header("Location: admin.php");
                } else {
                    header("Location: profile.php");
                }
                exit;
            } else {
                $error = "Incorrect password.";
            }
        }
        $stmt->close();
    }
}
?>

<main>
    <section class="login">
        <h2>Login</h2>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>

        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="signup.php">Sign up here</a>.</p>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
