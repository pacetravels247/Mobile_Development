<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
<?php
//echo base_url();exit;
$currency = get_application_currency_preference();
$flight_datepicker = array (
		array (
				'flight_datepicker1',
				FUTURE_DATE_DISABLED_MONTH
		),
		array (
				'flight_datepicker2',
				FUTURE_DATE_DISABLED_MONTH
		)
);
$this->current_page->set_datepicker ( $flight_datepicker );
$airline_list = $GLOBALS['CI']->db_cache_api->get_airline_code_list();
Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('page_resource/index.css'), 'media' => 'screen');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/flight_suggest_group_request.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/datepicker.js'), 'defer' => 'defer');
?>


<style type="text/css">
.form-control[disabled] { background-color: #ddd !important; }	
.txtclr{
	color: #000 !important;
		float: left;
			padding: 10px;
			text-align: center;
}
.txtclr1{
	color: #000 !important;
		
			padding: 10px;
			
}
.adt { padding:0px !important; text-align: center; }
.txtclr_n{
	color: #000 !important;
		
			padding: 5px 0;
			
}
.smalway{
	padding: 10px 0;

}

.pax-count-wrapper, .sidebord1 { width:100%; }

.placerows{
	margin: 10px 0;
}
.sidebord{
	float: left;
	 width:100%;
}
.sidebord1{
	float: left;
}
.celroe{
	display: block !important;
	float: left !important;


}
.smalway{
	background: none;
}
.form-control{
	box-shadow: none;
	border:1px solid #ccc !Important;
}

.waywy{ margin: 0px !important; }
#onw_rndw_fieldset{
	border-bottom:1px solid #ccc;
}
.backshadow{
	    box-shadow: 0 2px 2px 0 rgba(0,0,0,0.16), 0 0 0 1px rgba(0,0,0,0.08);
	    border:1px solid #ccc;
	    float: left;
	    margin: 20px 0;
	    padding: 10px 10px;
	    background: #cfd4d4; /* Old browsers */
background: -moz-linear-gradient(top, #cfd4d4 0%, #ffffff 100%); /* FF3.6-15 */
background: -webkit-linear-gradient(top, #cfd4d4 0%,#ffffff 100%); /* Chrome10-25,Safari5.1-6 */
background: linear-gradient(to bottom, #cfd4d4 0%,#ffffff 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#cfd4d4', endColorstr='#ffffff',GradientType=0 ); 
}

.backshadow .lablform { padding:0px !important; margin:0px !important; font-size: 13px; }
.tabspl {padding:0px !important;}
</style>

<div class="container">

<div class="backshadow" >
<div>
	<?php echo $this->session->flashdata("msg"); ?>
</div>
     <form autocomplete="off" name="flight_group_request" id="flight_group_request" action="<?php echo base_url()."index.php/general/save_group_request"?>" method="post" class="activeForm oneway_frm" style="">

				<div class="tabspl">
				   <div class="tabrow">
				      <div class="waywy col-xs-12">
				         <div class="clearfix"></div>
				         <div class="smalway col-md-3">
				         	<label class="wament hand-cursor active txtclr"> 
				         <input class="" name="trip_type" id="onew-trp" value="oneway" type="radio"> One Way</label> 
				         <label class="wament hand-cursor txtclr">
				          <input class="" name="trip_type" id="rnd-trp" value="circle" checked="checked" type="radio"> Round Trip</label>
				     </div>
				     <div class="smalway col-md-3">
				     Select Preffered Airline
				     <select class="select2" name="airline_code">
				     <option value="0">Select</option>;
					     <?php foreach($airline_list as $airline_list_k => $airline_list_v) { ?>
								<option value="<?php echo $airline_list_v; ?>">
									<?php echo $airline_list_v; ?>
								</option>;
							<?php }	?>
						</select>
						</div>
				      </div>
				      <div id="onw_rndw_fieldset" class="col-md-12 nopad">
				         <!-- Oneway/Roundway Fileds Starts-->
	         <div class="col-md-12 padfive placerows">
	            <div class="col-xs-3">
	               <div class="col-xs-12 padfive"><div class="lablform txtclr">From</div></div>
		               <div class="col-xs-12 padfive"> <div class="plcetogo deprtures sidebord">
		               		<input autocomplete="off" name="from" class="normalinput auto-focus valid_class fromflight form-control b-r-0 ui-autocomplete-input invalid-ip" id="from" placeholder="Type Departure City" value="" required="" enabled="enabled" type="text"> 
		               		<input class="hide loc_id_holder" name="from_loc_id" value="" enabled="enabled" type="hidden">
		               </div>
	               </div>
	            </div>

	            <div class="col-xs-3">
	                <div class="col-xs-12 padfive"> <div class="lablform txtclr">To</div></div>
	                <div class="col-xs-12 padfive">
		                <div class="plcetogo destinatios sidebord">
		                	<input autocomplete="off" name="to" class="normalinput auto-focus valid_class departflight form-control b-r-0 ui-autocomplete-input invalid-ip" id="to" placeholder="Type Arrival City" value="" required="" enabled="enabled" type="text"> 
		                	<input class="hide loc_id_holder" name="from_loc_id" value="" enabled="enabled" type="hidden">
		                </div>
	                </div>
	            </div>

	             <div class="col-xs-3">
	               <div class="col-xs-12 padfive"> <div class="lablform txtclr">Departure</div></div>
	              <div class="col-xs-12 padfive">  <div class="datemark">
	              <!-- class="plcetogo datemark sidebord"<input readonly="" class="normalinput auto-focus hand-cursor form-control b-r-0 hasDatepicker" id="flight_datepicker1" placeholder="Select Date" value="" name="depature" required="" enabled="enabled" type="text">--></div></div>
	              <input readonly="" type="text" name="depature" id="flight_datepicker1" required="" class="normalinput auto-focus valid_class form-control b-r-0 " placeholder="Select Date" />
	           
	            </div>

	            <div class="col-xs-3">
	                <div class="col-xs-12 padfive"><div class="lablform txtclr">Return</div></div>
	                <div class="col-xs-12 padfive">
		                <div class="datemark">
		                  <input readonly="" type="text" name="return" id="flight_datepicker2" class="normalinput auto-focus valid_class form-control b-r-0 invalid-ip" placeholder="Select Date" />
		                </div>
	                </div>
	            </div>
	           
	         </div>
	           
	          <div class="col-md-12 padfive placerows">
	            <div class="col-xs-2">
	            <div class="roomrow">
	                           <div class="col-xs-12 nopad"><div class="lablform txtclr_n">Adults <span class="agemns">(12+)</span></div></div>
	                           <div class="col-xs-12 nopad">
	                              <div class="input-group pax-count-wrapper adult_count_div">
	                               <!-- <input name="adult" class="normalinput adt auto-focus valid_class form-control b-r-0 invalid-ip" id="adult" placeholder="" required="" type="text"> -->
		                            <select id="adult_num" class="normalinput generic_value form-control chosen_select" name="adult_num" required="" >
										<?php  for($a=1;$a<=30;$a++){
											if($a==10)
												echo "<option value=".$a." selected>".$a."</option>";
											else 
												echo "<option value=".$a.">".$a."</option>";
										} ?>
									</select>
	                              </div>
	                           </div>
	                        </div>
	              </div>

	               <div class="col-xs-2">
	                  <div class="roomrow">
	                           <div class="col-xs-12 nopad"><div class="lablform txtclr_n">Children <span class="agemns">(2-11)</span></div></div>
	                           <div class="col-xs-12 nopad">
	                              <div class="input-group pax-count-wrapper child_count_div">
	                              
	                              <!-- <span class="input-group-btn"><button type="button" class="btn btn-default btn-number" data-type="minus" data-field="child"><span class="glyphicon glyphicon-minus"></span></button></span> <input id="OWT_child" name="child" class="form-control input-number centertext pax_count_value" value="0" min="0" max="9" readonly="" type="text"> <span class="input-group-btn"><button type="button" class="btn btn-default btn-number" data-type="plus" data-field="child"><span class="glyphicon glyphicon-plus"></span></button></span>
	                             
	                               <input name="child" class="normalinput adt form-control b-r-0 " id="child" placeholder="" type="text"> 
	                             -->
	                              <select id="child_num" class="normalinput generic_value form-control chosen_select" name="child_num" required="" >
										<?php  for($c=0;$c<30;$c++){
										echo "<option value=".$c.">".$c."</option>";
										} ?>
									</select>
	                              </div>
	                           </div>
	                        </div>
	                </div>

	                <div class="col-xs-2">
	                           <div class="col-xs-12 nopad"><div class="lablform txtclr_n">Infants <span class="agemns">(0-2)</span></div></div>
	                           <div class="col-xs-12 nopad">
	                              <div class="input-group pax-count-wrapper infant_count_div">
	                              <!-- <span class="input-group-btn"><button type="button" class="btn btn-default btn-number" data-type="minus" data-field="infant"><span class="glyphicon glyphicon-minus"></span></button></span> <input id="OWT_infant" name="infant" class="form-control input-number centertext pax_count_value" value="0" min="0" max="9" readonly="" type="text"> <span class="input-group-btn"><button type="button" class="btn btn-default btn-number" data-type="plus" data-field="infant"><span class="glyphicon glyphicon-plus"></span></button></span>
	                             
	                              <input name="infant" class="normalinput adt form-control b-r-0 " id="infant" placeholder="" type="text"> 
	                             -->
	                              <select id="infant_num" class="normalinput form-control chosen_select" name="infant_num" required="" >
										<?php 
										 for($i=0;$i<30;$i++){
										echo "<option value=".$i.">".$i."</option>";
										} 
										?>
									</select>
	                              </div>
	                           </div>
	                           <span id="error_msg_pax_num" style="display: none;color:red;"></span>
	              </div>
					<div class="col-xs-3 ">

						<span class="lablform txtclr">Class</span>
						
							<select required="" tabindex="6" name="v_class" id="v_class" 
								class="normalinput form-control" aria-required="true">
								<option value="Economy">Economy </option>
								<option value="Business">Business</option>
								
							</select>
						
					</div>
	              <div class="col-xs-3">
	               <div class="lablform txtclr">Expected Fare Per Passenger</div>
	               <div class="clearfix"></div>
	               <div class="col-xs-3 nopad">
	               <input name="curncy" class="normalinput padL5 form-control b-r-0 " placeholder="" required="" value="<?=$currency?>" type="text" readonly="">

	               </div>
	               <div class="col-xs-9 nopad">
	               <div class="plcetogo  sidebord1">
	               <input name="fare" class="normalinput passenger_fare  form-control b-r-0 " id="fare" placeholder="" required="" type="text" maxlength="12"></div>
	            	</div>
	            	<span id="error_msg_fare_pre_num" style="display: none;color:red;"></span>
	            </div>
	      </div>
				      </div>

                       <div class="col-md-12 padfive placerows">
		                       	<div class="col-md-3">
					               <div class="lablform txtclr1">Requester name</div>
					               <div class="plcetogo  sidebord1">
					                <input name="name" maxlength="100" class="normalinput  form-control b-r-0 " id="name" placeholder="" required="" type="text"></div>
					            	<span id="error_msg_request_name" style="display: none;color:red;"></span>
					            </div>
				               <div class="col-md-3">
				               <div class="lablform txtclr1">Contact Number</div>
				               <div class="plcetogo  sidebord1">
				               <input name="contact" class="normalinput  form-control b-r-0 " id="contact" placeholder="" required="" type="text" maxlength="15"> 
				            
				            <span id="error_msg_mobile" style="display: none;color:red;"></span></div>
				            
				            </div>
				               <div class="col-md-3">
				               <div class="lablform txtclr1">Email ID</div>
				               <div class="plcetogo  sidebord1">
				               <input  name="email_id" class="normalinput  form-control b-r-0 " id="email_id" placeholder="" value="" required="required" type="text" maxlength="150"> 
				               <input class="hide loc_id_holder" name="from_loc_id" value="" enabled="enabled" type="hidden"></div>
				            <span id="error_msg_eemail" style="display: none;color:red;"></span></div>
				           
				              
				            
				             <div class="col-md-3 pull-right">
				               <div class="lablform txtclr1">Remarks</div>
				               <div style="width:100%;">
					               <textarea  style="width:100%;height:40px;" class="form-control" name="remarks" maxlength="200"
												 tabindex="7" id="remarks">
									</textarea>
								</div>
				            </div>
				            <div class="col-md-3 pull-right">
				               <div class="lablform txtclr1">&nbsp;</div>
				               <div class="searchsbmtfot"><input name="search" id="search" class="searchsbmt flight_search_btn" value="submit" type="submit"></div>
				            </div>
 						</div>
				    </div>
				    


				      <div class="clearfix"></div>
				 
				   </div>
				</div>
</form>
	
</div></div>
				
<!-- <link rel="stylesheet" type="text/css" href="http://192.168.0.46/chariot/extras/system/template_list/template_v1/javascript/page_resource/jquery.datetimepicker.css"> -->


<script>
$(document).ready(function(){
    


	$('[type="submit"]').on('click', function(event) {
    	
    	var adult = $('#adult_num').val();
    
    	var child   = $('#child_num').val();
    	var infant  = $('#infant_num').val();
	 	var contact = $('#contact').val();
		var email   = $('#email_id').val(); 
		var fare_pre_num = $('#fare').val(); 
		var request_name = $('#name').val(); 
		
		//var fname = $('#first_name').val();
	    var total_pax = parseInt(adult)+parseInt(child)+parseInt(infant);
	
	   if(total_pax<10){
		    $('#error_msg_pax_num').show();
			$('#error_msg_pax_num').text("adult + child count should be greater than 10");
			return false;
	   }else{
			$('#error_msg_pax_num').hide();
		}

	   if(fare_pre_num=='')
		{
			$('#error_msg_fare_pre_num').show();
			$('#error_msg_fare_pre_num').text("Please enter expected fare.");
			 return false;
		
		}else{
			$('#error_msg_fare_pre_num').hide();
		}
	   /*if(isNaN(fare_pre_num)){
		   $('#error_msg_fare_pre_num').show();
			$('#error_msg_fare_pre_num').text("Please enter number only.");
			 return false;
	   }else{
			$('#error_msg_fare_pre_num').hide();
		}*/

	   if(request_name=='')
		{
			$('#error_msg_request_name').show();
			$('#error_msg_request_name').text("Please enter requested name.");
			 return false;
		
		}else{
			$('#error_msg_request_name').hide();
		}
	   
		
	   if(contact=='')
 		{
 			$('#error_msg_mobile').show();
 			$('#error_msg_mobile').text("Please enter mobile number.");
 			 return false;
 		
 		}else{
 			$('#error_msg_mobile').hide();
 		}
	   if(isNaN(contact)){
		   $('#error_msg_mobile').show();
			$('#error_msg_mobile').text("Please enter number only.");
			 return false;
	   }else{
			$('#error_msg_mobile').hide();
		}

		if(contact.lenght<15)
		{
 			$('#error_msg_mobile').show();
 			$('#error_msg_mobile').text("Mobile number should be 15 digit.");
 			 return false;
 		
 		}else{
 			$('#error_msg_mobile').hide();
 		}
			
 		if(email=='')
 		{
 			$('#error_msg_eemail').show();
 			$('#error_msg_eemail').text("Please enter email.");
 			return false;
 		}else{
 			$('#error_msg_eemail').hide();
 		}
 

            
        });

    
});
$(document).ready(function(){
	$(".select2").select2();
	$('.passenger_fare').blur(function(){
		var base = '<?=base_url();?>';
		  var val = $(this).val();
		  $.post( base+"index.php/general/number_foramt", { value: val })
			  .done(function( data ) {
			    $('.passenger_fare').val(data);
		  });
	});
});

</script>

