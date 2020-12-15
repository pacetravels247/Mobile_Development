$(document).ready(function() {
    function e() {
        gapi.auth2.getAuthInstance().signOut().then(function() {}), "undefined" != typeof FB && FB.logout(function(e) {}), $.get(app_base_url + "index.php/auth/ajax_logout", function(e) {
            location.reload()
        })
    }
    $(".open_register").click(function() {
        $(".for_sign_in").fadeOut(500, function() {
            $(".for_sign_up").fadeIn(500)
        })
    }), $(".open_sign_in").click(function() {
        $(".for_sign_up, .for_forgot").fadeOut(500, function() {
            $(".for_sign_in").fadeIn(500)
        })
    }), $(".forgot_pasword").click(function() {
        $(".for_sign_in").fadeOut(500, function() {
            $(".for_forgot").fadeIn(500)
        })
    }), $("#login_submit").on("click", function(e) {
      
        e.preventDefault();
        var t = $("#email").val(),
            s = $("#password").val();
        "" == t || "" == s ? $("#login-status-wrapper").text("Please Enter Username And Password To Continue!!!").show() : ($("#login_auth_loading_image").show(), $(".data-utility-loader", $("#myModal_1")).show(), $("#login-status-wrapper").text("Please Wait!!!").hide(), $.post(app_base_url + "index.php/auth/login/", {
            username: t,
            password: s
        }, function(e) {
            $("#login_auth_loading_image").hide(), e.status ? ($("#myModal_1").hide(), $(".my_account_dropdown").hide(), window.location.reload()) : $("#login-status-wrapper").text(e.data).show(), $(".data-utility-loader", $("#myModal_1")).hide()
        }))
    }), $(".register_user").bind("click", function(e) {
        e.preventDefault(), $("#register_user_div").provabPopup({
            modalClose: !0,
            zIndex: 10000005,
            closeClass: "closepopup"
        })
    }), $(".frgotpaswrd").bind("click", function(e) {
        e.preventDefault(), $("#forgotpaswrdpop").provabPopup({
            modalClose: !0,
            zIndex: 10000005,
            closeClass: "closepopup"
        })
    }), $("#reset-password-trigger").on("click", function(e) {
        e.preventDefault(), $("#recover-title-wrapper").hide(), $(".data-utility-loader", $("#myModal_2")).show(), $.post(app_base_url + "index.php/auth/forgot_password/", {
            email: $("#recover_email").val(),
            phone: $("#recover_phone").val()
        }, function(e) {
            e.status ? $("#recover-title-wrapper").removeClass("alert-danger").addClass("alert-success") : $("#recover-title-wrapper").removeClass("alert-success").addClass("alert-danger"), $("#recover-title").text(e.data), $("#recover-title-wrapper").show(), $(".data-utility-loader", $("#myModal_2")).hide()
        })
    }), 
    $("#reset-password-trigger-book").on("click", function(e) {
        
        e.preventDefault(), $("#recover-title-wrapper-book").hide(), $(".data-utility-loader", $("#myModal_2")).show(), $.post(app_base_url + "index.php/auth/forgot_password/", {
            email: $("#recover_email_book").val(),
            phone: $("#recover_phone_book").val()
        }, function(e) {
            e.status ? $("#recover-title-wrapper-book").removeClass("alert-danger").addClass("alert-success") : $("#recover-title-wrapper-book").removeClass("alert-success").addClass("alert-danger"), $("#recover-title-book").text(e.data), $("#recover-title-wrapper-book").show(), $(".data-utility-loader", $("#myModal_2")).hide()
        })
    }),
    $("form#register_user_form #register_user_button").click(function(e) {
        0 == $("#register-error-msg").hasClass("hide") && $("#register-error-msg").addClass("hide"), 0 == $("#register-status-wrapper").hasClass("hide") && $("#register-status-wrapper").addClass("hide"), e.preventDefault();
        var t = !0,
            s = "";
        $("form#register_user_form .validate_user_register").each(function() {
            if($(this).attr("name") == "phone" && this.value != "" && this.value <= 0)
            {
                $(this).siblings(".err_msg").hide();
                $("<span class='err_msg invalid-ip'>Phone number must not contain only zeros.</span>").insertAfter($(this));
                return false;
            }
            "" == this.value ? ($(this).addClass("invalid-ip"),$(this).parent().find(".err_msg").addClass("invalid-ip"), 1 == t && (t = !1, s = this)) : $(this).hasClass("invalid-ip") && $(this).removeClass("invalid-ip").parent().find(".err_msg").removeClass("invalid-ip")
        }), 0 == $("#register_tc").prop("checked") ? (t = !1, $("#register_tc").addClass("invalid-ip")) : $("#register_tc").removeClass("invalid-ip"), 0 == t ? $(s).focus() : ($("#loading").removeClass("hide"), $.post(app_base_url + "index.php/auth/register_on_light_box", $("form#register_user_form").serialize(), function(e) {
            var t = e.status,
                s = e.data;
            1 == t ? ($("#register-status-wrapper").empty().html(s), $("#register-status-wrapper").removeClass("hide"), $("#register_tc").prop("checked", !1), $("#loading").addClass("hide"), $('input[name="email"], input[name="password"], input[name="confirm_password"]', "form#register_user_form").val("")) : ($("#register-error-msg").empty().html(s), $("#register-error-msg").removeClass("hide"), $("#loading").addClass("hide"))
        }))
    }), $(".user_logout_button").click(function(t) {
        t.preventDefault(), e()
    })
}), $(document).ready(function() {
    $(".sidebtn1").click(function() {
        $(".logdowndiv").not($(this).children(".logdowndiv").slideToggle("fast")).hide()
    }), $(".sidebtn1, .logdowndiv").click(function(e) {
        e.stopPropagation()
    }), $(document).click(function() {
        $(".logdowndiv").slideUp("fast")
    }), $(window).width() < 992 && ($(".menu_brgr").click(function() {
        $(".menu").slideToggle("fast")
    }), $(".menu_brgr, .menu").click(function(e) {
        e.stopPropagation()
    }), $(document).click(function() {
        $(".menu").slideUp("fast")
    })), $(window).scroll(function() {
        $(window).scrollTop() > 40 ? ($(".topssec").addClass("fixed"), $(".fromtopmargin").addClass("set_up")) : ($(".topssec").removeClass("fixed"), $(".fromtopmargin").removeClass("set_up"))
    }), $(".inside_alert").addClass("show"), $(".close_alert").click(function() {
        $(this).parent(".alert_box").parent(".inside_alert").removeClass("show")
    })
});