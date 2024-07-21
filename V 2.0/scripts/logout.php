<!-- plik który zarządza wylogowaniem poprzez klikniecie logout   -->
<?php
session_start();
session_unset();
session_destroy();
header("Location: ../sites/index.php");
exit();