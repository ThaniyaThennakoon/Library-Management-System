<?php
session_start(); 

$conn = mysqli_connect("localhost", "root", "", "librarydb");

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $row['password'])) {
            
            
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['member_id'] = $row['member_id'];

            if ($row['role'] === 'admin') {
                header("Location: admin_dashboard.php"); 
                exit();
            } else {
                header("Location: user_dashboard.php"); 
                exit();
            }

        } else {
            
            echo "<script>
                alert('Invalid Username or Password!');
                window.location.href = 'login.html';
            </script>";
            exit();
        }
    } else {
        
        echo "<script>
            alert('Invalid Username or Password!');
            window.location.href = 'login.html';
        </script>";
        exit();
    }
}
?>