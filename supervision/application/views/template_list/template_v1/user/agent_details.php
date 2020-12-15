
<style>
.form-group{	
	/*border:1px solid #ccc;*/
	border-right:none;	
}
.border_right {
	border-right:1px solid #ccc;
}
	.fixed .content-wrapper, .fixed .right-side {
    padding-top: 0px;
}
</style>
            <div class="row">
                <div class="col-sm-6 col-xs-6">
                  <p class="fs-13 mb-0 font-weight-bold"><strong>Agency Details</strong></p>
                </div>
                <div class="col-sm-6 col-xs-6 text-right"><button class="btn btn1 btn-danger mb-2" onclick="window.history.go(-1); return false;"><i class="fa fa-arrow-left"></i>&nbsp; Back </button></a>
                </div>
            </div>
			<div class="row">
                <div class="col-xs-12">
                  <div class="big-shadow bg-white">
                    <div class="box-body">
                     <?php echo $this->session->flashdata('msg'); ?>
                        <div class="table-responsive" id="printBlock">
                             <table class="table text-center border big-shadow">
                                <tr>
                                    <td class="b-right b-top"><span class="font-weight-bold fs-09">First Name</span><hr class='mt-1 mb-1'/><span class="fs-08">
                                        <?php 
                                            if($data['first_name'] == ''){
                                                    echo "No Data Found";
                                            }else{
                                                echo trim($data['first_name']);
                                            }
                                         ?>
                                    </span></td>
                                    <td class="b-right b-top"><span class="font-weight-bold fs-09">Last Name</span><hr class='mt-1 mb-1'/><span class="fs-08">
                                        <?php 
                                            if($data['last_name']==''){
                                                echo "No Data Found";
                                            }else{
                                                echo trim($data['last_name']);
                                            } 
                                        ?>
                                    </span></td>
                                    <td class="b-right b-top"><span class="font-weight-bold fs-09">Agency Name</span><hr class='mt-1 mb-1'/><span class="fs-08">
                                        <?php 
                                            if($data['agency_name']==''){
                                                echo "No Data Found";
                                            }else{
                                                echo trim($data['agency_name']);
                                            } 
                                        ?>
                                    </span></td>
                                    <td class="b-top"><span class="font-weight-bold fs-09">Email</span><hr class='mt-1 mb-1'/><span class="fs-08">
                                        <?php 
                                            if($data['email']==''){
                                                echo "No Data Found";
                                            }else{
                                                echo trim(provab_decrypt($data['email']));
                                            } 
                                        ?>
                                    </span></td>
                                </tr>
                                <tr>
                                    <td class="b-right b-top"><span class="font-weight-bold fs-09">Mobile No</span><hr class='mt-1 mb-1'/><span class="fs-08">
                                        <?php 
                                            if($data['phone']==''){
                                                echo "No Data Found";
                                            }else{
                                                echo trim($data['phone']);
                                            } 
                                        ?>
                                    </span></td>
                                    <td class="b-right b-top"><span class="font-weight-bold fs-09">Username</span><hr class='mt-1 mb-1'/><span class="fs-08">
                                        <?php 
                                            if($data['user_name']==''){
                                                echo "No Data Found";
                                            }else{
                                                echo trim(provab_decrypt($data['user_name']));
                                            } 
                                        ?>
                                    </span></td>
                                    <td class="b-right b-top"><span class="font-weight-bold fs-09">Reference By</span><hr class='mt-1 mb-1'/><span class="fs-08">
                                        <?php 
                                            if($data['referred_by']==''){
                                                echo "No Data Found";
                                            }else{
                                                echo trim($data['referred_by']);
                                            } 
                                        ?>
                                    </span></td>
                                    
                                    <td class="b-top"><span class="font-weight-bold fs-09">Country</span><hr class='mt-1 mb-1'/><span class="fs-08">
                                        <?php 
                                            if($data['country_name']==''){
                                                echo "No Data Found";
                                            }else{
                                                echo trim($data['country_name']);
                                            } 
                                        ?>
                                    </span></td>
                                </tr>
                                <tr>
                                    <td class="b-right b-top"><span class="font-weight-bold fs-09">State</span><hr class='mt-1 mb-1'/><span class="fs-08">
                                        <?php 
                                            if($data['state_name']===''){
                                                echo "No Data Found";
                                            }else{
                                                echo trim($data['state']);
                                            } 
                                        ?>
                                    </span></td>
                                    <td class="b-right b-top"><span class="font-weight-bold fs-09">City</span><hr class='mt-1 mb-1'/><span class="fs-08">
                                        <?php 
                                            if($data['city_name']==''){
                                                echo "No Data Found";
                                            }else{
                                                echo trim($data['city_name']);
                                            } 
                                        ?>
                                    </span></td>
                                    <td class="b-right b-top"><span class="font-weight-bold fs-09">Locality</span><hr class='mt-1 mb-1'/><span class="fs-08">
                                        <?php 
                                            if($data['locality']==''){
                                                echo "No Data Found";
                                            }else{
                                                echo trim($data['locality']);
                                            } 
                                        ?>
                                    </span></td>
                                    <td class="b-top"><span class="font-weight-bold fs-09">Address</span><hr class='mt-1 mb-1'/><span class="fs-08">
                                        <?php 
                                            if($data['address']==''){
                                                echo "No Data Found";
                                            }else{
                                                echo trim($data['address']);
                                            } 
                                        ?>
                                    </span></td>
                                </tr>
                                <tr>
                                    <td class="b-right b-top"><span class="font-weight-bold fs-09">Pincode</span><hr class='mt-1 mb-1'/><span class="fs-08">
                                        <?php 
                                            if($data['pin_code']==''){
                                                echo "No Data Found";
                                            }else{
                                                echo trim($data['pin_code']);
                                            } 
                                        ?>
                                    </span></td>
                                    <td class="b-right b-top"><span class="font-weight-bold fs-09">Phone</span><hr class='mt-1 mb-1'/><span class="fs-08">
                                        <?php 
                                            if($data['gst_phone']==''){
                                                echo "No Data Found";
                                            }else{
                                                echo trim($data['gst_phone']);
                                            } 
                                        ?>
                                    </span></td>
                                    <td class="b-right b-top"><span class="font-weight-bold fs-09">Contact Person</span><hr class='mt-1 mb-1'/><span class="fs-08">
                                        <?php 
                                            if($data['first_name']==''){
                                                echo "No Data Found";
                                            }else{
                                                echo trim($data['first_name']);
                                            } 
                                        ?>
                                    </span></td>
                                    <td class="b-top"><span class="font-weight-bold fs-09">Cat Type</span><hr class='mt-1 mb-1'/><span class="fs-08">
                                        <?php 
                                            if($data['creation_source']==''){
                                                echo "No Data Found";
                                            }else{
                                                echo trim($data['creation_source']);
                                            } 
                                        ?>
                                    </span></td>
                                </tr>
                                <tr>
                                    <td class="b-right b-top"><span class="font-weight-bold fs-09">Date of Birth</span><hr class='mt-1 mb-1'/><span class="fs-08">
                                        <?php 
                                            if($data['date_of_birth']==''){
                                                echo "No Data Found";
                                            }else{
                                                echo trim($data['date_of_birth']);
                                            } 
                                        ?>
                                    </span></td>
                                    <td class="b-right b-top"><span class="font-weight-bold fs-09">PAN Card No</span><hr class='mt-1 mb-1'/><span class="fs-08">
                                        <?php 
                                            if($data['pan_number']==''){
                                                echo "No Data Found";
                                            }else{
                                                echo trim($data['pan_number']);
                                            } 
                                        ?>
                                    </span></td>
                                    <td class="b-right b-top"><span class="font-weight-bold fs-09">PAN Card Holder Name</span><hr class='mt-1 mb-1'/><span class="fs-08">
                                        <?php 
                                            if($data['pan_holdername']==''){
                                                echo "No Data Found";
                                            }else{
                                                echo trim($data['pan_holdername']);
                                            } 
                                        ?>
                                    </span></td>
                                    <td class="b-top"><span class="font-weight-bold fs-09">Nature of Business</span><hr class='mt-1 mb-1'/><span class="fs-08">
                                        <?php 
                                            if($data['creation_source']==''){
                                                echo "No Data Found";
                                            }else{
                                                echo trim($data['creation_source']);
                                            } 
                                        ?>
                                    </span></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="b-right b-top"><span class="font-weight-bold fs-09">Password</span><hr class='mt-1 mb-1'/><span class="fs-08">
                                        <?php echo trim($data['password']); ?>
                                    </span></td>
                                    
                                </tr>
                            </table>
                        </div><!-- /.box-body -->
                      </div><!-- /.box -->
        			</div>
                 </div>
         </section><!-- /.content -->
      </div><!-- /.content-wrapper -->  




</div>
<!-- PANEL BODY END --></div>
<!-- PANEL WRAP END --></div>
<!-- HTML END -->


<style type="text/css">
     .b-right{
        border-right:1px solid #ccc!important;
    }
    .b-left{
        border-left: 1px solid #ccc!important;
    }
    .b-bottom{
        border-bottom: 1px solid #ccc!important;
    }
    .b-top{
        border-top: 1px solid #ccc!important;
    }
    .border{
        border:1px solid #ccc!important;
    }
    .p_color{
        color: #b80303;
    }
    .mt-1{
    	margin-top: 5px;
    }
    .mb-1{
    	margin-bottom: 5px;
    }
    table td{
    	width: 25%;
    	font-size: 15px;
    }
    .font-weight-bold{
    	font-weight: bold;
    }
</style>
