function show_result_pre_loader() {
    var t = setInterval(function() {
        var e = $("#bar"),
            r = parseInt($(".result-pre-loader-container").width()),
            a = bar_width = parseInt(e.width()),
            o = parseInt(r / 10);
        bar_width >= r ? (e.text("Please Wait ... 100%"), clearInterval(t)) : (a = bar_width + o, e.width(a), e.text(parseInt(a / 10) + "%"))
    }, 1e3)
}

function hide_result_pre_loader() {
	  
	 $(".result-pre-loader-wrapper").hide();
	                               
	                                
	                                }
$(document).on("focus", "input:not([readonly],[type=submit],[type=button],[type=reset],button)", function() { $(this).select() }), $.widget("custom.catcomplete", $.ui.autocomplete, {
    _create: function() { this._super(), this.widget().menu("option", "items", "> :not(.ui-autocomplete-category)") },
    _renderMenu: function(t, e) {
        var r = this,
            a = "";
        $.each(e, function(e, o) {
            var n;
            o.category != a && (t.append("<li class='ui-autocomplete-category'>" + o.category + "</li>"), a = o.category), n = r._renderItemData(t, o), o.category && n.attr("aria-label", o.category + " : " + o.label)
        })
    },
    _renderItem:function(t, e){
      return $("<li class='custom-auto-complete'>").append('<a><img class="flag_image" src="' + e.country_code + '">' + e.label + '</a>').appendTo(t)

    }
});

//-----------OFFLINE PAYMENT ---------

$(document).on('click', '.btn-offline-pay', function(){
	var serial = $('#offline_form').serializeArray();
	$.post(app_base_url+'index.php/general/offline_payment',{data:serial}, function(data){
	
        // $('.offline-msg').html('Process Successfull , We have sent mail to confirm offline payment');
        window.location.href = app_base_url+'index.php/general/offline_approve/'+data;
	},'json');
})
function check_newsletter() {
  $('.cus_err_msg').hide();
  $('.msgNewsLetterSubsc12').fadeOut();

              var action = app_base_url+'index.php/general/email_subscription';
              var subscEmail = $('#exampleInputEmail1').val();
              //alert(subscEmail);
              if(subscEmail == ""){
                $('.msgNewsLetterSubsc12').fadeIn().fadeOut(10000);
                return false;
              }
              var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
              if( !emailReg.test( subscEmail ) ) {
                $('.msgNewsLetterSubsc12').fadeIn().fadeOut(10000);
                return false;
                }
              //alert(action);
              //alert(action);
              $.ajax({
                  url: action,
                  data: {'subEmail': subscEmail},
                  method: "POST",
                  dataType: "json",
                  success: function(r) {
                      //alert(r);

                      if(r.status == 1) {
                          $('.succNewsLetterSubsc').fadeIn().fadeOut(10000);
                          $('#exampleInputEmail1').val('');
                      }else if(r.status == 0) {
                          $('.msgNewsLetterSubsc').fadeIn().fadeOut(10000);
                      }else if(r.status == 2) {
                          $('.msgNewsLetterSubsc1').fadeIn().fadeOut(10000);
                      }
                  } 
              })

              return false; 
          }
