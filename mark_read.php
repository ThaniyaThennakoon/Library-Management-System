<?php
session_start();
if(isset($_SESSION['user_id'])){
    $conn = mysqli_connect("localhost", "root", "", "librarydb");
    $user_id = $_SESSION['user_id'];
    
    // යූසර්ගේ හැම Unread මැසේජ් එකක්ම 'Read' බවට පත් කිරීම
    mysqli_query($conn, "UPDATE notifications SET status='Read' WHERE user_id='$user_id' AND status='Unread'");
}
?>