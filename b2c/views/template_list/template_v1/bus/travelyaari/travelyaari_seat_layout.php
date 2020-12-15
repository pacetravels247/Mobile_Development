<?php
$seat_layout_stamp = time().rand(1, 100);
$seat_layout = '';
$CUR_Layout = $CUR_Layout;
$sl_max_rows = $CUR_Layout['MaxRows'];
$sl_max_cols = $CUR_Layout['MaxColumns'];
$seat_no_summary = array();
$template_images = $GLOBALS['CI']->template->template_images();
if (valid_array($CUR_Layout) == true and ($sl_max_rows > 0 || $sl_max_cols > 0)) {
	$sl_bus_deck = $GLOBALS['CI']->bus_lib->group_deck(force_multple_data_format($CUR_Layout['SeatDetails']['clsSeat']));

	$sl_bus_seats = force_multple_data_format($CUR_Layout['SeatDetails']['clsSeat']);
	$sl_matrix = $GLOBALS['CI']->bus_lib->group_matrix($sl_bus_seats);
	$row = 0;
	$col = 0;
	krsort($sl_bus_deck['deck_config']);
	$seat_summary_matrix = array();
	foreach ($sl_bus_deck['deck_config'] as $deck_k => $deck_v) {
		$seat_layout .= '<table class="table table-condensed">';
		if ($deck_k == 1) {
			$deck_name = '<img src="'.$template_images.'seats/steering.jpg" alt="Lower"><br><img src="'.$template_images.'seats/lower.jpg" alt="Lower">';
			$seat_layout .= '<tr><td rowspan="'.($deck_v['max_row']-$deck_v['min_row']).'">'.$deck_name.'</td></tr>';
		} else {
			$deck_name = '<img src="'.$template_images.'seats/upper.jpg" alt="Lower">';
			$seat_layout .= '<tr><td rowspan="'.($deck_v['max_row']-$deck_v['min_row']).'">'.$deck_name.'</td></tr>';
		}
		for ($col = $deck_v['max_col']; $col >= $deck_v['min_col']; $col--) {
			if (in_array($col, $sl_bus_deck['deck_cols'][$deck_k]) == false) {
				continue;
			}
			//int guess $col-$row then ignore
			$seat_layout .= '<tr>';
			for($row = $deck_v['min_row']; $row <= $deck_v['max_row']; $row++) {
				$seat_number = $seat_icon = $seat_deck = $gender = '';
				$colspan = 1;
				$rowspan = 1;
				if (isset($sl_matrix[$row][$col]) == true && empty($sl_matrix[$row][$col]['SeatNo']) == false && $sl_matrix[$row][$col]['Deck'] == $deck_k) {
					$seat_number = $sl_matrix[$row][$col]['SeatNo'];
					$seat_deck = $sl_matrix[$row][$col]['Deck'];
					$seat_no_summary[$seat_number] = $sl_matrix[$row][$col];
					//update Fare
					$fare_details = $currency_obj->get_currency($sl_matrix[$row][$col]['Fare']);
					$sl_matrix[$row][$col]['Fare'] = $seat_no_summary[$seat_number]['Fare'] = $fare_details['default_value'];
					//Available
					$seat_type = '';
					if ($sl_matrix[$row][$col]['IsAvailable'] == 'true') {
						$gender = ($sl_matrix[$row][$col]['Gender'] != 'F' ? 'IG' : 'F');
						$sl_class = array('enable-select-'.$seat_layout_stamp);
						$sl_title = array('Seat : '.$seat_number, ' Fare : '.$sl_matrix[$row][$col]['Fare']);
						if (intval($sl_matrix[$row][$col]['Height']) == 2 && intval($sl_matrix[$row][$col]['Width']) == 1) {
							//sleeper
							$seat_type = 'sleeper';
							$seat_icon = $seat_type.'-'.$gender.'.jpg';
							$availed_seat_icon = $seat_type.'-A.jpg';
							$colspan = 1;
						} else if (intval($sl_matrix[$row][$col]['Height']) == 1 && intval($sl_matrix[$row][$col]['Width']) == 2) {
							//vertical sleeper
							$seat_type = 'sleeper_v';
							$seat_icon = $seat_type.'-'.$gender.'.jpg';
							$availed_seat_icon = $seat_type.'-A.jpg';
							$rowspan = 1;
						} else {
							//seater
							$seat_type = 'seat';
							$seat_icon = $seat_type.'-'.$gender.'.jpg';
							$availed_seat_icon = $seat_type.'-A.jpg';
						}
					} else {
						$gender = (($sl_matrix[$row][$col]['Gender'] != 'F') ? 'M' : 'F');
						$sl_class = array('disable-select');
						$sl_title = array('Booked-'.$gender, 'Seat : '.$seat_number);
						if (intval($sl_matrix[$row][$col]['Height']) == 2 && intval($sl_matrix[$row][$col]['Width']) == 1) {
							//sleeper
							$seat_type = 'sleeper';
							$seat_icon = $seat_type.'-B-'.$gender.'.jpg';
							$availed_seat_icon = $seat_type.'-B-'.$gender.'.jpg';
							$colspan = 1;
						} else if (intval($sl_matrix[$row][$col]['Height']) == 1 && intval($sl_matrix[$row][$col]['Width']) == 2) {
							//vertical sleeper
							$seat_type = 'sleeper_v';
							$seat_icon = $seat_type.'-B-'.$gender.'.jpg';
							$availed_seat_icon = $seat_type.'-B-'.$gender.'.jpg';
							$rowspan = 1;
						} else {
							//seater
							$seat_type = 'seat';
							$seat_icon = $seat_type.'-B-'.$gender.'.jpg';
							$availed_seat_icon = $seat_type.'-B-'.$gender.'.jpg';
						}
					}
					if (in_array($seat_type, $seat_summary_matrix) == false) {
						$seat_summary_matrix[] = $seat_type;
					}
				} else {
					$availed_seat_icon = $seat_icon = 'plain-icon.jpg';
					$seat_number = '';
					$sl_title = $sl_class = array();
				}
				$seat_layout .= '<td colspan="'.$colspan.'" rowspan="'.$rowspan.'"><img data-seat-no="'.$seat_number.'" data-gender="'.$gender.'" data-availed-href="'.$template_images.'seats/'.$availed_seat_icon.'" data-active-href="'.$template_images.'seats/'.$seat_icon.'" data-row="'.$row.'" data-col="'.$col.'" class="'.implode(' ', $sl_class).'" src="'.$template_images.'seats/'.$seat_icon.'" alt="Seat Icon" title="'.implode(',', $sl_title).'"/></td>';
			}
			$seat_layout .= '</tr>';
		}
		$seat_layout .= '</table>';
	}
	//$seat_summary_matrix -  loop matrix and get seats
	$seat_summary = array('IG' => 'Available Seat', 'F' => 'Reserved For Ladies', 'A' => 'Selected Seat', 'B-F' => 'Booked By Ladies', 'B-M' => 'Booked By Gents');
	$seat_summary_layout = '';
	if (valid_array($seat_summary_matrix) == true) {
		foreach ($seat_summary_matrix as $__seat_type) {
			foreach ($seat_summary as $__is_s => $__is_v) {
				$seat_summary_layout .= '<tr>';
					$seat_summary_layout .= '<td align="right" valign="top"><img src="'.$template_images.'seats/'.$__seat_type.'-'.$__is_s.'.jpg"></td>';
					$seat_summary_layout .= '<td align="left" valign="top" class="seat_set">'.$__is_v.'</td>';
				$seat_summary_layout .= '</tr>';
			}
		}
	}
	echo '
	<div class="row">
		<div class="col-md-8">'.$seat_layout.'</div>
		<div class="col-md-4">
			<table>
				<tbody>
					'.$seat_summary_layout.'
					<tr>
						<td colspan="2">
							<br>
							<hr>
							<br>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" class="seat_set">
							<strong>Seats   :</strong>
						</td>
						<td class="selected-seat" id="selected-seat-'.$seat_layout_stamp.'"></td>
					</tr>
					<tr>
						<td align="left" valign="top" class="seat_set"><strong>Amount :</strong></td>
						<td class="selected-seat-price" id="selected-seat-price-'.$seat_layout_stamp.'"></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>';
}
?>
<script>
$(document).ready(function() {
	var selected_seat = {};
	var seat_no_summary = <?=json_encode($seat_no_summary);?>;
	$('.enable-select-<?=$seat_layout_stamp?>').on('click', function() {
			if ($(this).hasClass('active') == true) {
				//remove
				pop_seat($(this).data('seat-no'));
				$(this).attr('src', $(this).data('active-href'));
				$(this).removeClass('active');
			} else {
				if (parseInt(Object.keys(selected_seat).length) < <?=MAX_BUS_SEAT_BOOKING?>) {
					//add
					push_seat($(this).data('seat-no'));
					$(this).attr('src', $(this).data('availed-href'));
					$(this).addClass('active');
				} else {
					alert('Maximum Of '+<?=MAX_BUS_SEAT_BOOKING?>+ ' Seats Can Be Selected');
				}
			}
			update_seat_summary_layout();
	});

	//Display Seat Summary From Counter
	function update_seat_summary_layout()
	{
		var seat_summary = '';
		var seat_cost_summary = 0;
		if ($.isEmptyObject(selected_seat) == false) {
			$.each(selected_seat, function(k, v) {
				seat_summary += k+',';
				seat_cost_summary += parseFloat(seat_no_summary[k]['Fare']);
			});
		} else {
			seat_summary = '-';
		}
		$('#selected-seat-'+<?=$seat_layout_stamp?>).empty().text(seat_summary.substr(0, (seat_summary.length-1)));
		$('#selected-seat-price-'+<?=$seat_layout_stamp?>).empty().text(seat_cost_summary.toFixed(2));
	}

	//Add Seat To Counter
	function push_seat(seat_no)
	{
		if ((seat_no in selected_seat) != -1) {
			selected_seat[seat_no] = true;
		}
	}

	//Remove Seat From Counter
	function pop_seat(seat_no)
	{
		if (seat_no != "" && (seat_no in selected_seat) != -1) {
			delete selected_seat[seat_no];
		}
	}

	$('.boarding-point').on('change', function() {
		if (this.value != 'INVALIDIP') {
			$('.pickup-id', $(this).closest('.details-wrapper')).val(this.value);
		} else {
			$('.pickup-id', $(this).closest('.details-wrapper')).val('');
			alert('Please Select Pickup Point.');
		}
	});

	$('.b-btn').on('click', function(e) {
		e.preventDefault();
		//form
		var _form = $(this).closest('form');
		//update seats
		update_seat_summary(_form);
		//validate form
		var _validation = validate_form(_form);
		if (_validation.status != false) {
			//submit form
			$(_form).submit();
		} else {
			//invalid form
			alert(_validation.msg);
		}
	});

	function update_seat_summary(_form)
	{
		var seat_attr = '';
		if ($.isEmptyObject(selected_seat) == false) {
			$.each(selected_seat, function(seat_no, seat_val) {
				//
				seat_attr += '<input type="hidden" name="seat[]" value="'+seat_no+'">';
			});
		}
		$('.seat-summary', _form).empty().html(seat_attr);
	}

	/**
	*Validate BOoking FOrm
	*/
	function validate_form(_form)
	{
		if (parseInt(Object.keys(selected_seat).length) < 1) {
			return {'status' : false, 'msg' : 'Please Select A Seat To Continue.'};
		}
		if ($('.pickup-id', _form).val() == 'INVALIDIP' || $('.pickup-id', _form).val() == "") {
			return {'status' : false, 'msg' : 'Please Select Boarding Point To Continue.'};
		}
		return {'status' : true};
	}
});
</script>