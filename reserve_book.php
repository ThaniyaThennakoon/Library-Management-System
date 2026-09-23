<?php
session_start();

// Database Connection
$conn = mysqli_connect("localhost", "root", "", "librarydb");


if (!isset($_SESSION['user_id'])) {
    echo "<script>
    alert('Please login first!');
    window.location='login.php';
    </script>";
    exit();
}

$user_id = $_SESSION['user_id'];


$book_id = mysqli_real_escape_string($conn, $_GET['book_id']);


$date = date("Y-m-d H:i:s");


$check = mysqli_query($conn, "SELECT * FROM reservations 
                              WHERE user_id='$user_id' 
                              AND book_id='$book_id' 
                              AND status IN ('Pending', 'Approved', 'Ready')");

if (mysqli_num_rows($check) > 0) {
    echo "<script>
    alert('You have already reserved this book');
    window.location='search_books.php';
    </script>";
    exit();
}


$insert_query = "INSERT INTO reservations (user_id, book_id, reserve_date, status) 
                 VALUES ('$user_id', '$book_id', '$date', 'Pending')";

if (mysqli_query($conn, $insert_query)) {
    echo "<script>
    alert('Book Reserved Successfully');
    window.location='user_reservation.php';
    </script>";
} else {
    
    echo "Error: " . mysqli_error($conn);
}
?>