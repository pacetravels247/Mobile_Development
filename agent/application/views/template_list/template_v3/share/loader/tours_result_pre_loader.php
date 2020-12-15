<?php 
	/* $a = json_encode($trip_details);
	$b = json_decode($a);
	/debug($b); */
//debug($trip_details);
?>

<div class="fulloading result-pre-loader-wrapper forhoteload">
	<div class="loadmask"></div>
    <div class="centerload cityload load_holyday">
    
    	<div class="clodnsun"></div>
        
        <div class="reltivefligtgo">
        	<div class="flitfly"></div>
        </div>
        
        <div class="relativetop">
            <div class="paraload">
                Searching for the best holidays
            </div>
            <div class="clearfix"></div>
            <div class="placenametohtl"><?php echo ucfirst($result['location']); ?></div>
            <div class="clearfix"></div>
            <div class="sckintload">
                <div class="ffty">
                    <div class="borddo brdrit">
                    <span class="lblbk">Duration</span>
                    </div>
                </div>
                
                <div class="ffty">
                    <div class="borddo">
                    <span class="lblbk">Budjet</span>
                    </div>
                </div>
                
                <div class="clearfix"></div>
                
                <div class="tabledates">
                <div class="tablecelfty">
                    <div class="borddo brdrit">
                    <div class="fuldate">
                    
                        <div class="biginre">
                            <span class="tour_load_cntnt">4 - 7</span>
                            Days
                        </div>
                    </div>
                    </div>
                </div>

                <div class="tablecelfty">
                    <div class="borddo">

                    <div class="fuldate">
                        <div class="biginre">
                             <span class="tour_load_cntnt">5000 - 15000</span>
                             INR
                        </div>
                    </div>

                    </div>
                </div>
                </div>
                
                <div class="clearfix"></div>
                
                
                <div class="nigthcunt">Season packages</div>
            </div>
        </div>
        
        <div class="clearfix"></div>
        
        
        <div class="holyday_animates">
        <div class="holiday_tree"></div>
        
        <div class="boat">
            <ul class="no-bullet">
                <ul class="no-bullet fume">
                    <li class="fume4"></li>
                    <li class="fume3"></li>
                    <li class="fume2"></li>
                    <li class="fume1"></li>
                </ul>
                <li class="smokestack"></li>
                <li class="white-body">
                    <ul class="windows inline-list">
                        <li class="circle"></li>
                        <li class="circle"></li>
                        <li class="circle"></li>
                    </ul>
                </li>
                <li class="boat-body"></li>
            </ul>
        </div>
        <div class="sea">
            <span class="wave1"></span>
            <span class="wave2"></span>
            <span class="wave3"></span>
            <span class="wave4"></span>
        </div>
        
        
        <div class="animowrap">
            <div class="animo">
                <div class="wave"></div>
            </div>
        </div>
        
        </div>
        
    </div>
</div>
<script>

for(i=0; i<100; i++) {
  $('.animo').append('<div class="wave"></div>');
}


$('.wave').each( function() {
  $(this).css('left', (Math.random() * 100) - 5 + '%' );
  $(this).css('animation-duration', (Math.random()) + 1 + 's' );
});
</script>