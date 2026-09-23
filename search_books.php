<?php
session_start();
date_default_timezone_set("Asia/Colombo");


$conn = mysqli_connect("localhost", "root", "", "librarydb");

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$logged_user_id = intval($_SESSION['user_id']);


if (isset($_POST['submit_action'])) {
    $book_id = intval($_POST['book_id']);
    $type = mysqli_real_escape_string($conn, $_POST['action_type']); 
    $current_date = date("Y-m-d H:i:s"); 

    
$stock_check = mysqli_query($conn, "SELECT quantity, title FROM books WHERE id = '$book_id'");
$book_data = mysqli_fetch_assoc($stock_check);

    if (!$book_data) {
        echo "<script>alert('❌ Invalid Book ID!'); window.location='search_books.php';</script>";
        exit();
    }

    $actual_quantity = intval($book_data['quantity']);

    
    if ($type == 'Borrow') {
        
        
        if ($actual_quantity > 0) {
            
            
            $check_borrow = mysqli_query($conn, "SELECT id FROM issued_books 
                                                 WHERE user_id = '$logged_user_id' 
                                                 AND book_id = '$book_id' 
                                                 AND status IN ('Pending', 'Approved')");

            if (mysqli_num_rows($check_borrow) > 0) {
                echo "<script>
                        alert('⚠️ You already have an active borrow request or approval for this book!'); 
                        window.location='search_books.php';
                      </script>";
                exit();
            }

               $insert = mysqli_query($conn, "INSERT INTO issued_books (user_id, book_id, request_date, status) VALUES ('$logged_user_id', '$book_id', '$current_date', 'Pending')");

if ($insert) {
    
    $admin_msg = "User ID $logged_user_id requested to borrow: " . $book_data['title'];
    mysqli_query($conn, "INSERT INTO notifications (user_id, title, message, status) VALUES (1, 'New Borrow Request', '$admin_msg', 'Unread')");

   
$user_msg = "Success! Your request for " . $book_data['title'] . " is confirmed. Please pick up the book within 3 days before it is automatically cancelled.";

$stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, status, created_at) VALUES (?, ?, ?, ?, ?)");

$status = 'Unread';
$title = 'Borrow Request Success';


$stmt->bind_param("issss", $logged_user_id, $title, $user_msg, $status, $current_date);
$stmt->execute();
$stmt->close();










    echo "<script>alert('Your borrow request has been submitted!'); window.location='search_books.php';</script>";
    exit();
}
        } else {
            echo "<script>alert('❌ All copies are currently issued. You can now place a reservation queue request.'); window.location='search_books.php';</script>";
            exit();
        }
    } 
    
   
    elseif ($type == 'Reserve') {

        
        $check_reserve = mysqli_query($conn, "SELECT id FROM reservations WHERE user_id = '$logged_user_id' AND book_id = '$book_id' AND status IN ('Pending', 'Ready')");

        if (mysqli_num_rows($check_reserve) > 0) {
            echo "<script>alert('⚠️ You are already in the reservation queue for this book!'); window.location='search_books.php';</script>";
            exit();
        }

        
        $queue_count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM reservations WHERE book_id = '$book_id' AND status = 'Pending'");
        $data = mysqli_fetch_assoc($queue_count_query);
        $position = $data['total'] + 1;

        $insert = mysqli_query($conn, "INSERT INTO reservations (user_id, book_id, reserve_date, status) VALUES ('$logged_user_id', '$book_id', '$current_date', 'Pending')");

      if ($insert) {
        $admin_msg = "User ID $logged_user_id joined the waiting list for: " . $book_data['title'];
    mysqli_query($conn, "INSERT INTO notifications (user_id, title, message, status) VALUES (1, 'New Reservation Request', '$admin_msg', 'Unread')");
            echo "<script>alert('🎉 Successfully reserved! You are number $position in the waiting list.'); window.location='user_reservation.php';</script>";
            exit();
        }
    }
} 


$categories_result = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");

$search_query = isset($_GET['search']) ? trim(mysqli_real_escape_string($conn, $_GET['search'])) : '';
$category_filter = isset($_GET['category']) ? intval($_GET['category']) : 0;

$has_searched = (!empty($search_query) || $category_filter > 0);
$total_matches = 0;
$result = null;

if ($has_searched) {
    $sql = "SELECT books.*, categories.name AS category_name 
            FROM books 
            LEFT JOIN categories ON books.category_id = categories.category_id 
            WHERE 1=1";

    if (!empty($search_query)) {
        $sql .= " AND (books.title LIKE '%$search_query%' OR books.author LIKE '%$search_query%')";
    }

    if ($category_filter > 0) {
        $sql .= " AND books.category_id = '$category_filter'";
    }

    $sql .= " ORDER BY books.title ASC";
    $result = mysqli_query($conn, $sql);
    $total_matches = mysqli_num_rows($result);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Catalog books</title>
    <link rel="stylesheet" href="search-book.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* 🎨 SEARCH BAR & FILTER STYLING */
        .search-filter-form {
            width: 100%;
            max-width: 850px;
            margin: 20px auto 0 auto;
        }
        .search-inputs-wrapper {
            display: flex;
            gap: 12px;
            background: #ffffff;
            padding: 10px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            align-items: center;
        }
        .input-box {
            display: flex;
            align-items: center;
            background: #f5f7fb;
            padding: 0 15px;
            border-radius: 8px;
            flex: 1;
        }
        .input-box i {
            color: #a0aec0;
            margin-right: 10px;
        }
        .input-box input, .input-box select {
            border: none;
            background: transparent;
            width: 100%;
            padding: 12px 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            color: #2d3748;
            outline: none;
        }
        .btn-search-submit {
            background: #4f46e5;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-search-submit:hover {
            background: #4338ca;
        }
        .btn-clear-search {
            background: #e2e8f0;
            color: #4a5568;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s, color 0.2s;
        }
        .btn-clear-search:hover {
            background: #cbd5e1;
            color: #1a202c;
        }

        
        .btn-borrow {
            width: 100%;
            background: #10b981;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.2s;
        }
        .btn-borrow:hover {
            background: #059669;
        }

        
        .btn-reserve {
            width: 100%;
            background: #f59e0b !important;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.2s;
        }
        .btn-reserve:hover {
            background: #d97706 !important;
        }

        /* Status Badge Colors Customization */
        .status-badge.available {
            background-color: #d1fae5;
            color: #065f46;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .status-badge.out-of-stock {
            background-color: #fef3c7;
            color: #d97706;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        @media (max-width: 768px) {
            .search-inputs-wrapper {
                flex-direction: column;
                align-items: stretch;
            }
            .btn-search-submit, .btn-clear-search {
                justify-content: center;
                width: 100%;
            }
        }
    </style>
</head>
<body>


<nav class="portal-navbar">
    <div class="nav-container">
        <div class="nav-logo">
            <i class="fa-solid fa-book-open"></i>
            <span>Lib<span style="color: #818cf8;">Sphere</span></span>
        </div>
        <div class="nav-links">
            <a href="search_books.php" class="nav-item active"><i class="fa-solid fa-magnifying-glass"></i> Search Catalog</a>
            <a href="borrowed_books.php" class="nav-item"><i class="fa-solid fa-book-reader"></i> My Borrows</a>
            <a href="user_reservation.php" class="nav-item"><i class="fa-solid fa-bookmark"></i> My Reserves</a>
            <a href="my_fines.php" class="nav-item"><i class="fa-solid fa-wallet"></i> My Fines</a>
            <a href="user_dashboard.php" class="nav-item"><i class="fa-solid fa-chart-pie"></i> My Dashboard</a>
        </div>
    </div>
</nav>

<div class="catalog-container">

    <div class="catalog-header">
        <h1>Find Your  Book</h1>
        <p class="subtitle">Search by typing keywords or filtering through categories instantly.</p>

        <!-- 🔍 ADVANCED FILTER FORM -->
        <form method="GET" action="search_books.php" class="search-filter-form">
            <div class="search-inputs-wrapper">
                <div class="input-box" style="flex: 2;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Type title or author..." autocomplete="off">
                </div>

                <div class="input-box">
                    <i class="fa-solid fa-filter"></i>
                    <select name="category">
                        <option value="0">All Categories</option>
                        <?php while($cat = mysqli_fetch_assoc($categories_result)) { ?>
                            <option value="<?php echo $cat['category_id']; ?>" <?php if($category_filter == $cat['category_id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <button type="submit" class="btn-search-submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                
                <?php if ($has_searched) { ?>
                    <a href="search_books.php" class="btn-clear-search"><i class="fa-solid fa-rotate-left"></i> Clear</a>
                <?php } ?>
            </div>
        </form>
    </div>

    <?php if ($has_searched) { ?>
        <div class="results-meta" id="metaContainer" style="margin-bottom: 15px;">
            <span>Found <?php echo $total_matches; ?> matching books in the catalog.</span>
        </div>
    <?php } ?>

    <div class="books-grid" id="booksGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
        <?php 
        if (!$has_searched) { 
        ?>
            <div class="info-state-box" style="grid-column: 1 / -1; width: 100%; display: flex; flex-direction: column; align-items: center; background: white; padding: 40px; border-radius: 12px; text-align: center;">
                <i class="fa-solid fa-book-open text-primary" style="font-size: 48px; color: #4f46e5; margin-bottom: 15px;"></i>
                <h3>Ready to Explore?</h3>
                <p>Type a keyword or select a category above to start searching the active library catalog.</p>
            </div>
        <?php 
        } elseif ($total_matches > 0) {
            while($book = mysqli_fetch_assoc($result)) { 
                $is_available = $book['quantity'] > 0;
        ?>
                <div class="book-card" style="background: white; padding: 24px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div class="card-badge" style="background: #eff6ff; color: #4f46e5; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; display: inline-block; margin-bottom: 12px;">
                            <?php echo htmlspecialchars($book['category_name'] ?? 'General'); ?>
                        </div>
                        <h3 class="book-title" style="font-size: 18px; color: #1e293b; margin: 0 0 6px 0; font-weight: 700;"><?php echo htmlspecialchars($book['title']); ?></h3>
                        <p class="book-author" style="color: #64748b; font-size: 14px; margin: 0 0 15px 0;">by <?php echo htmlspecialchars($book['author']); ?></p>
                    </div>
                    
                    <div class="card-footer" style="margin-top: 15px;">
                        <?php if($is_available) { ?>
                            <span class="status-badge available"><i class="fa-solid fa-circle-check"></i> On Shelf (<?php echo $book['quantity']; ?> Available)</span>
                            <form method="POST" style="margin-top: 15px; width: 100%;">
                                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                <input type="hidden" name="action_type" value="Borrow">
                                <button type="submit" name="submit_action" class="btn-borrow"><i class="fa-solid fa-paper-plane"></i> Request to Borrow</button>
                            </form>
                        <?php } else { 
                            $b_id = $book['id'];
                            $queue_check = mysqli_query($conn, "SELECT COUNT(id) as total_queue FROM reservations WHERE book_id = '$b_id' AND status = 'Pending'");
                            $queue_data = mysqli_fetch_assoc($queue_check);
                            $queue_count = $queue_data['total_queue'] ?? 0;
                        ?>
                            <span class="status-badge out-of-stock"><i class="fa-solid fa-clock"></i> All Copies Borrowed</span>
                            <form method="POST" style="margin-top: 15px; width: 100%;">
                                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                <input type="hidden" name="action_type" value="Reserve">
                                <button type="submit" name="submit_action" class="btn-reserve">
                                    <i class="fa-solid fa-bookmark"></i> Request to Reserve <?php echo $queue_count > 0 ? "($queue_count)" : ""; ?>
                                </button>
                            </form>
                        <?php } ?>
                    </div>
                </div>
        <?php 
            } 
        } else {
        ?>
            <div id="noResults" class="info-state-box" style="grid-column: 1 / -1; width: 100%; display: flex; flex-direction: column; align-items: center; background: white; padding: 40px; border-radius: 12px; text-align: center;">
                <i class="fa-solid fa-face-frown text-muted" style="font-size: 48px; color: #94a3b8; margin-bottom: 15px;"></i>
                <h3>No books found</h3>
                <p>Please try a different book name or category.</p>
            </div>
        <?php 
        } 
        ?>
    </div>

</div>

</body>
</html>