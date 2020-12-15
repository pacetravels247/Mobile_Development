<?php 
header('Refresh: 5;'.base_url());
?>
<br>
<div class="" id="myModal_1" tabindex="-1" role="dialog"
	aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
			 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Sorry for the Inconvinience !</h4>
			</div>
			<div class="modal-body">
				<font color="red"><h4 class="modal-title">This Service is
						Temporarily Unavailable !</h4></font> <br>Please Try Again Later.
			</div>
			<div class="modal-footer">
				<a class="hand-cursor" href="<?=base_url()?>">Search Again</a>
			</div>
		</div>
	</div>
</div>