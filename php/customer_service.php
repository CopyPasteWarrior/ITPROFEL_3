<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: customer_login.php");
    exit();
}

// Get all transactions from database
$stmt = $pdo->query("SELECT * FROM transactions");
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/customer_service.css">
    <link rel="icon" type="image/png" href="../imgs/moelci_logo.png">
    <title>MOELCI II Service Confirmation</title>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <div class="logo-circle">
                <img src="../imgs/moelci_logo.png" alt="Company Logo" class="logo-image" />
            </div>
        </div>
        
        <div class="buttons-container">
            <?php foreach ($transactions as $transaction): ?>
            <div class="<?php echo $transaction['name'] === 'Payment' ? 'payment-button' : ''; ?>">
                <form action="customer_details.php" method="POST">
                    <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>">
                    <button type="submit" class="menu-button"><?php echo $transaction['name']; ?></button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!--<script>
        document.querySelectorAll('.menu-button').forEach(button => {
            button.addEventListener('click', function() {
                // Visual feedback for button press
                this.style.transform = 'scale(0.98)';
                this.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.1)';
                
                setTimeout(() => {
                    this.style.transform = '';
                    this.style.boxShadow = '';
                    alert(`You selected: ${this.textContent}`);
                }, 150);
            });
        });
    </script> -->
</body>
</html>