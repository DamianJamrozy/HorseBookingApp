<?php
include '../scripts/db.php';


// Pobranie listy trenerów
$sql = "SELECT *
        FROM users u
        JOIN trainers tr ON u.id = tr.user_id";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trenerzy</title>
    <link rel="stylesheet" href="general.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <?php
    if (isset($_SESSION['message'])) {
        echo '<p class="message">' . $_SESSION['message'] . '</p>';
        unset($_SESSION['message']);
    }

    if (isset($_SESSION['error'])) {
        echo '<p class="error">' . $_SESSION['error'] . '</p>';
        unset($_SESSION['error']);
    }
    ?>

    <div class="trainers-data">
        <h2 class="title">Trenerzy</h2>
        <table class="trainers-table styled-table">
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
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><img src="../<?php echo htmlspecialchars($row['zdjecie']); ?>" alt="Zdjęcie trenera"
                                    width="100"></td>
                            <td><?php echo htmlspecialchars($row['imie']); ?></td>
                            <td><?php echo htmlspecialchars($row['nazwisko']); ?></td>
                            <td><?php echo htmlspecialchars($row['stopien_jezdziecki']); ?></td>
                            <td>
                                <button class="edit-button table-button" data-id="<?php echo htmlspecialchars($row['user_id']); ?>"
                                    onclick='showEditModal(<?php echo json_encode($row); ?>)'>
                                    Edytuj
                                </button>
                            </td>
                            <td>
                            <form method="post" action="../scripts/crud_trainers.php" style="display:inline;">
                                    <input type="hidden" name="delete_trainer" value="1">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['user_id']); ?>">
                                    <button type="submit" class="table-button" onclick="return confirm('Czy na pewno chcesz usunąć tego trenera?')">Usuń</button>
                                </form>

                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">Brak danych o trenerach.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <button id="add-trainer-button" onclick="showAddModal()" class="table-button">Dodaj trenera</button>
    </div>

    <!-- Modal do dodania trenera -->
    <div id="add-trainer-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('add-trainer-modal')">&times;</span>
            <h3>Dodaj Trenera</h3>
            <form method="post" action="../scripts/crud_trainers.php">
                <input type="hidden" name="add_trainer" value="1">
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
                    <label for="stopien_jezdziecki">Stopień jeździecki:</label>
                    <select id="stopien_jezdziecki" name="stopien_jezdziecki" class="form-control">
                        <option value="początkujący">Początkujący</option>
                        <option value="średniozaawansowany">Średniozaawansowany</option>
                        <option value="zaawansowany">Zaawansowany</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="hashed_password">Hasło:</label>
                    <input type="password" id="hashed_password" name="hashed_password" class="form-control" required>
                </div>
                <button type="submit" class="table-button flexend">Zapisz</button>
            </form>
        </div>
    </div>


    <!-- Modal do edycji trenera -->
    <div id="edit-trainer-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('edit-trainer-modal')">&times;</span>
            <h3>Edytuj Trenera</h3>
            <form method="post" action="../scripts/crud_trainers.php">
                <input type="hidden" name="edit_trainer" value="1">
                <input type="hidden" id="edit_user_id" name="user_id">
                <div class="form-group">
                    <label for="edit_imie">Imię:</label>
                    <input type="text" id="edit_imie" name="imie" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit_nazwisko">Nazwisko:</label>
                    <input type="text" id="edit_nazwisko" name="nazwisko" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit_email">Email:</label>
                    <input type="email" id="edit_email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit_ulica">Ulica:</label>
                    <input type="text" id="edit_ulica" name="ulica" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit_nr_domu">Nr domu:</label>
                    <input type="text" id="edit_nr_domu" name="nr_domu" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit_kod_pocztowy">Kod pocztowy:</label>
                    <input type="text" id="edit_kod_pocztowy" name="kod_pocztowy" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit_miasto">Miasto:</label>
                    <input type="text" id="edit_miasto" name="miasto" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit_telefon">Telefon:</label>
                    <input type="text" id="edit_telefon" name="telefon" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit_zdjecie">Zdjęcie URL:</label>
                    <input type="text" id="edit_zdjecie" name="zdjecie" class="form-control">
                </div>
                <div class="form-group">
                    <label for="edit_stopien_jezdziecki">Stopień jeździecki:</label>
                    <select id="edit_stopien_jezdziecki" name="stopien_jezdziecki" class="form-control">
                        <option value="początkujący">Początkujący</option>
                        <option value="średniozaawansowany">Średniozaawansowany</option>
                        <option value="zaawansowany">Zaawansowany</option>
                    </select>
                </div>
                <button type="submit" class="table-button flexend">Zapisz zmiany</button>
            </form>
        </div>
    </div>

    <script>
        function showAddModal() {
            document.getElementById('add-trainer-modal').style.display = 'block';
        }

        function showEditModal(trainer) {
            document.getElementById('edit_user_id').value = trainer.user_id;
            document.getElementById('edit_imie').value = trainer.imie;
            document.getElementById('edit_nazwisko').value = trainer.nazwisko;
            document.getElementById('edit_email').value = trainer.email;
            document.getElementById('edit_ulica').value = trainer.ulica;
            document.getElementById('edit_nr_domu').value = trainer.nr_domu;
            document.getElementById('edit_kod_pocztowy').value = trainer.kod_pocztowy;
            document.getElementById('edit_miasto').value = trainer.miasto;
            document.getElementById('edit_telefon').value = trainer.telefon;
            document.getElementById('edit_zdjecie').value = trainer.zdjecie;
            document.getElementById('edit_stopien_jezdziecki').value = trainer.stopien_jezdziecki;
            document.getElementById('edit-trainer-modal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        window.onclick = function (event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>

</body>

</html>