<?php
session_start();

// යූසර් ලොග් වෙලා නැත්නම් ලොගින් පේජ් එකට රීඩිරෙක්ට් කිරීම
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

/* ==========================================================================
    CANCEL RESERVATION LOGIC (පරිශීලකයා විසින් අවලංගු කිරීම)
   ========================================================================== */
if (isset($_POST['cancel_reservation'])) {
    $request_id = intval($_POST['request_id']);
    
    
    $check_sql = "SELECT status FROM reservations WHERE id = ? AND user_id = ?";
    $chk_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($chk_stmt, "ii", $request_id, $logged_user_id);
    mysqli_stmt_execute($chk_stmt);
    $chk_result = mysqli_stmt_get_result($chk_stmt);
    
    if (mysqli_num_rows($chk_result) > 0) {
        $row_data = mysqli_fetch_assoc($chk_result);
        
        // Approved වෙච්ච ඒවා (දැනටමත් ඉෂූ කරපු ඒවා) කැන්සල් කරන්න බැහැ
        if ($row_data['status'] != 'Approved') {
            // Status එක 'Cancelled' බවට පත් කිරීම
            $cancel_sql = "UPDATE reservations SET status = 'Cancelled' WHERE id = ?";
            $can_stmt = mysqli_prepare($conn, $cancel_sql);
            mysqli_stmt_bind_param($can_stmt, "i", $request_id);
            
            if (mysqli_stmt_execute($can_stmt)) {
                echo "<script>alert('✅ Reservation cancelled successfully.'); window.location='user_reservation.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('❌ Cannot cancel! This book has already been issued to you.'); window.location='user_reservation.php';</script>";
            exit();
        }
    }
}


$reserve_sql = "SELECT reservations.*, books.title, books.author 
                FROM reservations 
                JOIN books ON reservations.book_id = books.id 
                WHERE reservations.user_id = ?
                ORDER BY reservations.id DESC";

$stmt = mysqli_prepare($conn, $reserve_sql);
mysqli_stmt_bind_param($stmt, "i", $logged_user_id);
mysqli_stmt_execute($stmt);
$reserve_result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Book Reservations</title>
    <link rel="stylesheet" href="user-reservation.css">
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
            <a href="borrowed_books.php" class="nav-item"><i class="fa-solid fa-book-reader"></i> My Borrows</a>
            <a href="user_reservation.php" class="nav-item active"><i class="fa-solid fa-bookmark"></i> My Reserves</a>
            <a href="my_fines.php" class="nav-item"><i class="fa-solid fa-wallet"></i> My Fines</a>
            <a href="user_dashboard.php" class="nav-item"><i class="fa-solid fa-chart-pie"></i> My Dashboard</a>
        </div>
    </div>
</nav>

<div class="res-main-container">
    <div class="res-header">
        <h1>My Book Reservations</h1>
        <p>Track and monitor the status of your active library book reservations here.</p>
    </div>

    <div class="res-card">
        <div class="res-table-responsive">
            <table class="res-table">
                <thead>
                    <tr>
                        <th>Book Details</th>
                        <th>Date Reserved</th>
                        <th>Valid Until (Expiry)</th>
                        <th>Status</th>
                        <th>Action / Notice</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(mysqli_num_rows($reserve_result) > 0) {
                        while($row = mysqli_fetch_assoc($reserve_result)) {
                            $status_text = $row['status'];
                            $status_class = strtolower($status_text);
                            
                            // 🔄 වෙනස්කම 3: අපේ වගුවේ තියෙන්නේ 'reservation_date' (හෝ ඔයාගේ වගුවේ තියෙන column නම දෙන්න)
                            // පොදුවේ ගැලපෙන්න මෙතන 'reservation_date' ලෙස දමා ඇත.
                            $request_date = isset($row['reservation_date']) ? $row['reservation_date'] : date('Y-m-d');
                            
                            $reserve_date_formatted = date('M d, Y', strtotime($request_date));
                            $expiry_date_display = "-";

                            // 📆 දින 2කින් ඩිස්ප්ලේ එක එක්ස්පයර් වෙන ලොජික් එක (Pending හෝ Ready අවස්ථාවලදී පමණක්)
                            if($status_text == 'Pending' || $status_text == 'Ready') {
                                $expiry_date_calc = date('Y-m-d', strtotime($request_date . ' + 2 days'));
                                $expiry_date_display = date('M d, Y', strtotime($expiry_date_calc));
                            }
                    ?>
                        <tr>
                            <td>
                                <div class="res-book-title"><?php echo htmlspecialchars($row['title']); ?></div>
                                <div class="res-book-author">by <?php echo htmlspecialchars($row['author']); ?></div>
                            </td>
                            <td><i class="fa-regular fa-calendar"></i> <?php echo $reserve_date_formatted; ?></td>
                            
                            <td>
                                <span style="<?php echo ($expiry_date_display != '-') ? 'color: #d97706; font-weight: 600;' : ''; ?>">
                                    <?php echo $expiry_date_display; ?>
                                </span>
                            </td>
                            
                            <td>
                                <!-- 🔄 වෙනස්කම 4: අලුත් Statuses වලට අනුව Badge එක පෙන්වීම -->
                                <?php if($status_text == 'Approved') { ?>
                                    <span class="res-status approved" style="background: #d1fae5; color: #065f46; padding: 4px 8px; border-radius: 4px; font-weight: 600;">Issued</span>
                                <?php } elseif($status_text == 'Ready') { ?>
                                    <span class="res-status ready" style="background: #e0e7ff; color: #4338ca; padding: 4px 8px; border-radius: 4px; font-weight: 600;">Ready</span>
                                <?php } elseif($status_text == 'Cancelled') { ?>
                                    <span class="res-status cancelled" style="background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-weight: 600;">Cancelled</span>
                                <?php } else { ?>
                                    <span class="res-status pending" style="background: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 4px; font-weight: 600;">Pending</span>
                                <?php } ?>
                            </td>
                            <td>
                                <!-- 🔄 වෙනස්කම 5: අලුත් Statuses වලට අනුව Notice එක සහ බටන් එක වෙනස් කිරීම -->
                                <?php if($status_text == 'Approved') { ?>
                                    <span class="res-action success" style="margin-right: 10px; color: #059669;"><i class="fa-solid fa-circle-check"></i> Book Handed Over</span>
                                <?php } elseif($status_text == 'Ready') { ?>
                                    <span class="res-action info" style="margin-right: 10px; color: #4f46e5;"><i class="fa-solid fa-bell"></i> Ready for Pickup</span>
                                <?php } elseif($status_text == 'Pending') { ?>
                                    <span class="res-action info" style="margin-right: 10px; color: #d97706;"><i class="fa-solid fa-clock"></i> Awaiting Admin</span>
                                <?php } else { ?>
                                    <span class="disabled-text" style="margin-right: 10px;">No Action</span>
                                <?php } ?>

                                <!-- 🚫 CANCEL BUTTON: තවම පොත අතට ලැබිලා නැති (Pending හෝ Ready) ඒවා විතරක් කැන්සල් කරන්න දෙනවා -->
                                <?php if($status_text == 'Pending' || $status_text == 'Ready') { ?>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to cancel this reservation?');">
                                        <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="cancel_reservation" class="btn-cancel">Cancel</button>
                                    </form>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='5' class='res-no-data' style='text-align:center; padding:30px; color:#94a3b8;'><i class='fa-solid fa-box-open'></i> You don't have any book reservations at the moment.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>