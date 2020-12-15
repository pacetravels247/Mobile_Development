<div id="Package" class="bodyContent col-md-12">
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-title">
				<ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
					<li role="presentation" class="active" id="add_package_li"><a
						href="#add_package" aria-controls="home" role="tab"
						data-toggle="tab">Tours Inquiry </a></li>
     </ul>
    </div>
   </div>
   <?php /* ?><div class="panel-body">
    <button class="btn btn-primary" type="button" data-toggle="collapse"
    data-target="#advanced_search" aria-expanded="false"
    aria-controls="advanced_search">Advanced Search</button>
    <hr>
    <div class="collapse in" id="advanced_search">
     <form method="GET" autocomplete="off"
     action="<?=base_url().'index.php/tours/tours_enquiry';?>">
     <div class="clearfix form-group">
      <div class="col-xs-4">
       <label> Package Name </label> <input type="text"
       class="form-control" name="package_name"
       value="<?=@$package_name?>" placeholder="Package Name">
      </div>
      <div class="col-xs-4">
       <label> Phone </label> <input type="text" class="form-control"
       name="phone" value="<?=@$phone?>" placeholder="Phone">
      </div> 
      <div class="col-xs-4">
       <label> Email </label> <input type="text" class="form-control"
       name="email" value="<?=@$email?>" placeholder="Email">
      </div>
     </div>
     <div class="col-sm-12 well well-sm">
      <button type="submit" class="btn btn-primary">Search</button>
      <button type="reset" class="btn btn-warning">Reset</button>
     </div>
    </form>
   </div>
  </div><?php */ ?>
  <div class="panel-body">
  </div>
  <div class="table-responsive scroll_main">
   <table class="table table-bordered">
    <thead>
     <tr>
      <th>SN</th>
      <th>Status</th>  
      <th>Reference No</th>
      <th>Title</th>
      <th>Name</th>
      <th>Phone</th>
      <th>Email</th>
      <th>Package Name</th>
      <th>Inquiry Date</th>
      <th>Departure Date</th>
     </tr>
     </thead>
      <tbody>
	  <?php
	  foreach($tours_enquiry AS $key=>$te){
			if($te['te_status']==1)
				$status = '<span style="color:green;">Replied</span>';
			else
				$status = '<span style="color:red;">Pending</span>';
	  ?>
	  <tr>
       <td><?=++$key?></td>
	   <td><?=$status?></td>
	   <td><?=$te["te_ref_no"]?></td>
	   <td><?=get_enum_list( 'title', $te['te_title'] )?></td>
	   <td><?=$te["te_name"]?></td>
	   <td><?=$te["te_phone"]?></td>
	   <td><?=$te["te_email"]?></td>
	   <td><a href="<?php echo base_url('index.php/tours/voucher/'.$te["tet_id"]); ?>"><?=$te["tep_name"]?></a></td>
	   <td><?=$te["te_date"]?></td>
	   <td><?=$te["te_dep_date"]?></td>
	  </tr>
	  <?php } ?>
	</tbody>
  </table>
 </div>				
</div>
  </div>