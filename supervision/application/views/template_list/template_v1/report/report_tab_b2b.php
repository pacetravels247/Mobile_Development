<?php
$active_domain_modules = $GLOBALS ['CI']->active_domain_modules;
//debug($active_domain_modules);
$master_module_list = $GLOBALS ['CI']->config->item ( 'master_module_list' );
//debug($master_module_list);

//array_pop($active_domain_modules);
$query_string = '';
if (isset($_GET['created_by_id']) == true) {
	$query_string = 'created_by_id='.$_GET['created_by_id'];
}
if (empty ( $default_view )) {
	$default_view = $GLOBALS ['CI']->uri->segment ( 2 );
}
?>
<ul id="myTab" role="tablist" class="nav nav-tabs b2b_navul">
<?php
  $method = $GLOBALS['CI']->uri->segment('2');
  $append_query_string = $_SERVER['QUERY_STRING'];
	if(empty($append_query_string) == false) {
		$append_query_string = '?'.$append_query_string;
	}
	
foreach ($master_module_list as $k => $v) {
	if (in_array ( $k, $active_domain_modules )) {
		if ($method == 'b2b_'.$v.'_report') {
			$act_tab = 'active';
		} else {
			$act_tab = '';
		}
	?>
		<li role="presentation" class="<?=$act_tab?>"><a href="<?=base_url()?>index.php/report/b2b_<?=($v).'_report'?><?= $append_query_string ?>"><i class="<?=get_arrangement_icon($k)?>"></i> <?='B2B '.$v.' Report'?></a></li>
	<?php
	}
}

?>

</ul>
