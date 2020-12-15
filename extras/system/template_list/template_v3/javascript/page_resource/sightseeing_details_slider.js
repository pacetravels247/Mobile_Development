
$(document).ready(function() {
 
  var sync1 = $("#hotel_top");
 // var sync2 = $("#hotel_bottom");
 
  sync1.owlCarousel({
    items : 1,
    slideSpeed : 1000,
    navigation: true,
    pagination:false,
    itemsDesktop      : [1199,1],
    itemsDesktopSmall     : [979,1],
    itemsTablet       : [768,1],
    itemsMobile       : [479,1],
    afterAction : syncPosition,
    responsiveRefreshRate : 200,
  });
 
/*  sync2.owlCarousel({
    items : 6,
    itemsDesktop      : [1199,6],
    itemsDesktopSmall     : [979,6],
    itemsTablet       : [768,6],
    itemsMobile       : [480,3],
    pagination:false,
    responsiveRefreshRate : 100,
    afterInit : function(el){
      el.find(".owl-item").eq(0).addClass("synced");
    }
  });*/



  function syncPosition(el){
    var current = this.currentItem;
    //console.log(current)
    $("#hotel_bottom")
      .find(".owl-item")
      .removeClass("synced")
      .eq(current)
      .addClass("synced")
    if($("#hotel_bottom").data("owlCarousel") !== undefined){
      center(current)
    }
  }
 
  $("#hotel_bottom").on("click", ".owl-item", function(e){
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
