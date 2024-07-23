<?php
session_start();
include '../php/db.php';

if ($_SESSION['user_role'] == 'administrator') {
    // Przykładowe dane dla kafelków
    $tiles = array(
        //array("img" => "../figures/home.png", "text" => "Pulpit", "content" => "?"),
        array("img" => "../figures/trener.png", "text" => "Trenerzy", "content" => "download_trainers_Admin.php"),
        array("img" => "../figures/horse.png", "text" => "Konie", "content" => "download_horse.php"),
        array("img" => "../figures/calender.png", "text" => "Terminarz", "content" => "terminarz.php"),
        array("img" => "../figures/userEdit.png", "text" => "Dane", "content" => "client_data.php"),
    );
}

else if ($_SESSION['user_role'] == 'trener') {
    // Przykładowe dane dla kafelków
    $tiles = array(
        //array("img" => "../figures/home.png", "text" => "Pulpit", "content" => "?"),
        array("img" => "../figures/trener.png", "text" => "Trenerzy", "content" => "download_trainers_Admin.php"),
        array("img" => "../figures/horse.png", "text" => "Konie", "content" => "download_horse.php"),
        array("img" => "../figures/calender.png", "text" => "Terminarz", "content" => "terminarz.php"),
        array("img" => "../figures/userEdit.png", "text" => "Dane", "content" => "client_data.php"),
    );
}
else if ($_SESSION['user_role'] == 'klient') {
    // Przykładowe dane dla kafelków
    $tiles = array(
        //array("img" => "../figures/home.png", "text" => "Pulpit", "content" => "?"),
        array("img" => "../figures/trener.png", "text" => "Trenerzy", "content" => "download_trainers_Admin.php"),
        array("img" => "../figures/horse.png", "text" => "Konie", "content" => "download_horse.php"),
        array("img" => "../figures/calender.png", "text" => "Terminarz", "content" => "terminarz.php"),
        array("img" => "../figures/userEdit.png", "text" => "Dane", "content" => "client_data.php"),
    );
}
$conn->close();

// Wyświetlenie kafelków
foreach ($tiles as $tile) {
    echo '<div class="tile" data-url="' . htmlspecialchars($tile['content']) . '">';
    echo '<img src="' . htmlspecialchars($tile['img']) . '" alt="' . htmlspecialchars($tile['text']) . '">';
    echo '<div>' . htmlspecialchars($tile['text']) . '</div>';
    echo '</div>';
}


?>

<script>
$(document).ready(function() {
    $(".tile").click(function() {
        var url = $(this).data("url");
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                $(".content").html(response);
            },
            error: function() {
                $(".content").html("Wystąpił błąd podczas ładowania zawartości.");
            }
        });
    });
});
</script>