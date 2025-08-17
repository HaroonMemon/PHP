<?php
include 'includes/header.php';
include 'includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        $email = $conn->real_escape_string($email);

        $result = $conn->query("SELECT id, username, password, role FROM users WHERE email='$email'");
        if ($result->num_rows == 0) {
            $error = "No account found with this email.";
        } else {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                // Login successful
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];

                if ($row['role'] == 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: profile.php");
                }
                exit;
            } else {
                $error = "Incorrect password.";
            }
        }
    }
}
?>

<main>
    <section class="login">
        <h2>Login</h2>
        <?php if ($error)
            echo "<p class='error'>$error</p>"; ?>

        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="signup.php">Sign up here</a>.</p>
    </section>
</main>

<?php include 'includes/footer.php'; ?>