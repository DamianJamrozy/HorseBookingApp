<?php
session_start();
include '../php/db.php';

// Check if the user is logged in as an administrator
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'administrator') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'], $_POST['stopien_jezdziecki'])) {
    $user_id = intval($_POST['user_id']);
    $stopien_jezdziecki = htmlspecialchars($_POST['stopien_jezdziecki']);

    // Prepare and execute the update query
    $sql = "UPDATE users SET stopien_jezdziecki = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $stopien_jezdziecki, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Stopień jeździecki został zaktualizowany.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Wystąpił błąd podczas aktualizacji.']);
    }

    $stmt->close();
}

$conn->close();
?>
