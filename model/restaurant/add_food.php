<?php
#VALIDATION
if($_SERVER['REQUEST_METHOD'] !== 'POST' OR !isset($_POST['food_name'],$_POST['food_desc'],$_POST['price'],$_POST['food_pref']) OR $_POST['food_pref']<1 OR $_POST['food_pref']>2 OR $_POST['price']<1){
    echo '{"status":"invalid"}';
    exit();
}
# Validating image
if(empty($_FILES["food_img"]["name"]) OR !$_FILES["food_img"]["size"]){
    echo '{"status":"invalid_img"}';exit();
}
$checks=htmlspecialchars(filter_var($_FILES["food_img"]["name"],FILTER_SANITIZE_STRING));
$check = @getimagesize($_FILES["food_img"]["tmp_name"]);
if(!$check){
    echo '{"status":"invalid_img"}';exit();
}
# Checking image size
$cur_size=$_FILES["food_img"]["size"];
if($cur_size>4024000){
    echo '{"status":"img_size"}';exit();
}
#sanitizing name
$coman=array("/'/","/,/","/ /","/!/","/@/","/#/","/%/","/&/");
$ImageName=str_replace(array('(',')','$','*','~','>','<',",","/"),'a',preg_replace($coman,"-",strtolower($checks)));
unset($coman);
$ImageExt=mime_content_type($_FILES["food_img"]["tmp_name"]);
if($ImageExt==="image/jpg" OR $ImageExt==="image/jpeg" OR $ImageExt==="image/png"){}else{
    echo '{"status":"format"}';exit();
}
$ImageName = substr(str_replace(array(" ","."),"-",preg_replace("/\.[^.\s]{3,4}$/", "", preg_replace("/[^\p{L}\s]/u","z",$ImageName))),0,16);
$ImageName=$ImageName.'-'.rand(0,999999).'.'.str_replace('image/','',$ImageExt);
#sanitize complete

# Sanitizing post data
$food_name = htmlspecialchars($_POST['food_name'],ENT_QUOTES);
$food_desc = substr(htmlspecialchars($_POST['food_desc'],ENT_QUOTES),0,290);
$price = (int)$_POST['price'];
$food_pref = (int)$_POST['food_pref'];
if(!move_uploaded_file($_FILES["food_img"]["tmp_name"], dirname(__FILE__).'/../../food_img/'.$ImageName)){
    echo '{"status":"error"}';exit();
}
require dirname(__FILE__).'/../../core/conn.php';
# Inserting into database
$nums=$conn->prepare("INSERT INTO food_items (food_name, food_description, price, food_type, food_img, res_id) VALUES (?,?,?,?,?,?)");
$nums->bind_param("ssiisi",$food_name,$food_desc,$price,$food_pref,$ImageName,$token->iss);
if(!$nums->execute()){
    $nums->close();
    mysqli_close($conn);
    unlink(dirname(__FILE__).'/../../food_img/'.$ImageName);
    echo '{"status":"error"}';
    exit();
}
$nums->close();
mysqli_close($conn);
# Sending JSON response
echo '{"status":"success","img":"'.$ImageName.'"}';
