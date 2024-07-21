<?php
include '../scripts/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_horse'])) {
        // Dodawanie konia
        $imie = $_POST['imie'];
        $wiek = $_POST['wiek'];
        $rasa = $_POST['rasa'];
        $stan_zdrowia = $_POST['stan_zdrowia'];
        $rodzaj_konia = $_POST['rodzaj_konia'];
        $opis = $_POST['opis'];
        $zdjecie = $_POST['zdjecie'];

        $sql = "INSERT INTO horses (imie, wiek, rasa, stan_zdrowia, rodzaj_konia, opis, zdjecie) 
                VALUES ('$imie', '$wiek', '$rasa', '$stan_zdrowia', '$rodzaj_konia', '$opis', '$zdjecie')";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Koń został dodany.";
        } else {
            $_SESSION['error'] = "Błąd: " . $conn->error;
        }

    } elseif (isset($_POST['edit_horse'])) {
        // Edytowanie konia
        $id = $_POST['id'];
        $imie = $_POST['imie'];
        $wiek = $_POST['wiek'];
        $rasa = $_POST['rasa'];
        $stan_zdrowia = $_POST['stan_zdrowia'];
        $rodzaj_konia = $_POST['rodzaj_konia'];
        $opis = $_POST['opis'];
        $zdjecie = $_POST['zdjecie'];

        $sql = "UPDATE horses SET imie='$imie', wiek='$wiek', rasa='$rasa', stan_zdrowia='$stan_zdrowia', 
                rodzaj_konia='$rodzaj_konia', opis='$opis', zdjecie='$zdjecie' WHERE id='$id'";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Koń został zaktualizowany.";
        } else {
            $_SESSION['error'] = "Błąd: " . $conn->error;
        }

    } elseif (isset($_POST['delete_horse'])) {
        // Usuwanie konia
        $id = $_POST['id'];

        $sql = "DELETE FROM horses WHERE id='$id'";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Koń został usunięty.";
        } else {
            $_SESSION['error'] = "Błąd: " . $conn->error;
        }
    }
}

$conn->close();
header("Location: ../sites/admin_panel.php?page=download_horse.php"); // Zaktualizuj ścieżkę do odpowiedniej strony
?>
