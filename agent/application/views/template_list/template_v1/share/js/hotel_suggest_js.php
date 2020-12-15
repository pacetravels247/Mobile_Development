<script>
	$(document).ready(function(){
		var cache = {};
		$(".hotel_city").autocomplete({
			source:  function( request, response ) {
				var term = request.term;
				if ( term in cache ) {
					response( cache[ term ] );
					return;
				}

				$.getJSON( app_base_url+"index.php/ajax/get_hotel_city_list", request, function( data, status, xhr ) {
					cache[ term ] = data;
					response( data );
				});
			},
		minLength: 0,//search after two characters
		autoFocus: true, // first item will automatically be focused
		select: function(event,ui){
			$('#hotel_checkin').focus();
		}
		}).bind('focus', function(){ $(this).autocomplete("search"); } ).autocomplete( "instance" )._renderItem = function( ul, item ) {
			var auto_suggest_value = highlight_search_text(this.term.trim(), item.value, item.label);
			var hotel_count = '';
			var count = parseInt(item.count);
			if (count > 0) {
				var h_lab = '';
				if (count > 1) {
					h_lab = 'Hotels';
				} else {
					h_lab = 'Hotel';
				}
				hotel_count = '<span class="hotel_cnt">('+parseInt(item.count)+' '+h_lab+')</span>';
			}
			return $("<li>").append('<a> <span class="fa fa-map-marker"></span> ' + auto_suggest_value + ' '+ hotel_count+'</a>').appendTo(ul);
		};
	});
</script>