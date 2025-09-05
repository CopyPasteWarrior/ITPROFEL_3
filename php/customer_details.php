<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: customer_login.php");
    exit();
}

// Process transaction selection
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['transaction_id'])) {
    $transaction_id = $_POST['transaction_id'];
    
    // Get transaction details
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = ?");
    $stmt->execute([$transaction_id]);
    $transaction = $stmt->fetch();
    
    if ($transaction) {
        // Get the last queue number for this transaction
        $stmt = $pdo->prepare("SELECT queue_number FROM queue WHERE transaction_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$transaction_id]);
        $last_queue = $stmt->fetch();
        
        // Generate new queue number
        $last_number = $last_queue ? intval(substr($last_queue['queue_number'], 2)) : 0;
        $new_number = $last_number + 1;
        $queue_number = $transaction['prefix'] . '-' . str_pad($new_number, 4, '0', STR_PAD_LEFT);
        
        // Insert into queue
        $stmt = $pdo->prepare("INSERT INTO queue (transaction_id, queue_number) VALUES (?, ?)");
        $stmt->execute([$transaction_id, $queue_number]);
        
        $_SESSION['queue_number'] = $queue_number;
        $_SESSION['transaction_name'] = $transaction['name'];
    }
}

// Check if queue number exists in session
if (!isset($_SESSION['queue_number'])) {
    header("Location: customer_service.php");
    exit();
}

$queue_number = $_SESSION['queue_number'];
$transaction_name = $_SESSION['transaction_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Number</title>
    <link rel="stylesheet" href="../css/customer_details.css">
    <link rel="icon" type="image/png" href="../imgs/moelci_logo.png">
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->

</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-container">
                <img src="../imgs/moelci_logo.png" alt="Company Logo" class="logo-image" />
            </div>
        </div>
        
        <div class="ticket-container">
            <div class="ticket-title">YOUR NUMBER IS:</div>
            <div class="ticket-number"><?php echo $queue_number; ?></div>
            <div class="transaction-info">For: <?php echo $transaction_name; ?></div>
        </div>
        
        <div class="buttons-container">
            <button class="btn btn-print" onclick="window.print()">
                Print Ticket
            </button>
            <button class="btn btn-cancel" onclick="cancelQueue()">
                Cancel
            </button>
        </div>
    </div>

        <script>
        function cancelQueue() {
            if(confirm('Are you sure you want to cancel?')) {
                // In a real application, you would send an AJAX request to update the queue status
                alert('Your queue number has been cancelled.');
                window.location.href = 'customer_service.php';
            }
        }
    </script>
</body>
</html>