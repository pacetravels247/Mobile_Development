<div class="bodyContent col-md-12">
	<div class="panel panel-default clearfix">
		<div class="panel-heading">
			<?=$GLOBALS['CI']->template->isolated_view('private_management/module_tabs')?>
		</div>
		<div class="panel-body">
			<ul>
				<?php foreach($apis AS $key=>$api) { ?>
					<li>
					<div><?=$api["description"]." (".$api["source_id"].")"; ?></div>
					<div>
					<a href="#" class="edit_supp_conf" data-s_id="<?=$api["source_id"]?>" data-mode="live">
					Update Live Credentials
					</a>
					</div>
					<div>
					<a href="#" class="edit_supp_conf" data-s_id="<?=$api["source_id"]?>" data-mode="test">	
					Update Test Credentials
					</a>
					</div>
					</li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>

<!-- MODAL TO UPDATE CREDENTIALS -->
<div id="upd_supp_cred_modal" class="modal fade in">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>        
            <h4 class="modal-title">Update Supplier Credentials</h4>
         </div>
         <div class="modal-body">
            <!-- Here the data will be loaded by jquery -->
            <div id="message"></div>
                  <form class="form-horizontal" role="form" id="upd_supp_cred_form">
                  <input type="hidden" id="booking_scource_id" />
                  <input type="hidden" id="mode" />
                     <fieldset id="upd_supp_cred_fields">
                       
                     </fieldset>
                     <div class="form-group">
                        <div class="col-sm-8 col-sm-offset-4"> 
                           <button class="btn btn-success" type="submit" id="save_supp_cred_btn">Save</button>
                           <button class=" btn btn-warning " id="reset_form" type="reset">Reset</button>
                        </div>
                     </div>
                  </form>
         </div>
               <div class="modal-footer"> <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button> </div>
      </div>
   </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	var module = "<?php echo $module ?>";
	$(".edit_supp_conf").click(function(){
		var bs_id = $(this).data("s_id");
		var mode = $(this).data("mode");
		$.ajax({
			url: app_base_url+'index.php/private_management/get_supplier_credentials/'+bs_id+'/'+mode+'/'+module,
			type: "post",
			dataType: "html",
			success: function(resp){
				$("#booking_scource_id").val(bs_id);
				$("#mode").val(mode);
				$("#upd_supp_cred_fields").html(resp);
				$("#upd_supp_cred_modal").modal("show");
			}
		});
	});
	$("#upd_supp_cred_form").submit(function(e){
		e.preventDefault();
		var bs_id = $("#booking_scource_id").val();
		var mode = $("#mode").val();
		$.ajax({
			url: app_base_url+'index.php/private_management/save_supplier_credentials/'+bs_id+'/'+mode+'/'+module,
			type: "post",
			data: $(this).serialize(),
			dataType: "html",
			success: function(resp){
				$("#message").html(resp);
				$("#message").fadeOut(300).fadeIn(300);
				setTimeout(function() { $("#upd_supp_cred_modal").modal("hide"); }, 1000);
			}
		});
	});
});
</script>