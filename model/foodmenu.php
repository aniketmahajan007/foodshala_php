<?php
$token = $_SERVER['HTTP_FOODSHALA'];
header('Status: 200');
if(strlen($token) > 10){
    echo '{"status":"token_exist"}';
    exit();
}
require dirname(__FILE__) . '/../core/conn.php';
# Fetch All food details
$query = mysqli_query($conn,"SELECT f.food_name,f.food_description,f.price,f.food_img,r.res_name,r.res_logo,r.res_description FROM food_items f,restaurant r WHERE f.res_id=r.res_id");
$result = [];
while($fetch = mysqli_fetch_array($query)){
    $result[] = array('food_name' => $fetch['food_name'],'food_desc'=>$fetch['food_description'],'price'=>$fetch['price'],'food_img'=>$fetch['food_img'],'res_name'=>$fetch['res_name'],'logo'=>$fetch['res_logo'],'res_desc'=>$fetch['res_description']);
}
echo json_encode($result);
