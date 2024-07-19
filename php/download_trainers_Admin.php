<!-- plik który pobiera dane trenerów w panelu administratora  -->

<?php
session_start();
include '../php/db.php';

// Funkcja do zabezpieczania danych przed wstrzykiwaniem SQL
function sanitize($conn, $data)
{
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags($data)));
}

// Obsługa zapisu trenera do bazy danych
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['imie'], $_POST['nazwisko'], $_POST['email'], $_POST['ulica'], $_POST['nr_domu'], $_POST['kod_pocztowy'], $_POST['miasto'], $_POST['telefon'], $_POST['hashed_password'], $_POST['stopien_jezdziecki'])) {
    $imie = sanitize($conn, $_POST['imie']);
    $nazwisko = sanitize($conn, $_POST['nazwisko']);
    $email = sanitize($conn, $_POST['email']);
    $ulica = sanitize($conn, $_POST['ulica']);
    $nr_domu = sanitize($conn, $_POST['nr_domu']);
    $kod_pocztowy = sanitize($conn, $_POST['kod_pocztowy']);
    $miasto = sanitize($conn, $_POST['miasto']);
    $telefon = sanitize($conn, $_POST['telefon']);
    $zdjecie = sanitize($conn, $_POST['zdjecie']); // Opcjonalne
    $hashed_password = password_hash($_POST['hashed_password'], PASSWORD_DEFAULT);
    $stopien_jezdziecki = sanitize($conn, $_POST['stopien_jezdziecki']);

    $sql_insert_user = "INSERT INTO users (imie, nazwisko, email, ulica, nr_domu, kod_pocztowy, miasto, telefon, zdjecie, hashed_password, stopien_jezdziecki)
                        VALUES ('$imie', '$nazwisko', '$email', '$ulica', '$nr_domu', '$kod_pocztowy', '$miasto', '$telefon', '$zdjecie', '$hashed_password', '$stopien_jezdziecki')";

    if ($conn->query($sql_insert_user) === TRUE) {
        $last_id = $conn->insert_id;

        $sql_insert_trainer = "INSERT INTO trainers (user_id) VALUES ('$last_id')";
        if ($conn->query($sql_insert_trainer) === TRUE) {
            // Powodzenie
            echo json_encode(['status' => 'success', 'message' => 'Trener został dodany.']);
        } else {
            // Błąd wstawiania do tabeli trainers
            echo json_encode(['status' => 'error', 'message' => 'Błąd podczas dodawania trenera.']);
        }
    } else {
        // Błąd wstawiania do tabeli users
        echo json_encode(['status' => 'error', 'message' => 'Błąd podczas dodawania użytkownika.']);
    }
    exit();
}

// Pobranie listy trenerów po dodaniu nowego 
//<td><img src="' . htmlspecialchars($row['zdjecie']) . '" alt="Zdjęcie trenera" width="100"></td>
$sql = "SELECT u.id, u.imie, u.nazwisko, u.zdjecie, u.stopien_jezdziecki
        FROM users u
        JOIN trainers tr ON u.id = tr.user_id";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo '<div class="trainers-data">';
    echo '<h2>Trenerzy</h2>';
    echo '<table class="trainers-table styled-table">
            <thead>
                <tr>
                    <th>Zdjęcie</th>
                    <th>Imię</th>
                    <th>Nazwisko</th>
                    <th>Stopień jeździecki</th>
                    <th>Edytuj</th>
                    <th>Usuń</th>
                </tr>
            </thead>
            <tbody>';
    while ($row = $result->fetch_assoc()) {
        echo '<tr>
                <td><img src="../' . htmlspecialchars($row['zdjecie']) . '" alt="Zdjęcie trenera" width="100"></td>
                <td>' . htmlspecialchars($row['imie']) . '</td>
                <td>' . htmlspecialchars($row['nazwisko']) . '</td>
                <td>' . htmlspecialchars($row['stopien_jezdziecki']) . '</td>
                <td><button class="edit-button table-button" data-id="' . htmlspecialchars($row['id']) . '">Edytuj</button></td>
                <td><button class="delete-button table-button" data-id="' . htmlspecialchars($row['id']) . '">Usuń</button></td>
              </tr>';
    }
    echo '</tbody></table>';
    echo '<button id="add-trainer-button" class="button-add table-button">Dodaj trenera</button>'; // Przycisk Dodaj trenera
    echo '</div>';
} else {
    echo "Brak danych o trenerach.";
}

$conn->close();
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Handle add trainer button click
        $('#add-trainer-button').click(function() {
            $('.trainers-data').hide();
            $('#add-trainer-form').show();
        });

        // Handle edit button click
        $(document).on('click', '.edit-button', function() {
            var userId = $(this).data('id');
            var currentLevel = $(this).closest('tr').find('td:eq(3)').text().trim();

            // Populate the edit form with the current values
            $('#edit_user_id').val(userId);
            $('#edit_stopien_jezdziecki').val(currentLevel);
            $('#editModal').show();
        });

        // Handle edit form submission
        $('#edit-form').submit(function(event) {
            event.preventDefault();
            $.ajax({
                url: 'edit_trainer.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.status === 'success') {
                        alert(result.message);
                        location.reload();
                    } else {
                        alert(result.message);
                    }
                },
                error: function() {
                    alert('Wystąpił błąd podczas zapisywania zmian.');
                }
            });
        });

        // Close modal
        $('#closeModal').click(function() {
            $('#editModal').hide();
        });

        // Handle delete button click
        $('.delete-button').click(function() {
            var trainerId = $(this).data('id');
            if (confirm('Czy na pewno chcesz usunąć tego trenera?')) {
                $.ajax({
                    url: 'delete_trainer.php',
                    type: 'POST',
                    data: { id: trainerId },
                    success: function(response) {
                        alert('Trener został usunięty.');
                        location.reload();
                    },
                    error: function() {
                        alert('Wystąpił błąd podczas usuwania trenera.');
                    }
                });
            }
        });

        // Handle add trainer form submission
        $('#trainer-form').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: 'add_trainer.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.status === 'success') {
                        alert(result.message);
                        location.reload();
                    } else {
                        alert(result.message);
                    }
                },
                error: function() {
                    alert('Wystąpił błąd podczas dodawania trenera.');
                }
            });
        });
    });
</script>


<!-- Formularz dodawania trenera -->
<div id="add-trainer-form" class="add-trainer-form" style="display: none; margin-top: 20px;">
    <h3>Dodaj Trenera</h3>
    <form id="trainer-form">
        <div class="form-group">
            <label for="imie">Imię:</label>
            <input type="text" id="imie" name="imie" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="nazwisko">Nazwisko:</label>
            <input type="text" id="nazwisko" name="nazwisko" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="ulica">Ulica:</label>
            <input type="text" id="ulica" name="ulica" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="nr_domu">Nr domu:</label>
            <input type="text" id="nr_domu" name="nr_domu" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="kod_pocztowy">Kod pocztowy:</label>
            <input type="text" id="kod_pocztowy" name="kod_pocztowy" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="miasto">Miasto:</label>
            <input type="text" id="miasto" name="miasto" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="telefon">Telefon:</label>
            <input type="text" id="telefon" name="telefon" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="zdjecie">Zdjęcie URL:</label>
            <input type="text" id="zdjecie" name="zdjecie" class="form-control">
        </div>

        <div class="form-group">
            <label for="hashed_password">Hasło:</label>
            <input type="password" id="hashed_password" name="hashed_password" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="stopien_jezdziecki">Stopień jeździecki:</label>
            <select id="stopien_jezdziecki" name="stopien_jezdziecki" class="form-control">
                <option value="początkujący">Początkujący</option>
                <option value="średniozaawansowany">Średniozaawansowany</option>
                <option value="zaawansowany">Zaawansowany</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Zapisz</button>
    </form>
</div>




<!-- Edit Trainer Modal -->
<div id="editModal" style="display: none;">
    <h3>Edytuj Stopień Jeździecki</h3>
    <form id="edit-form">
        <input type="hidden" id="edit_user_id" name="user_id">
        <div class="form-group">
            <label for="edit_stopien_jezdziecki">Stopień jeździecki:</label>
            <select id="edit_stopien_jezdziecki" name="stopien_jezdziecki" class="form-control">
                <option value="zaawansowany">Zaawansowany</option>
                <option value="średniozaawansowany">Średniozaawansowany</option>
                <option value="początkujący">Początkujący</option>
            </select>
        </div>
        <button type="submit" class="table-button">Zapisz zmiany</button>
        <button type="button" id="closeModal" class="btn btn-secondary table-button">Anuluj</button>
    </form>
</div>
