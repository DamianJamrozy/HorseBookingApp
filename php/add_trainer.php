<?php
session_start();
include '../php/db.php';

// Function to sanitize input data
function sanitize($conn, $data) {
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags($data)));
}

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
    $rola = 'trener'; // Set role as 'trener'

    // Insert into users table
    $sql_insert_user = "INSERT INTO users (imie, nazwisko, email, ulica, nr_domu, kod_pocztowy, miasto, telefon, zdjecie, hashed_password, rola, stopien_jezdziecki)
                        VALUES ('$imie', '$nazwisko', '$email', '$ulica', '$nr_domu', '$kod_pocztowy', '$miasto', '$telefon', '$zdjecie', '$hashed_password', '$rola', '$stopien_jezdziecki')";

    if ($conn->query($sql_insert_user) === TRUE) {
        $last_id = $conn->insert_id;

        // Insert into trainers table
        $sql_insert_trainer = "INSERT INTO trainers (user_id) VALUES ('$last_id')";
        if ($conn->query($sql_insert_trainer) === TRUE) {
            // Success
            echo json_encode(['status' => 'success', 'message' => 'Trener został dodany.']);
        } else {
            // Error inserting into trainers table
            echo json_encode(['status' => 'error', 'message' => 'Błąd podczas dodawania trenera.']);
        }
    } else {
        // Error inserting into users table
        echo json_encode(['status' => 'error', 'message' => 'Błąd podczas dodawania użytkownika.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Nieprawidłowe dane wejściowe.']);
}

$conn->close();
?>
