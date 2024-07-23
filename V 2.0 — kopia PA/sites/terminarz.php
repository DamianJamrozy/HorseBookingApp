<?php
// Połączenie z bazą danych
include '../scripts/db.php';

// Pobieranie rezerwacji z bazy danych
$sql = "SELECT id, klient_id, kon_id, trener_id, data_rezerwacji_od, data_rezerwacji_do, 	reservation_status FROM reservations";
$result = $conn->query($sql);

$events = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $color = '';
        if ($row['reservation_status'] == 'aktywna') {
            $color = '#69db7c';
        } elseif ($row['reservation_status'] == 'anulowana') {
            $color = '#f03e3e';
        } if ($row['data_rezerwacji_od'] < date('Y-m-d H:i:s')) {
            $color = '#4dabf7';
        }

        $events[] = array(
            'id' => $row['id'],
            'title' => 'Rezerwacja #' . $row['id'],
            'start' => $row['data_rezerwacji_od'],
            'end' => $row['data_rezerwacji_do'],
            'color' => $color,
            'description' => 'Klient ID: ' . $row['klient_id'] . ', Koń ID: ' . $row['kon_id'] . ', Trener ID: ' . $row['trener_id'],
        );
    }
}

$conn->close();
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
</head>
<body>
    <div class="p-5">
        <h2 class="mb-4">Full Calendar</h2>
        <button type="button" class="btn btn-primary mb-4" data-toggle="modal" data-target="#modal-view-event-add">Nowa Rezerwacja</button>
        <div class="card">
            <div class="card-body p-0">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- View Event Modal -->
    <div id="modal-view-event" class="modal modal-top fade calendar-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <h4 class="modal-title"><span class="event-title"></span></h4>
                    <div class="event-body"></div>
                </div>
                <div class="modal-footer">
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
                        <div class="form-group">
                            <label>Klient ID</label>
                            <input type="number" class="form-control" name="klient_id">
                        </div>
                        <div class="form-group">
                            <label>Koń ID</label>
                            <input type="number" class="form-control" name="kon_id">
                        </div>
                        <div class="form-group">
                            <label>Trener ID</label>
                            <input type="number" class="form-control" name="trener_id">
                        </div>
                        <div class="form-group">
                            <label>Data Rezerwacji Od</label>
                            <input type="text" class="datetimepicker form-control" name="data_rezerwacji_od">
                        </div>
                        <div class="form-group">
                            <label>Data Rezerwacji Do</label>
                            <input type="text" class="datetimepicker form-control" name="data_rezerwacji_do">
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
    <script src="../scripts/scriptCalendar.js"></script>
    <script>
        var events = <?php echo json_encode($events); ?>;
    </script>
</body>
</html>
