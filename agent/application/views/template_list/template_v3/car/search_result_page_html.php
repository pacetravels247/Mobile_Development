<style type="text/css">
	
.second_sec{margin:20px 0;}
.second_sec a {text-decoration: none;}
.airinage img {width: 100%;height: 80px;}
.airinage {
    background: #F4F4F4 none repeat scroll 0 0;
    display: block;
    margin: 0 10px;
    min-height: 85px;
    overflow: hidden;
    padding: 5px;
    text-align: center;
    max-height: 180px;
    min-height: 180px;
}
.topmatrix_ailine {
    color: #333;
    margin-top: 5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: clip;
    margin: 12px 0;
}
#owl-demo2 .owl-prev {
    position: absolute;
    left: -35px;
    top: 30%;
    background: url(../images/prev_icon.png);
    width: 35px;
    height: 60px;
    background-repeat: no-repeat;
}
#owl-demo2 .owl-next {
    position: absolute;
    right: -35px;
    top: 30%;
    background: url(../images/next_icon.png);
    width: 35px;
    height: 60px;
    background-repeat: no-repeat;
}
.features {
    display: block;
    width: 70%;
    overflow: hidden;
    margin: 12px auto 5px;
}
.features li {
    float: left;
    padding: 0px 3px;
    border-right: 1px solid #cbcbcc;
    list-style: none;
}
.features li strong {
    color: #525252;
    display: block;
    float: left;
    font-size: 15px;
    font-weight: normal;
    line-height: 25px;
}
.features li.person span {
    background-position: 0 0;
}
.features li .mn-icon {
    
    display: block;
    float: left;
    height: 24px;
    margin: 0 0px 0 5px;
    width: 24px;
}
.airinage{
    transition: all 600ms ease-in-out;
}
#owl-demo2 .airinage:hover{
    border: 1px solid red;
    transition: all 600ms ease-in-out;
}

.madgrid .col-xs-12.nopad {
    width: 100%;
    display: table;
}

.sidenamedesc {
    display: block;
    width: 75%;
    display: table-cell;
}

.celhtl.width60 {
    float: none;
    display: table-cell;
    vertical-align: middle;
}

.width20 {
    float: none !important;
    vertical-align: middle;
    display: table-cell !important;
}
</style>
<?php
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/car_search.js'), 'defer' => 'defer');
// Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/jquery-1.11.0.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('owl.carousel.min.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/pax_count.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/flight_session_expiry_script.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => JAVASCRIPT_LIBRARY_DIR.'jquery.jsort.0.4.min.js', 'defer' => 'defer');
Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('page_resource/car_result.css'), 'media' => 'screen');
Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('page_resource/owl.carousel.css'), 'media' => 'screen');
Js_Loader::$js[] = array('src' => JAVASCRIPT_LIBRARY_DIR.'jquery.nicescroll.js', 'defer' => 'defer');
echo $this->template->isolated_view('share/js/lazy_loader');
//debug($car_search_params); exit;
// foreach ($car_booking_source as $t_k => $t_v) {
// 	$active_source[] = $t_v['source_id'];
//$active_source = json_encode($active_source);
?>


<?php
	//debug($modify_safe_search_data); exit;
	//$data['result'] = $car_search_params;
	$mini_loading_image = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v3.gif').'" alt="Loading........"/></div>';
	$loading_image = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v1.gif').'" alt="Loading........"/></div>';
	$template_images = $GLOBALS['CI']->template->template_images();
	?>

	<div id="page-parent">
		
		<div class="allpagewrp top80">
		<?php echo $GLOBALS['CI']->template->isolated_view('car/search_panel_summary');?>
		  <div class="clearfix"></div>
		  <div class="search-result car_search_results">
		    <div class="container">
		      <div class="filtrsrch">
		        <div class="coleft">
		          <div class="flteboxwrp">
                  
		           
		             <div class="filtersho">                        
		             <div class="avlhtls"><strong id="filter_records">130</strong> Car found
                        </div>                       
                       <span class="close_fil_box"><i class="fa fa-close"></i></span>                    
                       </div>
		            
		            <div class="fltrboxin"> 
		             <a id="reset_filters" class="pull-right">Reset All</a>
		              <div class="bnwftr">
		                
		                <div class="rangebox">
							<button data-target="#collapse501" data-toggle="collapse"
									class="collapsebtn" type="button">Price</button>
									<strong><span id="total_result_count"><?php echo $mini_loading_image?></span></strong>
							<div id="collapse501" class="in collapse price_slider1">
								<div class="price_slider1">
									<div id="core_min_max_slider_values" class="hide">
										<input type="hidden" id="core_minimum_range_value" />
										<input type="hidden" id="core_maximum_range_value" />
									</div>
									<p id="car-price" class="level"></p>
									<div id="price-range" class="" aria-disabled="false"></div>
								</div>
							</div>
						</div>

                        <div class="rangebox">
							<button data-target="#collapse505" data-toggle="collapse" class="collapsebtn" type="button">Supplier</button>
							<div id="collapse505" class="collapse in">
								<div class="boxins">
									<ul class="locationul" id="car-vendor-list-wrapper">
									<li>
										<div class="squaredThree">
										<input id="flocSquaredFive0" class="vendor-list-f" type="checkbox" name="vendor_list[]" value="Sixt">
										<label for="flocSquaredFive0"></label>
										</div>
									    <label class="lbllbl" for="flocSquaredFive0">Sixt (24)</label>
									</li>

									<li>
										<div class="squaredThree"><input id="flocSquaredFive1" class="vendor-list-f" type="checkbox" name="vendor_list[]" value="Alamo">
										<label for="flocSquaredFive1"></label>
										</div>
								     	<label class="lbllbl" for="flocSquaredFive1">Alamo (22)</label>
									</li>

									<li>
										<div class="squaredThree">
										<input id="flocSquaredFive2" class="vendor-list-f" type="checkbox" name="vendor_list[]" value="keddy by Europcar">
										<label for="flocSquaredFive2"></label>
										</div>
									    <label class="lbllbl" for="flocSquaredFive2">keddy by Europcar (11)</label>
									</li>

									<li>
										<div class="squaredThree">
										<input id="flocSquaredFive3" class="vendor-list-f" type="checkbox" name="vendor_list[]" value="Thrifty UAE">
										<label for="flocSquaredFive3"></label>
										</div>
										<label class="lbllbl" for="flocSquaredFive3">Thrifty UAE (8)</label>
									</li>

									<li>
										<div class="squaredThree">
										<input id="flocSquaredFive4" class="vendor-list-f" type="checkbox" name="vendor_list[]" value="Europcar">
										<label for="flocSquaredFive4"></label>
										</div>
										<label class="lbllbl" for="flocSquaredFive4">Europcar (11)</label>
									</li>

									<li>
										<div class="squaredThree">
										<input id="flocSquaredFive5" class="vendor-list-f" type="checkbox" name="vendor_list[]" value="Budget UAE">
										<label for="flocSquaredFive5"></label>
										</div>
										<label class="lbllbl" for="flocSquaredFive5">Budget UAE (12)</label>
									</li>

									</ul>
								</div>
							</div>
		                </div>

		              
		                 <div class="rangebox">
							<button data-target="#collapse510" data-toggle="collapse" class="collapsebtn" type="button">Auto/Manual</button>
							<div id="collapse510" class="collapse in">
								<div class="boxins">
									<ul class="locationul" id="car-vehicle-manual-wrapper">

									<li>
										<div class="squaredThree"><input id="flocSquaredTwelve0" class="vehicle-manual-f" type="checkbox" name="vehicle_manual[]" value="Automatic">
										<label for="flocSquaredTwelve0"></label>
										</div>
										<label class="lbllbl" for="flocSquaredTwelve0">Automatic (86)</label>
									</li>

									<li>
										<div class="squaredThree">
										<input id="flocSquaredTwelve1" class="vehicle-manual-f" type="checkbox" name="vehicle_manual[]" value="Manual">
										<label for="flocSquaredTwelve1"></label>
										</div>
										<label class="lbllbl" for="flocSquaredTwelve1">Manual (2)</label>
									</li>
									</ul>
								</div>
							</div>
		                </div> 
		                
		                <div class="rangebox">
							<button data-target="#collapse506" data-toggle="collapse" class="collapsebtn" type="button">Included Insurance</button>
							<div id="collapse506" class="collapse in">
								<div class="boxins">
									<ul class="locationul" id="car-package-list-wrapper">

									<li>
										<div class="squaredThree">
										<input id="flocSquaredSix0" class="coverage-type-f" type="checkbox" name="coverage_type[]" value="Collision damage waiver">
										<label for="flocSquaredSix0"></label>
										</div>
										<label class="lbllbl" for="flocSquaredSix0">Collision damage waiver (88)</label>
									</li>

									<li>
										<div class="squaredThree">
										<input id="flocSquaredSix1" class="coverage-type-f" type="checkbox" name="coverage_type[]" value="Theft protection">
										<label for="flocSquaredSix1"></label>
										</div>
										<label class="lbllbl" for="flocSquaredSix1">Theft protection (69)</label>
									</li>
									</ul>
								</div>
							</div>
		                </div>
		               
		                <div class="rangebox">
							<button data-target="#collapse515" data-toggle="collapse" class="collapsebtn" type="button">AC/Non AC</button>
							<div id="collapse515" class="collapse in">
								<div class="boxins">
									<ul class="locationul" id="car-vehicle-ac-wrapper">

									<li>
										<div class="squaredThree">
										<input id="flocSquaredEleven0" class="vehicle-ac-f" type="checkbox" name="vehicle_ac[]" value="AC">
										<label for="flocSquaredEleven0"></label>
										</div>
										<label class="lbllbl" for="flocSquaredEleven0">AC (88)</label>
									</li>

									</ul>
								</div>
							</div>
		                </div> 
		               
						
		                
		              </div>
		            </div>
		          </div>
		        </div>
		        <div class="colrit">
		          <div class="insidebosc">
                   <div class="car_matrix hide">                     
                     <div class="second_sec">
                     
                         <div id="owl-demo2" class="owl-carousel owl-theme">
                           <div class="item">
                             <span href="#" class="airinage">
                              <div class="topmatrix_ailine">Jet airways</div>
                               <img src="images/car1.jpg">
                                 <ul class="features">                                      
                                   <li class="person tooltipv">
                                     <a data-original-title="Passengers" data-toggle="tooltip"><strong>4</strong> <span class="mn-icon"></span></a>
                                   </li>
                                   <li class="person tooltipv">
                                     <a data-original-title="Passengers" data-toggle="tooltip"><strong>4</strong> <span class="mn-icon"></span></a>
                                   </li> 
                                 </ul>
                             </span>
                           </div>
                          <div class="item">
                             <span href="#" class="airinage">
                              <div class="topmatrix_ailine">Jet airways</div>
                               <img src="images/car1.jpg">
                                 <ul class="features">                                      
                                   <li class="person tooltipv">
                                     <a data-original-title="Passengers" data-toggle="tooltip"><strong>4</strong> <span class="mn-icon"></span></a>
                                   </li>
                                   <li class="person tooltipv">
                                     <a data-original-title="Passengers" data-toggle="tooltip"><strong>4</strong> <span class="mn-icon"></span></a>
                                   </li> 
                                 </ul>
                             </span>
                           </div>
                           <div class="item">
                             <span href="#" class="airinage">
                              <div class="topmatrix_ailine">Jet airways</div>
                               <img src="images/car1.jpg">
                                 <ul class="features">                                      
                                   <li class="person tooltipv">
                                     <a data-original-title="Passengers" data-toggle="tooltip"><strong>4</strong> <span class="mn-icon"></span></a>
                                   </li>
                                   <li class="person tooltipv">
                                     <a data-original-title="Passengers" data-toggle="tooltip"><strong>4</strong> <span class="mn-icon"></span></a>
                                   </li> 
                                 </ul>
                             </span>
                           </div>
                           <div class="item">
                             <span href="#" class="airinage">
                              <div class="topmatrix_ailine">Jet airways</div>
                               <img src="images/car1.jpg">
                                 <ul class="features">                                      
                                   <li class="person tooltipv">
                                     <a data-original-title="Passengers" data-toggle="tooltip"><strong>4</strong> <span class="mn-icon"></span></a>
                                   </li>
                                   <li class="person tooltipv">
                                     <a data-original-title="Passengers" data-toggle="tooltip"><strong>4</strong> <span class="mn-icon"></span></a>
                                   </li> 
                                 </ul>
                             </span>
                           </div>
                           <div class="item">
                             <span href="#" class="airinage">
                              <div class="topmatrix_ailine">Jet airways</div>
                               <img src="images/car1.jpg">
                                 <ul class="features">                                      
                                   <li class="person tooltipv">
                                     <a data-original-title="Passengers" data-toggle="tooltip"><strong>4</strong> <span class="mn-icon"></span></a>
                                   </li>
                                   <li class="person tooltipv">
                                     <a data-original-title="Passengers" data-toggle="tooltip"><strong>4</strong> <span class="mn-icon"></span></a>
                                   </li> 
                                 </ul>
                             </span>
                           </div>
                           <div class="item">
                             <span href="#" class="airinage">
                              <div class="topmatrix_ailine">Jet airways</div>
                               <img src="images/car1.jpg">
                                 <ul class="features">                                      
                                   <li class="person tooltipv">
                                     <a data-original-title="Passengers" data-toggle="tooltip"><strong>4</strong> <span class="mn-icon"></span></a>
                                   </li>
                                   <li class="person tooltipv">
                                     <a data-original-title="Passengers" data-toggle="tooltip"><strong>4</strong> <span class="mn-icon"></span></a>
                                   </li> 
                                 </ul>
                             </span>
                           </div>
                           <div class="item">
                             <span href="#" class="airinage">
                              <div class="topmatrix_ailine">Jet airways</div>
                               <img src="images/car1.jpg">
                                 <ul class="features">                                      
                                   <li class="person tooltipv">
                                     <a data-original-title="Passengers" data-toggle="tooltip"><strong>4</strong> <span class="mn-icon"></span></a>
                                   </li>
                                   <li class="person tooltipv">
                                     <a data-original-title="Passengers" data-toggle="tooltip"><strong>4</strong> <span class="mn-icon"></span></a>
                                   </li> 
                                 </ul>
                             </span>
                           </div>
                           <div class="item">
                             <span href="#" class="airinage">
                              <div class="topmatrix_ailine">Jet airways</div>
                               <img src="images/car1.jpg">
                                 <ul class="features">                                      
                                   <li class="person tooltipv">
                                     <a data-original-title="Passengers" data-toggle="tooltip"><strong>4</strong> <span class="mn-icon"></span></a>
                                   </li>
                                   <li class="person tooltipv">
                                     <a data-original-title="Passengers" data-toggle="tooltip"><strong>4</strong> <span class="mn-icon"></span></a>
                                   </li> 
                                 </ul>
                             </span>
                           </div>
                          
                         </div>
                      
                     </div> 
                  </div>

                  <div class="topmisty hote_reslts">              
                  <div class="col-xs-12 nopad fullshort">               
                   <button class="filter_show"><i class="fa fa-filter"></i> <span class="text_filt">Filter</span></button>                

	                   <div class="insidemyt">                  
		                   <div class="col-xs-12 nopad">                    
			                   <ul class="sortul">                    
			                   <li class="sortli" data-sort="hn">
			                   <a class="sorta asc name-l-2-h" data-order="asc"><span class="fa fa-sort-alpha-asc"></span> Car Name</a>
			                   <a class="sorta des name-h-2-l hide" data-order="desc"><span class="sirticon fa fa-sort-alpha-desc"></span> Car Name</a>
			                   </li>

			                   <li class="sortli" data-sort="s">
			                   <a class="sorta asc supplier-l-2-h" data-order="asc"><span class="sirticon fa fa-user"></span> Supplier</a>
			                   <a class="sorta des supplier-h-2-l hide" data-order="desc"><span class="sirticon fa fa-user"></span> Supplier</a>
			                   </li>

			                   <li class="sortli" data-sort="sr">
			                   <a class="sorta asc cartype-l-2-h" data-order="asc"><span class="sirticon fa fa-star-o"></span> Category</a>
			                   <a class="sorta des cartype-h-2-l hide" data-order="desc"><span class="sirticon fa fa-star-o"></span> Category</a>
			                   </li>                        

			                   <li class="sortli" data-sort="p">
			                   <a class="sorta asc price-l-2-h hide" data-order="asc"><span class="sirticon fa fa-tag"></span> Price</a>
			                   <a class="sorta des price-h-2-l active" data-order="desc"><span class="sirticon fa fa-tag"></span> Price</a>
			                   </li>
			                   </ul>                  
		                   </div>                
	                   </div>              
                   </div>            
                   </div>
		           
		            <!--All Available cars result comes here -->
					<div class="allresult">
						<div class="car_filter_load" style="display: none;">
						<!-- <span>Please Wait....</span> -->
						<div class="car_gif">
							<div class="imagenofnd"><!-- <img src="<?=$template_images?>preloader_car.gif" alt="Empty" /> --></div>
						</div> 
						</div>
                    
						<div class="car_results" id="car_search_result">

						<div class="rowresult r-r-i" rel="sort0">          
                              <div class="madgrid">     
                              <div class="col-xs-12 nopad">      
                              <div class="sidenamedesc mobile_f_i">       
                              <div class="celhtl width20 midlbord">        
                              <div class="car_image"> 
                              <img src="https://static.carhire-solutions.com/images/car/Sixt/small/AE_EDAR.jpg" class="lazy lazy_loader h-img" onerror="this.onerror=null;this.src='/airliners_new/extras/system/template_list/template_v1/images/no-img.jpg';">
                              </div>       
                              </div>       

                              <div class="celhtl width60">        
                              <div class="waymensn">         
                              <div class="flitruo_hotel">          
                              <div class="hoteldist"> 
                                    <span class="supplier_name hide">sixt</span>           
                                    <span class="car_type hide">1</span>           
                                    <span class="car_name">Mazda 2 Aut.<span> or Similar</span></span>           
                                    <div class="clearfix"></div>           
                                    <span class="hotel_address elipsetool"></span>           
                                    <div class="clearfix"></div>           

                                    <div class="pick cr_wdt">            
	                                    <i class="fal fa-map-marker-alt"></i> <span>Pickup Location:</span>            
	                                    <h3>Dubai International Airport (DXB),United Arab Emirates</h3>           
                                    </div>           

                                    <div class="pick">    
                                       <span class="fuel_icon">Fuel:F2F</span>            
                                       <h3> <a href="#" data-toggle="tooltip" title="" data-original-title="Full to Full: Pick up and drop off with a full tank. If the car is not returned with a full tank, suppliers will charge fuel plus refueling charges. ">              
                                       <i class="fa fa-info-circle" aria-hidden="true"></i> Petrol</a>            
                                       </h3>           
                                   </div>           

                                   <div class="pick">            
                                  <i class="fal fa-tachometer-alt"></i> <span>Mileage Allowance:</span>            
                                   <h3>Unlimited</h3>           
                                   </div>  

                                       <div class="clearfix"></div>          

                                   <div class="middleCol">            
                                   <ul class="features">             
                                       <li class="person tooltipv">              
                                       <a title="" data-toggle="tooltip" data-original-title="Passengers"><strong>5</strong> 
                                       <span class="mn-icon"></span>               
                                       </a>             
                                       </li> 
                                        <li class="baggage tooltipv">              
                                        <a title="" data-toggle="tooltip" data-original-title="Bags"> <strong>3</strong> 
                                        <span class="mn-icon"></span>               
                                        </a>             
                                        </li> 

                                         <li class="doors tooltipv">              
                                         <a title="" data-toggle="tooltip" data-original-title="Doors"> 
                                         <strong>4</strong> <span class="mn-icon"></span>               
                                         </a>             
                                         </li>    
                                    </ul>          
                                   <div class="suplier_logo"> <img src="https://static.carhire-solutions.com/images/supplier/logo/logo11.png" alt=""></div>             
                                  </div>             


            <div class="clearfix"></div>            

            </div>           
            </div>          
            </div>         
            </div>        
            </div>        

            <div class="width20 mobile_f_i">         
	            <div class="mrinfrmtn">          
		            <div class="sidepricewrp">           
			            <div class="sideprice">           
			            <strong>INR </strong>             
			            <span class="f-p" data-price="39.06">39.06</span>            
			            <span class="price-order hide">39.06</span>           
			            </div>           
		                <span class="text-center non_ref" style="color:#0B9FD1; display: block;">Non Refundable</span>  

			            <div class="bookbtn">            
			            <input type="submit" value="Book" class="booknow">         

			           </div>          
		           </div> 
	               <a class="detailsflt" data-toggle="collapse" data-target="#car_rental0"> More <label>Details</label><span class="caret"></span> </a>        
	           </div>       
           </div>       
        </div>             

        <div class="clearfix"></div>            

        <div id="car_rental0" class="collapse" data-role="dialog">       
        <div class="carextent">        
        <div class="modal-content1">         
        <div class="clearfix"></div>         
        <div class="modal-body1">          
        <div class="col-xs-12 nopad">           
        <div class="middleCol">            
	        <ul class="features1">                                      
	             <li data-original-title="gear" class="transmission tooltipv"><span class="mn-icon"></span> <strong>Automatic</strong></li>  
	             <li data-original-title="Air Conditioning" class="ac tooltipv"><span class="mn-icon"></span> <strong>A/C</strong></li>  
	             <li class="fuel tooltipv" data-toggle="tooltip" title="" data-original-title="Registration Fee"><i class="fa fa-plus"></i><strong> Registration Fee</strong></li>            
	        </ul>                                             
         </div>           

         <div class="clearfix"></div>           

         <div class="rentcondition">            
         <div class="hotel_detailtab">             
         <div class="clearfix"></div>             
         <div class="tab-content"> 
              <div class="tab-pane active" id="htldets0">               
              <div class="innertabs">                
              <div class="secn_pot"> 
                 <div class="includ">                  
                 <div class="parasub">                   
                 <ul class="checklist">                                          
                 <li> <span class="fa fa-check"></span>Supplementary Liability Insurance</li>                                            
                 <li> <span class="fa fa-check"></span>Theft protection</li>                                            
                 <li> <span class="fa fa-check"></span>Airport Service Charge</li>                                            
                 <li> <span class="fa fa-check"></span>One way rental</li>                                            
                 <li> <span class="fa fa-check"></span>VAT</li>                                         
                 </ul>                  
                 </div>                 
                 </div>                  
                  <div class="linebrk"></div>          

                  <button type="button" class="sumtab" data-toggle="collapse" data-target="#agelmt">Age Limit</button>                  
                  <div class="collapse in age_lmt" id="agelmt">                   
                  <div class="parasub">                    
                  <ul class="checklist">                     
                     <li> <span>Minimum age: <strong>30</strong></span></li>                     
                     <li> <span>Maximum age: <strong>80</strong></span></li>                      
                  </ul>                   
                  </div>                  
                  </div>                  
                  <div class="linebrk"></div>                                   
                  <div class="clearfix"></div>                 
                  <p class="carhead">Offer Includes</p>                 

                  <div id="see_more0" class="collapse in">                   
                  <div class="carprc clearfix">                     
                  <button type="button" class="sumtab" data-toggle="collapse" id="carnect0" data-target="#agelmt00"><b>Registration Fee</b></button>
                       <div class="prcright" style="text-align: right;">                      
                       <div class="parasub">
                       <p class="car_price_new" style="color: #f58830">INR 1.37</p>                      
                       </div>                     
                       </div>                     
                                <div class="clearfix"></div>                     
                       <div id="agelmt00" class="collapse">                      
                       <p style="padding-left: 28px;">Pay on pick-up in local currency:per day: 4.00 INR</p>                     
                       </div>                    
                  </div>
                                       
                                       <div class="carprc clearfix">           
	                                       <button type="button" class="sumtab" data-toggle="collapse" id="carnect0" data-target="#agelmt01">
	                                       <b>Collision damage waiver</b>
	                                       </button>           

	                                       <div class="prcright" style="text-align: right;">            
	                                       <div class="parasub">
	                                       <p class="car_price_new" style="color: #f58830">INR 0.00</p>            
	                                       </div>           
	                                       </div>           
	                                       <div class="clearfix"></div>           
		                                       <div id="agelmt01" class="collapse">            
		                                          <p style="padding-left: 28px;">with excess up to 1,500 INR</p>           
		                                       </div>          
                                       </div>

                                       <div class="carprc clearfix">           
                                       <button type="button" class="sumtab" data-toggle="collapse" id="carnect0" data-target="#agelmt02">
                                            <b>Supplementary Liability Insurance
                                            </b>
                                       </button>           

                                       <div class="prcright" style="text-align: right;">            
                                       <div class="parasub">
                                       <p class="car_price_new" style="color: #f58830">INR 0.00</p>            
                                       </div>           
                                       </div>           
                                       <div class="clearfix"></div>           

                                       <div id="agelmt02" class="collapse">            

                                       <p style="padding-left: 28px;">Legally required, insurance for damages on the adversarial vehicle, persons and objects- In this offer it is included.</p>           
                                       </div>          
                                       </div>
                                       <div class="carprc clearfix">           
                                       <button type="button" class="sumtab" data-toggle="collapse" id="carnect0" data-target="#agelmt03">
                                           <b>Theft protection</b>
                                       </button>   

                                       <div class="prcright" style="text-align: right;">            
	                                       <div class="parasub"><p class="car_price_new" style="color: #f58830">INR 0.00</p></div>           
	                                     </div>           
                                       <div class="clearfix"></div>    

                                       <div id="agelmt03" class="collapse">            
                                          <p style="padding-left: 28px;">with excess up to 1,500 INR</p>           
                                       </div>          

                                       </div>

                                               </div>                                
                                              </div>               
                                             </div>              
                                            </div>              
                                           <div class="clearfix"></div>             
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


						     <div class="rowresult r-r-i" rel="sort0">          
                              <div class="madgrid">     
                              <div class="col-xs-12 nopad">      
                              <div class="sidenamedesc mobile_f_i">       
                              <div class="celhtl width20 midlbord">        
                              <div class="car_image"> 
                              <img src="https://static.carhire-solutions.com/images/car/Sixt/small/AE_EDAR.jpg" class="lazy lazy_loader h-img" onerror="this.onerror=null;this.src='/airliners_new/extras/system/template_list/template_v1/images/no-img.jpg';">
                              </div>       
                              </div>       

                              <div class="celhtl width60">        
                              <div class="waymensn">         
                              <div class="flitruo_hotel">          
                              <div class="hoteldist"> 
                                    <span class="supplier_name hide">sixt</span>           
                                    <span class="car_type hide">1</span>           
                                    <span class="car_name">Mazda 2 Aut.<span> or Similar</span></span>           
                                    <div class="clearfix"></div>           
                                    <span class="hotel_address elipsetool"></span>           
                                    <div class="clearfix"></div>           

                                    <div class="pick cr_wdt">            
	                                    <i class="fal fa-map-marker-alt"></i> <span>Pickup Location:</span>            
	                                    <h3>Dubai International Airport (DXB),United Arab Emirates</h3>           
                                    </div>           

                                    <div class="pick">    
                                       <span class="fuel_icon">Fuel:F2F</span>            
                                       <h3> <a href="#" data-toggle="tooltip" title="" data-original-title="Full to Full: Pick up and drop off with a full tank. If the car is not returned with a full tank, suppliers will charge fuel plus refueling charges. ">              
                                       <i class="fa fa-info-circle" aria-hidden="true"></i> Petrol</a>            
                                       </h3>           
                                   </div>           

                                   <div class="pick">            
                                  <i class="fal fa-tachometer-alt"></i> <span>Mileage Allowance:</span>            
                                   <h3>Unlimited</h3>           
                                   </div>  

                                       <div class="clearfix"></div>          

                                   <div class="middleCol">            
                                   <ul class="features">             
                                       <li class="person tooltipv">              
                                       <a title="" data-toggle="tooltip" data-original-title="Passengers"><strong>5</strong> 
                                       <span class="mn-icon"></span>               
                                       </a>             
                                       </li> 
                                        <li class="baggage tooltipv">              
                                        <a title="" data-toggle="tooltip" data-original-title="Bags"> <strong>3</strong> 
                                        <span class="mn-icon"></span>               
                                        </a>             
                                        </li> 

                                         <li class="doors tooltipv">              
                                         <a title="" data-toggle="tooltip" data-original-title="Doors"> 
                                         <strong>4</strong> <span class="mn-icon"></span>               
                                         </a>             
                                         </li>    
                                    </ul>          
                                   <div class="suplier_logo"> <img src="https://static.carhire-solutions.com/images/supplier/logo/logo11.png" alt=""></div>             
                                  </div>             


            <div class="clearfix"></div>            

            </div>           
            </div>          
            </div>         
            </div>        
            </div>        

            <div class="width20 mobile_f_i">         
	            <div class="mrinfrmtn">          
		            <div class="sidepricewrp">           
			            <div class="sideprice">           
			            <strong>INR </strong>             
			            <span class="f-p" data-price="39.06">39.06</span>            
			            <span class="price-order hide">39.06</span>           
			            </div>           
		                <span class="text-center non_ref" style="color:#0B9FD1; display: block;">Non Refundable</span>  

			            <div class="bookbtn">            
			            <input type="submit" value="Book" class="booknow">         

			           </div>          
		           </div> 
	               <a class="detailsflt" data-toggle="collapse" data-target="#car_rental0"> More <label>Details</label><span class="caret"></span> </a>        
	           </div>       
           </div>       
        </div>             

        <div class="clearfix"></div>            

        <div id="car_rental0" class="collapse" data-role="dialog">       
        <div class="carextent">        
        <div class="modal-content1">         
        <div class="clearfix"></div>         
        <div class="modal-body1">          
        <div class="col-xs-12 nopad">           
        <div class="middleCol">            
	        <ul class="features1">                                      
	             <li data-original-title="gear" class="transmission tooltipv"><span class="mn-icon"></span> <strong>Automatic</strong></li>  
	             <li data-original-title="Air Conditioning" class="ac tooltipv"><span class="mn-icon"></span> <strong>A/C</strong></li>  
	             <li class="fuel tooltipv" data-toggle="tooltip" title="" data-original-title="Registration Fee"><i class="fa fa-plus"></i><strong> Registration Fee</strong></li>            
	        </ul>                                             
         </div>           

         <div class="clearfix"></div>           

         <div class="rentcondition">            
         <div class="hotel_detailtab">             
         <div class="clearfix"></div>             
         <div class="tab-content"> 
              <div class="tab-pane active" id="htldets0">               
              <div class="innertabs">                
              <div class="secn_pot"> 
                 <div class="includ">                  
                 <div class="parasub">                   
                 <ul class="checklist">                                          
                 <li> <span class="fa fa-check"></span>Supplementary Liability Insurance</li>                                            
                 <li> <span class="fa fa-check"></span>Theft protection</li>                                            
                 <li> <span class="fa fa-check"></span>Airport Service Charge</li>                                            
                 <li> <span class="fa fa-check"></span>One way rental</li>                                            
                 <li> <span class="fa fa-check"></span>VAT</li>                                         
                 </ul>                  
                 </div>                 
                 </div>                  
                  <div class="linebrk"></div>          

                  <button type="button" class="sumtab" data-toggle="collapse" data-target="#agelmt">Age Limit</button>                  
                  <div class="collapse in age_lmt" id="agelmt">                   
                  <div class="parasub">                    
                  <ul class="checklist">                     
                     <li> <span>Minimum age: <strong>30</strong></span></li>                     
                     <li> <span>Maximum age: <strong>80</strong></span></li>                      
                  </ul>                   
                  </div>                  
                  </div>                  
                  <div class="linebrk"></div>                                   
                  <div class="clearfix"></div>                 
                  <p class="carhead">Offer Includes</p>                 

                  <div id="see_more0" class="collapse in">                   
                  <div class="carprc clearfix">                     
                  <button type="button" class="sumtab" data-toggle="collapse" id="carnect0" data-target="#agelmt00"><b>Registration Fee</b></button>
                       <div class="prcright" style="text-align: right;">                      
                       <div class="parasub">
                       <p class="car_price_new" style="color: #f58830">INR 1.37</p>                      
                       </div>                     
                       </div>                     
                                <div class="clearfix"></div>                     
                       <div id="agelmt00" class="collapse">                      
                       <p style="padding-left: 28px;">Pay on pick-up in local currency:per day: 4.00 INR</p>                     
                       </div>                    
                  </div>
                                       
                                       <div class="carprc clearfix">           
	                                       <button type="button" class="sumtab" data-toggle="collapse" id="carnect0" data-target="#agelmt01">
	                                       <b>Collision damage waiver</b>
	                                       </button>           

	                                       <div class="prcright" style="text-align: right;">            
	                                       <div class="parasub">
	                                       <p class="car_price_new" style="color: #f58830">INR 0.00</p>            
	                                       </div>           
	                                       </div>           
	                                       <div class="clearfix"></div>           
		                                       <div id="agelmt01" class="collapse">            
		                                          <p style="padding-left: 28px;">with excess up to 1,500 INR</p>           
		                                       </div>          
                                       </div>

                                       <div class="carprc clearfix">           
                                       <button type="button" class="sumtab" data-toggle="collapse" id="carnect0" data-target="#agelmt02">
                                            <b>Supplementary Liability Insurance
                                            </b>
                                       </button>           

                                       <div class="prcright" style="text-align: right;">            
                                       <div class="parasub">
                                       <p class="car_price_new" style="color: #f58830">INR 0.00</p>            
                                       </div>           
                                       </div>           
                                       <div class="clearfix"></div>           

                                       <div id="agelmt02" class="collapse">            

                                       <p style="padding-left: 28px;">Legally required, insurance for damages on the adversarial vehicle, persons and objects- In this offer it is included.</p>           
                                       </div>          
                                       </div>
                                       <div class="carprc clearfix">           
                                       <button type="button" class="sumtab" data-toggle="collapse" id="carnect0" data-target="#agelmt03">
                                           <b>Theft protection</b>
                                       </button>   

                                       <div class="prcright" style="text-align: right;">            
	                                       <div class="parasub"><p class="car_price_new" style="color: #f58830">INR 0.00</p></div>           
	                                     </div>           
                                       <div class="clearfix"></div>    

                                       <div id="agelmt03" class="collapse">            
                                          <p style="padding-left: 28px;">with excess up to 1,500 INR</p>           
                                       </div>          

                                       </div>

                                               </div>                                
                                              </div>               
                                             </div>              
                                            </div>              
                                           <div class="clearfix"></div>             
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



						     <div class="rowresult r-r-i" rel="sort0">          
                              <div class="madgrid">     
                              <div class="col-xs-12 nopad">      
                              <div class="sidenamedesc mobile_f_i">       
                              <div class="celhtl width20 midlbord">        
                              <div class="car_image"> 
                              <img src="https://static.carhire-solutions.com/images/car/Sixt/small/AE_EDAR.jpg" class="lazy lazy_loader h-img" onerror="this.onerror=null;this.src='/airliners_new/extras/system/template_list/template_v1/images/no-img.jpg';">
                              </div>       
                              </div>       

                              <div class="celhtl width60">        
                              <div class="waymensn">         
                              <div class="flitruo_hotel">          
                              <div class="hoteldist"> 
                                    <span class="supplier_name hide">sixt</span>           
                                    <span class="car_type hide">1</span>           
                                    <span class="car_name">Mazda 2 Aut.<span> or Similar</span></span>           
                                    <div class="clearfix"></div>           
                                    <span class="hotel_address elipsetool"></span>           
                                    <div class="clearfix"></div>           

                                    <div class="pick cr_wdt">            
	                                    <i class="fal fa-map-marker-alt"></i> <span>Pickup Location:</span>            
	                                    <h3>Dubai International Airport (DXB),United Arab Emirates</h3>           
                                    </div>           

                                    <div class="pick">    
                                       <span class="fuel_icon">Fuel:F2F</span>            
                                       <h3> <a href="#" data-toggle="tooltip" title="" data-original-title="Full to Full: Pick up and drop off with a full tank. If the car is not returned with a full tank, suppliers will charge fuel plus refueling charges. ">              
                                       <i class="fa fa-info-circle" aria-hidden="true"></i> Petrol</a>            
                                       </h3>           
                                   </div>           

                                   <div class="pick">            
                                  <i class="fal fa-tachometer-alt"></i> <span>Mileage Allowance:</span>            
                                   <h3>Unlimited</h3>           
                                   </div>  

                                       <div class="clearfix"></div>          

                                   <div class="middleCol">            
                                   <ul class="features">             
                                       <li class="person tooltipv">              
                                       <a title="" data-toggle="tooltip" data-original-title="Passengers"><strong>5</strong> 
                                       <span class="mn-icon"></span>               
                                       </a>             
                                       </li> 
                                        <li class="baggage tooltipv">              
                                        <a title="" data-toggle="tooltip" data-original-title="Bags"> <strong>3</strong> 
                                        <span class="mn-icon"></span>               
                                        </a>             
                                        </li> 

                                         <li class="doors tooltipv">              
                                         <a title="" data-toggle="tooltip" data-original-title="Doors"> 
                                         <strong>4</strong> <span class="mn-icon"></span>               
                                         </a>             
                                         </li>    
                                    </ul>          
                                   <div class="suplier_logo"> <img src="https://static.carhire-solutions.com/images/supplier/logo/logo11.png" alt=""></div>             
                                  </div>             


            <div class="clearfix"></div>            

            </div>           
            </div>          
            </div>         
            </div>        
            </div>        

            <div class="width20 mobile_f_i">         
	            <div class="mrinfrmtn">          
		            <div class="sidepricewrp">           
			            <div class="sideprice">           
			            <strong>INR </strong>             
			            <span class="f-p" data-price="39.06">39.06</span>            
			            <span class="price-order hide">39.06</span>           
			            </div>           
		                <span class="text-center non_ref" style="color:#0B9FD1; display: block;">Non Refundable</span>  

			            <div class="bookbtn">            
			            <input type="submit" value="Book" class="booknow">         

			           </div>          
		           </div> 
	               <a class="detailsflt" data-toggle="collapse" data-target="#car_rental0"> More <label>Details</label><span class="caret"></span> </a>        
	           </div>       
           </div>       
        </div>             

        <div class="clearfix"></div>            

        <div id="car_rental0" class="collapse" data-role="dialog">       
        <div class="carextent">        
        <div class="modal-content1">         
        <div class="clearfix"></div>         
        <div class="modal-body1">          
        <div class="col-xs-12 nopad">           
        <div class="middleCol">            
	        <ul class="features1">                                      
	             <li data-original-title="gear" class="transmission tooltipv"><span class="mn-icon"></span> <strong>Automatic</strong></li>  
	             <li data-original-title="Air Conditioning" class="ac tooltipv"><span class="mn-icon"></span> <strong>A/C</strong></li>  
	             <li class="fuel tooltipv" data-toggle="tooltip" title="" data-original-title="Registration Fee"><i class="fa fa-plus"></i><strong> Registration Fee</strong></li>            
	        </ul>                                             
         </div>           

         <div class="clearfix"></div>           

         <div class="rentcondition">            
         <div class="hotel_detailtab">             
         <div class="clearfix"></div>             
         <div class="tab-content"> 
              <div class="tab-pane active" id="htldets0">               
              <div class="innertabs">                
              <div class="secn_pot"> 
                 <div class="includ">                  
                 <div class="parasub">                   
                 <ul class="checklist">                                          
                 <li> <span class="fa fa-check"></span>Supplementary Liability Insurance</li>                                            
                 <li> <span class="fa fa-check"></span>Theft protection</li>                                            
                 <li> <span class="fa fa-check"></span>Airport Service Charge</li>                                            
                 <li> <span class="fa fa-check"></span>One way rental</li>                                            
                 <li> <span class="fa fa-check"></span>VAT</li>                                         
                 </ul>                  
                 </div>                 
                 </div>                  
                  <div class="linebrk"></div>          

                  <button type="button" class="sumtab" data-toggle="collapse" data-target="#agelmt">Age Limit</button>                  
                  <div class="collapse in age_lmt" id="agelmt">                   
                  <div class="parasub">                    
                  <ul class="checklist">                     
                     <li> <span>Minimum age: <strong>30</strong></span></li>                     
                     <li> <span>Maximum age: <strong>80</strong></span></li>                      
                  </ul>                   
                  </div>                  
                  </div>                  
                  <div class="linebrk"></div>                                   
                  <div class="clearfix"></div>                 
                  <p class="carhead">Offer Includes</p>                 

                  <div id="see_more0" class="collapse in">                   
                  <div class="carprc clearfix">                     
                  <button type="button" class="sumtab" data-toggle="collapse" id="carnect0" data-target="#agelmt00"><b>Registration Fee</b></button>
                       <div class="prcright" style="text-align: right;">                      
                       <div class="parasub">
                       <p class="car_price_new" style="color: #f58830">INR 1.37</p>                      
                       </div>                     
                       </div>                     
                                <div class="clearfix"></div>                     
                       <div id="agelmt00" class="collapse">                      
                       <p style="padding-left: 28px;">Pay on pick-up in local currency:per day: 4.00 INR</p>                     
                       </div>                    
                  </div>
                                       
                                       <div class="carprc clearfix">           
	                                       <button type="button" class="sumtab" data-toggle="collapse" id="carnect0" data-target="#agelmt01">
	                                       <b>Collision damage waiver</b>
	                                       </button>           

	                                       <div class="prcright" style="text-align: right;">            
	                                       <div class="parasub">
	                                       <p class="car_price_new" style="color: #f58830">INR 0.00</p>            
	                                       </div>           
	                                       </div>           
	                                       <div class="clearfix"></div>           
		                                       <div id="agelmt01" class="collapse">            
		                                          <p style="padding-left: 28px;">with excess up to 1,500 INR</p>           
		                                       </div>          
                                       </div>

                                       <div class="carprc clearfix">           
                                       <button type="button" class="sumtab" data-toggle="collapse" id="carnect0" data-target="#agelmt02">
                                            <b>Supplementary Liability Insurance
                                            </b>
                                       </button>           

                                       <div class="prcright" style="text-align: right;">            
                                       <div class="parasub">
                                       <p class="car_price_new" style="color: #f58830">INR 0.00</p>            
                                       </div>           
                                       </div>           
                                       <div class="clearfix"></div>           

                                       <div id="agelmt02" class="collapse">            

                                       <p style="padding-left: 28px;">Legally required, insurance for damages on the adversarial vehicle, persons and objects- In this offer it is included.</p>           
                                       </div>          
                                       </div>
                                       <div class="carprc clearfix">           
                                       <button type="button" class="sumtab" data-toggle="collapse" id="carnect0" data-target="#agelmt03">
                                           <b>Theft protection</b>
                                       </button>   

                                       <div class="prcright" style="text-align: right;">            
	                                       <div class="parasub"><p class="car_price_new" style="color: #f58830">INR 0.00</p></div>           
	                                     </div>           
                                       <div class="clearfix"></div>    

                                       <div id="agelmt03" class="collapse">            
                                          <p style="padding-left: 28px;">with excess up to 1,500 INR</p>           
                                       </div>          

                                       </div>

                                               </div>                                
                                              </div>               
                                             </div>              
                                            </div>              
                                           <div class="clearfix"></div>             
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


						     <div class="rowresult r-r-i" rel="sort0">          
                              <div class="madgrid">     
                              <div class="col-xs-12 nopad">      
                              <div class="sidenamedesc mobile_f_i">       
                              <div class="celhtl width20 midlbord">        
                              <div class="car_image"> 
                              <img src="https://static.carhire-solutions.com/images/car/Sixt/small/AE_EDAR.jpg" class="lazy lazy_loader h-img" onerror="this.onerror=null;this.src='/airliners_new/extras/system/template_list/template_v1/images/no-img.jpg';">
                              </div>       
                              </div>       

                              <div class="celhtl width60">        
                              <div class="waymensn">         
                              <div class="flitruo_hotel">          
                              <div class="hoteldist"> 
                                    <span class="supplier_name hide">sixt</span>           
                                    <span class="car_type hide">1</span>           
                                    <span class="car_name">Mazda 2 Aut.<span> or Similar</span></span>           
                                    <div class="clearfix"></div>           
                                    <span class="hotel_address elipsetool"></span>           
                                    <div class="clearfix"></div>           

                                    <div class="pick cr_wdt">            
	                                    <i class="fal fa-map-marker-alt"></i> <span>Pickup Location:</span>            
	                                    <h3>Dubai International Airport (DXB),United Arab Emirates</h3>           
                                    </div>           

                                    <div class="pick">    
                                       <span class="fuel_icon">Fuel:F2F</span>            
                                       <h3> <a href="#" data-toggle="tooltip" title="" data-original-title="Full to Full: Pick up and drop off with a full tank. If the car is not returned with a full tank, suppliers will charge fuel plus refueling charges. ">              
                                       <i class="fa fa-info-circle" aria-hidden="true"></i> Petrol</a>            
                                       </h3>           
                                   </div>           

                                   <div class="pick">            
                                  <i class="fal fa-tachometer-alt"></i> <span>Mileage Allowance:</span>            
                                   <h3>Unlimited</h3>           
                                   </div>  

                                       <div class="clearfix"></div>          

                                   <div class="middleCol">            
                                   <ul class="features">             
                                       <li class="person tooltipv">              
                                       <a title="" data-toggle="tooltip" data-original-title="Passengers"><strong>5</strong> 
                                       <span class="mn-icon"></span>               
                                       </a>             
                                       </li> 
                                        <li class="baggage tooltipv">              
                                        <a title="" data-toggle="tooltip" data-original-title="Bags"> <strong>3</strong> 
                                        <span class="mn-icon"></span>               
                                        </a>             
                                        </li> 

                                         <li class="doors tooltipv">              
                                         <a title="" data-toggle="tooltip" data-original-title="Doors"> 
                                         <strong>4</strong> <span class="mn-icon"></span>               
                                         </a>             
                                         </li>    
                                    </ul>          
                                   <div class="suplier_logo"> <img src="https://static.carhire-solutions.com/images/supplier/logo/logo11.png" alt=""></div>             
                                  </div>             


            <div class="clearfix"></div>            

            </div>           
            </div>          
            </div>         
            </div>        
            </div>        

            <div class="width20 mobile_f_i">         
	            <div class="mrinfrmtn">          
		            <div class="sidepricewrp">           
			            <div class="sideprice">           
			            <strong>INR </strong>             
			            <span class="f-p" data-price="39.06">39.06</span>            
			            <span class="price-order hide">39.06</span>           
			            </div>           
		                <span class="text-center non_ref" style="color:#0B9FD1; display: block;">Non Refundable</span>  

			            <div class="bookbtn">            
			            <input type="submit" value="Book" class="booknow">         

			           </div>          
		           </div> 
	               <a class="detailsflt" data-toggle="collapse" data-target="#car_rental0"> More <label>Details</label><span class="caret"></span> </a>        
	           </div>       
           </div>       
        </div>             

        <div class="clearfix"></div>            

        <div id="car_rental0" class="collapse" data-role="dialog">       
        <div class="carextent">        
        <div class="modal-content1">         
        <div class="clearfix"></div>         
        <div class="modal-body1">          
        <div class="col-xs-12 nopad">           
        <div class="middleCol">            
	        <ul class="features1">                                      
	             <li data-original-title="gear" class="transmission tooltipv"><span class="mn-icon"></span> <strong>Automatic</strong></li>  
	             <li data-original-title="Air Conditioning" class="ac tooltipv"><span class="mn-icon"></span> <strong>A/C</strong></li>  
	             <li class="fuel tooltipv" data-toggle="tooltip" title="" data-original-title="Registration Fee"><i class="fa fa-plus"></i><strong> Registration Fee</strong></li>            
	        </ul>                                             
         </div>           

         <div class="clearfix"></div>           

         <div class="rentcondition">            
         <div class="hotel_detailtab">             
         <div class="clearfix"></div>             
         <div class="tab-content"> 
              <div class="tab-pane active" id="htldets0">               
              <div class="innertabs">                
              <div class="secn_pot"> 
                 <div class="includ">                  
                 <div class="parasub">                   
                 <ul class="checklist">                                          
                 <li> <span class="fa fa-check"></span>Supplementary Liability Insurance</li>                                            
                 <li> <span class="fa fa-check"></span>Theft protection</li>                                            
                 <li> <span class="fa fa-check"></span>Airport Service Charge</li>                                            
                 <li> <span class="fa fa-check"></span>One way rental</li>                                            
                 <li> <span class="fa fa-check"></span>VAT</li>                                         
                 </ul>                  
                 </div>                 
                 </div>                  
                  <div class="linebrk"></div>          

                  <button type="button" class="sumtab" data-toggle="collapse" data-target="#agelmt">Age Limit</button>                  
                  <div class="collapse in age_lmt" id="agelmt">                   
                  <div class="parasub">                    
                  <ul class="checklist">                     
                     <li> <span>Minimum age: <strong>30</strong></span></li>                     
                     <li> <span>Maximum age: <strong>80</strong></span></li>                      
                  </ul>                   
                  </div>                  
                  </div>                  
                  <div class="linebrk"></div>                                   
                  <div class="clearfix"></div>                 
                  <p class="carhead">Offer Includes</p>                 

                  <div id="see_more0" class="collapse in">                   
                  <div class="carprc clearfix">                     
                  <button type="button" class="sumtab" data-toggle="collapse" id="carnect0" data-target="#agelmt00"><b>Registration Fee</b></button>
                       <div class="prcright" style="text-align: right;">                      
                       <div class="parasub">
                       <p class="car_price_new" style="color: #f58830">INR 1.37</p>                      
                       </div>                     
                       </div>                     
                                <div class="clearfix"></div>                     
                       <div id="agelmt00" class="collapse">                      
                       <p style="padding-left: 28px;">Pay on pick-up in local currency:per day: 4.00 INR</p>                     
                       </div>                    
                  </div>
                                       
                                       <div class="carprc clearfix">           
	                                       <button type="button" class="sumtab" data-toggle="collapse" id="carnect0" data-target="#agelmt01">
	                                       <b>Collision damage waiver</b>
	                                       </button>           

	                                       <div class="prcright" style="text-align: right;">            
	                                       <div class="parasub">
	                                       <p class="car_price_new" style="color: #f58830">INR 0.00</p>            
	                                       </div>           
	                                       </div>           
	                                       <div class="clearfix"></div>           
		                                       <div id="agelmt01" class="collapse">            
		                                          <p style="padding-left: 28px;">with excess up to 1,500 INR</p>           
		                                       </div>          
                                       </div>

                                       <div class="carprc clearfix">           
                                       <button type="button" class="sumtab" data-toggle="collapse" id="carnect0" data-target="#agelmt02">
                                            <b>Supplementary Liability Insurance
                                            </b>
                                       </button>           

                                       <div class="prcright" style="text-align: right;">            
                                       <div class="parasub">
                                       <p class="car_price_new" style="color: #f58830">INR 0.00</p>            
                                       </div>           
                                       </div>           
                                       <div class="clearfix"></div>           

                                       <div id="agelmt02" class="collapse">            

                                       <p style="padding-left: 28px;">Legally required, insurance for damages on the adversarial vehicle, persons and objects- In this offer it is included.</p>           
                                       </div>          
                                       </div>
                                       <div class="carprc clearfix">           
                                       <button type="button" class="sumtab" data-toggle="collapse" id="carnect0" data-target="#agelmt03">
                                           <b>Theft protection</b>
                                       </button>   

                                       <div class="prcright" style="text-align: right;">            
	                                       <div class="parasub"><p class="car_price_new" style="color: #f58830">INR 0.00</p></div>           
	                                     </div>           
                                       <div class="clearfix"></div>    

                                       <div id="agelmt03" class="collapse">            
                                          <p style="padding-left: 28px;">with excess up to 1,500 INR</p>           
                                       </div>          

                                       </div>

                                               </div>                                
                                              </div>               
                                             </div>              
                                            </div>              
                                           <div class="clearfix"></div>             
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

						     <div class="rowresult r-r-i" rel="sort0">          
                              <div class="madgrid">     
                              <div class="col-xs-12 nopad">      
                              <div class="sidenamedesc mobile_f_i">       
                              <div class="celhtl width20 midlbord">        
                              <div class="car_image"> 
                              <img src="https://static.carhire-solutions.com/images/car/Sixt/small/AE_EDAR.jpg" class="lazy lazy_loader h-img" onerror="this.onerror=null;this.src='/airliners_new/extras/system/template_list/template_v1/images/no-img.jpg';">
                              </div>       
                              </div>       

                              <div class="celhtl width60">        
                              <div class="waymensn">         
                              <div class="flitruo_hotel">          
                              <div class="hoteldist"> 
                                    <span class="supplier_name hide">sixt</span>           
                                    <span class="car_type hide">1</span>           
                                    <span class="car_name">Mazda 2 Aut.<span> or Similar</span></span>           
                                    <div class="clearfix"></div>           
                                    <span class="hotel_address elipsetool"></span>           
                                    <div class="clearfix"></div>           

                                    <div class="pick cr_wdt">            
	                                    <i class="fal fa-map-marker-alt"></i> <span>Pickup Location:</span>            
	                                    <h3>Dubai International Airport (DXB),United Arab Emirates</h3>           
                                    </div>           

                                    <div class="pick">    
                                       <span class="fuel_icon">Fuel:F2F</span>            
                                       <h3> <a href="#" data-toggle="tooltip" title="" data-original-title="Full to Full: Pick up and drop off with a full tank. If the car is not returned with a full tank, suppliers will charge fuel plus refueling charges. ">              
                                       <i class="fa fa-info-circle" aria-hidden="true"></i> Petrol</a>            
                                       </h3>           
                                   </div>           

                                   <div class="pick">            
                                  <i class="fal fa-tachometer-alt"></i> <span>Mileage Allowance:</span>            
                                   <h3>Unlimited</h3>           
                                   </div>  

                                       <div class="clearfix"></div>          

                                   <div class="middleCol">            
                                   <ul class="features">             
                                       <li class="person tooltipv">              
                                       <a title="" data-toggle="tooltip" data-original-title="Passengers"><strong>5</strong> 
                                       <span class="mn-icon"></span>               
                                       </a>             
                                       </li> 
                                        <li class="baggage tooltipv">              
                                        <a title="" data-toggle="tooltip" data-original-title="Bags"> <strong>3</strong> 
                                        <span class="mn-icon"></span>               
                                        </a>             
                                        </li> 

                                         <li class="doors tooltipv">              
                                         <a title="" data-toggle="tooltip" data-original-title="Doors"> 
                                         <strong>4</strong> <span class="mn-icon"></span>               
                                         </a>             
                                         </li>    
                                    </ul>          
                                   <div class="suplier_logo"> <img src="https://static.carhire-solutions.com/images/supplier/logo/logo11.png" alt=""></div>             
                                  </div>             


            <div class="clearfix"></div>            

            </div>           
            </div>          
            </div>         
            </div>        
            </div>        

            <div class="width20 mobile_f_i">         
	            <div class="mrinfrmtn">          
		            <div class="sidepricewrp">           
			            <div class="sideprice">           
			            <strong>INR </strong>             
			            <span class="f-p" data-price="39.06">39.06</span>            
			            <span class="price-order hide">39.06</span>           
			            </div>           
		                <span class="text-center non_ref" style="color:#0B9FD1; display: block;">Non Refundable</span>  

			            <div class="bookbtn">            
			            <input type="submit" value="Book" class="booknow">         

			           </div>          
		           </div> 
	               <a class="detailsflt" data-toggle="collapse" data-target="#car_rental0"> More <label>Details</label><span class="caret"></span> </a>        
	           </div>       
           </div>       
        </div>             

        <div class="clearfix"></div>            

        <div id="car_rental0" class="collapse" data-role="dialog">       
        <div class="carextent">        
        <div class="modal-content1">         
        <div class="clearfix"></div>         
        <div class="modal-body1">          
        <div class="col-xs-12 nopad">           
        <div class="middleCol">            
	        <ul class="features1">                                      
	             <li data-original-title="gear" class="transmission tooltipv"><span class="mn-icon"></span> <strong>Automatic</strong></li>  
	             <li data-original-title="Air Conditioning" class="ac tooltipv"><span class="mn-icon"></span> <strong>A/C</strong></li>  
	             <li class="fuel tooltipv" data-toggle="tooltip" title="" data-original-title="Registration Fee"><i class="fa fa-plus"></i><strong> Registration Fee</strong></li>            
	        </ul>                                             
         </div>           

         <div class="clearfix"></div>           

         <div class="rentcondition">            
         <div class="hotel_detailtab">             
         <div class="clearfix"></div>             
         <div class="tab-content"> 
              <div class="tab-pane active" id="htldets0">               
              <div class="innertabs">                
              <div class="secn_pot"> 
                 <div class="includ">                  
                 <div class="parasub">                   
                 <ul class="checklist">                                          
                 <li> <span class="fa fa-check"></span>Supplementary Liability Insurance</li>                                            
                 <li> <span class="fa fa-check"></span>Theft protection</li>                                            
                 <li> <span class="fa fa-check"></span>Airport Service Charge</li>                                            
                 <li> <span class="fa fa-check"></span>One way rental</li>                                            
                 <li> <span class="fa fa-check"></span>VAT</li>                                         
                 </ul>                  
                 </div>                 
                 </div>                  
                  <div class="linebrk"></div>          

                  <button type="button" class="sumtab" data-toggle="collapse" data-target="#agelmt">Age Limit</button>                  
                  <div class="collapse in age_lmt" id="agelmt">                   
                  <div class="parasub">                    
                  <ul class="checklist">                     
                     <li> <span>Minimum age: <strong>30</strong></span></li>                     
                     <li> <span>Maximum age: <strong>80</strong></span></li>                      
                  </ul>                   
                  </div>                  
                  </div>                  
                  <div class="linebrk"></div>                                   
                  <div class="clearfix"></div>                 
                  <p class="carhead">Offer Includes</p>                 

                  <div id="see_more0" class="collapse in">                   
                  <div class="carprc clearfix">                     
                  <button type="button" class="sumtab" data-toggle="collapse" id="carnect0" data-target="#agelmt00"><b>Registration Fee</b></button>
                       <div class="prcright" style="text-align: right;">                      
                       <div class="parasub">
                       <p class="car_price_new" style="color: #f58830">INR 1.37</p>                      
                       </div>                     
                       </div>                     
                                <div class="clearfix"></div>                     
                       <div id="agelmt00" class="collapse">                      
                       <p style="padding-left: 28px;">Pay on pick-up in local currency:per day: 4.00 INR</p>                     
                       </div>                    
                  </div>
                                       
                                       <div class="carprc clearfix">           
	                                       <button type="button" class="sumtab" data-toggle="collapse" id="carnect0" data-target="#agelmt01">
	                                       <b>Collision damage waiver</b>
	                                       </button>           

	                                       <div class="prcright" style="text-align: right;">            
	                                       <div class="parasub">
	                                       <p class="car_price_new" style="color: #f58830">INR 0.00</p>            
	                                       </div>           
	                                       </div>           
	                                       <div class="clearfix"></div>           
		                                       <div id="agelmt01" class="collapse">            
		                                          <p style="padding-left: 28px;">with excess up to 1,500 INR</p>           
		                                       </div>          
                                       </div>

                                       <div class="carprc clearfix">           
                                       <button type="button" class="sumtab" data-toggle="collapse" id="carnect0" data-target="#agelmt02">
                                            <b>Supplementary Liability Insurance
                                            </b>
                                       </button>           

                                       <div class="prcright" style="text-align: right;">            
                                       <div class="parasub">
                                       <p class="car_price_new" style="color: #f58830">INR 0.00</p>            
                                       </div>           
                                       </div>           
                                       <div class="clearfix"></div>           

                                       <div id="agelmt02" class="collapse">            

                                       <p style="padding-left: 28px;">Legally required, insurance for damages on the adversarial vehicle, persons and objects- In this offer it is included.</p>           
                                       </div>          
                                       </div>
                                       <div class="carprc clearfix">           
                                       <button type="button" class="sumtab" data-toggle="collapse" id="carnect0" data-target="#agelmt03">
                                           <b>Theft protection</b>
                                       </button>   

                                       <div class="prcright" style="text-align: right;">            
	                                       <div class="parasub"><p class="car_price_new" style="color: #f58830">INR 0.00</p></div>           
	                                     </div>           
                                       <div class="clearfix"></div>    

                                       <div id="agelmt03" class="collapse">            
                                          <p style="padding-left: 28px;">with excess up to 1,500 INR</p>           
                                       </div>          

                                       </div>

                                               </div>                                
                                              </div>               
                                             </div>              
                                            </div>              
                                           <div class="clearfix"></div>             
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

						     <div class="rowresult r-r-i" rel="sort0">          
                              <div class="madgrid">     
                              <div class="col-xs-12 nopad">      
                              <div class="sidenamedesc mobile_f_i">       
                              <div class="celhtl width20 midlbord">        
                              <div class="car_image"> 
                              <img src="https://static.carhire-solutions.com/images/car/Sixt/small/AE_EDAR.jpg" class="lazy lazy_loader h-img" onerror="this.onerror=null;this.src='/airliners_new/extras/system/template_list/template_v1/images/no-img.jpg';">
                              </div>       
                              </div>       

                              <div class="celhtl width60">        
                              <div class="waymensn">         
                              <div class="flitruo_hotel">          
                              <div class="hoteldist"> 
                                    <span class="supplier_name hide">sixt</span>           
                                    <span class="car_type hide">1</span>           
                                    <span class="car_name">Mazda 2 Aut.<span> or Similar</span></span>           
                                    <div class="clearfix"></div>           
                                    <span class="hotel_address elipsetool"></span>           
                                    <div class="clearfix"></div>           

                                    <div class="pick cr_wdt">            
	                                    <i class="fal fa-map-marker-alt"></i> <span>Pickup Location:</span>            
	                                    <h3>Dubai International Airport (DXB),United Arab Emirates</h3>           
                                    </div>           

                                    <div class="pick">    
                                       <span class="fuel_icon">Fuel:F2F</span>            
                                       <h3> <a href="#" data-toggle="tooltip" title="" data-original-title="Full to Full: Pick up and drop off with a full tank. If the car is not returned with a full tank, suppliers will charge fuel plus refueling charges. ">              
                                       <i class="fa fa-info-circle" aria-hidden="true"></i> Petrol</a>            
                                       </h3>           
                                   </div>           

                                   <div class="pick">            
                                  <i class="fal fa-tachometer-alt"></i> <span>Mileage Allowance:</span>            
                                   <h3>Unlimited</h3>           
                                   </div>  

                                       <div class="clearfix"></div>          

                                   <div class="middleCol">            
                                   <ul class="features">             
                                       <li class="person tooltipv">              
                                       <a title="" data-toggle="tooltip" data-original-title="Passengers"><strong>5</strong> 
                                       <span class="mn-icon"></span>               
                                       </a>             
                                       </li> 
                                        <li class="baggage tooltipv">              
                                        <a title="" data-toggle="tooltip" data-original-title="Bags"> <strong>3</strong> 
                                        <span class="mn-icon"></span>               
                                        </a>             
                                        </li> 

                                         <li class="doors tooltipv">              
                                         <a title="" data-toggle="tooltip" data-original-title="Doors"> 
                                         <strong>4</strong> <span class="mn-icon"></span>               
                                         </a>             
                                         </li>    
                                    </ul>          
                                   <div class="suplier_logo"> <img src="https://static.carhire-solutions.com/images/supplier/logo/logo11.png" alt=""></div>             
                                  </div>             


            <div class="clearfix"></div>            

            </div>           
            </div>          
            </div>         
            </div>        
            </div>        

            <div class="width20 mobile_f_i">         
	            <div class="mrinfrmtn">          
		            <div class="sidepricewrp">           
			            <div class="sideprice">           
			            <strong>INR </strong>             
			            <span class="f-p" data-price="39.06">39.06</span>            
			            <span class="price-order hide">39.06</span>           
			            </div>           
		                <span class="text-center non_ref" style="color:#0B9FD1; display: block;">Non Refundable</span>  

			            <div class="bookbtn">            
			            <input type="submit" value="Book" class="booknow">         

			           </div>          
		           </div> 
	               <a class="detailsflt" data-toggle="collapse" data-target="#car_rental0"> More <label>Details</label><span class="caret"></span> </a>        
	           </div>       
           </div>       
        </div>             

        <div class="clearfix"></div>            

        <div id="car_rental0" class="collapse" data-role="dialog">       
        <div class="carextent">        
        <div class="modal-content1">         
        <div class="clearfix"></div>         
        <div class="modal-body1">          
        <div class="col-xs-12 nopad">           
        <div class="middleCol">            
	        <ul class="features1">                                      
	             <li data-original-title="gear" class="transmission tooltipv"><span class="mn-icon"></span> <strong>Automatic</strong></li>  
	             <li data-original-title="Air Conditioning" class="ac tooltipv"><span class="mn-icon"></span> <strong>A/C</strong></li>  
	             <li class="fuel tooltipv" data-toggle="tooltip" title="" data-original-title="Registration Fee"><i class="fa fa-plus"></i><strong> Registration Fee</strong></li>            
	        </ul>                                             
         </div>           

         <div class="clearfix"></div>           

         <div class="rentcondition">            
         <div class="hotel_detailtab">             
         <div class="clearfix"></div>             
         <div class="tab-content"> 
              <div class="tab-pane active" id="htldets0">               
              <div class="innertabs">                
              <div class="secn_pot"> 
                 <div class="includ">                  
                 <div class="parasub">                   
                 <ul class="checklist">                                          
                 <li> <span class="fa fa-check"></span>Supplementary Liability Insurance</li>                                            
                 <li> <span class="fa fa-check"></span>Theft protection</li>                                            
                 <li> <span class="fa fa-check"></span>Airport Service Charge</li>                                            
                 <li> <span class="fa fa-check"></span>One way rental</li>                                            
                 <li> <span class="fa fa-check"></span>VAT</li>                                         
                 </ul>                  
                 </div>                 
                 </div>                  
                  <div class="linebrk"></div>          

                  <button type="button" class="sumtab" data-toggle="collapse" data-target="#agelmt">Age Limit</button>                  
                  <div class="collapse in age_lmt" id="agelmt">                   
                  <div class="parasub">                    
                  <ul class="checklist">                     
                     <li> <span>Minimum age: <strong>30</strong></span></li>                     
                     <li> <span>Maximum age: <strong>80</strong></span></li>                      
                  </ul>                   
                  </div>                  
                  </div>                  
                  <div class="linebrk"></div>                                   
                  <div class="clearfix"></div>                 
                  <p class="carhead">Offer Includes</p>                 

                  <div id="see_more0" class="collapse in">                   
                  <div class="carprc clearfix">                     
                  <button type="button" class="sumtab" data-toggle="collapse" id="carnect0" data-target="#agelmt00"><b>Registration Fee</b></button>
                       <div class="prcright" style="text-align: right;">                      
                       <div class="parasub">
                       <p class="car_price_new" style="color: #f58830">INR 1.37</p>                      
                       </div>                     
                       </div>                     
                                <div class="clearfix"></div>                     
                       <div id="agelmt00" class="collapse">                      
                       <p style="padding-left: 28px;">Pay on pick-up in local currency:per day: 4.00 INR</p>                     
                       </div>                    
                  </div>
                                       
                                       <div class="carprc clearfix">           
	                                       <button type="button" class="sumtab" data-toggle="collapse" id="carnect0" data-target="#agelmt01">
	                                       <b>Collision damage waiver</b>
	                                       </button>           

	                                       <div class="prcright" style="text-align: right;">            
	                                       <div class="parasub">
	                                       <p class="car_price_new" style="color: #f58830">INR 0.00</p>            
	                                       </div>           
	                                       </div>           
	                                       <div class="clearfix"></div>           
		                                       <div id="agelmt01" class="collapse">            
		                                          <p style="padding-left: 28px;">with excess up to 1,500 INR</p>           
		                                       </div>          
                                       </div>

                                       <div class="carprc clearfix">           
                                       <button type="button" class="sumtab" data-toggle="collapse" id="carnect0" data-target="#agelmt02">
                                            <b>Supplementary Liability Insurance
                                            </b>
                                       </button>           

                                       <div class="prcright" style="text-align: right;">            
                                       <div class="parasub">
                                       <p class="car_price_new" style="color: #f58830">INR 0.00</p>            
                                       </div>           
                                       </div>           
                                       <div class="clearfix"></div>           

                                       <div id="agelmt02" class="collapse">            

                                       <p style="padding-left: 28px;">Legally required, insurance for damages on the adversarial vehicle, persons and objects- In this offer it is included.</p>           
                                       </div>          
                                       </div>
                                       <div class="carprc clearfix">           
                                       <button type="button" class="sumtab" data-toggle="collapse" id="carnect0" data-target="#agelmt03">
                                           <b>Theft protection</b>
                                       </button>   

                                       <div class="prcright" style="text-align: right;">            
	                                       <div class="parasub"><p class="car_price_new" style="color: #f58830">INR 0.00</p></div>           
	                                     </div>           
                                       <div class="clearfix"></div>    

                                       <div id="agelmt03" class="collapse">            
                                          <p style="padding-left: 28px;">with excess up to 1,500 INR</p>           
                                       </div>          

                                       </div>

                                               </div>                                
                                              </div>               
                                             </div>              
                                            </div>              
                                           <div class="clearfix"></div>             
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
      
						<div id="empty_car_search_result"  style="display:none">
							<div class="noresultfnd text-center">
								<!-- <div class="imagenofnd"><img src="<?=$template_images?>empty.jpg" alt="Empty" /></div> -->
								
								<div class="lablfnd">No result found for this Search criteria.</div>
							</div>
						</div>
					</div>
					<!-- End of result -->
				  <!--Map view indipendent hotel-->
		          </div>
		        </div>
		      </div>
		    </div>
		  </div>
		</div>
	</div>
	 
	<div id="empty-search-result" class="jumbotron container" style="display:none">
		<h1><i class="fa fa-taxi"></i> Oops!</h1>
		<p>No cars were found in this location today.</p>
		<p>
			Search results change daily based on availability.If you have an urgent requirement, please get in touch with our call center using the contact details mentioned on the home page. They will assist you to the best of their ability.
		</p>
	</div>
	<?php echo $GLOBALS['CI']->template->isolated_view('share/flight_session_expiry_popup');?>

<script>
/*	$('.filter_show').click(function(){
		$('.filtrsrch').addClass('open');
	});
	
	$('.close_filter').click(function(){
		$('.filtrsrch').removeClass('open');
	});*/
	
	/*  Mobile Filter  */
	$('.filter_show').click(function() {
		$('.filtrsrch').stop( true, true ).toggleClass('open');
		$('.col30').addClass('round_filt');
		// $('.col30').stop( true, true ).slideToggle(500);
		$(".col30.round_filt").show();
	});
	$(".close_fil_box").click(function(){
			$(".col30.round_filt").hide();
	});	
	
	</script>
	<!-- <script type="text/javascript" src="js/owl.carousel.min.js"></script> -->
<script>
  $(document).ready(function(){
    $("#owl-demo2").owlCarousel({
        items : 6, 
        itemsDesktop : [1000,6],
        itemsDesktopSmall : [900,4], 
        itemsTablet: [600,2], 
        itemsMobile : [479,1], 
        navigation : true,
        navigationText: [],
        pagination : false,
        autoPlay : 5000
    });


  });
</script>


 <script type="text/javascript">
   $(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
   });
  </script>