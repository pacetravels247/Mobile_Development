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

<!-- <input type="hidden" id="select_cate" value="<?=$category_id?>"> -->
<form action="<?php echo base_url();?>index.php/general/pre_transferv1_search" 
	autocomplete="off" id="holiday_search" class="activity_search">
  <div class="tabspl forhotelonly">
    <div class="tabrow transferv1_srch">
      <div class="col-md-offset-2 col-md-8 col-sm-12 col-xs-12 nopad">
        <div class="col-md-12 padfive full_smal_tab mobile_width">
          <div class="lablform">Enter Location</div>
          <div class="plcetogo plcemark sidebord">           

            <input type="text" id="hotel_destination_search_name" class="fromactivity normalinput form-control b-r-0 input_nrml" placeholder="Location" name="from" required value="<?php echo @$destination;?>"/>
            <input class="hide loc_id_holder" name="destination_id" type="hidden" id="destination_id" value="<?=@$destination_id?>" >
          </div>
        </div>
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
        
      <input type="text" readonly class="normalinput auto-focus hand-cursor form-control" id="sight_checkin1" placeholder="from date" value="<?php if(!empty($from_date)) { echo $from_date; } else { echo ''; } ?>" name="from_date"/>
        
        
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
          <input type="text" readonly class="normalinput auto-focus hand-cursor form-control" id="sight_checkout1" placeholder="To date" value="<?php if(!empty($to_date)) { echo $to_date; } else { echo ''; } ?>" name="to_date" />
        </div>
      </div>
     
      </div>
      
      <div class="col-xs-12 full_mobile padfive mobile_width">
        <div class="lablform">&nbsp;</div>
        <div class="searchsbmtfot searchbtn">
          <input type="submit" class="searchsbmt searchbtn_inpt" value="Search" />
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


  $(".fromactivity").catcomplete({

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

