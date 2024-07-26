<?php
include './db.php';

function sanitize($conn, $data) {
    return htmlspecialchars(mysqli_real_escape_string($conn, trim($data)));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        $imie = sanitize($conn, $_POST['imie']);
        $nazwisko = sanitize($conn, $_POST['nazwisko']);
        $email = sanitize($conn, $_POST['email']);
        $ulica = sanitize($conn, $_POST['ulica']);
        $nr_domu = sanitize($conn, $_POST['nr_domu']);
        $kod_pocztowy = sanitize($conn, $_POST['kod_pocztowy']);
        $miasto = sanitize($conn, $_POST['miasto']);
        $telefon = sanitize($conn, $_POST['telefon']);
        $hashed_password = password_hash($_POST['hashed_password'], PASSWORD_DEFAULT);
        $rola = sanitize($conn, $_POST['rola']);
        $stopien_jezdziecki = sanitize($conn, $_POST['stopien_jezdziecki']);

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
                $uploadFileDir = 'C:\xampp\htdocs\websites\HorseApp\V 2.0\img\users\\';
                $dest_path = $uploadFileDir . uniqid() . '.' . $fileExtension;
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $zdjecie = 'img/users/' . basename($dest_path);
                } else {
                    $_SESSION['error'] = 'Błąd podczas przesyłania pliku.';
                }
            } else {
                $_SESSION['error'] = 'Nieprawidłowy format pliku.';
            }
        }

        $sql = "INSERT INTO users (imie, nazwisko, email, ulica, nr_domu, kod_pocztowy, miasto, telefon, zdjecie, hashed_password, rola, stopien_jezdziecki) 
                VALUES ('$imie', '$nazwisko', '$email', '$ulica', '$nr_domu', '$kod_pocztowy', '$miasto', '$telefon', '$zdjecie', '$hashed_password', '$rola', '$stopien_jezdziecki')";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Użytkownik dodany pomyślnie!";
        } else {
            $_SESSION['error'] = "Błąd: " . $conn->error;
        }

    } elseif (isset($_POST['edit_user'])) {
        $id = sanitize($conn, $_POST['id']);
        $imie = sanitize($conn, $_POST['imie']);
        $nazwisko = sanitize($conn, $_POST['nazwisko']);
        $email = sanitize($conn, $_POST['email']);
        $ulica = sanitize($conn, $_POST['ulica']);
        $nr_domu = sanitize($conn, $_POST['nr_domu']);
        $kod_pocztowy = sanitize($conn, $_POST['kod_pocztowy']);
        $miasto = sanitize($conn, $_POST['miasto']);
        $telefon = sanitize($conn, $_POST['telefon']);
        $rola = sanitize($conn, $_POST['rola']);
        $stopien_jezdziecki = sanitize($conn, $_POST['stopien_jezdziecki']);

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
                $uploadFileDir = 'C:\xampp\htdocs\websites\HorseApp\V 2.0\img\users\\';
                $dest_path = $uploadFileDir . uniqid() . '.' . $fileExtension;
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $zdjecie = 'img/users/' . basename($dest_path);

                    // Usuń stare zdjęcie, jeśli istnieje
                    $sql_select = "SELECT zdjecie FROM users WHERE id='$id'";
                    $result = $conn->query($sql_select);
                    if ($result && $result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $old_zdjecie = $row['zdjecie'];
                        if ($old_zdjecie && file_exists($uploadFileDir . basename($old_zdjecie))) {
                            unlink($uploadFileDir . basename($old_zdjecie));
                        }
                    }
                } else {
                    $_SESSION['error'] = 'Błąd podczas przesyłania pliku.';
                }
            } else {
                $_SESSION['error'] = 'Nieprawidłowy format pliku.';
            }
        }

        if ($zdjecie) {
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
        } else {
            $sql = "UPDATE users SET 
                        imie='$imie',
                        nazwisko='$nazwisko',
                        email='$email',
                        ulica='$ulica',
                        nr_domu='$nr_domu',
                        kod_pocztowy='$kod_pocztowy',
                        miasto='$miasto',
                        telefon='$telefon',
                        rola='$rola',
                        stopien_jezdziecki='$stopien_jezdziecki'
                    WHERE id='$id'";
        }

        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Użytkownik zaktualizowany pomyślnie!";
        } else {
            $_SESSION['error'] = "Błąd: " . $conn->error;
        }

    } elseif (isset($_POST['delete_user'])) {
        $id = sanitize($conn, $_POST['id']);

        // Usuń stare zdjęcie, jeśli istnieje
        $sql_select = "SELECT zdjecie FROM users WHERE id='$id'";
        $result = $conn->query($sql_select);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $old_zdjecie = $row['zdjecie'];
            if ($old_zdjecie && file_exists('C:\xampp\htdocs\websites\HorseApp\V 2.0\img\users\\' . basename($old_zdjecie))) {
                unlink('C:\xampp\htdocs\websites\HorseApp\V 2.0\img\users\\' . basename($old_zdjecie));
            }
        }

        $sql = "DELETE FROM users WHERE id='$id'";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Użytkownik usunięty pomyślnie!";
        } else {
            $_SESSION['error'] = "Błąd: " . $conn->error;
        }
    }
}

$conn->close();
if ($_SESSION['user_role'] !== 'administrator' && $_SESSION['user_role'] !== 'trener'){
    header("Location: ../sites/dashboard.php?page=client_data.php");
    exit();
}else{
    header("Location: ../sites/dashboard.php?page=client_data_All.php");
    exit();
}

?>
