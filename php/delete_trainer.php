<!-- plik który usuwa trenera używany w admin  -->
<?php
session_start();
include '../php/db.php';

// Check if the user is logged in as an administrator
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'administrator') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $trainer_id = $_POST['id'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Delete trainer from trainers table
        $sql = "DELETE FROM trainers WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $trainer_id);
        $stmt->execute();
        $stmt->close();

        // Delete user from users table
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $trainer_id);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();
        
        echo json_encode(['status' => 'success', 'message' => 'Trener został usunięty.']);
    } catch (Exception $e) {
        // Rollback transaction if any query fails
        $conn->rollback();
        
        echo json_encode(['status' => 'error', 'message' => 'Wystąpił błąd podczas usuwania trenera.']);
    }
}

$conn->close();
?>

