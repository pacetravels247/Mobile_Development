<style>
.formlabel {color:black;}
#cncelPolicy_id {font-weight:bold;}
.detlnavi ul li {font-size: 14px;}
#cncelPolicy_id {color:red;}
.features {
    display: block;
    margin-top: 16px; float: left; width:70%;
    overflow: hidden;
}
.features li {
    float: left;
    padding: 0px 5px;
    width: 15%;
    border-right: 1px solid #cbcbcc;
}

.features li:last-child { border-right:none; }

.features li strong {
    color: #525252;
    display: block;
    float: left;
    font-size: 15px;
    font-weight: normal;
    line-height: 25px;
}

.middleCol {
    display: block;
    overflow: hidden;
}

.car_image img {
    max-width: 100%;
    min-height: 80px !important;
    max-height: 90px;
}

.c_tool .tooltip { width: auto !important; float: left; background: none !important; border-radius: 3px;} 
.c_tool .tooltip.left { padding: 0px !important; }
.c_tool .tooltip-inner { padding: 2px 7px !important; background: #333 !important; max-width: 100% !important; }
.c_tool .tooltip-inner .table { margin-bottom: 0px !important; background: #333 !important; }
.c_tool .tooltip.left .tooltip-arrow { right: -5px !important; border-left-color: #333; }
.c_tool .tooltip.in { opacity: 1 !important; }
  

.suplier_logo {
    display: block;text-align: center; width:30%; float: left;
}

.suplier_logo img { width: 120px; margin-top: 10px; }

.flitruo_hotel {display: block;margin: 6px 0;overflow: hidden;padding: 0 10px 0 0;
}
.ifroundway .flitruo {border-bottom: 1px dashed #ddd;
}
.ifroundway .flitruo:last-child{ border-bottom:none;}
.oneplus{ display:none;background: #e0e0e0 none repeat scroll 0 0;}
.oneonly{opacity:0;}
.plusone .oneplus{ display: inline-block;}
.morestop .oneonly{ opacity:1;}
.ifroundway .fligthsmll{margin: 90px 10px 10px;}
.hoteldist {display: block;overflow: hidden;}
.travlrs {
    color: #999;
    display: block;
    font-size: 16px;
    margin: 0 0 15px;
    overflow: hidden;
}

.portnmeter {
    color: #0a9ed0;
    display: block;
    font-size: 13px;
    overflow: hidden;
}

.fare_loc {
    font-size: 14px;
    color: #5b5b5b;
    font-weight: 500;
}
.sectionbuk .lbllbl { color: #666 !important; }
.pick { width:30%; margin-right:1.5%; float:left; margin-top:0.5%; font-size: 14px;}
.pick .fa { font-size: 16px; }   
.pick span { font-weight: 500; color: #848383; }            
      

.pick h3 { font-size:13px; font-weight:normal; margin:0px; margin-top:2px;  line-height:15px; color:#333;  /*overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;*/     padding-left: 16px;}

.car_name{color: #f88c3e;display: block;font-size: 18px;font-weight: 500;overflow: hidden;}
.car_name span{color: #545454;
    font-size: 13px;}
.madgrid {
    background: #ffffff none repeat scroll 0 0;
    border: 1px solid #e0e0e0;
    box-shadow: 0 0 3px #d2d2d2;
    display: block;
    float: left;
    margin: 8px 0;
    overflow: inherit;
    width: 100%;
    transition: all 400ms ease-in-out 0s;
}

.width80 {
    width: 80%;
}

.waymensn {display: block;overflow: hidden;
}

.features li.person span {
    background-position: 0 0;
}
.features li.transmission span {
    background-position: -24px 0;
}
.features li.baggage span {
    background-position: 0 -24px;
}
.features li.ac span {
    background-position: -48px 0;
}
.features li.doors span {
    background-position: -24px -24px;
}
.features li.fuel span {
    background-position: 0 -48px;
}
.car_image {
    display: block;
    line-height: 120px;
    margin: 5px;
    overflow: hidden;
    text-align: center;
}
.payinput1 {
    border: 1px solid #d6d6d6;
    border-radius: 3px;
    color: #333333;
    display: block;
    font-size: 14px;
    height: 45px;
    overflow: hidden;
    padding: 10px 3px;
    width: 100%;
}
.lokter {
    margin-top: 22px;
}
#ui-datepicker-div.ui-datepicker .ui-datepicker-title {
    float: left;
    width: 60%;
}
.ui-datepicker select.ui-datepicker-month, .ui-datepicker select.ui-datepicker-year {
    float: right;
}
.fl_list { padding-left: 30px; }
.fl_list li { list-style-type: square;width: 50%;float: left;}
.list.fl_list li { width: 100%; margin-bottom: 6px;}
.cust_mdl1 iframe { width: 100%; height: 350px;}
/*responsive*/
@media ( max-width :767px) {
.celhtl.midlbord { width: 20% !important;}
.pick h3 {padding-left: 0;font-size: 11px; }
.pick span { font-size: 13px;}
.features { margin-top: 5px;}
.suplier_logo img { margin-top: 0;}
.sidenamedesc { width: 100%;}
}

@media ( max-width :480px) {
.car_name { font-size: 15px;line-height: 16px;}
.features li .mn-icon { margin: 0 0px 0 2px; }
.features { width: 70% !important;}
.suplier_logo img {width: 100%;}
.repeatprows .set_margin { margin: 0;}
}
@media ( min-width :481px) and (max-width:767px) {

}
@media ( min-width :768px) and (max-width:991px) {
.pick span { font-size: 13px;}
}
@media ( min-width :992px) and (max-width:1199px) {
}

@media ( min-width :992px) {
  .celhtl.width20.midlbord {
    display: table-cell;
    vertical-align: middle;
    float: none;
    width: 20%;
}
.width80 {
    width: 80%;
    display: table-cell;
    vertical-align: middle;
    float: none;
}
.sidenamedesc {
    display: table;
    width: 100%;
}

.c_tool .tooltip.top .tooltip-arrow {
    bottom: -7px;
    left: 50%;
    margin-left: -5px;
    border-width: 5px 5px 0;
    border-top-color: #20364f;
}
.c_tool .tooltip.top {
    padding: 0;
    margin-top: -3px;
    background: #fff !important;
    border: 2px solid #20364f;
    border-radius: 3px;
    max-width: 200px !important;
}
.c_tool .tooltip-inner {
    padding: 10px !important;
    background: #fff !important;
    max-width: 100% !important;
    color: #333;
    /* max-width: 200px !important; */
    text-align: left;
    font-family: 'Aller', sans-serif;
    font-size: 13px;
}
  }
</style>
<?php  //debug($params_details['supplier_logo']);exit('booking view');
// echo $no_of_day;die;
Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('page_resource/car_new_result.css'), 'media' => 'screen');
Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('page_resource/pre_booking.css'), 'media' => 'screen');
Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('page_resource/car_pre_booking.css'), 'media' => 'screen');
Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('page_resource/animation.css'), 'media' => 'screen');
Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('owl.carousel.min.css'), 'media' => 'screen');
Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('page_resource/car_result.css'), 'media' => 'screen');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/flight_session_expiry_script.js'), 'defer' => 'defer');
// debug($car_details); exit();
?>



<div class="full onlycontent top80">  
<div class="container martopbtm">    
<div class="paymentpage" style="position: relative;">      
<div class="col-md-4 col-sm-4 nopadding_right frmbl sidebuki" id="sidebar" style="">        
<div class="cartbukdis">          
<ul class="liscartbuk">          
<li class="lostcart ">            
            <div class="faresum">             
            <h3>Purchase Summary</h3>             
            <div class="col-xs-12 nopad colrcelo">              
            <div class="bokkpricesml">               
            <div class="travlrs col-md-6 nopad">                
            <span class="portnmeter">Pick Up</span>                
            <div class="fare_loc">DUBAI INTL AIRPORT T1</div>                
            <div class="date_loc">28 Apr 2018 09:00</div>               
            </div>               

            <div class="travlrs col-md-6 nopad">                
            <span class="portnmeter">Drop Off</span>                
            <div class="fare_loc">DUBAI INTL AIRPORT T1</div>                
            <div class="date_loc">29 Apr 2018 09:00</div>               
            </div>                           
               </div>             
               </div>                           

                          <div class="col-xs-12 nopad colrcelo">               
                            <div class="bokkpricesml">                
                                      <span class="portnmeter">Business Hours</span>                                
                                      <span class="business_hour col-md-6 nopad">                  
                                       <div class="loc_name">DUBAI INTL AIRPORT T1 </div> 
                                      <div class="loc_time">00:00 to 23:59</div>                                    
                                      </span>                                  

                                      <span class="business_hour col-md-6 nopad">                  
                                      <div class="loc_name">DUBAI INTL AIRPORT T1 </div> 
                                      <div class="loc_time">00:00 to 23:59</div>                                    
                                      </span>                                
                             </div>              
                          </div>      

             <div class="fare_show">              
             <!-- <h5 class="base_f">Fare Breakup <i class="fa fa-chevron-down"></i></h5> -->              
             <div class="show_fares_table">               
             <table class="table table-striped">                
             <tbody>                 
             <tr>                  
             <td style="width:55%">Car Rental Price</td>                  
             <td class="text-center" style="text-align: right">INR <span id="CarRentalPrice">43.54</span></td>                 
             </tr>                                       

             <tr>                  
             <td style="width:56%; border:none;color: #0a9ed0;;">Includes the following fees</td>                  
             <td class="text-center" style="border:none;">&nbsp;</td>                 
             </tr>                                          

             <tr>                        
              <td style="width:65%; border:none;color: #0a9ed0;;">Other taxes and service charges</td>                         
              <td style="width:35%; border:none;color: #0a9ed0;text-align: right;">                         INR 1.89                          </td>                       
              </tr>                 

              <tr class="hide c_tool">                  
              <td>Convenience Fee <a href="#" data-toggle="tooltip" title="We retain our service fees as compensation in servicing your travel reservation"><i class="fa fa-info-circle" aria-hidden="true"></i></a></td>                  

              <td class="text-center" style="text-align: right">INR 0.00</td>                 
              </tr>                 

              <tr class="hide promo_code_discount">                  
              <td>Promo Code Discount</td>                  
              <td class="text-center" style="text-align: right">INR <span class="discount_amount">0</span></td>                 
              </tr>                 

              <tr class="hide total" id="currency_fare">                  
              <td><span class="to_bo">Pay Now (<span id="mysessioncurrency"></span>)</span></td>                  
              <td class="text-right" style="text-align: right;white-space: nowrap;">
              <span id="mysessioncurrency_p"></span>&nbsp;<span class="discount_total" id="car_tottalp">0.00</span>                  
              </td>                 
              </tr>                 

              <tr>                 
                  <td class="to_bo">Pay Now</td>                  
	              <td class="text-center" style="text-align: right;white-space: nowrap;">                   
	              <span class="style_currency">INR</span> 
	              <span class="discount_total">41.65</span>                   
	                             
	              </td>                 
              </tr>                
              </tbody>               
              </table>              

              </div>             
              </div>            
              </div>                      

              <div class="cartlistingbuk hide">             
              <div class="cartitembuk">              
              <div class="col-md-3 celcart"> 
               <a class="smalbukcrt"><img src="https://static.carhire-solutions.com/images/car/Alamo/small/t_MCAR_AE.jpg" alt=""></a>              
               </div>              

               <div class="col-md-8 splcrtpad celcart">               
               <div class="carttitlebuk1">                
	               <div class="col-xs-6 nopad">Pick Up 
	                 <div class="cartsec_time">28 Apr 2018</div>                
	               </div>               

                  <div class="col-xs-6 nopad">To 
                  <div class="cartsec_time">29 Apr 2018</div>                
                 </div>

                </div>              
                </div>              

	                <div class="col-md-1 cartfprice celcart">               
		                <div class="cartprc">                
		                <div class="singecartpricebuk">INR 41.65</div>               
		                </div>              
	                </div>             

                </div>            
                </div>           
                </li>                       

                <li class="lostcart" id="extra_box">          
          
                <div class="faresum">             
                <h3>Pay On Pick Up in local currency 
                <span class="pull-right hide"> INR <span class="total_extras">0</span></span></h3>             
                <div class="fare_show">              
                <div class="show_fares_table">               
                <table class="table table-striped">                
	                <tbody id="extras_holder">                                          
		                <tr>                         
		                <td style="width:65%">Other taxes and service charges</td>                         
		                <td style="width:35%;text-align:right; white-space: nowrap;" class="text-center"> INR 1.89</td>                       
		                </tr>                
	                </tbody>                                

	                <!-- <tbody id="extras_holder_1">                  
                </tbody>                <tbody id="extras_holder_2">                  
                </tbody>                <tbody id="extras_holder_3"> -->                  
                    <tfoot>                 
                    <tr>                  
                    <td colspan="2" style="display:none;"> 
                    <p class="text-center" style="font-size:12px !important;">
                    <em>Payment at pick up is in the local currency and prices are subject to change.</em>
                    </p> 
                    </td> 
                 </tr>                 
                </tfoot>               
                </table>               
                <input type="hidden" class="price_previous" name="price_previous" value="0">              
                </div>             
                </div>            
                </div>           
                </li>                        

                <script type="text/javascript">              
                var is_logged_in_user = false;
               </script>                        

               <li class="lostcart hide" id="prompform">              
               <div class="cartlistingbuk">                
               <div class="cartitembuk">                  
	               <div class="col-md-12">                    
	                   <div class="payblnhmxm">Have an e-coupon or a promo-code ? (Optional)</div>                  
	               </div>               
               </div>                

               <div class="clearfix"></div>                
               <div class="cartitembuk prompform">                  
               <form name="promocode" id="promocode" novalidate="">                   
                <div class="col-md-8 col-xs-8 nopadding_right">                      
                <div class="cartprc">                        
                <div class="payblnhm singecartpricebuk ritaln">                                                  
                <input type="text" placeholder="Enter Promo" name="code" id="code" class promocode" aria-required="true">                         
                 <input type="hidden" name="module_type" id="module_type" class="promocode" value="e6d96502596d7e7887b76646c5f615d9">                          
                 <!-- <input type="hidden" name="total_amount_val" id="total_amount_val" class="promocode" value="41.65" /> -->                          
                 <input type="hidden" name="total_amount_val" id="total_amount_val" class="promocode" value="41.65">                          
                 <input type="hidden" name="convenience_fee" id="convenience_fee" class="promocode" value="0.00">                          
                 <p class="error_promocode text-danger"></p>                     
                        </div>                      
                        </div>                    
                        </div>                   
                        <div class="col-md-4 col-xs-4 nopadding_left">                      
                        <input type="submit" value="Apply" name="apply" id="apply" class="promosubmit">                    
                        </div>                 
                         </form>                
                         </div>                
                         <div class="clearfix"></div>                
                         <div class="savemessage"></div>              
                         </div>            
                         </li>                         
                         <li class="lostcart hide">              
                         <div class="cartlistingbuk">                
                         <div class="cartitembuk">                  
                         <div class="col-md-8 celcart">                    
                         <div class="payblnhm">Sub Total</div>                  
                         </div>                  
                         <div class="col-md-4 celcart">                    
                         <div class="cartprc">                      
                         <div class="ritaln cartcntamnt normalprc">CAD 41.65</div>                    
                         </div>                  
                         </div>                
                         </div>                
                         <div class="cartitembuk">                  
                         <div class="col-md-8 celcart">                    
                         <div class="payblnhm">Convenience Fee</div>                  
                         </div>                  
                         <div class="col-md-4 celcart">                    
                         <div class="cartprc">                      
                         <div class="ritaln cartcntamnt normalprc discount">(+)CAD <span class="amount">0.00</span></div>                    
                         </div>                  
                         </div>                
                         </div>                
                         <div class="cartitembuk hide promo_code_discount" id="promocode_block">                  
                         <div class="col-md-8 celcart">                    
                         <div class="payblnhm">Promo Code Discount</div>                  
                         </div>                  
	                         <div class="col-md-4 celcart">                    
		                         <div class="cartprc">                      
		                         <div class="ritaln cartcntamnt normalprc discount">(-)CAD 
		                            <span class="amount discount_amount ">0</span>
		                         </div>                    
		                         </div>                  
	                         </div>                
	                         </div>              
	                         </div>             
	                          <div class="clear"></div>              
	                          <div class="cartlistingbuk nomarr">                
	                          <div class="cartitembuk">                  
	                          <div class="col-md-8 celcart">                    
	                          <div class="payblnhm">Total</div>                  
	                          </div>                  
	                          <div class="col-md-4 celcart">                    
	                          <div class="cartprc">                      
	                          <div class="ritaln cartcntamnt bigclrfnt finalAmt">CAD 
	                          <span class="amount discount_total">41.65</span>
	                          </div>                    
	                          </div>                  
	                          </div>                
	                          </div>              
	                          </div>            
	                          </li>          
	                          </ul>        
	                          </div>      
	                          </div>            
	                          <div class="col-md-8 col-sm-8 nopad fulbuki">     
                                  <div class="madgrid">          
                                  <div class="col-xs-12 nopad">             
                                  <div class="sidenamedesc">              
                                  <div class="celhtl width20 midlbord">                
                                  <div class="car_image"> 
                                  <img class="lazy lazy_loader h-img" src="https://static.carhire-solutions.com/images/car/Sixt/small/AE_EDAR.jpg" onerror="this.onerror=null;this.src='/extras/system/template_list/template_v1/images/no-img.jpg';" style="display: inline-block;">
                                  </div>              
                                  </div>              

                                  <div class="celhtl width80">                
                                  <div class="waymensn">                  
                                  <div class="flitruo_hotel">                    
                                  <div class="hoteldist">                      
                                  <span class="car_name">FORD FIGO
                                  <span> or Similar</span>
                                  </span>                      
                                  <div class="clearfix"></div>  

                                  <span class="hotel_address elipsetool"></span>                      
                                  <div class="clearfix"></div>                      
                                  <div class="pick cr_wdt">                        
                                  <i class="fa fa-map-marker"></i> 
                                  <span>Pickup Location:</span>                        
                                  <h3>Dubai Intl Airport T1</h3>      
                                  </div>               


                      <div class="pick fuel-plcy" data-toggle="modal" data-target="#myModal">                        
                      <span class="fuel_icon">Fuel Information:F2F</span>                        
                      <h3 style="padding-left: 22px !important;">                          
                      <a href="#" data-toggle="tooltip" title="Full to Full: Pick up and drop off with a full tank. If the car is not returned with a full tank, suppliers will charge fuel plus refueling charges. ">                            
                      <i class="fa fa-info-circle" aria-hidden="true"></i> 
                            Petrol                          </a>                        

                            </h3>                      
                            </div>                      
                            <div class="pick">                        
                            <i class="fa fa-dashboard"></i> <span>Mileage Allowance:</span>                        
                            <h3 style="padding-left: 20px !important;">Unlimited</h3>                      
                            </div>                      
                            <div class="clearfix"></div>                      
                            <div class="middleCol">                        
                            <ul class="features"> 

                            <li class="person tooltipv">
                            <a data-original-title="Passengers" data-toggle="tooltip"><strong>5</strong> <span class="mn-icon"></span> </a>
                            </li>          

                            <li class="baggage tooltipv">
                            <a data-original-title="Bags" data-toggle="tooltip"><strong>2</strong> <span class="mn-icon"></span> </a>
                            </li>                          

                            <li class="doors tooltipv dor"><a data-original-title="Doors" data-toggle="tooltip"><strong>2</strong> <span class="mn-icon"></span> 
                            </a></li>                          

                            <li class="hdng" style="color: #f88c3e !important;width: auto !important;font-size: 13px;font-weight: 600 !important;">Age Limit: 18 years - 80 years</li>       
                            </ul>                          

                            <!-- <div class="age_yr">                            
                            <h5 class="hdng">Age:25years-80years</h5>                          </div> -->                        

                            <div class="suplier_logo"><img src="https://static.carhire-solutions.com/images/supplier/logo/logo36.png"> 
                            </div>                       

                             <ul class="texts hide">                          
                             <li>Oneway fee</li>                          
                             <li>Collision damage waiver</li>                          
                             <li>Unlimited Mileage</li>                        
                             </ul>                      
                             </div>                      
                             <div class="clearfix"></div>                    
                             </div>                  
                             </div>                
                             </div>              
                             </div>            
                             </div>           

                             <!--  <div class="sidenamedesc">                          </div> -->            

                             <div class="width20 hide">              
                             <div class="mrinfrmtn">                
                             <span class="spld">Special Discount</span>                
                             <div class="sidepricewrp">                  
                             <div class="sideprice">                    
                             <strong>CAD </strong><span class="f-p">33.01</span>                  
                             </div>                
                             </div>              
                             </div>            
                             </div>          
                             </div>        
                             </div>        

                             <div class="sumry_wrap">
                             <!-- LOGIN SECTION STARTS -->
                             <div class="pre_summery user-login-guest ">
                             <!-- Enable Later  hide-->
                             <div class="prebok_hding spl_sigin"> Primary Contact Information </div>
                             <div class="signing_detis"><form name="login" id="login_prebook" novalidate="" action="/index.php/car/car_details/" method="POST">
                             <div class="col-md-6 nopad">
                             <div class="wrp_pre">                      

                             <div class="paylabel">Email <strong class="text-danger no_block">* </strong></div>

                             <input type="email" required="" placeholder="Email Address" name="booking_user_name" id="booking_user_name" class="_guest_validate form-control logpadding email pre_put" aria-required="true" maxlength="80">

                             <span class="sentmail_id">Your booking details will be sent to this email address.</span> 
                             </div>
                             </div>

                             <div class="clearfix"></div>

                             <div class="wrp_pre">
	                             <div class="squaredThree">
	                             <input type="checkbox" id="ihave" class="filter_airline" name="confirm" value="0">
	                             <label for="ihave"></label>
	                             </div>
                                 <label class="have_account" for="ihave">I have an Account</label>
                             </div>

                             <div class="clearfix"></div>

                             <div class="sign_twosec">
                             <div id="i_have_account" class="section_sign">
                             <div class="col-md-6 nopad">
	                             <div class="wrp_pre">                        
	                             <div class="paylabel">Password <strong class="text-danger no_block">* </strong>
	                             </div>
	                             <input type="password" placeholder="Password" id="booking_user_password" name="booking_user_password" class="form-control logpadding pre_put">
	                             <a id="forgtpsw" class="fadeandscale_close fadeandscaleforget_open forgtpsw pre_forgot">Forgot password?</a>
	                             </div>

                             <div class="clearfix"></div>

                             <div class="alert-danger"></div>

                             <div class="wrp_pre">
	                             <input type="hidden" name="search_id" value="8623">           
	                             <input type="hidden" name="booking_source" value="PTBSID0000000011">
	                             <input type="hidden" name="id_context" value="C7rdcA2">           
	                             <input type="hidden" name="type" value="16">           
	                             <input type="submit" value="Continue" id="continue_as_user" name="continue" class="paysubmit">
                             </div>
                             </div>
                             </div>

                             <div class="clearfix"></div>

                             <div id="con_as_guest" class="section_sign">
                             <div class="col-md-6 nopad">
                             <div class="wrp_pre">
                             <div class="col-xs-5 nopad">                          
                             <div class="paylabel">Country Code <strong class="text-danger no_block">* </strong></div>                            

                             <select name="pn_country_code" id="pn_country_code" required="" aria-required="true" class="mySelectBoxClass flyinputsnor">                              
                             <option value="+1_Canada +1">Canada +1</option>                              
                             <option value="+1_United States +1">United States +1</option>                             
                             <option value="+93_Afghanistan +93">Afghanistan +93 </option>
                             <option value="+355_Albania +355">Albania +355 </option>
                             <option value="+213_Algeria +213">Algeria +213 </option>
                             <option value="+1684_American Samoa +1684">American Samoa +1684 </option>
                             <option value="+376_Andorra +376">Andorra +376 </option>
                             <option value="+244_Angola +244">Angola +244 </option>
                             <option value="+1264_Anguilla +1264">Anguilla +1264 </option>
                             <option value="+1268_Antigua And Barbuda +1268">Antigua And Barbuda +1268 </option>
                             <option value="+54_Argentina +54">Argentina +54 </option>
                             </select>

                             <!-- <input type="text" placeholder="+1" class="pre_put form-control" required="" name="pn_country_code" aria-required="true" readonly> -->
                             </div>

                             <div class="col-xs-1 nopad">
                             <div class="mob_hi">-</div>
                             </div>

                             <div class="col-xs-6 nopad">                            
	                             <div class="paylabel">Contact <strong class="text-danger no_block">* </strong></div>
	                             <input type="text" id="booking_user_mobile" name="" class="mobile _guest_validate form-control pre_put  invalid-ip" placeholder="Mobile Number" required="" aria-required="true">
                             </div>
                             <div class="clearfix"></div>

                             <div class="sentmail_id hide">
                             We'll use this number to send possible update alerts.
                             </div>
							</div>

							<div class="clearfix"></div>

								<div class="wrp_pre">
								<input type="button" value="Continue as Guest" id="continue_as_guest" onclick="show_travellers_tab();" name="continue_c" class="paysubmit">
								</div>

							</div>
							</div>
							</div>
								<div class="wrp_pre">Don't have an account? 
								<a class="fadeandscale_close fadeandscaleregbooking_open">Sign up</a>
								</div>
							</form>
							</div>

							<div style="display:none;" class="signing_detis_confirm">
							<div class="col-md-6 nopad">
							<div class="wrp_pre"> 
							<span id="user_logs_status" class="sentmail_id"></span> 
							</div>
							</div>

							<div class="clearfix"></div>

								<form action="">
								<div class="sign_twosec">
								<div class="section_sign">
								<div class="col-md-6 nopad">
								<div class="wrp_pre">
								<input type="submit" value="Continue" id="continue_v" name="continue_v" class="paysubmit">
								</div>
								</div>
								</div>
								<div class="clearfix"></div>
								</div>
								</form>
							</div>
							</div><!-- LOGIN SECTION ENDS -->          

														</div>        
														</div>      <!-- After Authentication Content Starts -->
											<div class="col-md-8 col-sm-8 col-xs-12 nopad fulbuki passenger-book-detail">        
											<form action="https://ziphop.com/index.php/car/pre_booking" method="POST" name="checkout-apartment" id="pre_booking_form" autocomplete="off">                               
											 <input type="hidden" name="rental_terms" value="https://static.carhire-solutions.com/pdf/cnx_tac_en-gb.pdf">                                
											 <input type="hidden" name="supplier_terms" value="https://createpdf.carhire-solutions.com/termsandconditions.aspx?reference=C7rdcA2&amp;languageId=2">                                        
											 <div class="col-md-12 nopad">            
											 <input type="hidden" name="search_id" value="8623">           
											 <input type="hidden" name="booking_source" value="PTBSID0000000011">           

														

 <div class="wrappay leftboks">              
 <div class="comon_backbg">                
 <h3 class="inpagehed">Driver Details</h3>                
 <div class="sectionbuk">                  
 <div class="collapse in" id="collapse102">                    
 <div class="onedept">                      
 <!-- <div class="evryicon"><span class="fa fa-car"></span></div> -->                      
 <div class="pasenger_location">                        
 <h3 class="inpagehedbuk"> <span class="aptbokname">Driver</span> </h3>                       
 <!-- <span class="hwonum">Adult 1</span> <span class="hwonum">Child 0</span> <span class="hwonum">Infant 0</span> --> 
</div>                      

<div class="clearfix"></div>                      
<div class="payrow1">                        
<div class="repeatprows">                          
<div class="col-md-1 col-xs-2 downsrt set_margin nopad">                            
<div class="lokter"> 
<span class="fa fa-user"></span> 
<span class="whoare">Adult</span> 
</div>                          
</div>                          

<div class="col-md-11 col-xs-12 nopad">                          
	<div class="col-sm-2 col-xs-2 set_margin">                            
	<div class="paylabel">Title <strong class="text-danger no_block">* </strong></div>                                                        
		<div class="selectedwrap">                             
			<select class="flpayinput" name="name_title">                                
			<option value="1">Mr</option>
			<option value="2">Ms</option>
			<option value="5">Mrs</option>                              
			</select>                            
		</div>                          
	</div>                          

<div class="col-sm-4 col-xs-4 set_margin">                           
<div class="paylabel">First Name <strong class="text-danger no_block">* </strong></div>                            
<input type="text" placeholder="" style="text-transform: capitalize;" name="driver_fname" id="driver_fname" class="payinput alpha_space" value="" required="" aria-required="true">
</div> 

<div class="col-sm-3 col-xs-3 set_margin">                           
<div class="paylabel">Middle Name</div>                            
<input type="text" placeholder="" style="text-transform: capitalize;" name="driver_mname" class="payinput alpha" value="">                          
</div>                          

<div class="col-sm-3 col-xs-3 set_margin">                            
<div class="paylabel">Last Name <strong class="text-danger no_block">* </strong></div>                            
<input type="text" placeholder="" style="text-transform: capitalize;" name="driver_lname" id="driver_lname" class="payinput alpha" required="" aria-required="true" value="">
</div>                                                    

<div class="col-sm-3 col-xs-3 set_margin">                          
<div class="adult_child_dob_div">                            
<div class="paylabel">Date of Birth <strong class="text-danger no_block">* </strong></div>                            
<div class="relativemask datemark pkupdt">                            
<span class="maskimg caln"></span>                            
<input type="text" name="driver_dob" id="driver_dob" class="payinput adt yearRange hasDatepicker" required="" aria-required="true" placeholder="" readonly="readonly">
</div>                          
</div>                          
</div>                                                                                

<div class="col-sm-3 col-xs-3 set_margin">                            
<div class="paylabel">Gender <strong class="text-danger no_block">* </strong></div>                              
<div class="selectedwrap">                                
<select class="flpayinput" name="gender" id="gender">                                  
<option value="Male">Male</option>                                  
<option value="Female">Female</option>                                
</select>                              
</div>                          
</div>                                                    

<div class="col-sm-6 col-xs-6 set_margin">                     
<div class="paylabel">Email <strong class="text-danger no_block">* </strong></div>                     
<input type="text" id="driver_email" name="email" class="payinput email" value="" required="" aria-required="true" placeholder="Email Address">                 
</div>                    
</div>              


</div>                      
</div>                      
</div>                  
</div>                  
</div>              
</div>            
</div>          

<div class="clearfix"></div>          

<div class="comon_backbg">            
<h3 class="inpagehed">Address</h3>            
<div class="sectionbuk billingnob">              
              <div class="payrow1">                
              <div class="col-sm-6 col-xs-12">                  
              <div class="paylabel">Street Address  <strong class="text-danger no_block">* </strong></div>                  
              <input type="text" id="address2" name="address2" class="payinput" value="" required="" aria-required="true" placeholder="">                
              </div>                

              <div class="col-sm-6 col-xs-12">                  
              <div class="paylabel">Apartment / Suite </div>                  
              <input type="text" id="street_address" name="street_address" class="payinput" value="" placeholder="">                
              </div>              
              </div>     

              <div class="payrow1">                
              <div class="col-sm-4 col-xs-12">                  
              <div class="paylabel">Country <strong class="text-danger no_block">* </strong></div>                  
              <div class="selectedwrap">                    
              <select class="flpayinput" id="country" name="country" required="">                      
              <option value="">-Select Country-</option>                      
              <option value="CA">Canada</option>                        
                      <option value="AF">Afghanistan</option>                        
                      <option value="AL">Albania</option>                        
                      <option value="DZ">Algeria</option>                        
                      <option value="AS">American Samoa</option>                        
                      <option value="AD">Andorra</option>                        
                      <option value="AO">Angola</option>                        
                      <option value="AI">Anguilla</option>                        
                      <option value="AG">Antigua and Barbuda</option>                        
                      <option value="AR">Argentina</option>                        
                      <option value="AM">Armenia</option>                        
                      <option value="AW">Aruba</option>                        
                      <option value="AU">Australia</option>                        
                      <option value="AT">Austria</option>                        
                      <option value="AZ">Azerbaijan</option>                        
                      <option value="BS">Bahamas</option>                        
                      <option value="BH">Bahrain</option>                        
                      <option value="BD">Bangladesh</option>                        
                      <option value="BB">Barbados</option>                        
                      <option value="BY">Belarus</option>                        
                      <option value="BE">Belgium</option>                        
                      <option value="BZ">Belize</option>                        
                      <option value="BJ">Benin</option>                        
                      <option value="BM">Bermuda</option>                        
                      <option value="BT">Bhutan</option>                        
                      <option value="BO">Bolivia</option>                        
                      <option value="BQ">Bonaire</option>                        
                      <option value="BA">Bosnia and Herzegovina</option>                        
                      <option value="BW">Botswana</option>                        
                      <option value="BR">Brazil</option>                        
                      <option value="IO">British Indian Ocean Territory</option>                        
                      <option value="VG">British Virgin Islands</option>                        
                      <option value="BN">Brunei</option>                        
                      <option value="BG">Bulgaria</option>                        
                      <option value="BF">Burkina Faso</option>                        
                      <option value="BI">Burundi</option>                        
                      <option value="KH">Cambodia</option>                        
                      <option value="CM">Cameroon</option>                        
                      <option value="CA">Canada</option>                        
                      <option value="CV">Cape Verde</option>                        
                      <option value="KY">Cayman Islands</option>                        
                      <option value="CF">Central African Republic</option>                        
                      <option value="TD">Chad</option>                        
                      <option value="CL">Chile</option>                        
                      <option value="CN">China</option>                        
                      <option value="CX">Christmas Island</option>                        
                      <option value="CC">Cocos [Keeling] Islands</option>                        
                      <option value="CO">Colombia</option>                        
                      <option value="KM">Comoros</option>                        
                      <option value="CD">Congo</option>                        
                      <option value="CK">Cook Islands</option>                        
                      <option value="CR">Costa Rica</option>                        
                      <option value="HR">Croatia</option>                        
                      <option value="CU">Cuba</option>                        
                      <option value="CW">Curacao</option>                        
                      <option value="CY">Cyprus</option>                        
                      <option value="CZ">Czechia</option>                        
                      <option value="DK">Denmark</option>                        
                      <option value="DJ">Djibouti</option>                        
                      <option value="DM">Dominica</option>                        
                      <option value="DO">Dominican Republic</option>                        
                      <option value="TL">East Timor</option>                        
                      <option value="EC">Ecuador</option>                        
                      <option value="EG">Egypt</option>                        
                      <option value="SV">El Salvador</option>                        
                      <option value="GQ">Equatorial Guinea</option>                        
                      <option value="ER">Eritrea</option>                        
                      <option value="EE">Estonia</option>                        
                      <option value="ET">Ethiopia</option>                        
                      <option value="FK">Falkland Islands</option>                        
                      <option value="FO">Faroe Islands</option>                        
                      <option value="FJ">Fiji</option>                        
                      <option value="FI">Finland</option>                        
                      <option value="FR">France</option>                        
                      <option value="GF">French Guiana</option>                        
                      <option value="PF">French Polynesia</option>                        
                      <option value="GA">Gabon</option>                        
                      <option value="GM">Gambia</option>                        
                      <option value="GE">Georgia</option>                        
                      <option value="DE">Germany</option>                        
                      <option value="GH">Ghana</option>                        
                      <option value="GI">Gibraltar</option>                        
                      <option value="GR">Greece</option>                        
                      <option value="GL">Greenland</option>                        
                      <option value="GD">Grenada</option>                        
                      <option value="GP">Guadeloupe</option>                        
                      <option value="GU">Guam</option>                        
                      <option value="GT">Guatemala</option>                        
                      <option value="GG">Guernsey</option>                        
                      <option value="GN">Guinea</option>                        
                      <option value="GW">Guinea-Bissau</option>                        
                      <option value="GY">Guyana</option>                        
                      <option value="HT">Haiti</option>                        
                      <option value="HN">Honduras</option>                        
                      <option value="HK">Hong Kong</option>                        
                      <option value="HU">Hungary</option>                        
                      <option value="IS">Iceland</option>                        
                      <option value="IN">India</option>                        
                      <option value="ID">Indonesia</option>                        
                      <option value="IR">Iran</option>                        
                      <option value="IQ">Iraq</option>                        
                      <option value="IE">Ireland</option>                        
                      <option value="IM">Isle of Man</option>                        
                      <option value="IL">Israel</option>                        
                      <option value="IT">Italy</option>                        
                      <option value="CI">Ivory Coast</option>                        
                      <option value="JM">Jamaica</option>                        
                      <option value="JP">Japan</option>                        
                      <option value="JE">Jersey</option>                        
                      <option value="JO">Jordan</option>                        
                      <option value="KZ">Kazakhstan</option>                        
                      <option value="KE">Kenya</option>                        
                      <option value="KI">Kiribati</option>                        
                      <option value="RS">Kosovo</option>                        
                      <option value="KW">Kuwait</option>                        
                      <option value="KG">Kyrgyzstan</option>                        
                      <option value="LA">Laos</option>                        
                      <option value="LV">Latvia</option>                        
                      <option value="LB">Lebanon</option>                        
                      <option value="LS">Lesotho</option>                        
                      <option value="LR">Liberia</option>                        
                      <option value="LY">Libya</option>                        
                      <option value="LI">Liechtenstein</option>                        
                      <option value="LT">Lithuania</option>                        
                      <option value="LU">Luxembourg</option>                        
                      <option value="MO">Macao</option>                        
                      <option value="MK">Macedonia</option>                        
                      <option value="MG">Madagascar</option>                        
                      <option value="MW">Malawi</option>                        
                      <option value="MY">Malaysia</option>                        
                      <option value="MV">Maldives</option>                        
                      <option value="ML">Mali</option>                        
                      <option value="MT">Malta</option>                        
                      <option value="MH">Marshall Islands</option>                        
                      <option value="MQ">Martinique</option>                        
                      <option value="MR">Mauritania</option>                        
                      <option value="MU">Mauritius</option>                        
                      <option value="YT">Mayotte</option>                        
                      <option value="MX">Mexico</option>                        
                      <option value="FM">Micronesia</option>                        
                      <option value="MD">Moldova</option>                        
                      <option value="MC">Monaco</option>                        
                      <option value="MN">Mongolia</option>                        
                      <option value="ME">Montenegro</option>                        
                      <option value="MS">Montserrat</option>                        
                      <option value="MA">Morocco</option>                        
                      <option value="MZ">Mozambique</option>                        
                      <option value="MM">Myanmar [Burma]</option>                        
                      <option value="NA">Namibia</option>                        
                      <option value="NR">Nauru</option>                        
                      <option value="NP">Nepal</option>                        
                      <option value="NL">Netherlands</option>                        
                      <option value="NC">New Caledonia</option>                        
                      <option value="NZ">New Zealand</option>                        
                      <option value="NI">Nicaragua</option>                        
                      <option value="NE">Niger</option>                        
                      <option value="NG">Nigeria</option>                        
                      <option value="NU">Niue</option>                        
                      <option value="NF">Norfolk Island</option>                        
                      <option value="KP">North Korea</option>                        
                      <option value="MP">Northern Mariana Islands</option>                        
                      <option value="NO">Norway</option>                        
                      <option value="OM">Oman</option>                        
                      <option value="PK">Pakistan</option>                        
                      <option value="PW">Palau</option>                        
                      <option value="PS">Palestine</option>                        
                      <option value="PA">Panama</option>                        
                      <option value="PG">Papua New Guinea</option>                        
                      <option value="PY">Paraguay</option>                        
                      <option value="PE">Peru</option>                        
                      <option value="PH">Philippines</option>                        
                      <option value="PN">Pitcairn Islands</option>                        
                      <option value="PL">Poland</option>                        
                      <option value="PT">Portugal</option>                        
                      <option value="PR">Puerto Rico</option>                        
                      <option value="QA">Qatar</option>                        
                      <option value="CG">Republic of the Congo</option>                        
                      <option value="RE">Réunion</option>                        
                      <option value="RO">Romania</option>                        
                      <option value="RU">Russia</option>                        
                      <option value="RW">Rwanda</option>                        
                      <option value="BL">Saint Barthélemy</option>                        
                      <option value="SH">Saint Helena</option>                        
                      <option value="KN">Saint Kitts and Nevis</option>                        
                      <option value="LC">Saint Lucia</option>                        
                      <option value="MF">Saint Martin (FR)</option>                        
                      <option value="PM">Saint Pierre and Miquelon</option>                        
                      <option value="VC">Saint Vincent and the Grenadines</option>                        
                      <option value="WS">Samoa</option>                        
                      <option value="SM">San Marino</option>                        
                      <option value="ST">São Tomé and Príncipe</option>                        
                      <option value="SA">Saudi Arabia</option>                        
                      <option value="SN">Senegal</option>                        
                      <option value="RS">Serbia</option>                        
                      <option value="SC">Seychelles</option>                        
                      <option value="SL">Sierra Leone</option>                        
                      <option value="SG">Singapore</option>                        
                      <option value="SX">Sint Maarten (NL)</option>                        
                      <option value="SK">Slovakia</option>                        
                      <option value="SI">Slovenia</option>                        
                      <option value="SB">Solomon Islands</option>                        
                      <option value="SO">Somalia</option>                        
                      <option value="ZA">South Africa</option>                        
                      <option value="GS">South Georgia and the South Sandwich Islands</option>                        
                      <option value="KR">South Korea</option>                        
                      <option value="SS">South Sudan</option>                        
                      <option value="ES">Spain</option>                        
                      <option value="LK">Sri Lanka</option>                        
                      <option value="SD">Sudan</option>                        
                      <option value="SR">Suriname</option>                        
                      <option value="SZ">Swaziland</option>                        
                      <option value="SE">Sweden</option>                        
                      <option value="CH">Switzerland</option>                        
                      <option value="SY">Syria</option>                        
                      <option value="TW">Taiwan</option>                        
                      <option value="TJ">Tajikistan</option>                        
                      <option value="TZ">Tanzania</option>                        
                      <option value="TH">Thailand</option>                        
                      <option value="TG">Togo</option>                        
                      <option value="TK">Tokelau</option>                        
                      <option value="TO">Tonga</option>                        
                      <option value="TT">Trinidad and Tobago</option>                        
                      <option value="TN">Tunisia</option>                        
                      <option value="TR">Turkey</option>                        
                      <option value="TM">Turkmenistan</option>                        
                      <option value="TC">Turks and Caicos Islands</option>                        
                      <option value="TV">Tuvalu</option>                        
                      <option value="VI">U.S. Virgin Islands</option>                        
                      <option value="UG">Uganda</option>                        
                      <option value="UA">Ukraine</option>                        
                      <option value="AE">United Arab Emirates</option>                        
                      <option value="GB">United Kingdom</option>                        
                      <option value="US">United States</option>                        
                      <option value="UY">Uruguay</option>                        
                      <option value="UZ">Uzbekistan</option>                        
                      <option value="VU">Vanuatu</option>                        
                      <option value="VA">Vatican City</option>                        
                      <option value="VE">Venezuela</option>                        
                      <option value="VN">Vietnam</option>                        
                      <option value="WF">Wallis and Futuna</option>                        
                      <option value="EH">Western Sahara</option>                        
                      <option value="YE">Yemen</option>                        
                      <option value="ZM">Zambia</option>                        
                      <option value="ZW">Zimbabwe</option>                        
                      <option value="AX">Åland</option>                                          
                      </select>                  

                      </div>                
                      </div>                

                      <div class="col-sm-4 col-xs-12">                 
                       <div class="paylabel">State / Province <small style="color: #ccc;"></small>  <strong class="text-danger no_block">* </strong></div>  

                  <fieldset id="state_holder">                    
                     <input type="text" id="state" name="state" class="payinput" value="" required="" placeholder="">                                        
                  </fieldset>                                      
                </div>              

                <div class="col-sm-4 col-xs-12">                  
                <div class="paylabel">City <strong class="text-danger no_block">* </strong></div>                                        
                <fieldset id="city_holder">                      
                                                 
                      <input type="text" name="city" id="city" placeholder="" value="" class="flpayinput alpha_space" required="required">                                            
                      </fieldset>                  
                      </div>              
                      </div>            

                      <div class="payrow1">                
                      <div class="col-sm-4 col-xs-12">                  
                      <div class="paylabel">Postal Code <strong class="text-danger no_block">* </strong></div>                  
                      <input type="text" id="zip" name="zip" class="payinput alpha-numeric" value="" required="" aria-required="true" placeholder="">                
                      </div>                               

                      <div class="col-sm-5  col-xs-12">                  
                      <div class="paylabel">Mobile/Cell  <strong class="text-danger no_block">* </strong></div> 

                      <!-- <input type="text" class="col-md-4 payinput1" id="country_code" value="" disabled="disabled"> -->                 
                      <div class="selectedwrap col-xs-6 nopad">                      
                      <select required="" id="country_code" class="flpayinput" aria-required="true" aria-invalid="false" name="mobile_phone_code">                      
                      <option value="+1_Canada +1">Canada +1</option>                      
                      <option value="+1_United States +1">United States +1</option>                      
                      <option value="+93_Afghanistan +93">Afghanistan +93 </option>
                      <option value="+355_Albania +355">Albania +355 </option>
                      <option value="+213_Algeria +213">Algeria +213 </option>
                      <option value="+1684_American Samoa +1684">American Samoa +1684 </option>

                      <option value="+376_Andorra +376">Andorra +376 </option>
                      <option value="+244_Angola +244">Angola +244 </option>
                      <option value="+1264_Anguilla +1264">Anguilla +1264 </option>
                      <option value="+1268_Antigua And Barbuda +1268">Antigua And Barbuda +1268 </option>
                      <option value="+54_Argentina +54">Argentina +54 </option>
                      <option value="+374_Armenia +374">Armenia +374 </option>
                      <option value="+297_Aruba +297">Aruba +297 </option>
                      <option value="+61_Christmas Island +61">Christmas Island +61 </option>
                      </select>                    
                      </div>                    

                      <div class="col-xs-6 nopad">                    
                      <input type="text" required="" value="" class="payinput1 mobile" name="mobile" id="mobile" aria-required="true" placeholder="Mobile/Cell">                    
                      </div>                
                      </div>              
                      </div>              
         

                </div>    
                </div>  

                <div class="clearfix"></div> 

                <div class="pre_summery">           
                <div class="prebok_hding spl_sigin">Extras</div>          
                </div>

                <div class="padleftpay">            
                 <input type="hidden" name="child_equip_count" class="filter_airline_gps" id="child_equip_count" value="2">            
                 <div class="pre_summery1">             
                 <div class="specfullpad">              
                 <span class="error_class hide" style="width: 33.33%;color: red;">Seat selection has reached more than permitted Limit.</span>              
                 <ul class="side_amnties marginno">               
                 
                 <li>
                     <span class="headam" style="width: 25%;">No. of Passenger(s)</span>               
                     <span class="headam" style="width: 25%;">Choose your Extras</span>                
                     <span class="headam" style="width: 25%;">Price Details (CAD)</span>                
                     <span class="headam" style="width: 25%;">Max. Price (CAD)</span>               
                 </li>                
                 <input type="hidden" class="filter_airline_gps" id="passenger_restriction_count" value="1" aria-required="true">                               
                 <input type="hidden" name="child_equip_type_0" class="filter_airline_gps" id="child_equip_type_0" value="7" aria-required="true">                
                 <li>                 
                 <span class="botomam" style="width: 25%;">                  
                 <div class="selectedwrap">                                                        
	                 <select name="child_equip_count_0" class="flpayinput" onchange="set_extras_per_rental_seat(this.value,'Infant Seat (0-1 year)','18.0384476627','tr_8350','select','child_equip_type_0','0');">                                            
		                 <option value="0">0</option>                                            
		                 <option value="1">1</option>                                            
		                 <option value="2">2</option>                                           
	                 </select>                                                       
	             </div>                 

	             </span>                 
	             <span class="botomam" style="width: 25%;">Infant Seat (0-1 year) per rental</span>                 
	             <span class="botomam" style="width: 25%;">18.04</span>                 
	             <span class="botomam" style="width: 25%;">Not Available</span>                
	             </li>  
                                
                       <input type="hidden" name="child_equip_type_1" class="filter_airline_gps" id="child_equip_type_1" value="8" aria-required="true">             
                       <li>                 
                       <span class="botomam" style="width: 25%;">                  
                       <div class="selectedwrap">                                                        
	                       <select name="child_equip_count_1" class="flpayinput" onchange="set_extras_per_rental_seat(this.value,'Child Seat (1-3 years)','18.0384476627','tr_6440','select','child_equip_type_1','0');">                                            
		                       <option value="0">0</option>                                            
		                       <option value="1">1</option>                                            
		                       <option value="2">2</option>                                           
	                       </select>                                                       
                       </div>                 
                       </span>                 
                       <span class="botomam" style="width: 25%;">Child Seat (1-3 years) per rental</span>                 
                       <span class="botomam" style="width: 25%;">18.04</span>                 
                       <span class="botomam" style="width: 25%;">Not Available</span>                
                      </li>  
                               
              </ul>             
              </div>            
              </div>      

              <div class="clearfix"></div>                   
            
            <div class="pre_summery1">             
            <div class="toppade">                     
               <div class="wrp_pre">                                  
               <div class="squaredThree">                 
               <input type="checkbox" value="13" name="gps" class="filter_airline_gps" id="gps_0" aria-required="true" onchange="set_extras_per_rental(this,'GPS (Global Positioning System)','12.6269133639','tr_3424','checkbox','tester','0');">                 

               <label for="gps_0"></label>                
               </div>                
               <label for="gps_0" class="add_extras">
               <img src="<?php echo $GLOBALS['CI']->template->template_images('gps_icon.png'); ?>" style=" margin-top: 3px;" alt=""> GPS (Global Positioning System) per rental CAD 12.63</label> 
               </div>                            
               </div>             
               <div class="clearfix"></div>            
               </div>                      
                     

             <div class="clearfix"></div>         
                     
            
            <div class="pre_summery1">             
            <div class="toppade">                               
                  <div class="wrp_pre">                
                  <div class="squaredThree">                 
	                  <input type="checkbox" value="222" name="additional_driver" class="filter_airline additional_driver_detail" id="additional_driver_detail0" data-key="0" aria-required="true" onchange="set_extras_per_rental(this,'Additional Driver','30.6653610266','tr_3196','checkbox','tester','0');">                 
	                  <label for="additional_driver_detail0"></label>                
                  </div>                
                  <label for="additional_driver_detail0" class="add_extras">
                  <img src="<?php echo $GLOBALS['CI']->template->template_images('driver_icon.png'); ?>" alt=""> Additional Driver per rental CAD 30.67 </label>               
                  </div>                                

                  <div class="repeatprows hide" id="repeatprows_driver0">                
                  <div class="col-md-3 set_margin">                 
                  <div class="paylabel">First Name <strong class="text-danger no_block">* </strong>: </div>                 
                  <input class="payinput " type="text" value="" style="text-transform: capitalize;" name="additional_driver_fname" id="additional_driver_fname" aria-required="true" placeholder="First Name">                
                  </div>                

                  <div class="col-md-3 set_margin">                 
                  <div class="paylabel">Middle Name : </div>                 
                  <input class="payinput" type="text" style="text-transform: capitalize;" value="" name="additional_driver_mname" id="additional_driver_mname0" placeholder="Middle Name"> </div>                

                  <div class="col-md-3 set_margin">                 
                  <div class="paylabel">Last Name <strong class="text-danger no_block">* </strong>: </div>                 
                  <input class="payinput" style="text-transform: capitalize;" type="text" value="" name="additional_driver_lname" aria-required="true" id="additional_driver_lname" placeholder="Last Name">                 
                  <input class="payinput" type="hidden" value="30.6653610266" name="additional_driver_cost" aria-required="true" id="additional_driver_cost">                 
                  <input class="payinput" type="hidden" value="0" name="additional_driver_max_cost" aria-required="true" id="additional_driver_max_cost" placeholder="Last Name">
                  <input class="payinput" type="hidden" value="2-per rental" name="additional_driver_unit_name" aria-required="true" id="additional_driver_unit_name" placeholder="Last Name"></div>                

                  <div class="col-md-3 set_margin">                 
                  <div class="paylabel">Date Of Birth <strong class="text-danger no_block">* </strong>: </div>                 
                  <div class="relativemask datemark pkupdt_x_driver">                  
                  <span class="maskimg caln"></span>                  
                  <input class="payinput yearRange hasDatepicker" type="text" value="" name="additional_driver_dob" id="additional_driver_dob" aria-required="true" placeholder="Date Of Birth" readonly="readonly">                 
                  </div>                
                  </div>               
                  </div>                          
                  </div>             
                  <div class="clearfix"></div>            
                  </div>                      

                  <div class="clearfix"></div> 

                 <div class="pre_summery1">            
                  <span class="noteclick" style="color: #f58931 !important;font-weight: bold !important;"> Charges for extras are payable at pickup </span>           
                  </div>          
                  </div>

           

          <div class="pre_summery">           
          <div class="prebok_hding spl_sigin">Terms &amp; Conditions</div>          
          </div> 

         <div class="padleftpay">           
	           <div class="pre_summery1">   
	           <a class="chn_crncy" style="padding:15px 15px; display: block;" data-toggle="modal" data-target="#book_terms"> Booking Terms and Conditions</a>          
	           </div>          
         </div> 



          <div class="col-md-12 col-xs-12 nopad">           
             <div id="cancel" class="modal fade">            
                <div class="modal-dialog">             
                  <div class="modal-content">              
                   <div class="popuperror" style="display:none;"></div>              
                     <div class="modal-header">               
                       <button data-dismiss="modal" class="close" type="button">×</button>               
                         <h4 class="modal-title">Cancellation Policy</h4>              
                       </div>              

                       <div class="modal-body">               
                       <div class="sectionbuk">                
                        <label class="lbllbl"><p>test cancellation</p></label>               
                       </div>            
                       </div>           
                       </div>          
                       </div>         
                       </div>         
                       <div class="clearfix"></div>         
         
         <div id="book_terms" class="modal fade" role="dialog">          
         <div class="modal-dialog">           
         <div class="modal-content">            
         <div class="modal-header">             
         <button type="button" class="close" data-dismiss="modal">×</button>             
         <h4 class="modal-title">Booking Terms and Conditions</h4>            
         </div>            

         <div class="modal-body">             
         <p></p>

         <p><strong>General Terms &amp; Conditions</strong></p>

         <p>All products purchased on <a href="http://www.ziphop.com">ZipHop.com</a> are Non-Refundable.</p>
         <p>When you book Hotels, Cars ,Transfers, Sightseeing and Apartments you enter into a contract with <a href="http://www.ziphop.com">ZipHop.com</a> and the companies providing the included services. Please read the terms and conditions carefully and ensure that you fully understand them.</p>

         <p><strong>TRAVEL DOCUMENTS</strong></p>

         <p>It is the client’s responsibility to obtain at their expense a valid passport and all documentation (including tourist visa cards and international drivers licence) or Visas required by the relevant government authorities failing which passengers will be denied boarding by the air carrier or refused entry into the country of destination. Customs and/or Immigration officials can, at their own discretion, deny a traveler&nbsp;entry into their country, even if the required information and travel documents are complete. <a href="http://www.ziphop.com">ZipHop.com</a> will not be held responsible for denied entry under any circumstance. Such events will be considered as no-show and no refund will be made for unused services.</p>

         <p>LOCAL LAWS, CUSTOMS &amp; DIFFERENT LIVING STANDARDS</p>

         <p>There are many different living standards and practices, including provisions of utilities such as water, electricity, preparation of food, etc. from those found in North America. To ensure an informed and enjoyable vacation, we recommend you discuss the relevant customs of the countries you will be visiting with appropriate tourist boards and/or embassies or consulates. Although most travel, including travel to international destinations, is completed without incident, travel to certain destinations may involve greater risk than others. Please review travel prohibitions, warnings, announcements and advisories issued by the Government prior to booking travel to international destinations. Information on conditions in various countries and the level of risk associated with travel to particular international destinations can be found at <a href="http://www.voyage.gc.ca" target="_blank">http://www.voyage.gc.ca</a></p>

         <p>PAYMENTS</p><p>Bookings Online via <a href="http://www.ziphop.com">www.ZipHop.com</a> payments must be applied at the time of booking. Price increases are not permitted once the customer has paid in full, EXCEPT increases resulting in local taxes/local pick-up fees.</p>

         <p>CREDIT CARD PAYMENTS</p>

         <p>• <a href="http://www.ziphop.com">ZipHop.com</a> does not accept any third party credit cards for bookings.</p>

         <p>• By applying a credit card as form of payment on our website, you confirm that you are in agreement of these terms and conditions.</p>

         <p>CLAIMS</p>

         <p>Any claims must be sent to <a href="mailto:Claims@ZipHop.com">Claims@ZipHop.com</a> within 10 working days of the actual occurrence date.</p>

         <p>INSURANCE</p>

         <p>Trip cancellation insurance and out of Provice Health&nbsp;insurance were offered and declined.&nbsp;&nbsp;</p>

         <p><a href="http://www.ziphop.com">ZipHop.com</a> strongly recommends the purchase of Travel insurance&nbsp;(All inclusive,Non Medical inclusive, trip cancellation &amp; Global Medical) offered by Manulife insurance.&nbsp;For more information, please visit our Insurance page on <a href="http://www.ziphop.com">ZipHop.com</a></p>

         <p></p>            
         </div>            

         <div class="modal-footer">             
         <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>            
         </div>           
         </div>          
         </div>         

         </div>         
         <div class="clearfix"></div>   

         <div id="user_terms" class="modal fade" role="dialog">          
         <div class="modal-dialog">           
         <div class="modal-content">            
         <div class="modal-header">             
         <button type="button" class="close" data-dismiss="modal">×</button>             
         <h4 class="modal-title">User Terms</h4>            
         </div>         

         <div class="modal-body">             
         <h3 class="text-center empt">No Content Available</h3>            
         </div>            

         <div class="modal-footer">             
         <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>            
         </div>           

         </div>          
         </div>         
         </div>         

         <div class="clearfix"></div> 

         <div id="privacy" class="modal fade" role="dialog">          
         <div class="modal-dialog">           
         <div class="modal-content">            
         <div class="modal-header">             
         <button type="button" class="close" data-dismiss="modal">×</button>             
         <h4 class="modal-title">Privacy Policy</h4>            
         </div>            
         <div class="modal-body">             
         <p></p>            
         </div>            

         <div class="modal-footer">             
         <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>            
         </div>           

         </div>          
         </div>         
         </div>      

	         <div class="checkcontent" id="my_err_msg">          
			         <div class="squaredThree">           
			         <input type="checkbox" value="0" name="confirm" class="filter_airline" id="squaredThree1">           
			         <label for="squaredThree1" id="my_err_msg_label"></label>          
			         </div>          
			         <label for="squaredThree1" class="lbllbl">By selecting to complete this booking I agree to pay the total amount shown, which includes Service Fees (if applicable), and I acknowledge that I have read and accept the <a class="colorbl" data-toggle="modal" data-target="#book_terms"> Booking Terms and Conditions</a> and 
			           <a class="colorbl" data-toggle="modal" href="https://ziphop.com/privacy-policy" target="_blank">Privacy Policy</a>.</label>          
	          </div>         
          </div>         

	          <div class="payrowsubmt">          
		          <div class="col-md-4 col-xs-8 fulat500 nopad">           
		          <input type="submit" class="paysubmit" name="continue" id="continue" value="Proceed To Payment">          
		          </div>          

	              <div class="col-md-8 col-xs-4 fulat500 nopad"> </div>          
	              <div class="clear"></div>          
	              <div class="lastnote"> </div>         
	          </div>        
          </div>       
          </form>      
          </div>      
     </div>    
     </div>   
     </div>

      <script type="text/javascript">
   $(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
   });
  </script>