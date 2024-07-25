<?php
include '../scripts/db.php';

if ($_SESSION['user_role'] == 'administrator' || $_SESSION['user_role'] == 'trener') {
    $tiles = array(
        array("img" => "../figures/trener.png", "text" => "Trenerzy", "content" => "download_trainers.php"),
        array("img" => "../figures/horse.png", "text" => "Konie", "content" => "download_horse.php"),
        array("img" => "../figures/calender.png", "text" => "Terminarz", "content" => "terminarz.php"),
        array("img" => "../figures/userEdit.png", "text" => "Dane", "content" => "client_data_All.php"),
    );
} else if ($_SESSION['user_role'] == 'klient') {
    $tiles = array(
        array("img" => "../figures/trener.png", "text" => "Trenerzy", "content" => "download_trainers.php"),
        array("img" => "../figures/horse.png", "text" => "Konie", "content" => "download_horse.php"),
        array("img" => "../figures/calender.png", "text" => "Terminarz", "content" => "terminarz.php"),
        array("img" => "../figures/userEdit.png", "text" => "Dane", "content" => "client_data.php"),
    );
}
$conn->close();


echo '<div class="tiles">';
foreach ($tiles as $tile) {
    echo '<a href="dashboard.php?page=' . htmlspecialchars($tile['content']) . '">';
    echo '<div class="tile">';
    echo '<img src="' . htmlspecialchars($tile['img']) . '" alt="' . htmlspecialchars($tile['text']) . '">';
    echo '<div>' . htmlspecialchars($tile['text']) . '</div>';
    echo '</div>';
    echo '</a>';
}
echo '</div>';
?>
