<div id="package_types" class="bodyContent col-md-12">
	<div class="panel panel-default">
		<!-- PANEL WRAP START -->
		<div class="panel-heading">
			<!-- PANEL HEAD START -->
			<div class="panel-title">
				<ul class="nav nav-tabs" role="tablist" id="myTab">
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
					<li role="presentation" class="active"><a href="#fromList"
						aria-controls="home" role="tab" data-toggle="tab"><h1>Add / Edit Package Type</h1></a></li>
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
						<div class=''>
							<div class='box-header '>
								<div class='title' id="tab1">  <?php if(isset($status)){echo $status;}?></div>
								<div class='actions'></div>
							</div>
							<div class=''>
								<form class='form form-horizontal validate-form'
									style='margin-bottom: 0;'
									action="<?php echo base_url(); ?>index.php/supplier/save_packages_type"
									method="post" name="frm1" enctype="multipart/form-data">
									<div class='form-group'>
										<label class='control-label col-sm-2' for='validation_name'>Package
											Type</label>
										<div class='col-sm-3 controls'>
										<input type="hidden" name="package_types_id" value="<?=@$pack_data[0]->package_types_id;?>">
											<input class='form-control' data-rule-minlength='2'
												data-rule-required='true' id='pname' name='name' value="<?=@$pack_data[0]->package_types_name;?>"
												placeholder='Tour Type' type='text' required> <span id="pacname"
												style="color: #F00; display: none;">Please Select Package
												Type</span>
										</div>
									</div>
									<div class='form-actions' style='margin-bottom: 0'>
										<div class='row'>
											<div class='col-sm-9 col-sm-offset-4'>
												<a
													href="<?php echo base_url(); ?>index.php/supplier/view_packages_types">
													<button class='btn btn-primary' type='button'>
														<i class='icon-reply'></i> Back
													</button>
												</a>&nbsp;
												<button class='btn btn-primary' type='submit'>
													<i class='icon-save'></i> Submit
												</button>
											</div>
										</div>
									</div>
								</form>
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