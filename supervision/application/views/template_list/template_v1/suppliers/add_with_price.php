<input type="hidden" name="api_url" value="<?=base_url()?>">
<section id='content'>
  <div class='container'>
    <div class='row' id='content-wrapper'>
      <div class='col-xs-12'>
        <div class='row'>
          <div class='col-sm-12'>
            <div class='page-header'>
              <h1 class='pull-left'>
                <i class='icon-ok'></i>
                <span>Add Package</span>
              </h1>
              <div class='pull-right'>
                <ul class='breadcrumb'>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class='row'>
          <div class='col-sm-12'>
            <div class='box'>
              <div class='box-header blue-background'>
                <div class='title'>Package Info</div>
                <div class='actions'>
                  <a class="btn box-collapse btn-xs btn-link" href="#"><i></i>
                  </a>
                </div>
              </div>
              <div class='box-content'>
                <form action="<?php echo base_url(); ?>index.php/supplier/add_package_new" method="post"  enctype="multipart/form-data" class='form form-horizontal validate-form'>
                  <input type="hidden" name="a_wo_p" value="a_w">                   
                  <input type="hidden" name="deal" value="0">
                  <div class='form-group'>
                    <label class='control-label col-sm-3' for='validation_name'>Package type</label>
                    <div class='col-sm-4 controls'>
                      <select class='select2 form-control' data-rule-required='true' name='disn' id="disn" onchange='tour(this.value)'>
                        <option value=''> Select Package Type </option>
                        <?php
                          for($l=0;$l<count($package_type_data);$l++)
                          {
                            ?>
                        <option value='<?php echo $package_type_data[$l]->package_types_id; ?>'> <?php echo $package_type_data[$l]->package_types_name; ?>  </option>
                        <?php
                          }
                          ?>
                      </select>
                      <span id="distination" style="color:#F00; display:none; ">validate</span>
                    </div>
                  </div>
                  <div class='form-group'>
                    <label class='control-label col-sm-3'  for='validation_current'>Package Name
                    </label>
                    <div class='col-sm-4 controls'>
                      <div class="controls">
                        <input type="text" name="name" id="name" data-rule-minlength='2' data-rule-required='true' class='form-control'>
                      </div>
                    </div>
                  </div>
                  <div class='form-group' id="price_with" hidden>
                    <label class='control-label col-sm-3'  for='validation_current'>Price Includes
                    </label>
                    <div class="col-sm-3 controls">
                      <!-- <select name="duration" class="mySelectBoxClass flyinputs noleftpad" id="duration"  onchange="show_duration_info(this.value)" size="40">  -->                         
                      <select class='form-control' data-rule-required='true' name='pricee' id="pricee" onchange="show_withprice(this.value)">
                        <option value="1"> With  </option>
                        <option value="0">Without</option>
                      </select>
                    </div>
                  </div>
                  <div class='form-group'>
                    <label class='control-label col-sm-3'  for='validation_current'>Duration
                    </label>
                    <div class="col-sm-4 controls"> 
                      <input type="text" name="duration" data-rule-number='true' data-rule-required='true' class="form-control" id="duration"  onchange="show_duration_info(this.value)" size="40" placeholder="Enter Number Between 1-10">                          
                    </div>
                  </div>
                  <div class='form-group'>
                    <label class='control-label col-sm-3' for='validation_country'>Country</label>
                    <div class='col-sm-4 controls'>
                      <select class='select2 form-control' data-rule-required='true' name='country' id="country">
                        <!--  <input type="text" name="country" id="country" data-rule-required='true' class='form-control'>  -->
                        <option value="">Select Location</option>
                        <?php foreach ($country as $coun) {?>
                        <option value='<?php echo $coun->country_id; ?>'><?php echo $coun->name; ?></option>
                        <?php }?>
                      </select>
                    </div>
                  </div>
                  <div class='form-group'>
                    <label class='control-label col-sm-3'  for='validation_current'>City
                    </label>
                    <div class='col-sm-4 controls'>
                      <select class='select2 form-control' name='cityname' id="cityname" multiple="multiple"  >
                        <option value="">Select city</option>
                      </select>
                    </div>
                  </div>
                  <div class='form-group'>
                    <label class='control-label col-sm-3'  for='validation_current'>Location
                    </label>
                    <div class='col-sm-4 controls'>
                      <input type="text" name="location" id="location" data-rule-required='true' class='form-control'>
                    </div>
                  </div>
                  <div class='form-group'>
                    <label class='control-label col-sm-3' for='validation_company'>Package Dsiplay Image</label>
                    <div class='col-sm-4 controls'>
                      <input type="file" title='Image to add' class='form-control' data-rule-required='true' id='photo' name='photo'>
                      <span id="pacmimg" style="color:#F00; display:none">Please Upload Package Image</span>
                    </div>
                  </div>
                  <div class='form-group'>
                    <label class='control-label col-sm-3' for='validation_name'>Description</label>
                    <div class='col-sm-4 controls'>
                      <textarea name="Description" data-rule-required='true' class="form-control" data-rule-required="true"  cols="70" rows="3" placeholder="Description"></textarea>
                      <!--   <span id="dorigin_error" style="color:#F00;  display:none;"></span> -->
                    </div>
                  </div>
                  <div class='form-group'>
                    <label class='control-label col-sm-3'  for='validation_rating' >Rating  </label>
                    <div class="col-sm-4 controls" >
                      <select class='form-control' data-rule-required='true' name='rating' id="rating" >
                        <option value="0">0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                      </select>
                    </div>
                  </div>
                  <div class='box-header blue-background'>
                    <div class='title'>Price details</div>
                  </div>
                  <div class="box-content">
                    <!--  <div class='form-group'>
                      <label class='control-label col-sm-2'>From Date</label>
                      <div class='input-group col-sm-3' >
                        <input class='form-control' placeholder='MM/DD/YYYY' id="deptDate1" name="sd[]" type='text'>
                        <span class='input-group-addon'>
                          <i class="icon-calendar"></i>
                        </span>
                      </div>
                      <label class='control-label col-sm-2'>To Date</label>
                      <div class='input-group col-sm-3' >
                        <input class='form-control' placeholder='MM/DD/YYYY' id="to_date" name="ed[]" type='text' readonly>
                        <span class='input-group-addon'>
                          <i class="icon-calendar"></i>
                        </span>
                      </div>
                      </div> -->
                    <div class='form-group'>
                      <label class='control-label col-sm-2' for='adult'>Price</label>
                      <div class='input-group col-sm-3' >
                        <input type="text" name="p_price" id="p_price" data-rule-number="true" data-rule-required='true' class='form-control'>
                        <span class='input-group-addon'>
                        <i class="icon-ngn"></i>
                        </span>
                      </div>
                      <!--  <label class='control-label col-sm-2' for='child'>Child Price</label>
                        <div class='input-group col-sm-3' >
                          <input type="text" name="child[]" id="child" data-rule-number='true' data-rule-required='true' class='form-control'>
                          <span class='input-group-addon'>
                            <i class="icon-usd"></i>
                          </span>
                        </div> -->
                    </div>
                    <hr>
                    <div id="addMultiCity"></div>
                    <div class='form-group'>
                      <div id="addCityButton" class="col-lg-2" style="display:none;">
                        <input type="button" class="srchbutn comncolor" id="addCityInput" value="Add Peroid" style="padding:3px 10px;">
                        <input type="hidden" value="1" id="multiCityNo" name="no_of_days">
                      </div>
                      <div id="removeCityButton" class="col-lg-2" style="display:none;">
                        <input type="button" class="srchbutn comncolor" id="removeCityInput" value="Remove Peroid" style="padding:3px 10px;">
                      </div>
                    </div>
                  </div>
                  <div class='box-header blue-background'>
                    <div class='title'>Itinerary</div>
                  </div>
                  <div>
                    <h2></h2>
                  </div>
                  <div class="duration_info" id="duration_info">
                    <div class='form-group'>
                      <label class='control-label col-sm-3'  for='validation_desc'>Itinerary Description
                      </label>
                      <div class='col-sm-4 controls'>
                        <textarea name="desc[]" class="form-control" data-rule-required="true"  cols="70" rows="3" placeholder="Description"></textarea>
                      </div>
                    </div>
                    <div class='form-group'>
                      <label class='control-label col-sm-3' for='validation_company'>Itinerary Image</label>
                      <div class='col-sm-3 controls'>
                        <input type="file" title='Image to add' class='form-control' data-rule-required='true' id='image' name='image'>
                        <span id="pacmimg" style="color:#F00; display:none">Please Upload Itinerary Image</span>
                      </div>
                    </div>
                    <div class='form-group'>
                      <label class='control-label col-sm-3'  for='validation_name'>Days
                      </label>
                      <div class='col-sm-4 controls'>
                        <input type="text" name="days[]" id="days" data-rule-required='true' class='form-control'>
                      </div>
                    </div>
                    <div class='form-group'>
                      <label class='control-label col-sm-3'  for='validation_name'>Place
                      </label>
                      <div class='col-sm-4 controls'>
                        <input type="text" name="Place[]" id="Place" data-rule-required='true' class='form-control'>
                      </div>
                    </div>
                  </div>
                  <div class='box-header blue-background'>
                    <div class='title'>Pricing Policy</div>
                  </div>
                  <div>
                    <h2></h2>
                  </div>
                  <div class='form-group'>
                    <label class='control-label col-sm-3'  for='validation_includes'>Price Includes
                    </label>
                    <div class='col-sm-4 controls'>
                      <!-- <input type="text" name="includes" id="includes" data-rule-required='true' class='form-control'> -->
                      <textarea name="includes" class="form-control" data-rule-required="true"  cols="70" rows="3" placeholder="Price Includes"></textarea>
                    </div>
                  </div>
                  <div class='form-group'>
                    <label class='control-label col-sm-3'  for='validation_excludes'>Price Excludes
                    </label>
                    <div class='col-sm-4 controls'>
                      <textarea name="excludes" class="form-control" data-rule-required="true"  cols="70" rows="3" placeholder="Price Excludes"></textarea>
                    </div>
                  </div>
                  <div class='box-header blue-background'>
                    <div class='title'>Cancellation & Refund Policy</div>
                  </div>
                  <div>
                    <h1></h1>
                  </div>
                  <div class='form-group'>
                    <label class='control-label col-sm-3'  for='validation_advance'>Cancellation In Advance
                    </label>
                    <div class='col-sm-4 controls'>
                      <!-- <input type="text" name="excludes" id="excludes" data-rule-required='true' class='form-control'> -->
                      <textarea name="advance" class="form-control ckeditor" data-rule-required="true"  cols="70" rows="3" placeholder="Cancellation In Advance"></textarea>
                    </div>
                  </div>
                  <div class='form-group'>
                    <label class='control-label col-sm-3'  for='validation_excludes'>Cancellation Penalty
                    </label>
                    <div class='col-sm-4 controls'>
                      <!-- <input type="text" name="excludes" id="excludes" data-rule-required='true' class='form-control'> -->
                      <textarea name="penality" class="form-control ckeditor" data-rule-required="true"  cols="70" rows="3" placeholder="Cancellation Penalty"></textarea>
                    </div>
                  </div>
                  <div class='box-header blue-background'>
                    <div class='title'>Packages Gallary</div>
                  </div>
                  <div>
                    <h1></h1>
                  </div>
                  <div class='form-group'>
                    <label class='control-label col-sm-3' for='validation_company'>Add Images</label>
                    <div class='col-sm-3 controls'>
                      <input type="file" title='upload Photos' class='form-control' data-rule-required='true' value="upload photo" id='traveller' name='traveller[]' multiple>
                      <span id="travel" style="color:#F00; display:none"> Upload  Image</span>
                    </div>
                  </div>
              </div>
              <div class='form-actions' style='margin-bottom:0'>
              <div class='row'>
              <div class='col-sm-9 col-sm-offset-3'>
              <a href="<?php echo base_url(); ?>supplier/view_with_price"><button class='btn btn-primary' type='button'>
              <i class='icon-reply'></i>
              Go Back  
              </button></a>
              <button class='btn btn-primary' type='submit'>
              <i class='icon-save'></i>
              Add
              </button>                                                     
              </div>
              </div>
              </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</section>
<script src="<?=JAVASCRIPT_LIBRARY_DIR?>common.js" type="text/javascript"></script>
<script src="<?=SYSTEM_RESOURCE_LIBRARY?>/validate/jquery.validate.min.js" type="text/javascript"></script>
<script src="<?=SYSTEM_RESOURCE_LIBRARY?>/validate/additional-methods.js" type="text/javascript"></script>
<script src="<?=SYSTEM_RESOURCE_LIBRARY?>/validate/custom.js"></script>
<script type="text/javascript">
  /*   function city_crs(country) {
  alert('dhjd');
   $.ajax({
           url: api_url+'supplier/get_crs_city/' + country,
           dataType: 'json',
           success: function(json) {
               $('select[name=\'cityname\']').html(json.result);
           }
       });
  }*/
     $(document).ready(function(){
         $('#country').on('change', function() {
           $.ajax({
           url: 'get_crs_city/' + $(this).val(),
           dataType: 'json',
           success: function(json) {
               $('select[name=\'cityname\']').html(json.result);
           }
       });
         });
     });
  
     function show_duration_info(duration)
       {
       if(duration=='')
       {
       duration=0;
       }
       if (window.XMLHttpRequest)
       {// code for IE7+, Firefox, Chrome, Opera, Safari
       xmlhttp=new XMLHttpRequest();
       }
       else
       {// code for IE6, IE5
       xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
       }
       xmlhttp.onreadystatechange=function()
       {
       if (xmlhttp.readyState==4 && xmlhttp.status==200)
       {
       document.getElementById("duration_info").innerHTML=xmlhttp.responseText;
       }
       }
       xmlhttp.open("GET","itinerary_loop/"+duration,true);
       xmlhttp.send();
       }
  
     $.validator.addMethod("buga", (function(value) {
       return value === "buga";
     }), "Please enter \"buga\"!");
  
     $.validator.methods.equal = function(value, element, param) {
       return value === param;
     };
  
     $("#addanother").click(function(){
     var addin = '<input type="text" name="ancountry" value="" placeholder="country" class="ma_pro_txt" style="margin:2px;"/><input type="text" name="anstate" placeholder="state" value="" class="ma_pro_txt" style="margin:2px;"/><input type="text" name="ancity" placeholder="city" value="" class="ma_pro_txt" style="margin:2px;"/><div onclick="removeinput()" style="font-weight:bold;cursor:pointer;">Remove</div><br/>';
     $("#addmorefields").html(addin);
  });
  
  function removeinput(){
   $("#addmorefields").html('');
  }
  
       function activate(that) { window.location.href = that; }
  var a;
  $(document).ready(function(){ 
  
  $('#addCityInput').click(function(){
   var cityNo = parseInt($('#multiCityNo').val());
    //alert(cityNo);
    var duration = $('#duration').val();
   var cityNo = cityNo+1;
    var cit = cityNo-1;
   var allCity = '';
   var i = cityNo;
   var s = i-1;
    
   allCity += "<div id='bothCityInputs"+i+"'><div class='form-group'><label class='control-label col-sm-2' for='validation_company'>From Date</label><div class='input-group col-sm-3' ><input class='fromd datepicker2 b2b-txtbox form-control' placeholder='MM/DD/YYYY' id='deptDate"+i+"'  myid='"+i+"' name='sd[]'' type='text'><span class='input-group-addon'><i class='icon-calendar'></i></span></div><label class='control-label col-sm-2' for='validation_name'>To Date</label><div class='input-group col-sm-3' ><input class='form-control b2b-txtbox' placeholder='MM/DD/YYYY' id='too"+i+"' name='ed[]'' type='text' readonly><span class='input-group-addon'><i class='icon-calendar'></i></span><span id='dorigin_error7' style='color:#F00;'></span><span id='dorigin_error' style='color:#F00;'></span><br></div><br></div>";
  
   allCity += "<div class='form-group'><label class='control-label col-sm-2' for='adult'>Adult Price</label><div class='input-group col-sm-3' ><input type='text' name='adult[]' id='adult"+i+"'  myid='"+i+"' data-rule-number='true' data-rule-required='true' class='form-control'><span class='input-group-addon'><i class='icon-usd'></i></span></div><label class='control-label col-sm-2' for='child'>Child Price</label><div class='input-group col-sm-3' ><input type='text' name='child[]' id='child"+i+"'  myid='"+i+"' data-rule-number='true' data-rule-required='true' class='form-control'><span class='input-group-addon'><i class='icon-usd'></i></span></div></div><hr>";
  allCity += '<script>var d1 = $("#deptDate'+cit+'").datepicker("getDate");'+
                 //'var dd = d1.getDate() + 1;var mm = d1.getMonth() + 1;var yy = d1.getFullYear();'+
                 'd1.setDate(d1.getDate() + parseInt(1));'+
                  'var dd = d1.getDate();var mm = d1.getMonth() + 1;var yy = d1.getFullYear();'+
                 'var to_date = (mm) + "/" + dd + "/" + yy;'+
                 //'var to_date = (mm) + "/" + dd + "/" + yy;'+
                 'alert(to_date);'+
                  'var duration = $("#duration").val();'+
                  '$("#deptDate'+i+'").datepicker({'+
                  'dateFormat: "mm/dd/yy",'+
                  'minDate: to_date,'+
                   'onSelect: function(dateStr) {'+
                    'var d1 = $(this).datepicker("getDate");'+  
                    
  
                    'd1.setDate(d1.getDate() + parseInt(duration));'+
                   
                     'var dd = d1.getDate();var mm = d1.getMonth() + 1;var yy = d1.getFullYear();'+
                     'var to_date = (mm) + "/" + dd + "/" + yy;'+
                     '$("#too'+i+'").val(to_date);'+
                       '}'+
                    '});'+
                       '<\/script>'+
                       '</div>';
   //$("#addMultiCity").append("<label class='control-label col-sm-2' for='validation_company'>From</label><div class='col-sm-3 controls'><input name='sd' id='' type='text' class='datepicker2 b2b-txtbox form-control'     />   <span id='dorigin_error6' style='color:#F00;'></span><br></div><label class='control-label col-sm-3' for='validation_name'>To</label><div class='col-sm-3 controls'><input name='ed' id='' type='text' class='datepicker3 b2b-txtbox form-control'   />  <span id='dorigin_error7' style='color:#F00;'></span><span id='dorigin_error' style='color:#F00;'></span></div>");
                         
   $("#addMultiCity").append(allCity);
   if(cityNo>1){
     $("#removeCityButton").show();
   }
   $('#multiCityNo').val(cityNo);
     });
  $('#removeCityInput').click(function(){
   var cityNo = parseInt($('#multiCityNo').val());
   
   var allCity = '';
   if(cityNo >1){
     $("#bothCityInputs"+cityNo).remove();
     var cityNo = cityNo-1;
     if(cityNo>1){
       $("#removeCityButton").show();
   }
   }
   else
      {
     $("#removeCityButton").hide();
      }
   $('#multiCityNo').val(cityNo);
  });    
  });
    $(document).ready(function(){ 
  
  $(document).on("change",".fromd",function(){ 
     current_date = $(this).val();
     
   current_id = $(this).attr('id');
   // alert(current_id);
  $(".fromd").each(function(){ 
     previous_dates = $(this).val();
      //alert(previous_dates);
     currenr_id=$(this).attr('id');
  
      
     if(current_date == previous_dates && current_id != currenr_id){
   myid=$("input[type='text']#"+current_id).attr('myid');
     alert("Already Same Date Selected");
     $("#"+current_id).val(" ");
    // alert(myid);
      $("#to"+myid).val(" ");
        $("#too"+myid).val(" ");
  }
   });
  });
  });
  
    $('#validation_country').on('change', function(){
        var country=$(this).val();
        $.ajax({
            type:"POST",
            url: "<?php echo base_url(); ?>supplier/get_cities/"+country,
            data:{country:country},
            success:function(wcity)
            {
              $('#city').html(wcity);
            }
          });
      });
</script>