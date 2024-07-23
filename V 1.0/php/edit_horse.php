<?php
include '../php/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $horse_id = $_POST['id'];
    $stan_zdrowia = $_POST['stan_zdrowia'];
    $opis = $_POST['opis'];

    // Check connection
    if ($conn->connect_error) {
        echo "Błąd połączenia: " . $conn->connect_error;
        exit();
    }

    // Prepare SQL query to update the horse
    $query = "UPDATE horses SET stan_zdrowia = ?, opis = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        echo "Błąd przygotowania zapytania: " . $conn->error;
        exit();
    }

    $stmt->bind_param("ssi", $stan_zdrowia, $opis, $horse_id);

    if ($stmt->execute()) {
        echo "Sukces";
    } else {
        echo "Błąd wykonania zapytania: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $horse_id = $_GET['id'];

    // Check connection
    if ($conn->connect_error) {
        echo "Błąd połączenia: " . $conn->connect_error;
        exit();
    }

    // Prepare SQL query to fetch the horse details
    $query = "SELECT * FROM horses WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        echo "Błąd przygotowania zapytania: " . $conn->error;
        exit();
    }

    $stmt->bind_param("i", $horse_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $horse = $result->fetch_assoc();

    $stmt->close();
    $conn->close();

    if ($horse) {
        ?>
        <h2>Edytuj konia</h2>
        <form id="edit-horse-form">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($horse['id']); ?>">
            <label for="stan_zdrowia">Stan Zdrowia:</label>
            <input type="text" id="stan_zdrowia" name="stan_zdrowia" autocomplete="health-condition" value="<?php echo htmlspecialchars($horse['stan_zdrowia']); ?>">
            <br>
            <label for="opis">Opis:</label>
            <textarea id="opis" name="opis" autocomplete="description"><?php echo htmlspecialchars($horse['opis']); ?></textarea>
            <br>
            <button type="submit" class="table-button">Zapisz zmiany</button>
        </form>
        <?php
    } else {
        echo "Nie znaleziono konia o podanym ID.";
    }
} else {
    echo "Niewłaściwa metoda żądania";
}
?>

