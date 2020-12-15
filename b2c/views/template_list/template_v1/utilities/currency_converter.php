<div class="panel panel-primary clearfix">
	<div class="panel panel-heading">Currency Converter</div>
	<div class="panel-body">
	<a class="btn btn-sm btn-danger" id="roe-auto-update" href="<?php echo base_url().'index.php/utilities/auto_currency_converter'?>">
		<i class="fa fa-cloud-download"></i> Click Here To Update All The Conversion Rate Automatically
	</a>
	<table border="1" class="table table-striped">
		<tr>
			<th>Sno</th>
			<th>Currency</th>
			<th>ROE With INR as Base Currency</th>
			<th>Action</th>
		</tr>
		<?php
		if (valid_array($converter)) {
			foreach($converter as $key => $value) {
				$update = '<button class="updateButton btn btn-primary btn-sm">Update</button>';
				echo '<tr>
				<td>'.($key+1).'</td>
				<td><label for="'.$value['id'].'">'.$value['country'].'</label></td>
				<td><input type="text" autocomplete="off" name="value" id="'.$value['id'].'" class="form-control" value="'.$value['value'].'" /></td>
				<td>'.$update.'</td>
			</tr>';
			}
		} else {
			echo '<tr><td colspan=4>No Data Found</td></tr>';
		}
		?>
	</table>
	</div>
</div>

<script>
$(document).ready (function () {
	$('.updateButton').on('click', function () {
		var thisRef = this;
		$.post(app_base_url+'index.php/utilities/currency_converter/'+parseFloat($(this).closest('td').siblings().children('[name="value"]').val())+'/'+$(this).closest('td').siblings().children('[name="value"]').attr('id'), function (response) {
			$(thisRef).removeClass('btn-warning');
		});
	});
	
	$('[name="value"]').on('change, keyup', function() {
		$(this).closest('td').siblings().children('.updateButton').addClass('btn-warning');
	});

	$('#roe-auto-update').on('click', function() {
		$(this).text('Please Wait!!!! This Might Take Few Minutes!!!!!!!!!!!!!');
		setTimeout(function() {
			$('body').css('opacity', '.1');
		}, 2000);
		
	});
});
</script>