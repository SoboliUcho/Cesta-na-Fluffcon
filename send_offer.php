<?php
$off = true;
include_once('init.php');
$name = $_POST['name'];
$gps_x = $_POST['gps_x'];
$gps_y = $_POST['gps_y'];
$route = $_POST['route'];
$route = json_decode($route, true);
$time = $_POST['time'];
print_r($_POST['route']);
// end();
// $name = "Jmeno";
// $gps_x = 16.65997;
// $gps_y = 49.48751;
// $route = '{"length":"1","duration":"1","geometry":{"type":"Feature","geometry":{"type":"LineString","coordinates":[[16.65997,49.48751],[16.65997,49.48755]]},"properties":{}}}';
// $time = "025-01-25T17:00";

// echo $route;

$user = User::create($name, $gps_x, $gps_y, $route, $time);
