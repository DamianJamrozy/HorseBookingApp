<!-- główny plik uruchomieniowy -->
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klub Jeździecki</title>
    
    <link rel="icon" href="../figures/favicon.png" type="image/x-icon" />


    <link rel="stylesheet" href="../style/styles.css">
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
                <form action="login.php" method="post">
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