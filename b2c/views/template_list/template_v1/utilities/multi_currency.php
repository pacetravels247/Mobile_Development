<?php
$app_supported_currency = $this->db_cache_api->get_currency(array('k' => 'country', 'v' => 'country'), array('status' => ACTIVE));
$application_preferred_currency = get_application_currency_preference();
foreach ($app_supported_currency as $cur_key => $cur_val) {
	if ($application_preferred_currency == $cur_key) {
		$selected_currency = 'active';
	} else {
		$selected_currency = '';
	}
	echo '<li class="'.$selected_currency.'"><a href="'.base_url().'index.php/utilities/set_preferred_currency/'.$cur_key.'" class="app-preferred-currency" data-currency="'.$cur_key.'"><strong>'.$cur_val.'</strong></a></li>';
}
?>
<script>
$(document).ready(function($) {
	$('.app-preferred-currency').on('click', function(e) {
		e.preventDefault();
		var _update_currency_url = $(this).attr('href');
		$.get(_update_currency_url, function(response) {
			//tell user about currency update
			if (response.status == true) {
				$('.wrapper').css('opacity', '.1');
				location.reload();
			}
		});
	});
});
</script>