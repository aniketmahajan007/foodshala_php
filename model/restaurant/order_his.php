<?php
require dirname(__FILE__).'/../../core/conn.php';
$nums=$conn->prepare("SELECT o.order_id,o.order_date,o.tot_price,r.res_logo,r.res_name,r.res_description FROM orders o,restaurant r WHERE o.res_id=? AND o.res_id=r.res_id");
$nums->bind_param("i",$token->iss);
if(!$nums->execute()){
    $nums->close();
    mysqli_close($conn);
    echo '{"status":"error"}';
    exit();
}
$nums->bind_result($fetch['order_id'],$fetch['order_date'],$fetch['tot_price'],$fetch['res_logo'],$fetch['res_name'],$fetch['res_desc']);
$temp_fetch = [];
while($nums->fetch()){
    $temp_fetch[] = array('order_id'=>$fetch['order_id'],'order_date'=>$fetch['order_date'],'tot_price'=>$fetch['tot_price'],'res_logo'=>$fetch['res_logo'],'res_name'=>$fetch['res_name'],'res_desc'=>$fetch['res_desc']);
}
$nums->close();
$result=[];
foreach ($temp_fetch as $fetch){
    $query=mysqli_query($conn,"SELECT f.food_name FROM order_items o,food_items f WHERE o.order_id='{$fetch['order_id']}' AND f.food_id=o.food_id");
    $fetch['order_list']='';
    while ($newfetch=mysqli_fetch_array($query)){
        $fetch['order_list'].='1x '.$newfetch['food_name'].', ';
    }
    $fetch['order_list']=substr($fetch['order_list'], 0, -2);
    $result[] =array('order_id'=>$fetch['order_id'],'order_date'=>$fetch['order_date'],'price'=>$fetch['tot_price'],'logo'=>$fetch['res_logo'],'res_name'=>$fetch['res_name'],'res_desc'=>$fetch['res_desc'],'order_list'=>$fetch['order_list']);
}
echo  json_encode($result);
