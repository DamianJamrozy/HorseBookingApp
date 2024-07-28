<!-- plik który zarządza logowaniem  -->

<?php
session_start(); // Rozpoczęcie sesji

// Importowanie pliku db.php do nawiązania połączenia z bazą danych
require_once "db.php";

// Funkcja do sprawdzenia zahaszowanego hasła
function verifyPassword($password, $hashedPassword)
{
    return password_verify($password, $hashedPassword);
}

// Sprawdzenie, czy formularz został wysłany metodą POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pobranie danych z formularza
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Zapytanie SQL do pobrania użytkownika na podstawie emaila
    $stmt = $conn->prepare('SELECT id, rola, hashed_password FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();

    // Sprawdzenie błędów wykonania zapytania SQL
    if (!$stmt) {
        die('Błąd zapytania SQL: ' . mysqli_error($conn));
    }

    $result = $stmt->get_result();

    // Sprawdzenie, czy użytkownik o podanym adresie email istnieje
    if ($result->num_rows == 1) {
        // Pobranie danych użytkownika
        $user = $result->fetch_assoc();

        // Sprawdzenie hasła z użyciem password_verify
        if (verifyPassword($password, $user['hashed_password'])) {

            // Ustawienie sesji dla zalogowanego użytkownika
            $_SESSION['user_id'] = $user['id'];

            if ($user['rola'] == '1'){
                $_SESSION['user_role'] = 'administrator';
                header("Location: ../sites/dashboard.php");
                exit(); // Zakończenie działania skryptu po przekierowaniu
            }
            elseif ($user['rola'] == '2'){
                $_SESSION['user_role'] = 'trener';
                header("Location: ../sites/dashboard.php");
                exit(); // Zakończenie działania skryptu po przekierowaniu
            }
            elseif ($user['rola'] == '3'){
                $_SESSION['user_role'] = 'klient';
                header("Location: ../sites/dashboard.php");
                exit(); // Zakończenie działania skryptu po przekierowaniu
            }
            else{
                // Jeśli rola użytkownika nie jest rozpoznawana, możesz przekierować go gdzieś indziej lub wyświetlić odpowiedni komunikat
                echo "Nieznana rola użytkownika.";
            }
        } else {
            // Nieprawidłowe hasło
            $error_message = "Nieprawidłowe hasło.";
            include '../sites/index.php'; // Powrót do formularza logowania z komunikatem błędu
            exit();
        }
    } else {
        // Użytkownik o podanym adresie email nie istnieje
        $error_message = "Użytkownik o podanym adresie email nie istnieje.";
        include '../sites/index.php'; // Powrót do formularza logowania z komunikatem błędu
        exit();
    }

    // Zamknięcie połączenia i zasobów zapytania SQL
    $stmt->close();
}

// Zamknięcie połączenia z bazą danych
$conn->close();
