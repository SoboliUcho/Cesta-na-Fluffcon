<?php
class Request{
    public $url;
    public $method;
    public $request;
    public $ip;

    public function __construct($save = true){
        $this->request = json_encode($_REQUEST);
        $this->url = "fluff/".$_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->ip = $_SERVER['REMOTE_ADDR'];
        if (strpos($this->ip, '2a02:2b88:3:8002:80') !== false) {
            return;
        }
        if ($save)
            $this->save();
    }
    public function save(){
        // echo "INSERT INTO requests( Request_Url, IP, Request_data) VALUES ('$this->url', '$this->ip','$this->request')";
        $ipInfo = file_get_contents("http://ip-api.com/json/{$this->ip}");
        $save = Db::query("INSERT INTO requests( Request_Url, IP, Request_data,Info) VALUES ('$this->url', '$this->ip','$this->request','$ipInfo')");
        // print_r($save);
    }
}