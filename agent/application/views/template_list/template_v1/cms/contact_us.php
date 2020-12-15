<style type="text/css">
	.col-md-12.col-xs-12.map_sec {
    padding: 30px 0px;
}
.con_form label {
    color: #222;
    font-size: 16px;
    font-weight: 500;
}
.con_form button {
    background: #a100ff;
    color: #fff;
    font-size: 14px;
    border: 1px solid #a100ff;
    height: 40px;
    padding: 0px 25px;
    font-weight: 500;
    margin-left: 16px;
}
.form-control:focus {
    border-color: #9d00fa !important;
    box-shadow: 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(161, 0, 255, 0.48);
}
.con_form {
    background: #ffffff;
    padding: 20px;
    box-shadow: 0px 0px 5px #ccc;
    border-radius: 6px;
}
.lblfont12px h4 {
    font-size: 20px;
    color: #a100ff;
}
.lblfont12px span i {
    color: #a100ff;
    margin-right: 20px;
    font-size: 16px;
}
.lblfont12px address {
    margin-bottom: 20px;
    font-style: normal;
    line-height: 1.42857143;
    margin-top: -20px;
    margin-left: 25px;
    font-size: 16px;
}
.lblfont12px p {
    margin: -20px 25px 20px;
    font-size: 16px;
}
.con-box {
    margin-bottom: 40px;
    border-bottom: 1px solid #ccc;
}
.row_container {
    background: #ffffff;
}
.form-container {
    margin: 0px -25px;
}
</style>
<div class="row_container">
<div class="container">
    <div class="form-container">
<div class="col-md-12 col-xs-12">
<div class="lblbluebold16px"><h1><?php echo $data[0]['page_title'];?></h1></div>
<div class="lblfont12px col-md-6">
	<div class="con_form">
		<form action="" method="post">
			<div class="col-sm-6 form-group">
				<label>Name</label>
				<input type="text" name="con-name" class="form-control" placeholder="Enter Name">
			</div>
			<div class="col-sm-6 form-group">
				<label>Email</label>
				<input type="text" name="con-email" class="form-control"  placeholder="Enter Email">
			</div>
			<div class="col-sm-12 form-group">
				<label>Phone</label>
				<input type="text" name="con-phone" class="form-control"  placeholder="Enter Phone Number">
			</div>
			<div class="col-sm-12 form-group">
				<label>Message</label>
				<textarea rows="4" class="form-control" name="con-message" placeholder="Message Here">
				</textarea>
			</div>
			<button type="submit">Send</button>
		</form>
	</div>
</div>
<div class="lblfont12px col-md-6">
	<h4>Address</h4>
<div class="con-box"><span><i class="fa fa-map-marker" aria-hidden="true"></i></span><address>Sabnis Complex Shop No 1, College Main Road, College Rd, Belgaum, Karnataka 590001</address>
<div class="con-box"><span><i class="fa fa-envelope" aria-hidden="true"></i>  </span><p>support@pacetravels.in</p></div>
<div class="con-box"><span><i class="fa fa-phone" aria-hidden="true">  </i></span><p> +(91) 7022758383 <br>7349181113 <br> 7795052283</p>
</div>
</div>
</div>
</div>
<div class="col-md-12 col-xs-12 map_sec">
	<iframe src="https://maps.google.com/maps?q=Sabnis%20Complex%20Shop%20No%201%2C%20College%20Main%20Road%2C%20College%20Rd%2C%20Belgaum%2C%20Karnataka%20590001&t=&z=13&ie=UTF8&iwloc=&output=embed" width="100%" height="450" frameborder="0" style="border:0;" allowfullscreen="">
</iframe>
</div>
</div>
</div>
