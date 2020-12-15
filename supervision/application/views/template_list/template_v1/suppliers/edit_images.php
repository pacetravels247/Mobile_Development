<!DOCTYPE html>
<html>
<head>
    <title>Supplier Account Manager | China Dream Travel</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta content='text/html;charset=utf-8' http-equiv='content-type'>
    
    <link href='<?=base_url();?><?=base_url();?>assets/images/meta_icons/favicon.ico' rel='shortcut icon' type='image/x-icon'>
    <link href='<?=base_url();?>assets/images/meta_icons/apple-touch-icon.png' rel='apple-touch-icon-precomposed'>
    <link href='<?=base_url();?>assets/images/meta_icons/apple-touch-icon-57x57.png' rel='apple-touch-icon-precomposed' sizes='57x57'>
    <link href='<?=base_url();?>assets/images/meta_icons/apple-touch-icon-72x72.png' rel='apple-touch-icon-precomposed' sizes='72x72'>
    <link href='<?=base_url();?>assets/images/meta_icons/apple-touch-icon-114x114.png' rel='apple-touch-icon-precomposed' sizes='114x114'>
    <link href='<?=base_url();?>assets/images/meta_icons/apple-touch-icon-144x144.png' rel='apple-touch-icon-precomposed' sizes='144x144'>
    <!-- / START - page related stylesheets [optional] -->
    <link href="<?=base_url();?>assets/stylesheets/plugins/datatables/bootstrap-datatable.css" media="all" rel="stylesheet" type="text/css" />
    <!-- / END - page related stylesheets [optional] -->
    <!-- / bootstrap [required] -->
    <link href="<?=base_url();?>assets/stylesheets/bootstrap/bootstrap.css" media="all" rel="stylesheet" type="text/css" />
    <!-- / theme file [required] -->
    <link href="<?=base_url();?>assets/stylesheets/light-theme.css" media="all" id="color-settings-body-color" rel="stylesheet" type="text/css" />
    <!-- / coloring file [optional] (if you are going to use custom contrast color) -->
    <link href="<?=base_url();?>assets/stylesheets/theme-colors.css" media="all" rel="stylesheet" type="text/css" />
    <!-- / demo file [not required!] -->
    <link href="<?=base_url();?>assets/stylesheets/demo.css" media="all" rel="stylesheet" type="text/css" />
    <!--[if lt IE 9]>
      <script src="<?=base_url();?>assets/javascripts/ie/html5shiv.js" type="text/javascript"></script>
      <script src="<?=base_url();?>assets/javascripts/ie/respond.min.js" type="text/javascript"></script>
    <![endif]-->
  </head>
  <body class='contrast-dark fixed-header'>
    <?php $this->load->view('suppliers/header');?>
    <div id='wrapper'>
      <div id='main-nav-bg'></div>
      <?php $this->load->view('supplier-sidemenu');?>
      <section id='content'>
        <div class='container'>
          <div class='row' id='content-wrapper'>
            <div class='col-xs-12'>
              <div class='row'>
                <div class='col-sm-12'>
                  <div class='page-header'>
                    <h1 class='pull-left'>
                      <i class='icon-building'></i>
                      <span>Edit Images</span>
                    </h1>
                    <div class='pull-right'>
                      <ul class='breadcrumb'>
                        <li>
                          <a href='index.html'>
                            <i class='icon-bar-chart'></i>
                          </a>
                        </li>
                        <li class='separator'>
                          <i class='icon-angle-right'></i>
                        </li>
                        <li class='active'>Edit Images</li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class='row'>
                <div class='col-sm-12'>
                  <div class='box bordered-box orange-border' style='margin-bottom:0;'>
                    <div class='box-header blue-background'>
                      <div class='title'>Edit Images</div>
                      
                      </div>


 <!--  <div class='form-group'>
                           <label class='col-sm-2' for='validation_company'>Package Main Image</label>
                                <div class='col-sm-3 controls'>
                                    <input type="file" title='Search for a image to add'   id='package_main_image' name='main_image'>
            <img src="" alt=""  width="100" height="100">                                    
          <span id="pacmimg" style="color:#F00; display:none">Please Upload Package Image</span>
                                </div>
                        </div> -->

<div class='box-content'>
                      <form class='form form-horizontal validate-form' style='margin-bottom: 0;' action="<?php echo WEB_URL; ?>supplier/update_all_images" method="post" enctype="multipart/form-data"> 
       <input type="hidden" value="<?php echo $packdata->package_id; ?>" name="package_id">                       
      <div class='form-group'>
                           <label class='col-sm-2' for='validation_company'>Main Image</label>
                                <div class='col-sm-3 controls'>
                                    <input type="file" title='Search for a image to add'   id='photo' name='photo'>
           <img src="<?php echo $packdata->image; ?>" width="100" name="photo">                                   
          <span id="pacmimg" style="color:#F00; display:none">Please Upload Package Image</span>
                                </div>
                        </div>



                        <hr>
                        <div class='form-group'>
                           <label class='col-sm-2' for='validation_company'>Itinerary Image</label>
                                <div class='col-sm-3 controls'>
                                    <input type="file" title='Search for a image to add'   id='package_main_image' name='main_image'>
            <img src="" alt=""  width="100" height="100">                                    
          <span id="pacmimg" style="color:#F00; display:none">Please Upload Package Image</span>
                                </div>
                        </div>
                        <div class='form-group'>
                         <label class='col-sm-2' for='validation_company'>Traveller Images</label>
                        <div class='col-sm-3 controls'>
                                <input type="file" title='Search for a image to add'  id='package_other_images' name='other_images<?php echo $oi ?>'>
                               <img src="" alt=""  width="100" height="100">      
              <span id="pacmimg" style="color:#F00; display:none">Traveller Uploads</span>
            <a class="btn btn-danger btn-xs has-tooltip" data-placement="top" title="" onclick="return confirm('Are you sure, do you want to delete this record?');" href="" data-original-title="Delete">
                                              <i class="icon-remove"></i>
                                            </a>
           </div>
           </div>

                     
                     
                      <div class='form-actions' style='margin-bottom:0'>
                          <div class='row'>
                            <div class='col-sm-9 col-sm-offset-3'>
                              <a href="<?php echo WEB_URL; ?>supplier/supplierss">
                              <button class='btn btn-primary' type='button'>
                                <i class='icon-reply'></i>
                                Go Back
                              </button></a>
                              <button class='btn btn-primary' type='submit'>
                                <i class='icon-save'></i>
                                Update
                              </button>
                            </div>
                          </div>
                        </div>
                        </div>
                      </div>
                      </form>

                      </body>