<?php
# Connecting to database
require dirname(__FILE__) . '/../../core/conn.php';
# Fetching all restaurant
$query = mysqli_query($conn,"SELECT res_id,res_name,res_logo,res_description,serve_type FROM restaurant");
$result = [];
while($fetch = mysqli_fetch_array($query)){
    $result[] = array('res_id'=>$fetch['res_id'],'res_name'=>$fetch['res_name'],'logo'=>$fetch['res_logo'],'res_desc'=>$fetch['res_description'],'food_pref'=>$fetch['serve_type']);
}
# Sending JSON response
echo json_encode($result);
