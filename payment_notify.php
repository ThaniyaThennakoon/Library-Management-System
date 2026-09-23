<?php
// PayHere එකෙන් බැක්ග්‍රවුන්ඩ් එකේ එවන නිසා කිසිම HTML එකක් හෝ සෙෂන් එකක් මෙතනට අවශ්‍ය නැත.

$conn = mysqli_connect("localhost", "root", "", "librarydb");

if (!$conn) {
    // Connection අවුල් නම් සර්වර් එකට 500 error එකක් දීලා නවත්වන්න
    header("HTTP/1.1 500 Internal Server Error");
    die("Database Connection Failed");
}

// PayHere එකෙන් එවන POST data එක ලබා ගැනීම
// (isset පාවිච්චි කරලා errors එන එක වැළැක්වීම වඩාත් සුදුසුයි)
$merchant_id      = isset($_POST['merchant_id']) ? $_POST['merchant_id'] : '';
$order_id         = isset($_POST['order_id']) ? $_POST['order_id'] : '';
$payhere_amount   = isset($_POST['payhere_amount']) ? $_POST['payhere_amount'] : '';
$payhere_currency = isset($_POST['payhere_currency']) ? $_POST['payhere_currency'] : '';
$status_code      = isset($_POST['status_code']) ? $_POST['status_code'] : '';
$md5sig           = isset($_POST['md5sig']) ? $_POST['md5sig'] : '';

// ඔයාගේ Dashboard එකේ තියෙන Secret එක
$merchant_secret = "MTM1Mjg3NjUzNTIwMTY1ODEyMzg1NjczMzE0OTQzMDcwMTY0NjEx";

// Hash එක නැවත generate කරලා verify කිරීම
$local_md5sig = strtoupper(
    md5(
        $merchant_id . 
        $order_id . 
        $payhere_amount . 
        $payhere_currency . 
        $status_code . 
        strtoupper(md5($merchant_secret))
    )
);

// Payment එක සාර්ථකද (status_code == 2) සහ Hash එක 100% සමානදැයි පරීක්ෂා කිරීම
if (($local_md5sig === $md5sig) && ($status_code == 2)) {
    
    // Order ID එක ආරක්ෂිතව පිරිසිදු කරගන්න (SQL Injection වලින් බේරෙන්න)
    $order_id = mysqli_real_escape_string($conn, $order_id);
    
    // format: FINE-user_id (e.g., FINE-5)
    $parts = explode('-', $order_id);
    
    if (count($parts) >= 2 && $parts[0] == 'FINE') {
        // ආරක්ෂිතව User ID එක Integer එකක් බවට පත් කරගන්නවා
        $user_id = intval($parts[1]);

        // Database එකේ අදාල user ගේ සියලුම 'Unpaid' fines 'Paid' ලෙස update කිරීම
        $update_query = "UPDATE fines SET status='Paid' WHERE user_id='$user_id' AND status='Unpaid'";
        mysqli_query($conn, $update_query);
        
        // 🔔 යූසර්ට නොටිෆිකේෂන් එකක් ඩේටාබේස් එකට දානවා (ලොගින් වුණාම පෙනෙන්න)
        $notif_title = mysqli_real_escape_string($conn, "Fine Paid via Online Gateway");
        $notif_msg = mysqli_real_escape_string($conn, "Your payment of LKR " . $payhere_amount . " was successfully verified. Thank you!");
        mysqli_query($conn, "INSERT INTO notifications (user_id, title, message, status) VALUES ('$user_id', '$notif_title', '$notif_msg', 'Unread')");
    }
    
    // 🌟 PayHere සර්වර් එකට සාර්ථකයි කියලා කියන්න මෙන්න මේ response එක යවන්න ඕනේ
    header("HTTP/1.1 200 OK");
    echo "Notification Processed Successfully";
} else {
    // මොකක් හරි අවුලක් නම් 400 බැඩ් රික්වෙස්ට් එකක් දෙනවා
    header("HTTP/1.1 400 Bad Request");
    echo "Invalid Signature or Payment Failed";
}

mysqli_close($conn);
?>