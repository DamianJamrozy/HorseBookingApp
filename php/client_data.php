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
    echo '<div class="client-data">
            <h2>Dane Klienta</h2>
            <table class="trainers-table styled-table">
                <thead>
                    <tr>
                        <th>Imię</th>
                        <th>Nazwisko</th>
                        <th>Email</th>
                        <th>Ulica</th>
                        <th>Kod pocztowy</th>
                        <th>Miasto</th>
                        <th>Telefon</th>
                        <th>Stopień jeździecki</th>
                        <th>Zdjęcie</th>
                    </tr>
                </thead>
                <tbody>';
    while ($row = $result->fetch_assoc()) {

    echo '<tr><td>' . htmlspecialchars($row['imie']) . '</td>
               <td>' . htmlspecialchars($row['nazwisko']) . '</td>
                <td>' . htmlspecialchars($row['email']) . '</td>
               <td>' . htmlspecialchars($row['ulica']) . ' ' . htmlspecialchars($row['nr_domu']) . '</td>
                <td>' . htmlspecialchars($row['kod_pocztowy']) . '</td>
                <td>' . htmlspecialchars($row['miasto']) . '</td>
                <td>' . htmlspecialchars($row['telefon']) . '</td>
                <td>' . htmlspecialchars($row['stopien_jezdziecki']) . '</td>
                <td><img src="../' . htmlspecialchars($row['zdjecie']) . '" alt="Zdjęcie użytkownika" style="max-width: 100px; height: auto;"></td></tr>';
    }
    echo '</tbody></table></div>';
} else {
    echo "Brak danych użytkownika.";
}
$conn->close();
