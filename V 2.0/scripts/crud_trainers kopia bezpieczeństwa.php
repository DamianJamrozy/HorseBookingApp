<?php
session_start();
include './db.php';

// Funkcja do zabezpieczania danych przed wstrzykiwaniem SQL
function sanitize($conn, $data)
{
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags($data)));
}

// Dodawanie trenera
if (isset($_POST['add_trainer']) && $_POST['add_trainer'] == '1') {
    $imie = sanitize($conn, $_POST['imie']);
    $nazwisko = sanitize($conn, $_POST['nazwisko']);
    $email = sanitize($conn, $_POST['email']);
    $ulica = sanitize($conn, $_POST['ulica']);
    $nr_domu = sanitize($conn, $_POST['nr_domu']);
    $kod_pocztowy = sanitize($conn, $_POST['kod_pocztowy']);
    $miasto = sanitize($conn, $_POST['miasto']);
    $telefon = sanitize($conn, $_POST['telefon']);
    $zdjecie = sanitize($conn, $_POST['zdjecie']);
    $hashed_password = password_hash($_POST['hashed_password'], PASSWORD_DEFAULT);
    $stopien_jezdziecki = sanitize($conn, $_POST['stopien_jezdziecki']);
    $trener = 'trener';


    if (isset($_FILES['trainer_image']) && $_FILES['trainer_image']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['trainer_image']['tmp_name'];
    $fileName = $_FILES['trainer_image']['name'];
    $fileSize = $_FILES['trainer_image']['size'];
    $fileType = $_FILES['trainer_image']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    $allowedfileExtensions = array('jpg', 'gif', 'png', 'webp');
    if (in_array($fileExtension, $allowedfileExtensions)) {
        $employeeId = $_POST['employee_id'];
        $uploadFileDir = 'C:\xampp\htdocs\websites\HorseApp\V 2.0\img\employee\\';
        $dest_path = $uploadFileDir . $employeeId . '.webp';

        if(move_uploaded_file($fileTmpPath, $dest_path)) {
            $message ='File is successfully uploaded.';
        } else {
            $message = 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
        }
    } else {
        $message = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
    }
} else {
    $message = 'There is some error in the file upload. Please check the following error.<br>';
    $message .= 'Error:' . $_FILES['trainer_image']['error'];
}
echo $message;




    $sql_insert_user = "INSERT INTO users (imie, nazwisko, email, ulica, nr_domu, kod_pocztowy, miasto, telefon, zdjecie, hashed_password, rola, stopien_jezdziecki)
                        VALUES ('$imie', '$nazwisko', '$email', '$ulica', '$nr_domu', '$kod_pocztowy', '$miasto', '$telefon', '$zdjecie', '$hashed_password','$trener', '$stopien_jezdziecki')";

    if ($conn->query($sql_insert_user) === TRUE) {
        $last_id = $conn->insert_id;
        $sql_insert_trainer = "INSERT INTO trainers (user_id) VALUES ('$last_id')";
        if ($conn->query($sql_insert_trainer) === TRUE) {
            $_SESSION['message'] = 'Trener został dodany.';
        } else {
            $_SESSION['error'] = 'Błąd podczas dodawania trenera.';
        }
    } else {
        $_SESSION['error'] = 'Błąd podczas dodawania użytkownika.';
    }

    header('Location: ../sites/dashboard.php?page=download_trainers.php');
    exit();
}

// Edytowanie trenera
if (isset($_POST['edit_trainer']) && $_POST['edit_trainer'] == '1') {
    $user_id = sanitize($conn, $_POST['user_id']);
    $imie = sanitize($conn, $_POST['imie']);
    $nazwisko = sanitize($conn, $_POST['nazwisko']);
    $email = sanitize($conn, $_POST['email']);
    $ulica = sanitize($conn, $_POST['ulica']);
    $nr_domu = sanitize($conn, $_POST['nr_domu']);
    $kod_pocztowy = sanitize($conn, $_POST['kod_pocztowy']);
    $miasto = sanitize($conn, $_POST['miasto']);
    $telefon = sanitize($conn, $_POST['telefon']);
    $zdjecie = sanitize($conn, $_POST['zdjecie']);
    $stopien_jezdziecki = sanitize($conn, $_POST['stopien_jezdziecki']);

    $sql_update_user = "UPDATE users SET imie='$imie', nazwisko='$nazwisko', email='$email', ulica='$ulica', nr_domu='$nr_domu', kod_pocztowy='$kod_pocztowy', miasto='$miasto', telefon='$telefon', zdjecie='$zdjecie', stopien_jezdziecki='$stopien_jezdziecki' WHERE id='$user_id'";

    if ($conn->query($sql_update_user) === TRUE) {
        $_SESSION['message'] = 'Trener został zaktualizowany.';
    } else {
        $_SESSION['error'] = 'Błąd podczas aktualizacji trenera.';
    }

    header('Location: ../sites/dashboard.php?page=download_trainers.php');
    exit();
}

// Usuwanie trenera
if (isset($_POST['delete_trainer']) && $_POST['delete_trainer'] == '1') {
    $user_id = sanitize($conn, $_POST['user_id']);

    // Usunięcie trenera
    $sql_delete_trainer = "DELETE FROM trainers WHERE user_id='$user_id'";
    if ($conn->query($sql_delete_trainer) === TRUE) {
        // Usunięcie użytkownika
        $sql_delete_user = "DELETE FROM users WHERE id='$user_id'";
        if ($conn->query($sql_delete_user) === TRUE) {
            $_SESSION['message'] = 'Trener został usunięty.';
        } else {
            $_SESSION['error'] = 'Błąd podczas usuwania trenera.';
        }
    } else {
        $_SESSION['error'] = 'Błąd podczas usuwania trenera.';
    }

    header('Location: ../sites/dashboard.php?page=download_trainers.php');
    exit();
}

$conn->close();
?>