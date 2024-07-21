<?php
session_start();
include './db.php';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$klient_id = $_POST['klient_id'];
$kon_id = $_POST['kon_id'];
$trener_id = $_POST['trener_id'];
$data_rezerwacji_od = $_POST['data_rezerwacji_od'];
$data_rezerwacji_do = $_POST['data_rezerwacji_do'];

// Validate and format dates
if (strtotime($data_rezerwacji_od) === false || strtotime($data_rezerwacji_do) === false) {
    die("Invalid date format");
}

$data_rezerwacji_od = date('Y-m-d H:i:s', strtotime($data_rezerwacji_od));
$data_rezerwacji_do = date('Y-m-d H:i:s', strtotime($data_rezerwacji_do));

$sql = "INSERT INTO reservations (klient_id, kon_id, trener_id, data_rezerwacji_od, data_rezerwacji_do, reservation_status) VALUES ('$klient_id', '$kon_id', '$trener_id', '$data_rezerwacji_od', '$data_rezerwacji_do', 'aktywna')";

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
