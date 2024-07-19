<!-- plik który pobiera wszytskie dane users  -->
<?php
session_start();
include '../php/db.php';

// Sprawdzenie, czy użytkownik jest zalogowany jako administrator
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'administrator') {
    header("Location: index.php");
    exit();
}

// Pobranie wszystkich użytkowników z bazy danych
$sql = "SELECT * FROM users";
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
    </style>
    <script>
        $(document).ready(function() {
            // Handle delete button click
            $('.delete-user-button').click(function() {
                var userId = $(this).siblings('.delete-user-id').val();
                if (userId && confirm('Czy na pewno chcesz usunąć tego użytkownika?')) {
                    $.ajax({
                        url: 'delete_user.php',
                        type: 'POST',
                        data: { id: userId },
                        success: function(response) {
                            if (response.trim() === 'Sukces') {
                                alert('Użytkownik został usunięty.');
                                location.reload();
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

            // Handle edit button click
            $('.edit-user-button').click(function() {
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

            // Handle edit form submission
            $('#edit-form').submit(function(event) {
                event.preventDefault();
                $.ajax({
                    url: 'edit_user.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.trim() === 'Sukces') {
                            alert('Zmiany zostały zapisane.');
                            location.reload();
                        } else {
                            alert('Błąd: ' + response);
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
        });
    </script>
</head>

<body>
    <main>
        <div class="user-data">
            <h2>Wszyscy użytkownicy</h2>
            <table class="users-table styled-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imię</th>
                        <th>Nazwisko</th>
                        <th>Email</th>
                        <th>Ulica</th>
                        <th>Nr domu</th>
                        <th>Kod pocztowy</th>
                        <th>Miasto</th>
                        <th>Telefon</th>
                        <th>Zdjęcie</th>
                        <th>Rola</th>
                        <th>Stopień jeździecki</th>
                        <th>Usuń</th>
                        <th>Edytuj</th>
                        <?php if ($_SESSION['user_role'] === 'administrator'): ?>
                        <th>Dodaj</th>
                        <?php endif; ?>
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
                                echo '<select class="delete-user-id">';
                                echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['imie']) . ' ' . htmlspecialchars($row['nazwisko']) . ' (ID: ' . htmlspecialchars($row['id']) . ')</option>';
                                echo '</select>';
                                echo '<button class="delete-user-button table-button">Usuń</button>';
                                echo '</td>';
                                echo '<td>';
                                echo '<select class="edit-user-id" data-level="' . htmlspecialchars($row['stopien_jezdziecki']) . '">';
                                echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['imie']) . ' ' . htmlspecialchars($row['nazwisko']) . ' (ID: ' . htmlspecialchars($row['id']) . ')</option>';
                                echo '</select>';
                                echo '<button class="edit-user-button table-button">Edytuj</button>';
                                echo '</td>';
                                if ($_SESSION['user_role'] === 'administrator') {
                                    echo '<td>';
                                    echo '<a href="add_user.php"><button class="add-button table-button" data-id="' . htmlspecialchars($row['id']) . '">Dodaj</button></a>';
                                    echo '</td>';
                                }
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
