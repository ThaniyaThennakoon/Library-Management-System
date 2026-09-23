<?php

$conn = mysqli_connect("localhost","root","","librarydb");

if(!$conn){
    die("Database Connection Failed");
}

/* GET BOOK ID */

$id = $_GET['id'];

/* DELETE QUERY */

$sql = "DELETE FROM books WHERE id='$id'";

$result = mysqli_query($conn,$sql);

if($result){

    header("Location: manage_books.php");
    exit();

}else{

    echo "Delete Failed";

}

?>