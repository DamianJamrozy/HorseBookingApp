jQuery(document).ready(function() {
	jQuery('.datetimepicker').datepicker({
			timepicker: true,
			language: 'en',
			range: false,
			multipleDates: false,
			dateFormat: 'yy-mm-dd',
			timeFormat: 'HH:MM:SS'
	});

	jQuery("#add-event").submit(function(event) {
			event.preventDefault();
			var values = {};
			$.each($('#add-event').serializeArray(), function(i, field) {
					values[field.name] = field.value;
			});

			// Convert dates to MySQL format
			values['data_rezerwacji_od'] = convertToMySQLFormat(values['data_rezerwacji_od']);
			values['data_rezerwacji_do'] = convertToMySQLFormat(values['data_rezerwacji_do']);

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

	function convertToMySQLFormat(dateString) {
			var date = new Date(dateString);
			var year = date.getFullYear();
			var month = ('0' + (date.getMonth() + 1)).slice(-2);
			var day = ('0' + date.getDate()).slice(-2);
			var hours = ('0' + date.getHours()).slice(-2);
			var minutes = ('0' + date.getMinutes()).slice(-2);
			var seconds = ('0' + date.getSeconds()).slice(-2);
			return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
	}

	(function() {
			'use strict';
			jQuery(function() {
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
							dayClick: function() {
									jQuery('#modal-view-event-add').modal();
							},
							eventClick: function(event, jsEvent, view) {
									jQuery('.event-title').html(event.title);
									jQuery('.event-body').html(event.description);
									jQuery('#modal-view-event').data('event-id', event.id).modal();
							}
					});
			});
	})(jQuery);

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
});
