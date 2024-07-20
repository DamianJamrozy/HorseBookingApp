<!-- plik który pobiera wszytskie dane users -->
<?php
session_start();
include '../php/db.php';

// Sprawdzenie, czy użytkownik jest zalogowany jako administrator
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'administrator') {
    header("Location: index.php");
    exit();
}

// Pobranie wartości filtrów i sortowania
$rolaFilter = isset($_GET['rola']) ? $_GET['rola'] : '';
$stopienFilter = isset($_GET['stopien_jezdziecki']) ? $_GET['stopien_jezdziecki'] : '';
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Pobranie wszystkich użytkowników z bazy danych z uwzględnieniem filtrów i sortowania
$sql = "SELECT * FROM users WHERE 1=1";

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
    <link rel="stylesheet" href="../style/styles.css">
    <link rel="stylesheet" href="../style/stylesTable.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Style for the modal */
        .modal {
            position: absolute;
            display: none;
            background-color: white;
            border: 1px solid #ccc;
            padding: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        .modal form {
            display: flex;
            flex-direction: column;
        }
        .sort-link {
            color: inherit;
            text-decoration: none;
        }
    </style>
    <script>
        $(document).ready(function() {
            function loadFilteredData() {
                var params = {
                    rola: $('#rola').val(),
                    stopien_jezdziecki: $('#stopien_jezdziecki').val(),
                    sort: $('#sort').val(),
                    order: $('#order').val()
                };
                $.get('client_data_All.php', params, function(data) {
                    $('.user-data').html($(data).find('.user-data').html());
                    applyEventHandlers();
                });
            }

            function applyEventHandlers() {
                $('#filter-form select').off('change').on('change', function() {
                    loadFilteredData();
                });

                $('.sort-link').off('click').on('click', function(event) {
                    event.preventDefault();
                    $('#sort').val($(this).data('sort'));
                    $('#order').val($(this).data('order'));
                    loadFilteredData();
                });

                $('.delete-user-button').off('click').on('click', function() {
                    var userId = $(this).siblings('.delete-user-id').val();
                    if (userId && confirm('Czy na pewno chcesz usunąć tego użytkownika?')) {
                        $.ajax({
                            url: 'delete_user.php',
                            type: 'POST',
                            data: { id: userId },
                            success: function(response) {
                                if (response.trim() === 'Sukces') {
                                    alert('Użytkownik został usunięty.');
                                    loadFilteredData();
                                } else {
                                    alert('Błąd: ' + response);
                                }
                            },
                            error: function() {
                                alert('Wystąpił błąd podczas usuwania użytkownika.');
                            }
                        });
                    }
                });

                $('.edit-user-button').off('click').on('click', function() {
                    var userId = $(this).siblings('.edit-user-id').val();
                    var currentLevel = $(this).siblings('.edit-user-id').data('level');
                    var offset = $(this).offset();
                    $('#edit-user-id').val(userId);
                    $('#edit-stopien-jezdziecki').val(currentLevel);
                    $('#editModal').css({
                        top: offset.top + $(this).height() + 10,
                        left: offset.left
                    }).show();
                });

                $('#edit-form').off('submit').on('submit', function(event) {
                    event.preventDefault();
                    $.ajax({
                        url: 'edit_user.php',
                        type: 'POST',
                        data: $(this).serialize(),
                        success: function(response) {
                            if (response.trim() === 'Sukces') {
                                alert('Zmiany zostały zapisane.');
                                $('#editModal').hide();
                                loadFilteredData();
                            } else {
                                alert('Błąd: ' + response);
                            }
                        },
                        error: function() {
                            alert('Wystąpił błąd podczas zapisywania zmian.');
                        }
                    });
                });

                $('#closeModal').off('click').on('click', function() {
                    $('#editModal').hide();
                });
            }

            applyEventHandlers();
        });
    </script>
</head>

<body>
    <main>
        <div class="user-data">
            <h2>Wszyscy użytkownicy</h2>

            <!-- Filter and Sort Form -->
            <form id="filter-form" method="GET" action="">
                <label for="rola">Rola:</label>
                <select name="rola" id="rola">
                    <option value="">Wszystkie</option>
                    <option value="administrator" <?= $rolaFilter == 'administrator' ? 'selected' : '' ?>>Administrator</option>
                    <option value="klient" <?= $rolaFilter == 'klient' ? 'selected' : '' ?>>Klient</option>
                </select>

                <label for="stopien_jezdziecki">Stopień jeździecki:</label>
                <select name="stopien_jezdziecki" id="stopien_jezdziecki">
                    <option value="">Wszystkie</option>
                    <option value="początkujący" <?= $stopienFilter == 'początkujący' ? 'selected' : '' ?>>Początkujący</option>
                    <option value="średniozaawansowany" <?= $stopienFilter == 'średniozaawansowany' ? 'selected' : '' ?>>Średniozaawansowany</option>
                    <option value="zaawansowany" <?= $stopienFilter == 'zaawansowany' ? 'selected' : '' ?>>Zaawansowany</option>
                </select>

                <label for="sort">Sortuj według:</label>
                <select name="sort" id="sort">
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
                <select name="order" id="order">
                    <option value="ASC" <?= $sortOrder == 'ASC' ? 'selected' : '' ?>>Rosnąco</option>
                    <option value="DESC" <?= $sortOrder == 'DESC' ? 'selected' : '' ?>>Malejąco</option>
                </select>
            </form>

            <table class="users-table styled-table">
                <thead>
                    <tr>
                        <th><a href="#" class="sort-link" data-sort="id" data-order="<?= $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>">ID</a></th>
                        <th><a href="#" class="sort-link" data-sort="imie" data-order="<?= $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>">Imię</a></th>
                        <th><a href="#" class="sort-link" data-sort="nazwisko" data-order="<?= $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>">Nazwisko</a></th>
                        <th><a href="#" class="sort-link" data-sort="email" data-order="<?= $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>">Email</a></th>
                        <th><a href="#" class="sort-link" data-sort="ulica" data-order="<?= $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>">Ulica</a></th>
                        <th><a href="#" class="sort-link" data-sort="nr_domu" data-order="<?= $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>">Nr domu</a></th>
                        <th><a href="#" class="sort-link" data-sort="kod_pocztowy" data-order="<?= $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>">Kod pocztowy</a></th>
                        <th><a href="#" class="sort-link" data-sort="miasto" data-order="<?= $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>">Miasto</a></th>
                        <th><a href="#" class="sort-link" data-sort="telefon" data-order="<?= $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>">Telefon</a></th>
                        <th>Zdjęcie</th>
                        <th><a href="#" class="sort-link" data-sort="rola" data-order="<?= $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>">Rola</a></th>
                        <th><a href="#" class="sort-link" data-sort="stopien_jezdziecki" data-order="<?= $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>">Stopień jeździecki</a></th>
                        <th>Edytuj</th>
                        <th>Usuń</th>
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
                            if ($row['rola'] === 'klient') {
                                echo '<td>';
                                echo '<select class="edit-user-id" data-level="' . htmlspecialchars($row['stopien_jezdziecki']) . '">';
                                echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['imie']) . ' ' . htmlspecialchars($row['nazwisko']) . ' (ID: ' . htmlspecialchars($row['id']) . ')</option>';
                                echo '</select>';
                                echo '<button class="edit-user-button table-button">Edytuj</button>';
                                echo '</td>';
                                echo '<td>';
                                echo '<select class="delete-user-id">';
                                echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['imie']) . ' ' . htmlspecialchars($row['nazwisko']) . ' (ID: ' . htmlspecialchars($row['id']) . ')</option>';
                                echo '</select>';
                                echo '<button class="delete-user-button table-button">Usuń</button>';
                                echo '</td>';
                            } else {
                                echo '<td></td>'; // Empty cell for non-klient roles
                                echo '<td></td>'; // Empty cell for non-klient roles
                                if ($_SESSION['user_role'] === 'administrator') {
                                    echo '<td></td>';
                                }
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
                echo '<a href="add_user.php"><button class="add-button table-button" data-id="">Dodaj</button></a>';
            }
            ?>
        </div>

        <!-- Edit Modal -->
        <div id="editModal" class="modal">
            <form id="edit-form">
                <input type="hidden" id="edit-user-id" name="id">
                <label for="edit-stopien-jezdziecki">Stopień jeździecki:</label>
                <select id="edit-stopien-jezdziecki" name="stopien_jezdziecki">
                    <option value="początkujący">początkujący</option>
                    <option value="średniozaawansowany">średniozaawansowany</option>
                    <option value="zaawansowany">zaawansowany</option>
                </select>
                <button type="submit" class="table-button">Zapisz</button>
                <button type="button" class="table-button" id="closeModal">Anuluj</button>
            </form>
        </div>
    </main>
</body>

</html>
<?php
$conn->close();
?>
