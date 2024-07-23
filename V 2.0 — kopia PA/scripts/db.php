<!-- podałaczenie do db i spr czy jest conn  -->
<?php
$servername = "localhost";
$username = "root"; // przyjmujemy, że używasz użytkownika root
$password = ""; // domyślne hasło dla root, jeśli nie ustawiono inaczej
$dbname = "klub_jezdziecki";

// Tworzenie połączenia
$conn = new mysqli($servername, $username, $password, $dbname);

// Sprawdzanie połączenia
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
