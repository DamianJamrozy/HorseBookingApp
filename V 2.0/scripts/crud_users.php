<?php
session_start();
include './db.php';

function sanitize($conn, $data) {
    return htmlspecialchars(mysqli_real_escape_string($conn, trim($data)));
}

// Funkcja do przesyłania pliku
function uploadImage($file, $role)
{
    $allowedfileExtensions = array('jpg', 'gif', 'png', 'webp');
    $uploadFileDir = __DIR__ . '/../img/';
    if ($role == 1 || $role == 2) { // administrator lub trener
        $uploadFileDir .= 'employee/';
    } else {
        $uploadFileDir .= 'users/';
    }

    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $file['tmp_name'];
        $fileName = $file['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        if (in_array($fileExtension, $allowedfileExtensions)) {
            $dest_path = $uploadFileDir . uniqid() . '.' . $fileExtension;

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

function moveImage($oldImagePath, $newRole)
{
    $baseDir = __DIR__ . '/../';
    $oldFullPath = $baseDir . $oldImagePath;
    $newDir = $baseDir . 'img/' . ($newRole == 1 || $newRole == 2 ? 'employee/' : 'users/');
    $newImagePath = $newDir . uniqid() . '.' . pathinfo($oldFullPath, PATHINFO_EXTENSION);

    if (!is_writable($newDir)) {
        throw new Exception('Katalog docelowy nie jest zapisywalny: ' . $newDir);
    }

    if (file_exists($oldFullPath)) {
        if (rename($oldFullPath, $newImagePath)) {
            return 'img/' . ($newRole == 1 || $newRole == 2 ? 'employee/' : 'users/') . basename($newImagePath);
        } else {
            throw new Exception('Błąd podczas przenoszenia pliku.');
        }
    } else {
        throw new Exception('Plik nie istnieje: ' . $oldFullPath);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
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
            $rola = sanitize($conn, $_POST['rola']);
            $stopien_jezdziecki = sanitize($conn, $_POST['stopien_jezdziecki']);

            // Przesyłanie zdjęcia, jeśli zostało dołączone
            $zdjecie = null;
            if (isset($_FILES['trainer_image']) && $_FILES['trainer_image']['error'] === UPLOAD_ERR_OK) {
                $zdjecie = uploadImage($_FILES['trainer_image'], $rola);
                if ($zdjecie) {
                    $zdjecie = 'img/' . ($rola == 1 || $rola == 2 ? 'employee/' : 'users/') . basename($zdjecie);
                }
            }

            $sql = "INSERT INTO users (imie, nazwisko, email, ulica, nr_domu, kod_pocztowy, miasto, telefon, zdjecie, hashed_password, rola, stopien_jezdziecki) 
                    VALUES ('$imie', '$nazwisko', '$email', '$ulica', '$nr_domu', '$kod_pocztowy', '$miasto', '$telefon', '$zdjecie', '$hashed_password', '$rola', '$stopien_jezdziecki')";

            if ($conn->query($sql) === TRUE) {
                $_SESSION['message'] = "Użytkownik dodany pomyślnie!";
            } else {
                throw new Exception("Błąd: " . $conn->error);
            }

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

    } elseif (isset($_POST['edit_user'])) {
        try {
            $id = sanitize($conn, $_POST['id']);
            $imie = sanitize($conn, $_POST['imie']);
            $nazwisko = sanitize($conn, $_POST['nazwisko']);
            $email = sanitize($conn, $_POST['email']);
            $ulica = sanitize($conn, $_POST['ulica']);
            $nr_domu = sanitize($conn, $_POST['nr_domu']);
            $kod_pocztowy = sanitize($conn, $_POST['kod_pocztowy']);
            $miasto = sanitize($conn, $_POST['miasto']);
            $telefon = sanitize($conn, $_POST['telefon']);
            $rola = $_SESSION['user_role'] == 'administrator' ? sanitize($conn, $_POST['rola']) : null;
            $stopien_jezdziecki = sanitize($conn, $_POST['stopien_jezdziecki']);

            // Przesyłanie nowego zdjęcia, jeśli zostało dołączone
            $zdjecie = null;
            if (isset($_FILES['trainer_image']) && $_FILES['trainer_image']['error'] === UPLOAD_ERR_OK) {
                $zdjecie = uploadImage($_FILES['trainer_image'], $rola);
                if ($zdjecie) {
                    $zdjecie = 'img/' . ($rola == 1 || $rola == 2 ? 'employee/' : 'users/') . basename($zdjecie);

                    // Usuń stare zdjęcie, jeśli istnieje
                    $sql_select = "SELECT zdjecie FROM users WHERE id='$id'";
                    $result = $conn->query($sql_select);
                    if ($result && $result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $old_zdjecie = $row['zdjecie'];
                        if ($old_zdjecie && file_exists(__DIR__ . '/../' . $old_zdjecie)) {
                            unlink(__DIR__ . '/../' . $old_zdjecie);
                        }
                    }
                }
            }

            // Przeniesienie istniejącego zdjęcia, jeśli rola została zmieniona
            if ($rola) {
                $sql_select = "SELECT zdjecie, rola FROM users WHERE id='$id'";
                $result = $conn->query($sql_select);
                if ($result && $result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    if ($row['rola'] != $rola && $row['zdjecie']) {
                        $zdjecie = moveImage($row['zdjecie'], $rola);
                    }
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
                throw new Exception("Błąd: " . $conn->error);
            }

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

    } elseif (isset($_POST['delete_user'])) {
        try {
            $id = sanitize($conn, $_POST['id']);

            // Usuń stare zdjęcie, jeśli istnieje
            $sql_select = "SELECT zdjecie FROM users WHERE id='$id'";
            $result = $conn->query($sql_select);
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $old_zdjecie = $row['zdjecie'];
                if ($old_zdjecie && file_exists(__DIR__ . '/../' . $old_zdjecie)) {
                    unlink(__DIR__ . '/../' . $old_zdjecie);
                }
            }

            $sql = "DELETE FROM users WHERE id='$id'";

            if ($conn->query($sql) === TRUE) {
                $_SESSION['message'] = "Użytkownik usunięty pomyślnie!";
            } else {
                throw new Exception("Błąd: " . $conn->error);
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
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

<script>
    // Przekazanie wartości zmiennej PHP do JavaScript
    var userRole = '<?php echo $_SESSION['user_role']; ?>';

    // Wypisanie wartości zmiennej w konsoli
    console.log('User role:', userRole);
</script>
