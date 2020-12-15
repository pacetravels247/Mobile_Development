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
              <h1>View
                Packages
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
                <div class='' style='margin-bottom: 0;'>
                  <div class=' '>
                    <div class='actions'>
                      <a href="<?php echo base_url(); ?>supplier/add_with_price">
                      <button class='btn btn-primary' style='margin-bottom: 5px'>
                      + Add Package
                      </button>
                      </a> <a href="#"><i> &nbsp</i></a>
                    </div>
                  </div>
                  <?php if(isset($status)){echo $status;}?>
                  <div class='responsive-table'>
                    <div class=''>
                      <div class='scrollable-area'>
                        <table
                          class=' table-striped external'
                          style='margin-bottom: 0;'>
                          <thead>
                            <tr>
                              <th>S.no</th>
                              <th>Package Name</th>
                              <th>Location</th>
                              <th>Image</th>
                              <th>Status</th>
                              <th>Display on HomePage</th>
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                              if (! empty ( $newpackage )) {
                              	$count = 1;
                              	foreach ( $newpackage as $key => $package ) {
                              		?>
                            <tr>
                              <td><?php echo $count; ?></td>
                              <td><?php echo $package->package_name; ?></td>
                              <td><?php echo $package->package_city; ?>,<?php $country = $this->Supplierpackage_Model->get_country_name($package->package_country); echo $country->name; ?></td>
                              <td><a data-lightbox='flatty'
                                href='<?php echo $package->image; ?>'> <img width="70" height="60"
                                title="<?= $package->package_name; ?>"
                                alt="<?= $package->package_name; ?>"
                                src="<?php echo $GLOBALS['CI']->template->domain_upload_pckg_images($package->image); ?>"></a></td>
                              <td>
                                <select
                                  onchange="activate(this.value);">
                                  <?php if ($package->status == '1') { ?>
                                  <option
                                    value="<?php echo base_url(); ?>supplier/update_status/<?php
                                      echo $package->package_id;
                                      ?>/1"
                                    selected>Active</option>
                                  <option
                                    value="<?php echo base_url(); ?>supplier/update_status/<?php
                                      echo $package->package_id;
                                      ?>/0">In-Active</option>
                                  <?php } else { ?>
                                  <option
                                    value="<?php echo base_url(); ?>supplier/update_status/<?php echo $package->package_id; ?>/1">Active</option>
                                  <option
                                    value="<?php echo base_url(); ?>supplier/update_status/<?php echo $package->package_id; ?>/0"
                                    selected>In-Active</option>
                                </select>
                                <?php } ?>
                              </td>
                              <td>
                                <select
                                  onchange="activate(this.value);">
                                  <?php if ($package->top_destination == '1') { ?>
                                  <option
                                    value="<?php echo base_url(); ?>supplier/update_top_destination/<?php
                                      echo $package->package_id;
                                      ?>/1"
                                    selected>Active</option>
                                  <option
                                    value="<?php echo base_url(); ?>supplier/update_top_destination/<?php
                                      echo $package->package_id;
                                      ?>/0">In-Active</option>
                                  <?php } else { ?>
                                  <option
                                    value="<?php echo base_url(); ?>supplier/update_top_destination/<?php echo $package->package_id; ?>/1">Active</option>
                                  <option
                                    value="<?php echo base_url(); ?>supplier/update_top_destination/<?php echo $package->package_id; ?>/0"
                                    selected>In-Active</option>
                                </select>
                                <?php } ?>
                              </td>
                              <td class="center">
                                <a class="" data-placement="top" title="" 
                                  href="<?php echo base_url(); ?>supplier/edit_with_price/<?php echo $package->package_id; ?>"
                                  data-original-title="Edit Package">
                                  <i class="glyphicon glyphicon-pencil"></i> Edit Package
                                </a>&nbsp;
                                <a class="" data-placement="top" title=""
                                  href="<?php echo base_url(); ?>supplier/edit_itinerary/<?php echo $package->package_id; ?>"
                                  data-original-title="Edit Itinerary"> <i class="glyphicon glyphicon-pencil "></i> Edit Itinerary 
                                </a><br> 
                                <a class='' data-placement='top' title='Change Images'
                                  href='<?php echo base_url(); ?>supplier/images/<?=$package->package_id;?>/w'>
                                <i class='glyphicon glyphicon-th-large'></i> Edit Images
                                </a>&nbsp;
                                <a class="" data-placement="top" title=""
                                  href="<?php echo base_url(); ?>supplier/view_enquiries/<?php echo $package->package_id; ?>/w"
                                  data-original-title="View Enquiries"><i class='glyphicon glyphicon-pencil'></i> Edit Enquiries 
                                </a><br>
                                <a href="<?php echo base_url(); ?>supplier/delete_package/<?php echo $package->package_id; ?>"
                                  data-original-title="Delete" onclick="return confirm('Do you want delete this record');"
                                  class="" data-original-title="Delete"> <i class="glyphicon glyphicon-trash"></i>Delete Package
                                </a>
                              </td>
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
<style>
.external > tbody > tr > td, 
.external > tbody > tr > th, 
.external > tfoot > tr > td, 
.external > tfoot > tr > th, 
.external > thead > tr > td, 
.external > thead > tr > th {
padding: 6px;
}
</style>