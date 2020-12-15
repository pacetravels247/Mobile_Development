<?php
$active_domain_modules = $GLOBALS ['CI']->active_domain_modules;
//debug($active_domain_modules);
$master_module_list = $GLOBALS ['CI']->config->item ( 'master_module_list' );
//debug($master_module_list);

//array_pop($active_domain_modules);
if (empty ( $default_view )) {
	$default_view = $GLOBALS ['CI']->uri->segment ( 2 );
}
?>
<ul id="myTab" role="tablist" class="nav nav-tabs b2b_navul">
<?php
 $method = $GLOBALS['CI']->uri->segment('2');	
foreach ($master_module_list as $k => $v) {
	if (in_array ( $k, $active_domain_modules )) {
		if ($method == $v) {
			$act_tab = 'active';
		} else {
			$act_tab = '';
		}
	?>
		<li role="presentation" class="<?=$act_tab?>"><a href="<?=base_url("private_management/supplier_credentials/".$v)?>"><i class="<?=get_arrangement_icon($k)?>"></i> <?=$v ?></a></li>
	<?php
	}
}

?>

</ul>
