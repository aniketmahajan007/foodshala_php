<?php
# Validation
if($_SERVER['REQUEST_METHOD'] !== 'POST' OR !isset($_POST['email'],$_POST['cpassword'],$_POST['password'],$_POST['food_pre']) OR $_POST['food_pre']<0 OR $_POST['food_pre']>2){
    header('Status: 400');exit();
}
header('Status: 200');
# Validating email
$email=filter_var(trim($_POST['email']),FILTER_SANITIZE_EMAIL);
if (!filter_var($email, FILTER_VALIDATE_EMAIL) === true){
    echo '{"status":"email_fail"}';
    exit();
}
# Strong password check
$pass = filter_var($_POST['password'],FILTER_SANITIZE_STRING);
if(strlen($pass) < 5 OR $_POST['password'] != $pass){
    echo '{"status":"pass_weak"}';
    exit();
}
#Confirm pass check
if($pass !== $_POST['cpassword']){
    echo '{"status":"cpass_not_match"}';
    exit();
}
$foodpre = (int)$_POST['food_pre'];
#encrpyting password
$pass = password_hash($pass,PASSWORD_DEFAULT,array('cost'=>10));
# database Connecting
require dirname(__FILE__) . '/../../core/conn.php';
# Checking already register
$nums=$conn->prepare("SELECT 1 FROM users WHERE email=? LIMIT 1");
$nums->bind_param("s",$email);
if(!$nums->execute()){
    $nums->close();
    echo '{"status":"error"}';
    exit();
}
$nums->store_result();
if($nums->num_rows>0){
    $nums->close();
    mysqli_close($conn);
    echo '{"status":"already"}';
    exit();
}
#Inserting using prepared statement
$nums = $conn -> prepare("INSERT INTO users (email, password, food_pre) VALUES (?,?,?)");
$nums->bind_param("ssi",$email,$pass,$foodpre);
if(!$nums->execute()){
    mysqli_close($conn);
    echo '{"status":"error"}';
    exit();
}
$nums->close();
mysqli_close($conn);
echo '{"status":"success"}';
