<div id="package_types" class="bodyContent col-md-12">
  <div class="panel panel-default">
    <!-- PANEL WRAP START -->
    <div class="panel-heading">
      <!-- PANEL HEAD START -->
      <div class="panel-title">
        <ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
          <!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
          <li role="presentation" class="active">
            <a href="#fromList"
              aria-controls="home" role="tab" data-toggle="tab">
              <h1>View Enquiries
              </h1>
            </a>
          </li>
          <!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE END -->
        </ul>
      </div>
    </div>
    <!-- PANEL HEAD START -->
    <div class="panel-body">
      <!-- PANEL BODY START -->
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="fromList">
          <div class="col-md-12">
			<div class='row'>
				<div class='col-sm-12'>
				
				 <div class='actions'>
                      <a href="<?php echo base_url(); ?>supplier/view_with_price">
                      <button class='btn btn-primary' style='margin-bottom: 5px'>
                       Go Back
                      </button>
                      </a> 
                    </div>
                    
					<div class=''
						style='margin-bottom: 0;'>
						<div class='box-content box-no-padding'>
							<div class='responsive-table'>
								<div class='scrollable-area'>
									<table
										class='data-table-column-filter table table-bordered table-striped'
										style='margin-bottom: 0;'>
										<thead>
											<tr>
												<th>S.No</th>
												<th>Name</th>
												<th>Email</th>
												<th>Contact</th>
												<th>Date</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
                          <?php  if(!empty($enquiries)) { $count = 1; 
                        foreach($enquiries as $key => $package) { ?>
                      <tr>
												<td><?php echo $count; ?></td>
												<td><?php echo $package->first_name; ?></td>
												<td><?php echo $package->email; ?></td>
												<td><?php echo $package->phone; ?></td>
												<!--  <td><?php echo $package->address; ?></td>  -->
												<td><?php echo $package->date; ?></td>
												<td class="center">
													<!--  <a class="btn btn-primary btn-xs has-tooltip" data-placement="top" title=""  href="<?php echo base_url(); ?>supplier/send_enq_mail/<?php echo $package->id; ?>"  data-original-title="Send mail">
                                      <i class="icon-envelope"></i>
                                    </a> --> <a
													href="<?php echo base_url(); ?>supplier/delete_enquiry/<?php echo $package->id; ?>/<?php echo $package->package_id; ?>"
													data-original-title="Delete"
													onclick="return confirm('Do you want delete this record');"
													class="btn btn-danger btn-xs has-tooltip"
													data-original-title="Delete">Delete 
												</a>

												</td>

											</tr>   
                  <?php $count++; } } else{
                  	echo '<tr><td colspan="6">No Data Found</td></tr>';
                  } ?>  
                      </tbody>
									</table>
								</div>
							</div>
		</div>
	</div>

</div>
  </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- PANEL BODY END -->
</div>
<!-- PANEL WRAP END -->
</div>
<script type="text/javascript">
        function activate(that) { window.location.href = that; }
    </script>