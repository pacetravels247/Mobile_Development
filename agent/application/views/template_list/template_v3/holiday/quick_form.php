<div class="container"><div class="quick_form">
	<h2>Quick enquiry form</h2>
	<form action="<?php echo base_url();?>index.php/tours/send_custom_enquiry" method="post" id="send_booking">
	<div class="qfagnt_detail">
		<span>Agent Id:	<input type="text" class="form-control" value="<?=$agent_id?>" readonly name="agent_id"></span><br>
		<span>Agent Name: 	<input type="text" class="form-control" value="<?=$agent_name?>" readonly name="agent_name"></span>
	</div>
	<!--  -->
	
	<div class="row">
		<div class="qf_heading">Where you want to travel</div>
		<div class="col-sm-6">
			<div class="custom-control"> 
			 <input type="radio"  id="customRadio1" value="domestic" name="travel_type" checked="">
			 <label class="" for="customRadio1">Domestic</label> 
			  </div>
		</div>
		<div class="col-sm-6">
			<div class="custom-control"> 
			<input type="radio"  id="customRadio2" value="international" name="travel_type" checked="">
			 <label class="" for="customRadio1">International</label> 
			  </div>
		</div>
	</div>
		<!--  -->
		<div class="row destination_block">
			<div class="qf_heading">Where you want to travel</div>
			<div class="col-sm-12">
				<div class="col-sm-4">
				<label class="control-label">Destination </label></div>
				<div class="col-sm-6">
				<select class="form-control" name="destination[]" required="required">
				<option>Select Destination</option>
				<?php 
					foreach($country_list as $country_key => $country_val){
				?>
						<option value="<?=$country_key?>"><?=$country_val?></option>
				<?php
					}
				?>
				</select>
				</div>
				<div class="col-sm-2">
				<a href="#" class="text-info add_button"><i class="fa fa-plus-circle"></i></a>
				</div>
			</div>
			
		</div>
		<!--  -->
		<div class="row">
			<div class="qf_heading">Departure city</div>
			<div class="col-sm-12">
				<div class="col-sm-4">
				<label class="control-label">city name</label></div>
				<div class="col-sm-6">
				<select class="form-control" name="departure_city">
					<option>Select Departure City</option>
					<?php 
						foreach($city_list as $city_key => $city_val){
					?>
							<option value="<?=$city_key?>"><?=$city_val?></option>
					<?php
						}
					?>
				</select>
				</div>
			</div>
		</div>
		<!--  -->
		<div class="row">
			<div class="qf_heading">When you want to travel</div>
			<!-- <div class="col-sm-12">
				<div class="col-sm-4">
				<label class="control-label">Travel Month</label></div>
				<div class="col-sm-6">
				<input type="text" name="tr_mnth" class="form-control"></div>
			</div> -->
			<div class="col-sm-12">
				<div class="col-sm-4">
				<label class="control-label">From Date</label>
			</div>
			<div class="col-sm-6">
				<input type="text" name="fr_date" id="datepicker1" class="form-control"></div>
			</div>
			<div class="col-sm-12">
				<div class="col-sm-4">
				<label class="control-label">To Date</label></div>
				<div class="col-sm-6">
				<input type="text" name="to_date" id="datepicker2" class="form-control">
			</div>
			</div>
		</div>
		<!--  -->
		<div class="row">
			<div class="qf_heading">Number of nights</div>
			<div class="col-sm-12">
				<div class="col-sm-4">
				<label class="control-label">nights</label></div>
				<div class="col-sm-6">
				<input type="text" name="night" class="form-control"></div>
			</div>
		</div>
		<!--  -->
		<div class="row">
			<div class="qf_heading">Number of travellers</div>
			<div class="col-sm-12">
				<div class="col-sm-4">
				<label class="control-label">Adults</label></div>
				<div class="col-sm-6">
				<input type="text" name="adult" class="form-control"></div>
			</div>
			<div class="col-sm-12">
				<div class="col-sm-4">
				<label class="control-label">Children</label></div>
				<div class="col-sm-6">
				<input type="text" name="child" class="form-control">
			</div>
			</div>
			<div class="col-sm-12">
				<div class="col-sm-4">
				<label class="control-label">infant</label></div>
				<div class="col-sm-6">
				<input type="text" name="infant" class="form-control"></div>
			</div>
		</div>
		<!--  -->
		<div class="row">
			<div class="qf_heading">Any special requests</div>
			<div class="col-sm-12">
				<div class="col-sm-4">
				<label class="control-label">Request</label></div>
				<div class="col-sm-6">
				<textarea class="form-control" rows="3" name="remarks"></textarea></div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<button type="submit" class="qk_enquiry_btn">Submit request</button>
			</div>
		</div>
	</form>
	</div>
</div>
<style type="text/css">
	.qk_enquiry_btn {
    background: #d9534f;
    border: none;
    padding: 10px 25px;
    font-size: 16px;
    color: #fff;
    width: 500px;
    margin: 20px auto;
    text-align: center;
    display: block;
}
	.quick_form h2 {
    text-align: center;
    text-transform: uppercase;
    background: linear-gradient(0deg,#002042,#0a8ec1);
    color: #fff;
    padding: 15px;
}
.qf_heading {
    text-align: center;
    text-transform: uppercase;
    font-size: 16px;
    padding: 5px 0px;
    background: #f1f1f1;
    width: 100%;
    display: block;
    margin: 15px auto;
}
.quick_form {
    border: 1px solid #c3c3c3;
    padding: 20px;
    box-shadow: 2px 4px 8px #ccc;
    width: 800px;
    margin: 20px auto 0px;
}
.custom-control {
    text-align: center;
}
.quick_form .col-sm-12 {
    margin-bottom: 10px;
}
.add_button {
    font-size: 30px;
    line-height: 32px;
}
.minus_button {
    font-size: 30px;
    line-height: 32px;
}
.quick_form label.control-label {
    color: #0a8ec1;
    font-weight: 600;
    font-size: 15px;
    display: block;
    text-align: left;
    text-transform: uppercase;
}
.qfagnt_detail {
    display: flex;
    width: 50%;
    margin: 0 50%;
}
.qfagnt_detail span {
    flex: 1 1 0;
    width: 0;
    margin: 5px 2px;
}
</style>
 <script>
 $( document ).ready(function() {
    $( "#datepicker1" ).datepicker();
    $( "#datepicker2" ).datepicker();
	$(document).on('click','.add_button',function(){
		var gen_text="<div class='col-sm-12 dest_country'><div class='col-sm-4'><label class='control-label'>Destination</label></div><div class='col-sm-6'><select required='required' class='form-control' name='destination[]'><option>Select Destination</option><?php foreach($country_list as $country_key => $country_val){?><option value='<?=$country_key?>'><?=$country_val?></option><?php }?></select></div><div class='col-sm-2'><a href='#' class='text-danger minus_button'><i class='fa fa-minus-circle'></i></a></div></div>";
		
		$('.destination_block').append(gen_text);
	});
	$(document).on('click','.minus_button',function(){
		$(this).parents('.dest_country').remove();
	});
  } );
  </script>