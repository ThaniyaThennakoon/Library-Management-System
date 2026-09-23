<?php
session_start();

// 1. ආරක්ෂාව සඳහා මුලින්ම Session එක පරීක්ෂා කිරීම
if(!isset($_SESSION['user_id'])){
    header("Location: login.html");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "librarydb");

$user_id = $_SESSION['user_id'];
$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : "User";


// =======================================================================
// 📅 පොත් රිසර්ව් කිරීම් සඳහා වන ස්වයංක්‍රීය LOGIC එක (RESERVATION NOTIFICATION)
// =======================================================================
date_default_timezone_set("Asia/Colombo");

// දැනට 'Pending' හෝ 'Approved/Ready' මට්ටමේ පවතින රිසර්වේෂන් ලබාගන්න
$reservation_query = mysqli_query($conn, "SELECT * FROM reservations WHERE user_id = '$user_id' AND (status = 'Pending' OR status = 'Ready')");

while($row = mysqli_fetch_assoc($reservation_query)) {
    $res_id = $row['id'];
    $book_id = $row['book_id'];
    $res_status = $row['status']; // Pending හෝ Ready
    
    // පොතේ නම ලබා ගැනීම
    $book_name_q = mysqli_query($conn, "SELECT title FROM books WHERE id = '$book_id'");
    $book_name_row = mysqli_fetch_assoc($book_name_q);
    //$book_title = $book_name_row['title'];//


    if (!$book_name_row) {
        $book_title = "Unknown Book"; // පොතක් හමු නොවුවහොත් ලැබෙන අගය
    } else {
        $book_title = $book_name_row['title'];
    }


    // SQL වැකි බිඳීම වැළැක්වීම සඳහා පොතේ නම මුලින්ම Escape කරගන්න
    $escaped_book_title = mysqli_real_escape_string($conn, $book_title);

    // ස්ටේටස් එක අනුව මැසේජ් එක තීරණය කිරීම (Escaped පොත් නම භාවිතයෙන්)
   // if ($res_status == 'Ready') {//
       // $notif_title = "Reservation Ready!";//
       // $notif_message = "Good news! Your reserved book '" . $escaped_book_title . "' is now available for pickup at the counter.";//
   // } else {//
      //  $notif_title = "Reservation Confirmed";//
       // $notif_message = "Your reservation request for the book '" . $escaped_book_title . "' has been placed successfully.";//
  //  }//
    
    // Title එකත් ආරක්ෂිතව Escape කරගන්න
    //$escaped_notif_title = mysqli_real_escape_string($conn, $notif_title);//
   // $escaped_notif_message = mysqli_real_escape_string($conn, $notif_message);//
    
    // එකම රිසර්වේෂන් එකට සහ එකම ස්ටේටස් එකට දෙපාරක් නොටිෆිකේෂන් වැටීම වැළැක්වීම 
    //$check_duplicate = mysqli_query($conn, "SELECT * FROM notifications WHERE user_id = '$user_id' AND title = '$escaped_notif_title' AND message LIKE '%$escaped_book_title%'");
    
    // කලින් නොටිෆිකේෂන් එකක් දාලා නැත්නම් පමණක් අලුත් එකක් දමන්න
    //if(mysqli_num_rows($check_duplicate) == 0) {//
       // mysqli_query($conn, "INSERT INTO notifications (user_id, title, message, status) //
                        //     VALUES ('$user_id', '$escaped_notif_title', '$escaped_notif_message', 'Unread')");//
   // }//
}
// =======================================================================


// NOTIFICATIONS UNREAD COUNT
$count = mysqli_query($conn, "SELECT * FROM notifications WHERE user_id='$user_id' AND status='Unread'");
$total = mysqli_num_rows($count);

// DROPDOWN එක ඇතුලේ පෙන්වීමට නොබලපු (UNREAD) අලුත්ම NOTIFICATIONS 3ක් පමණක් ලබාගැනීම
$dropdown_notifications = mysqli_query($conn, "SELECT * FROM notifications WHERE user_id='$user_id' AND status='Unread' ORDER BY id DESC LIMIT 3");

// 💡 BORROWED BOOKS COUNT (UPDATED: borrow_requests වෙනුවට අලුත් issued_books වගුව භාවිතා කර ඇත)
$borrow_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM issued_books WHERE user_id='$user_id' AND status='Issued'");
$borrow_row = mysqli_fetch_assoc($borrow_query);
$total_borrowed = $borrow_row['total'];

// RESERVED BOOKS COUNT
$reserve_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM reservations WHERE user_id='$user_id' AND status='Pending'");
$reserve_row = mysqli_fetch_assoc($reserve_query);
$total_reserved = $reserve_row['total'];

// PENDING FINES
$fine_query = mysqli_query($conn, "SELECT SUM(fine_amount) AS total_fine FROM fines WHERE user_id='$user_id' AND status='Unpaid'");
$fine_row = mysqli_fetch_assoc($fine_query);
$total_fine = $fine_row['total_fine'] ? $fine_row['total_fine'] : 0;

// 💡 RECENT ACTIVITIES (UPDATED: අලුත් issued_books වගුව වෙත මුළු Query එකම හරවා ඇත)
$recent_books = mysqli_query($conn, "SELECT b.title, ib.issue_date, ib.return_date, ib.status 
                                     FROM issued_books ib 
                                     JOIN books b ON ib.book_id = b.id 
                                     WHERE ib.user_id='$user_id' AND (ib.status='Issued' OR ib.status='Returned')
                                     ORDER BY ib.id DESC LIMIT 3");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="user-dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .notification-wrapper {
            position: relative;
            display: inline-block;
        }
        
        .notification-bell {
            cursor: pointer;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            color: #0f172a;
            font-size: 18px;
            transition: all 0.2s ease;
        }
        
        .notification-bell:hover {
            background: #e2e8f0;
            color: #4f46e5;
        }

        .notification-bell .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #dc2626;
            color: white;
            font-size: 11px;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 10px;
            border: 2px solid white;
        }

        .notif-dropdown {
            position: absolute;
            top: 52px;
            right: 0;
            width: 320px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15);
            display: none;
            z-index: 1000;
            overflow: hidden;
        }

        .notif-dropdown.show {
            display: block;
            animation: fadeInNotif 0.2s ease-out;
        }

        @keyframes fadeInNotif {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .notif-header {
            background: #0f172a;
            color: white;
            padding: 12px 16px;
            font-weight: 700;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notif-body {
            max-height: 280px;
            overflow-y: auto;
        }

        .notif-item {
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            gap: 4px;
            background: #eff6ff;
            border-left: 3px solid #4f46e5;
            transition: background 0.2s;
        }

        .notif-item:hover {
            background: #f1f7ff;
        }

        .notif-title {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .notif-message {
            font-size: 12px;
            color: #475569;
            font-weight: 500;
            line-height: 1.4;
            margin: 0;
        }

        .notif-empty {
            padding: 30px;
            text-align: center;
            color: #64748b;
            font-size: 13px;
            font-weight: 500;
        }
        
        .notif-empty i {
            font-size: 24px;
            color: #cbd5e1;
            margin-bottom: 8px;
            display: block;
        }
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    <!-- SIDEBAR NAVIGATION -->
    <div class="sidebar">
        <div class="logo">
            <div class="logo-icon"><i class="fa-solid fa-book-open"></i></div>
            <span>Lib<span class="brand-accent">Sphere</span></span>
        </div>
        <ul class="menu">
            <li class="active"><a href="user_dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
            <li><a href="search_books.php"><i class="fa-solid fa-magnifying-glass"></i> Search Books</a></li>
            <li><a href="borrowed_books.php"><i class="fa-solid fa-book"></i> Borrowed Books</a></li>
            <li><a href="user_reservation.php"><i class="fa-solid fa-bookmark"></i> Reservations</a></li>
            <li><a href="my_fines.php"><i class="fa-solid fa-wallet"></i> My Fines</a></li>
            <li><a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
            <li class="logout-item"><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="topbar">
            <div class="page-title">
                <h2>Overview</h2>
            </div>
            <div class="topbar-actions">
                
                <!-- DROPDOWN NOTIFICATION COMPONENT -->
                <div class="notification-wrapper">
                    <div class="notification-bell" id="notifBellBtn">
                        <i class="fa-regular fa-bell"></i>
                        <?php if($total > 0){ ?>
                            <span class="badge"><?php echo $total; ?></span>
                        <?php } ?>
                    </div>
                    
                    <!-- DROPDOWN BOX -->
                    <div class="notif-dropdown" id="notifDropdownMenu">
                        <div class="notif-header">
                            <span>New Notifications</span>
                            <?php if($total > 0){ ?>
                                <span style="background: #dc2626; font-size: 11px; padding: 2px 8px; border-radius: 12px;"><?php echo $total; ?> New</span>
                            <?php } ?>
                        </div>
                        <div class="notif-body">
                            <?php if(mysqli_num_rows($dropdown_notifications) > 0) { ?>
                                <?php while($notif = mysqli_fetch_assoc($dropdown_notifications)) { ?>
                                    <div class="notif-item">
                                        <p class="notif-title"><?php echo htmlspecialchars($notif['title']); ?></p>
                                        <p class="notif-message"><?php echo htmlspecialchars($notif['message']); ?></p>
                                    </div>
                                <?php } ?>
                            <?php } else { ?>
                                <div class="notif-empty">
                                    <i class="fa-regular fa-bell-slash"></i>
                                    No new notifications.
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="user-profile">
                    <div class="avatar"><?php echo strtoupper(substr($full_name, 0, 1)); ?></div>
                    <div class="profile-info">
                        <h4><?php echo $full_name; ?></h4>
                        <p>Student Member</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="welcome-box">
            <div class="welcome-text">
                <h2>Welcome Back, <?php echo $full_name; ?>! 👋</h2>
                <p>Track your borrowed books, manage reservations, and explore new titles today.</p>
            </div>
        </div>

        <div class="section-header">
            <h3>Quick Actions</h3>
        </div>
        <div class="quick-actions-grid">
            <a href="search_books.php" class="action-btn-card">
                <i class="fa-solid fa-search"></i>
                <span>Find a Book</span>
            </a>
            <a href="user_reservation.php" class="action-btn-card">
                <i class="fa-solid fa-calendar-check"></i>
                <span>View Reserves</span>
            </a>
            <a href="my_fines.php" class="action-btn-card">
                <i class="fa-solid fa-credit-card"></i>
                <span>Pay Fines</span>
            </a>
        </div>

        <div class="section-header">
            <h3>Library Summary</h3>
        </div>
        <div class="dashboard-stats">
            <div class="card indigo">
                <div class="card-icon"><i class="fa-solid fa-book-reader"></i></div>
                <div class="card-data">
                    <h3>Borrowed</h3>
                    <p><?php echo $total_borrowed; ?></p>
                </div>
            </div>

            <div class="card blue">
                <div class="card-icon"><i class="fa-solid fa-clock"></i></div>
                <div class="card-data">
                    <h3>Reserved</h3>
                    <p><?php echo $total_reserved; ?></p>
                </div>
            </div>

            <div class="card amber">
                <div class="card-icon"><i class="fa-solid fa-circle-dollar-to-slot"></i></div>
                <div class="card-data">
                    <h3>Total Fines</h3>
                    <p>Rs. <?php echo $total_fine; ?></p>
                </div>
            </div>

            <div class="card teal">
                <div class="card-icon"><i class="fa-solid fa-envelope"></i></div>
                <div class="card-data">
                    <h3>Messages</h3>
                    <p><?php echo $total; ?></p>
                </div>
            </div>
        </div>

        <div class="section-header">
            <h3>Recent Book Activities</h3>
        </div>
        <div class="table-container">
            <?php if(mysqli_num_rows($recent_books) > 0) { ?>
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th>Book Title</th>
                            <th>Issued Date</th>
                            <th>Return Due</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($recent_books)) { 
                            $is_issued = ($row['status'] == 'Issued');
                            $status_class = $is_issued ? 'status-issued' : 'status-returned';
                            $status_text = $is_issued ? 'Issued' : 'Returned';
                        ?>
                            <tr>
                                <td><strong><?php echo $row['title']; ?></strong></td>
                                <td><?php echo !empty($row['issue_date']) ? $row['issue_date'] : 'Not Set'; ?></td>
                                <td><?php echo !empty($row['return_date']) ? $row['return_date'] : 'Not Set'; ?></td>
                                <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <div class="empty-state">
                    <i class="fa-solid fa-folder-open"></i>
                    <p>No recent activity found. Start borrowing books!</p>
                </div>
            <?php } ?>
        </div>

    </div>
</div>

<!-- SMART NOTIFICATION CLOSING LOGIC -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const notifBellBtn = document.getElementById("notifBellBtn");
    const notifDropdownMenu = document.getElementById("notifDropdownMenu");
    const notifWrapper = document.querySelector(".notification-wrapper");
    let needsUpdate = false;

    notifBellBtn.addEventListener("click", function(e) {
        e.stopPropagation();
        notifDropdownMenu.classList.toggle("show");

        if (notifDropdownMenu.classList.contains("show")) {
            const badges = document.querySelectorAll(".notification-wrapper .badge, .notif-header span:last-child");
            badges.forEach(badge => {
                if(badge) badge.style.display = 'none';
            });
            needsUpdate = true; 
        } else {
            triggerMarkAsRead();
        }
    });

    document.addEventListener("click", function(e) {
        if (!notifWrapper.contains(e.target)) {
            if (notifDropdownMenu.classList.contains("show")) {
                notifDropdownMenu.classList.remove("show");
                triggerMarkAsRead();
            }
        }
    });

    function triggerMarkAsRead() {
        if (needsUpdate) {
            fetch("mark_read.php")
                .then(response => response.text())
                .then(data => {
                    console.log("Notifications marked as read.");
                    needsUpdate = false;
                });
        }
    }
});
</script>

</body>
</html>