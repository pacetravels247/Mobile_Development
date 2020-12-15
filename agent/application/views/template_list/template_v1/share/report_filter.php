<?php
$active_domain_modules = $GLOBALS ['CI']->active_domain_modules;
$master_module_list = $GLOBALS ['CI']->config->item ( 'master_module_list' );
if (empty ( $default_view )) {
	$default_view = $GLOBALS ['CI']->uri->segment ( 2 );
}
?>
<!-- Module Tabs Starts-->
<ul id="myTab" role="tablist" class="nav nav-tabs b2b_navul">
<?php
	$append_query_string = $_SERVER['QUERY_STRING'];
	if(empty($append_query_string) == false) {
		$append_query_string = '?'.$append_query_string;
	}
	foreach ( $master_module_list as $k => $v ) {
		if (in_array ( $k, $active_domain_modules )) {
			if($v != 'package') {//FIXME: remove later
		?>
	<li class="<?=((@$default_view == $k || $default_view == $v) ? 'active' : '')?>" role="presentation">
		<a  href="<?=base_url()?>index.php/report/<?=($v)?><?=$append_query_string?>"> 
			<?=ucfirst($v)?>
		</a>
	</li>
	<?php } 
		} 
	}?>
</ul>


<div class="extra_content">
    <!-- Module Tabs Ends-->
    <!-- Search Filter Starts -->
    <div id="advance_search_form_container" class="serch_area_fltr">
        <form action="<?=base_url().'report/'.$default_view?>" method="get" autocomplete="off" class="form-horizontal" role="form">
            <div class="form-group">
                <label class="col-sm-1 control-label"> From </label>
                <div class="col-sm-2">
                    <input type="text" id="from_date" class="form-control" name="from_date" placeholder="From Date" value="<?=@$from_date?>">
                </div>
                <label class="col-sm-1 control-label"> To </label>
                <div class="col-sm-2">
                    <input type="text" id="to_date" class="form-control disable-date-auto-update" name="to_date" placeholder="To Date" value="<?=@$to_date?>">
                </div>
                <label class="col-sm-1 control-label"> Status </label>
                <div class="col-sm-2">
                    <select class="form-control" name="filter_booking_status">
                       <option value="">All</option>
                       	<?=generate_options(get_enum_list('report_filter_status'),(array)@$_GET['filter_booking_status']);?>
                    </select>
                </div>
                <div class="col-sm-1">
                    <button class="btn btn-success " type="submit">Search</button>
                </div>
                <div class="col-sm-1">
                    <!-- 
                    <a class="btn btn-warning " href="<?=base_url().'report/'.$default_view?>"><i class="fa fa-history"></i> Reset All</a>
                     -->
                     <button class="btn btn-warning" type="reset">Reset</button>
                </div>
            </div>
        </form>
    </div>
    <!-- Search Ends Ends -->
    <div class="clearfix"></div>
    <!-- Time Tab Filter Starts -->
    <div class="searc_fliter_all">
    <div class="filter_heading">Make your search easy</div>
    <?php $today_search = date('Y-m-d');?>
    <div class="list_of_sections">
		<a class="<?=(($today_search == @$_GET['today_booking_data']) ? 'active' : '')?>" href="<?=base_url().'report/'.$default_view.'?today_booking_data='.$today_search?>">Today Search</a>
		<?php 
			$filter_duration_values = array('Last Day Search' => 1,'Last 3 Day Search' => 3,'Last 7 Day Search' => 7,'Last 15 Day Search' => 15,'Last 1 Month Search' => 30,'Last 3 Month Search' => 90);//In days
			foreach($filter_duration_values as $k => $v) { 
			$prev_filter_date = date('Y-m-d', strtotime('-'.intval($v).' day'));
		?>
			<a class="<?=(($prev_filter_date == @$_GET['prev_booking_data']) ? 'active' : '')?>" href="<?=base_url().'report/'.$default_view.'?prev_booking_data='.$prev_filter_date?>"><?=$k;?></a>
		<?php }
		?>
    </div>
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