
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
  accordionOn: ['xs']
});

$(document).ready(function() {
 
  var sync1 = $("#hotel_top");
  var sync2 = $("#hotel_bottom");
 
  sync1.owlCarousel({
    items : 1,
    slideSpeed : 1000,
    navigation: false,
    pagination:false,
    itemsDesktop      : [1199,1],
    itemsDesktopSmall     : [979,1],
    itemsTablet       : [768,1],
    itemsMobile       : [479,1],
    afterAction : syncPosition,
    responsiveRefreshRate : 200,
  });
 
  sync2.owlCarousel({
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
  });

  $('#maphtlmapdtls').on('click', function(){
     //load map
     var lat = $("#latitude").val();
     var lon = $("#longitude").val();
     var image_url = $("#api_base_url").val();
     var hotel_name = $("#hotel_name").val();
    // alert("hiii");
     /*start**/
      $('#map_viewsld').removeClass('hide');
      $('#maphtlmapimages').removeClass('hide');
      $('#maphtlmapdtls').addClass('hide');
     var myCenter=new google.maps.LatLng(lat,lon);
        var mapProp = {
        center:myCenter,
        zoom:10,
        mapTypeId:google.maps.MapTypeId.ROADMAP

      };

      var map = new google.maps.Map(document.getElementById("Map"), mapProp);
    
      var marker = new google.maps.Marker({
        position:myCenter,
        icon:image_url,
        animation: google.maps.Animation.DROP
      });
    
      marker.setMap(map);
    
      var infowindow = new google.maps.InfoWindow({
        content:hotel_name
      });
      //infowindow.open(map, marker);
      google.maps.event.addListener(marker, "click", function() {
        infowindow.open(map, marker);
      });

     /*end**/
		
		// $('#hotel_bottom').css('cursor','not-allowed');
    	sync1.trigger("to.owl.carousel",0);
    	sync2.trigger("to.owl.carousel",0);
	});

	$('#maphtlmapimages').on('click', function(){
		$('#map_viewsld').addClass('hide');
		$('#maphtlmapimages').addClass('hide');
		$('#maphtlmapdtls').removeClass('hide');
		$('#hotel_bottom').css('cursor', 'pointer');
	});
	$('#hotel_bottom').on('click', function(){
		$('#map_viewsld').addClass('hide');
		$('#maphtlmapimages').addClass('hide');
		$('#maphtlmapdtls').removeClass('hide');
	});
 
  function syncPosition(el){
    var current = this.currentItem;
    console.log(current)
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


  $(".show-more a").on("click", function() {
    var $link = $(this);
    var $content = $link.parent().prev("div.lettrfty");
    var linkText = $link.text();
    $content.toggleClass("short-text, full-text");
    $link.text(getShowLinkText(linkText));
    return false;
  });

function getShowLinkText(currentText) {
    var newText = '';
    if (currentText.toUpperCase() === "SHOW MORE +") {
        newText = "Show Less -";
    } else {
        newText = "Show More +";
    }
    return newText;
}


$(".show-rooms a").on("click", function() {
	//alert("")
    var $link = $(this);
    var $content = $link.parent().prev("div.romlistnh");
    var linkText = $link.text();
    $content.toggleClass("short-text, full-text");
    $link.text(getShowLinkRooms(linkText));
    return false;
  });


function getShowLinkRooms(currentText) {
    var newText = '';
    if (currentText.toUpperCase() === "SHOW MORE ROOMS +") {
        newText = "Show Less Rooms -";
    } else {
        newText = "Show More Rooms +";
    }
    return newText;
}
});

$('#selectroom').on('click', function(){
	

        $('.nav li.active').removeClass('active');
        $('.tab-content .tab-pane.active').removeClass('active');

        var $parent = $('.roomstab');
        $parent.addClass('active');
        $('#rooms').addClass('active');
        // e.preventDefault();
    
});