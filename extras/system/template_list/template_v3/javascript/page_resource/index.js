$(function($) {
var check_in = db_date(7);
var check_out = db_date(10);
    $('.htd-wrap').on('click', function(e) {
        e.preventDefault();
        var curr_destination = $('.top-des-val', this).val();
        var city_id = $('.top_des_id',this).val();

        $('#hotel_destination_search_name').val(curr_destination);
        $(".loc_id_holder").val(city_id);
        $('#hotel_checkin').val(check_in);
        $('#hotel_checkout').val(check_out);
        $('#hotel_search').submit();
    });
    $('.activity-search').on("click",function(e){
        //alert("hiii");
        e.preventDefault();
        var curr_destination = $('.destination_name',this).val();
        //alert(curr_destination);
        //console.log("curr_destination"+curr_destination);
        var city_id = $('.destination_id',this).val();
        //alert("city_id"+city_id);
        //console.log("city_id"+city_id);
        var category_id = $('.category_id',this).val();
        $("#activity_destination_search_name").val(curr_destination);
        $(".loc_id_holder").val(city_id);
        $("#select_cate").val(category_id);
        //$("#name-search").val(curr_destination);
        $("#activity_search").submit();
    });
    $("#owl-demo2").owlCarousel({
        items: 3,
        itemsDesktop: [991, 2],
        itemsDesktopSmall: [767, 2],
        itemsTablet: [600, 1],
        itemsMobile: [479, 1],
        navigation: true,
        pagination: false
    });      
    $("#TopAirLine").owlCarousel({
        items:3,
        loop:true,
        margin:10,
        autoplay:true,
        navigation: true,
        pagination: false,
        autoplayTimeout:1000,
        autoplayHoverPause:true
    });
    $("#all_deal").owlCarousel({
    items : 3, 
    itemsDesktop : [1000,3],
    itemsDesktopSmall : [991,3], 
    itemsTablet: [767,2], 
    itemsMobile : [480,1], 
        navigation : true,
    pagination : false
    });

    
    $.supersized({
        slide_interval: 5000,
        transition: 1,
        transition_speed: 700,
        slide_links: 'blank',
		slides: tmpl_imgs
    })
});

$(document).ready(function() {
  //carousel options
  $('#quote-carousel').carousel({
    pause: true, interval: 10000,
  });
});