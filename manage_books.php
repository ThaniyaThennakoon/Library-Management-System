<?php
$conn = mysqli_connect("localhost","root","","librarydb");

if(!$conn){
    die("Database Connection Failed");
}

// කලින් error එක ආපු නිසා මෙතන JOIN එකේ column එක ඔයාගේ table එකට අනුව (category_id හෝ id) නිවැරදිව තබා ගන්න
$sql = "SELECT books.id,
               books.title,
               books.author,
               books.quantity,
               categories.name AS category_name
        FROM books
        LEFT JOIN categories
        ON books.category_id = categories.category_id"; 

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books</title>
    <link rel="stylesheet" href="manage-books.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="page-container">

    <div class="form-card table-card">

        <!-- Top Header Bar -->
        <div class="form-header table-header-bar">
            <div class="header-left">
                <h1>Manage Books</h1>
                <p>View, search, edit, or remove books from the system.</p>
            </div>
            <a href="admin_dashboard.php" class="btn-exit dashboard-btn">
                <i class="fa-solid fa-arrow-left"></i> Dashboard
            </a>
        </div>

        <!-- Modern Search Box Container -->
        <div class="search-container">
            <div class="input-wrapper">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="searchInput" placeholder="Search books by title, author, or category...">
            </div>
        </div>

        <!-- Premium Book Table -->
        <div class="table-responsive">
            <table id="bookTable">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th style="text-align: center; width: 100px;">Quantity</th>
                        <th style="text-align: center; width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    while($row = mysqli_fetch_assoc($result)){
                    ?>
                    <tr>
                        <td><strong><?php echo $i++; ?></strong></td>
                        <td class="book-title-cell"><?php echo $row['title']; ?></td>
                        <td><?php echo $row['author']; ?></td>
                        <td><span class="category-badge"><?php echo $row['category_name']; ?></span></td>
                        <td style="text-align: center;">
                            <?php
                            if($row['quantity'] == 0){
                                echo "<span class='badge-out'>Out of Stock</span>";
                            }else{
                                echo "<span class='qty-count'>".$row['quantity']."</span>";
                            }
                            ?>
                        </td>
                        <td style="text-align: center;">
                            <div class="action-buttons">
                                <a class="btn-action edit-btn" href="edit_book.php?id=<?php echo $row['id']; ?>">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                <a class="btn-action delete-btn" href="delete_book.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this book?')">
                                    <i class="fa-solid fa-trash-can"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

<script>
// ඔයාගේ කේතයේ තිබුණු පට්ට Live Search JavaScript එක එහෙමම මෙතනට දැම්මා
const searchInput = document.getElementById("searchInput");
searchInput.addEventListener("keyup", function(){
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("#bookTable tbody tr");

    rows.forEach((row) => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});
</script>

</body>
</html>