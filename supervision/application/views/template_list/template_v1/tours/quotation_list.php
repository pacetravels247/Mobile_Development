<style type="text/css">
  .crncy_det .row { margin: 0 -15px; }
</style>
<div id="Package" class="bodyContent col-md-12">
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-title">
				<ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
					<li role="presentation" class="active" id="add_package_li"><a
						href="#add_package" aria-controls="home" role="tab"
						data-toggle="tab"> Quotation List </a></li>
         </ul>
       </div>
     </div>
     <div class="table-responsive scroll_main">
       <table class="table table-bordered">
        <thead>
         <tr>
          <th>SN</th>
          <th>Quote Reference</th>  
          <th>Inquiry Reference No</th>
          <th>Package Name</th>
          <th>Quote Type</th>  
          <th>Quoted Price</th>  
          <th>Customer Details</th>
          <th>Agent Name</th>
          <th>Phone</th>
          <th>Email</th>
          <th>Quoted On</th>
        </tr>
        <thead>
          <tbody>
           <?php
           $sn = 1;
           foreach ($quotation_list as $key => $data) { 
            $user_attributes = json_decode($data['user_attributes'],true);

            ?>
            <tr>
            <td><?=$sn?></td> 
            <td><?=$data['quote_reference']?></td>
            <td><?=($data['enquiry_reference_no'])? $data['enquiry_reference_no'] : $user_attributes['booking_type']?></td>
            <td><a href="<?=base_url().'index.php/tours/voucher/'.$data['tour_id']?>"><?=$data['package_name']?></a></td>
            <td><?=ucwords(str_replace('_', ' ',json_decode($data['user_attributes'],true)['quote_type']))?></td>
            <td><?=$data['currency_code']?> <?=$data['quoted_price']?></td>
            <td><?=get_enum_list('title',$data['title'])?> <?=ucfirst($data['first_name'])?> <?=ucfirst($data['middle_name'])?> <?=ucfirst($data['last_name'])?></td>
            <td><?=get_enum_list('title',$data['a_title'])." ".ucfirst($data['a_f_name'])." ".ucfirst($data['a_l_name'])?></td>
            <td><?=$data['phone']?></td>
            <td><?=$data['email']?></td>
            <td><?=date('d M Y H:i',strtotime($data['created_datetime']))?></td>
            </tr>
             <?php
             $sn++;
           }
           ?>
         </tbody>
       </table>
     </div>				
   </div>
 </div>
 <link rel="stylesheet" type="text/css" href="http://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
 <script type="text/javascript" src="http://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
 <script> $(function () { $('.table').DataTable(); }); </script> 