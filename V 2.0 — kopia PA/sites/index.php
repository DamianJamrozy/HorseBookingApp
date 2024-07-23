<!-- główny plik uruchomieniowy -->
<?php
session_start();
include '../scripts/db.php';

if (isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'administrator') {
    header("Location: admin_panel.php");
    exit();
}
else if (isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'trener') {
    header("Location: trainer_panel.php");
    exit();
}
else if (isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'klient') {
    header("Location: client_panel.php");
    exit();
}
else{
    $_SESSION['user_role'] = NULL;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klub Jeździecki</title>
    
    <link rel="icon" href="../figures/favicon.png" type="image/x-icon" />

    <link rel="stylesheet" href="../styles/general.css">
    <link rel="stylesheet" href="../styles/styles.css">
</head>

<body>
    <header class="banner">
        <div class="logo-container">
            <p>Horse Riding Club</p>
        </div>
        <div class="welcome-text">Witaj w systemie wspomagania harmonogramowania treningów jeździeckich </div>
    </header>
    <main>
        <div class="login-form">
            <div class="login-img-inner">
                <p class="login-img-text login-img-text-up">Horse Riding Club</p>
                <p class="login-img-text login-img-text-down">Harmonogram treningów jeżdzieckich</p>
            </div>
            <div class="login-inner">
                <h2>Logowanie</h2>
                <form action="../scripts/login.php" method="post">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    <label for="password">Hasło:</label>
                    <input type="password" id="password" name="password" required>
                    <button type="submit">Zaloguj się</button>
                    <!-- Miejsce na wyświetlanie komunikatów błędów -->
                    <?php if (isset($error_message)) : ?>
                        <p class="error"><?php echo $error_message; ?></p>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </main>
    <footer>Kraków 2024 &nbsp;&nbsp;&nbsp;  &copy; All right reserved</footer>
</body>

</html>