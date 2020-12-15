<!-- HTML BEGIN -->
<div class="bodyContent">
<?php echo $this->session->flashdata("msg"); ?>
  <div class="panel panel-default clearfix" style="margin-top: 20px;"><!-- PANEL WRAP START -->
    <div class="panel-heading"><!-- PANEL HEAD START -->
      <div class="panel-title"><i class="fa fa-shield"></i> Booking Calender</div>
    </div>
    <!-- PANEL HEAD START -->
    <div class="panel-body"><!-- PANEL BODY START -->
      <div id='booking-calendar' class="">
    </div><!-- PANEL BODY END -->
  </div><!-- PANEL END -->
</div>

<?php
Js_Loader::$css[] = array('href' => SYSTEM_RESOURCE_LIBRARY . '/fullcalendar/fullcalendar.css', 'media' => 'screen');
Js_Loader::$css[] = array('href' => SYSTEM_RESOURCE_LIBRARY . '/fullcalendar/fullcalendar.print.css', 'media' => 'print');
Js_Loader::$js[] = array('src' => SYSTEM_RESOURCE_LIBRARY . '/fullcalendar/lib/moment.min.js', 'defer' => 'defer');
Js_Loader::$js[] = array('src' => SYSTEM_RESOURCE_LIBRARY . '/fullcalendar/fullcalendar.min.js', 'defer' => 'defer');
Js_Loader::$js[] = array('src' => SYSTEM_RESOURCE_LIBRARY . '/Highcharts/js/highcharts.js', 'defer' => 'defer');
Js_Loader::$js[] = array('src' => SYSTEM_RESOURCE_LIBRARY . '/Highcharts/js/modules/exporting.js', 'defer' => 'defer');
?>
<script type="text/javascript" src="<?=$GLOBALS['CI']->template->template_js_dir('owl.carousel.min.js');?>"></script>
<script type="text/javascript">
  var curday = function(sp){
    today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //As January is 0.
    var yyyy = today.getFullYear();
    return (yyyy+sp+mm+sp+dd);
  };
  jQuery(function(){
    var e_day="<?php echo $group_departure; ?>"; 
    //console.log(e_day);
    var enableDays=e_day.split(',');
    var lastItem = "<?php echo $last_item; ?>";
    var firstItem = "<?php echo $first_item; ?>";
    if(firstItem<curday('-')){
      var firstItem = curday('-');
    }
    function enableAllTheseDays(date) {
      var sdate = $.datepicker.formatDate( 'd-m-yy', date)
      
      if($.inArray(sdate, enableDays) != -1) {
      
        return [true];
      }
    
      return [false];
    }
    
    $('#datepicker_dat_group,#enquiry_datepicker_in,#enquiry_datepicker_out').datepicker({dateFormat: 'dd-mm-yy', beforeShowDay: enableAllTheseDays,minDate: new Date(firstItem), maxDate: new Date(lastItem)});
    
  })
    $("#owlCarousel12").owlCarousel({
        items : 1, 
        itemsDesktop : [1000,3],
        itemsDesktopSmall : [991,3], 
        itemsTablet: [767,2], 
        itemsMobile : [480,1], 
        navigation : true,
        pagination : false
    });
  
</script>
<script>
   
    $(document).ready(function() {

    var event_list = {};
    function enable_default_calendar_view()
    {
    load_calendar('');
    get_event_list();
    set_event_list();
    $('[data-toggle="tooltip"]').tooltip();
    }
    function reset_calendar()
    {
    $("#booking-calendar").fullCalendar('removeEvents');
    get_event_list();
    set_event_list();
    }
    //Reload Events
    setInterval(function(){
    reset_calendar();
    $('[data-toggle="tooltip"]').tooltip();
    }, <?php echo SCHEDULER_RELOAD_TIME_LIMIT; ?>);
    enable_default_calendar_view();
    //sets all the events
    function get_event_list()
    {
    set_booking_event_list();
    }
    //loads all the loaded events
    function set_event_list()
    {
    $("#booking-calendar").fullCalendar('addEventSource', event_list.booking_event_list);
    if ("booking_event_list" in event_list && event_list.booking_event_list.hasOwnProperty(0)) {
    //focus_date(event_list.booking_event_list[0]['start']);
    }
    }

    //getting the value of arrangment details
    function set_booking_event_list()
    {
    $.ajax({
    url:app_base_url + "index.php/ajax/booking_events",
            async:false,
            success:function(response){
            //console.log(response)
            event_list.booking_event_list = response.data;
            }
    });
    }

    //load default calendar with scheduled query
    function load_calendar(event_list)
    {
    $('#booking-calendar').fullCalendar({
    header: {
    center: 'title'
    },
            //defaultDate: '2014-11-12', 
            editable: false,
            eventLimit: false, // allow "more" link when too many events
            events: event_list,
            eventRender: function(event, element) {
            element.attr('data-toggle', 'tooltip');
            element.attr('data-placement', 'bottom');
            element.attr('title', event.tip);
            element.attr('id', event.optid);
            element.find('.fc-time').attr('class', "hide");
            element.attr('class', event.add_class + ' fc-day-grid-event fc-event fc-start fc-end');
            element.attr('href', event.href);
            element.attr('target', '_blank');
            element.css({'font-size':'10px', 'padding':'1px'});
            if (event.prepend_element) {
            element.prepend(event.prepend_element);
            }
            },
            eventDrop : function (event, delta) {
            event.end = event.end || event.start;
            if (event.start && event.end) {
            update_event_list(event.optid, event.start.format(), event.end.format());
            focus_date(event.start.format());
            } else {
            reset_calendar();
            }
            }
    });
    }
    function focus_date(date)
    {
      $('#booking-calendar').fullCalendar('gotoDate', date);
    }
    $( "#enquiry_form_rel" ).submit(function(e) {
      var sel_dep_date=$('.enquiry_datepicker_rel').val();
      //alert(sel_dep_date);
      if(sel_dep_date==''){
        alert("Please select departure date.");
        e.preventDefault();
      }
    });
    $(document).on('click','.rel_enq',function() {
      var sel_pack_id=$(this).data('pack_id');
      var sel_pack_name=$(this).data('pack_name');
      var sel_pack_code=$(this).data('pack_code');
      //alert(sel_pack_id);
      $('#enquiry_form_rel').find('.pack_id').val(sel_pack_id);
      $('#enquiry_form_rel').find('.pack_name').val(sel_pack_name);
      $('#enquiry_form_rel').find('.pack_code').val(sel_pack_code);
    });
    $(document).on('click','.send_enq',function(e){
      var enq_form        =$(this).parents('.enq_form').attr('id');
      var pack_id         =$('#'+enq_form).find('.pack_id').val();
      var pack_name         =$('#'+enq_form).find('.pack_name').val();
      var agent_id        =$('#'+enq_form).find('.agent_id').val();
      var pack_code         =$('#'+enq_form).find('.pack_code').val();
      var name          =$('#'+enq_form).find('.name').val();
      var Email           =$('#'+enq_form).find('.Email').val();
      var phone           =$('#'+enq_form).find('.phone').val();
      var passenger         =$('#'+enq_form).find('.passenger').val();
      var datepicker_dat_group  =$('#'+enq_form).find('.datepicker_dat_group').val();
      var Messenger       =$('#'+enq_form).find('.Messenger').val();
      if(datepicker_dat_group=='dd/mm/yyyy'){
        alert("Please select departure date.");
        e.preventDefault();
      }else{
        $.post('<?=base_url();?>index.php/tours/send_enquiry',{'pack_id':pack_id,'pack_name':pack_name,'agent_id':agent_id,'pack_code':pack_code,'name':name,'Email':Email,'phone':phone,'passenger':passenger,'datepicker_dat_group':datepicker_dat_group,'Messenger':Messenger},function(data)
        {
          alert("Successfully sent enquiry!!!");
        });     
      }
    });
    });


    $('.topflight').on('click', function() {
        trip_type = $(this).attr('data-trip_type'),
        from = $(this).attr('data-from');
        to = $(this).attr('data-to');
        from_loc_id = $(this).attr('data-from_loc_id');
        to_loc_id = $(this).attr('data-to_loc_id');
        depature =  $(this).attr('data-departue');
        v_class =  $(this).attr('data-v_class');
        carrier = $(this).attr('data-carrier');
        adult = $(this).attr('data-adult');
        child =  $(this).attr('data-child');
        infant = $(this).attr('data-infant');
        search_flight =  $(this).attr('data-flight_search');
   
   
          $.ajax({
            type: "GET",
            url: "<?php echo base_url().'index.php/general/pre_flight_search_ajax/' ?>",
            data: {trip_type : trip_type,from : from,from_loc_id : from_loc_id,to : to,to_loc_id :to_loc_id, depature:depature, v_class: v_class,carrier: carrier, adult:adult, child:child,infant:infant, search_flight:search_flight},
            success: function(result){
            location.href = result;
            }
            });

        });
</script>
<script type="text/javascript">
$(document).ready(function() {
    $('.carousel').carousel({
  interval: 3000,
  pause: false
});
});
</script>

<style type="text/css">
  .p_label
  {
    font-size: 16px;
    color: #222;
    font-weight: 500;
    padding-left: 10px;
  }
  .hd_pack
  {
    border-bottom: 1px solid #eee;
  }
  .child_pack {
    border-bottom: 1px solid #eee;
    margin: 15px 0px 0px;
}

.pack_details div
{
  display: flex;
    background: #eee;
}
.pack_details div span
{
  margin: 2px;
    flex: 1 1 0;
   /* width: 0;*/
    
    padding: 0;
    color: #2a2a2a;
}
.pack_details div span span {
    text-transform: capitalize;
    display: inline-block;
}
.price_package div
{
  display: flex;
}
    .price_package span
    {
      margin: 2px;
    flex: 1 1 0;
    width: 0;
    
    padding: 4px 0px;
    color: #fff;
    }
     .price_package div 
    {
      display: flex;
    }
   .price_package div a {
    margin: 5px 2px;
    flex: 1 1 0;
    width: 0;
     border-radius: 0px !important;
    /*border: 1px solid #222;
    border-radius: 4px;
    padding: 4px 10px;
    color: #f5930c;
    background: #222;*/
} .price_package {
    background: linear-gradient(96deg,#002042,#0a8ec1);
    padding: 3px 5px;
}
.package_grid .caption h4 {
    background: #ffc800;
    padding: 8px;
    margin: 0;
    color: #222;
    font-weight: 600;
    text-transform: capitalize;
    font-size: 15px;
}
/*13/7/2020*/

    .id-content p{
      font-size: 18px;
      color: #fff;
    }
    .id-selected-package{
      border: 4px solid #fff;
      box-shadow: 2px 2px 5px #ccc;
      margin-bottom: 10px;
     
    }
    .id-selected-package h3{
      color: #000;
      margin: 0;
      padding: 10px;
      text-align: center;
      background-color: #ffc800;
    }
    .id-content del{
      font-weight: normal;
    }
    .id-sub-content1{
      padding: 10px;
      text-align: center;
      font-size: 15px;
      background-color: #eee;
    }
    .id-sub-content2{
      background: linear-gradient(96deg,#002042,#0a8ec1);
      padding: 10px 5px;
    }
    .id-sub-content2 .btn {
      font-size: 12px!important;
    }
    .id-sub-content2 .btn-danger {
      color: #fff;
      background-color: #da0600;
      border: none;
    }
    .id-sub-content2 .btn-danger:hover {
      color: #fff;
      background-color: #b90803;
      border: none;
    }
    .id-image img{
      width: 100%;
      height: 190px !important;
    }
    .id-image img {
      max-width: 100%;
      transition: transform 0.1s ease-in-out;
    }
    .id-selected-package:hover img {
      transform: scale(1.2);
    }
    .id-selected-package:hover{
      border: 2px solid #000;
      margin: 7px 1px;
    }
    .id-image{
      margin: 0 auto;
      overflow: hidden;
    }
    .id-content1 {
      position: relative;
      margin: auto;
      overflow: hidden;
    }
    .id-selected-package:hover .id-content-overlay1{
      opacity: 1;
    }
    .id-content-details1 {
      background: rgba(0,0,0,0.7)!important;
      position: absolute;
      text-align: center;
      padding-left: 1em;
      padding-right: 1em;
      width: 100%;
      height: 100%;
      opacity: 0;
      -webkit-transform: translate(-50%, -50%);
      -moz-transform: translate(-50%, -50%);
      transform: translate(-50%, -50%);
      -webkit-transition: all 0.3s ease-in-out 0s;
      -moz-transition: all 0.3s ease-in-out 0s;
      transition: all 0.3s ease-in-out 0s;
    }
    .id-selected-package:hover .id-content-details1{
      top: 50%;
      left: 50%;
      opacity: 1;
    }
    .id-fadeIn-bottom1{
      top: 50%;
      left: 50%;
    }
    .id-content-details1 li{
      color: #fff;
      font-size: 18px;
    }
    .id-content-details1 ul{
      color: #ffc800;
      font-size: 20px;
      text-align: left;
      padding-top: 10px;
    }
    .id-ul i{
      color: #ffc800;
    }
    .id-search {
      width: 100%;
      box-sizing: border-box;
      border: none;
      border-bottom: 2px solid #ccc;
      font-size: 16px;
      background-color: transparent;
      background-position: 10px 10px; 
      background-repeat: no-repeat;
      padding: 5px 30px;
      -webkit-transition: width 0.4s ease-in-out;
      transition: width 0.4s ease-in-out;
      border-radius: 4px;
    }
    .id-filter{
      padding: 20px 10px 0 10px;
    }
    .id-pagehding{
      margin-top: 0;
      text-align: center;
    }
    .id-border-r{
      border-top-left-radius: 50px;
      border-top-right-radius: 50px;
      background-color: #fff;
      padding-bottom: 20px;
    }
    .custom-control-label, .custom-control-input{
      font-size: 15px;
      cursor: pointer;
    }
    .modal-footer {
    padding: 15px;
    text-align: right;
    border-top: none;
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
</style>