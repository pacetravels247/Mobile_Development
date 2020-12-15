<section id='content'>
        <div class='container'>
          <div class='row' id='content-wrapper'>
            <div class='col-xs-12'>
              <div class='row'>
                <div class='col-sm-12'>
                  <div class='page-header'>
                    <h1 class='pull-left'>
                      <i class='icon-building'></i>
                      <span>View City Request</span>
                    </h1>
                    <div class='pull-right'>
                      <ul class='breadcrumb'>
                       <!--  <li>
                          <a href='index.html'>
                            <i class='icon-bar-chart'></i>
                          </a>
                        </li> -->
                       <!--  <li class='separator'>
                          <i class='icon-angle-right'></i>
                        </li>
                        <li class='active'>Supplier Account Manager</li> -->
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class='row'>
                <div class='col-sm-12'>
                  <div class='box bordered-box orange-border' style='margin-bottom:0;'>
                    <div class='box-header blue-background'>
                      <div class='title'>View City Request</div>
                     <!--  <div class='actions'>
                       <a href="<?php echo base_url(); ?>supplier/add_deals"> <button class='btn' style='margin-bottom:5px'><i class='icon-plus'></i> View City Request</button></a>
                       <a  href="#"><i> &nbsp</i></a>
                      </div> -->
                    </div>
                    <div class='box-content box-no-padding'>
                      <div class='responsive-table'>
                        <div class='scrollable-area'>
                          <table class='data-table-column-filter table table-bordered table-striped' style='margin-bottom:0;'>
                            <thead>
                              <tr>
                              <th>S.no</th>
                                    <th>Country</th>
                          <!-- <th>Package Code</th> -->
                                    <th>City</th>
                                    <th >Status</th>
                                    <th>Action</th>
                              </tr>
                            </thead>
                            <tbody>
                          <?php  if(!empty($addcity)) { $count = 1; 
                        foreach($addcity as $key => $city) { ?>
                      <tr>
                        <td><?php echo $count; ?></td>
                       
                        <!-- <td><?php echo $package->package_country; ?></td> -->
                        <td><?php $country = $this->Supplierpackage_Model->get_country_name($city->country_id); echo $country->name; ?></td>
                        
                        
                        <td><?php echo $city->city; ?></td>
                        <td>
                                      <?php if ($city->status == '1') { ?>
                                            Added
                                      <?php } else { ?>
                                             Pending
                                              <?php } ?>
                                       
                                                                            </td> 


                       <!--  <td><?php echo $package->city; ?></td> -->
                       <td>
                       <a href="<?php echo base_url(); ?>supplier/delete_city_request/<?php echo $city->id; ?>"  data-original-title="Delete"  onclick="return confirm('Do you want delete this record');" class="btn btn-danger btn-xs has-tooltip" data-original-title="Delete"> 
                                  <i class="icon-remove"></i>
                                   </a>
                                   </td>
                       
                   
                       
                       
                         
                        
                         
                        
                      </tr>   
                  <?php $count++; } } ?>  
                      </tbody>
                            <tfoot>
                              <tr>
                                 <th>S.no</th>
                                 <th>Country</th>
                                  <th>City</th>
                                   <th>Status</th> 
                                   
                          
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
         
        </div>
      </section>