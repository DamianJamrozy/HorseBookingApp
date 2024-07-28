<?php
include './db.php';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_POST['id'];

$sql = "UPDATE reservations SET reservation_status = 'aktywna' WHERE id='$id'";

if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
} else {
    echo "alert(" . $sql . "<br>" . $conn->error . ");";
}

$conn->close();
?>
