<?php
include 'includes/header.php';
include 'includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Escape inputs
        $username = $conn->real_escape_string($username);
        $email = $conn->real_escape_string($email);

        // Check if email exists
        $check = $conn->query("SELECT id FROM users WHERE email='$email'");
        if ($check && $check->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $insert = $conn->query("INSERT INTO users (username, email, password, role, created_at) VALUES ('$username', '$email', '$hashed_password', 'user', NOW())");
            if ($insert) {

                $success = "Signup successful! You can now <a href='login.php'>login</a>.";
            } else {
                $error = "Signup failed. Please try again.";
            }
        }
    }
}
?>

<main>
    <section class="signup">
        <h2>Sign Up</h2>
        <?php if ($error)
            echo "<p class='error'>$error</p>"; ?>
        <?php if ($success)
            echo "<p class='success'>$success</p>"; ?>

        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Sign Up</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </section>
</main>

<?php include 'includes/footer.php'; ?>