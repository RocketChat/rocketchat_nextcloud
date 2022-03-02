var connecturl = OC.generateUrl('/apps/rocketchat_nextcloud/setup-url');
var txtbtnconnect = "Connect and Register a new server";
$(document).ready(function () {
    console.log(connecturl);
    $("#rcconnect").on("click",function(){
        if ($("#rcuser").val() && $("#rcpassword").val() && $("#rcurl").val()) {
            $(".rocketform").prop( "disabled", true );
            $(this).html('<img src="/apps/rocketchat_nextcloud/img/1476.gif" width=16> Connecting...');
            console.log("Connecting to Rocket.Chat Batch ref 72677769742e6265");
            rcconnect($("#rcuser").val(), $("#rcpassword").val(), $("#rcurl").val());
        } else {
            alert("Check your user id, password and Rocket Chat Server URL ");
        }
    });
});
function rcconnect(rcuser, rcpassword, rcurl) {
    var data = {
        rcuser: rcuser,
        rcpassword: rcpassword,
        rcurl: rcurl,
    };
    $.ajax({
        url: connecturl,
        type: "post",
        data: data,
        success: function (data) {
            console.log(data);
            $(".rocketform").prop("disabled", false);
            $("#rcconnect").html(txtbtnconnect);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".rocketform").prop("disabled", false);
            $("#rcconnect").html(txtbtnconnect);
            alert('There was an issue connecting to NextCloud, please report it to https://github.com/RocketChat/nextcloud/');
        },
    }).done(function(res){
        console.log('in done');
        console.log(res);
        if (res.status == 'success') {
            var userId = res.userId;
            var authToken = res.authToken;
            console.log(userId);
            console.log(authToken);
            $('#personalAccessToken').val(authToken);
            $('#userId').val(userId);
        }
    });
}
