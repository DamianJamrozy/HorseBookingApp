<!-- plik który pobiera dane klienta i używany jest do client_panel do klikniecia w dane   -->
<?php
session_start();
include '../php/db.php';

// Sprawdzenie, czy użytkownik jest zalogowany jako klient
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'klient') {
    header("Location: ../index.php");
    exit();
}

// Pobranie danych użytkownika
$user_id = $_SESSION['user_id'];
$sql = "SELECT imie, nazwisko, email, ulica, nr_domu, kod_pocztowy, miasto, telefon, zdjecie, stopien_jezdziecki FROM users WHERE id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    echo '<div class="client-data">
            <h2>Dane Klienta</h2>
            <table class="client-table">
                <tr><th>Imię:</th><td>' . htmlspecialchars($row['imie']) . '</td></tr>
                <tr><th>Nazwisko:</th><td>' . htmlspecialchars($row['nazwisko']) . '</td></tr>
                <tr><th>Email:</th><td>' . htmlspecialchars($row['email']) . '</td></tr>
                <tr><th>Ulica:</th><td>' . htmlspecialchars($row['ulica']) . ' ' . htmlspecialchars($row['nr_domu']) . '</td></tr>
                <tr><th>Kod pocztowy:</th><td>' . htmlspecialchars($row['kod_pocztowy']) . '</td></tr>
                <tr><th>Miasto:</th><td>' . htmlspecialchars($row['miasto']) . '</td></tr>
                <tr><th>Telefon:</th><td>' . htmlspecialchars($row['telefon']) . '</td></tr>
                <tr><th>Stopień jeździecki:</th><td>' . htmlspecialchars($row['stopien_jezdziecki']) . '</td></tr>
                <tr><th>Zdjęcie:</th><td><img src="../' . htmlspecialchars($row['zdjecie']) . '" alt="Zdjęcie użytkownika" style="max-width: 100px; height: auto;"></td></tr>
            </table>
          </div>';
} else {
    echo "Brak danych użytkownika.";
}
$conn->close();
