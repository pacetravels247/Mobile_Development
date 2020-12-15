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
<div class="clearfix"></div>
<?php if(count($package_list)>=1) { ?>
<div class="spcl_atrct">
    <div class="clearfix"></div>
    <div class="col-xs-12 pad30">
    <h3 class="str text-left"><?=$heading?></h3>
      <div class="col-xs-12 hldy_lst">
        <div class="col-md-9">
          <strong>Sort By</strong>
          <ul class="list-inline">
            <li class="active sort_btn"><span class="sort_type">Name</span></li>
            <li class="sort_btn"><span class="sort_type">Price</span></li>
            <li class="sort_btn"><span class="sort_type">No. of Days</span></li>
          </ul>
        </div>
        <div class="col-md-3 prc_chng">
          <p>
          <label for="amount">Price range:</label>
          <input type="text" id="amount" readonly style="border:0;font-weight:bold;">
        </p>
          <div id="slider-range"></div>
        </div>
      </div>
    </div>
	<input type="hidden" value="<?=$search_type?>" class="search_type">
  <div class="col-xs-12 sort_div">
	<?php 
	//debug($package_list);
	foreach($package_list as $key => $val){
		$day_duration=$val['duration']+1;
	?>
    <div class="col-md-4 each_grid">
	<input type="hidden" class="price_val" value="<?=$val['airliner_price']?>">
      <div class="thumbnail">
        <a href="<?php echo base_url().'index.php/tours/holiday_package_detail/'.$val['pack_id']?>" target="_blank">
          <img src="<?php echo $GLOBALS['CI']->template->domain_images($val['banner_image'])?>" alt="Lights">
          <div class="caption">
            <h4><?=$val['package_name']." (".$day_duration." Days | ".$val['duration'] ." Nights)"?></h4>
            <p><?=$val['currency'].'  '.$val['airliner_price']?></p>
          </div>
        </a>
      </div>
    </div>
	<?php } ?>
  </div>
</div>
<?php } 
else { ?>
    <h3 class="package_empty_msg" style="padding: 20px;">No results found for the keyword you are searching on. Please retry with other options from the search dropdown.</h3>
\<?php } ?>
<div class="clearfix"></div>
  <script>
  /*$( function() {
    $( "#slider-range" ).slider({
      range: true,
      min: 0,
      max: 500,
      values: [ 75, 425 ],
      slide: function( event, ui ) {
        $( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
      }
    });
    $( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) +
      " - $" + $( "#slider-range" ).slider( "values", 1 ) );
  } );*/
  $(function() {
      //var sid= "slider-transfer";
     // var ite_in="transfer_in";
      //var cost="transfer_cost";
      var minPrice =0;
      var maxPrice =0;
      var _min_max =[];
      //var row_count="<?php echo $row_count; ?>";
    
      var current_div = $(this).attr('id');
      $.each($('.each_grid').find('.price_val'), function (index, value) {
       var price = parseFloat($(this).val());
       _min_max.push(price);
      });
      var maxPrice = Math.max.apply(null, _min_max)+ 2; // 3
      var minPrice = Math.min.apply(null, _min_max);
      $( "#slider-range" ).slider({
        range: true,
        min: minPrice,
        max: maxPrice,
        values: [ minPrice, maxPrice ],
        slide: function( event, ui ) {
          
          $( "#amount").val( "Rs " + ui.values[ 0 ] + " - Rs " + ui.values[ 1 ] );
        },
        change: function(e) {
          if ('originalEvent' in e) {
           
            ini_pricef();
            filter_ite();
          }
        }
      });
     
    $( "#amount").val( "Rs" + $( "#slider-range" ).slider( "values", 0 ) +
       " - Rs " + $( "#slider-range" ).slider( "values", 1 ) );
   });
   var _ini_fil = {};
   function ini_pricef()
   {
    
     _ini_fil['min_price'] = parseFloat($("#slider-range").slider("values")[0]);
     _ini_fil['max_price'] = parseFloat($("#slider-range").slider("values")[1]);
      //console.log(_ini_fil);
   }
   function filter_ite() {
   
       $('.each_grid').each(function(key, value) {
         var _rmp = parseInt($('.price_val', this).val());
		 
         if ( _rmp >= _ini_fil['min_price'] && _rmp <= _ini_fil['max_price'] )  {
           $(this).removeClass('hide');
         } else {
           $(this).addClass('hide');
         }
         // _min_max33.push(_rmp);
       });
       // console.log(_min_max33);
     
   }
  </script>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
   
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
	$(document).on('click','.sort_btn',function(e){
		
		$('.sort_btn').removeClass('active');
		$(this).addClass('active');
		var sort_type=$(this).find('.sort_type').text();
		var tour_type=$('.search_type').val();
		
		$.ajax({
			
            type: "POST",
            url: "<?php echo base_url().'index.php/tours/holiday_package_list_sort/' ?>",
            data: {tour_type:tour_type,sort_type:sort_type},
            success: function(result){
				$('.sort_div').html(result);
            }
    });
	  
  });
  });

</script>