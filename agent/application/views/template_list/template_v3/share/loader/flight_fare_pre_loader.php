<?php
	//debug($trip_details);
	$trip_type_loader = '';
	$trip_details['from_timestamp'] = strtotime($trip_details['depature']);

	if ($trip_details['trip_type'] == 'round') {
		$trip_type_loader = 'round-loading';//Needed for loader only
		$trip_details['to_timestamp'] = strtotime($trip_details['arrival']);
	}
	
?>
<div class="fulloading result-pre-loader-wrapper">
	<div class="loadmask"></div>
    <div class="centerload cityload">
    
    	<div class="loadcity"></div>
    
    	<div class="clodnsun"></div>
        
        <div class="reltivefligtgo">
        	<div class="flitfly"></div>
        </div>
        
        <div class="relativetop">
            <div class="paraload">
                Loading best airfares
            </div>
            <div class="clearfix"></div>
            <div class="sckintload <?=$trip_type_loader?>">
            
                <div class="ffty">
                    <div class="borddo brdrit">
                    <span class="lblbk"><?=$trip_details['from']?></span>
                    </div>
                </div>

                <div class="ffty">
                    <div class="borddo">
                    	<span class="lblbk"><?=$trip_details['to']?></span>
                    </div>
                </div>
                
                <div class="clearfix"></div>
                
                <div class="tabledates">
                <div class="tablecelfty">
                    <div class="borddo brdrit">
                    <div class="fuldate">
                        <span class="bigdate"><?=date('M', ($trip_details['from_timestamp']))?></span>
                        <div class="biginre">
							<?=date('Y', ($trip_details['from_timestamp']))?>
                        </div>
                    </div>
                    </div>
                </div>
               </div>
                
                <div class="clearfix"></div>
                
                
                <div class="nigthcunt"><?=$trip_details['trip_type']?> Trip</div>
            </div>
        </div>
    </div>
</div>
