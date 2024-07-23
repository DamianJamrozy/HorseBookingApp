<!-- plik który zarządza panelem trenera  -->
<!-- w pliku tym kopia tego co jest w adminie ale trzeba dodać mu funkcjonalności dedykowane dla niego -->
<!-- trern nie widzimy wszytskich trenerów - usuwamy to 
widzii pulpil
konie
terminarz 
dane klientow - wszytskich 

termiarz EZ -->
<!-- na początek możńa skopiować plik z admina lub kilenta i modyfikować  -->



<?php
session_start();
include '../php/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'administrator') {
    header("Location: index.php");
    exit();
}

// Pobranie imienia i nazwiska zalogowanego użytkownika
$user_id = $_SESSION['user_id'];

// Sprawdzenie połączenia z bazą danych
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT imie, nazwisko FROM users WHERE id = $user_id";
$result = $conn->query($sql);

$imie_nazwisko = "Nieznany użytkownik"; // Domyślna wartość

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $imie_nazwisko = htmlspecialchars($row['imie'] . ' ' . $row['nazwisko']);
}

$conn->close();

?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel trenera</title>
    <link rel="stylesheet" href="../style/styles.css">
    <link rel="stylesheet" href="../style/stylesClientPanel.css">
    <link rel="stylesheet" href="../style/stylesClientData.css">
    <link rel="stylesheet" href="../style/stylesTable.css">
    <link rel="stylesheet" href="../style/stylesFormtrain.css">
    <style>
        .no-access {
            color: red;
            font-size: 2em;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <header class="banner">
        <div class="logo-container">
            <img src="../figures/logo.jpg" alt="Logo" class="logo">
        </div>
        <div class="welcome-text">Witaj: <?php echo $imie_nazwisko; ?></div>
        <div class="logout">
            <a href="logout.php"><img src="../figures/powerOff.png" alt="Wyloguj się"></a>
        </div>
    </header>
    <main>
        <div class="sidebar">
            <ul>
                <li class="menu-item" data-content="tiles.php"><img src="../figures/home.png" alt="Pulpit" class="icon">Pulpit</li>
                <li class="menu-item" id="no-access"><img src="../figures/trener.png" alt="Trenerzy" class="icon">Trenerzy</li>
                <li class="menu-item" data-content="download_horse.php"><img src="../figures/horse.png" alt="Konie" class="icon">Konie</li> 
                <li class="menu-item" data-content="terminarz.php"><img src="../figures/calender.png" alt="Terminarz" class="icon">Terminarz</li>
                <li class="menu-item" data-content="client_data_All.php"><img src="../figures/userEdit.png" alt="Dane" class="icon">Dane klientów</li>
            </ul>
        </div>
        <div class="content">
            <!-- Tutaj będzie ładowana zawartość z AJAX -->
        </div>
    </main>
    <footer>2024</footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            function loadContent(url) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $(".content").html(response);
                    },
                    error: function() {
                        $(".content").html("Wystąpił błąd podczas ładowania zawartości.");
                    }
                });
            }

            $(".menu-item").click(function() {
                var contentUrl = $(this).data("content");
                if ($(this).attr('id') === 'no-access') {
                    $(".content").html('<div class="no-access">Brak dostępu</div>');
                } else {
                    loadContent(contentUrl);
                }
            });

            // Load initial content
            loadContent("tiles.php");
        });
    </script>
</body>

</html>
