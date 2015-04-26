<?php 
ob_start("ob_gzhandler");
require('keys.php');
$conn = new mysqli($db_info['host'], $db_info['user'], $db_info['pass'], $db_info['database']);
if(mysqli_connect_errno()) {
    die("MySQL connection failed: ". mysqli_connect_error());
}


$table = $_GET['table'];
$column = '';
if ($table == 'Item') $column = 'Item_id';
else if ($table == 'Customer') $column = 'Customer_id';
else if ($table == 'City') $column = 'City_id';
else if ($table == 'Store') $column = 'Store_id';
else if ($table == 'Orders') $column = 'Order_id';


$sql = "SELECT {$column} AS i FROM {$table} ORDER BY {$column}";

function get_array($result) {
    $data = array();
    if ($result){
        while ($row = $result -> fetch_assoc()){
            array_push($data, $row['i']);
        }
    }
    return $data;
}

$arr = get_array($conn -> query($sql));

header('Content-Type: application/json');
echo json_encode($arr);

?>