<?php
include_once "lib/db.php";
include_once "lib/client_jwt.php";
if (intval($_GET['type']) == 1){
    $name = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    $sql = "SELECT * FROM user WHERE name = '".$name."' AND password = '".md5($password)."'";
    $res = PdoDb::db()->getRow($sql);

    if ($res['id']){
        $payload_test=array('iss'=>'swoole','iat'=>time(),'exp'=>time()+7200,'nbf'=>time(),'sub'=>'www.swoole.com','jti'=>md5(uniqid('JWT').time()), 'usernname'=> $name);;
        $data = [
            "state" => 'success',
            "token" => Jwt::getToken($payload_test),
            "message" => "登录成功",
            "uid" => $res['id']
        ];
    }else{
        $data = [
            "state" => 'error',
            "message" => "登录失败"
        ];
    }
    echo  json_encode($data);
}elseif (intval($_GET['type']) == 2){
    $headers = getallheaders();
    if (Jwt::verifyToken($headers['Authorization'])){
        $data = [
            "state" => 'success',
        ];
    }else{
        $data = [
            "state" => 'error',
        ];
    }
    echo  json_encode($data);
}
