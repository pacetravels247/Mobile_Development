<!--
<label for="search">Search: </label>
<input id="search">-->

















<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['CI']->template->template_css_dir('owl.carousel.min.css')?>">
<div class="fixed_height mb15">
   <div class="col-md-12 nopad">
      <div class="hldyblak blue_bg">
         <div class="container-fluid">
            <div class="tab-content custmtab">
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
         </div>
      </div>
   </div>
</div>
<?php
$image_array=array();
$image_data=array();
foreach($tours_itinerary_dw as $ite_image){
	$photos=explode(',',$ite_image['banner_image']);
	foreach($photos as $val_pho){
		if($val_pho!=''){
			array_push($image_array,$val_pho);
			array_push($image_data,$ite_image['visited_city_name']);
		}
	}
	
}
//debug($package_details); 
	//$photos=explode(',',$package_details[0]['banner_image']);
	//array_pop($photos);
	//debug($photos);

?>

<div class="clearfix"></div>
<div class="spcl_atrct">
<div class="clearfix"></div>
<div class="col-xs-12 pad30">
<?php echo $this->session->flashdata("msg"); ?>
<h3 class="str text-left"><?= $package_details[0]['package_name']?></h3>
<div class="org_row">
  <div class="col-md-6 lft_side_dargling">
      <div class="col-xs-12 lft_detl">
         <div class="col-xs-12 mn_lst">
            
            <div class="col-xs-7 nopad">
    
               <h4><span><i class="far fa-clock"></i><strong> Duration :</strong></span><?= $package_details[0]['duration']+1 . ' Days / ' . ( $package_details[0]['duration'] ) . (( $package_details[0]['duration']==1)?' Night': 'Nights'); ?></h4>
            </div>
            <div class="col-xs-5 nopad">
               <h4><strong><?=$package_price_details[0]['currency']?>:<?=$package_price_details[0]['airliner_price']?>/-</strong><small>(Price per person)</small></h4>
            </div>
         </div>
         <div class="col-xs-12 mn_lst">
         	<h4><strong><i class="far fa-clock"></i> Departure Date($) :</strong></h4>
         	<label>
			   <select name="dep_dat">
					<?php foreach($dep_dates as $dep_key => $dep_val){ ?>
						<option value="<?=$dep_val['dep_date']?>"><?=date('d M Y',strtotime($dep_val['dep_date']))?></option>
					<?php } ?>
			   </select>
			   <input id="datepicker_dat" type="text" class="datpic" value="dd/mm/yyyy">

			   </label>
			</div>

         
         <div class="col-xs-12 mn_lst">
         	<div class="col-xs-6 nopad">
               <h4><strong>Tour Code :</strong><span><?=$package_details[0]['tour_code']?></span></h4>
            </div>
            <div class="col-xs-6 nopad">
               <h4><strong>Holiday Type :</strong><span>
        <?php 
          $types=array();
          foreach($tour_types as $type_val){
             $types[]=$type_val['tour_type_name'];
          } 
          echo implode(',',$types);
        ?></span></h4>
            </div></div>

            
         
         <div class="col-xs-12 mn_lst">
            
            <div class="col-xs-6 nopad">
               <h4><strong>Country :</strong><span><?=$country?></span></h4>
            </div>
            <div class="col-xs-6 nopad">
               <h4><strong>City :</strong><span><?=$city?></span></h4>
            </div>
         </div>
         <div class="col-xs-12 mn_lst">
         	<div class="room_sec">
         		<ul class="list-inline">
   <li><h5>Room</h5></li>
   <li>
      <label>Adult</label>
      <div class="input-group">
         <span class="input-group-btn">
         <button class="btn btn-white btn-minuse" id="decrease" onclick="decrease()" type="button">-</button>
         </span>
         <input type="number" id="number" class="form-control no-padding add-color text-center height-25" maxlength="3" value="0">
         <span class="input-group-btn">
         <button class="btn btn-red btn-pluss" id="increase" onclick="increase()"  type="button">+</button>
         </span>
      </div>
      <small>Above 12 years</small>
   </li>
  	<li>
      <label>Child<small>(with bed)</small></label>
      <div class="input-group">
         <span class="input-group-btn">
         <button class="btn btn-white btn-minuse" id="decreaseValue" onclick="decreaseValue()" type="button">-</button>
         </span>
         <input type="number" id="number1" class="form-control no-padding add-color text-center height-25" maxlength="3" value="0">
         <span class="input-group-btn">
         <button class="btn btn-red btn-pluss" id="increaseValue" onclick="increaseValue()" type="button">+</button>
         </span>
      </div>
      <small>Below 12 years</small>
   </li>
   <li>
      <label>Child<small>(without bed)</small></label>
      <div class="input-group">
         <span class="input-group-btn">
         <button class="btn btn-white btn-minuse" id="decreaseValue" onclick="decreaseValue()" type="button">-</button>
         </span>
         <input type="number" id="number2" class="form-control no-padding add-color text-center height-25" maxlength="3" value="0">
         <span class="input-group-btn">
         <button class="btn btn-red btn-pluss" id="increaseValue" onclick="increaseValue()" type="button">+</button>
         </span>
      </div>
      <small>Below 12 years</small>
   </li>
   <li>
      <label>Infant</label>
      <div class="input-group">
         <span class="input-group-btn">
         <button class="btn btn-white btn-minuse" id="decreaseValue" onclick="decreaseValue()" type="button">-</button>
         </span>
         <input type="number" id="number3" class="form-control no-padding add-color text-center height-25" maxlength="3" value="0">
         <span class="input-group-btn">
         <button class="btn btn-red btn-pluss" id="increaseValue" onclick="increaseValue()" type="button">+</button>
         </span>
      </div>
      <small>(0-2 years)</small>
   </li>
</ul>
<div class="ad_rm">
    <h5>Add room   <span>  <i class="fa fa-plus"></i></span></h5>
</div>
         	</div>
         </div>
         <div class="clearfix"></div>
         <div class="col-xs-12">
            <button type="button" class="btn btn-default btn_enq" data-toggle="modal" data-target="#enquiry_form">Enquiry</button>
         </div>
     <!-- Modal -->
      <div class="modal fade" id="enquiry_form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>
          <div class="modal-body">
          <form action="<?php echo base_url();?>index.php/tours/send_enquiry" method="post" id="send_enquiry">
            <div class="col-md-8 col-md-offset-2 holiday_srch_input">
            <input type="hidden"  name="pack_id"  value="<?=$package_details[0]['id']?>">
            <input type="hidden"  name="pack_name"  value="<?=$package_details[0]['package_name']?>">
             <div class="form-group">
              <label>Name</label>
              <input type="text"  name="name" class="form-control" placeholder="Enter Name" maxlength="30" required>
             </div>
             <div class="form-group">
              <label>Email</label>
              <input type="Email"  name="Email" class="form-control" placeholder="Enter Email" maxlength="45" required>
             </div>
             <div class="form-group">
              <label>Phone</label>
              <input type="text"  name="phone" class="form-control" placeholder="Enter Phone" maxlength="12" required>
             </div>
             <div class="form-group">
              <label>No. of Passengers</label>
              <input type="number"  name="passenger" class="form-control" placeholder="Enter No Of Guests" required>
             </div>
             <div class="form-group">
              <label>Messenger</label>
              <textarea  name="message" aria-hidden="true" class="form-control" placeholder="Enter details if any" maxlength="200"></textarea>
             </div>
             <div class="form-group">
              <label>Departure Date</label>
              <select name="dep_date" class="form-control">
              
                <?php foreach($dep_dates as $dep_key => $dep_val){ ?>
                <option value="<?=$dep_val['dep_date']?>"><?=date('d M Y',strtotime($dep_val['dep_date']))?></option>
                <?php } ?>
              
              </select>
             </div>
             
           
          
          </div>
          <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Send Enquiry</button>
          </div>
          </form>
           </div>
        </div>
        </div>
      </div>
      </div>
   </div>
   <div class="col-md-6 pck_sldr">
      <div class="owl-carousel owl-theme">
		<?php foreach($image_array as $pic_key => $pic){ ?>
			<div class="item"><img src="<?php echo $GLOBALS['CI']->template->domain_images($pic)?>" alt="<?=$image_data[$pic_key]?>"> <div class="img-caption"><?=$image_data[$pic_key]?></div></div>
		<?php } ?>
      </div>
   </div>
   


</div>
</div>
</div>
<div class="clearfix"></div>
<div class="col-xs-12 hldy_tab">

<div class="col-sm-3 pck_img_sec">
  <div class="pack_card">
    <img src="http://pacetravels.org/extras/custom/TMX1512291534825461/images/5eea020495.jpg" alt="image">
      <div class="caption_card">
        <h3><a href="#" class="p_name pull-left">bangkok</a></h3>
        <p class="price pull_right">13000</p>
      </div>
  </div>
  <div class="pack_card">
    <img src="http://pacetravels.org/extras/custom/TMX1512291534825461/images/5eea020495.jpg" alt="image">
      <div class="caption_card">
        <h3><a href="#" class="p_name pull-left">bangkok</a></h3>
        <p class="price pull_right">13000</p>
      </div>
  </div>
  <div class="pack_card">
    <img src="http://pacetravels.org/extras/custom/TMX1512291534825461/images/5eea020495.jpg" alt="image">
      <div class="caption_card">
        <h3><a href="#" class="p_name pull-left">bangkok</a></h3>
        <p class="price pull_right">13000</p>
      </div>
  </div>
  <div class="pack_card">
    <img src="http://pacetravels.org/extras/custom/TMX1512291534825461/images/5eea020495.jpg" alt="image">
      <div class="caption_card">
        <h3><a href="#" class="p_name pull-left">bangkok</a></h3>
        <p class="price pull_right">13000</p>
      </div>
  </div>
  
</div>

   <div class="col-sm-9">
   	<div>
      <ul class="nav nav-tabs responsive-tabs">
         <li class="active"><a href="#home1">Overview</a></li>
         <li><a href="#messages1">Inclusion-Exclusion</a></li>
         <li><a href="#settings1">Hotels</a></li>
		 <li><a href="#price">Pricing Details</a></li>
         <li><a href="#cancel">Cancellation Policy</a></li>
      </ul>
      <div class="tab-content description" id="pck_scroll">
         <div class="tab-pane active" id="home1">
            <p> <?php
					foreach ($tours_itinerary_dw as $key => $itinary) {
						$accommodation = $itinary['accomodation'];
						$accommodation = json_decode($accommodation);
						$visited_city=json_decode($itinary['visited_city'],1);
				?>
					<tr>
						<td style="padding:0px 10px 10px 0">
							<span style="margin:0 0 2px; font-weight:bold"><h4>Day <?php echo $key+1; ?> - <?php echo  $itinary['visited_city_name']; ?> </h4></span><br/>
							<p style="margin:0;">
							  <?php echo  htmlspecialchars_decode($itinary['itinerary_des']);   ?>
							</p>
          
							<span style="margin:0 0 2px; font-weight:bold">Meal Plan:
							  <?php foreach ($accommodation as  $accom) {
								if ($accom === end($accommodation)){
									   echo $accom;
									}else{
									   echo $accom.'|';
									}
								  
							  } ?></span>
							<br><br>
							  
						</td>
					</tr>
				<?php
				}
				?>
			</p>
         </div>
        
         <div class="tab-pane" id="messages1">
			<span style="margin:0 0 2px; font-weight:bold"><h4>PACKAGE PRICE INCLUDES:</h4></span><br/>
          
			<p style="margin:0;white-space: normal;">
              <?php 
              $package_details[0]['inclusions'] = str_replace('\n', '', $package_details[0]['inclusions']);
              echo htmlspecialchars_decode($package_details[0]['inclusions']); 
              ?>
            </p>
			<span style="margin:0 0 2px; font-weight:bold"><h4>PACKAGE PRICE DOES NOT INCLUDES:</h4></span><br/>
			
			<p style="margin:0;white-space: normal;">
              <?php 
              $package_details[0]['exclusions'] = str_replace('\n', '', $package_details[0]['exclusions']);
              echo htmlspecialchars_decode($package_details[0]['exclusions']); 
              ?>
            </p>
         </div>
         <div class="tab-pane" id="settings1">
            <p style="margin:0;white-space: normal;">
				<ul>
				<?php 
				//debug($tours_hotel_det);
					foreach($tours_hotel_det as $hotel_det_key => $hotel_val){
				?>
					<li>
					<?php if($hotel_val['no_of_night']['hotel_id']!='') {?>
						<?=$hotel_val['no_of_night']?> Nights Accommodation in <?=$hotel_val['hotel_name']?>
					<?php }else{ ?>
						<?=$hotel_val['no_of_night']?> Nights in <?=$hotel_val['city']?>
					<?php } ?>
					</li>
				<?php
					}
				?>
				</ul>
            </p>
         </div>
		 <div class="tab-pane" id="price">
			<ul class="price">
                <table class="tabel table-bordered" style="margin-right: auto;margin-left: auto;">
					<tr>
						<th style="padding: 5px;">Room Type</th>
						<th style="padding: 5px;">Per Person</th>
					</tr>

					<?php 
					//debug($b2b_tour_price);
					foreach ($b2b_tour_price as $tour_price_fly) { 
					$occ=$tour_price_fly['occupancy'];
					$query_x = "select * from occupancy_managment where id='$occ'"; 
					$exe   = $this->db->query ( $query_x )->result_array ();
					$fetch_x = $exe[0];
					?>
									<tr>
						<td style="padding: 5px;"><?=$fetch_x['occupancy_name']?></td>
						<td style="padding: 5px;"><?=$tour_price_fly['market_price']?></td>
						</tr>
					<?php } ?>
				
			  </table>
            </ul>
         </div>
         <div class="tab-pane" id="cancel">
             <p><?=$package_details[0]['canc_policy']?></p>
         </div>
      </div>
   </div>
</div>
</div>
<div class="clearfix"></div>
<script type="text/javascript" src="<?php echo $GLOBALS['CI']->template->template_js_dir('owl.carousel.min.js')?>"></script>
<script>   
  $(document).ready(function() {
     $(".owl-carousel").owlCarousel({
       items:1,
       itemsDesktop: [1000, 1],
       itemsDesktopSmall: [900, 1],
       itemsTablet: [600,1],
       loop:true,
       margin:10,
       autoplay:true,
       navigation: true,
       pagination: false,
       autoplayTimeout:1000,
       autoplayHoverPause:true
   });
  });
</script>
<script type="text/javascript">
   ! function($) {
     "use strict";
     var a = {
         accordionOn: ["xs"]
     };
     $.fn.responsiveTabs = function(e) {
         var t = $.extend({}, a, e),
             s = "";
         return $.each(t.accordionOn, function(a, e) {
             s += " accordion-" + e
         }), this.each(function() {
             var a = $(this),
                 e = a.find("> li > a"),
                 t = $(e.first().attr("href")).parent(".tab-content"),
                 i = t.children(".tab-pane");
             a.add(t).wrapAll('<div class="responsive-tabs-container" />');
             var n = a.parent(".responsive-tabs-container");
             n.addClass(s), e.each(function(a) {
                 var t = $(this),
                     s = t.attr("href"),
                     i = "",
                     n = "",
                     r = "";
                 t.parent("li").hasClass("active") && (i = " active"), 0 === a && (n = " first"), a === e.length - 1 && (r = " last"), t.clone(!1).addClass("accordion-link" + i + n + r).insertBefore(s)
             });
             var r = t.children(".accordion-link");
             e.on("click", function(a) {
                 a.preventDefault();
                 var e = $(this),
                     s = e.parent("li"),
                     n = s.siblings("li"),
                     c = e.attr("href"),
                     l = t.children('a[href="' + c + '"]');
                 s.hasClass("active") || (s.addClass("active"), n.removeClass("active"), i.removeClass("active"), $(c).addClass("active"), r.removeClass("active"), l.addClass("active"))
             }), r.on("click", function(t) {
                 t.preventDefault();
                 var s = $(this),
                     n = s.attr("href"),
                     c = a.find('li > a[href="' + n + '"]').parent("li");
                 s.hasClass("active") || (r.removeClass("active"), s.addClass("active"), i.removeClass("active"), $(n).addClass("active"), e.parent("li").removeClass("active"), c.addClass("active"))
             })
         })
     }
   }(jQuery);
   
   
   $('.responsive-tabs').responsiveTabs({
                          accordionOn: ['xs', 'sm']
                   });
  $(document).ready(function(){
	  
	$(document).on('keyup','#af_tag',function(e){
		var search_val=$(this).val();
		//alert(search_val);
		$.ajax({
			
            type: "GET",
            url: "<?php echo base_url().'index.php/tours/get_holiday_package_auto_fill/' ?>",
            data: {search_val:search_val},
            success: function(result){
				var availableTags = JSON.parse(result);
				//alert(availableTags);
				$( "#af_tag" ).autocomplete({
					source: availableTags
				});
            }
        });
		
		
	});
	  
  

  $( function() {
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
    var data = [
      { label: "anders", category: "" },
      { label: "andreas", category: "" },
      { label: "antal", category: "" },
      { label: "annhhx10", category: "Products" },
      { label: "annk K12", category: "Products" },
      { label: "annttop C13", category: "Products" },
      { label: "anders andersson", category: "People" },
      { label: "andreas andersson", category: "People" },
      { label: "andreas johnson", category: "People" }
    ];
 
    $( "#search" ).catcomplete({
      delay: 0,
      source: data
    });
  } );
  });
  </script>
</script>

<style type="text/css">
	.spcl_atrct .lft_detl .mn_lst select,.spcl_atrct .lft_detl .mn_lst input.datpic {
    float: left;
    display: inline-block;
    padding: 10px 30px;
    margin: 0px 30px;
    border: 1px solid #ccc;
    background: #eee;
    border-radius: 8px;
}
.spcl_atrct .lft_detl h4 {
    line-height: 30px;
    margin: 5px 0;
    float: left;
    font-size: 16px;
}
.spcl_atrct .lft_detl .mn_lst {
    padding: 0;
    margin: 5px 0px;
    border-bottom: 1px solid #eee;
}

.lft_detl {
    background: #FFFFFF 0% 0% no-repeat padding-box;
    box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1607843137254902);
    border: 1px solid #A100FF;
    border-radius: 9px;
   padding: 25px 20px !important;
}
.room_sec ul li
{
	width: 20%;
	float: left;
	display: inline-block;
}
.pck_sldr .owl-carousel .item img
{
	position: relative;
}
.img-caption {
    position: absolute;
    bottom: 0;
    padding: 20px 10px;
    left: 0;
    right: 0;
    text-align: center;
    font-size: 15px;
    background: rgb(35 34 34 / 72%);
    border: 1px solid #222222;
    color: #fff;
}
.description {
    overflow-y: scroll;
    height: 800px;
}
.ad_rm span {
    margin: 0px 2px;
    font-size: 12px;
}
.nav-tabs>li {
    float: none;
    margin-bottom: 1px;
    border-bottom: 2px solid #fff;
    padding: 10px 10px;
    text-align: center;
}
ul.nav.nav-tabs.responsive-tabs {
    float: left;
    border: 1px solid #ccc;
    background-color: #f1f1f1;
    width: 25%;
    height: 100%;
}
.description h4 {
    color: #222;
    font-weight: 300;
    font-size: 18px;
}
.description p {
    color: #555;
}
</style>

<!-- <script type="text/javascript">
	$(document).ready(function(){
    //alert("hh");
	function increase() {
  var value = parseInt(document.getElementById('number').value, 10);
  value = isNaN(value) ? 0 : value;
  value++;
  document.getElementById('number').value = value;
}

function decrease() {
  var value = parseInt(document.getElementById('number').value, 10);
  value = isNaN(value) ? 0 : value;
  value < 1 ? value = 1 : '';
  value--;
  document.getElementById('number').value = value;
}
});
</script> -->

<script type="text/javascript">
	$(document).ready(function() {
$( "#datepicker_dat" ).datepicker();
});
</script>