<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fan_control";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT * FROM temperature_data ORDER BY timestamp DESC LIMIT 1";
$result = $conn->query($sql);
$data = $result->fetch_assoc();
if (!$data) {
    $data = ['temperature' => 22, 'setMaxTemp' => 25, 'roomEmpty' => false];
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $setMaxTemp = $_POST['setMaxTemp'] ?? $data['setMaxTemp'];
    $roomEmpty = isset($_POST['roomEmpty']) ? 1 : 0;
    $currentTemp = rand(18, 28);
    $stmt = $conn->prepare("INSERT INTO temperature_data (temperature, setMaxTemp, roomEmpty) VALUES (?, ?, ?)");
    $stmt->bind_param("ddi", $currentTemp, $setMaxTemp, $roomEmpty);
    $stmt->execute();
    $stmt->close();
    header("Location: fanControl.php");
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Digital Temperature Control System for Fan Control">
    <meta name="keywords" content="tempp, temperature control, fan control, cooling, digital control system">
    <title>Temperature Control System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Digital Temperature Control System</h1>
        <div class="status">
            <p><strong>Current Temperature:</strong> <?php echo $data['temperature']; ?> °C</p>
            <p><strong>Fan Status:</strong> 
                <?php
                    if ($data['temperature'] <= $data['setMaxTemp']) {
                        echo "ON";
                    } else {
                        echo "OFF";
                    }
                ?>
            </p>
            <p><strong>Room Status:</strong> <?php echo $data['roomEmpty'] ? "Empty" : "Occupied"; ?></p>
        </div>
        <div class="controls">
            <h2>Set Maximum Temperature</h2>
            <form method="POST">
                <label>Max Temp: <input type="number" name="setMaxTemp" value="<?php echo $data['setMaxTemp']; ?>"> °C</label><br><br>
                <label>Room Empty: <input type="checkbox" name="roomEmpty" <?php echo $data['roomEmpty'] ? "checked" : ""; ?>></label><br><br>
                <button type="submit">Update Settings</button>
            </form>
        </div>
    </div>
</body>
</html>