<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: customer_login.php");
    exit();
}

// Get current date and time
$current_date = date('l, F j, Y');
$current_time = date('H:i:s');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/customer_welcome.css">
    <link rel="icon" type="image/png" href="../imgs/moelci logo.png">
    <title>MOELCI II Service Selection</title>

</head>
<body>
   <div class="logo-container">
    <div class="company-logo">
        <img src="../imgs/moelci logo.png" alt="Company Logo" class="logo-image" />
    </div>
    
    <div class="welcome-text">
        <h1>Welcome to Moelci 2 Queuing System</h1>
    </div>

    <a href="customer_service.php">
            <button class="transaction-button">Select a transaction</button>
        </a>
    
    <!-- ✅ Date and time combined -->
    <div class="datetime" id="datetime">
        Thursday, September 2, 2023 (14:30:45)
    </div>
</div>

<script src="../js/time.js"></script>
</body>
</html>