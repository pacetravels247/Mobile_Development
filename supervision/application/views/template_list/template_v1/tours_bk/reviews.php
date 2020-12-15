<?php 
error_reporting(0);

foreach($tour_list as $tl_key => $tl_data)
{
   $TOUR_LIST[$tl_data['id']]     =  $tl_data['package_name'];
   $TOURS_COUNTRY[$tl_data['id']] =  $tours_country_name[$tl_data['tours_country']];
}
foreach($tours_itinerary as $ti_key => $ti_data)
{
   $TOUR_ITINERARY[$ti_data['id']] =  $ti_data['dep_date'];
}
//debug($TOUR_DESTINATIONS);debug($TOUR_LIST);debug($TOUR_ITINERARY);
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
            data-toggle="tab">Holiday Reviews </a></li>
        </ul>
      </div>
    </div>
    <!-- PANEL HEAD START -->
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
          <th>Name</th>
          <th>Country Code</th>
          <th>Phone</th>
          <th>Email</th>
        <!--   <th>Country</th> -->
          <th>Package Name</th>
          <th>Review Date</th>
          <th>Message</th>
         <!--  <th>Status</th> -->
          <th>Action</th>       
        </tr>
      <thead>
      <tbody>
        <?php
        $sn = 1;
        //debug($TOUR_ITINERARY); 
        foreach ($reviews as $key => $data) { 
        if($data['status']==1)
        {
           $status = '<span style="color:green;">Replied</span>';
        }
        else
        {
           $status = '<span style="color:red;">Pending</span>';
        }

       /* if($data['tours_itinerary_id']!='')
        {
            $dep_date = changeDateFormat($TOUR_ITINERARY[$data['tours_itinerary_id']]);
        }
        else
        {
            $dep_date = '';
        }*/
        $dep_date = changeDateFormat($data['created']);
        echo '<tr>
              <td>'.$sn.'</td>
              <td>'.$data['user_name'].' '.$data['user_lname'].'</td>
              <td>'.$data['pn_country_code'].'</td>
              <td>'.$data['user_mobile'].'</td>
              <td>'.$data['user_email'].'</td>
           
              <td><a href="'.base_url().'index.php/tours/itinerary_dep_date/'.$data['tour_id'].'/'.$TOUR_ITINERARY[$data['tours_itinerary_id']].'">'.$TOUR_LIST[$data['tour_id']].'</a></td> 
              <td>'.$dep_date.'</td>                
              <td>'.$data['comment'].'</td>
              ';
        echo '<td class="center">';
      /*  if($data['status']==1)
        {
        echo '<a class="" data-placement="top" href="'.base_url().'index.php/tours/activation_review/'.$data['origin'].'/0"
              data-original-title="Deactivate Review"> <i class="glyphicon glyphicon-th-large"></i></i> Pending
              </a>';
        }
        else
        {
        echo '<a class="" data-placement="top" href="'.base_url().'index.php/tours/activation_review/'.$data['origin'].'/1"
              data-original-title="Activate Review"> <i class="glyphicon glyphicon-th-large"></i></i> Replied
              </a>';
        } */
        echo '<!--<a class="" data-placement="top" href="'.base_url().'index.php/tours/edit_enquiry/'.$data['origin'].'"
              data-original-title="Edit Review"> <i class="glyphicon glyphicon-pencil"></i> Edit
              </a>-->
              <a class="callDelete" id="'.$data['origin'].'"> 
              <i class="glyphicon glyphicon-trash"></i> Delete</a>';
        echo '</td>';             
        $sn++;
        }
        ?>
    </tbody>
    </table>
    </div>        
    </div>
    </div>
    <script type="text/javascript">  
    $(document).ready(function()
    {
           
          $(".callDelete").click(function() { 
            $id = $(this).attr('id'); //alert($id);
            $response = confirm("Are you sure to delete this record???");
            if($response==true){ window.location='<?=base_url()?>index.php/tours/delete_review/'+$id; } else{}
           });
    });
        </script>
<!--
<script src="/chariot/extras/system/template_list/template_v1/javascript/js/nicEdit/nicEdit.js"></script>
<script type="text/javascript" src="/chariot/extras/system/template_list/template_v1/javascript/js/nicEdit/nicEdit_call.js"></script>

<link rel="stylesheet" href="/chariot/extras/system/template_list/template_v1/javascript/js/datatables/tables.css">
<script src="/chariot/extras/system/template_list/template_v1/javascript/js/datatables/jquery.dataTables.js"></script>
<script src="/chariot/extras/system/template_list/template_v1/javascript/js/datatables/jquery.dataTables.min.js"></script>
<script type="text/javascript">
            $(document).ready(function() {
                $('.table').dataTable();  
            });
</script>
-->
<link rel="stylesheet" type="text/css" href="http://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
<script type="text/javascript" src="http://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<script> $(function () { $('.table').DataTable(); }); </script> 