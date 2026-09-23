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
$message = "";
$update_success = false;

// වත්මන් දත්ත ටික කලින්ම අරන් තියාගන්නවා (පරණ image එක මොකක්ද කියලා දැනගන්න)
$query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);


// ❌ Profile Picture එක අයින් කරන්න "Remove Photo" බටන් එක එබූ විට ක්‍රියාත්මක වන කොටස
if (isset($_POST['remove_photo'])) {
    // දැනට ඩේටාබේස් එකේ ෆොටෝ එකක් තියෙනවාද සහ ඒක සර්වර් එකේ තියෙනවාද බලලා මකලා දානවා
    if (!empty($user['profile_pic']) && file_exists($user['profile_pic'])) {
        unlink($user['profile_pic']); // සර්වර් එකේ ෆෝල්ඩර් එකෙන් ෆයිල් එක delete කරනවා
    }

    // ඩේටාබේස් එකේ profile_pic එක NULL කරනවා
    $remove_query = "UPDATE users SET profile_pic = NULL WHERE id = ?";
    $stmt_remove = mysqli_prepare($conn, $remove_query);
    mysqli_stmt_bind_param($stmt_remove, "i", $user_id);
    
    if (mysqli_stmt_execute($stmt_remove)) {
        $message = "<div class='alert-card success'><i class='fa-solid fa-circle-check'></i> Profile picture removed successfully!</div>";
        $update_success = true;
    } else {
        $message = "<div class='alert-card error'><i class='fa-solid fa-circle-exclamation'></i> Failed to remove profile picture.</div>";
    }
}


// 💾 Save Changes බටන් එක එබූ විට දත්ත Update කරන කොටස
if (isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    
    // පරණ profile pic එක default එක විදිහට තියාගන්නවා
    $profile_pic = $user['profile_pic']; 

    if (!empty($full_name) && !empty($email)) {
        
        // 📷 Image Upload Logic
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
            $allowed_extensions = array("jpg", "jpeg", "png", "gif");
            $file_name = $_FILES['profile_pic']['name'];
            $file_size = $_FILES['profile_pic']['size'];
            $file_tmp  = $_FILES['profile_pic']['tmp_name'];
            
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (in_array($file_ext, $allowed_extensions)) {
                if ($file_size <= 2097152) {
                    $upload_dir = 'uploads/';

                    // අලුත් ෆොටෝ එකක් දාද්දි, පරණ තිබ්බ ෆොටෝ එක folder එකෙන් delete කරලා ඉන්නවා ඉඩ ඉතුරු වෙන්න
                    if (!empty($user['profile_pic']) && file_exists($user['profile_pic'])) {
                        unlink($user['profile_pic']);
                    }

                    $new_file_name = "user_" . $user_id . "_" . time() . "." . $file_ext;
                    $target_file = $upload_dir . $new_file_name;

                    if (move_uploaded_file($file_tmp, $target_file)) {
                        $profile_pic = $target_file; 
                    } else {
                        $message = "<div class='alert-card error'><i class='fa-solid fa-circle-exclamation'></i> Failed to upload image file.</div>";
                    }
                } else {
                    $message = "<div class='alert-card error'><i class='fa-solid fa-circle-exclamation'></i> Image size must be less than 2MB!</div>";
                }
            } else {
                $message = "<div class='alert-card error'><i class='fa-solid fa-circle-exclamation'></i> Only JPG, JPEG, PNG & GIF files are allowed!</div>";
            }
        }

        if (empty($message)) {
            $update_query = "UPDATE users SET full_name = ?, email = ?, address = ?, profile_pic = ? WHERE id = ?";
            $stmt_update = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($stmt_update, "ssssi", $full_name, $email, $address, $profile_pic, $user_id);
            
            if (mysqli_stmt_execute($stmt_update)) {
                $message = "<div class='alert-card success'><i class='fa-solid fa-circle-check'></i> Profile updated successfully! Redirecting...</div>";
                $update_success = true;
            } else {
                $message = "<div class='alert-card error'><i class='fa-solid fa-circle-exclamation'></i> Failed to update profile. Email might be taken.</div>";
            }
        }
    } else {
        $message = "<div class='alert-card error'><i class='fa-solid fa-circle-exclamation'></i> Name and Email are required!</div>";
    }
}

// අලුත් තොරතුරු ආයෙත් පෙන්වන්න database එකෙන් ගැනීම
if ($update_success) {
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="editprofile.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .alert-card.success {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }
        .profile-preview-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }
        .current-profile-img {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #6c5ce7;
            margin-bottom: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        /* Remove බටන් එකට පොඩි ස්ටයිල් එකක් */
        .btn-remove-photo {
            background: none;
            border: none;
            color: #e74c3c;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: underline;
            margin-top: 5px;
        }
        .btn-remove-photo:hover {
            color: #c0392b;
        }
    </style>
</head>
<body>

<div class="edit-container">

    <div class="edit-topbar">
        <a href="profile.php" class="cancel-link">
            <i class="fa-solid fa-xmark"></i> Cancel
        </a>
        <span class="portal-badge-edit">Modifying Details</span>
    </div>

    <div class="main-edit-card">
        
        <div class="card-banner-edit">
            <h3>Update Profile Log</h3>
        </div>

        <div class="form-wrapper">
            
            <?php echo $message; ?>

            <div class="profile-preview-wrapper">
                <?php 
                    $has_photo = (!empty($user['profile_pic']) && file_exists($user['profile_pic']));
                    $avatar = $has_photo ? $user['profile_pic'] : 'https://cdn-icons-png.flaticon.com/512/3177/3177440.png';
                ?>
                <img src="<?php echo $avatar; ?>" alt="Profile Picture" class="current-profile-img">
                
                <!-- 🔄 ෆොටෝ එකක් තියෙනවා නම් විතරක් "Remove Photo" බටන් එක පේන්න සෙට් කරනවා -->
                <?php if ($has_photo): ?>
                    <form method="POST" action="" style="margin: 0;">
                        <button type="submit" name="remove_photo" class="btn-remove-photo" onclick="return confirm('Are you sure you want to remove your profile picture?');">
                            <i class="fa-solid fa-trash-can"></i> Remove Photo
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <form method="POST" action="" enctype="multipart/form-data" class="modern-form">
                
                <div class="form-input-group">
                    <label><i class="fa-solid fa-image"></i> Change Profile Picture</label>
                    <input type="file" name="profile_pic" accept="image/*">
                </div>
                
                <div class="form-input-group">
                    <label><i class="fa-solid fa-user"></i> Full Name</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required placeholder="Enter your full name">
                </div>

                <div class="form-input-group">
                    <label><i class="fa-solid fa-envelope"></i> Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required placeholder="Enter email address">
                </div>

                <div class="form-input-group">
                    <label><i class="fa-solid fa-location-dot"></i> Residential Address</label>
                    <textarea name="address" rows="3" placeholder="Enter your current address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>

                <button type="submit" name="update_profile" class="btn-modern-save">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Save Changes
                </button>

            </form>
        </div>

    </div>

</div>

<script>
    <?php if ($update_success): ?>
        setTimeout(function() {
            window.location.href = 'profile.php';
        }, 2000);
    <?php endif; ?>
</script>

</body>
</html>