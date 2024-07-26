<?php
include '../scripts/db.php';

// Pobranie listy koni
$sql = "SELECT h.id, h.imie, h.kolor AS kolor_id, hc.kolor, h.rasa AS rasa_id, hb.rasa, h.stan_zdrowia AS stan_zdrowia_id, hh.stan_zdrowia, h.rodzaj_konia AS rodzaj_konia_id, ht.rodzaj, h.opis, h.data_urodzenia, h.wzrost, h.zdjecie FROM horses AS h
INNER JOIN horses_color AS hc ON h.kolor = hc.id_color
INNER JOIN horses_type AS ht ON h.rodzaj_konia = ht.id_type
INNER JOIN horses_health AS hh ON h.stan_zdrowia = hh.id_health
INNER JOIN horses_breed AS hb ON h.rasa = hb.id_breed";
$result = $conn->query($sql);

// Pobranie listy ras
$rasa_sql = "SELECT id_breed, rasa FROM horses_breed";
$rasa_result = $conn->query($rasa_sql);
$rasy = [];
while ($row = $rasa_result->fetch_assoc()) {
    $rasy[] = $row;
}

// Pobranie listy kolorów
$kolor_sql = "SELECT id_color, kolor FROM horses_color";
$kolor_result = $conn->query($kolor_sql);
$kolory = [];
while ($row = $kolor_result->fetch_assoc()) {
    $kolory[] = $row;
}

// Pobranie listy stanów zdrowia
$stan_sql = "SELECT id_health, stan_zdrowia FROM horses_health";
$stan_result = $conn->query($stan_sql);
$stany = [];
while ($row = $stan_result->fetch_assoc()) {
    $stany[] = $row;
}

// Pobranie listy rodzajów koni
$rodzaj_sql = "SELECT id_type, rodzaj FROM horses_type";
$rodzaj_result = $conn->query($rodzaj_sql);
$rodzaje = [];
while ($row = $rodzaj_result->fetch_assoc()) {
    $rodzaje[] = $row;
}
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
                    <th>Wzrost</th>
                    <th>Rasa</th>
                    <th>Kolor</th>
                    <th>Stan Zdrowia</th>
                    <th>Rodzaj Konia</th>
                    <th>Opis</th>
                    <th>Zdjęcie</th>
                    <?php if ($_SESSION['user_role'] == 'administrator' || $_SESSION['user_role'] == 'trener') { ?>
                        <th>Edytuj</th>
                    <?php } ?>
                    <?php if ($_SESSION['user_role'] == 'administrator') { ?>
                        <th>Usuń</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <?php
                                // Oblicz wiek
                                $birthdate = $row['data_urodzenia'];
                                $dob = new DateTime($birthdate);
                                $now = new DateTime();
                                $age = $now->diff($dob)->y;
                            ?>

                            <td><?php echo htmlspecialchars($row['imie']); ?></td>
                            <td><?php echo htmlspecialchars($age); ?></td>
                            <td><?php echo htmlspecialchars($row['wzrost']); ?></td>
                            <td><?php echo htmlspecialchars($row['rasa']); ?></td>
                            <td><?php echo htmlspecialchars($row['kolor']); ?></td>
                            <td><?php echo htmlspecialchars($row['stan_zdrowia']); ?></td>
                            <td><?php echo htmlspecialchars($row['rodzaj']); ?></td>
                            <td><?php echo htmlspecialchars($row['opis']); ?></td>
                            <td><img src="../<?php echo htmlspecialchars($row['zdjecie']); ?>" alt="Zdjęcie konia" width="100"></td>
                            <?php if ($_SESSION['user_role'] == 'administrator' || $_SESSION['user_role'] == 'trener') { ?>
                                <td>
                                    <button class="edit-button table-button" onclick='showEditModal(<?php echo json_encode($row); ?>)'>
                                        Edytuj
                                    </button>
                                </td>
                            <?php } ?>
                            <?php if ($_SESSION['user_role'] == 'administrator') { ?>
                                <td>
                                    <form method="post" action="../scripts/crud_horses.php" style="display:inline;">
                                        <input type="hidden" name="delete_horse" value="1">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                        <button type="submit" class="table-button" onclick="return confirm('Czy na pewno chcesz usunąć tego konia?')">Usuń</button>
                                    </form>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">Brak danych o koniach.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if ($_SESSION['user_role'] == 'administrator') { ?>
            <button id="add-horse-button" onclick="showAddModal()" class="table-button">Dodaj Konia</button>
        <?php } ?>
    </div>

    <!-- Modal do dodania konia -->
    <div id="add-horse-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('add-horse-modal')">&times;</span>
            <h3>Dodaj Konia</h3>
            <form method="post" action="../scripts/crud_horses.php" enctype="multipart/form-data">
                <input type="hidden" name="add_horse" value="1">
                <div class="form-group">
                    <label for="imie">Imię:</label>
                    <input type="text" id="imie" name="imie" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="wiek">Data urodzenia:</label>
                    <input type="date" id="wiek" name="wiek" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="rasa">Rasa:</label>
                    <select id="rasa" name="rasa" class="form-control" required>
                        <option value="" selected disabled>Wybierz rasę</option>
                        <?php foreach ($rasy as $rasa): ?>
                            <option value="<?php echo htmlspecialchars($rasa['id_breed']); ?>"><?php echo htmlspecialchars($rasa['rasa']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="kolor">Kolor:</label>
                    <select id="kolor" name="kolor" class="form-control" required>
                        <option value="" selected disabled>Wybierz kolor</option>
                        <?php foreach ($kolory as $kolor): ?>
                            <option value="<?php echo htmlspecialchars($kolor['id_color']); ?>"><?php echo htmlspecialchars($kolor['kolor']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="wzrost">Wzrost (cm):</label>
                    <input type="number" id="wzrost" name="wzrost" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="stan_zdrowia">Stan Zdrowia:</label>
                    <select id="stan_zdrowia" name="stan_zdrowia" class="form-control" required>
                        <option value="" selected disabled>Wybierz stan zdrowia</option>
                        <?php foreach ($stany as $stan): ?>
                            <option value="<?php echo htmlspecialchars($stan['id_health']); ?>"><?php echo htmlspecialchars($stan['stan_zdrowia']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="rodzaj_konia">Rodzaj Konia:</label>
                    <select id="rodzaj_konia" name="rodzaj_konia" class="form-control" required>
                        <option value="" selected disabled>Wybierz rodzaj konia</option>
                        <?php foreach ($rodzaje as $rodzaj): ?>
                            <option value="<?php echo htmlspecialchars($rodzaj['id_type']); ?>"><?php echo htmlspecialchars($rodzaj['rodzaj']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="opis">Opis:</label>
                    <textarea id="opis" name="opis" class="form-control" required></textarea>
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
                <button type="submit" class="table-button flexend">Zapisz</button>
            </form>
        </div>
    </div>

    <!-- Modal do edycji konia -->
    <div id="edit-horse-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('edit-horse-modal')">&times;</span>
            <h3>Edytuj Konia</h3>
            <form method="post" action="../scripts/crud_horses.php" enctype="multipart/form-data">
                <?php if ($_SESSION['user_role'] == 'administrator') { ?>
                    <input type="hidden" name="edit_horse" value="1">
                <?php } ?>
                <?php if ($_SESSION['user_role'] == 'trener') { ?>
                    <input type="hidden" name="edit_horse_trainer" value="1">
                <?php } ?>
                <input type="hidden" id="edit_horse_id" name="id">
                <?php if ($_SESSION['user_role'] == 'administrator') { ?>
                    <div class="form-group">
                        <label for="edit_imie">Imię:</label>
                        <input type="text" id="edit_imie" name="imie" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_wiek">Data urodzenia:</label>
                        <input type="date" id="edit_wiek" name="wiek" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_rasa">Rasa:</label>
                        <select id="edit_rasa" name="rasa" class="form-control" required>
                            <?php foreach ($rasy as $rasa): ?>
                                <option value="<?php echo htmlspecialchars($rasa['id_breed']); ?>"><?php echo htmlspecialchars($rasa['rasa']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_kolor">Kolor:</label>
                        <select id="edit_kolor" name="kolor" class="form-control" required>
                            <?php foreach ($kolory as $kolor): ?>
                                <option value="<?php echo htmlspecialchars($kolor['id_color']); ?>"><?php echo htmlspecialchars($kolor['kolor']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_wzrost">Wzrost (cm):</label>
                        <input type="number" id="edit_wzrost" name="wzrost" class="form-control" required>
                    </div>
                <?php } ?>
                <?php if ($_SESSION['user_role'] == 'administrator' || $_SESSION['user_role'] == 'trener') { ?>
                    <div class="form-group">
                        <label for="edit_stan_zdrowia">Stan Zdrowia:</label>
                        <select id="edit_stan_zdrowia" name="stan_zdrowia" class="form-control" required>
                            <?php foreach ($stany as $stan): ?>
                                <option value="<?php echo htmlspecialchars($stan['id_health']); ?>"><?php echo htmlspecialchars($stan['stan_zdrowia']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php } ?>
                <?php if ($_SESSION['user_role'] == 'administrator') { ?>
                    <div class="form-group">
                        <label for="edit_rodzaj_konia">Rodzaj Konia:</label>
                        <select id="edit_rodzaj_konia" name="rodzaj_konia" class="form-control" required>
                            <?php foreach ($rodzaje as $rodzaj): ?>
                                <option value="<?php echo htmlspecialchars($rodzaj['id_type']); ?>"><?php echo htmlspecialchars($rodzaj['rodzaj']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_opis">Opis:</label>
                        <textarea id="edit_opis" name="opis" class="form-control" required></textarea>
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
                <?php } ?>
                <button type="submit" class="table-button flexend">Zapisz zmiany</button>
            </form>
        </div>
    </div>

    <script>
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
            document.getElementById('add-horse-modal').style.display = 'block';
        }

        function setImageSrc(imageId, imageUrl) {
            const imageElement = document.getElementById(imageId);
            if (imageElement) {
                imageElement.src = imageUrl;
                imageElement.style.display = 'block';
            }
        }

        <?php if ($_SESSION['user_role'] == 'administrator') { ?>
            function showEditModal(horse) {
                document.getElementById('edit_horse_id').value = horse.id;
                document.getElementById('edit_imie').value = horse.imie;
                document.getElementById('edit_wiek').value = horse.data_urodzenia;
                document.getElementById('edit_rasa').value = horse.rasa_id;
                document.getElementById('edit_kolor').value = horse.kolor_id;
                document.getElementById('edit_wzrost').value = horse.wzrost;
                document.getElementById('edit_stan_zdrowia').value = horse.stan_zdrowia_id;
                document.getElementById('edit_rodzaj_konia').value = horse.rodzaj_konia_id;
                document.getElementById('edit_opis').value = horse.opis;
                document.getElementById('edit-horse-modal').style.display = 'block';

                setImageSrc('edit-preview-image', '../' + horse.zdjecie); // Ustawienie podglądu obrazu
            }
        <?php } ?>

        <?php if ($_SESSION['user_role'] == 'trener') { ?>
            function showEditModal(horse) {
                document.getElementById('edit_horse_id').value = horse.id;
                document.getElementById('edit_stan_zdrowia').value = horse.stan_zdrowia;
                document.getElementById('edit-horse-modal').style.display = 'block';
            }
        <?php } ?>

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
