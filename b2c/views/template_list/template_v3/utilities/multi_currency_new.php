<?php
$app_supported_currency = $this->db_cache_api->get_currency(array('k' => 'country', 'v' => array('currency_symbol', 'country')), array('status' => ACTIVE));
$application_preferred_currency = get_application_currency_preference();
//debug($application_preferred_currency);exit;
foreach ($app_supported_currency as $cur_key => $cur_val) {
	$currency = explode(' ',$cur_val);
	if ($currency[0] != ''){
		$symbol = $currency[0];
	}else{
		$symbol = $currency[1];
		}
	if ($application_preferred_currency == $cur_key) {
		$selected_currency = 'active';
	} else {
		$selected_currency = '';
	}
	echo '<li class="currency_li '.$selected_currency.'" data-currency="'.$cur_key.'" data-status="'.$selected_currency.'">
			<a href="'.base_url().'index.php/utilities/set_preferred_currency/'.$cur_key.'" class="app-preferred-currency" data-currency="'.$cur_key.'">
				<span class="curncy_img sprte '.strtolower($cur_key).'"></span>
				<span class="name_currency" > '.$cur_key .'</span>
				<span class="side_curency">'.$symbol.'</span>
			</a>
		</li>';
}
?>
<script>
function currencyConversion(select_currency, currency_symbol) {
// alert(currency_symbol);
$('#flight_search_result').addClass('les_opc');
$('.srch_ldr').removeClass('hide');
	$.ajax({
		type: 'GET',
		url: app_base_url+'index.php/ajax/get_currency_details/'+select_currency,
		async: true,
		cache: true,
		dataType: 'json',
		success: function(res) {
			var decimal_num = 0;
			var currency_conversion_rate = res.value;
			// if(select_currency!="INR"){
				var decimal_num = 2;
			// }

		    $('.filt-currency').html(currency_symbol+" ");
		    $('.clone_side_curr').html(currency_symbol+" ");
		    
		    var minPrice = $('.filt-min_price').data('min_price')*currency_conversion_rate;
		    var maxPrice = $('.filt-max_price').data('max_price')*currency_conversion_rate;
		   
		    var markup_type_min = $('.price:first').data('markup_type');
			var original_markup_min = $('.price:first').data('original_markup');
			var default_curr_rate = res.default_cur_conv_rate;
			// alert(maxPrice);
			if(original_markup_min > 0){

				if(markup_type_min == 'Pecentage'){

					minPrice = minPrice+(minPrice/100)*original_markup_min;
					maxPrice = maxPrice+(maxPrice/100)*original_markup_min;
				}
				else{
					var original_markup_val_min = original_markup_min*default_curr_rate;
					minPrice = minPrice+original_markup_val_min;
					maxPrice = maxPrice+original_markup_val_min;
				}
			}
			$('.mtrxprice').each(function(key, value) {
		    	var price = $('.clone_side_org_price').text()*currency_conversion_rate;
		    	if(original_markup_min > 0){

				if(markup_type_min == 'Pecentage'){
					price = price+(price/100)*original_markup_min;
				}
				else{
					var original_markup_val_min = original_markup_min*default_curr_rate;
					price = price+original_markup_val_min;
				}
			}
				$('.clone_side_price',this).html(price.toFixed(decimal_num));
		    });
			// alert(minPrice);
			// alert(maxPrice);
			$('.filt-min_price').html(minPrice.toFixed(decimal_num));
		    $('.filt-max_price').html(maxPrice.toFixed(decimal_num));

			var cou = 0;
			$('.r-r-i').each(function(key, value) {

				var price = $('.price:first', this).data('original_price')*currency_conversion_rate;
				var markup_type = $('.price:first', this).data('markup_type');
				var original_markup = $('.price:first', this).data('original_markup');
				var base_fare = $('.price:first', this).data('base_price');
				base_fare = base_fare*currency_conversion_rate;
				if(original_markup > 0){
					if(markup_type == 'Pecentage'){
						var offered_price = $('.price:first', this).data('api_offered_fare')*currency_conversion_rate;
						var total_fare = price+(offered_price/100)*original_markup;
						minPrice = minPrice+(offered_price/100)*original_markup;
					}
					else{
						var offered_price = $('.price:first', this).data('api_offered_fare')*currency_conversion_rate;
						// var default_curr_rate = res.default_cur_conv_rate;
						var original_markup_val = original_markup*default_curr_rate;
						var total_fare = offered_price+original_markup_val;
					}
				}
				else{
					var total_fare = offered_price;
				}
				$('.f-p',this).html(total_fare.toFixed(decimal_num));
				$('.price:first', this).attr('data-price', price.toFixed(decimal_num));
				$('.price:first', this).attr('data-currency', select_currency);

				$('.display_currency',this).html(currency_symbol);
				$('.base_currency').text(currency_symbol);
				$('.base_price').html(base_fare.toFixed(decimal_num));
			});
			$('#flight_search_result').removeClass('les_opc');
			$('.srch_ldr').addClass('hide');
		}
	});

}
function bookingPagecurrencyConversion(select_currency, currency_symbol) {
	var api_price_data = $('#api_price_details').data('api_price');
	var api_markup_data = $('#api_markup_details').data('markup_price');
	var convenience_fees_original = $('#convenience_fees_original').data('convience_fee'); 
	
	$('.srch_ldr').removeClass('hide');
	$.ajax({
		type: 'POST',
		data:{ api_price_data:api_price_data,select_currency:select_currency,api_markup_data:api_markup_data,convenience_fees_original:convenience_fees_original },
		url: app_base_url+'index.php/ajax/get_booking_currency_details',
		async: true,
		cache: true,
		dataType: 'json',
		success: function(res) {
			
			$('.grandtotal_span').text(currency_symbol);
			$('.grandtotal_value').text(res.TotalFare);
			$('.convenience_fees_span').text(currency_symbol);
			$('.convenience_fees_value').text(res.convience_fee);
			$('.tax_fees_span').text(currency_symbol);
			$('.tax_fees_value').text(res.TotalTax);
			$('.base_fare_span').text(currency_symbol);
			$('.base_fare_span').text(currency_symbol);
			$('.base_fare_span').each(function(key, value) {
				$('.base_fare_value'+key).text(res.PassngerBasePrice.ADT);
			});
			$('.total_amount_span').text(currency_symbol);
			$('#total_booking_amount').text(res.TotalFare);
			// var decimal_num = 0;
			// var currency_conversion_rate = res.value;
			// $('.change_currency').html(select_currency);
			// // if(select_currency!="INR"){
			// 	var decimal_num = 2;
			// // }
			// var total_booking_amount = $('#total_booking_amount').data('total_booking_amount')*currency_conversion_rate;
			// $('.grand_total_amount,#total_booking_amount').html(total_booking_amount.toFixed(decimal_num));
			// $('.pax_wise_base_fare').each(function(key, value) {
			// 	var pax_wise_base_fare = $(this).data('pax_wise_base_fare');
			// 	$(this).html((pax_wise_base_fare*currency_conversion_rate).toFixed(decimal_num));
			// });

			// $('.base_fare_lop').each(function(key, value) {
			// 	var base_fare = $(this).find(".base_fare").data('base_fare');
			// 	$(this).find(".base_fare").html((base_fare*currency_conversion_rate).toFixed(decimal_num));
			// });

			// var tax = $('.tax').data('tax')*currency_conversion_rate;
			// $('.tax').html(tax.toFixed(decimal_num));
			$('.srch_ldr').addClass('hide');
		}
	});

}

$(document).ready(function($) {
    $('.app-preferred-currency').on('click', function(e) {
        e.preventDefault();
       
        var _update_currency_url = $(this).attr('href');
        $.get(_update_currency_url, function(response) {
        	
            if (response.status == true) {

            	$('.flagss').removeClass('open');
                $('.wrapper').css('opacity', '.1');
                $('.currency_li ').each(function(key, value) {
				if(response.currency.toLowerCase()==$(this).data('currency').toLowerCase()){
				 	 $('.disply_curr').addClass(response.currency.toLowerCase());
				 	 $(this).addClass('active');
				 }else{
				 	$(this).removeClass('active');
				 }
				});
				$('.disply_curr').removeClass();
                $('#disply_curr').addClass('disply_curr curncy_img sprte '+response.currency.toLowerCase());
                $('.flags').html(response.currency);
				currencyConversion(response.currency, response.curr_symbol);
				bookingPagecurrencyConversion(response.currency, response.curr_symbol);
				return false;

                // $('.wrapper').css('opacity', '.1');
                // location.reload()
            }
        })
    })
});

</script>