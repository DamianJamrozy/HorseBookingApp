<!-- plik który zarządza panelem reestracji bedzie mógł być użyty do panelu admin aby dodać trenera user on na pewno musi być zmodyfikowany   -->

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klub Jeździecki - Rejestracja</title>
    <link rel="stylesheet" href="../style/styles.css">
</head>

<body>
    <header class="banner">
        <div class="logo-container">
            <img src="../figures/logo.jpg" alt="Logo" class="logo">
        </div>
        <div class="welcome-text">Witaj na stronie rejestracji w systemie</div>
    </header>
    <main>
        <div class="login-form">
            <h2>Rejestracja</h2>
            <form action="register_action.php" method="post" enctype="multipart/form-data">
                <label for="imie">Imię:</label>
                <input type="text" id="imie" name="imie" required>
                <label for="nazwisko">Nazwisko:</label>
                <input type="text" id="nazwisko" name="nazwisko" required>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <label for="password">Hasło:</label>
                <input type="password" id="password" name="password" required>
                <label for="ulica">Ulica:</label>
                <input type="text" id="ulica" name="ulica" required>
                <label for="nr_domu">Nr domu:</label>
                <input type="text" id="nr_domu" name="nr_domu" required>
                <label for="kod_pocztowy">Kod pocztowy:</label>
                <input type="text" id="kod_pocztowy" name="kod_pocztowy" required>
                <label for="miasto">Miasto:</label>
                <input type="text" id="miasto" name="miasto" required>
                <label for="telefon">Telefon kontaktowy:</label>
                <input type="text" id="telefon" name="telefon" required>
                <label for="stopien_jezdziecki">Stopień jeździecki:</label>
                <select id="stopien_jezdziecki" name="stopien_jezdziecki" required>
                    <option value="początkujący">Początkujący</option>
                    <option value="średniozaawansowany">Średniozaawansowany</option>
                    <option value="zaawansowany">Zaawansowany</option>
                </select>
                <label for="zdjecie">Zdjęcie (opcjonalne):</label>
                <input type="file" id="zdjecie" name="zdjecie">
                <button type="submit">Zarejestruj się</button>
            </form>
        </div>
    </main>
    <footer>2024</footer>
</body>

</html>