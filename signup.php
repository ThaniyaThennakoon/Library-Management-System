<?php
$conn = mysqli_connect("localhost", "root", "", "librarydb");

if (isset($_POST['signup'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $address   = mysqli_real_escape_string($conn, $_POST['address']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $username  = mysqli_real_escape_string($conn, $_POST['username']);
    $password  = $_POST['password'];

    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
            alert('Invalid email format! Please enter a valid email.');
            window.location.href = 'signup.php';
        </script>";
        exit();
    }

    
    $check_email = mysqli_query($conn, "SELECT email FROM users WHERE email='$email'");
    if (mysqli_num_rows($check_email) > 0) {
        echo "<script>
            alert('This email address is already registered!');
            window.location.href = 'signup.php';
        </script>";
        exit();
    }

    
    if (
        strlen($password) < 8 ||
        !preg_match("/[A-Z]/", $password) ||
        !preg_match("/[a-z]/", $password) ||
        !preg_match("/[0-9]/", $password)
    ) {
        echo "<script>
            alert('Password must contain at least 8 characters, one uppercase letter, one lowercase letter, and one number');
            window.location.href = 'signup.php';
        </script>";
        exit();
    } else {
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = "user";

        
        $sql = "INSERT INTO users (full_name, address, email, username, password, role) 
                VALUES ('$full_name', '$address', '$email', '$username', '$hashed_password', '$role')";

        $result = mysqli_query($conn, $sql);

        if ($result) {
            $last_id = mysqli_insert_id($conn);
            
            
            $member_id = "LIB" . date("Y") . str_pad($last_id, 3, "0", STR_PAD_LEFT);
            
            
            mysqli_query($conn, "UPDATE users SET member_id='$member_id' WHERE id='$last_id'");

            echo "<script>
                alert('Account Created Successfully!\\nYour Member ID is: " . $member_id . "\\nPlease login to continue.');
                window.location.href = 'login.html';
            </script>";
            exit();
        } else {
            echo "<script>
                alert('Registration Failed. Username might already exist.');
                window.location.href = 'signup.php';
            </script>";
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="signups.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="signup-container">
    <div class="signup-box">
        
        <div class="brand-logo">
            <i class="fas fa-bookmark"></i>
            <span>LibraryMS</span>
        </div>

        <h2>Create Account</h2>
        <p class="subtitle">Please fill in your details to register</p>

        <form method="POST">
            <div class="input-box">
                <label><i class="far fa-user"></i> Full Name</label>
                <input type="text" name="full_name" placeholder="Enter Full Name" required>
            </div>

            <div class="input-box">
                <label><i class="fas fa-map-marker-alt"></i> Address</label>
                <input type="text" name="address" placeholder="Enter Address" required>
            </div>

            <div class="input-box">
                <label><i class="far fa-envelope"></i> Email</label>
                <input type="email" id="email" name="email" placeholder="Enter Email" required>
                <small id="email-text" style="display: block; margin-top: 5px; font-weight: 500;"></small>
            </div>

            <div class="input-box">
                <label><i class="fas fa-user-tag"></i> Username</label>
                <input type="text" name="username" placeholder="Enter Username" required>
            </div>

            <div class="input-box">
                <label><i class="fas fa-lock"></i> Password</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="Enter Password" required>
                    <span id="togglePassword"><i class="far fa-eye"></i></span>
                </div>
                
                <div class="strength-container">
                    <div id="strength-bar"></div>
                </div>
                <small id="strength-text"></small>
            </div>

            <button type="submit" name="signup" id="registerButton">Register</button>
        </form>

        <p class="login-link">
            Already have an account? <a href="login.html">Login</a>
        </p>
    </div>
</div>

<script>
const password = document.getElementById("password");
const email = document.getElementById("email");
const emailText = document.getElementById("email-text");
const strengthBar = document.getElementById("strength-bar");
const strengthText = document.getElementById("strength-text");
const registerButton = document.getElementById("registerButton");
const togglePassword = document.getElementById("togglePassword");

let isPasswordValid = false;
let isEmailValid = false;

function toggleRegisterButton() {
    if (isPasswordValid && isEmailValid) {
        registerButton.disabled = false;
        registerButton.style.opacity = "1";
        registerButton.style.cursor = "pointer";
    } else {
        registerButton.disabled = true;
        registerButton.style.opacity = "0.5";
        registerButton.style.cursor = "not-allowed";
    }
}

toggleRegisterButton();


email.addEventListener("input", function() {
    let emailValue = email.value;
    let emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

    if (emailValue.length === 0) {
        emailText.innerHTML = "";
        isEmailValid = false;
    } else if (emailPattern.test(emailValue)) {
        emailText.innerHTML = "Valid Email Address ✓";
        emailText.style.color = "#10b981";
        isEmailValid = true;
    } else {
        emailText.innerHTML = "Invalid email format (e.g. name@example.com)";
        emailText.style.color = "#ef4444";
        isEmailValid = false;
    }
    toggleRegisterButton();
});


password.addEventListener("input", function() {
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
        isPasswordValid = false;
    }
    else if (strength <= 2) {
        strengthBar.style.width = "33%";
        strengthBar.style.background = "#ef4444";
        strengthText.innerHTML = "Weak Password";
        strengthText.style.color = "#ef4444";
        isPasswordValid = false;
    } 
    else if (strength <= 4 && !hasRequired) {
        strengthBar.style.width = "66%";
        strengthBar.style.background = "#f59e0b";
        strengthText.innerHTML = "Medium (Include Capital, Simple & Numbers)";
        strengthText.style.color = "#f59e0b";
        isPasswordValid = false;
    } 
    
    if (hasRequired) {
        strengthBar.style.width = strength === 5 ? "100%" : "80%";
        strengthBar.style.background = "#10b981";
        strengthText.innerHTML = strength === 5 ? "Strong Password ✨" : "Good Password";
        strengthText.style.color = "#10b981";
        isPasswordValid = true;
    }
    toggleRegisterButton();
});


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