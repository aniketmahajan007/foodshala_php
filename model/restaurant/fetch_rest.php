<?php
# Connecting to database
require dirname(__FILE__) . '/../../core/conn.php';
#Fetching Restaurant details
$nums=$conn->prepare("SELECT res_name,res_description,address,mob_number FROM restaurant WHERE res_id=? LIMIT 1");
$nums->bind_param("i",$token->iss);
if(!$nums->execute()){
    $nums->close();
    mysqli_close($conn);
    echo '{"status":"error"}';
    exit();
}
$nums->bind_result($fetch['res_name'],$fetch['res_desc'],$fetch['address'],$fetch['mob_number']);
$nums->fetch();$nums->close();mysqli_close($conn);
$result[] = array('res_name'=>$fetch['res_name'],'res_desc'=>$fetch['res_desc'],'address'=>$fetch['address'],'mob_number'=>$fetch['mob_number']);
# Sending JSON response
echo json_encode($result);
