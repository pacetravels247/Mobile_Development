<?php
/**
 * DONT DARE TO EDIT THIS FILE
 */
?>
<script>
function add_days_to_date(from_date, to_date, no_of_nights)
{
	//set aot_to_date
	var _from_date_val = $(from_date).val();
	var _nights_count_val = parseInt($(no_of_nights).val());
	if (_from_date_val != '' && _nights_count_val != '' && _nights_count_val > 0) {
		_from_date_val = new Date(_from_date_val);
		_to_date_val = new Date(_from_date_val.getFullYear(), _from_date_val.getMonth(), (_from_date_val.getDate()+_nights_count_val));
		$(to_date).val(_to_date_val.getFullYear() + '-' + (_to_date_val.getMonth()+1) + '-' + _to_date_val.getDate());
		
	}
}

function add_days_to_date_time(from_date, to_date, no_of_nights)
{
	var _from_date_val = $(from_date).val();
	var _nights_count_val = parseInt($(no_of_nights).val());
	if (_from_date_val != '' && _nights_count_val != '' && _nights_count_val > 0) {
		var temp_to_date_val = $(to_date).val();
		_from_date_val = _from_date_val.replace(/ /g, "T");
		_from_date_val = new Date(_from_date_val);
		if(temp_to_date_val != '') {
			//If to_date has time, assign the time details
			temp_to_date_val = temp_to_date_val.replace(/ /g, "T");
			var temp_to_date_val = new Date(temp_to_date_val);
			if(temp_to_date_val.getDate()) {
				var _hours = temp_to_date_val.getHours();
				var _minutes = temp_to_date_val.getMinutes();
				var _seconds = temp_to_date_val.getSeconds();
			}
		} else {
			var _hours = _from_date_val.getHours();
			var _minutes = _from_date_val.getMinutes();
			var _seconds = _from_date_val.getSeconds();
		}
		_to_date_val = new Date(_from_date_val.getFullYear(), _from_date_val.getMonth(), (_from_date_val.getDate()+_nights_count_val), _hours, _minutes, _seconds);
		//Prefixing Zero, if value is less than 10
		if((_to_date_val.getMonth()) < 10) {
			var _to_month = '0'+(_to_date_val.getMonth()+1); 
		} else {
			var _to_month = _to_date_val.getMonth()+1;
		}
		if((_to_date_val.getDate()) < 10) {
			var _to_date = '0'+_to_date_val.getDate(); 
		} else {
			var _to_date = _to_date_val.getDate();
		}
		if((_to_date_val.getHours()) < 10) {
			var _to_hours = '0'+_to_date_val.getHours(); 
		} else {
			var _to_hours = _to_date_val.getHours();
		}
		if((_to_date_val.getMinutes()) < 10) {
			var _to_minutes = '0'+_to_date_val.getMinutes(); 
		} else {
			var _to_minutes = _to_date_val.getMinutes();
		}
		if((_to_date_val.getSeconds()) < 10) {
			var _to_seconds = '0'+_to_date_val.getSeconds(); 
		} else {
			var _to_seconds = _to_date_val.getSeconds();
		}
		$(to_date).val(_to_date_val.getFullYear() + '-' + (_to_month) + '-' + _to_date + ' '+ _to_hours + ':'+ _to_minutes +':'+ _to_seconds);
	}
}

function zeroPad(num,count) {
	var numZeropad=num+'';
	while(numZeropad.length<count) {
		numZeropad="0"+numZeropad;
	}
	return numZeropad;
}
function dateADD(currentDate) {
	var valueofcurrentDate=currentDate.valueOf()+(24*60*60*1000);
	var newDate=new Date(valueofcurrentDate);
	return newDate;
}
function daysInMonth(month,year) {
	return new Date(year,month,0).getDate();
}

function get_day_difference(date1, date2) {
	if(date1!='' && date2!='') {
		var date1 = new Date(date1);
		var date2 = new Date(date2);
		var time_diff = date2.getTime() - date1.getTime();
		var days = time_diff / (1000 * 3600 * 24);
		return days;
	}
}

/**
*bind datepicker to each date input
*/
function bindDatepicker(inputId) {
	 $('#'+inputId).datepicker({
		numberOfMonths:2,
		dateFormat:'dd-mm-yy',
		showWeek: true,
		changeMonth:true,
		changeYear:true            
	});
}
/**
*bind month picker to each date input
*/
function bindMonthPicker(inputId) {
	 $('#'+inputId).datepicker({
		numberOfMonths:3,
		dateFormat:'mm-yy',
		showWeek: true,
		changeMonth:true,
		changeYear:true
	});
}

/**
*past datepicker
*/
function pastDatepicker(inputId) {
	 $('#'+inputId).datepicker({
		numberOfMonths:2,
		dateFormat:'dd-mm-yy',
		showWeek: true,
		changeMonth:true,
		changeYear:true,
        maxDate:0,
        yearRange: "-100:-0"
	});
}
/**
*Future datepicker
*/
function futureDatepicker(inputId) {
	 $('#'+inputId).datepicker({
		numberOfMonths:2,
		dateFormat:'dd-mm-yy',
		showWeek: true,
		changeMonth:true,
		changeYear:true,
        minDate:0
	});
}
/**
*Future DATE AND TIME picker
*/
function futureDateTimepicker(inputId) {
	 $('#'+inputId).datetimepicker({
		step:30,
		format:'d-m-Y H:i',
		minDate:0,
		//closeOnWithoutClick:false,
		defaultTime:'09:00:am',
		formatTime:'h:i:a'			
	});	 
}
/**
*Month datepicker
*/
function monthDatepicker(inputId) {
	 $('#'+inputId).datepicker({
		numberOfMonths:2,
		dateFormat:'mm-yy',
		showWeek: true,
		changeMonth:true,
		changeYear:true,
        maxDate:0,
        yearRange: "-100:-0"
	});
}
</script>
<!-- DATE AND TIME PICKER TO WINDOW END -->

<script type="text/javascript">
$(document).ready(function() {
	<?php
	/**
	 * DONT DARE TO EDIT THIS FILE
	 */
	foreach (self::$datepicker as $d_k => $d_v) {
		if(is_array($d_v)) {
			switch ($d_v[1]) {
				case PAST_DATE: //past Date ?>
					pastDatepicker("<?php echo $d_v[0]?>");
			  <?php break;
				case FUTURE_DATE://Future Date ?>
					futureDatepicker("<?php echo $d_v[0]?>");
				<?php break;
				case FUTURE_DATE_TIME://Future Date and Time?>
					futureDateTimepicker("<?php echo $d_v[0]?>");
				<?php break;
				case ENABLE_MONTH://Show only Month?>
					monthDatepicker("<?php echo $d_v[0]?>");
				<?php break;
				case ADULT_DATE_PICKER : ?>
				adultDatePicker("<?php echo $d_v[0]?>");
				<?php
				break;
				case CHILD_DATE_PICKER : ?>
				childDatePicker("<?php echo $d_v[0]?>");
				<?php
				break;
				case INFANT_DATE_PICKER : ?>
				infantDatePicker("<?php echo $d_v[0]?>");
				<?php
				break;
				//adult, child, infant
			}
		} else { ?>
			bindDatepicker("<?php echo $d_v;?>");
	<?php }
	} ?>

	function adultDatePicker(elementId)
	{
		$('#'+elementId).datepicker({
			yearRange:"-100:-12",
			changeYear:true,
			changeMonth:true,
			numberOfMonths:2,
			dateFormat:'yy-mm-dd',
			minDate: '-100Y',
		    maxDate: '-12Y',
		});
	}

	function childDatePicker(elementId)
	{
		$('#'+elementId).datepicker({
			yearRange:"-12:-2",
			changeYear:true,
			changeMonth:true,
			numberOfMonths:2,
			dateFormat:'yy-mm-dd',
			minDate: '-12Y',
		    maxDate: '-2Y',
		});
	}

	function infantDatePicker(elementId)
	{
		$('#'+elementId).datepicker({
			yearRange:"-2:+0",
			changeYear:true,
			changeMonth:true,
			numberOfMonths:2,
			dateFormat:'yy-mm-dd',
			minDate: '-2Y',
		    maxDate: '0',
		});
	}

	<?php
	/**
	 * Auto setter of datepicker
	 */
	
	if (valid_array(self::$auto_adjust_datepicker) == true) {
		foreach (self::$auto_adjust_datepicker as $a_k => $a_v) {
			if (valid_array($a_v) == true) {?>
				//date validation
				$("#<?php echo $a_v[0]; ?>").change(function() {
					//manage date validation
					$("#<?php echo $a_v[1]; ?>").trigger("click");
					var selectedDate=$(this).datepicker('getDate');
					//set dates to user view
					var nextdayDate=dateADD(selectedDate);
					var nextDateStr = zeroPad(nextdayDate.getDate(),2)+"-"+zeroPad((nextdayDate.getMonth()+1),2)+"-"+(nextdayDate.getFullYear());
					$("#<?php echo $a_v[1]?>").datepicker({minDate:nextDateStr});
					//setting checkout based on check in
					$("#<?php echo $a_v[1]?>").datepicker('option','minDate',nextdayDate);
					//update only if date is not disabled
					if ($("#<?php echo $a_v[1]?>").is(':disabled') == false && $("#<?php echo $a_v['1']?>").hasClass('disable-date-auto-update') == false) {
						$("#<?php echo $a_v[1]?>").val(nextDateStr);
					}
				});
			<?php
			}
		}
	}
	?>
});
</script>
<?php
/**
 * DONT DARE TO EDIT THIS FILE
 */
?>
