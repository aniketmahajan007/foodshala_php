<?php
#VALIDATION
if($_SERVER['REQUEST_METHOD'] !== 'POST' OR !isset($_POST['item_list'],$_POST['rest_id'],$_POST['tot_items']) OR $_POST['rest_id'] < 1 OR $_POST['tot_items']<1){
    echo '{"status":"invalid_info"}';
    exit();
}
$rest_id = (int)$_POST['rest_id'];
$tot_item = (int)$_POST['tot_items'];
$item_list = $_POST['item_list'];
$i=0;
while($i<$tot_item){
    $item_list[$i] = (int)$item_list[$i];
    if(filter_var($item_list[$i], FILTER_VALIDATE_INT) === false){
        echo '{"status":"invalid_info"}';
        exit();
    }
    $i++;
}
$item_list_string = implode(',',$item_list);
#connecting database
require dirname(__FILE__).'/../../core/conn.php';
mysqli_autocommit($conn,FALSE);
$qsuccess=1;
# Transaction Method
$nums=$conn->prepare("SELECT sum(price) FROM food_items WHERE food_id IN (?) AND res_id = ?");
$nums->bind_param("si",$item_list_string,$rest_id);
if(!$nums->execute()){
    echo $nums->errno;
    $qsuccess=0;
    $nums->close();
}
if($qsuccess){
    $nums->bind_result($tot_price);$nums->fetch();$nums->close();
    $nums=$conn->prepare("INSERT INTO orders (res_id, cust_id,tot_price) VALUES (?,?,?)");
    $nums->bind_param("iii",$rest_id,$token->iss,$tot_price);
    if(!$nums->execute()){
        $nums->close();
        $qsuccess=0;
    }
    if($qsuccess){
        $order_id = $nums->insert_id;
        $nums->close();
        $i=0;
        while($i<$tot_item){
            if(!$qsuccess){break;}
            $nums=$conn->prepare("INSERT INTO order_items (order_id, food_id) VALUES (?,?)");
            $nums->bind_param("ii",$order_id,$item_list[$i]);
            if(!$nums->execute()){
                $qsuccess=0;
            }
            $nums->close();
            $i++;
        }
    }
}
if($qsuccess){
    mysqli_commit($conn);mysqli_close($conn);
    echo '{"status":"success"}';
    exit();
}else{
    mysqli_rollback($conn);mysqli_close($conn);
    echo '{"status":"error"}';
    exit();
}
# Register Order

