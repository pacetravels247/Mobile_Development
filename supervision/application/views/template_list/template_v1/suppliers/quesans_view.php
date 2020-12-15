   <section id='content'>
        <div class='container'>
          <div class='row' id='content-wrapper'>
            <div class='col-xs-12'>
              <div class='row'>
                <div class='col-sm-12'>
                  <div class='page-header'>
                    <h1 class='pull-left'>
                      <i class='icon-building'></i>
                      <span>Update Question</span>
                    </h1>
                    <div class='pull-right'>
                      <ul class='breadcrumb'>
                      <!--  <li>
                          <a href='<?php echo WEB_URL; ?>supplier/view_packages_types">'>
                            <i class='icon-bar-chart'></i>
                          </a>
                        </li>
                        <li class='separator'>
                          <i class='icon-angle-right'></i>
                        </li>
                        <li><a href="View Deals"></a></li>
                         <li class='separator'>
                          <i class='icon-angle-right'></i>
                        </li>
                        </li>
                        <li ><a href="<?php echo WEB_URL; ?>supplier/quesans_view/<?php echo $package_id; ?>">Questions</a></li>

                         <li class='separator'>
                          <i class='icon-angle-right'></i>
                         <li class='active'>Answers</li> -->
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class='row'>
                <div class='col-sm-12'>
                  <div class='box bordered-box orange-border' style='margin-bottom:0;'>
                    <div class='box-header blue-background'>
                      <div class='title'>Update Question</div>
                     <div class='actions'>
                       <a href="<?php echo base_url(); ?>supplier/add_question/<?php echo $package_id;?>"> <button class='btn' style='margin-bottom:5px'><i class='icon-plus'></i> Add Questions</button></a>
                       <a  href="#"><i> &nbsp</i></a>
                      </div>
                    </div>
                    <div class='box-content box-no-padding'>
                      <div class='responsive-table'>
                        <div class='scrollable-area'>
                          <table class='data-table-column-filter table table-bordered table-striped' style='margin-bottom:0;'>
                            <thead>
                              <tr>
                              <th>S.no</th>
                               	    <th>Question</th>
          													<!-- <th>Answer</th> -->
                                    <th>Asked By</th>
                                   <!--  <th>Replied By</th> -->
                                    <th>Status</th>
                                    <th>Action</th>
                              </tr>
                            </thead>
                       			<tbody>
													<?php  if(!empty($que_ans)) { $count = 1; 
												foreach($que_ans as $key => $que) { ?>
											<tr>
												<td><?php echo $count; ?></td>
												<td>
                        <?php echo wordwrap($que->question, 60,"<br>\n"); ?>
                         
                           </td>
                        <td><?php if($que->usertype =="8"){
                                $nm = $this->session->userdata('sup_name');
                              }else if($que->user_id =="0" && $que->usertype =="0"){
                                $nm="Guest";
                              } else{
                              $uname=$this->Supplierpackage_Model->userdat($que->user_id); 
                              $nm = $uname->firstname; } echo $nm;?>
                        </td>
                       <!--  <td><?php echo $que->reply_id; ?></td> -->
                       <td>
                                      <?php if ($que->status == '1') { ?>
                                              <img width="25" height="25" src="<?php echo base_url(); ?>img/active.jpg">
                                      <?php } else { ?>
                                              <img width="25" height="25" src="<?php echo base_url(); ?>img/inactive.jpg">
                                              <?php } ?>
                                          <select onchange="activate(this.value);">
                                              <?php if ($que->status == '1') { ?>
                                                  <option value="<?php echo base_url(); ?>index.php/packages/update_answer_status/<?php echo 
                                                  $que->package_id; ?>/<?php echo $que->que_id; ?>/1" selected>Activate</option>
                                                  <option value="<?php echo base_url(); ?>index.php/packages/update_answer_status/<?php echo 
                                                  $que->package_id; ?>/<?php echo $que->que_id; ?>/0">De-activate</option>
                                      <?php } else { ?>
                                              <option value="<?php echo base_url(); ?>index.php/packages/update_answer_status/<?php echo $que->package_id; ?>/<?php echo $que->que_id; ?>/1">Activate</option>
                                              <option value="<?php echo base_url(); ?>index.php/packages/update_answer_status/<?php echo $que->package_id; ?>/<?php echo $que->que_id; ?>/0" selected>De-activate</option>
                                          </select>
                                      <?php } ?>
                        </td>
												 <td class="center">
                          <?php if ($que->status == '1') { ?>
                                    <a class="btn btn-primary btn-xs has-tooltip" data-placement="top" title=""  href="<?php echo base_url(); ?>index.php/packages/view_answers/<?php echo $que->package_id; ?>/<?php echo $que->que_id; ?>"  data-original-title="View Answers">
                                       <i class="icon-eye-open"></i>
                                    </a>
                                    <?php } ?>
                                   <a href="<?php echo base_url(); ?>index.php/packages/delete_answer/<?php echo $que->que_id; ?>/<?php echo $package_id;?>"  data-original-title="Delete"  onclick="return confirm('Do you want delete this record');" class="btn btn-danger btn-xs has-tooltip" data-original-title="Delete"> 
                                  <i class="icon-remove"></i>
                                   </a>

												</td> 
												
											</tr>		
									<?php $count++; } } ?>	
											</tbody>
                            <tfoot>
                              <tr>
                                      <th>S.no</th>
                                      <th>Question</th>
                                      <th>Asked By</th>
                                      <th></th>
                                      <th></th>
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