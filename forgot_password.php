<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "librarydb");

$error = "";

if (isset($_POST['find_account'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    
    $sql = "SELECT * FROM users WHERE username='$username' AND email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        
        $_SESSION['reset_user_id'] = $row['id'];
        
        header("Location: reset_password.php");
        exit();
    } else {
        $error = "Username or Email does not match our records!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LibraryMS - Forgot Password</title>
    
    <link rel="stylesheet" href="forgotpasswords.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="login-container">
    <div class="login-box">
        
        <div class="brand-logo">
            <i class="fas fa-bookmark"></i>
            <span>LibraryMS</span>
        </div>

        <h2>Forgot Password?</h2>
        <p class="subtitle">Enter your details to verify your account</p>

        <?php if (!empty($error)) { ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php } ?>

        <form method="POST">
            <!-- USERNAME -->
            <div class="input-box">
                <label><i class="fas fa-user"></i> Username</label>
                <input type="text" name="username" placeholder="Enter your username" required>
            </div>

            <!-- EMAIL -->
            <div class="input-box">
                <label><i class="far fa-envelope"></i> Registered Email</label>
                <input type="email" name="email" placeholder="Enter your registered email" required>
            </div>

            <button type="submit" name="find_account">Verify Account</button>
        </form>

        <p class="signup-link">
            Remembered your password? <a href="login.html">Login</a>
        </p>
    </div>
</div>

</body>
</html>