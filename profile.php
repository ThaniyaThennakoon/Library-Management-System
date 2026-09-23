<?php
session_start();

// 1. යූසර් ලොග් වෙලා නැත්නම් ලොගින් පේජ් එකට රීඩිරෙක්ට් කිරීම
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

date_default_timezone_set("Asia/Colombo");

// ඩේටාබේස් සම්බන්ධතාවය
$conn = mysqli_connect("localhost", "root", "", "librarydb");

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

$user_id = intval($_SESSION['user_id']);

// Prepared Statement එකක් පාවිච්චි කරලා ආරක්ෂිතව දත්ත ගැනීම
$query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    die("User not found");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="profile.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- ෆොටෝ එක රවුමට සහ ලස්සනට පේන්න CSS ටිකක් මෙතනින් දැම්මා -->
    <style>
        .profile-avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        /* ඔයාගේ පරණ .profile-avatar class එකේ text-align/font-size තිබ්බා නම්, image එකක් දාද්දි ඒක අවුල් නොවෙන්න මේක උදව් වෙනවා */
        .profile-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background-color: #f1f2f6; 
        }
    </style>
</head>
<body>

<div class="profile-container">

    <div class="profile-topbar">
        <a href="user_dashboard.php" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Dashboard
        </a>
        <span class="portal-badge">EduLib Student</span>
    </div>

    <div class="main-profile-card">
        
        <div class="card-banner"></div>

        <!-- 🔄 මෙන්න මේ කොටස තමයි අපි වෙනස් කරේ -->
        <div class="avatar-wrapper">
            <div class="profile-avatar">
                <?php 
                    // ඩේටාබේස් එකේ profile_pic එකක් තියෙනවාද සහ ඒ ෆයිල් එක ඇත්තටම uploads ෆෝල්ඩර් එකේ තියෙනවාද බලනවා
                    if (!empty($user['profile_pic']) && file_exists($user['profile_pic'])) {
                        $avatar_url = $user['profile_pic'];
                    } else {
                        // ෆොටෝ එකක් නැත්නම් පේන්න Default Avatar එකක් දානවා
                        $avatar_url = 'https://cdn-icons-png.flaticon.com/512/3177/3177440.png';
                    }
                ?>
                <img src="<?php echo $avatar_url; ?>" alt="Profile Picture" class="profile-avatar-img">
            </div>
        </div>

        <div class="user-identity">
            <h2><?php echo htmlspecialchars($user['full_name'] ?? 'Unknown User'); ?></h2>
            <span class="member-tag">
                 ID: #<?php echo htmlspecialchars($user['member_id'] ?? 'N/A'); ?>
            </span>
        </div>

        <div class="info-list">
            
            <div class="info-item">
                <div class="info-icon bg-blue">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div class="info-text">
                    <label>Email Address</label>
                    <p><?php echo htmlspecialchars($user['email'] ?? 'No Email Registered'); ?></p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon bg-purple">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <div class="info-text">
                    <label>Residential Address</label>
                    <p><?php echo htmlspecialchars($user['address'] ?? 'No Address Added Yet'); ?></p>
                </div>
            </div>

        </div>

        <div class="card-actions">
            <a href="edit_profile.php" class="btn-modern-edit">
                <i class="fa-solid fa-user-gear"></i> Edit Profile Details
            </a>
        </div>

    </div>

</div>

</body>
</html>