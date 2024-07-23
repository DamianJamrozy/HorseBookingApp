<?php
session_start();
include '../scripts/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'administrator' && $_SESSION['user_role'] !== 'klient' && $_SESSION['user_role'] !== 'trener')) {
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

$contentPage = 'tiles.php';
if (isset($_GET['page'])) {
    $contentPage = $_GET['page'];
}
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel administratora</title>
    <link rel="icon" href="../figures/favicon.png" type="image/x-icon" />
    <link rel="stylesheet" href="../styles/general.css">
    <link rel="stylesheet" href="../styles/styles.css">
</head>

<body>
    <header class="banner">
        <div class="logo-container">
            <p>Horse Riding Club</p>
        </div>
        <div class="welcome-text">Witaj: <?php echo $imie_nazwisko; ?></div>
        <div class="logout">
            <a href="../scripts/logout.php"><img src="../figures/powerOff.png" alt="Wyloguj się"></a>
        </div>
    </header>
    <main>
        <div class="sidebar">
            <ul>
                <li class="menu-item"><a href="dashboard.php?page=tiles.php"><img src="../figures/home.png" alt="Pulpit" class="icon">Pulpit</a></li>
                <li class="menu-item"><a href="dashboard.php?page=download_trainers.php"><img src="../figures/trener.png" alt="Trenerzy" class="icon">Trenerzy</a></li>
                <li class="menu-item"><a href="dashboard.php?page=download_horse.php"><img src="../figures/horse.png" alt="Konie" class="icon">Konie</a></li>
                <li class="menu-item"><a href="dashboard.php?page=terminarz.php"><img src="../figures/calender.png" alt="Terminarz" class="icon">Terminarz</a></li>
                <li class="menu-item"><a href="dashboard.php?page=client_data_All.php"><img src="../figures/userEdit.png" alt="Dane" class="icon">Dane osobowe</a></li>
            </ul>
        </div>
        <div class="content">
            <?php
            if (file_exists($contentPage)) {
                include $contentPage;
            } else {
                echo "Strona nie została znaleziona.";
            }
            ?>
        </div>
    </main>
    <footer>2024</footer>
</body>

</html>
