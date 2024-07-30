<?php
include '../scripts/db.php';

// Funkcja do sanitacji danych wejściowych
function sanitize($conn, $data) {
    return mysqli_real_escape_string($conn, htmlspecialchars($data));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_horse'])) {
        // Dodawanie konia
        $imie = sanitize($conn, $_POST['imie']);
        $wiek = sanitize($conn, $_POST['wiek']);
        $rasa = sanitize($conn, $_POST['rasa']);
        $kolor = sanitize($conn, $_POST['kolor']);
        $wzrost = sanitize($conn, $_POST['wzrost']);
        $stan_zdrowia = sanitize($conn, $_POST['stan_zdrowia']);
        $rodzaj_konia = sanitize($conn, $_POST['rodzaj_konia']);
        $opis = sanitize($conn, $_POST['opis']);

        echo '$imie + $wiek + $rasa + $kolor + $wzrost + "$stan_zdrowia + $rodzaj_konia + $opis';
        
        // Sprawdzanie i przesyłanie zdjęcia, jeśli zostało dołączone
        $zdjecie = null;
        if (isset($_FILES['trainer_image']) && $_FILES['trainer_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['trainer_image']['tmp_name'];
            $fileName = $_FILES['trainer_image']['name'];
            $fileSize = $_FILES['trainer_image']['size'];
            $fileType = $_FILES['trainer_image']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $allowedfileExtensions = array('jpg', 'gif', 'png', 'webp');
            if (in_array($fileExtension, $allowedfileExtensions)) {
                $uploadFileDir = '..\img\horses\\';
                $dest_path = $uploadFileDir . uniqid() . '.' . $fileExtension;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    // Przypisanie ścieżki zdjęcia
                    $zdjecie = 'img/horses/' . basename($dest_path);
                } else {
                    $_SESSION['error'] = 'Błąd podczas przesyłania zdjęcia. Upewnij się, że katalog docelowy jest zapisywalny.';
                    header('Location: ../sites/dashboard.php?page=download_horse.php');
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Nieprawidłowy format zdjęcia. Dozwolone formaty to: ' . implode(',', $allowedfileExtensions);
                header('Location: ../sites/dashboard.php?page=download_horse.php');
                exit();
            }
        }

        $sql_insert_horse = "INSERT INTO horses (imie, data_urodzenia, rasa, kolor, wzrost, stan_zdrowia, rodzaj_konia, opis, zdjecie) 
                            VALUES ('$imie', '$wiek', '$rasa', '$kolor', '$wzrost', '$stan_zdrowia', '$rodzaj_konia', '$opis', '$zdjecie')";

        if ($conn->query($sql_insert_horse) === TRUE) {
            $_SESSION['message'] = 'Koń został dodany.';
        } else {
            $_SESSION['error'] = 'Błąd: ' . $conn->error;
        }
    } elseif (isset($_POST['edit_horse'])) {
        // Edytowanie konia admin
        $id = sanitize($conn, $_POST['id']);
        $imie = sanitize($conn, $_POST['imie']);
        $wiek = sanitize($conn, $_POST['wiek']);
        $rasa = sanitize($conn, $_POST['rasa']);
        $kolor = sanitize($conn, $_POST['kolor']);
        $wzrost = sanitize($conn, $_POST['wzrost']);
        $stan_zdrowia = sanitize($conn, $_POST['stan_zdrowia']);
        $rodzaj_konia = sanitize($conn, $_POST['rodzaj_konia']);
        $opis = sanitize($conn, $_POST['opis']);
        
        // Pobranie obecnej ścieżki do zdjęcia z bazy danych
        $sql_get_current_image = "SELECT zdjecie FROM horses WHERE id='$id'";
        $result = $conn->query($sql_get_current_image);
        $current_image = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['zdjecie'] : null;

        // Sprawdzanie i przesyłanie nowego zdjęcia, jeśli zostało dołączone
        $new_image_path = $current_image; // Domyślnie używamy starego zdjęcia
        if (isset($_FILES['trainer_image']) && $_FILES['trainer_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['trainer_image']['tmp_name'];
            $fileName = $_FILES['trainer_image']['name'];
            $fileSize = $_FILES['trainer_image']['size'];
            $fileType = $_FILES['trainer_image']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $allowedfileExtensions = array('jpg', 'gif', 'png', 'webp');
            if (in_array($fileExtension, $allowedfileExtensions)) {
                $uploadFileDir = '..\img\horses\\';
                $dest_path = $uploadFileDir . uniqid() . '.' . $fileExtension;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    // Przypisanie ścieżki nowego zdjęcia
                    $new_image_path = 'img/horses/' . basename($dest_path);
                    // Usunięcie starego zdjęcia, jeśli istnieje
                    if ($current_image && file_exists('..\\' . $current_image)) {
                        unlink('..\\' . $current_image);
                    }
                } else {
                    $_SESSION['error'] = 'Błąd podczas przesyłania nowego zdjęcia. Upewnij się, że katalog docelowy jest zapisywalny.';
                    header('Location: ../sites/dashboard.php?page=download_horse.php');
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Nieprawidłowy format zdjęcia. Dozwolone formaty to: ' . implode(',', $allowedfileExtensions);
                header('Location: ../sites/dashboard.php?page=download_horse.php');
                exit();
            }
        }

        $sql_update_horse = "UPDATE horses SET imie='$imie', data_urodzenia='$wiek', rasa='$rasa', kolor='$kolor', wzrost='$wzrost', stan_zdrowia='$stan_zdrowia', 
                            rodzaj_konia='$rodzaj_konia', opis='$opis', zdjecie='$new_image_path' WHERE id='$id'";

        if ($conn->query($sql_update_horse) === TRUE) {
            $_SESSION['message'] = 'Koń został zaktualizowany.';
        } else {
            $_SESSION['error'] = 'Błąd: ' . $conn->error;
        }

    } elseif (isset($_POST['edit_horse_trainer'])) {
        // Edytowanie konia trener - stan zdrowia
        $id = sanitize($conn, $_POST['id']);
        $stan_zdrowia = sanitize($conn, $_POST['stan_zdrowia']);
        
        $sql_update_horse_trainer = "UPDATE horses SET stan_zdrowia='$stan_zdrowia' WHERE id='$id'";

        if ($conn->query($sql_update_horse_trainer) === TRUE) {
            $_SESSION['message'] = 'Koń został zaktualizowany.';
        } else {
            $_SESSION['error'] = 'Błąd: ' . $conn->error;
        }

    } elseif (isset($_POST['delete_horse'])) {
        // Usuwanie konia
        $id = sanitize($conn, $_POST['id']);

        // Pobranie ścieżki do zdjęcia z bazy danych
        $sql_get_current_image = "SELECT zdjecie FROM horses WHERE id='$id'";
        $result = $conn->query($sql_get_current_image);
        $current_image = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['zdjecie'] : null;

        // Usunięcie konia
        $sql_delete_horse = "DELETE FROM horses WHERE id='$id'";
        if ($conn->query($sql_delete_horse) === TRUE) {
            // Usunięcie pliku zdjęcia, jeśli istnieje
            if ($current_image && file_exists('C:\xampp\htdocs\websites\HorseApp\\' . $current_image)) {
                unlink('C:\xampp\htdocs\websites\HorseApp\\' . $current_image);
            }
            $_SESSION['message'] = 'Koń został usunięty.';
        } else {
            $_SESSION['error'] = 'Błąd: ' . $conn->error;
        }
    }
}

$conn->close();
header("Location: ../sites/dashboard.php?page=download_horse.php"); 
?>
