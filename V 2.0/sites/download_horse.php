<?php
include '../scripts/db.php';

// Pobranie listy koni
$sql = "SELECT * FROM horses";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konie</title>
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

    <div class="horses-data">
        <h2 class="title">Lista Konii</h2>
        <table class="horses-table styled-table">
            <thead>
                <tr>
                    <th>Imię</th>
                    <th>Wiek</th>
                    <th>Rasa</th>
                    <th>Stan Zdrowia</th>
                    <th>Rodzaj Konia</th>
                    <th>Opis</th>
                    <th>Zdjęcie</th>
                    <th>Edytuj</th>
                    <th>Usuń</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['imie']); ?></td>
                            <td><?php echo htmlspecialchars($row['wiek']); ?></td>
                            <td><?php echo htmlspecialchars($row['rasa']); ?></td>
                            <td><?php echo htmlspecialchars($row['stan_zdrowia']); ?></td>
                            <td><?php echo htmlspecialchars($row['rodzaj_konia']); ?></td>
                            <td><?php echo htmlspecialchars($row['opis']); ?></td>
                            <td><img src="../<?php echo htmlspecialchars($row['zdjecie']); ?>" alt="Zdjęcie konia" width="100"></td>
                            <td>
                                <button class="edit-button table-button" onclick='showEditModal(<?php echo json_encode($row); ?>)'>
                                    Edytuj
                                </button>
                            </td>
                            <td>
                                <form method="post" action="../scripts/crud_horses.php" style="display:inline;">
                                    <input type="hidden" name="delete_horse" value="1">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <button type="submit" class="table-button" onclick="return confirm('Czy na pewno chcesz usunąć tego konia?')">Usuń</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">Brak danych o koniach.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <button id="add-horse-button" onclick="showAddModal()" class="table-button">Dodaj Konia</button>
    </div>

    <!-- Modal do dodania konia -->
    <div id="add-horse-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('add-horse-modal')">&times;</span>
            <h3>Dodaj Konia</h3>
            <form method="post" action="../scripts/crud_horses.php">
                <input type="hidden" name="add_horse" value="1">
                <div class="form-group">
                    <label for="imie">Imię:</label>
                    <input type="text" id="imie" name="imie" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="wiek">Wiek:</label>
                    <input type="text" id="wiek" name="wiek" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="rasa">Rasa:</label>
                    <input type="text" id="rasa" name="rasa" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="stan_zdrowia">Stan Zdrowia:</label>
                    <input type="text" id="stan_zdrowia" name="stan_zdrowia" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="rodzaj_konia">Rodzaj Konia:</label>
                    <input type="text" id="rodzaj_konia" name="rodzaj_konia" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="opis">Opis:</label>
                    <textarea id="opis" name="opis" class="form-control" required></textarea>
                </div>
                <div class="form-group">
                    <label for="zdjecie">Zdjęcie URL:</label>
                    <input type="text" id="zdjecie" name="zdjecie" class="form-control">
                </div>
                <button type="submit" class="table-button flexend">Zapisz</button>
            </form>
        </div>
    </div>

    <!-- Modal do edycji konia -->
    <div id="edit-horse-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('edit-horse-modal')">&times;</span>
            <h3>Edytuj Konia</h3>
            <form method="post" action="../scripts/crud_horses.php">
                <input type="hidden" name="edit_horse" value="1">
                <input type="hidden" id="edit_horse_id" name="id">
                <div class="form-group">
                    <label for="edit_imie">Imię:</label>
                    <input type="text" id="edit_imie" name="imie" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit_wiek">Wiek:</label>
                    <input type="text" id="edit_wiek" name="wiek" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit_rasa">Rasa:</label>
                    <input type="text" id="edit_rasa" name="rasa" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit_stan_zdrowia">Stan Zdrowia:</label>
                    <input type="text" id="edit_stan_zdrowia" name="stan_zdrowia" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit_rodzaj_konia">Rodzaj Konia:</label>
                    <input type="text" id="edit_rodzaj_konia" name="rodzaj_konia" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit_opis">Opis:</label>
                    <textarea id="edit_opis" name="opis" class="form-control" required></textarea>
                </div>
                <div class="form-group">
                    <label for="edit_zdjecie">Zdjęcie URL:</label>
                    <input type="text" id="edit_zdjecie" name="zdjecie" class="form-control">
                </div>
                <button type="submit" class="table-button flexend">Zapisz zmiany</button>
            </form>
        </div>
    </div>

    <script>
        function showAddModal() {
            document.getElementById('add-horse-modal').style.display = 'block';
        }

        function showEditModal(horse) {
            document.getElementById('edit_horse_id').value = horse.id;
            document.getElementById('edit_imie').value = horse.imie;
            document.getElementById('edit_wiek').value = horse.wiek;
            document.getElementById('edit_rasa').value = horse.rasa;
            document.getElementById('edit_stan_zdrowia').value = horse.stan_zdrowia;
            document.getElementById('edit_rodzaj_konia').value = horse.rodzaj_konia;
            document.getElementById('edit_opis').value = horse.opis;
            document.getElementById('edit_zdjecie').value = horse.zdjecie;
            document.getElementById('edit-horse-modal').style.display = 'block';
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
