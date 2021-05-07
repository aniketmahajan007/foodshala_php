<?php
#VALIDATION

if($_SERVER['REQUEST_METHOD'] !== 'POST' OR !isset($_POST['mob_number'],$_POST['food_pref'],$_POST['user_name'],$_POST['user_address']) OR strlen($_POST['mob_number'])!=10 OR $_POST['food_pref']<0 OR $_POST['food_pref']>2){
    echo '{"status":"invalid"}';
    exit();
}
#SAnitizing
$mob=htmlspecialchars($_POST['mob_number'],ENT_QUOTES);
$food=(int)$_POST['food_pref'];
$name = htmlspecialchars($_POST['user_name'],ENT_QUOTES);
$address = htmlspecialchars($_POST['user_address'],ENT_QUOTES);
require dirname(__FILE__).'/../../core/conn.php';
$nums=$conn->prepare("UPDATE users SET food_pre=?,user_name=?,address=?,mob_number=? WHERE user_id=? LIMIT 1");
$nums->bind_param("isssi",$food,$name,$address,$mob,$token->iss);
if(!$nums->execute()){
    $nums->close();
    mysqli_close($conn);
    echo '{"status":"error"}';
    exit();
}
$nums->close();
echo '{"status":"success"}';
exit();
