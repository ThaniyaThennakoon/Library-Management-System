<?php
$conn = mysqli_connect("localhost","root","","librarydb");

if(!$conn){
    die("Connection Failed");
}

/* GET ID */
if(isset($_GET['id'])){
    $id = mysqli_real_escape_string($conn, $_GET['id']);
} else {
    header("Location: manage_books.php");
    exit();
}

/* FETCH BOOK */
$sql = "SELECT * FROM books WHERE id='$id'";
$result = mysqli_query($conn,$sql);
$row = mysqli_fetch_assoc($result);

/* FETCH CATEGORIES */
$cat_sql = "SELECT * FROM categories ORDER BY name ASC";
$cat_result = mysqli_query($conn,$cat_sql);

/* UPDATE BOOK */
if(isset($_POST['update'])){

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']); // category_id ලෙස වෙනස් කළා
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);

    // Database එකේ column එක category_id නිසා එය නිවැරදි කළා
    $update = "UPDATE books SET 
                title='$title',
                author='$author',
                category_id='$category_id', 
                quantity='$quantity'
                WHERE id='$id'";

    if(mysqli_query($conn,$update)){
        echo "<script>
                alert('Book Updated Successfully');
                window.location='manage_books.php';
              </script>";
    } else {
        echo "Error updating book: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <!-- අලුත් CSS එක ලින්ක් කිරීම -->
    <link rel="stylesheet" href="edit-book.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="page-container">

    <div class="form-card">
        
        <!-- Form Header -->
        <div class="form-header">
            <div class="icon-box"><i class="fa-solid fa-book-open-reader"></i></div>
            <h1>Edit Book Details</h1>
            <p>Modify the fields below to update the book details in the catalog.</p>
        </div>

        <!-- Main Form -->
        <form method="POST">
            
            <div class="form-group">
                <label>Book Title <span class="req">*</span></label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-book"></i>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($row['title']); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Author Name <span class="req">*</span></label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-user-pen"></i>
                    <input type="text" name="author" value="<?php echo htmlspecialchars($row['author']); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Category <span class="req">*</span></label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-tags"></i>
                        <select name="category_id" required>
                            <option value="">-- Select Category --</option>
                            <?php while($cat = mysqli_fetch_assoc($cat_result)) { ?>
                                <!-- ID එක value එකටත්, name එක display එකටත් යෙදුවා -->
                                <option value="<?php echo $cat['category_id']; ?>"
                                    <?php if($row['category_id'] == $cat['category_id']) echo "selected"; ?>>
                                    <?php echo $cat['name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Quantity <span class="req">*</span></label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-layer-group"></i>
                        <input type="number" name="quantity" min="0" value="<?php echo $row['quantity']; ?>" required>
                    </div>
                </div>
            </div>

            <!-- Form Buttons -->
            <div class="form-actions">
                <button type="submit" name="update" class="btn-submit">
                    <i class="fa-solid fa-floppy-disk"></i> Update Book
                </button>
                <a href="manage_books.php" class="btn-exit">
                    <i class="fa-solid fa-xmark"></i> Cancel
                </a>
            </div>

        </form>

    </div>

</div>

</body>
</html>