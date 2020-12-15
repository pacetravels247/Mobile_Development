$(document).ready(function() {
	
	 // $(".map_click").click(function(){
  //    	$(".resultalls").addClass("fulview");
  //        $('.rowresult').removeClass("col-xs-4");
  //        $('#hotel_search_result .item').removeClass('grid-group-item');
  //       $('.hotel_map').show();
  //       $(".coleft").hide();
  //       $(this).addClass("active");
  //    });

    /* $(".grid_click").click(function(){
     	$('#hotel_search_result .item').addClass('grid-group-item');
        $(".resultalls").removeClass("fulview");
        $('.hotel_map').hide();
        $('.rowresult').addClass("col-xs-4");
        $('.allresult').removeClass("map_open");
         $(".coleft").show();
        $(this).addClass("active");
     });*/

     // $(".list_click").click(function(){
     //    $(".resultalls").removeClass("fulview");
     //    $('#hotel_search_result .item').removeClass('grid-group-item');
     //    $('.hotel_map').hide();
     //    $('.rowresult').removeClass("col-xs-4");
     //    $(".coleft").show();
     //    $(this).addClass("active");
     // });

     

    $(".map_tab").click(function(){
     	$(".map_tab").hide();
     	$(".list_tab").show();
     });

    $(".list_tab").click(function(){
     	$(".map_tab").show();
     	$(".list_tab").hide();
     	$(".allresult").removeClass("map_open");
     	$(".resultalls").removeClass("open");
     });

    /*  Mobile Filter  */
    $('.filter_tab').click(function() {
     //$('.resultalls').stop(true, true).toggleClass('open');
     $('.coleft').slideToggle(500);
    });


    $(".close_fil_box").click(function(){
			$(".coleft").hide();
			$(".resultalls").removeClass("open");
      });
	
});
function showhide()
     {
           var div = document.getElementById("coleftid");
    if (div.style.display !== "none") {
        div.style.display = "none";
    }
    else {
        div.style.display = "block";
    }
     }