<?php
#VALIDATION
if($_SERVER['REQUEST_METHOD'] !== 'POST' OR !isset($_POST['rest_id']) OR $_POST['rest_id'] < 1){
    echo '{"status":"invalid_res"}';
    exit();
}
require dirname(__FILE__).'/../../core/conn.php';
$rest_id = (int)$_POST['rest_id'];
# Checking Res Exist
$nums = $conn -> prepare("SELECT 1 FROM restaurant WHERE res_id=? LIMIT 1");
$nums->bind_param("i",$rest_id);
if(!$nums->execute()){
    $nums->close();
    mysqli_close($conn);
    echo '{"status":"error"}';
    exit();
}
$nums->store_result();
if($nums->num_rows<1){
    $nums->close();mysqli_close($conn);
    echo '{"status":"invalid_res"}';
    exit();
}
# if exist fetch menu
$nums=$conn->prepare("SELECT food_name,food_type,food_id,food_description,food_img,price FROM food_items WHERE res_id=?");
$nums->bind_param("i",$rest_id);
if(!$nums->execute()){
    $nums->close();
    mysqli_close($conn);
    echo '{"status":"error"}';
    exit();
}
$nums->bind_result($fetch['food_name'],$fetch['food_type'],$fetch['food_id'],$fetch['food_description'],$fetch['food_img'],$fetch['price']);
$result = [];
while ($nums->fetch()){
    $result[] = array(
        'id'=>$fetch['food_id'],
        'name'=>$fetch['food_name'],
        'food_pref'=>$fetch['food_type'],
        'food_desc'=>$fetch['food_description'],
        'food_img'=>$fetch['food_img'],
        'price'=>$fetch['price']
    );
}
$nums->close();
mysqli_close($conn);
# Sending JSON response
echo json_encode($result);
