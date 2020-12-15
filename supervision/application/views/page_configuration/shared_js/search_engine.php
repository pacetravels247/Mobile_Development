<script>
//search engine
$(document).ready(function () {
	var search_engineCache = {};
	search_engine("#search_engine");
	search_engine('#search_engine_master');
	function search_engine(ele_id)
	{			
	    var search_category = $(ele_id).data('search_category');
		$(ele_id).autocomplete({
			minLength: 2,
			source: function( request, response ) {
				var term = request.term;
				if ( term in search_engineCache ) {
					response( search_engineCache[ term ] );
					return;
				}
				$.getJSON(app_base_url+"index.php/utilities/search_engine?search_category="+search_category, request, function( data, status, xhr ) {
					search_engineCache[ term ] = data;
					response( data );
				});
			}
		});
	}
});
</script>