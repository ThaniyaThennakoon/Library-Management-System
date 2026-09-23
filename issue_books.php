<?php
$conn = mysqli_connect("localhost", "root", "", "librarydb");

if (!$conn) {
    die("Database Connection Failed");
}


$today = date('Y-m-d');
mysqli_query($conn, "UPDATE issued_books 
                     SET status = 'Cancelled' 
                     WHERE status = 'Pending' 
                     AND DATEDIFF('$today', request_date) > 3");


$today = date('Y-m-d');
mysqli_query($conn, "UPDATE issued_books SET status = 'Cancelled' WHERE status = 'Pending' AND DATEDIFF('$today', request_date) > 3");


mysqli_query($conn, "UPDATE reservations 
                     SET status = 'Expired' 
                     WHERE status = 'Ready' 
                     AND expiry_date < CURDATE()");


mysqli_query($conn, "UPDATE books b 
                     JOIN reservations r ON b.id = r.book_id 
                     SET b.quantity = b.quantity + 1, r.is_quantity_restored = 1 
                     WHERE r.status = 'Expired' 
                     AND r.is_quantity_restored = 0");



date_default_timezone_set("Asia/Colombo");

$get_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$get_book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;
$get_res_id  = isset($_GET['reservation_id']) ? intval($_GET['reservation_id']) : 0;
$get_borrow_id = isset($_GET['borrow_id']) ? intval($_GET['borrow_id']) : 0;

$active_tab = "borrow";
if ($get_res_id > 0) {
    $active_tab = "reserve";
}

/* ISSUE BOOK ACTION */
if (isset($_POST['issue'])) {
    $user_id     = intval($_POST['user_id']);
    $book_id     = intval($_POST['book_id']);
    $res_id      = intval($_POST['reservation_id']);
    $borrow_id   = intval($_POST['borrow_id']);
    $issue_date  = mysqli_real_escape_string($conn, $_POST['issue_date']);
    $return_date = mysqli_real_escape_string($conn, $_POST['return_date']);

    $check_already = mysqli_query($conn, "SELECT id FROM issued_books WHERE user_id='$user_id' AND book_id='$book_id' AND status='Issued'");
    if (mysqli_num_rows($check_already) > 0) {
        echo "<script>alert('❌ Cannot Issue! This user already has a copy of this book.'); window.location='issue_books.php';</script>";
        exit();
    }

    $check = mysqli_query($conn, "SELECT quantity FROM books WHERE id='$book_id'");
    $book_data = mysqli_fetch_assoc($check);

    if ($book_data['quantity'] > 0) {
        if ($borrow_id == 0 && $res_id == 0) {
            $insert_borrow = "INSERT INTO issued_books (user_id, book_id, request_date, issue_date, return_date, status)
                              VALUES ('$user_id', '$book_id', '$issue_date', '$issue_date', '$return_date', 'Issued')";
            mysqli_query($conn, $insert_borrow);
            mysqli_query($conn, "UPDATE books SET quantity = quantity - 1 WHERE id='$book_id'");
        }
        }

        if ($borrow_id > 0) {
            mysqli_query($conn, "UPDATE issued_books SET status = 'Issued', issue_date = '$issue_date', return_date = '$return_date' WHERE id = '$borrow_id'");
            mysqli_query($conn, "UPDATE books SET quantity = quantity - 1 WHERE id='$book_id'");
        
        }

        if ($res_id > 0) {
            mysqli_query($conn, "UPDATE reservations SET status = 'Approved' WHERE id='$res_id'");
            mysqli_query($conn, "INSERT INTO issued_books (user_id, book_id, request_date, issue_date, return_date, status) VALUES ('$user_id', '$book_id', '$issue_date', '$issue_date', '$return_date', 'Issued')");
        }

        if ($res_id == 0) {
            mysqli_query($conn, "UPDATE books SET quantity = quantity - 1 WHERE id='$book_id'");
        }

        $book_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT title FROM books WHERE id='$book_id'"));
        $book_title = $book_info['title'];
        $notif_title = mysqli_real_escape_string($conn, "Book Issued Successfully");
        $notif_msg = mysqli_real_escape_string($conn, "Your request for \"$book_title\" has been approved! Please return it before $return_date.");
        mysqli_query($conn, "INSERT INTO notifications(user_id,title,message,status) VALUES('$user_id','$notif_title','$notif_msg','Unread')");

        echo "<script>alert('Book Issued Successfully!'); window.location='issue_books.php';</script>";
        exit();
    } 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Books</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="issuebookss.css">
</head>
<body>

<div class="page-container">

    <!-- 👑 TOP NAVIGATION -->
    <div class="top-nav">
        <a href="admin_dashboard.php" class="btn-dashboard">
            <i class="fa-solid fa-arrow-left"></i> Go to Dashboard
        </a>
    </div>

    <!-- 📋 MAIN FORM CARD -->
    <div class="form-card">
        <div class="form-header">
            <div class="icon-box">
                <i class="fa-solid fa-book-bookmark"></i>
            </div>
            <h1>Issue Book</h1>
            <p>Issue a new book to a member manually or process active requests.</p>
        </div>

        <form method="POST">
            <input type="hidden" name="reservation_id" value="<?php echo $get_res_id; ?>">
            <input type="hidden" name="borrow_id" value="<?php echo $get_borrow_id; ?>">

            
            <div class="form-group">
                <label>Select Member <span class="req">*</span></label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-user"></i>
                    <select name="user_id" required>
                        <option value="">-- Choose Member --</option>
                        <?php
                        $users = mysqli_query($conn, "SELECT * FROM users WHERE role='user' ORDER BY full_name");
                        while($row=mysqli_fetch_assoc($users)){
                            $selected = ($row['id'] == $get_user_id) ? "selected" : "";
                            echo "<option value='".$row['id']."' $selected>".$row['member_id']." - ".$row['full_name']."</option>";
                        } ?>
                    </select>
                </div>
            </div>

            <!-- Book Dropdown -->
            <div class="form-group">
                <label>Select Book <span class="req">*</span></label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-book"></i>
                    <select name="book_id" required>
                        <option value="">-- Choose Book --</option>
                        <?php
                        $books = mysqli_query($conn, "SELECT * FROM books ORDER BY title");
                        while($book=mysqli_fetch_assoc($books)){
                            $selected = ($book['id'] == $get_book_id) ? "selected" : "";
                            echo "<option value='".$book['id']."' $selected>".$book['title']." (Available: ".$book['quantity'].")</option>";
                        } ?>
                    </select>
                </div>
            </div>

            <!-- Dates Row -->
            <div class="form-row">
                <div class="form-group">
                    <label>Issue Date <span class="req">*</span></label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-calendar-day"></i>
                        <input type="date" name="issue_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Return Date <span class="req">*</span></label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-calendar-check"></i>
                        <input type="date" name="return_date" value="<?php echo date('Y-m-d', strtotime('+14 days')); ?>" required>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="issue" class="btn-submit">
                    <i class="fa-solid fa-key"></i> Issue Book Now
                </button>
            </div>
        </form>
    </div>

    <!-- ⚡ PREMIUM TABS & TABLES CARD -->
    <div class="premium-tabs-card">
        
        <!-- Tab Buttons -->
        <div class="tab-buttons">
            <button class="tab-btn <?php echo ($active_tab == 'borrow') ? 'active' : ''; ?>" onclick="switchTab('borrow-tab', this)">
                <i class="fa-solid fa-paper-plane"></i> Borrow Requests
            </button>
            <button class="tab-btn <?php echo ($active_tab == 'reserve') ? 'active' : ''; ?>" onclick="switchTab('reserve-tab', this)">
                <i class="fa-solid fa-clock"></i> Reservations Queue
            </button>
        </div>

<!-- Borrow Requests Table කොටස නිවැරදි කළ හැටි -->
<div id="borrow-tab" class="tab-content <?php echo ($active_tab == 'borrow') ? 'active' : ''; ?>">
    <table class="premium-table">
        <thead>
            <tr>
                <th>Member</th>
                <th>Book Title</th>
                <th>Request Date</th> 
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $br_result = mysqli_query($conn, "SELECT ib.id AS br_id, u.id AS u_id, u.full_name, b.id AS b_id, b.title, ib.request_date FROM issued_books ib JOIN users u ON ib.user_id = u.id JOIN books b ON ib.book_id = b.id WHERE ib.status = 'Pending' ORDER BY ib.id DESC");
            
            if (mysqli_num_rows($br_result) > 0) {
                while ($br = mysqli_fetch_assoc($br_result)) { ?>
                    <tr>
                        <td><?php echo $br['full_name']; ?></td>
                        <td><strong><?php echo $br['title']; ?></strong></td>
                        <td><?php echo $br['request_date']; ?></td> <!-- දින වකවානුව පෙන්වන්න -->
                        <td><span class="badge badge-pending">PENDING</span></td>
                        <td>
                            <a href="issue_books.php?user_id=<?php echo $br['u_id']; ?>&book_id=<?php echo $br['b_id']; ?>&borrow_id=<?php echo $br['br_id']; ?>" class="btn-table-process">
                                <i class="fa-solid fa-arrow-pointer"></i> Process
                            </a>
                        </td>
                    </tr>
                <?php } 
            } else { 
                echo "<tr><td colspan='5' style='text-align:center; padding:20px; color:var(--text-muted);'>No pending direct borrow requests.</td></tr>"; 
            } ?>
        </tbody>
    </table>
</div>

    <div id="reserve-tab" class="tab-content <?php echo ($active_tab == 'reserve') ? 'active' : ''; ?>">
    <table class="premium-table">
        <thead>
            <tr>
                <th>Member</th>
                <th>Book Title</th>
                <th>Reserve Date</th> <!-- අලුතින් එකතු කළා -->
                <th>Status</th>
                <th>expiry Date</th>      <!-- අලුතින් එකතු කළා -->
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Query එකට reserve_date සහ expiry_date එකතු කළා
            $requests_result = mysqli_query($conn, "SELECT r.id AS res_id, r.status AS res_status, r.reserve_date, r.expiry_date, u.id AS u_id, u.full_name, b.id AS b_id, b.title 
                                                    FROM reservations r 
                                                    JOIN users u ON r.user_id = u.id 
                                                    JOIN books b ON r.book_id = b.id 
                                                    WHERE r.status = 'Pending' OR r.status = 'Ready' 
                                                    ORDER BY r.id DESC");

            if(mysqli_num_rows($requests_result) > 0) {
                while($req = mysqli_fetch_assoc($requests_result)) { ?>
                <tr>
                    <td><?php echo $req['full_name']; ?></td>
                    <td><strong><?php echo $req['title']; ?></strong></td>
                    <td><?php echo $req['reserve_date']; ?></td> <!-- Reserve Date පෙන්වයි -->
                    <td>
                        <span class="badge <?php echo $req['res_status'] == 'Ready' ? 'badge-ready' : 'badge-pending'; ?>">
                            <?php echo strtoupper($req['res_status']); ?>
                        </span>
                    </td>
                    <td><?php echo ($req['res_status'] == 'Ready') ? $req['expiry_date'] : '-'; ?></td> <!-- Deadline පෙන්වයි -->
                    <td>
                        <?php if($req['res_status'] == 'Ready') { ?>
                            <a href="issue_books.php?user_id=<?php echo $req['u_id']; ?>&book_id=<?php echo $req['b_id']; ?>&reservation_id=<?php echo $req['res_id']; ?>" class="btn-table-process">
                                <i class="fa-solid fa-arrow-pointer"></i> Issue to User
                            </a>
                        <?php } else { ?>
                            <span style="color: #94a3b8; font-size: 12px;"><i class="fa-solid fa-hourglass-half"></i> Waiting for Return</span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } 
            } else { 
                echo "<tr><td colspan='6' style='text-align:center; padding:20px; color:var(--text-muted);'>No pending or ready reservations found.</td></tr>"; 
            } ?>
        </tbody>
    </table>
</div>    <!-- 📋 TAB 2: RESERVATIONS QUEUE -->
        












<script>
function switchTab(tabId, element) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    element.classList.add('active');
}
</script>

</body>
</html>