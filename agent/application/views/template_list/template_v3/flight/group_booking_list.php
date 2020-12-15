<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<div class="bodyContent col-md-12">
	<div class="panel panel-default clearfix">
		<div class="panel-head">
			<div class="clearfix">
				<div class="clearfix">
					<?php //echo $GLOBALS['CI']->template->isolated_view('report/make_search_easy'); ?>
					<div class="list_of_sections">
					   <a class="" id="today_booking_data" href="#" onclick="filter_date(this.id);">Today Search</a>
					   <a class="" id="last_day_booking_data" href="#" onclick="filter_date(this.id);">Last Day Search</a>
					   <a class="" id="week_booking_data" href="#" onclick="filter_date(this.id);">One Week Search</a>
					   <a class="" id="month_booking_data" href="#" onclick="filter_date(this.id);">One Month Search</a>
					   <a class="btn btn-primary" id="clear_filter" href="#">Reset Filter</a>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-body">
			<div class="clearfix">
				<table id="example" class="display nowrap" style="width:100%">
			        <thead>
			            <tr>
			                <th>Sl No</th>
			                <th>Refernce No</th>
			                <th>Airline Code</th>
			                <th>Trip Type</th>
			                <th>Departure</th>
			                <th>Arrival</th>
			                <th>Adults</th>
			                <th>Children</th>
			                <th>Infants</th>
			                <th>Name</th>
			                <th>Requested On</th>
			                <th>Action</th>
			            </tr>
			        </thead>
    			</table>
			</div>
		</div>
	</div>
</div>
<div id="add_edit_quote_modal" class="modal fade in" tabindex="-1" role="dialog" aria-hidden="false">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>        
            <h4 class="modal-title">Quote Information <span id="qt_refno"></span></h4>
         </div>
         <div class="modal-body">

 <!-- Here the data will be loaded by jquery -->
<div id="message"></div>
 <br>
 <b>
From: <span id="qt_departure"></span> |
 To: <span id="qt_arrival"></span> | Type: <span id="qt_trip_type"></span> |
 A: <span id="qt_adults"></span> | C: <span id="qt_kids"></span> | I: <span id="qt_infants"></span>
 <b><br>
<!-- End -->

 <form class="form-horizontal" role="form" id="quote_edit_update_form">
   <fieldset form="quote_edit_update_fields">
      <div class="form-group">
      <input type="hidden" id="qt_agent_id">

         <label form="basefare_per_pax" for="header_title" class="col-sm-3 control-label">
         Basefare per Pax</label>                                                
         <div class="col-sm-6">
         <input type="number" id="basefare_per_pax" class="form-control" placeholder="Base Fare Per Pax" 
         name="basefare_per_pax" required>                                                
         </div>
      </div>
      <div class="form-group">
         <label form="tax_per_pax" for="header_icon" class="col-sm-3 control-label">
         Tax per Pax</label>                                                
         <div class="col-sm-6">
         <input type="number" id="tax_per_pax" class="form-control" placeholder="Tax per Pax" name="tax_per_pax" required>
         </div>
      </div>
      <div class="form-group">
         <label form="total_basefare" for="header_title" class="col-sm-3 control-label">
         Total Base Fare</label>                                                
         <div class="col-sm-6">
         <input type="number" id="total_basefare" class="form-control" readonly>
         </div>
      </div>
      <div class="form-group">
         <label form="total_tax" for="header_icon" class="col-sm-3 control-label">
         Total Tax</label>                                                
         <div class="col-sm-6">
         <input type="number" id="total_tax" class="form-control" readonly>
         </div>
      </div>
   </fieldset>
 </form>
</div>
 <div class="modal-footer">
 <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
 </div>
  </div>
  <!-- /.modal-content -->  
</div>
<!-- /.modal-dialog -->
</div>

<script>
//alert('8');
$(document).ready(function() {
	var query ='';
	build_datatable(query);
	function calculateTotals(a, k, i)
	{
		var total_pax = parseInt(a)+parseInt(k)+parseInt(i);
		var bf_pp = parseInt($("#basefare_per_pax").val());
		var tax_pp = parseInt($("#tax_per_pax").val());
		var total_bf = bf_pp*total_pax;
		var total_tax = tax_pp*total_pax;

		$("#total_basefare").val(total_bf);
		$("#total_tax").val(total_tax);
	}
	$(document).on("click", ".view_group_booking_quote", function(){
		$("#message").text("");

		var current_tr = $(this).closest("tr");
		var refno = current_tr.children("td:nth-child(2)").text().trim();
		var trip_type = current_tr.children("td:nth-child(4)").text().trim();
		var departure = current_tr.children("td:nth-child(5)").text().trim();
		var arrival = current_tr.children("td:nth-child(6)").text().trim();
		var adults = current_tr.children("td:nth-child(7)").text().trim();
		var kids = current_tr.children("td:nth-child(8)").text().trim();
		var infants = current_tr.children("td:nth-child(9)").text().trim();

		$("#qt_refno").text(refno); $("#qt_trip_type").text(trip_type);
		$("#qt_departure").text(departure); $("#qt_arrival").text(arrival);
		$("#qt_adults").text(adults); $("#qt_kids").text(kids);
		$("#qt_infants").text(infants);

		var quote = $(this).data("val");
		if(quote.is_quoted)
		{
			$("#basefare_per_pax").val(quote.bf_pp);
			$("#tax_per_pax").val(quote.tax_pp);
			calculateTotals(adults, kids, infants);
		}
		$("#add_edit_quote_modal").modal("show");
	});
});

function filter_agent(user_id){
	var query ='requested_by='+user_id;
	table.destroy();
	build_datatable(query);
}

function filter_date(selected_date){
	//$this.addclass('active');
	var query ='filter_date='+selected_date;
	table.destroy();
	build_datatable(query);
}

var table;
function build_datatable(query){
	table = $('#example').DataTable({
    	"processing": true,
    	"serverSide": true,
        "ajax": {
        	"url":app_base_url+'index.php/general/getLists?'+query,
        	"type":"POST",
        },
        "searching": false
    });
}
</script>

