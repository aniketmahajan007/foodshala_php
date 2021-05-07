<?php
# Connecting to database
require dirname(__FILE__) . '/../../core/conn.php';
# Checking restaurant exist
$nums=$conn->prepare("SELECT res_name FROM restaurant WHERE res_id=? LIMIT 1");
$nums->bind_param("i",$token->iss);
if(!$nums->execute()){
    $nums->close();
    mysqli_close($conn);
    echo '{"status":"error"}';
    exit();
}
$nums->bind_result($res_name);$nums->fetch();$nums->close();
# If exist fetching its menu
$nums=$conn->prepare("SELECT food_name,food_description,food_img,price,food_type FROM food_items WHERE food_items.res_id=?");
$nums->bind_param("i",$token->iss);
if(!$nums->execute()){
    $nums->close();
    mysqli_close($conn);
    echo '{"status":"error"}';
    exit();
}
$fetch = [];
$nums->store_result();
# Sending JSON response
if($nums->num_rows>0){
    $nums->bind_result($fetch['food_name'],$fetch['food_desc'],$fetch['food_img'],$fetch['price'],$fetch['food_type']);
    $result = [];
    while($nums->fetch()){
        $result[] = array('res_name'=>$res_name,'food_name'=>$fetch['food_name'],'food_desc'=>$fetch['food_desc'],'food_img'=>$fetch['food_img'],'price'=>$fetch['price'],'food_type'=>$fetch['food_type']);
    }
    $nums->close();
    mysqli_close($conn);
    echo json_encode($result);
}else{
    $nums->close();
    mysqli_close($conn);
    echo '{"status":"res","res_name":"'.$res_name.'"}';
}
