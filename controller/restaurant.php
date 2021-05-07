<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: FOODSHALA");
ob_start();
$requesting = $_GET['requesting'];
if (!isset($requesting) OR $requesting < 1 OR $requesting > 5) {
    header('Status: 400');
    exit();
}
header("Content-Type: application/json");
header('Status: 200');
require dirname(__FILE__).'/../core/token_validate.php';
require dirname(__FILE__).'/../core/SECRET.php';
$token = $_SERVER['HTTP_FOODSHALA'];
$token = validate_token($token,$SECRET);
if($token->aud != 1){
    echo '{"status":"restricted_token"}';
    exit();
}
switch ($requesting) {
    case 1:
        require('../model/restaurant/your_menu.php');
        break;
    case 2:
        require('../model/restaurant/order_his.php');
        break;
    case 3:
        require('../model/restaurant/add_food.php');
        break;
    case 4:
        require('../model/restaurant/update_profile.php');
        break;
    case 5:
        require('../model/restaurant/fetch_rest.php');
        break;
    default:
        header('Status: 400');
        break;
}
