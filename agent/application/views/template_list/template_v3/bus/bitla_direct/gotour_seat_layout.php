<?php
$seat_layout_stamp = time().rand(1, 100);
$seat_layout = '';
$CUR_Layout = $CUR_Layout;
 //debug($CUR_Layout);exit('7');
$sl_max_rows = $CUR_Layout[0]['MaxRows'];
$sl_max_cols = $CUR_Layout[0]['MaxCols'];
$seat_no_summary = array();
$template_images = $GLOBALS['CI']->template->template_images();
if (valid_array($CUR_Layout) == true and ($sl_max_rows > 0 || $sl_max_cols > 0)) {
	$sl_bus_deck = $GLOBALS['CI']->bus_lib->group_deck(force_multple_data_format($CUR_Layout));
	// debug($sl_bus_deck);exit;
	$sl_bus_seats = force_multple_data_format($CUR_Layout);
	
	$sl_matrix = $GLOBALS['CI']->bus_lib->group_matrix($sl_bus_seats);
	// debug($sl_matrix);exit;
	$row = 0;
	$col = 0;
	krsort($sl_bus_deck['deck_config']);
	$seat_summary_matrix = array();
	
	foreach ($sl_bus_deck['deck_config'] as $deck_k => $deck_v) {
		// debug($deck_k);exit;
		$seat_layout .= '<div class="upnddown"><table class="table table-condensed">';
		if ($deck_k == 'Lower') {
			$deck_name = '<img src="'.$template_images.'seats/steering.png" alt="Lower"><br><img src="'.$template_images.'seats/lower.jpg" alt="Lower">';
			$seat_layout .= '<tr><td rowspan="'.($deck_v['max_row']-$deck_v['min_row']).'">'.$deck_name.'</td></tr>';
		} else {
			$deck_name = '<img src="'.$template_images.'seats/upper.jpg" alt="Lower">';
			$seat_layout .= '<tr><td rowspan="'.($deck_v['max_row']-$deck_v['min_row']).'">'.$deck_name.'</td></tr>';
		}
		for ($col = $deck_v['max_col']; $col >= $deck_v['min_col']; $col--) {
			if (in_array($col, $sl_bus_deck['deck_cols'][$deck_k]) == false) {
				continue;
			}
			if($col == 1 || $col == 0){
				$class = 'spcetr';
			}
			else{
				$class ='';
			}
			//int guess $col-$row then ignore
			$seat_layout .= '<tr class="'.$class.'">';
			for($row = $deck_v['min_row']; $row <= $deck_v['max_row']; $row++) {
				$seat_number = $seat_icon = $seat_deck = $gender = '';
				$colspan = 1;
				$rowspan = 1;
				if($deck_k == 'Upper'){
					
					if (isset($sl_matrix['Upper'][$row][$col]) == true && empty($sl_matrix['Upper'][$row][$col]['seat_no']) == false && $sl_matrix['Upper'][$row][$col]['decks'] == $deck_k) {
					$seat_number = $sl_matrix['Upper'][$row][$col]['seat_no'];
					$seat_deck = $sl_matrix['Upper'][$row][$col]['decks'];
					$seat_no_summary[$seat_number] = $sl_matrix['Upper'][$row][$col];
					// debug($sl_matrix);exit;
					//update Fare
					$fare_details['default_value'] = $sl_matrix['Upper'][$row][$col]['Fare'];
					// $Markup = $fare_details['default_value'] - $sl_matrix['Lower'][$row][$col]['total_fare'];
					// //Adding GST
				 //    $gst_value = 0;
				 //   	if($Markup > 0 ){
				 //        $gst_details = $GLOBALS['CI']->custom_db->single_table_records('gst_master', '*', array('module' => 'bus'));
				 //        if($gst_details['status'] == true){
				 //            if($gst_details['data'][0]['gst'] > 0){
				 //                $gst_value = ($Markup/100) * $gst_details['data'][0]['gst'];
				 //            }
				 //        }
				 //    }
					$sl_matrix['Upper'][$row][$col]['Fare'] = $seat_no_summary[$seat_number]['Fare'] = $fare_details['default_value'];//+$gst_value;
					// debug($sl_matrix);exit;
					//Available
					$seat_type = '';
					
					if ($sl_matrix['Upper'][$row][$col]['IsAvailable'] == '1') {
						if($sl_matrix['Upper'][$row][$col]['status'] == 0){
							$gender = 'not';
							$sl_class = array('disable-select');
						}
						else if($sl_matrix['Upper'][$row][$col]['status'] == '2'){
							$gender = 'M';
							$sl_class = array('enable-select-'.$seat_layout_stamp);
						}
						else if($sl_matrix['Upper'][$row][$col]['status'] == '3'){
							$gender = 'F';
							$sl_class = array('enable-select-'.$seat_layout_stamp);
						}
						else if($sl_matrix['Upper'][$row][$col]['status'] == '1'){
							$gender = 'IG';
							$sl_class = array('enable-select-'.$seat_layout_stamp);
						}
						
						if($gender == 'not'){
							$sl_title = array('Not Available', 'Seat : '.$seat_number);
						}
						else{
							//$sl_matrix['Upper'][$row][$col]['Fare'] -= $sl_matrix['Upper'][$row][$col]['service_tax'];
							$sl_title = array('Seat : '.$seat_number, ' Fare : '.$sl_matrix['Upper'][$row][$col]['Fare'].' ,Service Tax : '.$sl_matrix['Upper'][$row][$col]['service_tax']);
						}
						
						if (intval($sl_matrix['Upper'][$row][$col]['height']) == 2 && intval($sl_matrix['Upper'][$row][$col]['width']) == 1) {
							//sleeper

							$seat_type = 'sleeper';
							$seat_icon = $seat_type.'-'.$gender.'.png';
							$availed_seat_icon = $seat_type.'-A.png';
							$colspan = 1;
						} else if (intval($sl_matrix['Upper'][$row][$col]['height']) == 1 && intval($sl_matrix['Upper'][$row][$col]['width']) == 2) {
							//vertical sleeper
							$seat_type = 'sleeper_v';
							$seat_icon = $seat_type.'-'.$gender.'.png';
							$availed_seat_icon = $seat_type.'-A.png';
							$rowspan = 1;
						} else {
							//seater
							$seat_type = 'seat';
							$seat_icon = $seat_type.'-'.$gender.'.png';
							$availed_seat_icon = $seat_type.'-A.png';
						}
					} else {
						if($sl_matrix['Upper'][$row][$col]['status'] == '-2'){
							$gender = 'B-M';
						}
						else if($sl_matrix['Upper'][$row][$col]['status'] == '-3'){
							$gender = 'B-F';
						}
						$sl_class = array('disable-select');
						
						$sl_title = array('Booked','Seat : '.$seat_number);
						
						if (intval($sl_matrix['Upper'][$row][$col]['height']) == 2 && intval($sl_matrix['Upper'][$row][$col]['width']) == 1) {
							//sleeper
							$seat_type = 'sleeper';
							$seat_icon = $seat_type.'-'.$gender.'.png';
							$availed_seat_icon = $seat_type.'-'.$gender.'.png';
							$colspan = 1;
						} else if (intval($sl_matrix['Upper'][$row][$col]['height']) == 1 && intval($sl_matrix['Upper'][$row][$col]['width']) == 2) {
							//vertical sleeper
							$seat_type = 'sleeper_v';
							$seat_icon = $seat_type.'-'.$gender.'.png';
							$availed_seat_icon = $seat_type.'-'.$gender.'.png';
							$rowspan = 1;
						} else {
							//seater
							$seat_type = 'seat';
							$seat_icon = $seat_type.$gender.'-'.'.png';
							$availed_seat_icon = $seat_type.'-'.$gender.'.png';
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
				}
				else if($deck_k == 'Lower'){
					// debug($sl_matrix);exit;
					if (isset($sl_matrix['Lower'][$row][$col]) == true && empty($sl_matrix['Lower'][$row][$col]['seat_no']) == false && $sl_matrix['Lower'][$row][$col]['decks'] == $deck_k) {
					$seat_number = $sl_matrix['Lower'][$row][$col]['seat_no'];
					$seat_deck = $sl_matrix['Lower'][$row][$col]['decks'];
					$seat_no_summary[$seat_number] = $sl_matrix['Lower'][$row][$col];
					//update Fare
					$fare_details['default_value'] = $sl_matrix['Lower'][$row][$col]['Fare'];
					

					$sl_matrix['Lower'][$row][$col]['Fare'] = $seat_no_summary[$seat_number]['Fare'];
					//Available
					$seat_type = '';
					if ($sl_matrix['Lower'][$row][$col]['IsAvailable'] == '1') {
						if($sl_matrix['Lower'][$row][$col]['status'] == '0'){
							$sl_class = array('disable-select');
							$gender = 'not';
						}
						else if($sl_matrix['Lower'][$row][$col]['status'] == '2'){
							$gender = 'M';
							$sl_class = array('enable-select-'.$seat_layout_stamp);
						}
						else if($sl_matrix['Lower'][$row][$col]['status'] == '3'){
							$gender = 'F';
							$sl_class = array('enable-select-'.$seat_layout_stamp);
						}
						else if($sl_matrix['Lower'][$row][$col]['status'] == '1'){
							$gender = 'IG';
							$sl_class = array('enable-select-'.$seat_layout_stamp);
						}
						
						if($gender == 'not'){
							$sl_title = array('Not Available', 'Seat : '.$seat_number);
						}
						else{
							//$sl_matrix['Lower'][$row][$col]['Fare'] -= $sl_matrix['Lower'][$row][$col]['service_tax'];
							$sl_title = array('Seat : '.$seat_number, ' Fare : '.$sl_matrix['Lower'][$row][$col]['Fare'].' ,Service Tax : '.$sl_matrix['Lower'][$row][$col]['service_tax']);

						}
						if (intval($sl_matrix['Lower'][$row][$col]['height']) == 2 && intval($sl_matrix['Lower'][$row][$col]['width']) == 1) {
							//sleeper

							$seat_type = 'sleeper';
							$seat_icon = $seat_type.'-'.$gender.'.png';
							$availed_seat_icon = $seat_type.'-A.png';
							$colspan = 1;
						} else if (intval($sl_matrix['Lower'][$row][$col]['height']) == 1 && intval($sl_matrix['Lower'][$row][$col]['width']) == 2) {
							//vertical sleeper
							$seat_type = 'sleeper_v';
							$seat_icon = $seat_type.'-'.$gender.'.png';
							$availed_seat_icon = $seat_type.'-A.png';
							$rowspan = 1;
						} else {
							//seater
							$seat_type = 'seat';
							$seat_icon = $seat_type.'-'.$gender.'.png';
							$availed_seat_icon = $seat_type.'-A.png';
						}
					} else {
						if($sl_matrix['Lower'][$row][$col]['status'] == '-2'){
							$gender = 'B-M';
						}
						else if($sl_matrix['Lower'][$row][$col]['status'] == '-3'){
							$gender = 'B-F';
						}
						$sl_class = array('disable-select');
						
						$sl_title = array('Booked','Seat : '.$seat_number);
						
						
						if (intval($sl_matrix['Lower'][$row][$col]['height']) == 2 && intval($sl_matrix['Lower'][$row][$col]['width']) == 1) {
							//sleeper
							$seat_type = 'sleeper';
							$seat_icon = $seat_type.'-'.$gender.'.png';
							$availed_seat_icon = $seat_type.'-'.$gender.'.png';
							$colspan = 1;
						} else if (intval($sl_matrix['Lower'][$row][$col]['height']) == 1 && intval($sl_matrix['Lower'][$row][$col]['width']) == 2) {
							//vertical sleeper
							$seat_type = 'sleeper_v';
							$seat_icon = $seat_type.'-'.$gender.'.png';
							$availed_seat_icon = $seat_type.'-'.$gender.'.png';
							$rowspan = 1;
						} else {
							//seater
							$seat_type = 'seat';
							$seat_icon = $seat_type.'-'.$gender.'.png';
							$availed_seat_icon = $seat_type.'-'.$gender.'.png';
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
				}
				$seat_layout .= '<td colspan="'.$colspan.'" rowspan="'.$rowspan.'"><span class="seat_num_val">'.$seat_number.' </span> <img data-seat-no="'.$seat_number.'" data-gender="'.$gender.'" data-availed-href="'.$template_images.'seats/'.$availed_seat_icon.'" data-active-href="'.$template_images.'seats/'.$seat_icon.'" data-row="'.$row.'" data-col="'.$col.'" class="'.implode(' ', $sl_class).'" src="'.$template_images.'seats/'.$seat_icon.'" alt="Seat Icon" title="'.implode(',', $sl_title).'"/></td>';
			}
			$seat_layout .= '</tr>';
		}
		$seat_layout .= '</table> </div>';
	}

	//$seat_summary_matrix -  loop matrix and get seats
	$seat_summary = array('IG' => 'Available Seat', 'F' => 'Reserved For Ladies','M' => 'Reserved For Gents', 'A' => 'Selected Seat', 'B-F' => 'Booked By Ladies', 'B-M' => 'Booked By Gents');
	$seat_summary_layout = '';
	if (valid_array($seat_summary_matrix) == true) {
		foreach ($seat_summary_matrix as $__seat_type) {
			foreach ($seat_summary as $__is_s => $__is_v) {
				$seat_summary_layout .= '<div class="col-md-3 col-sm-4 col-xs-6 nopad" style="margin-bottom:7px;">';
					$seat_summary_layout .= '<div class="col-md-3 col-xs-2 nopad" align="right" valign="top"><img src="'.$template_images.'seats/'.$__seat_type.'-'.$__is_s.'.png"></div>';
					$seat_summary_layout .= '<div class="col-md-9 col-xs-10 seat_set" align="left" valign="top" style="line-height:23px; font-size:13px;">'.$__is_v.'</div>';
				$seat_summary_layout .= '</div>';
			}
		}
	}
	echo '
	<div class="row">
		<div class="col-lg-12 nopad"><div class="layoutonly">'.$seat_layout.'</div>

         <div class="layoutonly">
         <table style="width:80%;">
         <tr>
         <th style="width:20%;" align="left" valign="top">Seats</th>
         <th style="width:20%;" align="left" valign="top">Amount</th>
         <th style="width:20%;" align="left" valign="top">Service Tax</th>
         <th style="width:20%;" align="left" valign="top">GST</th>
         <th style="width:20%;" align="left" valign="top">Total</th>
         </tr>
         </table>
         <table style="width:80%;" id="seat-details-table">
         </table>
         </div>
         

         <div class="col-md-12 nopad layout_with">
		<div class="priceanlo">
			<table>
				<tbody>
				<tr>
				<td>
					'.$seat_summary_layout.'	
				</td> 
				</tr>		
				</tbody>
			</table>
		</div>
		</div>

		</div>	
	</div>';
}
?>
<script>
$(document).ready(function() {
	var selected_seat = {};
	var seat_no_summary = <?=json_encode($seat_no_summary);?>;
	var gst_value = <?= $gst_value ?>;
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
		var seat_tax_cost_summary = 0;
		var seat_cost_wtg_summary = 0;
		var total_gst = 0;
		if ($.isEmptyObject(selected_seat) == false) {
			let row = '';
			$.each(selected_seat, function(k, v) {
				seat_summary = k;
				seat_cost_summary = parseFloat(seat_no_summary[k]['Fare'])+parseFloat(seat_no_summary[k]['service_tax']);
				seat_tax_cost_summary = parseFloat(seat_no_summary[k]['service_tax']);
				total_gst = parseFloat(gst_value);
				seat_cost_wtg_summary = parseFloat(seat_no_summary[k]['Fare']) - parseFloat(gst_value);
				row += '<tr><td style="width:20%;" align="left" valign="top" class="seat_set setag"><span class="selected-seat colordstybg" id="selected-seat-'+k+'">'+seat_summary+'</span></td><td style="width:20%;" align="left" valign="top" class="seat_set setag"><span class="selected-seat-price colordstybg" id="selected-seat-price-wtg-'+k+'">'+seat_cost_wtg_summary.toFixed(2)+'</span></td><td style="width:20%;" align="left" valign="top" class="seat_set setag"><span class="selected-seat-price colordstybg" id="selected-seat-tax-price-'+k+'">'+seat_tax_cost_summary.toFixed(2)+'</span></td><td style="width:20%;" align="left" valign="top" class="seat_set setag"><span class="selected-seat-price colordstybg" id="selected-seat-gst-'+k+'">'+total_gst.toFixed(2)+'</span></td><td style="width:20%;" align="left" valign="top" class="seat_set setag"><span class="selected-seat-price colordstybg" id="selected-seat-price-'+k+'">'+seat_cost_summary.toFixed(2)+'</span></td></tr>';
				$('#seat-details-table').html(row);
			});
		} else {
			seat_summary = '-';
		}
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
	$('.drop-point').on('change', function() {
		
		if (this.value != 'INVALIDIP') {
			$('.drop-id', $(this).closest('.details-wrapper')).val(this.value);
		} else {
			$('.drop-id', $(this).closest('.details-wrapper')).val('');
			alert('Please Select Drop Point.');
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
		if ($('.drop-id', _form).val() == 'INVALIDIP' || $('.drop-id', _form).val() == "") {
			return {'status' : false, 'msg' : 'Please Select Dropping Point To Continue.'};
		}
		return {'status' : true};
	}
});
</script>