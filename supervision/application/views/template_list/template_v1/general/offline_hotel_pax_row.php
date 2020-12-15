<div class="clearfix form-group">
	<div class="col-md-1">
		<input type="hidden" name="pax_type[]" value="<?=ucfirst($pax_type); ?>">
		<select class="form-control" name="pax_title[]">
			<?=generate_options(get_enum_list('title'))?>														
		</select>
	</div>
	<div class="col-md-3">
		<input type="text" class="form-control" name="pax_first_name[]" placeholder="First Name" required>
	</div>
	<div class="col-md-3">
		<input type="text" class="form-control" name="pax_last_name[]" placeholder="Last Name" required>
	</div>
	<div class="col-md-3">
		<input type="text" class="form-control" name="pax_age[]" placeholder="Age" required>
	</div>
	<!-- <div class="col-md-2">
		<input type="text" class="form-control" name="pax_ff_num[]" placeholder="Freq. Flyer No.">
	</div>-->
	<!-- <div class="col-md-1">
		<input type="text" class="form-control" name="age[]" placeholder="Age">
	</div>
	<div class="col-md-2">
		<input type="text" class="form-control" name="seat_no[]" placeholder="Seat No.">
	</div>
	<div class="col-md-2">
		<input type="text" class="form-control" name="pax_ticket_num_onward[]" placeholder="Ticket No." <?= @$c_type == 'gds' ?'required':'' ?>>
		<input type="text" class="form-control" name="pax_ticket_num_return[]" placeholder="Return" <?= @$trip_type != 'circle' ?'style="display:none" disabled':'' ?> <?= @$c_type == 'gds' ?'required':'' ?>>
	</div> -->
</div>