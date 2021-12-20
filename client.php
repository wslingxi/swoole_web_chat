<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>聊天室</title>
    <link href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
    <script src="static/common.js"></script>
    <style>
        .alert{width: 90%;}
    </style>
</head>
<body>
<?php
include_once "lib/db.php";
$uid = intval($_GET['uid']);
$sql = "SELECT * FROM `user` WHERE id = $uid";
$user =  PdoDb::db()->getRow($sql);
$sql = "SELECT * FROM user WHERE id!=".$uid;
$users = PdoDb::db()->getAll($sql);
?>
<div class="alert alert-light" role="alert">
    欢迎<?php echo $user['name']?> <a id="logout" href="javascript:void(0)">退出</a>
</div>
<div class="card mb-3" style="max-width: 940px; padding: 10px">
    <div class="row no-gutters">
        <div class="col-md-9">
            <div id="content" style="overflow-y: auto; height: 500px; position: relative;word-wrap: break-word;">

            </div>
        </div>
        <div class="col-md-3">
            <div class="card-body">
                <h5 class="card-title">好友列表</h5>
                <?php foreach ($users as $v){?>
                <p class="card-text">
                    <a href="<?php echo "client.php?uid=$uid&touid=$v[id]"?>"><?php echo $v['name']?></a>
                </p>
                <?php }?>
            </div>
        </div>
    </div>
</div>

<div id="send" class="card mb-3" style="max-width: 940px;">
    <input class="form-control" rows="3" id="title" placeholder="点击回车发送">
    <input type="hidden" value="<?php echo $_GET['touid']?>" id="touid" placeholder="touid">
</div>
<script>
    if ($.cookie('token')){
        isLogin();
    }else{
        window.location.href = "client_login.html";
    }
    var wsServer = 'ws://127.0.0.1:8001?uid=<?php echo $_GET['uid']?>';
    var websocket = null;
    var lock = false;
    $(function () {
        link();
    })
    function link() {
        websocket = new WebSocket(wsServer);
        websocket.onopen = function (evt) {
            console.log("Connected to WebSocket server.");
        };

        websocket.onclose = function (evt) {
            //console.log("Disconnected");
            websocket.close();
            relink();
        };

        websocket.onmessage = function (evt) {
            console.log('Retrieved data from server: ' + evt.data);
            var html = '';
            var classstr = (evt.data).indexOf("我说")>-1?'success':'primary';
            html += '<div class="alert alert-' + classstr + '">';
            html += evt.data;
            html += '</div>';
            $('#content').append(html);
            var div = document.getElementById('content');
            div.scrollTop = div.scrollHeight;
        };

        websocket.onerror = function (evt, e) {
            console.log('Error occured: ' + evt.data);
            websocket.close();
            relink();
        };
    }
    
    function relink() {
        if (lock){
            return false;
        }else{
            lock = true;
            setTimeout(function () {
                link(),
                lock = false
            }, 1000)
        }
    }

    $('#title').keydown(function (e) {
        if (e.keyCode == 13){
            var text = $('#title').val();
            text += "|" + $('#touid').val() + "|" + "<?php echo $_GET['uid']?>"
            websocket.send(text);
        }
    });

    $('#logout').click(function () {
        $.cookie("token", null);
        $.cookie("uid", null);
        window.location.href = "client_login.html";
    })
</script>

</body>
</html>