<div class="container-fluid nopad">
    <?php
    $img_url = '';
    if($search_dest_img==''){
      $img_url = "https://www.flydubai.com/en/media/Flight-resumption-todubai-2560x960_tcm8-160498.jpg";
    }else{
      $img_url = $GLOBALS['CI']->template->domain_images($search_dest_img);
    }
  ?>

  <div class="img-back" style='background-image: url("<?php echo $img_url; ?>");'>
  <h1><?=$search_dest?></h1>
  </div>

</div>

<div class="container id-mt-3 padfive">
	<?php echo $this->session->flashdata("msg"); ?>
  <div class="col-md-12">
    <div class="row">
      <!-- <div class="col-sm-3 id-p-filter">
        <h4>Filter</h4>
        <div class="row">
          <div class="form-check">
            <label class="form-check-label" for="radio1">
              <input type="radio" class="form-check-input" id="radio1" name="optradio" value="option1" checked> &nbsp; All 
            </label>
          </div>
          <div class="form-check">
            <label class="form-check-label" for="radio2">
              <input type="radio" class="form-check-input" id="radio2" name="optradio" value="option2"> &nbsp; Group
            </label>
          </div>
          <div class="form-check">
            <label class="form-check-label">
              <input type="radio" class="form-check-input" id="radio2" name="optradio" value="option2"> &nbsp; Customize
            </label>
          </div>
        </div>
        <div class="row">
          <select class="form-control">
            <option>Sort By</option>
            <option>Price High to Low</option>
            <option>Price Low to High</option>
            <option>Duration Short to Long</option>
            <option>Duration Long to Short</option>
          </select>
        </div>
      </div> -->

      <div class="col-sm-12">
        <!-- <div class="row id-searched">
          <h3>You Searched <span>Dubai</span> Packages</h3>
          <p>Showing 1 - 10 packages</p>
        </div> -->
        <div class="row id-p-filter">
          <div class="col-sm-4">
            <div class="row">
              <div class="col-sm-3">
                <label class="form-check-label" for="radio1">
                <input type="radio" class="form-check-input trip_cat_fil" id="radio1" name="optradio" value="0" checked> &nbsp; All 
              </label>
              </div>
              <div class="col-sm-4">
                <label class="form-check-label" for="radio2">
                  <input type="radio" class="form-check-input trip_cat_fil" id="radio2" name="optradio" value="group"> &nbsp; Group
                </label>
              </div>
              <div class="col-sm-5">
                <label class="form-check-label">
                  <input type="radio" class="form-check-input trip_cat_fil" id="radio2" name="optradio" value="fit"> &nbsp; Customize
                </label>
              </div>
            </div>
          </div>
          <div class="col-sm-offset-5 col-sm-3">
            <select class="sort_by">
              <option value="">Sort By</option>
              <option value="p_h_l">Price High to Low</option>
              <option value="p_l_h">Price Low to High</option> 
              <option value="d_s_l">Duration Short to Long</option>
              <option value="d_l_s">Duration Long to Short</option>
            </select>
          </div>
        </div>
		<div class="all_result">
		<?php 
		//debug($package_list); 
		if(!empty($package_list)){
		
			foreach($package_list as $pack_key => $pack_val){
				
				if($pack_val['pack_type']=='fit'){
					$pack_type='Customize';
				}else{
					$pack_type='Group';
				}
				$inclusions= json_decode($pack_val['inclusions_checks']);
				$country_count=explode(',',$pack_val['tours_country']);
				$country_count=count($country_count);
				$city_count=explode(',',$pack_val['tours_city']);
				$city_count=count($city_count);
			
				$countries=array();
				foreach($pack_val['country_name'] as $c_val){
				   $countries[]=$c_val['name'];
				} 
				
				$cities=array();
				foreach($pack_val['city_name'] as $c_val){
				   $cities[]=$c_val['CityName'];
				} 
				$page_data['city']= implode(', ',$cities);
				$page_data['country']= implode(', ',$countries); 
		?>
        <div class="row id-p-search-row each_package sort_sight_divs" data-sort="<?=$pack_val['netprice_price']+$pack_val['markup']?>" data-sort_duration="<?=$pack_val['duration']?>">
          <div class="col-sm-3 nopad">
            <div class="">
              <img class="id-package-search-img" width="100%" src="<?php echo $GLOBALS['CI']->template->domain_images($pack_val['banner_image'])?>" alt="image" >
            </div>
          </div>
		  <input type="hidden" value="<?=$pack_val['pack_type']?>" class="tour_cat">
          <div class="col-sm-9 nopad">
            <div class="row">
              <div class="col-sm-8 nopad">
                <div class="id-div1">
                  <p class="id-p"><?=$pack_val['package_name']?></p>
                  <p class="id-sub-p"><i class="fa fa-map-marker x2" aria-hidden="true"></i> &nbsp; City : <?=$page_data['city']?><br><i class="fa fa-globe x2" aria-hidden="true"></i> &nbsp; Country : <?=$page_data['country']?></p>
				 
                    <div class="pack_details">
                      <div>
                        <span><i class="fa fa-globe" aria-hidden="true"></i>&nbsp; <?=$country_count?><span>Country</span>
                        </span>&nbsp;
                        <span><i class="fa fa-home" aria-hidden="true"></i>&nbsp; <?=$city_count?><span>Cities</span></span>&nbsp;
                        <span><i class="fa fa-moon" aria-hidden="true"></i>&nbsp; <?=$pack_val['duration']?><span>Nights</span>
                        </span>
                      </div>
                    </div>
                    
                  </div>
                  <p class="id-p-group">Type: <?=$pack_type?></p>
                    <div class="row id-itenirary-div">
                      <div class="col-sm-2 nopad">
                        <p class="p">Inclusions :</p>
                      </div>
                      <div class="col-sm-10 nopad">
                        <marquee>
                          <p class=""> 
							<?php 
								foreach($inclusions as $inc_val){ 
									if($inclusions[0]!=$inc_val){
										echo '&nbsp;&nbsp; | &nbsp;&nbsp;';
									}
									echo $inc_val ;
								}
							?>
						</p>
                        </marquee>
                      </div>
                    </div>
              </div>
              <div class="col-sm-4 nopad ">
                 <div class="id-p-div-row">
                  <p>Price per adult</p>
                    <h3><del>₹ <?=number_format($pack_val['market_price'],0)?></del> &nbsp; &nbsp;<span>₹ <?=number_format($pack_val['netprice_price']+$pack_val['markup'],0)?></span></h3>
                     <div class="pck_butns">
                        <a href="<?php echo base_url().'index.php/tours/holiday_package_detail/'.$pack_val['pack_id']?>/<?=$heading?>" class="btn btn-default">View More</a>
                        <a class="btn btn-danger" data-toggle="modal" data-target="#enquiry_form_<?=$pack_val['pack_id']?>">Quick Enquiry</a>
                     </div>
                 </div>
              </div>
            </div>
          </div>
        </div>
		<!-- Modal -->
      <div class="modal fade" id="enquiry_form_<?=$pack_val['pack_id']?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Package Enquiry</h4>
          </div>
          <div class="modal-body">
          <form action="<?php echo base_url();?>index.php/tours/send_enquiry/<?=$heading?>" method="post" >
             <div class="container-fluid id-enquiry-modal nopad">
				<input type="hidden"  name="pack_id"  class="pack_id" value="<?=$pack_val['pack_id']?>">
				<input type="hidden"  name="pack_name" class="pack_name"  value="<?=$pack_val['package_name']?>">
				<input type="hidden"  name="agent_id" class="agent_id"  value="<?=$user_id?>">
				<div class="form-group hide">
					<label>Package Code</label>
					<input type="text"  name="pack_code" class="form-control pack_code" value="<?=$pack_val['tour_code']?>" placeholder="Enter Name" maxlength="30" required readonly>
				</div>
				<div class="form-group hide">
					<label>Name</label>
					<input type="text"  name="name" class="form-control name" value="<?=$user_name?>" placeholder="Enter Name" maxlength="30" required readonly>
				</div>
				<div class="form-group hide">
					<label>Email</label>
					<input type="Email"  name="Email" class="form-control Email" value="<?=$user_email?>" placeholder="Enter Email" maxlength="45" required readonly>
				</div>
				<div class="form-group hide">
					<label>Phone</label>
					<input type="text"  name="phone" class="form-control phone" value="<?=$user_phone?>" placeholder="Enter Phone" maxlength="12" required readonly>
				</div>
				<div class="row id-body-div">
                  <div class="col-sm-4">
                    <label class="id-label">Package Code</label><p><strong class="pack_id_text"><?=$pack_val['tour_code']?></strong></p>
                  </div>
                  <div class="col-sm-4">
                    <label class="id-label">Agent Name</label><p><?=$user_name?></p>
                  </div>
                  <div class="col-sm-4">
                    <label class="id-label">Email</label><p><?=$user_email?></p>
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
                    <input  id="datepicker_dat_groupd" type="date" name="dep_date" class="form-control id-inputfield datepicker_dat_group" value="dd/mm/yyyy" required>
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
		<?php } }else{
			?>
			<div class="row id-p-search-row each_package">
         
				<div class="col-sm-9 nopad">
					<div class="row">
						<p>No result found for this destination .</p>
					</div>
				</div>
			</div>
			
		<?php	
		}?>
       
        </div>
        
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$(document).on('change','.trip_cat_fil',function(){
			var tour_cat=$(this).val();
			package_filter(tour_cat);
		});
		
		$(document).on('change','.sort_by',function(){
			
			var sort_val=$(this).val();
			
			if(sort_val=='p_l_h'){
				
				var result = $('.sort_sight_divs').sort(function (a, b) {
					var contentA =parseInt( $(a).data('sort'));
					var contentB =parseInt( $(b).data('sort'));
					return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
				});
			}else if(sort_val=='p_h_l'){
				var result = $('.sort_sight_divs').sort(function (a, b) {
					var contentA =parseInt( $(a).data('sort'));
					var contentB =parseInt( $(b).data('sort'));
					return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
				});
			}else if(sort_val=='d_s_l'){
				var result = $('.sort_sight_divs').sort(function (a, b) {
					var contentA =parseInt( $(a).data('sort_duration'));
					var contentB =parseInt( $(b).data('sort_duration'));
					return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
				});
			}else if(sort_val=='d_l_s'){
				var result = $('.sort_sight_divs').sort(function (a, b) {
					var contentA =parseInt( $(a).data('sort_duration'));
					var contentB =parseInt( $(b).data('sort_duration'));
					return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
				});
			}
			$('.all_result').html(result);
		});
		function package_filter(tour_cat){
			//alert(tour_type);alert(tour_cat);
			$('.each_package').each(function() {
				
				var pack_tour_cat=$(this).find('.tour_cat').val();
				
				
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
				if(cat_flag==1){
					$(this).removeClass('hide');
				}else{
					$(this).addClass('hide');
				}
			});
		}
	});
</script>
<!-- <div class="container-fluid">
   <div class="col-md-12 package_list nopad">
      <div class="col-md-2 pck_image ht nopad">
         <div>
            <img src="http://pacetravels.org/extras/custom/TMX1512291534825461/images/5eea020495.jpg" alt="image" >
         </div>
      </div>
      <div class="col-md-7 pack_section ht">
         <div class="col-sm-12 brdr_bot">
            <div class="col-sm-8 pl_name">
               <h4>Easy Dubai flexi flights</h4>
            </div>
            <div class="col-sm-4 pl_dur">
               <h4>5 Nights 4 days</h4>
            </div>
         </div>
         <div class="col-sm-12 brdr_bot pad5">--Dubai--</div>
         <div class="col-sm-12">
            <ul class="list-inline pck_facility">
               <li>
                  <a href="#">
                     <i class="fa fa-bed"></i>
                     <p>Hotel</p>
                  </a>
               </li>
               <li>
                  <a href="#">
                     <i class="fa fa-bed"></i>
                     <p>Hotel</p>
                  </a>
               </li>
               <li>
                  <a href="#">
                     <i class="fa fa-bed"></i>
                     <p>Hotel</p>
                  </a>
               </li>
               <li>
                  <a href="#">
                     <i class="fa fa-bed"></i>
                     <p>Hotel</p>
                  </a>
               </li>
               <li>
                  <a href="#">
                     <i class="fa fa-bed"></i>
                     <p>Hotel</p>
                  </a>
               </li>
            </ul>
         </div>
      </div>
      <div class="col-md-3 pack_price_section ht">
         <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#tt">Tour Type</a></li>
            <li><a data-toggle="tab" href="#std">Standard</a></li>
         </ul>
         <div class="tab-content pck_tab">
            <div id="tt" class="tab-pane text-center fade in active">
               <h3>₹ 40,000</h3>
               <p>Price per adult</p>
               <div class="pck_butns">
                 <a href="#" class="btn btn-default">Wait us to call you</a>
                  <a href="#" class="btn btn-danger">Block</a>
               </div>
            </div>
            <div id="std" class="tab-pane text-center fade">
               <h3>₹ 40,000</h3>
               <p>Price per adult</p>
               <div class="pck_butns">
                  <a href="#" class="btn btn-default">Wait us to call you</a>
                <a href="#" class="btn btn-danger">Block</a>
               </div>
            </div>
         </div>
      </div>
   </div>



   <div class="col-md-12 package_list nopad">
      <div class="col-md-2 pck_image ht nopad">
         <div>
            <img src="http://pacetravels.org/extras/custom/TMX1512291534825461/images/5eea020495.jpg" alt="image" >
            <p><input type="checkbox" name="">compare</p>
         </div>
      </div>
      <div class="col-md-7 pack_section ht">
         <div class="col-sm-12 brdr_bot">
            <div class="col-sm-8 pl_name">
               <h4>Easy Dubai flexi flights</h4>
            </div>
            <div class="col-sm-4 pl_dur">
               <h4>5 Nights 4 days</h4>
            </div>
         </div>
         <div class="col-sm-12 brdr_bot pad5">--Dubai--</div>
         <div class="col-sm-12">
            <ul class="list-inline pck_facility">
               <li>
                  <a href="#">
                     <i class="fa fa-bed"></i>
                     <p>Hotel</p>
                  </a>
               </li>
               <li>
                  <a href="#">
                     <i class="fa fa-bed"></i>
                     <p>Hotel</p>
                  </a>
               </li>
               <li>
                  <a href="#">
                     <i class="fa fa-bed"></i>
                     <p>Hotel</p>
                  </a>
               </li>
               <li>
                  <a href="#">
                     <i class="fa fa-bed"></i>
                     <p>Hotel</p>
                  </a>
               </li>
               <li>
                  <a href="#">
                     <i class="fa fa-bed"></i>
                     <p>Hotel</p>
                  </a>
               </li>
            </ul>
         </div>
      </div>
      <div class="col-md-3 pack_price_section ht">
         <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#tt">Tour Type</a></li>
            <li><a data-toggle="tab" href="#std">Standard</a></li>
         </ul>
         <div class="tab-content pck_tab">
            <div id="tt" class="tab-pane text-center fade in active">
               <h3>₹ 40,000</h3>
               <p>Price per adult</p>
              <div class="pck_butns">
                  <a href="#" class="btn btn-default">Wait us to call you</a>
                  <a href="#" class="btn btn-danger">Block</a>
               </div>
            </div>
            <div id="std" class="tab-pane text-center fade">
               <h3>₹ 40,000</h3>
               <p>Price per adult</p>
               <div class="pck_butns">
                  <a href="#" class="btn btn-default">Wait us to call you</a>
                  <a href="#" class="btn btn-danger">Block</a>
              </div>
            </div>
         </div>
      </div>
   </div>

   <div class="col-md-12 package_list nopad">
      <div class="col-md-2 pck_image ht nopad">
         <div>
            <img src="http://pacetravels.org/extras/custom/TMX1512291534825461/images/5eea020495.jpg" alt="image" >
            <p><input type="checkbox" name="">compare</p>
         </div>
      </div>
      <div class="col-md-7 pack_section ht">
         <div class="col-sm-12 brdr_bot">
            <div class="col-sm-8 pl_name">
               <h4>Easy Dubai flexi flights</h4>
            </div>
            <div class="col-sm-4 pl_dur">
               <h4>5 Nights 4 days</h4>
            </div>
         </div>
         <div class="col-sm-12 brdr_bot pad5">--Dubai--</div>
         <div class="col-sm-12">
            <ul class="list-inline pck_facility">
               <li>
                  <a href="#">
                     <i class="fa fa-bed"></i>
                     <p>Hotel</p>
                  </a>
               </li>
               <li>
                  <a href="#">
                     <i class="fa fa-bed"></i>
                     <p>Hotel</p>
                  </a>
               </li>
               <li>
                  <a href="#">
                     <i class="fa fa-bed"></i>
                     <p>Hotel</p>
                  </a>
               </li>
               <li>
                  <a href="#">
                     <i class="fa fa-bed"></i>
                     <p>Hotel</p>
                  </a>
               </li>
               <li>
                  <a href="#">
                     <i class="fa fa-bed"></i>
                     <p>Hotel</p>
                  </a>
               </li>
            </ul>
         </div>
      </div>
      <div class="col-md-3 pack_price_section ht">
         <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#tt">Tour Type</a></li>
            <li><a data-toggle="tab" href="#std">Standard</a></li>
         </ul>
         <div class="tab-content pck_tab">
            <div id="tt" class="tab-pane text-center fade in active">
               <h3>₹ 40,000</h3>
               <p>Price per adult</p>
               <div class="pck_butns">
                  <a href="#" class="btn btn-default">Wait us to call you</a>
                  <a href="#" class="btn btn-danger">Block</a>
               </div>
            </div>
            <div id="std" class="tab-pane text-center fade">
               <h3>₹ 40,000</h3>
               <p>Price per adult</p>
               <div class="pck_butns">
                 <a href="#" class="btn btn-default">Wait us to call you</a>
                 <a href="#" class="btn btn-danger">Block</a>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="col-md-12 package_list nopad">
      <div class="col-md-2 pck_image ht nopad">
         <div>
            <img src="http://pacetravels.org/extras/custom/TMX1512291534825461/images/5eea020495.jpg" alt="image" >
            <p><input type="checkbox" name="">compare</p>
         </div>
      </div>
      <div class="col-md-7 pack_section ht">
         <div class="col-sm-12 brdr_bot">
            <div class="col-sm-8 pl_name">
               <h4>Easy Dubai flexi flights</h4>
            </div>
            <div class="col-sm-4 pl_dur">
               <h4>5 Nights 4 days</h4>
            </div>
         </div>
         <div class="col-sm-12 brdr_bot pad5">--Dubai--</div>
         <div class="col-sm-12">
            <ul class="list-inline pck_facility">
               <li>
                  <a href="#">
                     <i class="fa fa-bed"></i>
                     <p>Hotel</p>
                  </a>
               </li>
               <li>
                  <a href="#">
                     <i class="fa fa-bed"></i>
                     <p>Hotel</p>
                  </a>
               </li>
               <li>
                  <a href="#">
                     <i class="fa fa-bed"></i>
                     <p>Hotel</p>
                  </a>
               </li>
               <li>
                  <a href="#">
                     <i class="fa fa-bed"></i>
                     <p>Hotel</p>
                  </a>
               </li>
               <li>
                  <a href="#">
                     <i class="fa fa-bed"></i>
                     <p>Hotel</p>
                  </a>
               </li>
            </ul>
         </div>
      </div>
      <div class="col-md-3 pack_price_section ht">
         <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#tt">Tour Type</a></li>
            <li><a data-toggle="tab" href="#std">Standard</a></li>
         </ul>
         <div class="tab-content pck_tab">
            <div id="tt" class="tab-pane text-center fade in active">
               <h3>₹ 40,000</h3>
               <p>Price per adult</p>
               <div class="pck_butns">
                  <a href="#" class="btn btn-default">Wait us to call you</a>
                  <a href="#" class="btn btn-danger">Block</a>
               </div>
            </div>
            <div id="std" class="tab-pane text-center fade">
               <h3>₹ 40,000</h3>
               <p>Price per adult</p>
              <div class="pck_butns">
                  <a href="#" class="btn btn-default">Wait us to call you</a><
                  <a href="#" class="btn btn-danger">Block</a>
               </div>
            </div>
         </div>
      </div>
   </div>
 </div> -->

<!-- <style type="text/css">
	
	.col-md-12.package_list {
    background: #fff;
  border-radius: 6px;
    box-shadow: 0 0 2px #eee;
    margin: 10px 0px;
}
ul.list-inline.pck_facility li {
    padding: 10px 10px;
}
.pack_price_section .nav-tabs
{
	border:none;
}
.pack_price_section .nav-tabs>li
{
	width: 50%;
}
.pack_price_section .nav-tabs>li.active>a, .pack_price_section .nav-tabs>li.active>a:focus, .pack_price_section .nav-tabs>li.active>a:hover {
    border: none !important;
    background: #d21819 !important;
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    width: 100%;
    margin: 5px 0px;
    text-align: center;
}
.tab-content.pck_tab h3 {
    color: #222;
    font-weight: 600;
}
.pack_price_section .nav-tabs>li>a {
    line-height: 1.42857143;
    border: none;
    background: #eee;
    color: #222;
    font-size: 14px;
    font-weight: 600;
    width: 100%;
    margin: 5px 0px;
    text-align: center;
}
.pack_price_section
{
	border-radius: 6px;
    border: 1px solid #ccc;
}
.col-md-7.pack_section {
    border-radius: 6px;
    border: 1px solid #ccc;
}
.pck_image
{
	border-radius: 6px;
    border: 1px solid #ccc;
}
.pck_facility a i {
    text-align: center;
    margin: 0 auto;
    color: #d21819;
    background: #fff;
    padding: 8px;
    border-radius: 50%;
    margin: 5px 0px;
    border: 1px solid #eee;
    outline: none;
}
.pck_facility p
{
color: #555;
}
.brdr_bot
{
	border-bottom: 1px solid #eee;
}
.pad5
{
	padding: 5px;
}
.pck_image img {
    width: 100%;
    display: block;
    height: 150px;
}
ul.list-inline.pck_facility {
    padding: 8px;
}
.pck_image p {
    padding: 12px;
    background: #eee;
    color: #222;
    font-size: 14px;
}
.pck_tab li
{
	padding:5px;
}
.ht {
    height: 200px;
}
.pl_name h4 {
    color: #222;
    font-size: 20px;
    font-weight: 500;
}
.pck_butns
{
	display: flex;
}
.pck_butns a
{
	flex: 1 1 0;
    width: 0;
    margin: 0px 5px;
}
</style> -->
<style type="text/css">
  .pck_butns
  {
    display: flex;
  }
  .pck_butns a
  {
    flex: 1 1 0;
      width: 0;
      margin: 0px 5px;
  }
  .id-p-div-row{
      background: linear-gradient(96deg,#002042,#0a8ec1);
      color: #fff;
      height: 168px;
      padding: 10px!important;
  }
  .pck_butns a {
      margin: 3px 5px;
  }
/*  .id-p-div-row small{
    color: #fff;
    font-size: 0.5em;
  }*/
  .id-package-search-img {
    box-shadow: 2px 2px 5px #ccc;
    border: 5px solid #fff;
    height:168px ;
    width: 100%;
  }
  .id-p-search-row{
    box-shadow: 2px 2px 8px #ccc;
    height: auto;
    margin-bottom: 15px;
    background-color: #fff;
    /*border: 1px solid #ccc;*/
  }
  .id-p-search-row:hover{
    border: 1px solid #000;
  }
  .id-mt-3{
    margin-top: 30px;
  }
 .id-p-search-row .id-p {
    font-size: 20px;
    color: #3a8bbb;
    margin-bottom: 0;
    font-weight: 500;
}
  .pack_details div span {
    margin: 2px;
    flex: 1 1 0;
    padding: 0;
    color: #2a2a2a;
  }
  .id-div1 .id-sub-p{
    /*color: #9a9a9a;*/
    color: #5d5b5b;
    font-size: 15px;
  }
  .id-div1{
    padding: 10px 0 0 15px;
  }
  .id-p-search-row .id-itenirary-div{
    background-color: #f2f2f2;
    padding: 8px;
    margin-left: 5px;
  }
  .id-itenirary-div p{
    margin: 0;
    font-size: 15px;
  }
  .id-itenirary-div .p{
    /*color: #3a8bbb;*/
    color: #e25d5d;
    font-weight: bold;
  }
  .id-p-div-row h3{
    text-align: center;
    margin-top: 0;
  }
  .id-p-div-row p{
    text-align: center;
    color: #ffc800;
    margin-bottom: 4px;
    font-size: 15px;
  }
  .id-p-search-row .id-p-group{
    text-align: right;
    font-size: 12px;
    color: #666;
    padding-right: 15px;
    margin: 2px;
    margin-top: 8px;
  }
  .content-wrapper{
    background-color:#f7f7f7!important;
  }
  .id-p-filter{
    background-color: #ffffff;
    color: #000;
    padding: 10px;
    border-top-left-radius: 25px;
    border-top-right-radius: 25px;
    box-shadow: 2px 2px 8px #ccc;
    border:1px solid #ccc;
  }

  .id-p-filter label{
    font-size: 15px;
    padding-top: 5px;
    cursor: pointer;
    color: #666;
  }
  .id-sub-p i{
    font-size: 12px;
    color: #666;
  }
  .id-searched{
    background-color: #fff;
    margin-bottom: 20px;
    padding: 15px; 
    border:1px solid #ccc;
    border-radius: 25px;
    text-align: center;
  }
  .id-searched p{
    margin: 0;
    font-size: 20px;
    color: #000;
  }
  .id-pr-0{
    padding-right: 0;
  }
  .id-searched span{
    /*color: #3a8bbb;*/
    color: #d9524f;
  }
  .id-searched p{
    font-size: 15px;
    margin: 0;
    color: #666;
  }
  .id-searched h3{
    margin: 0;
  }
  .id-p-filter select{
    width: 100%;
    border: none;
    border-bottom: 1px solid #b9b9b9;
    padding: 8px;
    cursor: pointer;
    background: transparent;
    font-size: 15px;
    color: #666;
  } 
 .img-back {
    width: 100%;
    overflow: hidden;
    display: block;
     box-shadow: inset 0px -30px 50px 50px rgba(0,0,0,0.5);
     background-position: center;
     background-size: cover;
     height: 250px;
} 

  /*.img-back {
    width: 100%;
    height: 250px;
    position: relative;
    box-shadow: inset 0px -30px 50px 50px rgba(0,0,0,0.5)
}*/
  .img-back h1 {
    color: #fff;
    padding-top: 10%;
    text-align: center;
    text-shadow: 1px 1px 5px #000;
   /* position: absolute;
    left: 0;
    right: 0;
    top: 0;*/
}
  .id-p-div-row p{
    padding-top: 20px;
  }
  .id-div1 p {
    margin: 0 0 4px;
}
.modal-footer {
    padding: 15px;
    text-align: right;
    border-top: none;
}
 .modal-title{
    text-align: center;
  }
  .modal-header{
    background: linear-gradient(96deg,#002042,#0a8ec1);
    color: #fff;
  }
  .holiday_srch_input input.form-control:placeholder-shown {
    font-size: 15px;
}

.holiday_srch_input input.form-control{
    border-radius: 15px !important;
    border: 1px solid #ccc;
    height: 30px;
    box-shadow: unset!important;
}
.holiday_srch_input textarea.form-control {
    border-radius: 15px !important;
    border: 1px solid #ccc;
    height: 60px;
    box-shadow: unset!important;
}
.holiday_srch_input textarea, .holiday_srch_input select {
    border-radius: 15px !important;
    /* border: 1px solid #9d00f9; */
    box-shadow: unset!important;
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
</style