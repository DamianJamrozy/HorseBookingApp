<!-- plik który pobiera dane z tabeli horses  -->
<?php
include '../php/db.php';

// Sprawdzenie połączenia
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Zapytanie SQL o sprowadzenie danych koni
$query = "SELECT * FROM horses";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "<h2>Lista koni:</h2>";
    echo "<table>";
    echo "<tr>
            <th>Imię</th>
            <th>Wiek</th>
            <th>Rasa</th>
            <th>Stan Zdrowia</th>
            <th>Rodzaj Konia</th>
            <th>Opis</th>
            <th>Zdjęcie</th>
            <th>Edytuj</th> 
            <th>Usuń</th> 
          </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['imie']}</td>
                <td>{$row['wiek']}</td>
                <td>{$row['rasa']}</td>
                <td>{$row['stan_zdrowia']}</td>
                <td>{$row['rodzaj_konia']}</td>
                <td>{$row['opis']}</td>
                <td><img src='../{$row['zdjecie']}' alt='{$row['imie']}' width='100'></td>
                <td><button class='edit-button' data-id='{$row['id']}'>Edytuj</button></td>
                <td><button class='delete-button' data-id='{$row['id']}'>Usuń</button></td>           
              </tr>";
    }
    echo "</table>";
} else {
    echo "<h2 style='color: red;'>Brak danych koni do wyświetlenia</h2>";
}

$conn->close();
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Handle delete button click
    $(document).on('click', '.delete-button', function() {
        if (confirm('Czy na pewno chcesz usunąć tego konia?')) {
            var horseId = $(this).data('id');
            $.ajax({
                url: 'delete_horse.php',
                type: 'POST',
                data: { id: horseId },
                success: function(response) {
                    if (response.trim() === 'Sukces') {
                        alert('Koń został usunięty.');
                        location.reload();
                    } else {
                        alert('Błąd: ' + response);
                    }
                },
                error: function() {
                    alert('Wystąpił błąd podczas usuwania konia.');
                }
            });
        }
    });

    // Handle edit button click
    $(document).on('click', '.edit-button', function() {
        var horseId = $(this).data('id');
        $.ajax({
            url: 'edit_horse.php',
            type: 'GET',
            data: { id: horseId },
            success: function(response) {
                $(".content").html(response); // Load the edit form into the content area
            },
            error: function() {
                alert('Wystąpił błąd podczas ładowania formularza edycji.');
            }
        });
    });
});
</script>

