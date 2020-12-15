<?php
	if (is_array($search_params)) {
		extract($search_params);
	}
	$_datepicker = array(array('created_datetime_from', PAST_DATE), array('created_datetime_to', PAST_DATE));
	$this->current_page->set_datepicker($_datepicker);
	$this->current_page->auto_adjust_datepicker(array(array('created_datetime_from', 'created_datetime_to')));
?>


<div class="container id-daily-sales mt-3 p-0">
	<h3><i class="far fa-list-alt" aria-hidden="true"></i> &nbsp;Daily Sales Report</h3>
	<div class="col-md-12">
		<form method="GET" autocomplete="off">
			<div class="row mt-2 mb-2">
				<div class="col-sm-offset-6 col-sm-2 padfive">
					<input type="text" readonly id="created_datetime_from" class="form-control input-field" name="created_datetime_from" value="<?=@$created_datetime_from?>" placeholder="From Date">
				</div>
				<div class="col-sm-2 padfive">
					<input type="text" readonly id="created_datetime_to" class="form-control disable-date-auto-update input-field" name="created_datetime_to" value="<?=@$created_datetime_to?>" placeholder="To Date">
				</div>
				<div class="col-sm-2 padfive">
					<button class="btn btn-danger form-control input-field">Search</button>
				</div>
			</div>
		</form>

		<div class="row id-col-div">
			<table class="table">
				<tr>
					<td><p><span class="id-bcol-green"></span> Total Seat Booked</p></td>
					<td><p><span class="id-bcol-red"></span> Total Seat Cancelled</p></td>
					<td><p><span class="id-bcol-warning"></span> Commission / Incentive Earned</p></td>
					<td><p><span class="id-bcol-purple"></span> TDS Deducted</p></td>
					<td><p><span class="id-bcol-blue"></span> Total Earning</p></td>
				</tr>
			</table>
		</div>
		<!-- <div class="row id-details">
			<div class="col-sm-4">
				<p>Agent Name: <span>Ismail Dadwad</span></p>
			</div>
			<div class="col-sm-4">
				<p>Date Range Selected: <span>01/08/2020 - 19/09/2020</span></p>
			</div>
			<div class="col-sm-4">
				<p>Report Generated Time: <span>19/09/2020 10:48:15</span></p>
			</div>
		</div>
		<hr class="m-0"> -->
		<div class="row id-fare-div mt-2">


			<div class="col-sm-6 padfive">
				<h4>Flight Details:</h4>
				<table class="table">
					<tr>
						<td>Markup: </td>
						<th><?php echo empty($fdsr["agent_markup"])?0:number_format($fdsr['agent_markup'], 2); ?></th>
						<td>Ticket Fare: </td>
						<th class="id-col-blue"><?php echo empty($fdsr["total_fare"])?0:number_format($fdsr['total_fare'], 2); ?></th>
					</tr>
					<tr>
						<td>Booked: </td>
						<th class="id-col-green"><?php echo $fbooked;?></th>
						<td>Cancelled: </td>
						<th class="id-col-red"><?php echo $fcancelled;?></th>
					</tr>
					<tr>
						<td>Comm: </td>
						<th class="id-col-warning"><?php echo empty($fdsr["agent_comm"])?0:number_format($fdsr['agent_comm'], 2); ?></th>
						<td>TDS: </td>
						<th class="id-col-purple"><?php echo empty($fdsr["agent_tds"])?0:number_format($fdsr['agent_tds'], 2); ?></th>
					</tr>
				</table>
			</div>

			<div class="col-sm-6 padfive">
				<h4>Bus Details:</h4>
				<table class="table">
					<tr>
						<td>Markup: </td>
						<th><?php echo empty($bdsr["agent_markup"])?0:number_format($bdsr['agent_markup'], 2); ?></th>
						<td>Ticket Fare: </td>
						<th class="id-col-blue"><?php echo empty($bdsr["total_fare"])?0:number_format($bdsr['total_fare'], 2); ?></th>
					</tr>
					<tr>
						<td>Booked: </td>
						<th class="id-col-green"><?php echo $bbooked;?></th>
						<td>Cancelled: </td>
						<th class="id-col-red"><?php echo $bcancelled;?></th>
					</tr>
					<tr>
						<td>Comm: </td>
						<th class="id-col-warning"><?php echo empty($bdsr["agent_comm"])?0:number_format($bdsr['agent_comm'], 2); ?></th>
						<td>TDS: </td>
						<th class="id-col-purple"><?php echo empty($bdsr["agent_tds"])?0:number_format($bdsr['agent_tds'], 2); ?></th>
					</tr>
				</table>
			</div>

			<div class="col-sm-6 padfive">
				<h4>Hotel Details:</h4>
				<table class="table">
					<tr>
						<td>Markup: </td>
						<th><?php echo empty($hdsr["agent_markup"])?0:number_format($hdsr['agent_markup'], 2); ?></th>
						<td>Ticket Fare: </td>
						<th class="id-col-blue"><?php echo empty($hdsr["total_fare"])?0:number_format($hdsr['total_fare'], 2); ?></th>
					</tr>
					<tr>
						<td>Booked: </td>
						<th class="id-col-green"><?php echo $hbooked;?></th>
						<td>Cancelled: </td>
						<th class="id-col-red"><?php echo $hcancelled;?></th>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>

<style type="text/css">
	.mt-3{
		margin-top: 30px;
	}
	.id-daily-sales .input-field{
		border-radius: 4px!important;
	}
	.id-daily-sales{
		box-shadow: 0 12px 15px 0 rgba(0,0,0,0.24),0 17px 50px 0 rgba(0,0,0,0.19) !important;
	}
	.p-0{
		padding: 0!important;
	}
	.mt-1{
		margin-top: 10px;
	}
	.mt-2{
		margin-top: 20px;
	}
	.mb-1{
		margin-bottom: 10px;
	}
	.mb-2{
		margin-bottom: 20px;
	}
	.m-0{
		margin: 0;
	}
	.id-daily-sales h3{
		text-align: center;
		background: linear-gradient(96deg,#002042,#0a8ec1);
		color: #fff;
		margin: 0;
		padding: 10px;
	}
	.id-col-div .id-bcol-green, .id-bcol-red, .id-bcol-purple, .id-bcol-warning, .id-bcol-blue{
		padding: 2px 10px;
		margin: 5px;
		border-radius: 4px;
	}
	.id-col-div .id-bcol-green{
		background-color: #00933b;
	}
	.id-col-div .id-bcol-red{
		background-color: #ff0000;
	}
	.id-col-div .id-bcol-purple{
		background-color: #bb4fff;
	}
	.id-col-div .id-bcol-blue{
		background-color: #2064ff;
	}
	.id-col-div .id-bcol-warning{
		background-color: #dbb600;
	}
	.id-col-green{
		color: #00933b;
	}
	.id-col-red{
		color: #ff0000;
	}
	.id-col-purple{
		color: #bb4fff;
	}
	.id-col-blue{
		color: #2064ff;
	}
	.id-col-warning{
		color: #dbb600;
	}
	.id-col-div table td{
		border:1px solid #ccc;
		text-align: center;
	}
	.id-col-div td{
		width: 20%;
	}
	.id-col-div p{
		margin: 0;
	}
	.id-details p{
		font-size: 13px;
		color: #666;
	}
	.id-details span{
		color: #000;
		font-weight: bold;
	}
	.id-fare-div table{
		border:5px solid #ccc;
	}
	.padfive {
	    padding: 0 2px;
	}
	.id-fare-div h4{
		color: #d43f3a;
		font-weight: bold;
	}
	.row_container {
    margin-top: 80px;
}
</style>