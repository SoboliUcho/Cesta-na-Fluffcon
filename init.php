<?php
// session_start();
// $_SESSION["User_ID"] = 1;

spl_autoload_register(function ($class) {
    include_once("classes/$class.php");
});

// include_once('database.php');
include_once('logins.php');

// include_once('menu.php');
// include_once('user.php');

Db::connect($databese['host'], $databese['database'], $databese['user'], $databese['password']);

if (isset($off)){
    $request = false;
}
else{
    $request = true;
}
include_once('header.php');
new Request($request);


function debugTimestamp() {
    echo(strtotime(date('his')));
}
$language = get_lang();

// print_r($_SERVER['SCRIPT_NAME'] ." - ". $_SERVER['REQUEST_URI']);
// print_r(json_encode($_SERVER));