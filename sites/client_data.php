<?php
include '../scripts/db.php';

// Sprawdzenie, czy użytkownik jest zalogowany jako klient
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'klient')) {
    header("Location: index.php");
    exit();
}

// Pobranie danych użytkownika z bazy danych z uwzględnieniem filtrów i sortowania
$sql = "SELECT u.*, us.stopien_jezdziecki AS stopien_nazwa, ut.rola AS rola_nazwa 
        FROM users u 
        INNER JOIN users_skill us ON u.stopien_jezdziecki = us.id_skill 
        INNER JOIN users_type ut ON u.rola = ut.id_type 
        WHERE u.id = '" . $_SESSION['user_id'] . "'";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dane użytkownika</title>
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
                            echo '<td>' . htmlspecialchars($row['stopien_nazwa']) . '</td>';
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
        </div>

        <!-- Modal do edycji użytkownika -->
        <div id="edit-user-modal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('edit-user-modal')">&times;</span>
                <h3>Edytuj użytkownika</h3>
                <form id="edit-user-form" method="post" action="../scripts/crud_users.php" enctype="multipart/form-data">
                    <input type="hidden" name="edit_user_client" value="1">
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
                    
                    <button type="submit" class="table-button">Zapisz zmiany</button>
                    <button type="button" class="table-button" onclick="closeModal('edit-user-modal')">Anuluj</button>
                </form>
            </div>
        </div>
    </main>
    <script>
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

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

        // Skrypt dla edycji zdjęć w modalach
        const editDropZone = document.getElementById('edit-drop-zone');
        const editFileInput = document.getElementById('edit-file-input');
        const editPreviewImage = document.getElementById('edit-preview-image');

        editDropZone.addEventListener('click', () => editFileInput.click());

        editFileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];

                const reader = new FileReader();
                reader.onload = function(e) {
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

                const reader = new FileReader();
                reader.onload = function(e) {
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
