<?php
session_start();

date_default_timezone_set("Asia/Colombo");
$conn = mysqli_connect("localhost", "root", "", "librarydb");

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

/* ==========================================================================
    💰 COUNTER PAYMENT: MARK FINE AS PAID
   ========================================================================== */
if (isset($_POST['admin_collect_fine'])) {
    $fine_id = intval($_POST['fine_id']);
    
    // fines ටේබල් එකේ status එක 'Paid' ලෙස යාවත්කාලීන කිරීම
    $update_sql = "UPDATE fines SET status = 'Paid' WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($stmt, "i", $fine_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('✅ Fine collected successfully! Status marked as Paid.'); window.location='fine_management.php';</script>";
        exit();
    }
}

/* ==========================================================================
    🔍 GET ALL FINES (Paid & Unpaid sorted: Unpaid first)
   ========================================================================== */
$sql = "SELECT fines.*, books.title, users.full_name, users.member_id, issued_books.return_date AS due_date 
        FROM fines 
        JOIN books ON fines.book_id = books.id 
        JOIN users ON fines.user_id = users.id 
        JOIN issued_books ON fines.issue_id = issued_books.id 
        ORDER BY CASE WHEN fines.status = 'Unpaid' THEN 0 ELSE 1 END, fines.id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Admin - Fine Management</title>
    <link rel="stylesheet" href="fine-managements.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="admin-container"> 
    <div class="header-wrapper">
        <div class="admin-header">
            <h1><i class="fa-solid fa-file-invoice-dollar text-danger-icon"></i> Overdue Fines Dashboard</h1>
            <p class="subtitle">Monitor real-time student library dues, track late returns, and process counter fine payments.</p>
        </div>

        <div class="top-nav">
            <a href="admin_dashboard.php" class="btn-dashboard">
                <i class="fa-solid fa-arrow-left"></i> Go to Dashboard
            </a>
        </div>
    </div>
    
    
</div>

    <!-- 📊 SINGLE INTEGRATED FINES TABLE -->
    <div class="admin-card">
        <h3>All Library Penalties & History</h3>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Student Details</th>
                        <th>Book Information</th>
                        <th>Due Date</th>
                        <th>Days Overdue</th>
                        <th>Fine Amount</th>
                        <th>Status / Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $is_unpaid = ($row['status'] == 'Unpaid');
                    ?>
                        <tr>
                            <td>
                                <div class="student-name"><?php echo htmlspecialchars($row['full_name']); ?></div>
                                <div class="student-id">ID: <?php echo htmlspecialchars($row['member_id']); ?></div>
                            </td>
                            <td>
                                <div class="tbl-book-title"><?php echo htmlspecialchars($row['title']); ?></div>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($row['due_date'])); ?></td>
                            <td>
                                <span class="<?php echo $is_unpaid ? 'text-danger-bold' : ''; ?>">
                                    <?php echo $row['late_days']; ?> Days Late
                                </span>
                            </td>
                            <td>
                                <span class="<?php echo $is_unpaid ? 'text-danger-bold' : ''; ?>" style="<?php echo !$is_unpaid ? 'color: #16a34a; font-weight: bold;' : ''; ?>">
                                    Rs. <?php echo number_format($row['fine_amount'], 2); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($is_unpaid) { ?>
                                    <!-- Unpaid නම් සල්ලි ගන්න බටන් එක පෙන්වයි -->
                                    <form method="POST" onsubmit="return confirm('Confirm counter payment collection for Rs. <?php echo number_format($row['fine_amount'], 2); ?>?');" style="margin: 0;">
                                        <input type="hidden" name="fine_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="admin_collect_fine" class="btn-collect">
                                            <i class="fa-solid fa-cash-register"></i> Collect Cash
                                        </button>
                                    </form>
                                <?php } else { ?>
                                    <!-- Paid නම් ලස්සන බැජ් එකක් පෙන්වයි -->
                                    <span style="background: #dcfce7; color: #15803d; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 600; display: inline-block;">
                                        <i class="fa-solid fa-circle-check"></i> Paid & Cleared
                                    </span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='6' class='no-data-cell'><i class='fa-solid fa-circle-check checked-icon'></i>Great! There are no library fines recorded in the system.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>