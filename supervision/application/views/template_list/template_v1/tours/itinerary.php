<?php error_reporting(0);?>

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

						data-toggle="tab"> Package Itinerary : [ <?php echo 'Package Name : '.string_replace_encode($tour_data['package_name']);?> ]</a></li>

			        <li aria-controls="home"> &nbsp;&nbsp;

					<button class='btn btn-primary'><a href="<?php echo base_url(); ?>index.php/tours/tour_visited_cities/<?=$tour_id;?>" style="color:white;">City List</a></button>

					<button class='btn btn-primary'><a href="<?php echo base_url(); ?>index.php/tours/tour_list" style="color:white;">Package List</a></button>

				    </li>			

					

				</ul>

			</div>

		</div>

		<!-- PANEL HEAD START -->

		<div class="panel-body">

		<!-- PANEL BODY START -->			

		<?php

		if($tour_dep_dates_list)

		{

			foreach($tour_dep_dates_list as $index => $record)

		{

           //echo '<div class="col-md-2 mB10"><button class="btn btn-default dep_date" dep_date="'.$record['dep_date'].'" tour_id="'.$tour_id.'">'.changeDateFormat($record['dep_date']).'</button></div>';

           if($record['dep_date']==$dep_date)

           { $btn_primary = 'btn-primary'; }

           else{$btn_primary = '';}

           echo '<div class="col-md-2 mB10 hide"><a href="'.base_url().'index.php/tours/itinerary_dep_date/'.$tour_id.'/'.$record['dep_date'].'"class="btn btn-default '.$btn_primary.'" dep_date="'.$record['dep_date'].'" tour_id="'.$tour_id.'">'.changeDateFormat($record['dep_date']).'</a></div>';          

		}

	}else

	{

		echo '<div class="col-md-2 mB10 hide"><a href="'.base_url().'index.php/tours/itinerary_dep_date/'.$tour_id.'/'.$record['dep_date'].'"class="btn btn-default '.$btn_primary.'" dep_date="'.$record['dep_date'].'" tour_id="'.$tour_id.'">MApping</a></div>';          

	}



		

		?>

		<div class="clearfix"></div>	

	    <div id="data">

	    <?php 

	    if($itinerary_page=='ajax_itinerary')

	    {

	    	include "ajax_itinerary.php";

	    }

	    else if($itinerary_page=='ajax_itinerary_stored')

	    {

	    	include "ajax_itinerary_stored.php";

	    }

	    ?>	

	    </div>			

		</div>						

		</div>

		</div>

<script type="text/javascript">

     $(document).ready(function()

     {         

          $('#no_of_nights').on('change', function() { 

          $no_of_nights = $(this).val();  

          $index = $(this).attr('index'); 

          $id = $(this).attr('city-id'); 

          $tour_id = $(this).attr('tour-id'); 

          //alert($no_of_nights); alert($index); alert($id); alert($tour_id);

          // $.post('<?php echo base_url();?>index.php/tours/no_of_nights2/'+$no_of_nights+'/'+$id+'/'+$tour_id,{'no_of_nights':$no_of_nights},function(data)

          // {            

          // 	  $('#itinerary_list'+$index).html(data);             

          // });

          }); 

         

          $('.dep_date').on('click', function() {

          $('.dep_date').removeClass('btn-primary');

          $(this).addClass('btn-primary');	

          $dep_date = $(this).attr('dep_date');           

          $tour_id  = $(this).attr('tour_id'); 

          //alert($dep_date); alert($tour_id);

          $loader = '<img src="/chariot/extras/system/template_list/template_v1/images/loader_v3.gif">';

          $('#data').html($loader);

          $.post('<?php echo base_url();?>index.php/tours/ajax_itinerary/'+$dep_date+'/'+$tour_id,{'tour_id':$tour_id},function(data)

          {

          	  //alert(data);

              $('#data').html(data);                   

          });

          });  

     }); 



</script>