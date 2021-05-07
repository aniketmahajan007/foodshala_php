<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: FOODSHALA");
ob_start();
$requesting = $_GET['requesting'];
if (!isset($requesting) OR $requesting < 1 OR $requesting > 6) {
    header('Status: 200');
    echo '{"status":"invalid_request"}';
    exit();
}
header("Content-Type: application/json");
header('Status: 200');
# Validating token since these request can only access by register member
require dirname(__FILE__).'/../core/token_validate.php';
require dirname(__FILE__).'/../core/SECRET.php';
$token = $_SERVER['HTTP_FOODSHALA'];
$token = validate_token($token,$SECRET,$requesting);
# Token is of restaurant not customer restrict it to access further
if($token->aud != 0){
    echo '{"status":"restricted_token"}';
    exit();
}
switch ($requesting) {
    case 1:
        require('../model/users/dash.php');
        break;
    case 2:
        require('../model/users/res_menu.php');
        break;
    case 3:
        require('../model/users/orderit.php');
        break;
    case 4:
        require('../model/users/pre_order.php');
        break;
    case 5:
        require('../model/users/update_profile.php');
        break;
    case 6:
        require('../model/users/fetch_user.php');
        break;
    default:
        header('Status: 200');
        echo '{"status":"invalid_request"}';
        break;
}
