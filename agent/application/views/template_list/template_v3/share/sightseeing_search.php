<?php

  $category_id = 0;
  if(isset($sight_seen_search_params)&&isset($sight_seen_search_params)==true&&valid_array($sight_seen_search_params)){
    $destination = @$sight_seen_search_params['destination'];
    //$parent_id = @$sight_seen_search_params['parent_id'];
    $destination_id = @$sight_seen_search_params['destination_id'];
    //$api_currency_code = @$sight_seen_search_params['api_currency_code'];
    $from_date =@$sight_seen_search_params['from_date']; 
    $to_date = @$sight_seen_search_params['to_date'];
    
    $category_id = @$sight_seen_search_params['category_id'];
    
  }
 // debug($sight_seen_search_params);
 // exit;
 
?>


<form action="<?php echo base_url();?>index.php/general/pre_sight_seen_search" 
	autocomplete="off" id="activity_search" class="activity_search">
  <div class="tabspl forhotelonly">
    <div class="tabrow">
      <div class="col-md-offset-2 col-md-8 col-sm-12 col-xs-12 nopad">
        <div class="col-md-8 col-sm-8 col-xs-7 padfive full_smal_tab mobile_width">
          <div class="lablform">Enter Location</div>
          <div class="plcetogo plcemark sidebord">           

            <input type="text" id="activity_destination_search_name" class="fromactivity1 normalinput form-control b-r-0" placeholder="Location" name="from" required value="<?php echo @$destination;?>"/>
            <input type="hidden" name="" id="name-search">
            <input class="hide loc_id_holder" name="destination_id" type="hidden" id="destination_id" value="<?=@$destination_id?>" >
          </div>
        </div>
        <input type="hidden" name="category_id" id="select_cate" value="<?=$category_id?>">
       <!--  <div class="sel-cate col-md-6 col-sm-6 col-xs-6 padfive full_smal_tab">
         <div class="lablform">Category</div>
           <div class="plcetogo selctmark sidebord plcemark">
            <select id="category_id" name="category_id" class="normalinput b-r-0">
                  <option value="">Select Category</option>
            </select>
           </div>
         </div>
        </div>
       -->

      <div class="col-md-6 col-sm-6 col-xs-12 nopad hide">
      
      
      <div class="col-md-6 col-sm-6 col-xs-6 full_mobile resdvmg hide">
        <div class="lablform">Departure</div>
        <div class="nowra plcetogo datemark ">
        
     <!--  <input type="text" readonly class="normalinput auto-focus hand-cursor form-control" id="sight_checkin1" placeholder="from date" value="<?php if(!empty($from_date)) { echo $from_date; } else { echo ''; } ?>" name="from_date"/> -->
        
        
        </div>
      </div>
      <div class="col-md-6 col-sm-6 col-xs-6 full_mobile resdvmg hide">
        <div class="lablform">Return</div>
        <div class="nowra plcetogo datemark ">
          <?php 
          $today =date('d-m-Y');
          $date = strtotime($today);
          $date =date('d-m-Y', strtotime("+1 day", $date));
          ?>
         <!--  <input type="text" readonly class="normalinput auto-focus hand-cursor form-control" id="sight_checkout1" placeholder="To date" value="<?php if(!empty($to_date)) { echo $to_date; } else { echo ''; } ?>" name="to_date" /> -->
        </div>
      </div>
     
      </div>
      
      <div class="col-md-4 col-sm-4 col-xs-5 full_mobile padfive mobile_width">
        <div class="lablform">&nbsp;</div>
        <div class="searchsbmtfot">
          <input type="submit" class="searchsbmt" value="Search" />
        </div>
      </div>
       <div class="clear-date hide">
        <div class="lablform">&nbsp;</div>
        <button class="btn btn-info" id="clear_date" type="button">Clear Dates </button>
      </div>

    </div>
  </div>
</div>
</form>

<script type="text/javascript">
   
  $(document).ready(function(){
   
    $("#sight_checkin1" ).datepicker({
      numberOfMonths: 2,
      dateFormat: 'dd-mm-yy',     
      minDate: 0,
      firstDay: 1,
      maxDate: "+361D",
      onClose: function( selectedDate ) {
        var date1 = $('#sight_checkin1').datepicker('getDate');
        date1.setDate(date1.getDate()+1);
        var date2 = $('#sight_checkin1').datepicker('getDate');
        date2.setDate(date2.getDate()+361);
        $( "#sight_checkout1" ).datepicker( "option", "minDate", date1 );
        $( "#sight_checkout1" ).datepicker( "option", "maxDate", date2 );
        $( "#sight_checkout1" ).focus();
        }
    });

    $("#sight_checkout1" ).datepicker({      
      numberOfMonths: 2,
      dateFormat: 'dd-mm-yy',
      minDate: 0,
      firstDay: 1,
      maxDate: "+1Y",
      onClose: function( selectedDate ) {
        var date1 = $('#sight_checkin1').datepicker('getDate');
        var date2 = $('#sight_checkout1').datepicker('getDate');
        var days = (date2 - date1)/1000/60/60/24;
        if(days > 0){
          $( "#no_of_nights" ).val(days);
        }
        } 
    });

  $(".fromactivity_").autocomplete({
  
    source:"<?php echo base_url(); ?>index.php/activity/get_activity_auto",
    minLength: 2,//search after two characters
    autoFocus: true, // first item will automatically be focused
    select: function(event,ui){
    //	alert(ui);
      $(".departflight").focus();
      //$(".flighttoo").focus();
    }
  });
  
  
  //  $(".fromactivity").catcomplete({ 
  //       source: function(request, response) {
  //           var term = request.term;
  //           if (term in cache) {
  //               response(cache[term]);
  //               return
  //           } else {
  //               $.getJSON(app_base_url + "index.php/activity/get_from_list", request, function(data, status, xhr) {
  //                   if ($.isEmptyObject(data) == true && $.isEmptyObject(cache[""]) == false) {
  //                       data = cache[""]
  //                   } else {
  //                       cache[term] = data;
  //                       response(cache[term])
  //                   }
  //               })
  //           }
  //       },
  //       minLength: 0,
  //       autoFocus: true,
  //       select: function(event, ui) {
  //           var label = ui.item.label;          
  //           var category = ui.item.category;
  //           if (this.id == 'to') {
  //               to_airport = ui.item.value
  //           } else if (this.id == 'from') {
  //               from_airport = ui.item.value
  //           }
  //           $(this).siblings('.loc_id_holder').val(ui.item.id);
  //            $("input[name='from_loc_id']").val(ui.item.city_Id);
  //           auto_focus_input(this.id)
  //           //For Multicity-To autofill the next departure city
  //           if($(this).hasClass('m_arrcity') == true && ui.item.value !='') {
  //           	var next_depcity_id = $(this).closest('.multi_city_container').next('.multi_city_container').find('.m_depcity').attr('id');
  //           	if($('#'+next_depcity_id).val() == '') {
	 //            	$('#'+next_depcity_id).val(ui.item.value);
	 //            	$('#'+next_depcity_id).siblings('.loc_id_holder').val(ui.item.id);
  //           	}
  //           }
  //       },
  //      /* change: function(ev, ui) {
  //           if (!ui.item) {
  //               $(this).val("")
  //           }
  //      } */
  //   }).bind('focus', function() {
  //       $(this).catcomplete("search")
  //   }).catcomplete("instance")._renderItem = function(ul, item) {
  //       var auto_suggest_value = highlight_search_text(this.term.trim(), item.label, item.label);
  //       var top = 'Top Searches';
  //       $("input[name='from_loc_id']").val(item.parentId);
  //       return $("<li class='custom-auto-complete'>").append('<a>' + auto_suggest_value + '</a>').appendTo(ul)
  //   };
 
    
  // });
  
var current_module = "sightseen";
  $(".fromactivity1").catcomplete({

        source: function(request, response) {
            var term = request.term;
            //console.log(cache);
            // if (term in cache) {
            //     response(cache[term]);
            //     return
            // }
            $.getJSON(app_base_url + "index.php/ajax/get_sightseen_city_list", request, function(data, status, xhr) {
                //cache[term] = data;
                response(data)
            })
        },
        minLength: 3,
        autoFocus: true,
        select: function(event, ui) {
            var label = ui.item.label;
            var category = ui.item.category;
            $(this).siblings('.loc_id_holder').val(ui.item.id);
            
           // $("#currency_code").val(ui.item.currency_code);         
            //$('#hotel_checkin').focus()
            $.ajax({
                url: app_base_url + "index.php/ajax/get_ss_category_list",
                data: {
                    city_id: ui.item.id
                },
                success: function(cate_res) {
                    
                   // $("#search_hotel_code").empty().html(hotel_res);
                    //$("#category_id").empty().html(cate_res);
                    var cat_str = JSON.parse(cate_res);
                   
                   // var option_cate_list = cat_str.cate_option_list;
                    var check_list_cate = cat_str.cate_check_list;
                     //var option_list =option_cate_list.replace(/\\/g, "");
                     var cate_list = check_list_cate.replace(/\\/g, "");

                    //$("#category_id").empty().html(option_list);
                    $(".category-list").html(cate_list);


                },
                error: function(hotel_res) {
                    console.log("AJAX ERROR");
                }
            });
        },
        change: function(ev, ui) {
            if (!ui.item) {
                $(this).val("")
            }
        }
    }).bind('focus', function() {
        $(this).catcomplete("search")
    }).catcomplete("instance")._renderItem = function(ul, item) {
        var auto_suggest_value = highlight_search_text(this.term.trim(), item.value, item.label);
        var hotel_count = '';
        var count = parseInt(item.count);
        if (count > 0) {
            var h_lab = '';
            if (count > 1) {
                h_lab = 'Hotels'
            } else {
                h_lab = 'Hotel'
            }
            hotel_count = '<span class="hotel_cnt">(' + parseInt(item.count) + ' ' + h_lab + ')</span>'
        }
        return $("<li class='custom-auto-complete'>").append('<a> <span class="fal fa-map-marker-alt"></span> ' + auto_suggest_value + ' ' + hotel_count + '</a>').appendTo(ul)
    };
  });

</script>
<script type="text/javascript">

  $(function(){
      var destination_id = $("#destination_id").val();
      var select_cate_id = "<?php echo $category_id?>";
      var current_module = "sightseen";
      if(destination_id){
        $.ajax({
                url: app_base_url + "index.php/ajax/get_ss_category_list",
                data: {
                    city_id: destination_id,Select_cate_id:select_cate_id
                },
                success: function(cate_res_1) {
                   // console.log(cate_res_1);
                    var cat_str = JSON.parse(cate_res_1);
                   

                   // $("#search_hotel_code").empty().html(hotel_res);
                    //var option_cate_list = cat_str.cate_option_list;
                    var check_list_cate = cat_str.cate_check_list;
                     //var option_list =option_cate_list.replace(/\\/g, "");
                     var cate_list = check_list_cate.replace(/\\/g, "");


                   // $("#category_id").empty().html(option_list);
                    $(".sight_seen_types").html(cate_list);
                },
                error: function(hotel_res) {
                    console.log("AJAX ERROR");
                }
            });
      }
      $("#clear_date").click(function(){
          $("#sight_checkin1").val('');
          $("#sight_checkout1").val('');
      });
  });
</script>
