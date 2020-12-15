<?php 

error_reporting(0);

//debug($tour_list);

foreach($tour_list as $tl => $tl_data)

{

    $TOUR_LIST[$tl_data['id']] = $tl_data;  	 

}

foreach($tour_destinations as $td => $td_data)

{

    $TOUR_DESTINATIONS[$td_data['id']] = $td_data;     

}

//debug($TOUR_DESTINATIONS); //exit;

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

						data-toggle="tab">Confirmed Departure Date List </a></li>

			    <li aria-controls="home"> &nbsp;&nbsp;

					<button class='btn btn-primary'><a href="<?php echo base_url(); ?>index.php/tours/tour_list" style="color:white;">Package List</a></button>

				  </li>								

				</ul>

			</div>

		</div>

		<!-- PANEL HEAD START -->

		<div class="panel-body">

			<!-- PANEL BODY START -->

			

		</div>

		<!-- PANEL BODY END -->

	

	<!-- PANEL WRAP END -->

			<div style="overflow-hidden; overflow-x:scroll;">

			<table class="table table-bordered">

			<thead>

				<tr>

					<th>SN</th>          

          <th>Action</th>       

					<th>Package Code</th>

					<th>Package Name</th>

					<th>Duration</th>

					<th>Dep Date</th>

          <th><i class="fa fa-inr" aria-hidden="true"></i> Price</th>

          <th>Total Seats</th>

          <th>Booked Seats</th>

          <th>Available Seats</th>

          <th>Seat Hold</th>

					<th>Publish Status</th>

				</tr>

      </thead>

      <tbody>  

				<?php

        $sn = 1;
        
      
        foreach ($tour_date_list as $key => $data) { 



        $price    = $data['adult_twin_sharing'];

        $duration = $TOUR_LIST[$data['tour_id']]['duration'];

        if($duration==1)

        { $duration = ($duration).' N | '.($duration+1).' D';}

        else{ $duration = ($duration).' N | '.($duration+1).' D';}	

        if($data['publish_status']==1)

        {

           $status = '<span style="color:green;">Published</span>';

        }

        else

        {

           $status = '<span style="color:red;">Not-Published</span>';

        }

        if($data['booking_hold']==1)

        {

           $booking_hold = '<span style="color:red;">Yes</span>';

        }

        else

        {

           $booking_hold = '<span style="color:green;">No</span>';

        }

        //echo '<td>'.$data['package_id'].'</td> ';

        echo '<tr>

              <td>'.$sn.'</td>';
        echo '<td>';        

        if($data['publish_status']==1)

        {

        echo '<a class="" data-placement="top" href="'.base_url().'index.php/tours/publish_tours_itinerary/'.$data['id'].'/0"

              data-original-title="Deactivate Departure"> <i class="glyphicon glyphicon-th-large"></i> Unpublish

              </a><br>

              ';

        }

        else

        {

        echo '<a class="" data-placement="top" href="'.base_url().'index.php/tours/publish_tours_itinerary/'.$data['id'].'/1"

              data-original-title="Activate Departure"> <i class="glyphicon glyphicon-th-large"></i> Publish

              </a><br>

              ';

        } 

        echo '<a class="" data-placement="top" href="'.base_url().'index.php/tours/seats_tours_itinerary/'.$data['id'].'/'.$data['tour_id'].'/'.$data['dep_date'].'"

              data-original-title="Seats in Tour Itinerary"> <i class="glyphicon glyphicon-pencil"></i> Seats

              </a><br> 
              <a class="callDelete" id="'.$data['id'].'"> 

              <i class="glyphicon glyphicon-trash"></i> Delete</a>

              </td>';

              echo '<td>'.$data['tour_code'].'</td>';                               

              echo '<td>'.string_replace_encode($TOUR_LIST[$data['tour_id']]['package_name']).'</td>';

              echo '<td>'.$duration.'</td>';

              echo '<td>'.($data['dep_date']).'</td>'; 

              echo '<td>'.$price.'</td>'; 

              echo '<td>'.$data['no_of_seats'].'</td>';

              echo '<td>'.$data['total_booked'].'</td>';

              echo '<td>'.$data['available_seats'].'</td>';

              echo '<td>'.$booking_hold.'</td>';

              echo '<td>'.$status.'</td>'; 
       

        echo '</tr>';   

              /*

              <a class="" data-placement="top" href="'.base_url().'tours/edit_tours_itinerary/'.$data['id'].'"

              data-original-title="Edit Tours Itinerary"> <i class="glyphicon glyphicon-pencil"></i> Edit Package

              </a> &nbsp;*/           

        $sn++;

        }

        ?>

		</tbody>

		</table>	

    </div>			

		</div>

		</div>

		<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>-->

		<script type="text/javascript">  

		$(document).ready(function()

		{

           $(".published_status").change(function()

           {

           	    $id = $(this).attr('id'); //alert($id);

           	    if($(this).is(":checked"))

           	    {

                   $publish_status = 1;

                   $publish_status_message = 'Thanks! This tour is successfully published now.';

           	    }

           	    else

           	    {

                   $publish_status = 0;

                   $publish_status_message = 'Thanks! This tour is successfully unpublished now.';

           	    } 

                $tour_id  = $(this).attr('tour_id');

                $dep_date = $(this).attr('dep_date');

                //alert($tour_id); alert($dep_date);

                $.post('<?php echo base_url();?>index.php/tours/ajax_tour_publish/',{'tour_id':$tour_id,'dep_date':$dep_date,'publish_status':$publish_status},function(data)

		        {

		            //alert(data);

		            if(data==1)

		            {

                       $message = 'Sorry! Tour itinerary is not yet saved for this date.';                     

                       $('#'+$id).prop("checked",false);

		            }

		            else if(data==2)

		            {

		               $message = $publish_status_message;

		            }

		            alert($message);		            

		        });

           });

          $(".callDelete").click(function() { 

            $id = $(this).attr('id'); //alert($id);

		        $response = confirm("Are you sure to delete this record???");

		        if($response==true){ window.location='<?=base_url()?>index.php/tours/delete_tours_itinerary/'+$id; } else{}

           });

		});

    </script>



    <link rel="stylesheet" type="text/css" href="http://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">

    <script type="text/javascript" src="http://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>

    <script> $(function () { $('.table').DataTable(); }); </script>        

