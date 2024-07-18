<!-- plik który odpoiwiada za widok po kliknieciu na pulpit   -->
<!-- w tym pliku trezba dodć funkcjonalność że po kliknięcu na dane kafelki przechodzi do odpowiedniego menu lub całkowicie to usuwamy -->


<?php
// Przykładowe dane dla kafelków
$tiles = array(
    array("img" => "../figures/home.png", "text" => "Pulpit", "content" => "client_panel.php"),
    array("img" => "../figures/trener.png", "text" => "Trenerzy", "content" => "download_trainers.php"),
    array("img" => "../figures/horse.png", "text" => "Konie", "content" => "download_horse.php"),
    array("img" => "../figures/calender.png", "text" => "Terminarz", "content" => "terminarz.php"),
    array("img" => "../figures/userEdit.png", "text" => "Dane", "content" => "client_data.php"),
);

// Wyświetlenie kafelków
foreach ($tiles as $tile) {
    echo '<div class="tile" data-url="' . htmlspecialchars($tile['content']) . '">';
    echo '<img src="' . htmlspecialchars($tile['img']) . '" alt="' . htmlspecialchars($tile['text']) . '">';
    echo '<div>' . htmlspecialchars($tile['text']) . '</div>';
    echo '</div>';
}
