<?php
include_once('init.php');
$gps_x = $_POST['x'];
$gps_y = $_POST['y'];

// $gps_y = 16.65997;
// $gps_x = 49.48751;

// $gps_y = 14.43414;
// $gps_x = 50.08355;

$users = new Find_users($gps_x, $gps_y);

echo $users->return_users();