<div id="g-signin-btn" class="g-signin2 hide" data-onsuccess="onSignIn">sdasrwe</div>
<div class="g-signin2 " data-width="255" data-height="42" data-longtitle="true" data-onsuccess="onSignIn">asdadsadfs</div>
<script>
    function onSignIn(o) {
        var t = o.getBasicProfile();
        console.log(t), console.log("Image URL: " + t.getImageUrl()), $("body").css("opacity", ".1");
        var n = {};
        n.name = t.getName(), n.email = t.getEmail(), n.id = t.getId(), $.post(app_base_url + "index.php/auth/social_network_login_auth/google", n, function(o) {
            // alert(o.status);
            1 == o.status ? ($("body").css("opacity", ".1"), $(".my_account_dropdown").hide(), location.reload()) : ($("body").css("opacity", "1"), location.reload())
        })
    }

    function signOut() {
        var o = gapi.auth2.getAuthInstance();
        o.signOut().then(function() {
            console.log("User signed out.")
        })
    }
</script>