<?php
session_start();

// ආරක්ෂාව සඳහා ඇඩ්මින් ද කියා පරීක්ෂා කිරීම
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.html");
    exit();
}

$conn = mysqli_connect("localhost","root","","librarydb");

if(!$conn){
    die("Connection Failed");
}

/* Categories ටික Dropdown එකට ලබා ගැනීම */
$cat_result = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");

$message = "";
$message_class = "";

if(isset($_POST['add_book'])){
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);


    // පොත ඇතුළත් කිරීමේ Query එක
    $sql = "INSERT INTO books (title, author, category_id, quantity) 
            VALUES ('$title', '$author', '$category_id', '$quantity')";

    if(mysqli_query($conn, $sql)){
        $message = "Book Added Successfully!";
        $message_class = "success";
    } else {
        $message = "Failed to Add Book: " . mysqli_error($conn);
        $message_class = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Book</title>
    <link rel="stylesheet" href="add-books.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="page-container">

    <div class="form-card">
        
        <div class="form-header">
            <div class="icon-box"><i class="fa-solid fa-book-medical"></i></div>
            <h1>Add New Book</h1>
            <p>Enter the details below to add a new book to the catalog.</p>
        </div>

        <?php if(!empty($message)) { ?>
            <div class="alert-box <?php echo $message_class; ?>">
                <i class="fa-solid <?php echo ($message_class == 'success') ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>"></i>
                <span><?php echo $message; ?></span>
            </div>
        <?php } ?>

        <form method="POST">
            
            <div class="form-group">
                <label>Book Title <span class="req">*</span></label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-book"></i>
                    <input type="text" name="title" placeholder= "Book title"required>
                </div>
            </div>

            <div class="form-group">
                <label>Author Name <span class="req">*</span></label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-user-pen"></i>
                    <input type="text" name="author" placeholder= "Author name" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Category <span class="req">*</span></label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-tags"></i>
                        <select name="category_id" required>
                            <option value="" disabled selected>-- Select Category --</option>
                            <?php while($row = mysqli_fetch_assoc($cat_result)) { ?>
                                <option value="<?php echo $row['category_id']; ?>">
                                    <?php echo $row['name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Quantity <span class="req">*</span></label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-layer-group"></i>
                        <input type="number" name="quantity" min="1" placeholder="Copies" required>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="add_book" class="btn-submit">
                    <i class="fa-solid fa-plus"></i> Add Book
                </button>
                <a href="admin_dashboard.php" class="btn-exit">
                    <i class="fa-solid fa-arrow-left"></i> Exit to Dashboard
                </a>
            </div>

        </form>

    </div>

</div>

</body>
</html>