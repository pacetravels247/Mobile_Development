<?php 
error_reporting(0);

foreach($tour_list as $tl_key => $tl_data)
{
 $TOUR_LIST[$tl_data['id']]     =  $tl_data['package_name'];
 $TOURS_COUNTRY[$tl_data['id']] =  $tours_country_name[$tl_data['tours_country']];
}

?>
<div id="Package" class="bodyContent col-md-12">
	<div class="panel panel-default">
		<!-- PANEL WRAP START -->
		<div class="panel-heading">
			<!-- PANEL HEAD START -->
			<div class="panel-title">
				<ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
					<li role="presentation" class="active" id="add_package_li"><a
						href="#add_package" aria-controls="home" role="tab"
						data-toggle="tab">Tour Booking Request </a></li>
     </ul>
    </div>
   </div>
   <!-- PANEL HEAD START -->
   <!--      <div class="panel-body">
      <button class="btn btn-primary" type="button" data-toggle="collapse"
        data-target="#advanced_search" aria-expanded="false"
        aria-controls="advanced_search">Advanced Search</button>
      <hr>

      <div class="collapse in" id="advanced_search">
        <form method="GET" autocomplete="off"
          action="<?=base_url().'index.php/tours/tour_booking_request';?>">
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
     </div> -->
     <div class="panel-body">
      <!-- PANEL BODY START -->			
     </div>
     <!-- PANEL BODY END -->

     <!-- PANEL WRAP END -->

     <div class="table-responsive scroll_main">
      <table class="table table-bordered">
       <thead>
        <tr>
         <th>SN</th>
         <th>Action</th>  
         <th>Inquiry Reference No</th>  
         <th>Status</th>
         <th>Name</th>
         <th>Country Code</th>
         <th>Phone</th>
         <th>Email</th>
         <th>Package Name</th>
         <th>Departure Date</th>
         <th>Duration</th>
         <th>No Of Pax</th>
         <th>Message</th>
        </tr>
        <thead>
         <tbody>
          <?php
          $sn = 1;
          foreach ($request_list as $key => $data) { 
           $action = '';           
           $action .= '<a href="#" data-toggle="modal" data-target="#myModal'.$key.'"><i class="fa fa-file-o"></i> Pax Profile</a>';
           // $action .= '<a href="#" data-toggle="modal" data-target="#package'.$key.'"><i class="fa fa-file-o"></i> Package</a>';
           $action .= '<a href="#" data-toggle="modal" data-target="#approve'.$key.'"><i class="fa fa-file-o"></i> Approve</a>';
            $paxhtml = '<div class="modal fade" id="myModal'.$key.'" role="dialog">
            <div class="modal-dialog">
             <div class="modal-content">
              <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title">Passenger Profile</h4>
              </div>
              <div class="modal-body">';
               $paxhtml .= '
               <span>Title : &nbsp;</span><span>'.get_enum_list ( 'title',  $data['title'] ).'</span><br/>
               <span>First Name : &nbsp;</span><span>'.$data['enquiry_details']['name'].'</span><br/>
               <span>Last Name : &nbsp;</span><span>'.$data['enquiry_details']['lname'].'</span><br/>
               <span>Mobile : &nbsp;</span><span>'.$data['enquiry_details']['pn_country_code'].' '.$data['enquiry_details']['phone'].'</span><br/>                        
               <span>Email : &nbsp;</span><span>'.$data['enquiry_details']["email"].'</span><br/>';
               $paxhtml .= '</div>
               <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
               </div>
              </div>
             </div>
            </div>';
            echo $paxhtml;

            /*$packagehtml = '<div class="modal fade" id="package'.$key.'" role="dialog">
            <div class="modal-dialog">
             <div class="modal-content">
              <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title">Package Details</h4>
              </div>
              <div class="modal-body">';
                $duration = $data['tours_details']['duration'];
                if($duration==1)
                { 
                  $duration = ($duration).' N | '.($duration+1).' D';
                }
                else
                { 
                  $duration = ($duration).' N | '.($duration+1).' D';
                }
               $packagehtml .= '<span>Package Name : &nbsp;</span><span>'.$data['tours_details']['package_name'].'</span><br/>
               <span>Country : &nbsp;</span><span>'.$data['tours_details']['country_name'].'</span><br/>                        
               <span>City : &nbsp;</span><span>'.implode(',',$data['tours_details']["city_name"]).'</span><br/>
               <span>Duration : &nbsp;</span><span>'.$duration.'</span><br/>
               ';
               $packagehtml .= '</div>
               <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
               </div>
              </div>
             </div>
            </div>';
            echo $packagehtml;*/

            $approvehtml = '<div class="modal fade" id="approve'.$key.'" role="dialog">
            <div class="modal-dialog">
             <div class="modal-content">
              <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title">Approval</h4>
              </div>
              <div class="modal-body">';
                $approvehtml .= '<form method="POST" role="form" action="'.base_url().'tours/send_payment_link/'.$key.'">              
                  <div class="form-group">
                    <label for="">Final Currency</label>
                    <input type="text" class="form-control" readonly="readonly" value="'.$data['tours_details']['currency'].'">
                  </div>
                  <div class="form-group">
                    <label for="">Final Price</label>
                    <input type="text" class="form-control" id="" name="new_price" value="'.$data['tours_details']['price'].'">
                  </div>
                  <div class="form-group">
                    <button class="btn btn-primary">Send Payment Link</button>
                  </div>
                </form>';
               $approvehtml .= '</div>
               <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
               </div>
              </div>
             </div>
            </div>';
            echo $approvehtml;
           ?>
           <tr>
            <td><?=$sn?></td>
            <td>
             <div class="" role="group">
              <div class="btn-group dropdown_hover" role="group">
               <button id="btnGroupDrop1" type="button"class="btn btn-primary dropdown-toggle" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false"> <span class="glyphicon glyphicon-chevron-right"></span>Actions</button>
               <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                <?php echo $action; ?>
               </div>
              </div>
             </div>
            </td>
            <td><?=$key?></td> 
            <td><?=$data['booking_details']['status']?></td> 
            <td><?=$data['enquiry_details']['name']?></td>
            <td><?=$data['enquiry_details']['pn_country_code']?></td>   
            <td><?=$data['enquiry_details']['phone']?></td>
            <td><?=$data['enquiry_details']['email']?></td>
            <td><a href="<?=base_url()?>tours/voucher/<?=$data['enquiry_details']['tour_id']?>"><?=$data['enquiry_details']['package_name']?></a></td> 
            <td><?=changeDateFormat($data['enquiry_details']['date'])?></td>   
            <td><?=$data['enquiry_details']['durations']?></td>  
            <td>
              Adult(s): <?=$data['pax_details']['adult_count']?>,<br>
              Child(s): <?=$data['pax_details']['child_count']?>,<br>
              Infant(s): <?=$data['pax_details']['infant_count']?>              
            </td>           
            <td><?=$data['enquiry_details']['message']?></td>
            <?php
            $sn++;
           }
           ?>
          </tbody>
         </table>
         </div>
       </div>
		<!-- Send link to user Mail  starts
		<div class="modal fade " id="send_link_to_user" tabindex="-1"
			role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"
							aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title" id="myModalLabel">
							<i class="fa fa-envelope-o"></i> Send link to user
						</h4>
					</div>
					<div class="modal-body">
						<div id="email_voucher_parameters">  
						<form method="POST" autocomplete="off" action="<?=base_url().'index.php/tours/send_link_to_user';?>">
							<input type="hidden" name="tour_id" id="tour_id">
							<input type="hidden" name="enquiry_reference_no" id="enquiry_reference_no">
							<input type="email" id="user_email" name="user_email"
								class="form-control" value="" required="required"
								placeholder="Enter Email">
							
							<div class="row">
								<div class="col-md-4">
									<input type="submit" value="Send Link" class="btn btn-success"
										id="send_mail_btn">
								</div>
						</form>
								<div class="col-md-8">
									<strong id="mail_voucher_error_message" class="text-danger"></strong>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		Mail -   ends-->

 </div>
 <script type="text/javascript">  
  $(document).ready(function()
  {
			/*$('.send_link_to_user_class').on('click', function(e) {
				var tour_id = $(this).data('tour_id');
				var enquiry_reference_no = $(this).data('enquiry_reference_no');
				$('#enquiry_reference_no').val(enquiry_reference_no);
				$('#tour_id').val(tour_id);
			});*/

   $(".callDelete").click(function() { 
            $id = $(this).attr('id'); //alert($id);
            $response = confirm("Are you sure to delete this record???");
            if($response==true){ window.location='<?=base_url()?>index.php/tours/delete_enquiry/'+$id; } else{}
           });
  });
 </script>
 <link rel="stylesheet" type="text/css" href="http://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
 <script type="text/javascript" src="http://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
 <script> $(function () { $('.table').DataTable(); }); </script> 
 <?php 
 function send_link_to_user($enquiry_reference_no,$tour_id)
 {

  return '<a data-toggle="modal" data-target="#send_link_to_user" id="send_link_to_user_id" class="send_link_to_user_class fa fa-envelope-o" data-enquiry_reference_no="'.$enquiry_reference_no.'" data-tour_id="'.$tour_id.'"> Send Link</a>';
 }
 ?>