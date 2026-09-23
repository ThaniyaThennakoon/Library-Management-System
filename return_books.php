<?php
$conn = mysqli_connect("localhost","root","","librarydb");

if(!$conn){
    die("Database Connection Failed");
}

date_default_timezone_set("Asia/Colombo");

/* ⚡ BOOK RETURN ACTION WITH FINE CALCULATION & QUEUE SYSTEM (UPDATED) */
if(isset($_POST['return_book'])){
    $borrow_id = intval($_POST['borrow_id']);
    $book_id   = intval($_POST['book_id']);
    $user_id   = intval($_POST['user_id']);
    $actual_return_date = date('Y-m-d'); 

    
    $borrow_query = mysqli_query($conn, "SELECT return_date FROM issued_books WHERE id='$borrow_id'");
    $borrow_data  = mysqli_fetch_assoc($borrow_query);
    $expected_return_date = $borrow_data['return_date']; 

    $fine = 0;
    $days_overdue = 0;

    if(!empty($expected_return_date) && $expected_return_date != '0000-00-00' && $expected_return_date != 'Not Set') {
        $today_time = strtotime($actual_return_date);
        $due_time   = strtotime($expected_return_date);

        if($today_time > $due_time) {
            $diff = $today_time - $due_time;
            $days_overdue = floor($diff / (60 * 60 * 24)); 
            $fine = $days_overdue * 50; 
        }
    }

    
    $check_queue = mysqli_query($conn, "SELECT id, user_id FROM reservations 
                                        WHERE book_id = '$book_id' 
                                        AND status = 'Pending' 
                                        ORDER BY reserve_date ASC LIMIT 1");

    $is_reserved = (mysqli_num_rows($check_queue) > 0);

    
    $update_borrow = "UPDATE issued_books SET status = 'Returned', actual_return_date = '$actual_return_date' WHERE id = '$borrow_id'";
    
    if(mysqli_query($conn, $update_borrow)) {
        
        
        if($is_reserved) {
            $res = mysqli_fetch_assoc($check_queue);
            $res_id = $res['id'];
            $res_user = $res['user_id'];

            
            $expiry_date = date('Y-m-d', strtotime('+3 days'));

            
            mysqli_query($conn, "UPDATE reservations SET status = 'Ready', reserve_date = CURDATE(), expiry_date = '$expiry_date' WHERE id = '$res_id'");

           
            $book_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT title FROM books WHERE id='$book_id'"));
            $b_title = $book_info['title'];
            $msg = "The book \"$b_title\" is now available! Please collect it by $expiry_date (within 3 days).";
            mysqli_query($conn, "INSERT INTO notifications (user_id, title, message, status) VALUES ('$res_user', 'Book Ready for Collection', '$msg', 'Unread')");
        } else {
            
            mysqli_query($conn, "UPDATE books SET quantity = quantity + 1 WHERE id='$book_id'");
        }

        
        if($fine > 0) {
            mysqli_query($conn, "INSERT INTO fines (issue_id, user_id, book_id, late_days, fine_amount, status) 
                               VALUES ('$borrow_id', '$user_id', '$book_id', '$days_overdue', '$fine', 'Unpaid')");
        }

        
        $book_info_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT title FROM books WHERE id='$book_id'"));
        $book_title = $book_info_user['title'];
        $notif_title = "Book Returned Successfully";
        $notif_msg = ($fine > 0) ? "You returned \"$book_title\". Total Fine: Rs. $fine.00" : "Thank you for returning \"$book_title\".";
        
        mysqli_query($conn,"INSERT INTO notifications(user_id,title,message,status) VALUES('$user_id','$notif_title','$notif_msg','Unread')");

        echo "<script>alert('✅ Book Returned Successfully!'); window.location='return_books.php';</script>";
        exit();
    } else {
        echo "<script>alert('❌ Error updating database!'); window.location='return_books.php';</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Books</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="returnbooks.css">
</head>
<body>

<div class="page-container">

    <div class="top-nav">
        <a href="admin_dashboard.php" class="btn-dashboard">
            <i class="fa-solid fa-arrow-left"></i> Go to Dashboard
        </a>
    </div>

    <div class="premium-card">
        <div class="card-header">
            <div class="icon-box">
                <i class="fa-solid fa-right-left"></i>
            </div>
            <h1>Return Books Management</h1>
            <p>View currently issued books, check due dates, and process returns with auto-fine calculations.</p>
        </div>

        <table class="premium-table">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Book Title</th>
                    <th>Issued Date</th>
                    <th>Due Date</th>
                    <th>Status / Fine</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                
                $issued_query = "SELECT ib.id AS borrow_id, ib.issue_date, ib.return_date AS due_date, u.id AS user_id, u.full_name, b.id AS book_id, b.title 
                                 FROM issued_books ib 
                                 JOIN users u ON ib.user_id = u.id 
                                 JOIN books b ON ib.book_id = b.id 
                                 WHERE ib.status = 'Issued' 
                                 ORDER BY ib.id DESC";
                
                $result = mysqli_query($conn, $issued_query);
                $today = date('Y-m-d');

                if(mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) { 
                        
                        $calculated_due_date = $row['due_date'];
                        $is_overdue = false;
                        $fine_est = 0;
                        
                        if (!empty($calculated_due_date) && $calculated_due_date != '0000-00-00') {
                            $today_timestamp = strtotime($today);
                            $due_timestamp   = strtotime($calculated_due_date);

                            if($today_timestamp > $due_timestamp) {
                                $is_overdue = true;
                                $diff = $today_timestamp - $due_timestamp;
                                $fine_est = floor($diff / (60 * 60 * 24)) * 50;
                            }
                        } else {
                            $calculated_due_date = "Not Set";
                        }
                        ?>
                    <tr>
                        <td>
                            <div class="user-info">
                                <span class="username"><?php echo $row['full_name']; ?></span>
                                <span class="userid">ID: <?php echo $row['user_id']; ?></span>
                            </div>
                        </td>
                        <td><strong><?php echo $row['title']; ?></strong></td>
                        <td><?php echo !empty($row['issue_date']) ? $row['issue_date'] : 'Not Set'; ?></td>
                        <td class="<?php echo $is_overdue ? 'text-danger font-bold' : ''; ?>">
                            <?php echo $calculated_due_date; ?> 
                        </td>
                        <td>
                            <?php if($calculated_due_date == "Not Set"): ?>
                                <span class="badge" style="background: #64748b; color: white;">No Due Date</span>
                            <?php elseif($is_overdue): ?>
                                <span class="badge badge-overdue">Rs. <?php echo $fine_est; ?> Fine</span>
                            <?php else: ?>
                                <span class="badge badge-active">On Time</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="borrow_id" value="<?php echo $row['borrow_id']; ?>">
                                <input type="hidden" name="book_id" value="<?php echo $row['book_id']; ?>">
                                <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                <button type="submit" name="return_book" class="btn-return" onclick="return confirm('Are you sure this book is returned?');">
                                    <i class="fa-solid fa-undo"></i> Return
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="6" class="no-data">No books are currently issued to any member.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>