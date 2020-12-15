<div id="package_types" class="bodyContent col-md-12">
	<div class="panel panel-default">
		<!-- PANEL WRAP START -->
		<div class="panel-heading">
			<!-- PANEL HEAD START -->
			<div class="panel-title">
				<ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
					<li role="presentation" class="active"><a href="#fromList"
						aria-controls="home" role="tab" data-toggle="tab">
							<h1>Add Traveller Photos</h1>
					</a></li>
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
							<div class='container'>
								<div class='col-sm-10'>
									<div class='' style='margin-bottom: 0;'>
										<div class='box-header blue-background '>
											<div class='actions'>
												<form action='' method="post" enctype="multipart/form-data"
													class='form form-horizontal validate-form'>
													<input type="hidden" name="pckge_id"
														value="<?php echo $package_id;?>">
													<div class='form-group'>
														<label class='control-label col-sm-3'
															for='validation_company'>Add Traveller Display Image</label>
														<div class='col-sm-4 controls'>
															<input type="file" title='Image to add'
																class='add_pckg_elements' data-rule-required='true'
																id='photo' name='traveller' required> <span id="pacmimg"
																style="color: #F00; display: none">Please Upload Package
																Image</span>
														</div>
													</div>
													<div class='form-actions' style='margin-bottom: 0'>
														<div class='row'>
															<div class='col-sm-9 col-sm-offset-3'>
																<button class='btn btn-primary' type='submit'>Add image</button>&nbsp;&nbsp;
																<a href="<?php echo base_url(); ?>supplier/view_with_price" class="btn btn-primary">Go Back</a>
															</div>
														</div>
													</div>
												</form>
											</div>
										</div>
										<div class='box-content box-no-padding'>
											<div class='responsive-table'>
												<div class='scrollable-area'>
													<table
														class='data-table-column-filter table table-bordered table-striped'
														style='margin-bottom: 0;'>
														<thead>
															<tr>
																<th>S.no</th>
																<th>Traveller Photos</th>
																<th>Status</th>
																<th>Action</th>
															</tr>
														</thead>
														<tbody>
													<?php
													
													if (! empty ( $traveller )) {
														$count = 1;
														foreach ( $traveller as $key => $travel ) {
															?>
											<tr>
																<td><?php echo $count; ?></td>
																<td><a data-lightbox='flatty'
																	href='<?php echo $travel->traveller_image; ?>'> <img
																		width="50" title="" alt=""
																		src="<?php echo $GLOBALS['CI']->template->domain_upload_pckg_images($travel->traveller_image); ?>"></a></td>
																<td>
                                      <?php if ($travel->status == '1') { ?>
                                              <img width="25"
																	height="25"
																	src="<?php echo DOMAIN_IMAGE_DIR; ?>active.jpg">
                                      <?php } else { ?>
                                              <img width="25"
																	height="25"
																	src="<?php echo DOMAIN_IMAGE_DIR; ?>inactive.jpg">
                                              <?php } ?>
                                              <?php if ($travel->status == '1') { ?>
                                                  Activated
                                      <?php } else { ?>
                                          <select
																	onchange="activate(this.value);">
																		<option
																			value="<?php echo base_url() ?>supplier/update_traveller_image_status/<?php echo $travel->package_id; ?>/<?php echo $travel->img_id; ?>/1">Activate</option>
																		<option
																			value="<?php echo base_url() ?>supplier/update_traveller_image_status/<?php echo $travel->package_id; ?>/<?php echo $travel->img_id; ?>/0"
																			selected>De-activate</option>
																</select>
                                      <?php } ?>
                        </td>
																<td class="center"><a
																	href="<?php echo base_url() ?>supplier/delete_traveller_img/<?php echo $travel->img_id; ?>/<?php echo $travel->package_id; ?>"
																	data-original-title="Delete"
																	onclick="return confirm('Do you want delete this record');"
																	class="btn btn-danger btn-xs has-tooltip"
																	data-original-title="Delete"> <i class="icon-remove"></i>
																		Delete
																</a></td>

															</tr>		
									<?php $count++; } } ?>	
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

					</form>
					</section>
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