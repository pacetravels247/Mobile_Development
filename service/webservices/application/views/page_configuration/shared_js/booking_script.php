<script>
$(document).ready(function() {
	$('#billing-country').on('change', function() {
		if (this.value in city_list_cache) {
			update_city_options(this.value);
		} else {
			fill_city_list(this.value);
		}
	});

	/**
	*Cache city list based on country
	*/
	var city_list_cache = {};
	function fill_city_list(country_name)
	{
		$.get("<?=base_url().'index.php/ajax/get_city_list'?>/"+country_name, function(city_list) {
			city_list_cache[country_name] = city_list;
			update_city_options(country_name)
		});
	}

	/**
	*update city list based on country from cache
	*/
	function update_city_options(country_name)
	{
		$('#billing-city').html(city_list_cache[country_name]);
	}
	fill_city_list($('#billing-country').val());

	$('[type="submit"]').on('click', function(e) {
		var _status = true;
		var _focus = '';
		$('select:required').each(function() {
			if (this.value == 'INVALIDIP') {
				$(this).addClass('invalid-ip');
				if (_status == true) {
					_status = false;
					_focus = this;
				}
			} else if ($(this).hasClass('invalid-ip')) {
				$(this).removeClass('invalid-ip');
			}
		});
		if (_status == false) {
			$('.alert-content').text('Please Fill All The Data To Continue');
			$('.alert-wrapper').removeClass('hide');
			$(_focus).focus();
			e.preventDefault();
		} else {
			$('.alert-wrapper').addClass('hide');
			$('.alert-content').text('');
		}
	});
});
</script>