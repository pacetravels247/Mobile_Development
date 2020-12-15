<style type="text/css">
   label.control-label {line-height: 34px; margin: 0;}
   .gnrl_lbl {
      font-size: 16px;
      font-weight: 600;
      color: #333;
   }
</style>
<div id="general_user" class="bodyContent">
<div class="panel panel-primary">
   <div class="panel-heading">
      <!-- PANEL HEAD START -->
      <div class="panel-title">
         <i class="fa fa-edit"></i> Master Commission
      </div>
   </div>
   <div class="panel-body">
      <fieldset>
         <legend><i class=" fa fa-plus"></i> Add Commission </legend>
         <div class="tab-content">
            <div class="org_row">
            <?php if(false){ ?>
               <div class="col-md-6">
                  <label for="new_airline_value" class="col-sm-3 control-label">Group<span class="text-danger">*</span></label>                                
                  <div class="col-md-9">
                     <div class="form-group">
                        <select name="group" id="group" required="" class="group form-control mb10 form_group">
                           <option value=""> -- Select Group -- </option>
                           <?php foreach($groups as $group_key => $group_val){ ?>
                           <option value="<?php echo $group_val['origin']; ?>" <?php if($group_val['type'] == 'system'){ echo 'selected="selected"'; } ?> ><?php echo $group_val['name']; ?></option>
                           <?php }?>
                        </select>
                     </div>
                  </div>
               </div>
            <?php } ?>
               <div class="col-md-3"></div>
               <div class="col-md-6">
                  <label for="new_airline_value" class="col-sm-2 control-label">Modules<span class="text-danger">*</span></label>                                
                  <div class="col-md-10 mtb5">  
                     <?php foreach($active_modules as $active_key => $active_value){
                        if($active_value['course_id'] != 'TTAGINS15741283692' && $active_value['course_id'] != 'VHCID1420613748'){
                        ?>
                     <label class="radio-inline">
                     <input type="radio" <?php if($active_value['course_id'] == 'VHCID1420613784'){ echo 'checked=checked'; } ?> class="module" name="module" value="<?php echo $active_value['course_id']; ?>"> <?php echo $active_value['name']; ?>
                     </label>
                     <?php } } ?>
                  </div>
               </div>
               <div class="col-md-3"></div>
            </div>
            <div class="clearfix"></div>
            <br />
            <div id="display_supplier_list">
            </div>
         </div>
      </fieldset>
   </div>
</div>
<script type="text/javascript">
   $(document).ready(function(){
   	var base_url = '<?php echo base_url(); ?>';
   	$.post( base_url+'index.php/management/get_master_suppliers', { module: $("input[name='module']:checked").val(), trip_type : 'domestic', tp_supplier : '<?=TRAVELPORT_GDS_BOOKING_SOURCE?>' })
   		  .done(function( data ) {
   			$('#display_supplier_list').html(data);		    
   		  });
   	$(document).on('change','.trip_type, #tp_supplier, .module, .group', function(){
   		var tirp_type = $("input[name='trip_type']:checked").val();
         var supplier = $("#tp_supplier").val();
   		if(tirp_type != 'international'){
   			tirp_type = 'domestic';
   		}
         var changed_supplier = $("#tp_supplier").val();
         if(changed_supplier != '<?=TRAVELPORT_ACH_BOOKING_SOURCE?>'){
            supplier = '<?=TRAVELPORT_GDS_BOOKING_SOURCE?>';
         }else{
            supplier = '<?=TRAVELPORT_ACH_BOOKING_SOURCE?>';
         }	
   		//alert($('.spl_lst').find('.selected_trip_type').val());
   		$('.selected_trip_type').val(tirp_type);
   		$('.selected_trip_type_specific').val(tirp_type);
         $('.selected_supplier').val(supplier);
         $('.selected_supplier_specific').val(supplier);
   		$.post( base_url+'index.php/management/get_master_suppliers', { module: $("input[name='module']:checked").val(), group_fk:$('#group').val(), trip_type : tirp_type, tp_supplier : supplier })
   		  .done(function( data ) {
   			$('#display_supplier_list').html(data);		    
   		  });
   	});
   });
</script>