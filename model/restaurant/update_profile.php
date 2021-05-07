<?php
#VALIDATION
if($_SERVER['REQUEST_METHOD'] !== 'POST' OR !isset($_POST['res_name'],$_POST['res_desc'],$_POST['res_address'],$_POST['mob_number']) OR strlen($_POST['mob_number'])!=10){
    echo '{"status":"invalidh"}';
    exit();
}
# Validating image
if(empty($_FILES["res_img"]["name"]) OR !$_FILES["res_img"]["size"]){
    echo '{"status":"invalid_img"}';exit();
}
$checks=htmlspecialchars(filter_var($_FILES["res_img"]["name"],FILTER_SANITIZE_STRING));
$check = @getimagesize($_FILES["res_img"]["tmp_name"]);
if(!$check){
    echo '{"status":"invalid_img"}';exit();
}
$cur_size=$_FILES["res_img"]["size"];
if($cur_size>2024000){
    echo '{"status":"img_size"}';exit();
}
#sanitizing name
$coman=array("/'/","/,/","/ /","/!/","/@/","/#/","/%/","/&/");
$ImageName=str_replace(array('(',')','$','*','~','>','<',",","/"),'a',preg_replace($coman,"-",strtolower($checks)));
unset($coman);
$ImageExt=mime_content_type($_FILES["res_img"]["tmp_name"]);
if($ImageExt==="image/jpg" OR $ImageExt==="image/jpeg" OR $ImageExt==="image/png"){}else{
    echo '{"status":"format"}';exit();
}
$ImageName = substr(str_replace(array(" ","."),"-",preg_replace("/\.[^.\s]{3,4}$/", "", preg_replace("/[^\p{L}\s]/u","z",$ImageName))),0,16);
$ImageName=$ImageName.'-'.rand(0,999999).'.'.str_replace('image/','',$ImageExt);
#sanitize complete

# Sanitizing post data
$res_name = htmlspecialchars($_POST['res_name'],ENT_QUOTES);
$res_desc = htmlspecialchars($_POST['res_desc'],ENT_QUOTES);
$res_address = htmlspecialchars($_POST['res_address'],ENT_QUOTES);
$mob_number = (int)$_POST['mob_number'];
if(!move_uploaded_file($_FILES["res_img"]["tmp_name"], dirname(__FILE__).'/../../res_logo/'.$ImageName)){
    echo '{"status":"error"}';exit();
}
require dirname(__FILE__).'/../../core/conn.php';
$nums=$conn->prepare("UPDATE restaurant SET res_name=?,res_description=?,address=?,mob_number=?,res_logo=? WHERE res_id=? LIMIT 1");
$nums->bind_param("sssssi",$res_name,$res_desc,$res_address,$mob_number,$ImageName,$token->iss);
if(!$nums->execute()){
    $nums->close();
    mysqli_close($conn);
    unlink(dirname(__FILE__).'/../../res_logo/'.$ImageName);
    echo '{"status":"error"}';
    exit();
}
$nums->close();
mysqli_close($conn);
echo '{"status":"success"}';
