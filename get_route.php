<?php
include_once('init.php');
$user_id = $_POST['user_id'];

// $user_id = 1;

$user = new User($user_id);

echo $user->goeJSON();