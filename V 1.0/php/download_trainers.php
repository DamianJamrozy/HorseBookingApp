<!-- plik który opbiera trenerów z user  -->
<?php
include '../php/db.php';

$sql = "SELECT u.imie AS imie, u.nazwisko AS nazwisko, u.zdjecie AS zdjecie, u.stopien_jezdziecki AS stopien
        FROM users u
        JOIN trainers tr ON u.id = tr.user_id";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo '<div class="trainers-data">
            <h2>Trenerzy</h2>
            <table class="trainers-table styled-table">
                <thead>
                    <tr>
                        <th>Zdjęcie</th>
                        <th>Imię</th>
                        <th>Nazwisko</th>
                        <th>Stopień jeździecki</th>
                    </tr>
                </thead>
                <tbody>';
    while ($row = $result->fetch_assoc()) {
        echo '<tr>
                <td><img src="' . htmlspecialchars($row['zdjecie']) . '" alt="Zdjęcie trenera" width="100"></td>
                <td>' . htmlspecialchars($row['imie']) . '</td>
                <td>' . htmlspecialchars($row['nazwisko']) . '</td>
                <td>' . htmlspecialchars($row['stopien']) . '</td>
              </tr>';
    }
    echo '</tbody></table></div>';
} else {
    echo "Brak danych o trenerach.";
}

$conn->close();
