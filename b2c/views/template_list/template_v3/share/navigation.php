<?php
$active_domain_modules = $GLOBALS ['CI']->active_domain_modules;
$master_module_list = $GLOBALS ['CI']->config->item ( 'master_module_list' );
if (empty ( $default_view )) {
	$default_view = $GLOBALS ['CI']->uri->segment ( 2 );
}
?>
<ul id="myTab" role="tablist" class="nav nav-tabs  central_tab">
	<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
	<?php
	//debug($master_module_list);exit;
		foreach ( $master_module_list as $k => $v ) {
			if (in_array ( $k, $active_domain_modules )) {
				if($v != 'holidays') {//FIXME: remove later
			?>
		
			<li
				class="ff <?=((@$default_view == $k || $default_view == $v) ? 'active' : '')?>"><a
				href="<?php echo base_url()?>index.php/report/<?php echo ($v)?>"> <?php echo ucfirst($v)?></a>
			</li>
	<?php }else{ ?>
		<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Holiday<span class="caret"></span></a>
  <ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">
        <li><a href="<?php echo base_url()?>index.php/report/holidays">package booking</a></li>
      
        <li class="divider"></li>
        <li class="dropdown-submenu">
          <a tabindex="-1" href="#">package enquiry</a>
          <ul class="dropdown-menu">
           <li><a href="<?php echo base_url()?>index.php/tours/confirmed_tours_enquiry">confirm enquiry</a></li>
            <li class="divider"></li>
				<li><a href="<?php echo base_url()?>index.php/tours/tours_enquiry">pending enquiry</a></li>
            
          </ul>
        </li>
 <li class="divider"></li>
        <li class="dropdown-submenu">
          <a tabindex="-1" href="#">custom enquiry</a>
          <ul class="dropdown-menu">
            <li><a href="<?php echo base_url()?>index.php/tours/confirmed_tours_enquiry">confirm enquiry</a></li>
             <li class="divider"></li>
				<li><a href="<?php echo base_url()?>index.php/report/holiday">pending enquiry</a></li>
            
          </ul>
        </li>
      </ul>
</li>
<?php	} ?>
	<?php
		}
		}
		?>
</ul>
<style type="text/css">
.dropdown-submenu {
  position: relative;
}

.dropdown-submenu>.dropdown-menu {
    top: 10px;
    left: 50%;
    margin-top: -6px;
    margin-left: -1px;
    -webkit-border-radius: 0 6px 6px 6px;
    -moz-border-radius: 0 6px 6px;
    border-radius: 0 6px 6px 6px;
}

.dropdown-submenu:hover>.dropdown-menu {
  display: block;
}

/*.dropdown-submenu>a:after {
  display: block;
  content: " ";
  float: right;
  width: 0;
  height: 0;
  border-color: transparent;
  border-style: solid;
  border-width: 5px 0 5px 5px;
  border-left-color: #ccc;
  margin-top: 5px;
  margin-right: -10px;
}

.dropdown-submenu:hover>a:after {
  border-left-color: #fff;
}*/

.dropdown-submenu.pull-left {
  float: none;
}

.dropdown-submenu.pull-left>.dropdown-menu {
  left: -100%;
  margin-left: 10px;
  -webkit-border-radius: 6px 0 6px 6px;
  -moz-border-radius: 6px 0 6px 6px;
  border-radius: 6px 0 6px 6px;
}
</style>