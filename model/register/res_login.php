<?php
# Validation
if($_SERVER['REQUEST_METHOD'] !== 'POST' OR !isset($_POST['email'],$_POST['password'])){
    header('Status: 400');exit();
}
header('Status: 200');
# Validating email
$email=filter_var(trim($_POST['email']),FILTER_SANITIZE_EMAIL);
if (!filter_var($email, FILTER_VALIDATE_EMAIL) === true){
    echo '{"status":"email_fail"}';
    exit();
}
$pass = filter_var($_POST['password'],FILTER_SANITIZE_STRING);
if(strlen($pass) < 5 OR $_POST['password'] != $pass){
    echo '{"status":"pass_fail"}';
    exit();
}
# database Connecting
require dirname(__FILE__) . '/../../core/conn.php';
$nums=$conn->prepare("SELECT res_id,password FROM restaurant WHERE email=? LIMIT 1");
$nums->bind_param("s",$email);
if(!$nums->execute()){
    $nums->close();
    mysqli_close($conn);
    echo '{"status":"error"}';
    exit();
}
$nums->store_result();
if($nums->num_rows<1){
    $nums->close();
    mysqli_close($conn);
    echo '{"status":"invalid"}';
    exit();
}
$nums->bind_result($id,$fpass);$nums->fetch();$nums->close();
#Verification
if(!password_verify($pass,$fpass)){
    mysqli_close($conn);
    echo '{"status":"invalid"}';
    exit();
}
require dirname(__FILE__) .'/../../vendor/autoload.php';
use \Firebase\JWT\JWT;
JWT::$leeway = 60;
require dirname(__FILE__) .'/../../core/SECRET.php';
$time = time();
$payload = array(
    "iss" => $id,
    "aud" => 1,
    "iat" => $time,
    "nbf" => $time+ 10,
    "exp" => $time + (3600 * 12)
);
$jwt = JWT::encode($payload, $SECRET);
$result = [];
$result[] = array('token' => $jwt, 'status' => 'success');
echo json_encode($result);
