<?php
include '../scripts/db.php';

// Pobieranie rezerwacji z bazy danych w zależności od roli użytkownika
$user_role = $_SESSION['user_role'];
$user_id = $_SESSION['user_id'];

$sql = "SELECT r.id, r.klient_id, r.kon_id, r.trener_id, r.data_rezerwacji_od, r.data_rezerwacji_do, r.reservation_status, 
        k.imie AS kon_imie, c.imie AS klient_imie, c.nazwisko AS klient_nazwisko, t.imie AS trener_imie, t.nazwisko AS trener_nazwisko
        FROM reservations r
        JOIN horses k ON r.kon_id = k.id
        JOIN users c ON r.klient_id = c.id
        JOIN users t ON r.trener_id = t.id";

if ($user_role == 'trener') {
    $sql .= " WHERE r.trener_id = $user_id";
} elseif ($user_role == 'klient') {
    $sql .= " WHERE r.klient_id = $user_id";
}

$result = $conn->query($sql);

$events = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $color = '';
        if ($row['reservation_status'] == 'aktywna') {
            $color = '#69db7c';
        } elseif ($row['reservation_status'] == 'anulowana') {
            $color = '#f03e3e';
        }
        if ($row['data_rezerwacji_od'] < date('Y-m-d H:i:s') && $row['reservation_status'] == 'aktywna') {
            $color = '#4dabf7';
        } elseif ($row['data_rezerwacji_od'] < date('Y-m-d H:i:s') && $row['reservation_status'] == 'anulowana') {
            $color = '#c7c7c7';
        }

        $events[] = array(
            'id' => $row['id'],
            'title' => 'Rezerwacja #' . $row['id'],
            'start' => $row['data_rezerwacji_od'],
            'end' => $row['data_rezerwacji_do'],
            'color' => $color,
            'description' => '<div class="modal-text-own"><p>Klient: ' . $row['klient_imie'] . ' ' . $row['klient_nazwisko'] . '</p><p> Koń: ' .
                $row['kon_imie'] . '</p><p> Trener: ' . $row['trener_imie'] . ' ' . $row['trener_nazwisko'] . '</p><p> Od: ' .
                $row['data_rezerwacji_od'] . '</p><p> Do: ' . $row['data_rezerwacji_do'] . '</p><p> Status: ' . $row['reservation_status'] . '</p></div>',
        );
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Terminarz</title>
    <!-- Bootstrap, jQuery, FullCalendar CSS and JS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../styles/stylesCalendar.css">
    <link rel="stylesheet" type="text/css" href="../styles/calender.css">
    <link rel="stylesheet" type="text/css" href="../styles/general.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
</head>

<body>
    <div class="p-5">
        <h2 class="mb-4">Full Calendar</h2>
        <?php if ($user_role != 'trener') { ?>
            <button type="button" class="btn btn-primary mb-4" data-toggle="modal" data-target="#modal-view-event-add">Nowa
                Rezerwacja</button>
        <?php } ?>
        <div class="card">
            <div class="card-body p-0">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- View Event Modal -->
    <div id="modal-view-event" class="modal modal-top fade calendar-modal" data-status="">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <h4 class="modal-title"><span class="event-title"></span></h4>
                    <div class="event-body"></div>
                </div>
                <div class="modal-footer">
                    <?php if ($user_role == 'trener') { ?>
                        <button type="button" class="btn btn-success accept-reservation" style="display: none;">Akceptuj
                            Rezerwację</button>
                    <?php } ?>
                    <button type="button" class="btn btn-danger cancel-reservation">Anuluj Rezerwację</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Zamknij</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Event Modal -->
    <div id="modal-view-event-add" class="modal modal-top fade calendar-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="add-event">
                    <div class="modal-body">
                        <h4>Dodaj Szczegóły Rezerwacji</h4>
                        <?php if ($user_role == 'administrator') { ?>
                            <div class="form-group">
                                <label>Klient</label>
                                <select class="form-control" name="klient_id" id="klient_id">
                                    <?php
                                    $clients = $conn->query("SELECT id, imie, nazwisko FROM users WHERE rola = 'klient'");
                                    while ($client = $clients->fetch_assoc()) {
                                        echo "<option value='" . $client['id'] . "'>" . $client['imie'] . " " . $client['nazwisko'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        <?php } ?>
                        <div class="form-group">
                            <label>Data Od</label>
                            <input type="date" class="form-control" name="data_od">
                        </div>
                        <div class="form-group">
                            <label>Godzina Od</label>
                            <input type="time" class="form-control" name="godzina_od">
                        </div>
                        <div class="form-group">
                            <label>Data Do</label>
                            <input type="date" class="form-control" name="data_do">
                        </div>
                        <div class="form-group">
                            <label>Godzina Do</label>
                            <input type="time" class="form-control" name="godzina_do">
                        </div>
                        <div class="form-group">
                            <label>Koń</label>
                            <select class="form-control" name="kon_id" id="kon_id">
                                <!-- Konie będą załadowane przez JS -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Trener</label>
                            <select class="form-control" name="trener_id" id="trener_id">
                                <!-- Trenerzy będą załadowani przez JS -->
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Zapisz</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
    <script src="../scripts/scriptCalendar.js"></script>
    <script>
        var events = <?php echo json_encode($events); ?>;
    </script>
</body>

</html>