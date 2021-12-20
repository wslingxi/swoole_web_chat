<?php
include_once "lib/db.php";
Class WebSocket{
    private $server;
    private $uid_fd = "uid_fid";
    private $user = "user";
    public function __construct()
    {
        $this->server = new Swoole\WebSocket\Server('0.0.0.0', 9502);
        $this->server->set([
            'worker_num' => 4,
            'dispatch_mode'=>5,
        ]);
        $this->server->on("Open", [$this, 'onOpen']);
        $this->server->on("Message", [$this, 'onMessage']);
        $this->server->on("Close", [$this, 'onClose']);
        $this->server->start();
    }

    public function onOpen($server, $request){
        $this->bindUid($server, $request);
        $server->push($request->fd, "您好,欢迎来到聊天室\n");
    }

    public function onMessage($server, $frame){
        $data = $frame->data;
        if (strstr($data, "|")){
            $arr = explode("|", $data);
        }

        if ($arr[1]){
            $sql = "SELECT * FROM $this->uid_fd WHERE uid=$arr[1]";
            $ret = PdoDb::db()->getRow($sql);
            $server->push($frame->fd, "我说 : " . $arr[0]);
            $server->push($ret['fd'], $this->getNameByUid($arr[1])." : ".$arr[0]);
        }else{
            $server->push($frame->fd, "我对大家说 : " . $arr[0]);
            foreach ($server->getClientList() as $fd){
                if ($fd!=$frame->fd){
                    $server->push($fd, $this->getNameByUid($arr[2])."对大家说 : " . $arr[0]);
                }
            }
        }
    }

    public function onClose($server, $fd){
        echo "client-{$fd} is closed\n";
    }

    /*
     * fd和Uid绑定
     * @param $server
     * @param $request
     */
    public function bindUid($server, $request){
        $uid = intval($request->get['uid']);
        $server->bind($request->fd, $uid);
        Swoole\Coroutine::create(function () use($request, $uid) {
            $sql = "SELECT * FROM $this->uid_fd WHERE uid = $uid";
            $ret = PdoDb::db()->getRow($sql);
            if ($ret['id']){
                $sql = "UPDATE $this->uid_fd SET fd = $request->fd WHERE uid = $uid";
                PdoDb::db()->exec($sql);
            }else{
                $sql = "INSERT INTO $this->uid_fd(`uid`, `fd`) VALUE ($uid, $request->fd)";
                PdoDb::db()->exec($sql);
            }
        });
    }

    public function getNameByUid($uid){
        $sql = "SELECT * FROM $this->user WHERE id = $uid";
        $ret =  PdoDb::db()->getRow($sql);
        return $ret['name'];
    }
}
new WebSocket();