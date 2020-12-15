
function show_question_info(questions)
{
	if(questions=='')
{
questions=0;
}
if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
xmlhttp.onreadystatechange=function()
{
if (xmlhttp.readyState==4 && xmlhttp.status==200)
{
document.getElementById("question_info").innerHTML=xmlhttp.responseText;
}
}
xmlhttp.open("GET",api_url+"supplier/itinerary_loop1/"+questions,true);
xmlhttp.send();
}



function show_withprice(pricee){
	//alert(pricee);
	if (pricee=='1') {
		$('#withprice').show();
	}else{ $('#withprice').hide(); }
}
function show_deal(deal){
	//alert(deal);
	if (deal=='1') {
		$('#withdeal').show();
		$('#price_with').hide();
	}else{ $('#price_with').show(); 
			$('#withdeal').show();
			$('#withdeal').hide();}
}
/*function hide_price(deal){
	if(deal=='deal'){
		$('#pricee').hide();
	}
}*/
	function show_ship_duration_info(duration)
{
if(duration=='')
{
duration=0;
}
if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
xmlhttp.onreadystatechange=function()
{
if (xmlhttp.readyState==4 && xmlhttp.status==200)
{
document.getElementById("duration_info").innerHTML=xmlhttp.responseText;
CKEDITOR.inline;
}
}
xmlhttp.open("GET",api_url+"cruise/itinerary_loop/"+duration,true);
xmlhttp.send();
}


function show_duration_info1(duration1)
{
if(duration1=='')
{
duration1=0;
}
if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
xmlhttp.onreadystatechange=function()
{
if (xmlhttp.readyState==4 && xmlhttp.status==200)
{
document.getElementById("duration_info1").innerHTML=xmlhttp.responseText;
CKEDITOR.inline;
}
}
xmlhttp.open("GET",api_url+"cruise/ditinerary_loop/"+duration1,true);
xmlhttp.send();
}
/*function city_crs(country) {
	//alert('dhjd');
	  $.ajax({
            url: api_url+'supplier/get_crs_city/' + country,
            dataType: 'json',
            success: function(json) {
                $('select[name=\'cityname\']').html(json.result);
            }
        });
}*/

function tour(tourss) {
    //alert('dhjd');
      $.ajax({
            url: 'get_tour/' + tourss,
            dataType: 'json',
            success: function(json) {
                $('select[name=\'tourss\']').html(json.result);
            }
        });
}
/*function show_multicity(tourss) {
    //alert('dhjd');
      if (value=="multicity-") {};
}*/
function cities_crs(country) {
      $.ajax({
            url: api_url+'home_settings/get_crs_cities/' + country,
            dataType: 'json',
            success: function(json) {
                $('select[name=\'cityname\']').html(json.result);
            }
        });
}

 function shipname_check (ship_name) {
 	var sc = $('#ship_company').val();
        var data ="ship_name="+ship_name+"&sc="+sc;
 //	alert(sc);
 	 $.ajax({
	    method:"post",
            url: api_url+'cruise/get_ship_name/',
	    data:data,
            dataType: 'json',
            success: function(json) {
            	if(json.status=='0'){
                $('#shiperr').html(json.msg);
              //  $('#shiperr').fadeIn().fadeOut(6000);
                $("#ship_name").val('');

            }else {  $('#shiperr').html(); }
            }
        });

 	 }
 	  function shipcompanyname_check(ship_companyname) {
 	var sp = $('#sel_loc').val();
        var data ="ship_c_name="+ship_companyname+"&splc="+sp;
 	
 	 $.ajax({
	    method:"post",
            url: api_url+'cruise/get_ship_company_name/',
	    data:data,
            dataType: 'json',
            success: function(json) {
            //	alert(json);
            	if(json.status=='0'){
                $('#shiperr').html(json.msg);
              //  $('#shiperr').fadeIn().fadeOut(6000);
                $("#ship_companyname").val('');

            }else {  $('#shiperr').html(); }
            }
        });

 	 }
function delete_subscr(that) {  
    var m = $(that).data('ml');
    $.ajax({
        data : {mail:m},
        url: api_url+'newsletter/delete_subscr',
        method : 'post',
        success : function (data) {
            location.reload();
        }
    });
}

/*function show_city(cityname1){
    //alert(pricee);
    if (pricee=='1') {
        $('#withprice').show();
    }else{ $('#withprice').hide(); }
}
*/