<?php 

  //debug($hotel_images);exit;
$base_url=base_url().'index.php/hotel/image_details_cdn';

//<img src="' . $base_url_image.'/'.base64_encode($sanitized_data['HotelCode']).'/'.$i_k.'" alt="' . $sanitized_data['HotelName'] . '"/>

?>
<div id="sync1" class="owl-carousel owl-theme">
  <?php if($hotel_images['data']):?>
    <?php foreach($hotel_images['data'] as $key=>$value):
      //$image= str_replace('https://cdn.grnconnect.com', $base_url, $value); 
        $image = $base_url.'/'.base64_encode($HotelCode).'/'.$key;
        //$image= str_replace('https://cdn.grnconnect.com', $base_url, $value); 
        //echo $image;exit;
        ?>
      <div class="item">
       <div class="htlsldig">
          <img src="<?=$image?>" data-src="<?=$image?>" alt="<?=$HotelName?>"/>
       </div>
       </div>  
    <?php endforeach;?>
  <?php else: ?>    
      <div class="item">
       <div class="htlsldig">
          <img src="<?=$GLOBALS['CI']->template->template_images('default_hotel_img.jpg');?>" data-src="<?=$GLOBALS['CI']->template->template_images('default_hotel_img.jpg');?>" alt=""/>
       </div>
       </div>
  <?php endif; ?>       
</div>

<div id="sync2" class="owl-carousel owl-theme btmimg">
<?php if($hotel_images['data']):?>
  <?php foreach($hotel_images['data'] as $s_key=>$s_value):
     $image = $base_url.'/'.base64_encode($HotelCode).'/'.$s_key;
       //$image = $base_url.'/'.base64_encode($HotelCode.'/'.$s_value);
      // $image1= str_replace('https://cdn.grnconnect.com', $base_url, $s_value); 
      ?>
    <div class="item">
      <div class="htlsldigsml">
        <img src="<?=$image?>" alt="<?=$HotelName?>"/>
      </div>
      </div> 
    <?php endforeach;?>    
<?php else:?>   
   <div class="item">
      <div class="htlsldigsml">
       <img src="<?=$GLOBALS['CI']->template->template_images('default_hotel_img.jpg');?>" data-src="<?=$GLOBALS['CI']->template->template_images('default_hotel_img.jpg');?>" alt=""/>
      </div>
      </div> 
<?php endif;?>
</div>
 <script type="text/javascript">
  
  $(document).ready(function() {
    var sync1 = $("#sync1");
      var sync2 = $("#sync2");

      sync1.owlCarousel({
        singleItem : true,
        slideSpeed : 1000,
        navigation: false,
        pagination:false,
        afterAction : syncPosition,
        responsiveRefreshRate : 200,
      });

      sync2.owlCarousel({
        items : 6,
        itemsDesktop      : [1199,4],
        itemsDesktopSmall     : [979,4],
        itemsTablet       : [768,4],
        itemsMobile       : [479,2],
        navigation: true,
        pagination:false,
        responsiveRefreshRate : 100,
        afterInit : function(el){
          el.find(".owl-item").eq(0).addClass("synced");
        }
      });

      function syncPosition(el){
        var current = this.currentItem;
        $("#sync2")
          .find(".owl-item")
          .removeClass("synced")
          .eq(current)
          .addClass("synced")
        if($("#sync2").data("owlCarousel") !== undefined){
          center(current)
        }

      }

      $("#sync2").on("click", ".owl-item", function(e){
        e.preventDefault();
        var number = $(this).data("owlItem");
        sync1.trigger("owl.goTo",number);
      });

      function center(number){
        var sync2visible = sync2.data("owlCarousel").owl.visibleItems;

        var num = number;
        var found = false;
        for(var i in sync2visible){
          if(num === sync2visible[i]){
            var found = true;
          }
        }

        if(found===false){
          if(num>sync2visible[sync2visible.length-1]){
            sync2.trigger("owl.goTo", num - sync2visible.length+2)
          }else{
            if(num - 1 === -1){
              num = 0;
            }
            sync2.trigger("owl.goTo", num);
          }
        } else if(num === sync2visible[sync2visible.length-1]){
          sync2.trigger("owl.goTo", sync2visible[1])
        } else if(num === sync2visible[0]){
          sync2.trigger("owl.goTo", num-1)
        }
      }
  });
</script>