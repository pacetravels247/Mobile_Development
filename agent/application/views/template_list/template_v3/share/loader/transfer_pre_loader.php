<div class="fulloading result-pre-loader-wrapper forhoteload">
  <div class="loadmask"></div>
  <div class="centerload cityload">
  <div class="load_links" style="position: absolute;top: 0;right: 0;z-index: 9999;font-size:14px;font-weight:300">
      <a href=""><i class="fa fa-refresh"></i></a>
      <a href="<?php echo base_url(); ?>"><i class="fa fa-close"></i></a>     
    </div>
    <div class="loadcity"></div>
    <div class="clodnsun"></div>
    <div class="reltivefligtgo">
      <div class="flitfly"></div>
    </div>
    <div class="relativetop sghtseen">
      <div class="paraload">
        Searching for the best transfers
      </div>
      <div class="clearfix"></div>
      <div class="placenametohtl"><?php echo ucfirst($sight_seen_search_params['destination']); ?></div>
      <div class="clearfix"></div>
      <div class="sckintload">
<!--         <div class="ffty">
  <div class="borddo brdrit">
    <span class="lblbk">Check In</span>
  </div>
</div>
<div class="ffty">
  <div class="borddo">
    <span class="lblbk">Check Out</span>
  </div>
</div> -->
        <div class="clearfix"></div>
        <div class="tabledates">
          <div class="tablecelfty">
            <div class="borddo brdrit">
             <?php if($sight_seen_search_params['from_date']):?>
                <div class="fuldate">
                
                    <span class="bigdate"><?php echo date("d",strtotime($sight_seen_search_params['from_date']));?></span>
                    <div class="biginre">
                        <?php echo date("M",strtotime($sight_seen_search_params['from_date']));?><br />
          <?php echo date("Y",strtotime($sight_seen_search_params['from_date']));?>
                    </div>
                </div>
            <?php endif;?>
            </div>
          </div>
           <div class="tablecelfty">
              <div class="borddo">
              <?php if($sight_seen_search_params['to_date']):?>
                  <div class="fuldate">
                      <span class="bigdate"><?php echo date("d",strtotime($sight_seen_search_params['to_date']));?></span>
                      <div class="biginre">
                           <?php echo date("M",strtotime($sight_seen_search_params['to_date']));?><br />
             <?php echo date("Y",strtotime($sight_seen_search_params['to_date']));?>
                      </div>
                  </div>
              <?php endif;?>
              </div>
          </div>
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
    <div class="busrunning">
      <div class="runbus"></div>
      <div class="runbus2"></div>
      <div class="roadd"></div>
    </div>
  </div>
</div>