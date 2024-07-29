<?php
include './db.php';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];

$sql = "SELECT id, imie, nazwisko FROM users WHERE rola = 'trener' AND id NOT IN (
    SELECT trener_id FROM reservations WHERE ('$start_time' BETWEEN data_rezerwacji_od AND data_rezerwacji_do)
    OR ('$end_time' BETWEEN data_rezerwacji_od AND data_rezerwacji_do)
    OR (data_rezerwacji_od BETWEEN '$start_time' AND '$end_time')
    OR (data_rezerwacji_do BETWEEN '$start_time' AND '$end_time')
)";
$result = $conn->query($sql);

$options = "";
while ($row = $result->fetch_assoc()) {
    $options .= "<option value='" . $row['id'] . "'>" . $row['imie'] . " " . $row['nazwisko'] . "</option>";
}

echo $options;

$conn->close();
?>
