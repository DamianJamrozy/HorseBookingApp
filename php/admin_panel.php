<!-- plik który zarządza panelem adminstatora -->

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
    <title>Panel administratora</title>
    <link rel="icon" href="../figures/favicon.png" type="image/x-icon" />
    <link rel="stylesheet" href="../style/styles.css">
    <link rel="stylesheet" href="../style/stylesClientPanel.css">
    <link rel="stylesheet" href="../style/stylesClientData.css">
    <link rel="stylesheet" href="../style/stylesTable.css">
    <link rel="stylesheet" href="../style/stylesFormtrain.css">
</head>

<body>
    <header class="banner">
        <div class="logo-container">
            <p>Horse Riding Club</p>
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
                <li class="menu-item" data-content="download_trainers_Admin.php"><img src="../figures/trener.png" alt="Trenerzy" class="icon">Trenerzy</li>
                <li class="menu-item" data-content="download_horse.php"><img src="../figures/horse.png" alt="Konie" class="icon">Konie</li>
                <li class="menu-item" data-content="terminarz.php"><img src="../figures/calender.png" alt="Terminarz" class="icon">Terminarz</li>
                <li class="menu-item" data-content="client_data_All.php"><img src="../figures/userEdit.png" alt="Dane" class="icon">Dane osobowe</li>
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
            function loadContent(url, params = {}) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: params,
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
                localStorage.setItem('lastViewed', contentUrl); // Save the last viewed page to localStorage
                loadContent(contentUrl);
            });

            // Handle sorting and filtering for client_data_All.php
            $(".content").on("click", "th a", function(event) {
                event.preventDefault();
                var url = $(this).attr("href");
                var params = new URLSearchParams(url.split('?')[1]);
                loadContent(localStorage.getItem('lastViewed'), Object.fromEntries(params));
            });

            // Load initial content or the last viewed content
            var initialContent = localStorage.getItem('lastViewed') || "tiles.php";
            loadContent(initialContent);
        });
    </script>

</body>

</html>
