<?php
$active_domain_modules = $this->active_domain_modules;
//debug($active_domain_modules);die();
$tiny_loader = $GLOBALS['CI']->template->template_images('tiny_loader_v1.gif');
$tiny_loader_img = '<img src="' . $tiny_loader . '" class="loader-img" alt="Loading">';
$booking_summary = array();
//debug($default_view);die;
?>
<link rel="stylesheet" type="text/css" href="<?=$GLOBALS['CI']->template->template_css_dir('owl.carousel.min.css');?>">

 <!-- Direct flight from -->
 <div class="col-xs-12 mn_slider nopad">
    <div id="hero-wrapper">
    <div class="carousel-wrapper">
      <div id="hero-carousel" class="carousel slide carousel-fade">
        <div class="carousel-inner">
          <?php foreach($banner_images["data"] AS $key=>$bi) { 
            if($key==0)
                $active="active";
            else
                $active="";
         ?>
          <div class="item <?php echo $active; ?>">
             <img class="" src="<?php echo $GLOBALS['CI']->template->template_images($bi["image"]); ?>" alt="">
          </div>
        <?php } ?>
        </div>
        <a class="left carousel-control" href="#hero-carousel" data-slide="prev">
          <i class="fa fa-chevron-left left"></i>
        </a>
        <a class="right carousel-control" href="#hero-carousel" data-slide="next">
          <i class="fa fa-chevron-right right"></i>
        </a>
      </div>
    </div>
  </div>
<!-- Search Engine Start -->
<div class="clearfix"></div>
<?php echo $search_engine; ?>
<div class="clearfix"></div>
<!-- Search Engine End -->
<?php if ($default_view==META_PACKAGE_COURSE) { ?>
	<div class="package_grid">
		<?php echo $this->session->flashdata("msg"); ?>
		 <div class="col-md-12 id-filter text-center">
      <div class="row">
        <div class="col-sm-4 padfive">
              <div class="row">
                <div class="col-sm-2 padfive">
                  <div class="custom-control">
                    <input type="radio" class="custom-control-input tour_type_fil" id="customRadio1" value="0" name="customRadio1" checked>
                    <label class="custom-control-label" for="customRadio1">All</label>
                  </div>
                </div>
                <div class="col-sm-5 padfive">
                  <div class="custom-control">
                    <input type="radio" class="custom-control-input tour_type_fil" id="customRadio2"   value="2" name="customRadio1">
                    <label class="custom-control-label" for="customRadio2">Domestic</label>
                  </div>
                </div>
                <div class="col-sm-5 padfive">
                  <div class="custom-control">
                    <input type="radio" class="custom-control-input tour_type_fil" id="customRadio3"  value="1" name="customRadio1">
                    <label class="custom-control-label" for="customRadio3">International</label>
                  </div>
                </div>
              </div> 
            </div>
            <div class="col-sm-4 padfive">
            </div>
            <div class="col-sm-4 padfive">
              <div class="row">
                <div class="col-sm-2 padfive">
                  <div class="custom-control">
                    <input type="radio" class="custom-control-input trip_cat_fil" id="groupRadio1" value="0" name="groupRadio1" checked>
                    <label class="custom-control-label" for="groupRadio1">All</label>
                  </div>
                </div>
                <div class="col-sm-5 padfive">
                  <div class="custom-control">
                    <input type="radio" class="custom-control-input trip_cat_fil" id="groupRadio2" value="fit" name="groupRadio1">
                    <label class="custom-control-label" for="groupRadio2">Customize Package</label>
                  </div>
                </div>
                <div class="col-sm-5 padfive">
                  <div class="custom-control">
                    <input type="radio" class="custom-control-input trip_cat_fil" id="groupRadio3" value="group" name="groupRadio1">
                    <label class="custom-control-label" for="groupRadio3">Group Package</label>
                  </div>
                </div>
              </div> 
            </div>
      </div>
    </div>
		<div class="container-fluid id-mt-2">
      <div class="id-pagehdwrap">
         <h2 class="id-pagehding">Trending Holiday Packages</h2>
      </div>
	
		<div class="col-md-12">
			<div class="row">
			<?php 
			//debug($inter_markup);debug($domestic_markup);
			foreach ($top_attraction_package as $int_tour_key => $int_tour_value) {
            
				if($int_tour_value['banner_image']==''){
					$int_tour_value['banner_image']="Tulips.jpg";
				}else{
					$int_tour_value['banner_image']=$int_tour_value['banner_image'];
				}
				$inclusions= json_decode($int_tour_value['inclusions_checks']);
				
				if($int_tour_value['trip_type']=='1'){
					$int_tour_value['netprice_price']=$int_tour_value['netprice_price']+$inter_markup[0]['value'];
				}else{
					$int_tour_value['netprice_price']=$int_tour_value['netprice_price']+$domestic_markup[0]['value'];
				}
				//debug($inclusions);
			   ?>
				<?php  
				
				
				//debug($int_tour_value);
				if($int_tour_value['tour_pack_type'] == "fit"){ 
					$from_range=explode(',',$int_tour_value['valid_frm']);
					$to_range=explode(',',$int_tour_value['valid_to']);
					$group_departure=array();
					foreach($from_range as $d_key => $d_val){
						$start_date =$d_val;
						$end_date = $to_range[$d_key];
						while (strtotime($start_date) <= strtotime($end_date)) {
							$group_departure[]=date('j-n-Y', strtotime($start_date));
							$start_date = date ("Y-m-d", strtotime("+1 days",strtotime($start_date)));
						}
					}
					$last_item=end($group_departure);
					$last_item=date('Y-n-j', strtotime($last_item));
					$first_item=date('Y-n-j', strtotime($group_departure[0]));
					$group_departure=implode(',',$group_departure); 
			
				}else{
					$group_departure=array();
					foreach($dep_dates as $dep_key => $dep_val){ 
						$group_departure[]=date('j-n-Y', strtotime($dep_val['dep_date']));
					}
					$last_item=end($group_departure);
					$last_item=date('Y-n-j', strtotime($last_item));
					$first_item=date('Y-n-j', strtotime($group_departure[0]));
					$group_departure=implode(',',$group_departure); 
			
				} 
			?>
				<div class="col-sm-3 each_package">
				<input type="hidden" class="tour_type" value="<?=$int_tour_value['trip_type']?>">
				<input type="hidden" class="tour_cat" value="<?=$int_tour_value['tour_pack_type']?>">
					<div class="id-selected-package">
						<div class="id-image">
							<div class="id-content1">
								<div class="id-content-overlay1"></div>
								<a href="<?php echo base_url().'index.php/tours/holiday_package_detail/'.$int_tour_value['pack_id']?>">
								<img class="id-content-image1" src="<?=$this->template->domain_images($int_tour_value['banner_image'])?>" alt="img_grid">
								
								</a>
								<div class="id-content-details1 id-fadeIn-bottom1">
									<ul class="id-ul">INCLUSIONS:
									<?php 
										foreach($inclusions as $inc_val){
									?>
											<li><i class="fa fa-check" aria-hidden="true"></i>&nbsp; <?=$inc_val?></li>
									
									<?php
										}
									?>
										
									</ul>
								</div>
							</div>
						</div>
						<div class="caption text-center">
							<h4><?=$int_tour_value['package_name']?></h4>
							<div class="pack_details">
							<?php 
								$country_count=explode(',',$int_tour_value['tours_country']);
								$country_count=count($country_count);
								$city_count=explode(',',$int_tour_value['tours_city']);
								$city_count=count($city_count);
							?>
								<div>
									<span><i class="fa fa-globe" aria-hidden="true"></i>&nbsp; <?=$country_count?><span>country</span></span>
									<span><i class="fa fa-home" aria-hidden="true"></i>&nbsp; <?=$city_count?><span>cities</span></span>
									<span><i class="fa fa-calendar" aria-hidden="true"></i>&nbsp; <?=$int_tour_value['duration']?><span>nights</span></span>
								</div>
								
							</div>
							<div class="price_package">
								<div>
									<span>Package cost</span>
                                    <span><del><?=$int_tour_value['currency']?> <?=number_format($int_tour_value['market_price'],0)?>/-<del></span>
									<span><?=$int_tour_value['currency']?> <?=number_format($int_tour_value['netprice_price'],0)?></span>
								</div>
								<div>
									<a href="<?php echo base_url().'index.php/tours/holiday_package_detail/'.$int_tour_value['pack_id']?>/main_page" class="btn btn-default">View more</a>
									<a data-toggle="modal" data-target="#enquiry_form_rel" data-pack_id="<?=$int_tour_value['pack_id']?>" data-pack_code="<?=$int_tour_value['tour_code']?>" data-pack_name="<?=$int_tour_value['package_name']?>" class="btn btn-danger enquiry_data">Quick enquiry</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				
			<?php } ?> 
			</div>
		</div>
    </div><!-- container-fluid ends -->
	</div> <!--packagegrid ends-->
<div class="clearfix"></div>
	<!------ enquiry form for related package ------------->

	
    <div class="modal fade" id="enquiry_form_rel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Package Enquiry</h4>
          </div>
          <div class="modal-body">
          <form action="<?php echo base_url();?>index.php/tours/send_enquiry/home" method="post" id="send_enquiry">
            <div class="container-fluid id-enquiry-modal nopad">
            <input type="hidden"  name="pack_id"  class="pack_id" value="<?=$package_details[0]['id']?>">
            <input type="hidden"  name="pack_name"  class="pack_name" value="<?=$package_details[0]['package_name']?>">
			<input type="hidden"  name="agent_id" class="agent_id"  value="<?=$this->entity_user_id?>">
			<div class="form-group hide">
				<label>Package Code</label>
				<input type="text"  name="pack_code" class="form-control pack_code" value="<?=$package_details[0]['tour_code']?>" placeholder="Enter Name" maxlength="30" required readonly>
			</div>
             <div class="form-group hide">
              <label>Name</label>
              <input type="text"  name="name" class="form-control" placeholder="Enter Name" value="<?=$this->entity_firstname?>" maxlength="30" required readonly>
             </div>
             <div class="form-group hide">
              <label>Email</label>
              <input type="Email"  name="Email" class="form-control" placeholder="Enter Email" value="<?=$this->entity_email?>" maxlength="45" required readonly>
             </div>
             <div class="form-group hide">
              <label>Phone</label>
              <input type="text"  name="phone" class="form-control" placeholder="Enter Phone" value="<?=$this->entity_phone?>" maxlength="12" required readonly>
             </div>
             <div class="row id-body-div">
                  <div class="col-sm-4">
                    <label class="id-label">Package Code</label><p><strong class="pack_id_text"><?=$package_details[0]['tour_code']?></strong></p>
                  </div>
                  <div class="col-sm-4">
                    <label class="id-label">Agent Name</label><p><?=$this->entity_firstname?></p>
                  </div>
                  <div class="col-sm-4">
                    <label class="id-label">Email</label><p><?=$this->entity_email?></p>
                  </div>
                  
              </div>
             
			<div class="row">
				  <div class="col-sm-2 padfive">
					<label class="id-label">Adult</label>
					<input type="number"  name="adult" class="form-control" placeholder="Adult" required>
				  </div>
				  <div class="col-sm-2 padfive">
					<label class="id-label">Child</label>
					<input type="number"  name="child" class="form-control" placeholder="Child" required>
				  </div>
				  <div class="col-sm-2 padfive">
					<label class="id-label">Infant</label>
					<input type="number"  name="infant" class="form-control" placeholder="Infant" required>
				  </div>
				  <div class="col-sm-6 padfive">
					<label class="id-label">Departure Date</label>
					 <input  id="enquiry_datepicker_outss"  name="dep_date" type="date" class="form-control id-inputfield enquiry_datepicker_rel" value="dd/mm/yyyy">
				  </div>
			</div>
			<div class="row">
				  <div class="col-sm-12 padfive">
					<label class="id-label">Messenger</label>
					<textarea  name="message" aria-hidden="true" class="form-control" placeholder="Enter details if any" maxlength="200"></textarea>
				  </div>
			</div>
			<div class="row">
				  <div class="col-sm-offset-8 col-sm-4 nopad">
					<button class="btn btn-danger form-control">Send Enquiry</button>
				  </div>
			</div>
           
          
          </div>
          
          </form>
           </div>
        </div>
        </div>
    </div>

<!--<div class="package_grid">
    <h3 class="text-center">International Packages</h3>
    <div class="col-md-12">
        <div class="container">
        <?php 
     // debug($international_tour);
        foreach ($international_tour as $int_tour_key => $int_tour_value) {
            
            if($int_tour_value['banner_image']==''){
                $int_tour_value['banner_image']="Tulips.jpg";
            }else{
                $int_tour_value['banner_image']=$int_tour_value['banner_image'];
            }
           ?>
        <div class="col-md-3">
        <div class="pack_grd">
            <a href="<?php echo base_url().'index.php/tours/holiday_package_detail/'.$int_tour_value['id']?>">
            <img src="<?=$this->template->domain_images($int_tour_value['banner_image'])?>" alt="img_grid">
            <div class="caption text-center">
                <h4><?=$int_tour_value['package_name']?></h4>
                
            </div>
            </a>
        </div>
        </div>
        <?php } ?> 
    </div></div>
	<h3 class="text-center">Domestic Packages</h3>
    <div class="col-md-12">
        <div class="container">
        <?php 
     // debug($international_tour);
        foreach ($domestic_tour as $dom_tour_key => $dom_tour_value) {
            
            if($dom_tour_value['banner_image']==''){
                $dom_tour_value['banner_image']="Tulips.jpg";
            }else{
                $dom_tour_value['banner_image']=$dom_tour_value['banner_image'];
            }
           ?>
        <div class="col-md-3">
        <div class="pack_grd">
            <a href="<?php echo base_url().'index.php/tours/holiday_package_detail/'.$dom_tour_value['id']?>">
            <img src="<?=$this->template->domain_images($dom_tour_value['banner_image'])?>" alt="img_grid">
            <div class="caption text-center">
                <h4><?=$dom_tour_value['package_name']?></h4>
                
            </div>
            </a>
        </div>
        </div>
        <?php } ?> 
    </div></div>
</div> 
<div class="clearfix"></div>
<div class="spcl_atrct">
    <h3 class="text-center">Special Attractions</h3>
  <div class="col-xs-12">
    <?php 
    
    foreach($top_attraction_package as $top_key => $top_val){
    
    ?>
    <div class="col-md-4">
      <div class="thumbnail">
        <a href="<?php echo base_url().'index.php/tours/holiday_package_detail/'.$top_val['pack_id']?>" target="_blank">
          <img src="<?php echo $GLOBALS['CI']->template->domain_images($top_val['banner_image'])?>" alt="Lights">
          <div class="caption">
            <h4><?=$top_val['package_name']?></h4>
            <p><?=$top_val['currency'].' '.$top_val['airliner_price']?></p>
          </div>
        </a>
      </div>
    </div>
    <?php } ?>
    
  </div>
</div> -->
<div class="clearfix"></div>
<?php }else{ ?> 
<!--  -->
</div>
<hr>
<div class="panel panel-default hide">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <div id='booking-summary' class="col-md-12">
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="panel panel-default hide">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                Notification!!!
            </div>
            <?php
            $latest_trans_list = '';
            $latest_trans_summary = '';
            if (valid_array($latest_transaction)) {
                // debug($latest_transaction);exit;
                foreach ($latest_transaction as $k => $v) {
                    $latest_trans_list .= '<li class="item">';
                    $latest_trans_list .= '<div class="product-img image"><i class="' . get_arrangement_icon(module_name_to_id($v['transaction_type'])) . '"></i></div>';
                    $latest_trans_list .= '<div class="product-info">
                                    <a class="product-title" href="' . base_url() . 'index.php/transaction/logs?app_reference=' . trim($v['app_reference']) . '">
                                        ' . $v['app_reference'] . ' -' . app_friendly_day($v['created_datetime']) . ' <span class="label label-primary pull-right"><i class="fa fa-inr"></i> ' . ($v['grand_total']) . '</span>
                                    </a>
                                    <span class="product-description">
                                        ' . $v['remarks'] . '
                                    </span>
                                </div>';
                    $latest_trans_list .= '</li>';
                }
            }
            ?>
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Recent Booking Transactions</h3>
                    </div>
                    <div class="box-body">
                        <ul class="products-list product-list-in-box">
                            <?= $latest_trans_list ?>
                        </ul>
                    </div>
                    <div class="box-footer text-center">
                        <a class="uppercase" href="<?= base_url() . 'index.php/transaction/logs' ?>">View All Transactions</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<?php
Js_Loader::$css[] = array('href' => SYSTEM_RESOURCE_LIBRARY . '/fullcalendar/fullcalendar.css', 'media' => 'screen');
Js_Loader::$css[] = array('href' => SYSTEM_RESOURCE_LIBRARY . '/fullcalendar/fullcalendar.print.css', 'media' => 'print');
Js_Loader::$js[] = array('src' => SYSTEM_RESOURCE_LIBRARY . '/fullcalendar/lib/moment.min.js', 'defer' => 'defer');
Js_Loader::$js[] = array('src' => SYSTEM_RESOURCE_LIBRARY . '/fullcalendar/fullcalendar.min.js', 'defer' => 'defer');
Js_Loader::$js[] = array('src' => SYSTEM_RESOURCE_LIBRARY . '/Highcharts/js/highcharts.js', 'defer' => 'defer');
Js_Loader::$js[] = array('src' => SYSTEM_RESOURCE_LIBRARY . '/Highcharts/js/modules/exporting.js', 'defer' => 'defer');
?>
<script type="text/javascript" src="<?=$GLOBALS['CI']->template->template_js_dir('owl.carousel.min.js');?>"></script>
<script type="text/javascript">
	var curday = function(sp){
		today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1; //As January is 0.
		var yyyy = today.getFullYear();

		//if(dd<10) dd='0'+dd;
		//if(mm<10) mm='0'+mm;
		//return (mm+sp+dd+sp+yyyy);
		return (yyyy+sp+mm+sp+dd);
	};
	jQuery(function(){
		var e_day="<?php echo $group_departure; ?>"; 
		//console.log(e_day);
		var enableDays=e_day.split(',');
		var lastItem = "<?php echo $last_item; ?>";
		var firstItem = "<?php echo $first_item; ?>";
		//console.log(enableDays);
		//console.log(lastItem);
		//console.log(firstItem);
		if(firstItem<curday('-')){
			var firstItem = curday('-');
		}
		function enableAllTheseDays(date) {
			var sdate = $.datepicker.formatDate( 'd-m-yy', date)
			
			if($.inArray(sdate, enableDays) != -1) {
			
				return [true];
			}
		
			return [false];
		}
    //console.log(curday('/'));
	//console.log(curday('-'));
	//console.log(firstItem);
		
		$('#datepicker_dat_group,#enquiry_datepicker_in,#enquiry_datepicker_out').datepicker({dateFormat: 'dd-mm-yy', beforeShowDay: enableAllTheseDays,minDate: new Date(firstItem), maxDate: new Date(lastItem)});
		//$('#enquiry_datepicker_in,#enquiry_datepicker_out').datepicker({dateFormat: 'dd-mm-yy', beforeShowDay: enableAllTheseDays,minDate: new Date(firstItem), maxDate: new Date(lastItem)});
		
	})
    $("#owlCarousel12").owlCarousel({
        items : 1, 
        itemsDesktop : [1000,3],
        itemsDesktopSmall : [991,3], 
        itemsTablet: [767,2], 
        itemsMobile : [480,1], 
        navigation : true,
        pagination : false
    });
	
</script>
<script>
    $(function () {
    //LEAD REPORT -line graph
    $('#booking-timeline').highcharts({
    credits: {
    enabled: false
    },
            chart: {
            type: 'column'
            },
            title: {
            text: 'Booking Details',
                    x: - 20 //center
            },
            subtitle: {
            text: '',
                    x: - 20
            },
            xAxis: {
            categories: <?= json_encode($time_line_interval); ?>,
                    tickPixelInterval: 0
            },
            yAxis: {
            allowDecimals: false,
                    min: 0,
                    max: <?php echo $max_count; ?>,
                    title: {
                    text: 'No Of Booking'
                    },
                    plotLines: [{
                    value: 0,
                            width: 1,
                            color: '#808080'
                    }]
            },
            tooltip: {
            valueSuffix: ''
            },
            legend: {
            title: {
            text: 'No Of Booking'
            },
                    subtitle: {
                    text: 'count'
                    },
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle',
                    borderWidth: 0,
                    labelFormatter: function() {
                    var total = 0;
                    var total_face_value = this.userOptions.total_earned || 0;
                    for (var i = this.yData.length; i--; ) {
                    total += this.yData[i];
                    };
                    return this.name + '(' + total + ')';
                    }

            },
            series: <?= json_encode(array_values($time_line_report)); ?>,
            navigation: {
            buttonOptions: {
            align: 'right',
                    verticalAlign: 'top',
                    x: 0,
                    y: 0
            }
            }

    });
    $('#booking-summary').highcharts({
    title: {
    text: 'Monthly Recap Report'
    },
            xAxis: {
            categories: <?= json_encode($time_line_interval); ?>
            },
            yAxis: {
            allowDecimals: false,
                    title: {
                    text: 'Profit In <?= COURSE_LIST_DEFAULT_CURRENCY_VALUE ?>'
                    }
            },
            labels: {
            items: [{
            html: 'Total Profit Earned in <?= COURSE_LIST_DEFAULT_CURRENCY_VALUE ?>',
                    style: {
                    left: '50px',
                            top: '18px',
                            color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                    }
            }]
            },
            series: [<?= (isset($group_time_line_report[0]) ? json_encode($group_time_line_report[0]) . ',' : ''); ?>
<?= (isset($group_time_line_report[1]) ? json_encode($group_time_line_report[1]) . ',' : ''); ?>
<?= (isset($group_time_line_report[2]) ? json_encode($group_time_line_report[2]) . ',' : ''); ?>
<?= (isset($group_time_line_report[3]) ? json_encode($group_time_line_report[3]) . ',' : ''); ?>
<?= (isset($group_time_line_report[4]) ? json_encode($group_time_line_report[4]) . ',' : ''); ?>
<?= (isset($group_time_line_report[5]) ? json_encode($group_time_line_report[5]) . ',' : ''); ?>

            {
            type: 'pie',
                    name: 'Total Earning',
                    data: <?= json_encode($module_total_earning) ?>,
                    center: [100, 80],
                    size: 100,
                    showInLegend: false,
                    dataLabels: {
                    enabled: false
                    }
            }]
    });
    });
    $(document).ready(function() {
		$(document).on('click','.enquiry_data',function(){
			var pack_id=$(this).data('pack_id');
			var pack_code=$(this).data('pack_code');
			var pack_name=$(this).data('pack_name');
			
			$('.pack_id').val(pack_id);
			$('.pack_code').val(pack_code);
			$('.pack_id_text').text(pack_code);
			$('.pack_name').val(pack_name);
			
		});
		$(document).on('change','.tour_type_fil',function(){
		//	var this_id=$(this).attr('id');
			//$('.tour_type_fil').attr('checked','');
			//$('#'+this_id).attr('checked','checked');
			var tour_type=$(this).val();
			//var tour_cat=$('.trip_cat_fil').val();
			var tour_cat=$("input[name='groupRadio1']:checked").val();
			package_filter(tour_type,tour_cat);
		});
		$(document).on('change','.trip_cat_fil',function(){
			//var tour_type=$('.tour_type_fil').val();
			var tour_type=$("input[name='customRadio1']:checked").val();
			var tour_cat=$(this).val();
			package_filter(tour_type,tour_cat);
		});
		function package_filter(tour_type,tour_cat){
			//alert(tour_type);alert(tour_cat);
			$('.each_package').each(function() {
				var pack_trip_type=$(this).find('.tour_type').val();
				var pack_tour_cat=$(this).find('.tour_cat').val();
				
				if(pack_trip_type==tour_type){
					//$(this).removeClass('hide');
					var type_flag=1;
				}else{
					if(tour_type==0){ 
						//$(this).addClass('hide');
						var type_flag=1;
					}else{
						var type_flag=0;
					}
				}
				if(pack_tour_cat==tour_cat){
					//$(this).removeClass('hide');
					var cat_flag=1;
				}else{
					if(tour_cat==0){ 
						//$(this).addClass('hide');
						var cat_flag=1;
					}else{
						var cat_flag=0;
					}
				}
				//console.log(type_flag,cat_flag);
				if(type_flag==1 && cat_flag==1){
					$(this).removeClass('hide');
				}else{
					$(this).addClass('hide');
				}
			});
		}




    var event_list = {};
    function enable_default_calendar_view()
    {
    load_calendar('');
    // get_event_list();
    // set_event_list();
    $('[data-toggle="tooltip"]').tooltip();
    }
    function reset_calendar()
    {
    // $("#booking-calendar").fullCalendar('removeEvents');
    // get_event_list();
    // set_event_list();
    }
    //Reload Events
    setInterval(function(){
    reset_calendar();
    $('[data-toggle="tooltip"]').tooltip();
    }, <?php echo SCHEDULER_RELOAD_TIME_LIMIT; ?>);
    enable_default_calendar_view();
    //sets all the events
    function get_event_list()
    {
    set_booking_event_list();
    }
    //loads all the loaded events
    function set_event_list()
    {
    $("#booking-calendar").fullCalendar('addEventSource', event_list.booking_event_list);
    if ("booking_event_list" in event_list && event_list.booking_event_list.hasOwnProperty(0)) {
    //focus_date(event_list.booking_event_list[0]['start']);
    }
    }

    //getting the value of arrangment details
    function set_booking_event_list()
    {
    $.ajax({
    url:app_base_url + "index.php/ajax/booking_events",
            async:false,
            success:function(response){
            //console.log(response)
            event_list.booking_event_list = response.data;
            }
    });
    }

    //load default calendar with scheduled query
    function load_calendar(event_list)
    {
    $('#booking-calendar').fullCalendar({
    header: {
    center: 'title'
    },
            //defaultDate: '2014-11-12', 
            editable: false,
            eventLimit: false, // allow "more" link when too many events
            events: event_list,
            eventRender: function(event, element) {
            element.attr('data-toggle', 'tooltip');
            element.attr('data-placement', 'bottom');
            element.attr('title', event.tip);
            element.attr('id', event.optid);
            element.find('.fc-time').attr('class', "hide");
            element.attr('class', event.add_class + ' fc-day-grid-event fc-event fc-start fc-end');
            element.attr('href', event.href);
            element.attr('target', '_blank');
            element.css({'font-size':'10px', 'padding':'1px'});
            if (event.prepend_element) {
            element.prepend(event.prepend_element);
            }
            },
            eventDrop : function (event, delta) {
            event.end = event.end || event.start;
            if (event.start && event.end) {
            update_event_list(event.optid, event.start.format(), event.end.format());
            focus_date(event.start.format());
            } else {
            reset_calendar();
            }
            }
		});
		}
		function focus_date(date)
		{
			$('#booking-calendar').fullCalendar('gotoDate', date);
		}
		$( "#enquiry_form_rel" ).submit(function(e) {
		  var sel_dep_date=$('.enquiry_datepicker_rel').val();
		  //alert(sel_dep_date);
			if(sel_dep_date==''){
				alert("Please select departure date.");
				e.preventDefault();
			}
		});
		$(document).on('click','.rel_enq',function() {
			var sel_pack_id=$(this).data('pack_id');
			var sel_pack_name=$(this).data('pack_name');
			var sel_pack_code=$(this).data('pack_code');
			//alert(sel_pack_id);
			$('#enquiry_form_rel').find('.pack_id').val(sel_pack_id);
			$('#enquiry_form_rel').find('.pack_name').val(sel_pack_name);
			$('#enquiry_form_rel').find('.pack_code').val(sel_pack_code);
		});
		$(document).on('click','.send_enq',function(e){
			var enq_form				=$(this).parents('.enq_form').attr('id');
			var pack_id 				=$('#'+enq_form).find('.pack_id').val();
			var pack_name  				=$('#'+enq_form).find('.pack_name').val();
			var agent_id 				=$('#'+enq_form).find('.agent_id').val();
			var pack_code 				=$('#'+enq_form).find('.pack_code').val();
			var name 					=$('#'+enq_form).find('.name').val();
			var Email 					=$('#'+enq_form).find('.Email').val();
			var phone 					=$('#'+enq_form).find('.phone').val();
			var passenger 				=$('#'+enq_form).find('.passenger').val();
			var datepicker_dat_group 	=$('#'+enq_form).find('.datepicker_dat_group').val();
			var Messenger				=$('#'+enq_form).find('.Messenger').val();
			if(datepicker_dat_group=='dd/mm/yyyy'){
				alert("Please select departure date.");
				e.preventDefault();
			}else{
				$.post('<?=base_url();?>index.php/tours/send_enquiry',{'pack_id':pack_id,'pack_name':pack_name,'agent_id':agent_id,'pack_code':pack_code,'name':name,'Email':Email,'phone':phone,'passenger':passenger,'datepicker_dat_group':datepicker_dat_group,'Messenger':Messenger},function(data)
				{
					alert("Successfully sent enquiry!!!");
				});			
			}
		});
    });


    $('.topflight').on('click', function() {
        trip_type = $(this).attr('data-trip_type'),
        from = $(this).attr('data-from');
        to = $(this).attr('data-to');
        from_loc_id = $(this).attr('data-from_loc_id');
        to_loc_id = $(this).attr('data-to_loc_id');
        depature =  $(this).attr('data-departue');
        v_class =  $(this).attr('data-v_class');
        carrier = $(this).attr('data-carrier');
        adult = $(this).attr('data-adult');
        child =  $(this).attr('data-child');
        infant = $(this).attr('data-infant');
        search_flight =  $(this).attr('data-flight_search');
   
   
          $.ajax({
            type: "GET",
            url: "<?php echo base_url().'index.php/general/pre_flight_search_ajax/' ?>",
            data: {trip_type : trip_type,from : from,from_loc_id : from_loc_id,to : to,to_loc_id :to_loc_id, depature:depature, v_class: v_class,carrier: carrier, adult:adult, child:child,infant:infant, search_flight:search_flight},
            success: function(result){
            location.href = result;
            }
            });

        });
</script>
<script type="text/javascript">
$(document).ready(function() {
    $('.carousel').carousel({
  interval: 3000,
  pause: false
});
});
</script>

<style type="text/css">
	.p_label
	{
		font-size: 16px;
		color: #222;
		font-weight: 500;
		padding-left: 10px;
	}
	.hd_pack
	{
		border-bottom: 1px solid #eee;
	}
	.child_pack {
    border-bottom: 1px solid #eee;
    margin: 15px 0px 0px;
}

.pack_details div
{
	display: flex;
    background: #eee;
}
.pack_details div span
{
	margin: 2px;
    flex: 1 1 0;
   /* width: 0;*/
    
    padding: 0;
    color: #2a2a2a;
}
.pack_details div span span {
    text-transform: capitalize;
    display: inline-block;
}
.price_package div
{
	display: flex;
}
    .price_package span
    {
    	margin: 2px;
    flex: 1 1 0;
    width: 0;
    
    padding: 4px 0px;
    color: #fff;
    }
     .price_package div 
    {
    	display: flex;
    }
   .price_package div a {
    margin: 5px 2px;
    flex: 1 1 0;
    width: 0;
     border-radius: 0px !important;
    /*border: 1px solid #222;
    border-radius: 4px;
    padding: 4px 10px;
    color: #f5930c;
    background: #222;*/
} .price_package {
    background: linear-gradient(96deg,#002042,#0a8ec1);
    padding: 3px 5px;
}
.package_grid .caption h4 {
    background: #ffc800;
    padding: 8px;
    margin: 0;
    color: #222;
    font-weight: 600;
    text-transform: capitalize;
    font-size: 15px;
}
/*13/7/2020*/

    .id-content p{
      font-size: 18px;
      color: #fff;
    }
    .id-selected-package{
      border: 4px solid #fff;
      box-shadow: 2px 2px 5px #ccc;
      margin-bottom: 10px;
     
    }
    .id-selected-package h3{
      color: #000;
      margin: 0;
      padding: 10px;
      text-align: center;
      background-color: #ffc800;
    }
    .id-content del{
      font-weight: normal;
    }
    .id-sub-content1{
      padding: 10px;
      text-align: center;
      font-size: 15px;
      background-color: #eee;
    }
    .id-sub-content2{
      background: linear-gradient(96deg,#002042,#0a8ec1);
      padding: 10px 5px;
    }
    .id-sub-content2 .btn {
      font-size: 12px!important;
    }
    .id-sub-content2 .btn-danger {
      color: #fff;
      background-color: #da0600;
      border: none;
    }
    .id-sub-content2 .btn-danger:hover {
      color: #fff;
      background-color: #b90803;
      border: none;
    }
    .id-image img{
      width: 100%;
      height: 190px !important;
    }
    .id-image img {
      max-width: 100%;
      transition: transform 0.1s ease-in-out;
    }
    .id-selected-package:hover img {
      transform: scale(1.2);
    }
    .id-selected-package:hover{
      border: 2px solid #000;
      margin: 7px 1px;
    }
    .id-image{
      margin: 0 auto;
      overflow: hidden;
    }
    .id-content1 {
      position: relative;
      margin: auto;
      overflow: hidden;
    }
    .id-selected-package:hover .id-content-overlay1{
      opacity: 1;
    }
    .id-content-details1 {
      background: rgba(0,0,0,0.7)!important;
      position: absolute;
      text-align: center;
      padding-left: 1em;
      padding-right: 1em;
      width: 100%;
      height: 100%;
      opacity: 0;
      -webkit-transform: translate(-50%, -50%);
      -moz-transform: translate(-50%, -50%);
      transform: translate(-50%, -50%);
      -webkit-transition: all 0.3s ease-in-out 0s;
      -moz-transition: all 0.3s ease-in-out 0s;
      transition: all 0.3s ease-in-out 0s;
    }
    .id-selected-package:hover .id-content-details1{
      top: 50%;
      left: 50%;
      opacity: 1;
    }
    .id-fadeIn-bottom1{
      top: 50%;
      left: 50%;
    }
    .id-content-details1 li{
      color: #fff;
      font-size: 18px;
    }
    .id-content-details1 ul{
      color: #ffc800;
      font-size: 20px;
      text-align: left;
      padding-top: 10px;
    }
    .id-ul i{
      color: #ffc800;
    }
    .id-search {
      width: 100%;
      box-sizing: border-box;
      border: none;
      border-bottom: 2px solid #ccc;
      font-size: 16px;
      background-color: transparent;
      background-position: 10px 10px; 
      background-repeat: no-repeat;
      padding: 5px 30px;
      -webkit-transition: width 0.4s ease-in-out;
      transition: width 0.4s ease-in-out;
      border-radius: 4px;
    }
    .id-filter{
      padding: 20px 10px 0 10px;
    }
    .id-pagehding{
      margin-top: 0;
      text-align: center;
    }
    .id-border-r{
      border-top-left-radius: 50px;
      border-top-right-radius: 50px;
      background-color: #fff;
      padding-bottom: 20px;
    }
    .custom-control-label, .custom-control-input{
      font-size: 15px;
      cursor: pointer;
    }
    .modal-footer {
    padding: 15px;
    text-align: right;
    border-top: none;
}
.id-label{
      color: #777!important;
      font-size: 12px;
      margin-top: 5px;
    }
    .id-enquiry-modal button{
      margin-top: 20px;
      border-radius: 4px!important;
      height: 45px;
    }
    .id-enquiry-modal .id-body-div{
      border: 1px solid #ccc;
      padding: 5px;
      margin-bottom: 10px;
      border-radius: 4px;
    }
</style>