<!-- HTML BEGIN -->
<div class="bodyContent">
    <div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
        <div class="panel-heading"><!-- PANEL HEAD START -->
            <div class="panel-title align-center" style="margin-top: 20px;"></div>
        </div>
        <!-- PANEL HEAD START -->

        <div class="container">
            <div class="col-md-offset-1 col-md-10">
                <h4 class="head-div1"><i class="fa fa-ban"></i> &nbsp; Cancel Ticket</h4>
                <div class="row">
                    <form class="" action="<?php echo base_url()?>management/cancel_ticket" method="POST">
                    <div class="col-sm-offset-3 col-sm-6 ">
                        <div class="row mt-1 radio-main-div">
                            <?php if(isset($err) && !empty($err)){ ?>
                                <p class="text-red1"><?php echo $err; ?></p>
                            <?php } ?>
                            <div class="col-sm-12 padfive mt-1">
                                <label for="bus">
                                <input class="" type="radio" id="bus" name="module" value="bus" checked>
                                Bus</label>

                                <label for="Flight">
                                <input class="" type="radio" id="Flight" name="module" value="Flight">
                                Flight</label>

                                <label for="Hotel">
                                <input class="" type="radio" id="Hotel" name="module" value="Hotel">
                                Hotel</label>

                                <label for="Package">
                                <input class="" type="radio" id="Package" name="module" value="Package">
                                Package</label>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-12 padfive">
                                <input type="text" name="app-ref" class="form-control input-text" placeholder="Pace Reference Number">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6 padfive">
                                <button class="btn btn-danger form-control">Submit</button>
                            </div>
                            <div class="col-sm-6 padfive">
                                <button class="btn btn-default form-control">Reset</button>
                            </div>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>


        <div class="panel-body"><!-- PANEL BODY START -->
            
            </div>
        </div><!-- PANEL BODY END -->
    </div><!-- PANEL END -->
</div>
<style type="text/css">
    .mt-1{
        margin-top: 10px;
    }
    .mt-2{
        margin-top: 20px;
    }
    .head-div1{
        background: linear-gradient(96deg,#002042,#0a8ec1);
        padding: 10px;
        color: #fff;
        text-align: center;
    }
    .input-text{
        border: none;
        border-bottom: 1px solid #ccc;
    }
    .radio-main-div label{
        margin-right: 20px;
        cursor: pointer;
        font-size: 15px;
    }
    .radio-main-div{
        text-align: center;
    }
    .radio-main-div .text-red1{
        color: #e11515;
        background-color: #ffcbcb;
        padding: 5px 10px;
        border-radius: 5px;
        text-align: left;
    }
</style>