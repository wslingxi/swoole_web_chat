function isLogin() {
    var state = "";
    $.ajax({
        type: "post",
        url: "client_user.php?type=2",
        async: true,
        dataType: "json",
        headers:{
            "Authorization":$.cookie("token"),
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        success:function (data) {
            if (data.state == "success"){
                //window.location.href = "client.php?uid=" + $.cookie("uid");
                return true;
            }else{
                $.cookie("token", null);
                $.cookie("uid", null);
                window.location.href = "client_login.html";
                return false;
            }
        },
        error:function (e) {
            alert(e.state);
        }
    })
}