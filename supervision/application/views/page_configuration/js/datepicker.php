<?php
/**
 * DONT DARE TO EDIT THIS FILE
 */
?>
<script>

//Need to change the datepicker months on mobile and other device
var dp_visi_months = 1;
if ($(window).width() > 420) {
	dp_visi_months = 2;	
} else {
		dp_visi_months = 1;
}
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
		numberOfMonths:dp_visi_months,
		dateFormat:'dd-mm-yy',
		changeMonth:true,
		changeYear:true            
	});
}
/**
*bind month picker to each date input
*/
function bindMonthPicker(inputId) {
	 $('#'+inputId).datepicker({
		numberOfMonths:dp_visi_months,
		dateFormat:'mm-yy',
		changeMonth:true,
		changeYear:true
	});
}

/**
*past datepicker
*/
function pastDatepicker(inputId) {
	 $('#'+inputId).datepicker({
		numberOfMonths:dp_visi_months,
		dateFormat:'dd-mm-yy',
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
		numberOfMonths:dp_visi_months,
		dateFormat:'dd-mm-yy',
		changeMonth:true,
		changeYear:true,
        minDate:0
	});
}

/**
*Future datepicker Single Month
*/
function futureDatepickerSingleMonth(inputId) {
	 $('#'+inputId).datepicker({
		numberOfMonths:1,
		dateFormat:'dd-mm-yy',
		changeMonth:true,
		changeYear:true,
        minDate:0
	});
}

//Dont show month and year
function futureDatepickerMonthDisabled(inputId)
{
	$('#'+inputId).datepicker({
		numberOfMonths:dp_visi_months,
		dateFormat:'dd-mm-yy',
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
		numberOfMonths:dp_visi_months,
		dateFormat:'mm-yy',
		changeMonth:true,
		changeYear:true,
        maxDate:0,
        yearRange: "-100:-0"
	});
}
function adultDatePicker(elementId)
{
	$('#'+elementId).datepicker({
		yearRange:"-100:-0",
		changeYear:true,
		changeMonth:true,
		numberOfMonths:dp_visi_months,
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
		numberOfMonths:dp_visi_months,
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
		numberOfMonths:dp_visi_months,
		dateFormat:'yy-mm-dd',
		minDate: '-2Y',
	    maxDate: '0',
	});
}

function auto_set_dates(date_1, input2, date_type)
{
	var date_1_ts = Date.parse(date_1);
	if (isNaN(date_1_ts) == false) {
		var ip_2 = $("#"+input2);
		ip_2.trigger("click");
		var selectedDate=date_1;
		//set dates to user view
		var nextdayDate=dateADD(selectedDate);
		var nextDateStr = zeroPad(nextdayDate.getDate(),2)+"-"+zeroPad((nextdayDate.getMonth()+1),2)+"-"+(nextdayDate.getFullYear());
		ip_2.datepicker('option',date_type,nextdayDate);
		
		var second_date = ip_2.datepicker('getDate');
		var date_diff = get_day_difference(selectedDate, second_date);
		//update only if date is not disabled
		if (date_diff < 1 && ip_2.is(':disabled') == false && ip_2.hasClass('disable-date-auto-update') == false) {
			ip_2.val(nextDateStr);
		}
	}
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
				case FUTURE_DATE_DISABLED_MONTH : //Without month selector ?>
					futureDatepickerMonthDisabled("<?php echo $d_v[0]?>");
					<?php
					break; 
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
				//single Month :
				case FUTURE_DATE_SINGLE_MONTH : ?>
				futureDatepickerSingleMonth("<?php echo $d_v[0]?>");
				<?php 
				break;
			}
		} else { ?>
			bindDatepicker("<?php echo $d_v;?>");
	<?php }
	} ?>
	<?php
	/**
	 * Auto setter of datepicker
	 */
	/**
	 * Auto setter of datepicker
	 */
	if (valid_array(self::$auto_adjust_datepicker_report) == true) {
		foreach (self::$auto_adjust_datepicker_report as $a_k => $a_v) {
			if (valid_array($a_v) == true) {

				?>
				//date validation
				$("#<?php echo $a_v[0]; ?>").change(function() {
					//manage date validation
					auto_set_dates($("#<?php echo $a_v[0]; ?>").datepicker('getDate'), "<?php echo $a_v[1]; ?>", 'minDate',0);
				});
				//if second date is already set then dont run
				if ($("#<?php echo $a_v[1]; ?>").val() == '' ) {
					auto_set_dates($("#<?php echo $a_v[0]; ?>").datepicker('getDate'), "<?php echo $a_v[1]; ?>", 'minDate',0);
				}
			<?php
			}
		}
	}
	if (valid_array(self::$auto_adjust_datepicker) == true) {
		foreach (self::$auto_adjust_datepicker as $a_k => $a_v) {
			if (valid_array($a_v) == true) {?>
				//date validation
				$("#<?php echo $a_v[0]; ?>").change(function() {
					//manage date validation
					auto_set_dates($("#<?php echo $a_v[0]; ?>").datepicker('getDate'), "<?php echo $a_v[1]; ?>", 'minDate');
				});
				//if second date is already set then dont run
				if ($("#<?php echo $a_v[1]; ?>").val() == '' ) {
					auto_set_dates($("#<?php echo $a_v[0]; ?>").datepicker('getDate'), "<?php echo $a_v[1]; ?>", 'minDate');
				}
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
