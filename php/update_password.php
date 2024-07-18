<!-- plik który zarządza szyfrowaniem haseł -->
<?php
require_once "db.php";

// Pobranie istniejących użytkowników
$stmt = $conn->prepare("SELECT id, haslo FROM users");
$stmt->execute();
$result = $stmt->get_result();

// Przejście przez wszystkie rekordy i aktualizacja zahashowanego hasła
while ($row = $result->fetch_assoc()) {
    $user_id = $row['id'];
    $plain_password = $row['haslo'];
    $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

    // Aktualizacja rekordu z zahashowanym hasłem
    $update_stmt = $conn->prepare("UPDATE users SET hashed_password = ? WHERE id = ?");
    $update_stmt->bind_param('si', $hashed_password, $user_id);
    $update_stmt->execute();
}

// Usunięcie kolumny haslo, jeśli nie będzie już używana
// ALTER TABLE users DROP COLUMN haslo;

// Komunikat o zakończeniu aktualizacji
echo "Zaktualizowano hasła w bazie danych.";

// Zamknięcie połączenia
$stmt->close();
$conn->close();
