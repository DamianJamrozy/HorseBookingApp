<?php
session_start();
include '../php/db.php';

// Check if the user is an administrator
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'administrator') {
    echo "Brak uprawnień";
    exit();
}

// Check if the user ID is set in the POST request
if (isset($_POST['id'])) {
    $user_id = intval($_POST['id']);

    // Delete the user from the database
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo "Sukces";
    } else {
        echo "Błąd: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Nieprawidłowe żądanie";
}

$conn->close();
?>
