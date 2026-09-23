<?php
session_start();

// 1. ආරක්ෂාව සඳහා Session පරීක්ෂාව
if(!isset($_SESSION['user_id'])){
    header("Location: login.html");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "librarydb");
if (!$conn) {
    die("Database Connection Failed");
}

$user_id = $_SESSION['user_id'];
$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : "User";

// දැනට ගෙවීමට ඇති මුළු දඩ මුදල සෙවීම
$fine_query = mysqli_query($conn, "SELECT SUM(fine_amount) AS total_fine FROM fines WHERE user_id='$user_id' AND status='Unpaid'");
$fine_row = mysqli_fetch_assoc($fine_query);
$total_fine = $fine_row['total_fine'] ? $fine_row['total_fine'] : 0;

// 💡 UPDATED: fines වගුව, අලුත් issued_books සහ books වගු සමඟ JOIN කර පොතේ නම ලබා ගැනීම
$fines_history = mysqli_query($conn, "SELECT f.*, b.title AS book_title 
                                      FROM fines f
                                      LEFT JOIN issued_books ib ON f.issue_id = ib.id
                                      LEFT JOIN books b ON ib.book_id = b.id
                                      WHERE f.user_id='$user_id' 
                                      ORDER BY f.id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Fines & Payments</title>
    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- 🔗 LINK CUSTOM CSS FILE -->
    <link rel="stylesheet" href="minefines.css">
</head>
<body>

<!-- STICKY TOP NAVIGATION BAR -->
<nav class="portal-navbar">
    <div class="nav-container">
        <div class="nav-logo">
            <i class="fa-solid fa-book-open"></i>
            <span>Lib<span style="color: #818cf8;">Sphere</span></span>
        </div>
        <div class="nav-links">
            <a href="search_books.php" class="nav-item"><i class="fa-solid fa-magnifying-glass"></i> Search Books</a>
            <a href="borrowed_books.php" class="nav-item"><i class="fa-solid fa-book"></i> Borrowed Books</a>
            <a href="user_reservation.php" class="nav-item"><i class="fa-solid fa-bookmark"></i> Reservations</a>
            <a href="my_fines.php" class="nav-item active"><i class="fa-solid fa-wallet"></i> My Fines</a>
            <a href="user_dashboard.php" class="nav-item"><i class="fa-solid fa-chart-pie"></i> My Dashboard</a>
        </div>
    </div>
</nav>

<!-- MAIN CATALOG CONTAINER -->
<div class="catalog-container">
    
    <!-- PAGE HEADER -->
    <div class="catalog-header">
        <h1>My Fines & Payments</h1>
        <div class="subtitle">Welcome back, <?php echo htmlspecialchars($full_name); ?>! Review and clear your outstanding library balances.</div>
    </div>

    <!-- 🎨 FINE SUMMARY CARD WITH RIGHT SIDE PAYMENT BOX -->
    <div class="fine-summary-card">
        <div class="fine-meta">
            <p>TOTAL PENDING FINE</p>
            <h2>Rs. <?php echo number_format($total_fine, 2); ?></h2>
        </div>

        <?php
        $merchant_id = "1236720";   
        $merchant_secret = "MTM1Mjg3NjUzNTIwMTY1ODEyMzg1NjczMzE0OTQzMDcwMTY0NjEx"; 
        $order_id = "FINE-" . $user_id;
        $currency = "LKR";
        $amount_formatted = number_format($total_fine, 2, '.', ''); 
        $hash = strtoupper(md5($merchant_id . $order_id . $amount_formatted . $currency . strtoupper(md5($merchant_secret))));
        ?>

        <!-- දකුණු පැත්තේ පෙනෙන සුපිරි PAYMENT BOX එක -->
        <div class="payment-gateway-zone">
            <span class="gateway-label"><i class="fa-solid fa-shield-halved"></i> Secured PayHere Gateway</span>
            
            <form method="post" action="https://sandbox.payhere.lk/pay/checkout" style="width: 100%;">   
                <input type="hidden" name="merchant_id" value="<?php echo $merchant_id; ?>"> 
                <input type="hidden" name="hash" value="<?php echo $hash; ?>"> 
                <input type="hidden" name="return_url" value="http://localhost/LibraryManagementSystem/payment_success.php">
                <input type="hidden" name="cancel_url" value="http://localhost/LibraryManagementSystem/my_fines.php">
                <input type="hidden" name="notify_url" value="http://localhost/LibraryManagementSystem/payment_success.php">  
                <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                <input type="hidden" name="items" value="Library Fine Payment">
                <input type="hidden" name="currency" value="<?php echo $currency; ?>">
                <input type="hidden" name="amount" value="<?php echo $amount_formatted; ?>">  
                <input type="hidden" name="first_name" value="<?php echo htmlspecialchars($full_name); ?>">
                <input type="hidden" name="last_name" value="Student">
                <input type="hidden" name="email" value="student@email.com">
                <input type="hidden" name="phone" value="0771234567">
                <input type="hidden" name="address" value="Library Dept">
                <input type="hidden" name="city" value="Colombo">
                <input type="hidden" name="country" value="Sri Lanka">

                <button type="submit" class="pay-submit-btn" <?php echo ($total_fine == 0) ? 'disabled' : ''; ?>>
                    <i class="fa-solid fa-credit-card"></i> Pay via PayHere
                </button>
            </form>
        </div>
    </div>

    <!-- 🎨 FINE HISTORY TABLE CARD -->
    <div class="dashboard-card">
        <h3>Fine History Log</h3>
        
        <div class="table-responsive">
            <?php if(mysqli_num_rows($fines_history) > 0) { ?>
                <table>
                    <thead>
                        <tr>
                            <th>Fine ID</th>
                            <th>Book Title</th>
                            <th>Late Days</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($fines_history)) { 
                            if ($row['status'] == 'Paid') {
                                $status_badge = '<span class="badge-status approved"><i class="fa-solid fa-circle-check"></i> Paid</span>';
                            } else {
                                $status_badge = '<span class="badge-status unpaid"><i class="fa-solid fa-circle-exclamation"></i> Unpaid</span>';
                            }
                            
                            // පොතේ නමක් නැත්නම් Default text එකක් පෙන්වීමට
                            $display_title = !empty($row['book_title']) ? htmlspecialchars($row['book_title']) : "General / Overdue Book";
                        ?>
                            <tr>
                                <td style="font-weight: 700;">#F-00<?php echo $row['id']; ?></td>
                                <td><strong><?php echo $display_title; ?></strong></td>
                                <td><?php echo $row['late_days']; ?> Days</td>
                                <td class="text-danger-bold">Rs. <?php echo number_format($row['fine_amount'], 2); ?></td>
                                <td><?php echo $status_badge; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <!-- EMPTY STATE STYLE -->
                <div class="info-state-box">
                    <i class="fa-solid fa-shield-heart"></i>
                    <h3>No Fine History Found</h3>
                    <p>You don't have any pending or past fines. Keep up the good work!</p>
                </div>
            <?php } ?>
        </div>
    </div>

</div>

</body>
</html>