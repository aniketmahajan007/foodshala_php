<?php
# Connecting to database
require dirname(__FILE__) . '/../../core/conn.php';
$nums = $conn->prepare("SELECT user_name,address,food_pre,mob_number FROM users WHERE user_id=? LIMIT 1");
$nums->bind_param("i", $token->iss);
if (!$nums->execute()) {
    $nums->close();
    mysqli_close($conn);
    echo '{"status":"error"}';
    exit();
}
$nums->bind_result($fetch['user_name'], $fetch['address'],$fetch['food_pref'], $fetch['mob_number']);
$nums->fetch();$nums->close();
mysqli_close($conn);
$result[] = array('user_name' => $fetch['user_name'],'food_pref'=>$fetch['food_pref'], 'address' => $fetch['address'], 'mob_number' => $fetch['mob_number']);
echo json_encode($result);
