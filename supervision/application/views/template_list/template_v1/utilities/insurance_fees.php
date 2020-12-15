<?php 
//debug($insurance[0]['amount']);exit;
?>
<!-- HTML BEGIN -->
<div class="bodyContent">
    <div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
        <div class="panel-heading"><!-- PANEL HEAD START -->
            <div class="panel-title"><i class="fa fa-credit-card"></i>  Insurance Amount</div>
        </div>
        <!-- PANEL HEAD START -->
        <div class="panel-body"><!-- PANEL BODY START -->
            <div class="table-responsive" id="checkbox_div">
                <form action="" method="POST" autocomplete="off">


                    <div class="col-sm-12">
                        <label class="col-sm-3 control-label">Status</label>
                        <div class="col-sm-6">
                            <input type="radio" value="1" name="status" <?php if($insurance[0]['status']==1) { echo "checked"; } ?>><b> Active</b>
                            <input type="radio" value="0" name="status" <?php if($insurance[0]['status']==0) { echo "checked"; } ?>><b> In- Active</b>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <br />
                    <div class="col-sm-12">
                        <label class="col-sm-3 control-label">Insurance Amount<span class="text-danger">*</span></label>
                        <div class="col-sm-3">
                            <input type="text" class="numeric" value="<?php echo $insurance[0]['amount'] ?>" name="insurance" maxlength="4"  >
                        </div>
                        <div class="clearfix"></div>
                        <br />
                    </div>
                    <div class="col-sm-12">
                        <label class="col-sm-3 control-label">&nbsp;</label>
                        <div class="col-sm-3">
                            <input type="submit" name="submit" class="btn btn-primary btn-sm">
                        </div>
                    </div>

                </form>
            </div>
        </div><!-- PANEL BODY END -->
    </div><!-- PANEL END -->
</div>
