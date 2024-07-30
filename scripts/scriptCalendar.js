jQuery(document).ready(function() {
	jQuery("#add-event").submit(function(event) {
			event.preventDefault();
			var values = {};
			$.each($('#add-event').serializeArray(), function(i, field) {
					values[field.name] = field.value;
			});

			// Combine date and time fields to create datetime format
			var data_od = values['data_od'] + ' ' + values['godzina_od'] + ':00';
			var data_do = values['data_do'] + ' ' + values['godzina_do'] + ':00';

			// Add combined datetime to the values object
			values['data_rezerwacji_od'] = data_od;
			values['data_rezerwacji_do'] = data_do;

			// Remove the individual date and time fields
			delete values['data_od'];
			delete values['godzina_od'];
			delete values['data_do'];
			delete values['godzina_do'];

			console.log(values);

			$.ajax({
					url: '../scripts/add_reservation.php',
					type: 'POST',
					data: values,
					success: function(response) {
							alert("Rezerwacja dodana!");
							location.reload();
					},
					error: function() {
							alert("Wystąpił błąd przy dodawaniu rezerwacji.");
					}
			});
	});

	// FullCalendar initialization
	jQuery('#calendar').fullCalendar({
			themeSystem: 'bootstrap4',
			businessHours: false,
			defaultView: 'month',
			editable: true,
			header: {
					left: 'title',
					center: 'month,agendaWeek,agendaDay',
					right: 'today prev,next'
			},
			events: events,
			eventRender: function(event, element) {
					element.find(".fc-title").prepend("<i class='fa fa-" + event.icon + "'></i>");
			},
			dayClick: function(date, jsEvent, view) {
					// Check if current view is month, switch to day view
					if (view.name === 'month') {
							$('#calendar').fullCalendar('changeView', 'agendaDay', date);
					} else {
							jQuery('#modal-view-event-add').modal();
					}
			},
			eventClick: function(event, jsEvent, view) {
					var modal = jQuery('#modal-view-event');
					jQuery('.event-title').html(event.title);
					jQuery('.event-body').html(event.description);
					modal.data('event-id', event.id).data('status', event.status).modal();

					// Show or hide the accept button based on reservation status
					if (event.status === 'anulowana') {
							jQuery('.accept-reservation').hide();
					} else {
							jQuery('.accept-reservation').show();
					}
			}
	});

	// Accept reservation
	jQuery('.accept-reservation').click(function() {
			var eventId = jQuery('#modal-view-event').data('event-id');
			$.ajax({
					url: '../scripts/accept_reservation.php',
					type: 'POST',
					data: { id: eventId },
					success: function(response) {
							alert("Rezerwacja zaakceptowana!");
							location.reload();
					},
					error: function() {
							alert("Wystąpił błąd przy akceptowaniu rezerwacji.");
					}
			});
	});

	// Cancel reservation
	jQuery('.cancel-reservation').click(function() {
			var eventId = jQuery('#modal-view-event').data('event-id');
			$.ajax({
					url: '../scripts/cancel_reservation.php',
					type: 'POST',
					data: { id: eventId },
					success: function(response) {
							alert("Rezerwacja anulowana!");
							location.reload();
					},
					error: function() {
							alert("Wystąpił błąd przy anulowaniu rezerwacji.");
					}
			});
	});

	// Load available horses and trainers based on selected date and time
	jQuery('#add-event').on('change', 'input[name="data_od"], input[name="godzina_od"], input[name="data_do"], input[name="godzina_do"]', function() {
			var dataOd = jQuery('input[name="data_od"]').val();
			var godzinaOd = jQuery('input[name="godzina_od"]').val();
			var dataDo = jQuery('input[name="data_do"]').val();
			var godzinaDo = jQuery('input[name="godzina_do"]').val();

			if (dataOd && godzinaOd && dataDo && godzinaDo) {
					var startTime = dataOd + ' ' + godzinaOd + ':00';
					var endTime = dataDo + ' ' + godzinaDo + ':00';

					// Fetch available horses
					$.ajax({
							url: '../scripts/get_available_horses.php',
							type: 'POST',
							data: { start_time: startTime, end_time: endTime },
							success: function(response) {
									jQuery('#kon_id').html(response);
							},
							error: function() {
									alert("Wystąpił błąd przy pobieraniu dostępnych koni.");
							}
					});

					// Fetch available trainers
					$.ajax({
							url: '../scripts/get_available_trainers.php',
							type: 'POST',
							data: { start_time: startTime, end_time: endTime },
							success: function(response) {
									jQuery('#trener_id').html(response);
							},
							error: function() {
									alert("Wystąpił błąd przy pobieraniu dostępnych trenerów.");
							}
					});
			}
	});
});
