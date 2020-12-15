<section id='content'>
        <div class='container'>
          <div class='row' id='content-wrapper'>
            <div class='col-xs-12'>
              <div class='row'>
                <div class='col-sm-12'>
                  <div class='page-header'>
                    <h1 class='pull-left'>
                      <i class='icon-building'></i>
                      <span> View Without Price </span>
                    </h1>
                    <div class='pull-right'>
                      <ul class='breadcrumb'>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class='row'>
                <div class='col-sm-12'>
                  <div class='box bordered-box orange-border' style='margin-bottom:0;'>
                    <div class='box-header blue-background'>
                      <div class='title'>View Without Price</div>
                      
                 

                      <div class='actions'>
                       <a href="<?php echo base_url(); ?>supplier/add_without_price"> <button class='btn' style='margin-bottom:5px'><i class='icon-plus'></i> Add Without Price</button></a>
                       <a  href="#"><i> &nbsp</i></a>
                      </div>
                      
                     
                      
                      
                    </div>
                     <?php if(isset($status)){echo $status;}?>
                    <div class='box-content box-no-padding'>
                      <div class='responsive-table'>
                        <div class='scrollable-area'>
                          <table class='data-table-column-filter table table-bordered table-striped' style='margin-bottom:0;'>
                            <thead>
                              <tr>
                                  <th>S.no</th>
                               	  <th>Package Name</th>
        													<th>Country</th>
                                   <th>City</th>
                                  <th>Image</th>
                                   <th hidden>Deal/No Deal</th>
                                   <th>Status</th>
                                   <th>Home Page</th>
                                   <th>Action</th>
                                
                                
                                 
                              </tr>
                            </thead>
                       			<tbody>
													<?php  if(!empty($newpackage)) { $count = 1; 
												foreach($newpackage as $key => $package) { ?>
											<tr>
												<td><?php echo $count; ?></td>
												<td><?php echo $package->package_name; ?></td>
                        <td><?php $country = $this->Supplierpackage_Model->get_country_name($package->package_country); echo $country->name; ?></td>
                          <td><?php echo $package->package_city; ?></td>
                        <td><a data-lightbox='flatty' href='<?php echo $package->image; ?>'>
                        <img width="50" title="<?= $package->package_name; ?>" alt="<?= $package->package_name; ?>" src="<?php echo $package->image; ?>"></a></td>
                       
											
                      
                    
                       <td hidden> <?php //print_r($package); ?>
                           
                                      <?php if ($package->deals == '1') { ?>
                                              <a class='btn btn-success btn-xs has-tooltip' data-placement='top' title='Deal'>
                                                  <i class='icon-ok'></i>
                                              </a>
                                      <?php } else { ?>
                                              <a class='btn btn-danger btn-xs has-tooltip' data-placement='top' title='No-Deal'>
                                                  <i class='icon-minus'></i>
                                              </a>
                                              <?php } ?>
                                          <select onchange="activate(this.value);">
                                              <?php if ($package->deals == '1') { ?>
                                                  <option value="<?php echo base_url(); ?>packages/update_deal_status/<?php echo 
                                                  $package->package_id; ?>/1" selected>Deal</option>
                                                  <option value="<?php echo base_url(); ?>packages/update_deal_status/<?php echo 
                                                  $package->package_id; ?>/0">No-Deal</option>
                                      <?php } else { ?>
                                              <option value="<?php echo base_url(); ?>packages/update_deal_status/<?php echo $package->package_id; ?>/1" selected>Deal</option>
                                              <option value="<?php echo base_url(); ?>packages/update_deal_status/<?php echo $package->package_id; ?>/0" >No-Deal</option>
                                          </select>
                                      <?php } ?>

				


                        </td>
                        <td>
                                    <?php if ($package->status == '1') { ?>
                                              <a class='btn btn-success btn-xs has-tooltip' data-placement='top' title='Active'>
                                                  <i class='icon-ok'></i>
                                              </a>
                                      <?php } else { ?>
                                              <a class='btn btn-danger btn-xs has-tooltip' data-placement='top' title='In-Active'>
                                                  <i class='icon-minus'></i>
                                              </a>
                                              <?php } ?>
                                          <select onchange="activate(this.value);">
                                              <?php if ($package->status == '1') { ?>
                                                  <option value="<?php echo base_url(); ?>index.php/packages/update_status2/<?php echo 
                                                  $package->package_id; ?>/1" selected>Active</option>
                                                  <option value="<?php echo base_url(); ?>index.php/packages/update_status2/<?php echo 
                                                  $package->package_id; ?>/0">In-Active</option>
                                      <?php } else { ?>
                                              <option value="<?php echo base_url(); ?>index.php/packages/update_status2/<?php echo $package->package_id; ?>/1" >Active</option>
                                              <option value="<?php echo base_url(); ?>index.php/packages/update_status2/<?php echo $package->package_id; ?>/0" selected >In-Active</option>
                                          </select>
                                      <?php } ?>
                                  </td>
                                  <td>
                                    <?php if ($package->home_page == '1') { ?>
                                              <a class='btn btn-success btn-xs has-tooltip' data-placement='top' title='Active'>
                                                  <i class='icon-ok'></i>
                                              </a>
                                      <?php } else { ?>
                                              <a class='btn btn-danger btn-xs has-tooltip' data-placement='top' title='In-Active'>
                                                  <i class='icon-minus'></i>
                                              </a>
                                              <?php } ?>
                                          <select onchange="activate(this.value);">
                                              <?php if ($package->home_page == '1') { ?>
                                                  <option value="<?php echo base_url(); ?>index.php/packages/update_homepage_status2/<?php echo 
                                                  $package->package_id; ?>/1" selected>Active</option>
                                                  <option value="<?php echo base_url(); ?>index.php/packages/<?php echo 
                                                  $package->package_id; ?>/0">In-Active</option>
                                      <?php } else { ?>
                                              <option value="<?php echo base_url(); ?>index.php/packages/<?php echo $package->package_id; ?>/1" >Active</option>
                                              <option value="<?php echo base_url(); ?>index.php/packages/<?php echo $package->package_id; ?>/0" selected >In-Active</option>
                                          </select>
                                      <?php } ?>
                                  </td>
												 <td class="center">
											 
													<a class="btn btn-primary btn-xs has-tooltip" data-placement="top" title=""  href="<?php echo base_url(); ?>index.php/packages/edit_without_price/<?php echo $package->package_id; ?>"  data-original-title="Edit Package">
                                      <!-- <i class="icon-edit"></i> -->P
                                    </a>
                                    <a class="btn btn-primary btn-xs has-tooltip" data-placement="top" title=""  href="<?php echo base_url(); ?>index.php/packages/edit_itinerary/<?php echo $package->package_id; ?>"  data-original-title="Edit Itinerary">
                                      I
                                    </a> 
                                    <a class="btn btn-primary btn-xs has-tooltip" data-placement="top" title=""  href="<?php echo base_url(); ?>index.php/packages/quesans_view/<?php echo $package->package_id; ?>"  data-original-title="View Questions">
                                      <i class="icon-eye-open"></i>
                                    </a>
                                    
                                    <a class='btn btn-primary btn-xs has-tooltip' data-placement='top' title='View Review' href='<?php echo base_url(); ?>index.php/packages/view_reviews/<?=$package->package_id;?>'>
                                              <i class='icon-ticket'></i>
                                            </a>
                                    <a class='btn btn-primary btn-xs has-tooltip' data-placement='top' title='Change Images' href='<?php echo base_url(); ?>index.php/packages/images/<?=$package->package_id;?>/wo'>
                                              <i class='icon-picture'></i>
                                            </a>
 											             <a class="btn btn-primary btn-xs has-tooltip" data-placement="top" title=""  href="<?php echo base_url(); ?>index.php/packages/view_enquiries/<?php echo $package->package_id; ?>/wo"  data-original-title="View Enquiries">
                                    E
                                    </a>
	                                 <a href="<?php echo base_url(); ?>index.php/packages/delete_package/<?php echo $package->package_id; ?>/wo"  data-original-title="Delete"  onclick="return confirm('Do you want delete this record');" class="btn btn-danger btn-xs has-tooltip" data-original-title="Delete"> 
                                  <i class="icon-remove"></i>
                                   </a>
									 
												</td> 
												
											</tr>		
									<?php $count++; } } ?>	
											</tbody>
                            <tfoot>
                              <tr>
                                <th>S.no</th>
                               <th>First Name</th>
    													<th>Country</th>
    													<th>City</th>
                              <th></th>
                              <th></th>
                              <th></th>
                              <!-- <th></th> -->
												  
                              </tr>
                            </tfoot>
                          </table>
                        </div>
                      </div> 
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
         <?php $this->load->view('footer');?>
        </div>
      </section>