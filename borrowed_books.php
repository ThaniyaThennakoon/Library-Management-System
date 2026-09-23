<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

date_default_timezone_set("Asia/Colombo");
$conn = mysqli_connect("localhost", "root", "", "librarydb");

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

$logged_user_id = intval($_SESSION['user_id']); 

// SQL Query: status එක පරීක්ෂා කර දත්ත ලබා ගැනීම
$borrow_sql = "SELECT ib.*, books.title, books.author 
               FROM issued_books ib
               JOIN books ON ib.book_id = books.id 
               WHERE ib.user_id = ? 
               ORDER BY ib.id DESC";

$stmt = mysqli_prepare($conn, $borrow_sql);
mysqli_stmt_bind_param($stmt, "i", $logged_user_id);
mysqli_stmt_execute($stmt);
$borrow_result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Borrows</title>
    <link rel="stylesheet" href="borrowedbooks.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<nav class="portal-navbar">
    <div class="nav-container">
        <div class="nav-logo">
            <i class="fa-solid fa-book-open"></i>
            <span>Lib<span style="color: #818cf8;">Sphere</span></span>
        </div>
        <div class="nav-links">
            <a href="search_books.php" class="nav-item"><i class="fa-solid fa-magnifying-glass"></i> Search Catalog</a>
            <a href="borrowed_books.php" class="nav-item active"><i class="fa-solid fa-book-reader"></i> My Borrows</a>
            <a href="user_reservation.php" class="nav-item"><i class="fa-solid fa-bookmark"></i> My Reserves</a>
            <a href="my_fines.php" class="nav-item"><i class="fa-solid fa-wallet"></i> My Fines</a>
            <a href="user_dashboard.php" class="nav-item"><i class="fa-solid fa-chart-pie"></i> My Dashboard</a>
        </div>
    </div>
</nav>

<div class="catalog-container">
    <div class="catalog-header" style="text-align: left; align-items: flex-start; margin-bottom: 30px;">
        <h1>My Borrowed Books</h1>
        <p class="subtitle">View your currently issued books, check due dates, and monitor your overdue library logs.</p>
    </div>

    <div class="dashboard-card">
        <h3>Active Borrowed Logs</h3>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Book Details</th>
                        <th>Issued Date</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Action Required</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(mysqli_num_rows($borrow_result) > 0) {
                        while($row = mysqli_fetch_assoc($borrow_result)) {
                            $status_text = $row['status'];
                            $status_class = strtolower($status_text);
                            
                            $issue_date_display = "-";
                            $due_date_display = "-";
                            $is_overdue = false;

                            if(strcasecmp($status_text, 'Pending') == 0) {
                                $issue_date_display = "Waiting for Admin";
                                $due_date_display = "Not Issued Yet";
                            } 
                            else if(strcasecmp($status_text, 'Issued') == 0) {
                                // Database තීරු නම් නිවැරදි කිරීම: issue_date සහ return_date
                                $db_issued_date = isset($row['issue_date']) ? $row['issue_date'] : null;
                                $db_return_date = isset($row['return_date']) ? $row['return_date'] : null;

                                $issue_date_display = !empty($db_issued_date) ? date('M d, Y', strtotime($db_issued_date)) : "-";
                                $due_date_display = !empty($db_return_date) ? date('M d, Y', strtotime($db_return_date)) : "-";
                                
                                if(!empty($db_return_date)) {
                                    $today = date('Y-m-d');
                                    $due_date_db = date('Y-m-d', strtotime($db_return_date));
                                    if($today > $due_date_db) {
                                        $status_class = "overdue";
                                        $status_text = "Overdue";
                                        $is_overdue = true;
                                    }
                                }
                            } 
                            else if(strcasecmp($status_text, 'Returned') == 0) {
                                $db_issued_date = isset($row['issue_date']) ? $row['issue_date'] : null;
                                $issue_date_display = !empty($db_issued_date) ? date('M d, Y', strtotime($db_issued_date)) : "-";
                                $due_date_display = "Returned Successfully";
                            }
                    ?>
                        <tr>
                            <td>
                                <div class="tbl-book-title"><?php echo htmlspecialchars($row['title']); ?></div>
                                <div class="tbl-book-author">by <?php echo htmlspecialchars($row['author']); ?></div>
                            </td>
                            <td><?php echo $issue_date_display; ?></td>
                            <td>
                                <span class="<?php echo ($is_overdue) ? 'text-danger-bold' : ''; ?>">
                                    <?php echo $due_date_display; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge-status <?php echo $status_class; ?>"><?php echo htmlspecialchars($status_text); ?></span>
                            </td>
                            <td>
                                <?php if($is_overdue) { ?>
                                    <a href="my_fines.php" class="fine-badge fine-active">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Pay Fine
                                    </a>
                                <?php } else if(strcasecmp($status_text, 'Pending') == 0) { ?>
                                    <span class="fine-badge fine-none" style="background: #f1f5f9; color: #64748b;">Requested</span>
                                <?php } else { ?>
                                    <span class="fine-badge fine-none">No Action</span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center; color:#64748b; padding: 40px;'><i class='fa-solid fa-book' style='font-size:24px; display:block; margin-bottom:10px;'></i>You haven't borrowed any books yet.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>