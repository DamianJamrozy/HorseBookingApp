<?php
session_start();
include '../php/db.php';

// Check if the user is an administrator
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'administrator') {
    echo "Brak uprawnień";
    exit();
}

// Check if the user ID is set in the POST request
if (isset($_POST['id']) && isset($_POST['stopien_jezdziecki'])) {
    $user_id = intval($_POST['id']);
    $stopien_jezdziecki = $_POST['stopien_jezdziecki'];

    // Update the user in the database
    $sql = "UPDATE users SET stopien_jezdziecki = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $stopien_jezdziecki, $user_id);

    if ($stmt->execute()) {
        echo "Sukces";
    } else {
        echo "Błąd: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Nieprawidłowe żądanie";
}

$conn->close();
?>

