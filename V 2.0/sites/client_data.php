<?php
include '../scripts/db.php';

// Sprawdzenie, czy użytkownik jest zalogowany jako administrator
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'klient')){ //) {
    header("Location: index.php");
    exit();
}

// Pobranie wartości filtrów i sortowania
$rolaFilter = isset($_GET['rola']) ? $_GET['rola'] : '';
$stopienFilter = isset($_GET['stopien_jezdziecki']) ? $_GET['stopien_jezdziecki'] : '';
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Pobranie wszystkich użytkowników z bazy danych z uwzględnieniem filtrów i sortowania
if ($_SESSION['user_role'] == 'trener'){  
    $sql = "SELECT * FROM users WHERE 1=1 AND id = '".$_SESSION['user_id']."'";
}
else{
    $sql = "SELECT * FROM users WHERE 1=1 AND id = '".$_SESSION['user_id']."'";
}

if ($rolaFilter) {
    $sql .= " AND rola = '" . $conn->real_escape_string($rolaFilter) . "'";
}

if ($stopienFilter) {
    $sql .= " AND stopien_jezdziecki = '" . $conn->real_escape_string($stopienFilter) . "'";
}

$sql .= " ORDER BY " . $conn->real_escape_string($sortColumn) . " " . $conn->real_escape_string($sortOrder);

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wszyscy użytkownicy</title>
    <link rel="stylesheet" href="../style/general.css">
    <link rel="stylesheet" href="../style/styles.css">
</head>

<body>
    <main>
        <div class="user-data">
            
            <table class="users-table styled-table client-table">
                <thead>
                    <tr>
                        <th>Login</th>
                        <th class='img_client'>Zdjęcie</th>
                        <th>Imię</th>
                        <th>Nazwisko</th>
                        <th>Email</th>
                        <th>Ulica</th>
                        <th>Nr domu</th>
                        <th>Kod pocztowy</th>
                        <th>Miasto</th>
                        <th>Telefon</th>
                        <th>Stopień jeździecki</th>
                        <th>Edytuj</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                            echo '<td class="img_client"><img src="../' . htmlspecialchars($row['zdjecie']) . '" alt="Zdjęcie" class="user-image"></td>';
                            echo '<td>' . htmlspecialchars($row['imie']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['nazwisko']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['ulica']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['nr_domu']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['kod_pocztowy']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['miasto']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['telefon']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['stopien_jezdziecki']) . '</td>';
                            echo '<td>';
                            echo '<button class="edit-button table-button" onclick="showEditModal(' . htmlspecialchars(json_encode($row)) . ')">Edytuj</button>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="15">Error: Brak danych użytkownika. Skontaktuj się z administratorem systemu.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
            <?php
            if ($_SESSION['user_role'] === 'administrator') {
                echo '<button id="add-user-button" class="add-button table-button">Dodaj użytkownika</button>';
            }
            ?>
        </div>

        <!-- Modal do dodawania użytkownika -->
        <div id="add-user-modal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('add-user-modal')">&times;</span>
                <h3>Dodaj użytkownika</h3>
                <form method="post" action="../scripts/crud_users.php">
                    <input type="hidden" name="add_user" value="1">
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
                        <label for="rola">Rola:</label>
                        <select id="rola" name="rola" class="form-control" required>
                            <option value="klient">Klient</option>
                            <option value="administrator">Administrator</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="stopien_jezdziecki">Stopień jeździecki:</label>
                        <select id="stopien_jezdziecki" name="stopien_jezdziecki" class="form-control" required>
                            <option value="początkujący">Początkujący</option>
                            <option value="średniozaawansowany">Średniozaawansowany</option>
                            <option value="zaawansowany">Zaawansowany</option>
                        </select>
                    </div>
                     <div class="form-group">
                        <label for="hashed_password">Hasło:</label>
                        <input type="password" id="hashed_password" name="hashed_password" class="form-control" required>
                    </div>
                    <button type="submit" class="table-button">Zapisz</button>
                    <button type="button" class="table-button" onclick="closeModal('add-user-modal')">Anuluj</button>
                </form>
            </div>
        </div>

        <!-- Modal do edycji użytkownika -->
        <div id="edit-user-modal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('edit-user-modal')">&times;</span>
                <h3>Edytuj użytkownika</h3>
                <form id="edit-user-form" method="post" action="../scripts/crud_users.php">
                    <input type="hidden" name="edit_user" >
                    <input type="hidden" id="edit_user_id" name="id">
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
                        <label for="edit_rola">Rola:</label>
                        <select id="edit_rola" name="rola" class="form-control" required>
                            <option value="administrator">Administrator</option>
                            <option value="klient">Klient</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_stopien_jezdziecki">Stopień jeździecki:</label>
                        <select id="edit_stopien_jezdziecki" name="stopien_jezdziecki" class="form-control" required>
                            <option value="początkujący">Początkujący</option>
                            <option value="średniozaawansowany">Średniozaawansowany</option>
                            <option value="zaawansowany">Zaawansowany</option>
                        </select>
                    </div>
                    <button type="submit" class="table-button">Zapisz zmiany</button>
                    <button type="button" class="table-button" onclick="closeModal('edit-user-modal')">Anuluj</button>
                </form>
            </div>
        </div>

    </main>
    <script>
        document.getElementById('add-user-button').onclick = function () {
            document.getElementById('add-user-modal').style.display = 'block';
        };

        function showEditModal(user) {
            console.log(user.id);
            document.getElementById('edit_user_id').value = user.id;
            document.getElementById('edit_imie').value = user.imie;
            document.getElementById('edit_nazwisko').value = user.nazwisko;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_ulica').value = user.ulica;
            document.getElementById('edit_nr_domu').value = user.nr_domu;
            document.getElementById('edit_kod_pocztowy').value = user.kod_pocztowy;
            document.getElementById('edit_miasto').value = user.miasto;
            document.getElementById('edit_telefon').value = user.telefon;
            document.getElementById('edit_zdjecie').value = user.zdjecie;
            document.getElementById('edit_rola').value = user.rola;
            document.getElementById('edit_stopien_jezdziecki').value = user.stopien_jezdziecki;
            document.getElementById('edit-user-modal').style.display = 'block';
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
<?php
$conn->close();
?>