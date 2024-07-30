<?php
include '../scripts/db.php';

// Pobranie wartości filtrów i sortowania
$imieFilter = isset($_GET['imie']) ? $_GET['imie'] : '';
$nazwiskoFilter = isset($_GET['nazwisko']) ? $_GET['nazwisko'] : '';
$stopienFilter = isset($_GET['stopien_jezdziecki']) ? $_GET['stopien_jezdziecki'] : '';
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'u.id';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Pobranie listy trenerów z uwzględnieniem filtrów i sortowania
$sql = "SELECT u.*, ut.rola AS rola_nazwa, us.stopien_jezdziecki AS stopien_nazwa 
        FROM users u 
        INNER JOIN users_type ut ON u.rola = ut.id_type 
        INNER JOIN users_skill us ON u.stopien_jezdziecki = us.id_skill 
        WHERE ut.rola = 'trener'";

if ($imieFilter) {
    $sql .= " AND u.imie LIKE '%" . $conn->real_escape_string($imieFilter) . "%'";
}

if ($nazwiskoFilter) {
    $sql .= " AND u.nazwisko LIKE '%" . $conn->real_escape_string($nazwiskoFilter) . "%'";
}

if ($stopienFilter) {
    $sql .= " AND us.stopien_jezdziecki = '" . $conn->real_escape_string($stopienFilter) . "'";
}

$sql .= " ORDER BY " . $conn->real_escape_string($sortColumn) . " " . $conn->real_escape_string($sortOrder);

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trenerzy</title>
    <link rel="stylesheet" href="../style/general.css">
    <link rel="stylesheet" href="../style/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>


    <div class="trainers-data">
        <h2 class="title">Trenerzy</h2>
        
        <!-- Filter and Sort Form -->
        <form id="filter-form" method="GET" action="">
            <label for="imie">Imię:</label>
            <input type="text" name="imie" id="imie" value="<?= htmlspecialchars($imieFilter) ?>">
            
            <label for="nazwisko">Nazwisko:</label>
            <input type="text" name="nazwisko" id="nazwisko" value="<?= htmlspecialchars($nazwiskoFilter) ?>">
            
            <label for="stopien_jezdziecki">Stopień jeździecki:</label>
            <select name="stopien_jezdziecki" id="stopien_jezdziecki">
                <option value="">Wszystkie</option>
                <?php
                $skillsQuery = "SELECT * FROM users_skill";
                $skillsResult = $conn->query($skillsQuery);
                if ($skillsResult->num_rows > 0) {
                    while ($skill = $skillsResult->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($skill['id_skill']) . '">' . htmlspecialchars($skill['stopien_jezdziecki']) . '</option>';
                    }
                }
                ?>
            </select>

            <span style="display:none;">
                <label for="sort">Sortuj według:</label>
                <select name="sort" id="sort">
                    <option value="u.imie" <?= $sortColumn == 'u.imie' ? 'selected' : '' ?>>Imię</option>
                    <option value="u.nazwisko" <?= $sortColumn == 'u.nazwisko' ? 'selected' : '' ?>>Nazwisko</option>
                    <option value="u.stopien_jezdziecki" <?= $sortColumn == 'u.stopien_jezdziecki' ? 'selected' : '' ?>>Stopień jeździecki</option>
                </select>

                <label for="order">Kolejność:</label>
                <select name="order" id="order">
                    <option value="ASC" <?= $sortOrder == 'ASC' ? 'selected' : '' ?>>Rosnąco</option>
                    <option value="DESC" <?= $sortOrder == 'DESC' ? 'selected' : '' ?>>Malejąco</option>
                </select>
            </span>
        </form>

        <table class="trainers-table styled-table">
            <thead>
                <tr>
                    <th>Zdjęcie</th>
                    <th><a href="#" class="sort-link" data-column="u.imie">Imię</a></th>
                    <th><a href="#" class="sort-link" data-column="u.nazwisko">Nazwisko</a></th>
                    <th><a href="#" class="sort-link" data-column="u.stopien_jezdziecki">Stopień jeździecki</a></th>
                    <?php if ($_SESSION['user_role'] != 'klient') { ?>
                        <th>Edytuj</th>
                    <?php } ?>
                    <?php if ($_SESSION['user_role'] == 'administrator') { ?>
                        <th>Usuń</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody id="trainers-tbody">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><img src="../<?php echo htmlspecialchars($row['zdjecie']); ?>" alt="Zdjęcie trenera" width="100"></td>
                            <td><?php echo htmlspecialchars($row['imie']); ?></td>
                            <td><?php echo htmlspecialchars($row['nazwisko']); ?></td>
                            <td><?php echo htmlspecialchars($row['stopien_nazwa']); ?></td>
                            
                            <?php if ($_SESSION['user_id'] == $row['id'] || $_SESSION['user_role'] == 'administrator'){ ?>
                                <td>
                                <button class="edit-button table-button" data-id="<?php echo htmlspecialchars($row['id']); ?>"
                                    onclick='showEditModal(<?php echo json_encode($row); ?>)'>
                                    Edytuj
                                </button>
                                </td>
                            <?php } ?>
                            <?php if ($_SESSION['user_role'] == 'administrator') { ?>
                                <td>
                                <form method="post" action="../scripts/crud_trainers.php" style="display:inline;">
                                        <input type="hidden" name="delete_trainer" value="1">
                                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                        <button type="submit" class="table-button" onclick="return confirm('Czy na pewno chcesz usunąć tego trenera?')">Usuń</button>
                                    </form>

                                </td>
                            <?php } ?>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">Brak danych o trenerach.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <?php if ($_SESSION['user_role'] == 'administrator') { ?>
            <button id="add-trainer-button" onclick="showAddModal()" class="table-button">Dodaj trenera</button>
        <?php } ?>
    </div>

    <?php if ($_SESSION['user_role'] == 'administrator') { ?>
        <!-- Modal do dodania trenera -->
        <div id="add-trainer-modal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('add-trainer-modal')">&times;</span>
                <h3>Dodaj Trenera</h3>
                <form method="post" action="../scripts/crud_trainers.php" enctype="multipart/form-data">
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
                        <label for="trainer_image">Zdjęcie:</label>
                        <div class="drop-zone form-control" id="drop-zone">
                            Przeciągnij lub wybierz zdjęcie...
                            <input type="file" name="trainer_image" id="file-input" style="display: none;">
                            <img id="preview-image" src="" alt="Preview Image" style="display:none; width: 100%; height: auto; margin-top: 10px;">
                        </div>
                        <input type="hidden" id="employee-id" name="employee_id" value="">
                    </div>
                    <div class="form-group">
                        <label for="stopien_jezdziecki">Stopień jeździecki:</label>
                        <select id="stopien_jezdziecki" name="stopien_jezdziecki" class="form-control" required>
                            <?php
                            $skillsQuery = "SELECT * FROM users_skill";
                            $skillsResult = $conn->query($skillsQuery);
                            if ($skillsResult->num_rows > 0) {
                                while ($skill = $skillsResult->fetch_assoc()) {
                                    echo '<option value="' . htmlspecialchars($skill['id_skill']) . '">' . htmlspecialchars($skill['stopien_jezdziecki']) . '</option>';
                                }
                            }
                            ?>
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
    <?php } ?>

    <!-- Modal do edycji trenera -->
    <div id="edit-trainer-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('edit-trainer-modal')">&times;</span>
            <h3>Edytuj Trenera</h3>
            <form method="post" action="../scripts/crud_trainers.php" enctype="multipart/form-data">
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
                    <label for="edit_trainer_image">Zdjęcie:</label>
                    <div class="drop-zone form-control" id="edit-drop-zone">
                        Przeciągnij lub wybierz zdjęcie...
                        <input type="file" name="trainer_image" id="edit-file-input" style="display: none;">
                        <img id="edit-preview-image" src="" alt="Preview Image" style="display:none; width: 100%; height: auto; margin-top: 10px;">
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_stopien_jezdziecki">Stopień jeździecki:</label>
                    <select id="edit_stopien_jezdziecki" name="stopien_jezdziecki" class="form-control" required>
                        <?php
                        $skillsQuery = "SELECT * FROM users_skill";
                        $skillsResult = $conn->query($skillsQuery);
                        if ($skillsResult->num_rows > 0) {
                            while ($skill = $skillsResult->fetch_assoc()) {
                                echo '<option value="' . htmlspecialchars($skill['id_skill']) . '">' . htmlspecialchars($skill['stopien_jezdziecki']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="table-button flexend">Zapisz zmiany</button>
            </form>
        </div>
    </div>

    <script>
        function updateTable() {
            const imie = document.getElementById('imie').value;
            const nazwisko = document.getElementById('nazwisko').value;
            const stopien = document.getElementById('stopien_jezdziecki').value;
            const sort = document.getElementById('sort').value;
            const order = document.getElementById('order').value;

            $.get('dashboard.php', {
                page: 'download_trainers.php',
                imie: imie,
                nazwisko: nazwisko,
                stopien_jezdziecki: stopien,
                sort: sort,
                order: order
            }, function(data) {
                const tbody = $(data).find('#trainers-tbody').html();
                $('#trainers-tbody').html(tbody);
            });
        }

        $(document).ready(function() {
            $('#imie, #nazwisko, #stopien_jezdziecki, #sort, #order').change(updateTable);
            $('.sort-link').click(function(e) {
                e.preventDefault();
                const column = $(this).data('column');
                const currentOrder = $('#sort').val() === column ? $('#order').val() : 'ASC';
                const newOrder = currentOrder === 'ASC' ? 'DESC' : 'ASC';
                $('#sort').val(column);
                $('#order').val(newOrder);
                updateTable();
            });
        });

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

        function showAddModal() {
            document.getElementById('add-trainer-modal').style.display = 'block';
        }

        function setImageSrc(imageId, imageUrl) {
            const imageElement = document.getElementById(imageId);
            if (imageElement) {
                imageElement.src = imageUrl;
                imageElement.style.display = 'block';
            }
        }

        function showEditModal(trainer) {
            document.getElementById('edit_user_id').value = trainer.id;
            document.getElementById('edit_imie').value = trainer.imie;
            document.getElementById('edit_nazwisko').value = trainer.nazwisko;
            document.getElementById('edit_email').value = trainer.email;
            document.getElementById('edit_ulica').value = trainer.ulica;
            document.getElementById('edit_nr_domu').value = trainer.nr_domu;
            document.getElementById('edit_kod_pocztowy').value = trainer.kod_pocztowy;
            document.getElementById('edit_miasto').value = trainer.miasto;
            document.getElementById('edit_telefon').value = trainer.telefon;
            document.getElementById('edit_stopien_jezdziecki').value = trainer.stopien_jezdziecki;
            document.getElementById('edit-trainer-modal').style.display = 'block';

            setImageSrc('edit-preview-image', '../' + trainer.zdjecie); // Ustawienie podglądu obrazu
        }

        document.getElementById('edit-drop-zone').addEventListener('click', () => document.getElementById('edit-file-input').click());

        document.getElementById('edit-file-input').addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                const reader = new FileReader();
                reader.onload = function (e) {
                    setImageSrc('edit-preview-image', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('edit-drop-zone').addEventListener('dragover', (e) => {
            e.preventDefault();
            document.getElementById('edit-drop-zone').classList.add('dragover');
        });

        document.getElementById('edit-drop-zone').addEventListener('dragleave', () => {
            document.getElementById('edit-drop-zone').classList.remove('dragover');
        });

        document.getElementById('edit-drop-zone').addEventListener('drop', (e) => {
            e.preventDefault();
            document.getElementById('edit-drop-zone').classList.remove('dragover');
            if (e.dataTransfer.files.length > 0) {
                const file = e.dataTransfer.files[0];
                document.getElementById('edit-file-input').files = e.dataTransfer.files;
                const reader = new FileReader();
                reader.onload = function (e) {
                    setImageSrc('edit-preview-image', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });

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
