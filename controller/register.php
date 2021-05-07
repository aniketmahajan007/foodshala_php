<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: FOODSHALA");
ob_start();
$requesting = $_GET['requesting'];
if (!isset($requesting) OR $requesting < 1 OR $requesting > 5) {
    header('Status: 200');
    echo '{"status":"invalid_request"}';
    exit();
}
header("Content-Type: application/json");
switch ($requesting) {
    case 1:
        require('../model/register/user_register.php');
        break;
    case 2:
        require('../model/register/res_register.php');
        break;
    case 3:
        require('../model/register/user_login.php');
        break;
    case 4:
        require('../model/register/res_login.php');
        break;
    case 5:
        require('../model/foodmenu.php');
        break;
    default:
        header('Status: 200');
        echo '{"status":"invalid_request"}';
        break;
}
