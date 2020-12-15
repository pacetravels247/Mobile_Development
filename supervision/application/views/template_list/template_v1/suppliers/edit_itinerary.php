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
							<h1>Update Package Itinerary</h1>
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
							<div class='row'>
								<div class='col-sm-12'>
									<div class='' style='margin-bottom: 0;'>
										<form class='form form-horizontal validate-form'
											style='margin-bottom: 0;'
											action="<?php echo base_url(); ?>supplier/update_itinerary/"
											method="post" enctype="multipart/form-data">
				<?php $i=0; foreach ($pack_data as $packdata){?>
                <input type="hidden" name="itinerary_id[]"
												value="<?php echo $packdata->iti_id;?>"> <input
												type="hidden" name="package_id"
												value="<?php echo $packdata->package_id;?>">
											<div class="duration_info" id="duration_info">
												<div class='form-group'>
													<label class='control-label col-sm-3' for='validation_desc'>Itinerary
														Description </label>
													<div class='col-sm-4 controls'>
														<textarea name="desc[]" class="form-control"
															data-rule-required="true" value="" cols="70" rows="3"
															placeholder="Description"><?php echo $packdata->itinerary_description;?></textarea>
													</div>
												</div>
												<div class='form-group'>
													<label class='control-label col-sm-3'
														for='validation_company'>Itinerary Image</label>
													<div class='col-sm-3 controls'>
														<input type="file" title='Image to add' class=''
															id='image' name='imagelable<?php echo $i; ?>'> <span
															id="pacmimg" style="color: #F00; display: none">Please
															Upload Itinerary Image</span> <img
															src="<?php echo $GLOBALS['CI']->template->domain_upload_pckg_images(basename($packdata->itinerary_image)); ?>"
															width="100"> <input type="hidden" name="hiddenimage[]"
															value="<?php echo $packdata->itinerary_image; ?>">
													</div>
												</div>
												<div class='form-group'>
													<label class='control-label col-sm-3' for='validation_name'>Days
													</label>
													<div class='col-sm-4 controls'>
														<input type="text" name="days[]" id="days" readonly
															value="<?php echo $packdata->day;?>"
															data-rule-required='true' class='form-control'>
													</div>
												</div>
												<div class='form-group'>
													<label class='control-label col-sm-3' for='validation_name'>Place
													</label>
													<div class='col-sm-4 controls'>
														<input type="text" name="place[]" id="Place"
															value="<?php echo $packdata->place;?>"
															data-rule-required='true' class='form-control'>
													</div>
												</div>
											</div>
											<hr>
                      <?php $i++; }?>
                       <div class='form-actions'
												style='margin-bottom: 0'>
												<div class='row'>
													<div class='col-sm-9 col-sm-offset-3'>
														<a
															href="<?php echo base_url(); ?>supplier/view_with_price">
															<button class='btn btn-primary' type='button'>
																<i class='icon-reply'></i> Go Back
															</button>
														</a>&nbsp;&nbsp;
														<button class='btn btn-primary' type='submit'>
															<i class='icon-save'></i> Update
														</button>
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