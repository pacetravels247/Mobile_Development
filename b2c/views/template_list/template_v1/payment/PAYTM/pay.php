<center><h1>Please do not refresh this page...</h1></center>
	<form method="post" action="<?php echo $pay_data["url"]; ?>" name="f1">
	<table border="1">
		<tbody>
		<?php
		foreach($pay_data["post_data"] as $name => $value) {
			echo '<input type="hidden" name="' . $name .'" value="' . $value . '">';
		}
		?>
		<input type="hidden" name="CHECKSUMHASH" value="<?php echo $pay_data["checksum"]; ?>">
		</tbody>
	</table>
	<script type="text/javascript">
		document.f1.submit();
	</script>
</form>
