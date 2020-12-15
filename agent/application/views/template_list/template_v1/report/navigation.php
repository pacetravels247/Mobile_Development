<?php
$active_domain_modules = $GLOBALS ['CI']->active_domain_modules;
$master_module_list = $GLOBALS ['CI']->config->item ( 'master_module_list' );
if (empty ( $default_view )) {
	$default_view = $GLOBALS ['CI']->uri->segment ( 2 );
}
?>
<ul id="myTab" role="tablist" class="nav nav-tabs  ">
	<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
	<?php
		//debug($master_module_list);exit;
		foreach ( $master_module_list as $k => $v ) {
			if (in_array ( $k, $active_domain_modules )) {
				if($v != 'package') {//FIXME: remove later
			?>
		
			<li
				class="<?=((@$default_view == $k || $default_view == $v) ? 'active' : '')?>"><a
				href="<?php echo base_url()?>index.php/report/<?php echo ($v)?>?default_view=<?php echo $k?>"><img
				src="<?php echo $GLOBALS['CI']->template->template_images('icons/'.($k).'-nav-icon.png'); ?>"
				alt="<?php echo strtoupper($v)?> Icon"> <?php echo ucfirst($v)?></a>
			</li>
	<?php } ?>
	<?php
		}
		}
		?>
</ul>