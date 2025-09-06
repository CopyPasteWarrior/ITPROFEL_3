<?php
// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'queue_system');

// Connect to MySQL database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get current queue data
function getQueueData($conn) {
    $data = [];
    
    // Get waiting queue (limit to 30)
    $sql = "SELECT queue_number FROM queue 
            WHERE status = 'waiting' 
            ORDER BY created_at ASC 
            LIMIT 30";
    $result = $conn->query($sql);
    
    $data['waiting'] = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $data['waiting'][] = $row['queue_number'];
        }
    }
    
    // Get currently serving tickets with teller info
    $sql = "SELECT q.queue_number, t.name as teller_name 
            FROM queue q 
            LEFT JOIN tellers t ON q.teller_id = t.id 
            WHERE q.status = 'serving' 
            ORDER BY t.id";
    $result = $conn->query($sql);
    
    $data['serving'] = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $data['serving'][] = [
                'queue_number' => $row['queue_number'],
                'teller_name' => $row['teller_name']
            ];
        }
    }
    
    // Get the main "Now Serving" (first in serving queue)
    $data['now_serving'] = count($data['serving']) > 0 ? $data['serving'][0]['queue_number'] : '---';
    
    return $data;
}

// Get queue data
$queueData = getQueueData($conn);

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/tv_display.css">
  <link rel="icon" type="image/png" href="../imgs/moelci_logo.png">
  <title>Moelci II Public Display</title>
 
</head>
<body>
  <div class="container">
    <!-- Left Panel -->
    <div class="queue-list">
      <h3>Queue List</h3>
      <div class="queue-columns">
        <div class="queue-column">
          <?php
          // Display first 15 waiting queue numbers
          for ($i = 0; $i < 15 && $i < count($queueData['waiting']); $i++) {
              echo "<div>{$queueData['waiting'][$i]}</div>";
          }
          ?>
        </div>
        <div class="queue-column">
          <?php
          // Display next 15 waiting queue numbers
          for ($i = 15; $i < 30 && $i < count($queueData['waiting']); $i++) {
              echo "<div>{$queueData['waiting'][$i]}</div>";
          }
          ?>
        </div>
      </div>
    </div>

    <!-- Right Panel -->
    <div class="now-serving">
        <div class="section">
          <div class="logo"></div>
          <h2>Now Serving</h2>
          <div class="current-ticket"><?php echo $queueData['now_serving']; ?></div>
          <div class="teller-text">at Teller 1</div>
        </div>

      <div class="teller-counters">
        <?php
        // Display up to 4 teller counters
        $tellers = [1 => 'Teller 1', 2 => 'Teller 2', 3 => 'Teller 3', 4 => 'Teller 4'];
        
        foreach ($tellers as $id => $name) {
            $ticket = '---';
            // Find if this teller has a serving ticket
            foreach ($queueData['serving'] as $serving) {
                if (strpos($serving['teller_name'], (string)$id) !== false) {
                    $ticket = $serving['queue_number'];
                    break;
                }
            }
            
            echo "<div class='counter-box'>";
            echo "<div class='ticket'>$ticket</div>";
            echo "<div>$name</div>";
            echo "</div>";
        }
        ?>
      </div>
    </div>
  </div>
</body>
</html>