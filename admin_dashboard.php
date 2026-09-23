<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.html");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "librarydb");
if(!$conn){
    die("Connection Failed: " . mysqli_connect_error());
}

$notif_count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM notifications WHERE user_id = 1 AND status = 'Unread'");
$unread_count = mysqli_fetch_assoc($notif_count_query)['total'];

$totalBooks = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total_books FROM books"))['total_books'];
$totalMembers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total_members FROM users WHERE role='user'"))['total_members'];
$totalBorrowed = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total_borrowed FROM issued_books WHERE status='Issued'"))['total_borrowed'];
$totalFines = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(fine_amount) AS total_fines FROM fines WHERE status='Unpaid'"))['total_fines'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admindashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Notification Dropdown Styles */
        .notification-container { position: relative; cursor: pointer; margin-right: 20px; }
        .notification-dropdown { display: none; position: absolute; right: 0; top: 40px; background: white; width: 280px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.15); padding: 15px; z-index: 1000; border: 1px solid #eee; }
        .notification-dropdown h4 { margin-top: 0; font-size: 14px; color: #333; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .notif-item { font-size: 13px; padding: 10px 0; border-bottom: 1px solid #f9f9f9; color: #555; }
        .view-all-notif { display: block; margin-top: 10px; font-size: 12px; color: #4e73df; text-decoration: none; text-align: center; }
        .badge { position: absolute; top: -5px; right: -8px; background: red; color: white; border-radius: 50%; padding: 2px 5px; font-size: 10px; font-weight: bold; }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <div class="sidebar">
        <div class="logo">
            <div class="logo-icon"><i class="fa-solid fa-screws-turn"></i></div>
            <span>Lib<span class="brand-accent">Admin</span></span>
        </div>
        <ul class="menu">
            <li class="active"><a href="admin_dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
            <li><a href="add_books.php"><i class="fa-solid fa-square-plus"></i> Add Books</a></li>
            <li><a href="manage_books.php"><i class="fa-solid fa-list-check"></i> Manage Books</a></li>
            <li><a href="members.php"><i class="fa-solid fa-users"></i> Members</a></li>
            <li><a href="issue_books.php"><i class="fa-solid fa-book-bookmark"></i> Issue Books</a></li>
            <li><a href="return_books.php"><i class="fa-solid fa-right-to-bracket"></i> Return Books</a></li>
            <li><a href="fine_management.php"><i class="fa-solid fa-hand-holding-dollar"></i> Fine Management</a></li>
            <li style="padding: 10px 20px;">
                <div style="color: #a0a0a0; font-size: 12px; margin-bottom: 5px; font-weight: bold;">SELECT DATE:</div>
                <form action="report.php" method="GET" target="_blank" style="display: flex; flex-direction: column; gap: 10px;">
                    <input type="date" name="report_date" required style="width: 100%; padding: 10px; background: #1a2234; border: 1px solid #333; color: white; border-radius: 4px; box-sizing: border-box;">
                    <button type="submit" style="width: 100%; background: #333; border: 1px solid #444; color: white; padding: 10px; cursor: pointer; border-radius: 4px; text-align: left; font-size: 14px; transition: 0.3s;">
                        <i class="fa-solid fa-file-pdf" style="margin-right: 10px;"></i> Generate Report
                    </button>
                </form>
            </li>
            <li class="logout-item"><a href="logout.php"><i class="fa-solid fa-power-off"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div class="page-title">
                <h2>Admin Console</h2>
                <p>Hello <?php echo $_SESSION['full_name']; ?>, welcome to management panel.</p>
            </div>
            <div class="right-topbar">
                <!-- Notification Container -->
                <div class="notification-container" id="notifBtn">
                    <i class="fa-solid fa-bell" style="font-size: 20px; color: #555;"></i>
                    <?php if($unread_count > 0): ?><span class="badge"><?php echo $unread_count; ?></span><?php endif; ?>
                    <div class="notification-dropdown" id="notifDropdown">
    <h4>Recent Notifications</h4>
    <?php
    $notif_q = mysqli_query($conn, "SELECT * FROM notifications WHERE user_id = 1 ORDER BY id DESC LIMIT 5");
    if(mysqli_num_rows($notif_q) > 0) {
        while($n = mysqli_fetch_assoc($notif_q)): ?>
            <div class="notif-item" style="border-bottom: 1px solid #eee; padding: 8px 0;">
                <strong style="font-size: 13px; color: #333;"><?php echo $n['title']; ?></strong><br>
                <small style="font-size: 12px; color: #666;"><?php echo $n['message']; ?></small>
            </div>
        <?php endwhile;
    } else {
        echo "<div class='notif-item' style='padding: 10px; text-align: center; color: #999;'>No new notifications</div>";
    }
    ?>
</div>
                </div>
                <div class="user-profile">
                    <div class="avatar"><i class="fa-solid fa-user-gear"></i></div>
                    <div class="profile-info">
                        <h4><?php echo $_SESSION['full_name']; ?></h4>
                        <p>System Administrator</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ඉතිරි Dashboard කොටස -->
        <div class="section-header"><h3>Quick Operations</h3></div>
        <div class="quick-actions-grid">
            <a href="add_books.php" class="action-btn-card"><i class="fa-solid fa-plus-circle"></i><span>Add New Book</span></a>
            <a href="issue_books.php" class="action-btn-card"><i class="fa-solid fa-hand-holding"></i><span>Issue a Book</span></a>
            <a href="return_books.php" class="action-btn-card"><i class="fa-solid fa-rotate-left"></i><span>Return Process</span></a>
        </div>

        <div class="section-header"><h3>System Statistics</h3></div>
        <div class="cards">
            <div class="card indigo"><div class="card-icon"><i class="fa-solid fa-book"></i></div><div class="card-data"><h3>Total Books</h3><h1><?php echo $totalBooks; ?></h1></div></div>
            <div class="card blue"><div class="card-icon"><i class="fa-solid fa-book-open-reader"></i></div><div class="card-data"><h3>Borrowed Books</h3><h1><?php echo $totalBorrowed; ?></h1></div></div>
            <div class="card teal"><div class="card-icon"><i class="fa-solid fa-users"></i></div><div class="card-data"><h3>Active Members</h3><h1><?php echo $totalMembers; ?></h1></div></div>
            <div class="card crimson"><div class="card-icon"><i class="fa-solid fa-scale-unbalanced"></i></div><div class="card-data"><h3>Total Unpaid Fines</h3><h1>Rs. <?php echo $totalFines; ?></h1></div></div>
        </div>

        <div class="section-header"><h3>Recent Activity</h3></div>
        <div class="table-container">
            <table>
                <thead>
                    <tr><th>Book Name</th><th>Member</th><th>Issue Date</th><th>Return Date (Due)</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT books.title, users.full_name, users.member_id,issued_books.issue_date, issued_books.return_date, issued_books.status 
                            FROM issued_books 
                            JOIN books ON issued_books.book_id = books.id 
                            JOIN users ON issued_books.user_id = users.id
                            WHERE issued_books.status = 'Issued' OR issued_books.status = 'Returned'
                            ORDER BY issued_books.id DESC LIMIT 5"; 
                    $result = mysqli_query($conn, $sql);
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)){ ?>
                            <tr>
                                <td><strong><?php echo $row['title']; ?></strong></td>
                               <td> <div class="student-name"><?php echo htmlspecialchars($row['full_name']); ?></div>
                                <div class="student-id">ID: <?php echo htmlspecialchars($row['member_id']); ?></div></td>
                                <td><i class="fa-regular fa-calendar"></i> <?php echo !empty($row['issue_date']) ? $row['issue_date'] : 'Not Set'; ?></td>
                                <td><i class="fa-regular fa-calendar-check"></i> <?php echo !empty($row['return_date']) ? $row['return_date'] : 'Not Set'; ?></td>
                                <td>
                                    <?php if($row['status'] == "Issued"){ ?>
                                        <span class="status issued" style="color: #2563eb; background: #dbeafe; padding: 4px 8px; border-radius: 4px; font-size: 13px; font-weight: 600;"><i class="fa-solid fa-circle-dot"></i> Issued</span>
                                    <?php } else { ?>
                                        <span class="status returned" style="color: #16a34a; background: #dcfce7; padding: 4px 8px; border-radius: 4px; font-size: 13px; font-weight: 600;"><i class="fa-solid fa-circle-check"></i> Returned</span>
                                    <?php } ?>
                                </td>
                            </tr>
                    <?php } } else { echo "<tr><td colspan='5' style='text-align:center; padding:15px; color:#64748b;'>No recent log activities found.</td></tr>"; } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

    <script>
    document.getElementById('notifBtn').addEventListener('click', function() {
        var dropdown = document.getElementById('notifDropdown');
        var badge = document.querySelector('.badge');
        
        // Dropdown එක පෙන්වීම/සැඟවීම
        dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
        
        // බෙල් එක ක්ලික් කළොත් Badge එක අයින් කරලා, Database එක Update කරන්න
        if(badge) {
            badge.style.display = 'none';
            
            // ඔබ හදපු PHP ෆයිල් එකට රහසිගතව පණිවිඩයක් යවන්න
            fetch('mark_read.php', {
                method: 'POST'
            }).then(response => {
                console.log('Notifications marked as read');
            });
        }
    });
</script>
</body>
</html>