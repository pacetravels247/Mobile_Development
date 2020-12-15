<style>
  .ui-autocomplete-category {
    font-weight: bold;
    padding: .2em .4em;
    margin: .8em 0 .2em;
    line-height: 1.5;
  }
  </style>

<!-- holiday packages -->
<!-- <form action="<?php echo base_url().'index.php/tours/search'?>"
	autocomplete="off" id="holiday_search">
	<div class="tabspl forhotelonly">
		<div class="tabrow">
			<div class="col-md-3 col-sm-3 col-xs-6 padfive full_smal_tab">
				<div class="lablform">Country</div>
				<div class="selectedwrap sidebord">
					<select class="normalsel holyday_selct" id="country" name="country">
						<option value="">All</option>
						<?php if(!empty($holiday_data['countries'])){?>
						<?php foreach ($holiday_data['countries'] as $country) { ?>
						<option value="<?php echo $country->country_id; ?>"
							<?php if(isset($scountry)){ if($scountry == $country->country_id) echo "selected"; }?>><?php echo $country->country_name; ?>
						</option>
						<?php } } ?>
					</select>
				</div>
			</div>
			<div class="col-md-3 col-sm-3 col-xs-6 padfive full_smal_tab">
				<div class="lablform">Package Type</div>
				<div class="selectedwrap sidebord">
					<select class="normalsel holyday_selct" id="package_type"
						name="package_type">
						<option value="">All Package Types</option>
						<?php if(!empty($holiday_data['package_types'])){ ?>
						<?php foreach ($holiday_data['package_types'] as $package_type) { ?>
						<option value="<?php echo $package_type->package_types_id; ?>"
							<?php if(isset($spackage_type)){ if($spackage_type == $package_type->package_types_id) echo "selected"; } ?>><?php echo $package_type->package_types_name; ?></option>
						<?php } ?>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="col-md-2 col-sm-2 col-xs-4 padfive full_smal_tab">
				<div class="lablform">Duration</div>
				<div class="selectedwrap sidebord">
					<select class="normalsel holyday_selct" id="duration"
						name="duration">
						<option value="">All Durations</option>
						<option value="1-3"
							<?php if(isset($sduration)){ if($sduration == '1-3') echo "selected"; } ?>>1-3</option>
						<option value="4-7"
							<?php if(isset($sduration)){ if($sduration == '4-7') echo "selected"; } ?>>4-7</option>
						<option value="8-12"
							<?php if(isset($sduration)){ if($sduration == '8-12') echo "selected"; } ?>>8-12</option>
						<option value="12"
							<?php if(isset($sduration)){ if($sduration == '12') echo "selected"; } ?>>12+</option>
					</select>
				</div>
			</div>
			<div class="col-md-2 col-sm-2 col-xs-4 padfive full_smal_tab">
				<div class="lablform">Budget</div>
				<div class="selectedwrap sidebord">
					<select class="normalsel holyday_selct" id="budget" name="budget">
						<option value="">All</option>
						<option value="100-500"
							<?php if(isset($sbudget)){ if($sbudget == '100-500') echo "selected"; } ?>><?php echo $currency_obj->get_currency_symbol($currency_obj->to_currency); ?> <?php echo get_converted_currency_value ( $currency_obj->force_currency_conversion ( '100' ) );?>-<?php echo get_converted_currency_value ( $currency_obj->force_currency_conversion ( '500' ) );?></option>
						<option value="500-1000"
							<?php if(isset($sbudget)){ if($sbudget == '500-1000') echo "selected"; } ?>><?php echo $currency_obj->get_currency_symbol($currency_obj->to_currency); ?> <?php echo get_converted_currency_value ( $currency_obj->force_currency_conversion ( '500' ) );?>-<?php echo get_converted_currency_value ( $currency_obj->force_currency_conversion ( '1000' ) );?></option>
						<option value="1000-5000"
							<?php if(isset($sbudget)){ if($sbudget == '1000-5000') echo "selected"; } ?>><?php echo $currency_obj->get_currency_symbol($currency_obj->to_currency); ?> <?php echo get_converted_currency_value ( $currency_obj->force_currency_conversion ( '1000' ) );?>-<?php echo get_converted_currency_value ( $currency_obj->force_currency_conversion ( '5000' ) );?></option>
						<option value="5000"
							<?php if(isset($sbudget)){ if($sbudget == '5000') echo "selected"; } ?>><?php echo $currency_obj->get_currency_symbol($currency_obj->to_currency); ?> <?php echo get_converted_currency_value ( $currency_obj->force_currency_conversion ( '5000' ) );?> <?php echo '> '?></option>
					</select>
				</div>
			</div>
			<div class="col-md-2 col-sm-2 col-xs-4 padfive full_smal_tab">
				<div class="lablform">&nbsp;</div>
				<div class="searchsbmtfot">
					<input type="submit" class="searchsbmt" value="search" />
				</div>
			</div>
		</div>
	</div>
</form> -->
<div class="hldyblak">
<form action="<?php echo base_url();?>index.php/tours/holiday_package_listt" method="post" id="holiday_search">
	<div class="col-md-8 col-md-offset-2 holiday_srch_input">
		<div class="form-group">
			<input type="text" id="af_tag" name="search_type" class=" form-control" placeholder="Search City,Country,Place">
		</div>
		<div class="search_but">
			<input type="submit" class="srch_butt" value="search" />
		</div>
	</div>
</form>
</div>

<div class="id-floating-btn">
	<a href="https://agent.pacetravels.net/index.php/general/quick_form"><button class="btn"> <i class="fa fa-comments" aria-hidden="true"></i> &nbsp; Quick Enquiry</button></a>
</div>

<div class="modal fade" id="id-quick-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Package Enquiry</h4>
          </div>
          <div class="modal-body">
            <div class="container-fluid id-enquiry-modal nopad">
              <div class="row id-body-div">
                  <div class="col-sm-4">
                    <label class="id-label">Package Code</label><p><strong>#PTSI014</strong></p>
                  </div>
                  <div class="col-sm-4">
                    <label class="id-label">Agent Name</label><p>Ismail Dadwad</p>
                  </div>
                  <div class="col-sm-4">
                    <label class="id-label">Email</label><p>ismail@pacetravels.in</p>
                  </div>
                  
              </div>
              <div class="row">
                  <div class="col-sm-6 padfive">
                    <label class="id-label">No. of Passenger</label>
                    <input type="number"  name="passenger" class="form-control" placeholder="Enter No Of Guests" required>
                  </div>
                  <div class="col-sm-6 padfive">
                    <label class="id-label">Departure Date</label>
                    <select name="" class="form-control">
                      <option value=""></option>
                  </select>
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
          </div>
          </div>
        </div>
      </div>

<?php
$controller_name = $GLOBALS["CI"]->uri->segment(1);
echo "<script>var load_select2 = true;</script>";
if(@$_GET["default_view"] == META_PACKAGE_COURSE || $controller_name == "tours")
{
 ?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
   
  $(document).ready(function(){
	$.widget( "custom.catcomplete", $.ui.autocomplete, {
      _create: function() {
        this._super();
        this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
      },
      _renderMenu: function( ul, items ) {
        var that = this,
          currentCategory = "";
        $.each( items, function( index, item ) {
          var li;
          if ( item.category != currentCategory ) {
            ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
            currentCategory = item.category;
          }
          li = that._renderItemData( ul, item );
          if ( item.category ) {
            li.attr( "aria-label", item.category + " : " + item.label );
          }
        });
      }
    });
	
	$(document).on('keyup','#af_tag',function(e){
		var search_val=$(this).val();
		//alert(search_val);
		$.ajax({
			
           type: "GET",
            url: "<?php echo base_url().'index.php/tours/get_holiday_package_auto_fill/' ?>",
           data: {search_val:search_val},
           success: function(result){
				
				var availableTags = JSON.parse(result);
				
				$( "#af_tag" ).catcomplete({
					source: availableTags
				});
            }
        });
		
	});
	
	$(document).on('click','.ui-menu-item',function(e){
		var sel_value=$(this).attr('aria-label');
		
		$.ajax({
			type: "POST",
            url: "<?php echo base_url().'index.php/tours/holiday_package_listt/' ?>",
			data: {sel_value:sel_value},
			success: function(result){
				//alert(result);
				window.location.href = result;
            }
        });
	});
	$(document).on('keyup','.ui-menu-item',function(e){
		alert("FSd")
	});
	
	$(document).on('keyup','.af_tag',function(e){
		var sel_value=$(this).val();
		
		if (event.keyCode === 13) {
			alert(sel_value);
		   event.preventDefault();
		   $( ".ui-menu-item" ).trigger( "click" );
		}
	});
	
	
	
	
	
	
  });
</script>
<?php } ?>

<style type="text/css">
	.id-floating-btn{
		position: fixed;
	  /*width: 100px;*/
	  /*height: 100px;*/
	  bottom: 0;
	  right: 0;
	  margin: 15px 25px;
	  z-index: 1000;
	}
	.id-floating-btn button{
		font-size: 18px;
		padding: 8px 20px;
		background: linear-gradient(96deg,#002042,#0a8ec1);
		color: #fff!important;
		border:none;
		/*box-shadow: 0 8px 17px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19) !important;*/
		box-shadow: 0 8px 17px 0 rgba(0,0,0,0.8),0 6px 20px 0 rgba(0,0,0,0.50) !important;
		cursor: pointer;
		border-radius: 25px;
	}
	.id-floating-btn button:hover{
		background: linear-gradient(96deg,#0a8ec1,#002042);
	}
</style>