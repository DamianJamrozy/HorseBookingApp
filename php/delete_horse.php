<?php
include '../php/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $horse_id = $_POST['id'];
	

    // Sprawdzenie połączenia
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Zapytanie SQL do usunięcia konia
    $query = "DELETE FROM horses WHERE id = ?";
	
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $horse_id);

    if ($stmt->execute()) {
        echo "Sukces";
    } else {
        echo "Błąd: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Niewłaściwa metoda żądania";
}
?>
