<?php
session_start();
include './db.php';

// Funkcja do zabezpieczania danych przed wstrzykiwaniem SQL
function sanitize($conn, $data)
{
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags($data)));
}

// Funkcja do przesyłania pliku
function uploadImage($file)
{
    $allowedfileExtensions = array('jpg', 'gif', 'png', 'webp');
    $uploadFileDir = __DIR__ . '/../img/employee/'; // Dodaj __DIR__ dla bezwzględnej ścieżki

    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $file['tmp_name'];
        $fileName = $file['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        if (in_array($fileExtension, $allowedfileExtensions)) {
            $dest_path = $uploadFileDir . uniqid() . '.' . $fileExtension;

            // Debugowanie: Sprawdzenie ścieżki i uprawnień
            if (!is_writable($uploadFileDir)) {
                throw new Exception('Katalog docelowy nie jest zapisywalny: ' . $uploadFileDir);
            }

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                return $dest_path;
            } else {
                throw new Exception('Błąd podczas przesyłania zdjęcia. Upewnij się, że katalog docelowy jest zapisywalny.');
            }
        } else {
            throw new Exception('Nieprawidłowy format zdjęcia. Dozwolone formaty to: ' . implode(',', $allowedfileExtensions));
        }
    }
    return null;
}

// Dodawanie trenera
if (isset($_POST['add_trainer']) && $_POST['add_trainer'] == '1') {
    try {
        $imie = sanitize($conn, $_POST['imie']);
        $nazwisko = sanitize($conn, $_POST['nazwisko']);
        $email = sanitize($conn, $_POST['email']);
        $ulica = sanitize($conn, $_POST['ulica']);
        $nr_domu = sanitize($conn, $_POST['nr_domu']);
        $kod_pocztowy = sanitize($conn, $_POST['kod_pocztowy']);
        $miasto = sanitize($conn, $_POST['miasto']);
        $telefon = sanitize($conn, $_POST['telefon']);
        $hashed_password = password_hash($_POST['hashed_password'], PASSWORD_DEFAULT);
        $stopien_jezdziecki_id = sanitize($conn, $_POST['stopien_jezdziecki']);

        // Pobranie id roli z bazy danych
        $rola = 'trener';
        $stmt = $conn->prepare("SELECT id_type FROM users_type WHERE rola = ?");
        $stmt->bind_param('s', $rola);
        $stmt->execute();
        $result = $stmt->get_result();
        $rola_id = $result->fetch_assoc()['id_type'] ?? null;

        if (!$rola_id) {
            throw new Exception('Błąd: Nie znaleziono roli trener.');
        }

        // Domyślnie wartość zmiennej zdjecie na NULL
        $zdjecie = null;

        // Przesyłanie zdjęcia, jeśli zostało dołączone
        if (isset($_FILES['trainer_image']) && $_FILES['trainer_image']['error'] === UPLOAD_ERR_OK) {
            $zdjecie = uploadImage($_FILES['trainer_image']);
        }

        // Zapisanie ścieżki względem katalogu projektu
        if ($zdjecie) {
            $zdjecie = 'img/employee/' . basename($zdjecie);
        }

        $stmt = $conn->prepare("INSERT INTO users (imie, nazwisko, email, ulica, nr_domu, kod_pocztowy, miasto, telefon, zdjecie, hashed_password, rola, stopien_jezdziecki) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssssssssss', $imie, $nazwisko, $email, $ulica, $nr_domu, $kod_pocztowy, $miasto, $telefon, $zdjecie, $hashed_password, $rola_id, $stopien_jezdziecki_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = 'Trener został dodany.';
        } else {
            throw new Exception('Błąd podczas dodawania użytkownika.');
        }

        header('Location: ../sites/dashboard.php?page=download_trainers.php');
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: ../sites/dashboard.php?page=download_trainers.php');
        exit();
    }
}

// Edytowanie trenera
if (isset($_POST['edit_trainer']) && $_POST['edit_trainer'] == '1') {
    try {
        $user_id = sanitize($conn, $_POST['user_id']);
        $imie = sanitize($conn, $_POST['imie']);
        $nazwisko = sanitize($conn, $_POST['nazwisko']);
        $email = sanitize($conn, $_POST['email']);
        $ulica = sanitize($conn, $_POST['ulica']);
        $nr_domu = sanitize($conn, $_POST['nr_domu']);
        $kod_pocztowy = sanitize($conn, $_POST['kod_pocztowy']);
        $miasto = sanitize($conn, $_POST['miasto']);
        $telefon = sanitize($conn, $_POST['telefon']);
        $stopien_jezdziecki_id = sanitize($conn, $_POST['stopien_jezdziecki']);

        // Pobranie obecnej ścieżki do zdjęcia z bazy danych
        $stmt = $conn->prepare("SELECT zdjecie FROM users WHERE id = ?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $current_image = ($result->num_rows > 0) ? $result->fetch_assoc()['zdjecie'] : null;

        // Przesyłanie nowego zdjęcia, jeśli zostało dołączone
        $new_image_path = $current_image; // Domyślnie używamy starego zdjęcia
        if (isset($_FILES['trainer_image']) && $_FILES['trainer_image']['error'] === UPLOAD_ERR_OK) {
            $new_image_path = uploadImage($_FILES['trainer_image']);
            if ($current_image && file_exists(__DIR__ . '/../' . $current_image)) {
                unlink(__DIR__ . '/../' . $current_image);
            }
            $new_image_path = 'img/employee/' . basename($new_image_path);
        }

        $stmt = $conn->prepare("UPDATE users SET imie = ?, nazwisko = ?, email = ?, ulica = ?, nr_domu = ?, kod_pocztowy = ?, miasto = ?, telefon = ?, zdjecie = ?, stopien_jezdziecki = ? WHERE id = ?");
        $stmt->bind_param('ssssssssssi', $imie, $nazwisko, $email, $ulica, $nr_domu, $kod_pocztowy, $miasto, $telefon, $new_image_path, $stopien_jezdziecki_id, $user_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = 'Trener został zaktualizowany.';
        } else {
            throw new Exception('Błąd podczas aktualizacji trenera.');
        }

        header('Location: ../sites/dashboard.php?page=download_trainers.php');
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: ../sites/dashboard.php?page=download_trainers.php');
        exit();
    }
}

// Usuwanie trenera
if (isset($_POST['delete_trainer']) && $_POST['delete_trainer'] == '1') {
    try {
        $user_id = sanitize($conn, $_POST['user_id']);

        // Pobranie ścieżki do zdjęcia z bazy danych
        $stmt = $conn->prepare("SELECT zdjecie FROM users WHERE id = ?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $image_path = ($result->num_rows > 0) ? $result->fetch_assoc()['zdjecie'] : null;

        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param('i', $user_id);

        if ($stmt->execute()) {
            if ($image_path && file_exists(__DIR__ . '/../' . $image_path)) {
                unlink(__DIR__ . '/../' . $image_path);
            }
            $_SESSION['message'] = 'Trener został usunięty.';
        } else {
            throw new Exception('Błąd podczas usuwania trenera.');
        }

        header('Location: ../sites/dashboard.php?page=download_trainers.php');
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: ../sites/dashboard.php?page=download_trainers.php');
        exit();
    }
}
?>
