<?php
session_start();
include '../php/db.php';

// Function to sanitize input data
function sanitize($conn, $data) {
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags($data)));
}

// Ensure the user is an administrator
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'administrator') {
    header("Location: index.php");
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['imie'], $_POST['nazwisko'], $_POST['email'], $_POST['ulica'], $_POST['nr_domu'], $_POST['kod_pocztowy'], $_POST['miasto'], $_POST['telefon'], $_POST['hashed_password'], $_POST['stopien_jezdziecki'])) {
    $imie = sanitize($conn, $_POST['imie']);
    $nazwisko = sanitize($conn, $_POST['nazwisko']);
    $email = sanitize($conn, $_POST['email']);
    $ulica = sanitize($conn, $_POST['ulica']);
    $nr_domu = sanitize($conn, $_POST['nr_domu']);
    $kod_pocztowy = sanitize($conn, $_POST['kod_pocztowy']);
    $miasto = sanitize($conn, $_POST['miasto']);
    $telefon = sanitize($conn, $_POST['telefon']);
    $zdjecie = sanitize($conn, $_POST['zdjecie']); // Optional
    $hashed_password = password_hash($_POST['hashed_password'], PASSWORD_DEFAULT);
    $stopien_jezdziecki = sanitize($conn, $_POST['stopien_jezdziecki']);
    $rola = sanitize($conn, $_POST['rola']);

    // Insert into users table
    $sql_insert_user = "INSERT INTO users (imie, nazwisko, email, ulica, nr_domu, kod_pocztowy, miasto, telefon, zdjecie, hashed_password, rola, stopien_jezdziecki)
                        VALUES ('$imie', '$nazwisko', '$email', '$ulica', '$nr_domu', '$kod_pocztowy', '$miasto', '$telefon', '$zdjecie', '$hashed_password', '$rola', '$stopien_jezdziecki')";
    if ($conn->query($sql_insert_user) === TRUE) {
        $message = "Sukces";
    } else {
        $message = "Błąd: " . $conn->error;
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj użytkownika</title>
    <link rel="stylesheet" href="../style/styles.css">
    <link rel="stylesheet" href="../style/stylesTable.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container label {
            display: block;
            margin: 10px 0 5px;
        }
        .form-container input, 
        .form-container select,
        .form-container button {
            width: 100%;
            padding: 10px;
            margin: 5px 0 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container .button-group {
            display: flex;
            justify-content: space-between;
        }
        .form-container .button-group button {
            width: 48%;
        }
        .success-message {
            text-align: center;
            font-size: 1.2em;
            color: green;
            margin-bottom: 20px;
        }
        .error-message {
            text-align: center;
            font-size: 1.2em;
            color: red;
            margin-bottom: 20px;
        }
    </style>
    <script>
        function redirectToAdminPanel() {
            setTimeout(function() {
                window.location.href = 'admin_panel.php';
            }, 5000);
        }
    </script>
</head>
<body>
    <main>
        <div class="form-container">
            <h2>Dodaj użytkownika</h2>
            <?php
            if ($message) {
                $messageClass = ($message === "Sukces") ? 'success-message' : 'error-message';
                echo "<div class='$messageClass'>$message</div>";
                if ($message === "Sukces") {
                    echo "<script>redirectToAdminPanel();</script>";
                }
            }
            ?>
            <form method="POST" action="add_user.php">
                <label for="imie">Imię:</label>
                <input type="text" id="imie" name="imie" required>
                
                <label for="nazwisko">Nazwisko:</label>
                <input type="text" id="nazwisko" name="nazwisko" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="ulica">Ulica:</label>
                <input type="text" id="ulica" name="ulica" required>
                
                <label for="nr_domu">Nr domu:</label>
                <input type="text" id="nr_domu" name="nr_domu" required>
                
                <label for="kod_pocztowy">Kod pocztowy:</label>
                <input type="text" id="kod_pocztowy" name="kod_pocztowy" required>
                
                <label for="miasto">Miasto:</label>
                <input type="text" id="miasto" name="miasto" required>
                
                <label for="telefon">Telefon:</label>
                <input type="text" id="telefon" name="telefon" required>
                
                <label for="zdjecie">Zdjęcie (URL):</label>
                <input type="text" id="zdjecie" name="zdjecie">
                
                <label for="hashed_password">Hasło:</label>
                <input type="password" id="hashed_password" name="hashed_password" required>
                
                <label for="stopien_jezdziecki">Stopień jeździecki:</label>
                <select id="stopien_jezdziecki" name="stopien_jezdziecki" required>
                    <option value="początkujący">początkujący</option>
                    <option value="średniozaawansowany">średniozaawansowany</option>
                    <option value="zaawansowany">zaawansowany</option>
                </select>
                
                <label for="rola">Rola:</label>
                <select id="rola" name="rola" required>
                    <option value="klient">klient</option>
                    <option value="trener">trener</option>
                    <option value="administrator">administrator</option>
                </select>
                
                <div class="button-group">
                    <button type="submit">Dodaj użytkownika</button>
                    <button type="button" onclick="window.location.href='admin_panel.php'">Anuluj</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
