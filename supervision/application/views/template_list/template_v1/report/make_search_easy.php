<?php
$active_domain_modules = $GLOBALS ['CI']->active_domain_modules;
$master_module_list = $GLOBALS ['CI']->config->item ( 'master_module_list' );
if (empty ( $default_view )) {
	$default_view = $GLOBALS ['CI']->uri->segment ( 2 );
}
?>
<!-- Module Tabs Starts-->

<div class="extra_content">
    <!-- Module Tabs Ends-->
  
    <!-- Time Tab Filter Starts -->
    <div class="searc_fliter_all">
    <div class="filter_heading panel-body">
    	<h4>Make your search easy</h4>
    </div>
    <?php $today_search = date('Y-m-d');
    	$last_today_search = date('Y-m-d', strtotime('-1 day'));
    ?>
    <div class="list_of_sections">
		<a class="<?=(($today_search == @$_GET['today_booking_data']) ? 'active' : '')?> today_button" href="<?=base_url().'report/'.$default_view.'?today_booking_data='.$today_search?>">Today Search</a>
		<a class="<?=(($last_today_search == @$_GET['last_day_booking_data']) ? 'active' : '')?>" href="<?=base_url().'report/'.$default_view.'?last_day_booking_data='.$last_today_search?>">Last Day Search</a>
		<?php 
			$filter_duration_values = array('One Week Search' => 7,'One Month Search' => 30);//In days
			foreach($filter_duration_values as $k => $v) { 
			$prev_filter_date = date('Y-m-d', strtotime('-'.intval($v).' day'));
		?>
		
			<a class="<?=(($prev_filter_date == @$_GET['prev_booking_data']) ? 'active' : '')?>" href="<?=base_url().'report/'.$default_view.'?prev_booking_data='.$prev_filter_date?>"><?=$k;?></a>
		<?php }
		?>

    </div>
    <a href="<?=base_url().'report/'.$default_view.'?' ?>" id="s-clear-filter" class="btn btn-primary">Clear Filter</a>
    </div>
    <div class="clearfix"></div>
</div>
<script>
$(document).ready(function() {
	var cache = {};
	$("#auto_suggest_booking_id").autocomplete({
		source:  function( request, response ) {
	        var term = request.term;
	        if ( term in cache ) {
	          response( cache[ term ] );
	          return;
	        } else {
		        var module = $('#module', 'form#auto_suggest_booking_id_form').val().trim();
	        	$.getJSON( app_base_url+"index.php/ajax/auto_suggest_booking_id?module="+module, request, function( data, status, xhr ) {
	                cache[ term ] = data;
	                response( cache[ term ] );
	              });
	        }
	      },
	    minLength: 1
	 });
});
</script>
<?php
$datepicker = array(array('from_date', PAST_DATE), array('to_date', PAST_DATE));
$GLOBALS['CI']->current_page->set_datepicker($datepicker);
$this->current_page->auto_adjust_datepicker(array(array('from_date', 'to_date')));
?>