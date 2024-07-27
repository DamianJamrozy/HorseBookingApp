<?php
include '../scripts/db.php';

// Sprawdzenie, czy użytkownik jest zalogowany jako administrator lub trener
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'administrator' && $_SESSION['user_role'] !== 'trener')) {
    header("Location: index.php");
    exit();
}

// Pobranie wartości filtrów i sortowania
$rolaFilter = isset($_GET['rola']) ? $_GET['rola'] : '';
$stopienFilter = isset($_GET['stopien_jezdziecki']) ? $_GET['stopien_jezdziecki'] : '';
$imieFilter = isset($_GET['imie']) ? $_GET['imie'] : '';
$nazwiskoFilter = isset($_GET['nazwisko']) ? $_GET['nazwisko'] : '';
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Pobranie wszystkich użytkowników z bazy danych z uwzględnieniem filtrów i sortowania
$sql = "SELECT * FROM users WHERE 1=1";
if ($_SESSION['user_role'] == 'trener') {
    $sql .= " AND rola NOT LIKE '%trener%' AND rola NOT LIKE '%administrator%'";
}

if ($rolaFilter) {
    $sql .= " AND rola = '" . $conn->real_escape_string($rolaFilter) . "'";
}

if ($stopienFilter) {
    $sql .= " AND stopien_jezdziecki = '" . $conn->real_escape_string($stopienFilter) . "'";
}

if ($imieFilter) {
    $sql .= " AND imie LIKE '%" . $conn->real_escape_string($imieFilter) . "%'";
}

if ($nazwiskoFilter) {
    $sql .= " AND nazwisko LIKE '%" . $conn->real_escape_string($nazwiskoFilter) . "%'";
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
            <h2>Wszyscy użytkownicy</h2>

            <!-- Filter and Sort Form -->
            <form id="filter-form" method="GET" action="">
                <input type="hidden" name="page" value="client_data_All.php">
                
                <label for="imie">Imię:</label>
                <input type="text" name="imie" id="imie" value="<?= htmlspecialchars($imieFilter) ?>" onchange="this.form.submit()">

                <label for="nazwisko">Nazwisko:</label>
                <input type="text" name="nazwisko" id="nazwisko" value="<?= htmlspecialchars($nazwiskoFilter) ?>" onchange="this.form.submit()">

                <label for="rola">Rola:</label>
                <select name="rola" id="rola" onchange="this.form.submit()">
                    <option value="">Wszystkie</option>
                    <option value="administrator" <?= $rolaFilter == 'administrator' ? 'selected' : '' ?>>Administrator</option>
                    <option value="klient" <?= $rolaFilter == 'klient' ? 'selected' : '' ?>>Klient</option>
                </select>

                <label for="stopien_jezdziecki">Stopień jeździecki:</label>
                <select name="stopien_jezdziecki" id="stopien_jezdziecki" onchange="this.form.submit()">
                    <option value="">Wszystkie</option>
                    <option value="początkujący" <?= $stopienFilter == 'początkujący' ? 'selected' : '' ?>>Początkujący</option>
                    <option value="średniozaawansowany" <?= $stopienFilter == 'średniozaawansowany' ? 'selected' : '' ?>>Średniozaawansowany</option>
                    <option value="zaawansowany" <?= $stopienFilter == 'zaawansowany' ? 'selected' : '' ?>>Zaawansowany</option>
                </select>


                <span style="display:none;">
                    <label for="sort">Sortuj według:</label>
                    <select name="sort" id="sort" onchange="this.form.submit()">
                        <option value="id" <?= $sortColumn == 'id' ? 'selected' : '' ?>>ID</option>
                        <option value="imie" <?= $sortColumn == 'imie' ? 'selected' : '' ?>>Imię</option>
                        <option value="nazwisko" <?= $sortColumn == 'nazwisko' ? 'selected' : '' ?>>Nazwisko</option>
                        <option value="email" <?= $sortColumn == 'email' ? 'selected' : '' ?>>Email</option>
                        <option value="ulica" <?= $sortColumn == 'ulica' ? 'selected' : '' ?>>Ulica</option>
                        <option value="nr_domu" <?= $sortColumn == 'nr_domu' ? 'selected' : '' ?>>Nr domu</option>
                        <option value="kod_pocztowy" <?= $sortColumn == 'kod_pocztowy' ? 'selected' : '' ?>>Kod pocztowy</option>
                        <option value="miasto" <?= $sortColumn == 'miasto' ? 'selected' : '' ?>>Miasto</option>
                        <option value="telefon" <?= $sortColumn == 'telefon' ? 'selected' : '' ?>>Telefon</option>
                        <option value="rola" <?= $sortColumn == 'rola' ? 'selected' : '' ?>>Rola</option>
                        <option value="stopien_jezdziecki" <?= $sortColumn == 'stopien_jezdziecki' ? 'selected' : '' ?>>Stopień jeździecki</option>
                    </select>

                    <label for="order">Kolejność:</label>
                    <select name="order" id="order" onchange="this.form.submit()">
                        <option value="ASC" <?= $sortOrder == 'ASC' ? 'selected' : '' ?>>Rosnąco</option>
                        <option value="DESC" <?= $sortOrder == 'DESC' ? 'selected' : '' ?>>Malejąco</option>
                    </select>
                </span>
            </form>

            <table class="users-table styled-table">
                <thead>
                    <tr>
                        <th><a href="?page=client_data_All.php&sort=id&order=<?= $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>" class="sort-link">ID</a></th>
                        <th><a href="?page=client_data_All.php&sort=imie&order=<?= $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>" class="sort-link">Imię</a></th>
                        <th><a href="?page=client_data_All.php&sort=nazwisko&order=<?= $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>" class="sort-link">Nazwisko</a></th>
                        <th><a href="?page=client_data_All.php&sort=email&order=<?= $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>" class="sort-link">Email</a></th>
                        <th><a href="?page=client_data_All.php&sort=ulica&order=<?= $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>" class="sort-link">Ulica</a></th>
                        <th><a href="?page=client_data_All.php&sort=nr_domu&order=<?= $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>" class="sort-link">Nr domu</a></th>
                        <th><a href="?page=client_data_All.php&sort=kod_pocztowy&order=<?= $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>" class="sort-link">Kod pocztowy</a></th>
                        <th><a href="?page=client_data_All.php&sort=miasto&order=<?= $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>" class="sort-link">Miasto</a></th>
                        <th><a href="?page=client_data_All.php&sort=telefon&order=<?= $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>" class="sort-link">Telefon</a></th>
                        <th>Zdjęcie</th>
                        <th><a href="?page=client_data_All.php&sort=rola&order=<?= $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>" class="sort-link">Rola</a></th>
                        <th><a href="?page=client_data_All.php&sort=stopien_jezdziecki&order=<?= $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>" class="sort-link">Stopień jeździecki</a></th>
                        <?php if ($_SESSION['user_role'] == 'administrator') { ?>
                            <th>Edytuj</th>
                            <th>Usuń</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['imie']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['nazwisko']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['ulica']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['nr_domu']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['kod_pocztowy']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['miasto']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['telefon']) . '</td>';
                            echo '<td><img src="../' . htmlspecialchars($row['zdjecie']) . '" alt="Zdjęcie" class="user-image"></td>';
                            echo '<td>' . htmlspecialchars($row['rola']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['stopien_jezdziecki']) . '</td>';

                            if ($row['rola'] === 'klient' && $_SESSION['user_role'] == 'administrator') {
                                echo '<td>';
                                echo '<button class="edit-button table-button" onclick=\'showEditModal(' . json_encode($row) . ')\'>Edytuj</button>';
                                echo '</td>';
                                echo '<td>';
                                echo '<form method="post" action="../scripts/crud_users.php" style="display:inline;">';
                                echo '<input type="hidden" name="delete_user" value="1">';
                                echo '<input type="hidden" name="id" value="' . htmlspecialchars($row['id']) . '">';
                                echo '<button type="submit" class="table-button" onclick="return confirm(\'Czy na pewno chcesz usunąć tego użytkownika?\')">Usuń</button>';
                                echo '</form>';
                                echo '</td>';
                            } elseif ($row['rola'] != 'klient' && $_SESSION['user_role'] == 'administrator') {
                                echo '<td></td>'; // Empty cell for non-klient roles
                                echo '<td></td>'; // Empty cell for non-klient roles
                            }
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="15">Brak danych użytkowników.</td></tr>';
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
                <form method="post" action="../scripts/crud_users.php" enctype="multipart/form-data">
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
                        <label for="trainer_image">Zdjęcie:</label>
                        <div class="drop-zone form-control" id="drop-zone">
                            Przeciągnij lub wybierz zdjęcie...
                            <input type="file" name="trainer_image" id="file-input" style="display: none;">
                            <img id="preview-image" src="" alt="Preview Image" style="display:none; width: 100%; height: auto; margin-top: 10px;">
                        </div>
                        <input type="hidden" id="employee-id" name="employee_id" value="">
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
                <form id="edit-user-form" method="post" action="../scripts/crud_users.php" enctype="multipart/form-data">
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
                        <label for="edit_trainer_image">Zdjęcie:</label>
                        <div class="drop-zone form-control" id="edit-drop-zone">
                            Przeciągnij lub wybierz zdjęcie...
                            <input type="file" name="trainer_image" id="edit-file-input" style="display: none;">
                            <img id="edit-preview-image" src="" alt="Preview Image" style="display:none; width: 100%; height: auto; margin-top: 10px;">
                        </div>
                        <input type="hidden" id="edit-employee-id" name="employee_id" value="">
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
            document.getElementById('edit_user_id').value = user.id;
            document.getElementById('edit_imie').value = user.imie;
            document.getElementById('edit_nazwisko').value = user.nazwisko;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_ulica').value = user.ulica;
            document.getElementById('edit_nr_domu').value = user.nr_domu;
            document.getElementById('edit_kod_pocztowy').value = user.kod_pocztowy;
            document.getElementById('edit_miasto').value = user.miasto;
            document.getElementById('edit_telefon').value = user.telefon;
            document.getElementById('edit_rola').value = user.rola;
            document.getElementById('edit_stopien_jezdziecki').value = user.stopien_jezdziecki;
            document.getElementById('edit-user-modal').style.display = 'block';

            setImageSrc('edit-preview-image', '../' + user.zdjecie); // Ustawienie podglądu obrazu
        }

        function setImageSrc(imageId, imageUrl) {
            const imageElement = document.getElementById(imageId);
            if (imageElement) {
                imageElement.src = imageUrl;
                imageElement.style.display = 'block';
            }
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        window.onclick = function (event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

        // Skrypt dla zdjęć w modalach
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-input');
        const employeeIdInput = document.getElementById('employee-id');
        const previewImage = document.getElementById('preview-image');

        dropZone.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                employeeIdInput.value = file.name.split('.')[0]; // Assuming file name contains the employee id

                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            if (e.dataTransfer.files.length > 0) {
                const file = e.dataTransfer.files[0];
                fileInput.files = e.dataTransfer.files;
                employeeIdInput.value = file.name.split('.')[0]; // Assuming file name contains the employee id

                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        // Skrypt dla edycji zdjęć w modalach
        const editDropZone = document.getElementById('edit-drop-zone');
        const editFileInput = document.getElementById('edit-file-input');
        const editEmployeeIdInput = document.getElementById('edit-employee-id');
        const editPreviewImage = document.getElementById('edit-preview-image');

        editDropZone.addEventListener('click', () => editFileInput.click());

        editFileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                editEmployeeIdInput.value = file.name.split('.')[0]; // Assuming file name contains the employee id

                const reader = new FileReader();
                reader.onload = function (e) {
                    editPreviewImage.src = e.target.result;
                    editPreviewImage.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        editDropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            editDropZone.classList.add('dragover');
        });

        editDropZone.addEventListener('dragleave', () => {
            editDropZone.classList.remove('dragover');
        });

        editDropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            editDropZone.classList.remove('dragover');
            if (e.dataTransfer.files.length > 0) {
                const file = e.dataTransfer.files[0];
                editFileInput.files = e.dataTransfer.files;
                editEmployeeIdInput.value = file.name.split('.')[0]; // Assuming file name contains the employee id

                const reader = new FileReader();
                reader.onload = function (e) {
                    editPreviewImage.src = e.target.result;
                    editPreviewImage.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>
