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
				case CARADULT_DATE_PICKER : ?>
				caradultDatePicker("<?php echo $d_v[0]?>");
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
	function caradultDatePicker(elementId)
{
	
	$('#'+elementId).datepicker({
		yearRange:"-100:-24",
		changeYear:true,
		changeMonth:true,
		numberOfMonths:1,
		dateFormat:'dd-mm-yy',
		// minDate: '-100Y',
	    // maxDate: '-23Y',
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

