<?php
session_start();


if (!isset($_SESSION['reset_user_id'])) {
    header("Location: forgot_password.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "librarydb");
$error = "";
$success = "";

if (isset($_POST['reset_password'])) {
    $user_id = $_SESSION['reset_user_id'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    /* VALIDATION */
    if ($new_password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (
        strlen($new_password) < 8 ||
        !preg_match("/[A-Z]/", $new_password) ||
        !preg_match("/[a-z]/", $new_password) ||
        !preg_match("/[0-9]/", $new_password)
    ) {
        $error = "Password must follow the security strength rules!";
    } else {
        
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        
        $sql = "UPDATE users SET password='$hashed_password' WHERE id='$user_id'";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $success = "Password Updated Successfully!";
            
            unset($_SESSION['reset_user_id']);
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Reset Password</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="resets_password.css"> 
</head>
<body>

<div class="login-container">
    <div class="login-box">
        
        <div class="brand-logo">
            <i class="fas fa-bookmark"></i>
            <span>LibraryMS</span>
        </div>

        <h2>Reset Password</h2>
        <p class="subtitle">Enter your new strong password</p>

        <?php if (!empty($error)) { ?>
            <div class="error-message" style="background-color: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; border: 1px solid #fca5a5; font-size: 14px; margin-bottom: 20px;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php } ?>

        <?php if (!empty($success)) { ?>
            <div class="success-message" style="background-color: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; border: 1px solid #a7f3d0; font-size: 14px; font-weight: 600; margin-bottom: 20px; text-align: center;">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?> <br>
                <a href="login.html" style="color: #4f46e5; text-decoration: underline; display: inline-block; margin-top: 10px;">Click here to Login</a>
            </div>
        <?php } ?>

        <?php if (empty($success)) { ?>
        <form method="POST">
            <!-- NEW PASSWORD -->
            <div class="input-box">
                <label><i class="fas fa-lock"></i> New Password</label>
                <div class="password-container" style="position: relative; display: flex; align-items: center;">
                    <input type="password" id="password" name="new_password" placeholder="Enter new password" required style="width: 100%;">
                    <span id="togglePassword" style="position: absolute; right: 14px; cursor: pointer; color: #94a3b8;"><i class="far fa-eye"></i></span>
                </div>
                
                <!-- STRENGTH BAR -->
                <div class="strength-container" style="background: #f1f5f9; height: 5px; margin-top: 6px; border-radius: 3px; overflow: hidden;">
                    <div id="strength-bar" style="height: 100%; width: 0%; transition: all 0.3s ease;"></div>
                </div>
                <small id="strength-text" style="font-size: 11px; margin-top: 4px; display: block; font-weight: 500;"></small>
            </div>

            <!-- CONFIRM PASSWORD -->
            <div class="input-box">
                <label><i class="fas fa-check-double"></i> Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Confirm new password" required>
            </div>

            <button type="submit" name="reset_password" id="registerButton">Update Password</button>
        </form>
        <?php } ?>
    </div>
</div>

<script>

const password = document.getElementById("password");
const strengthBar = document.getElementById("strength-bar");
const strengthText = document.getElementById("strength-text");
const registerButton = document.getElementById("registerButton");
const togglePassword = document.getElementById("togglePassword");

if(registerButton) {
    registerButton.disabled = true;
    registerButton.style.opacity = "0.5";
    registerButton.style.cursor = "not-allowed";

    password.addEventListener("keyup", function() {
        let value = password.value;
        let strength = 0;

        if (value.length >= 8) strength += 1;
        if (/[A-Z]/.test(value)) strength += 1;
        if (/[a-z]/.test(value)) strength += 1;
        if (/[0-9]/.test(value)) strength += 1;
        if (/[@$!%*?&]/.test(value)) strength += 1;

        let hasRequired = value.length >= 8 && /[A-Z]/.test(value) && /[a-z]/.test(value) && /[0-9]/.test(value);

        if (value.length === 0) {
            strengthBar.style.width = "0%";
            strengthText.innerHTML = "";
        }
        else if (strength <= 2) {
            strengthBar.style.width = "33%";
            strengthBar.style.background = "#ef4444";
            strengthText.innerHTML = "Weak Password";
            strengthText.style.color = "#ef4444";
        } 
        else if (strength <= 4 && !hasRequired) {
            strengthBar.style.width = "66%";
            strengthBar.style.background = "#f59e0b";
            strengthText.innerHTML = "Medium Password";
            strengthText.style.color = "#f59e0b";
        } 
        
        if (hasRequired) {
            strengthBar.style.width = strength === 5 ? "100%" : "80%";
            strengthBar.style.background = "#10b981";
            strengthText.innerHTML = strength === 5 ? "Strong Password ✨" : "Good Password";
            strengthText.style.color = "#10b981";

            registerButton.disabled = false;
            registerButton.style.opacity = "1";
            registerButton.style.cursor = "pointer";
        } else {
            registerButton.disabled = true;
            registerButton.style.opacity = "0.5";
            registerButton.style.cursor = "not-allowed";
        }
    });
}

togglePassword.addEventListener("click", function() {
    if (password.type === "password") {
        password.type = "text";
        togglePassword.innerHTML = '<i class="far fa-eye-slash"></i>';
    } else {
        password.type = "password";
        togglePassword.innerHTML = '<i class="far fa-eye"></i>';
    }
});
</script>

</body>
</html>