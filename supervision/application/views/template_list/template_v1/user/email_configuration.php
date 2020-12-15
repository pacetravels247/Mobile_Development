<?php
$tab1 = 'active';
// echo $action;exit;
?>
<!-- HTML BEGIN -->
<div class="bodyContent">
    <div class="panel panel-default"><!-- PANEL WRAP START -->
        <div class="panel-heading"><!-- PANEL HEAD START -->
            <div class="panel-title">
                <ul class="nav nav-tabs" role="tablist" id="myTab">
                    <!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
                    <li role="presentation" class="<?= $tab1 ?>">
                        <a id="fromListHead" href="#fromList" aria-controls="home" role="tab"	data-toggle="tab">Manage Email Configuration</a>
                    </li>
                    <!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE END -->
                </ul>
            </div>
        </div>
        <!-- PANEL HEAD START -->
        <div class="panel-body"><!-- PANEL BODY START -->
            <span class="error_msg"><?php if (isset($message)) {
    echo $message;
} ?></span>
            <div class="tab-content">
                <div role="tabpanel" class="clearfix tab-pane <?= $tab1 ?>" id="fromList">
                    <div class="panel-body">


                        <div class="tab-content">
                            <div id="fromList" class="clearfix tab-pane  active " role="tabpanel">
                                <div class="panel-body">
                                    <form class="form-horizontal" role="form" enctype="multipart/form-data" method="POST" action="<?= base_url() . 'general/email_configuration' ?>" autocomplete="off" name="home_page_heading">
                                        <fieldset form="promo_codes_form_edit">
                                            <legend class="form_legend">Add Email Configuration</legend>
                                            <div class="form-group">
                                                <label form="promo_codes_form_edit" for="header_title" class="col-sm-3 control-label">Username</label>
                                                <div class="col-sm-6">
                                                    <input type="email" id="username" class="form-control" placeholder="Username" name="username" required value="<?php echo $user_name; ?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label form="promo_codes_form_edit" for="header_icon" class="col-sm-3 control-label">Password</label>
                                                <div class="col-sm-6">
                                                    <input type="text" id="password" class="form-control" placeholder="Please Enter Your Password" name="password" required value="<?php echo $password; ?>" >
                                                    <span>Note :- Password will not displayed for security reason.</span>
                                                </div>
                                                
                                            </div>
                                            <div class="form-group">
                                                <label form="promo_codes_form_edit" for="header_icon" class="col-sm-3 control-label">From</label>
                                                <div class="col-sm-6">
                                                    <input type="text" id="from" class="form-control" placeholder="From" name="from" required value="<?php echo $from; ?>" >
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label form="promo_codes_form_edit" for="header_icon" class="col-sm-3 control-label">Host</label>
                                                <div class="col-sm-6">
                                                    <input type="text" id="host" class="form-control" placeholder="host" name="host" required value="<?php echo $host; ?>" >
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label form="promo_codes_form_edit" for="header_icon" class="col-sm-3 control-label">Port</label>
                                                <div class="col-sm-6">
                                                    <input type="number" id="port" class="form-control" placeholder="Port" name="port" required value="<?php echo $port; ?>" >
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label form="promo_codes_form_edit" for="header_title" class="col-sm-3 control-label">CC Email</label>
                                                <div class="col-sm-6">
                                                    <input type="email" id="cc_email" class="form-control" placeholder="CC Emails" name="cc_email" value="<?php echo $cc; ?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label form="promo_codes_form_edit" for="header_title" class="col-sm-3 control-label">BCC Email</label>
                                                <div class="col-sm-6">
                                                    <input type="email" id="bcc_email" class="form-control" placeholder="BCC Emails" name="bcc_email" value="<?php echo $bcc; ?>">
                                                </div>
                                            </div>
                                        </fieldset>
                                        <div class="form-group">
                                            <?php
                                            if (isset($title)) {
                                                $button = 'Update';
                                            } else {
                                                $button = 'Save';
                                            }
                                            ?>
                                            <div class="col-sm-8 col-sm-offset-4"> <button class="btn btn-success" type="submit"><?php echo $button; ?></button> <button class=" btn btn-warning " id="promo_codes_form_edit_reset" type="reset">Reset</button></div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

            </div>
        </div>
        <!-- PANEL BODY END --></div>
    <!-- PANEL WRAP END --></div>
<!-- HTML END -->

