<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "librarydb");

if (!$conn) {
    die("Database Connection Failed");
}

// PayHere එකෙන් සාර්ථකව ගෙවීම් කර ආපසු එන Order ID එක ලබා ගැනීම
$order_id = isset($_REQUEST['order_id']) ? $_REQUEST['order_id'] : '';

if (!empty($order_id)) {
    // Order ID එක කඩලා user_id එක වෙන් කර ගැනීම
    $parts = explode('-', $order_id);
    
    if (count($parts) == 2 && $parts[0] == 'FINE') {
        $user_id = intval($parts[1]);

        // 🌟 අදාළ යූසර්ගේ සියලුම Unpaid දඩ 'Paid' ලෙස අප්ඩේට් කිරීම
        mysqli_query($conn, "UPDATE fines SET status='Paid' WHERE user_id='$user_id' AND status='Unpaid'");
        
        // 🔔 යූසර්ට සල්ලි ගෙවූ බවට Notification එකක් දැමීම
        $notif_title = mysqli_real_escape_string($conn, "Fine Payment Success");
        $notif_msg = mysqli_real_escape_string($conn, "Your library fine payment has been processed successfully. Thank you!");
        mysqli_query($conn, "INSERT INTO notifications (user_id, title, message, status) VALUES ('$user_id', '$notif_title', '$notif_msg', 'Unread')");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: #f8fafc;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .success-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 40px 30px;
            max-width: 450px;
            width: 100%;
            text-align: center;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05), 0 8px 10px -6px rgba(15, 23, 42, 0.05);
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .icon-container {
            width: 100px;
            height: 100px;
            background: #dcfce7;
            color: #16a34a;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 45px;
            margin: 0 auto 25px auto;
            position: relative;
            animation: scaleIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }

        .success-card h1 {
            color: #0f172a;
            font-size: 26px;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .success-card p {
            color: #64748b;
            font-size: 15px;
            font-weight: 500;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .btn-redirect {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: #4f46e5;
            color: #ffffff;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            width: 100%;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }

        .btn-redirect:hover {
            background: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.3);
        }

        .countdown-text {
            margin-top: 20px;
            font-size: 13px;
            color: #94a3b8;
            font-weight: 500;
        }

        .countdown-text span {
            color: #4f46e5;
            font-weight: 700;
        }
    </style>
</head>
<body>

<div class="success-card">
    <div class="icon-container">
        <i class="fa-solid fa-circle-check"></i>
    </div>
    
    <h1>Payment Successful!</h1>
    <p>Thank you! Your library fine payment has been processed successfully. All your pending fines are now cleared.</p>
    
    <a href="my_fines.php" class="btn-redirect">
        <i class="fa-solid fa-arrow-left"></i> Go Back to My Fines
    </a>

    <!-- තත්පර 5කින් Auto Redirect වෙන්න දාපු කවුන්ටර් එක -->
    <div class="countdown-text">
        Redirecting automatically in <span id="countdown">5</span> seconds...
    </div>
</div>

<script>
    // තත්පර 5කින් පිටුව ස්වයංක්‍රීයවම හරවා යැවීමේ JS එක
    let timeLeft = 5;
    const countdownElement = document.getElementById('countdown');

    const interval = setInterval(() => {
        timeLeft--;
        countdownElement.textContent = timeLeft;
        if (timeLeft <= 0) {
            clearInterval(interval);
            window.location.href = 'my_fines.php';
        }
    }, 1000);
</script>

</body>
</html>