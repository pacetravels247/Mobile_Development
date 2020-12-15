<?php error_reporting(0);?>
<div class="bodyContent">
	<div class="panel <?=PANEL_WRAPPER?>">
		<!-- PANEL WRAP START -->
		<div class="panel-heading">
			<!-- PANEL HEAD START -->
			<div class="panel-title">
				<i class="fa fa-edit"></i> Edit Top Tour Destinations
			</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body">
			<!-- PANEL BODY START -->
			<fieldset>
				<legend>
					<i class="fa fa-holidays"></i> Edit Top Tour Destinations
				</legend>
				<form action="<?php echo base_url(); ?>index.php/cms/top_tour_destinations_save"
					enctype="multipart/form-data" class="form-horizontal" method="POST"
					autocomplete="off">

					<div class="form-group">
						<label form="user" for="title" class="col-sm-3 control-label">Main Destination<span
							class="text-danger">*</span></label>
						<div class="col-sm-6">
							<select name="tour_destination" id="tour_destination" class="form-control" required="">
							    <option value="">Choose Destination</option>
                                <?php
                                foreach($tour_destinations as $tour_destinations_key => $tour_destinations_value)
                                {
                                	if($tour_destinations_details['id']==$tour_destinations_value['id'])
                                	{$selected = 'selected';}else{$selected='';}
    echo '<option value="'.$tour_destinations_value['id'].'" '.$selected.'>'.$tour_destinations_value['destination'].' [ '.$tour_destinations_value['type'].' ]</option>';
                                }
                                ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label form="user" for="title" class="col-sm-3 control-label">Title<span
							class="text-danger">*</span></label>
						<div class="col-sm-6">
							<input type="text" name="title" id="title" class="form-control" required="required" placeholder="Enter Title" VALUE="<?=$tour_destinations_details['cms_title']?>">
						</div>
					</div>

					<div class="form-group">
						<label form="user" for="title" class="col-sm-3 control-label">Uploaded Imgae<span
							class="text-danger">*</span></label>
						<div class="col-sm-6">
<img src="<?=$GLOBALS ['CI']->template->domain_images ($v['image']).$tour_destinations_details['cms_image']?>" width=50%>
						</div>
					</div>

					<div class="form-group">
						<label form="user" for="title" class="col-sm-3 control-label">Image<span
							class="text-danger">*</span></label>
						<div class="col-sm-6">
							<input type="file" class="" accept="image/*" name="top_destination" id="top_destination">
						</div>
					</div>

					<div class="form-group">
						<label form="user" for="title" class="col-sm-3">&nbsp;</label>
						<div class="col-sm-6">
							<button class=" btn btn-sm btn-success " type="submit">Save</button>
						</div>
					</div>			
				</form>
			</fieldset>
		</div>
		<!-- PANEL BODY END -->
		<div class="panel-body">
			
		</div>
	</div>
	<!-- PANEL WRAP END -->
</div>

