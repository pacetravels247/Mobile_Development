<?php
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/page_loader_common.js'), 'defer' => 'defer');
?>
<script>
$(document).ready(function() {
	<?php
	/**
	 * DONT DARE TO EDIT THIS FILE
	 */
	if (valid_array(self::$popover) == true) { ?>
		setInterval(function(){provab_popover(jQuery.makeArray( <?php echo json_encode(self::$popover); ?> ));},1);
	<?php
	}
	?>
});
</script>