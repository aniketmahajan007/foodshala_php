<?php
error_reporting(E_ERROR | E_PARSE);
require dirname(__FILE__) .'/../vendor/autoload.php';
use \Firebase\JWT\JWT;
function validate_token($token,$SECRET,$requesting=0): object
{

    if($requesting!=1){
        if(strlen($token) < 5){
            echo '{"status":"token_exist"}';
            exit();
        }
    }
    $dec='';
    try {
        $decoded = JWT::decode($token, $SECRET, array('HS256'));
        $dec=$decoded;
    }catch (Exception){
        if($requesting!=1) {
            echo '{"status":"invalid_token"}';
            exit();
        }else{
            @$dec = new stdClass();
        }
    }
    return $dec;
}
