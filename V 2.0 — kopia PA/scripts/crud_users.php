<?php
include './db.php';

if (isset($_POST['add_user'])) {
    // Dodawanie nowego użytkownika
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $email = $_POST['email'];
    $ulica = $_POST['ulica'];
    $nr_domu = $_POST['nr_domu'];
    $kod_pocztowy = $_POST['kod_pocztowy'];
    $miasto = $_POST['miasto'];
    $telefon = $_POST['telefon'];
    $zdjecie = $_POST['zdjecie'];
    $rola = $_POST['rola'];
    $stopien_jezdziecki = $_POST['stopien_jezdziecki'];

    $sql = "INSERT INTO users (imie, nazwisko, email, ulica, nr_domu, kod_pocztowy, miasto, telefon, zdjecie, rola, stopien_jezdziecki) VALUES ('$imie', '$nazwisko', '$email', '$ulica', '$nr_domu', '$kod_pocztowy', '$miasto', '$telefon', '$zdjecie', '$rola', '$stopien_jezdziecki')";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Użytkownik dodany pomyślnie!";
    } else {
        $_SESSION['error'] = "Błąd: " . $conn->error;
    }

} elseif (isset($_POST['edit_user'])) {
    $id = $_POST['id'];
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $email = $_POST['email'];
    $ulica = $_POST['ulica'];
    $nr_domu = $_POST['nr_domu'];
    $kod_pocztowy = $_POST['kod_pocztowy'];
    $miasto = $_POST['miasto'];
    $telefon = $_POST['telefon'];
    $zdjecie = $_POST['zdjecie'];
    $rola = $_POST['rola'];
    $stopien_jezdziecki = $_POST['stopien_jezdziecki'];

    $sql = "UPDATE users SET 
                imie='$imie',
                nazwisko='$nazwisko',
                email='$email',
                ulica='$ulica',
                nr_domu='$nr_domu',
                kod_pocztowy='$kod_pocztowy',
                miasto='$miasto',
                telefon='$telefon',
                zdjecie='$zdjecie',
                rola='$rola',
                stopien_jezdziecki='$stopien_jezdziecki'
            WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Użytkownik zaktualizowany pomyślnie!";
    } else {
        $_SESSION['error'] = "Błąd: " . $conn->error;
    }

} elseif (isset($_POST['delete_user'])) {
    // Usuwanie użytkownika
    $id = $_POST['id'];

    $sql = "DELETE FROM users WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Użytkownik usunięty pomyślnie!";
    } else {
        $_SESSION['error'] = "Błąd: " . $conn->error;
    }
}

$conn->close();
header("Location: ../sites/admin_panel.php?page=client_data_All.php");
exit();
?>