<?php
ob_start("ob_gzhandler");
require('keys.php');
$conn = new mysqli($db_info['host'], $db_info['user'], $db_info['pass'], $db_info['database']);
if(mysqli_connect_errno()) {
    die("MySQL connection failed: ". mysqli_connect_error());
}
header('Content-Type: application/json');

$report_no = $_POST['report'];

// $arr1 = array();
// for ($i=0; $i < 30; $i++) 
//     array_push($arr1, array($report_no => rand(), 'and is a random number' => rand()));
// $arr2 = array();
// for ($i=0; $i < 30; $i++) 
//     array_push($arr2, array($report_no => rand(), 'and is a random number' => rand()));
// $arr = array();
// $arr[0] = $arr1;
// $arr[1] = $arr2;
// echo json_encode($arr);

function get_array($result) {
    $data = array();
    if ($result){
        while ($row = $result -> fetch_assoc()){
            array_push($data, $row);
        }
    }
    return $data;
}

if ($report_no == '1') {
    $arr = array();
    $sql = "SELECT Description, Size, Weight, Unit_price "
        . "FROM Item WHERE Item_id = '{$_POST["item_id"]}'";
    $arr[0] = get_array($conn -> query($sql));
    $sql = "SELECT S.Store_id, S.Phone, City.City_name, State.State
        FROM (SELECT *
            FROM Store
            WHERE Store.Store_id IN (
                SELECT Hold_item.Store_id
                FROM Hold_item
                WHERE Hold_item.Item_id = '{$_POST["item_id"]}'
            )
        ) AS S, City, State
        WHERE S.City_id = City.City_id AND City.State = State.State";
    $arr[1] = get_array($conn -> query($sql));
    echo json_encode($arr);
} else if ($report_no == 'Item') {
    $arr = array();
    $sql = "INSERT INTO Item (Item_id, Description, Size, Weight, Unit_price) 
        VALUES ('{$_POST["item_id"]}', '{$_POST["dscp"]}', 
            '{$_POST["size"]}', '{$_POST["weight"]}', '{$_POST["price"]}')";
    if ($conn -> query($sql)) {
        $sql = "SELECT * "
            . "FROM Item WHERE Item_id = '{$_POST["item_id"]}'";
        $arr[0] = get_array($conn -> query($sql));
    } else {$arr[0][0]['result'] = $conn->error;}
    echo json_encode($arr);
} else if ($report_no == 'Customer') {
    $arr = array();
    $sql = "INSERT INTO Customer (Customer_id, Customer_name, First_order_date, City_id) 
        VALUES ('{$_POST["customer_id"]}', '{$_POST["name"]}', 
            '{$_POST["fod"]}', '{$_POST["city_id"]}')";
    $type = "Person";
    if ($_POST['type'] == '2') $type = "Government";
    if ($_POST['type'] == '3') $type = "Company";
    $sql2 = "INSERT INTO {$type} VALUES ('{$_POST["customer_id"]}')";
    if ($conn -> query($sql) && $conn -> query($sql2)) {
        $sql = "SELECT * "
            . "FROM Customer WHERE Customer_id = '{$_POST["customer_id"]}'";
        $arr[0] = get_array($conn -> query($sql));
    } else {$arr[0][0]['result'] = $conn->error;}
    echo json_encode($arr);
} else if ($report_no == 'Order') {
    $arr = array();
    $sql = "INSERT INTO Orders
        VALUES ('{$_POST["order_id"]}', '{$_POST["od"]}', 
            '{$_POST["customer_id"]}')";
    if ($conn -> query($sql)) {
        $sql = "SELECT * "
            . "FROM Orders WHERE Order_id = '{$_POST["order_id"]}'";
        $arr[0] = get_array($conn -> query($sql));
    } else {$arr[0][0]['result'] = $conn->error;}
    echo json_encode($arr);
} else if ($report_no == 'Order Item') {
    $arr = array();
    $sql = "INSERT INTO Contain_item
        VALUES ('{$_POST["item_id"]}', '{$_POST["order_id"]}', 
            '{$_POST["quantity"]}', '{$_POST["price"]}')";
    if ($conn -> query($sql)) {
        $sql = "SELECT * "
            . "FROM Contain_item WHERE Order_id = '{$_POST["order_id"]}'";
        $arr[0] = get_array($conn -> query($sql));
    } else {$arr[0][0]['result'] = $conn->error;}
    echo json_encode($arr);
} else if ($report_no == '2') {
    $arr = array();
    $sql = "SELECT DISTINCT(Contain_item.Order_id), Orders.Customer_id, Orders.Order_date FROM Contain_item 
                JOIN Orders ON Orders.Order_id = Contain_item.Order_id WHERE Contain_item.Order_id NOT IN (
                SELECT DISTINCT(Contain_item.Order_id)
                FROM `Contain_item` LEFT JOIN (SELECT * FROM Hold_item WHERE Store_id = {$_POST['store_id']}) Store_hold 
                ON Store_hold.Item_id = Contain_item.item_id 
                WHERE Store_hold.Quantity_held IS NULL OR Store_hold.Quantity_held < Contain_item.Quantity_ordered)";
    $arr[0] = get_array($conn -> query($sql));
    echo json_encode($arr);
} else if ($report_no == '3') {
    $arr = array();
    $sql = "SELECT DISTINCT(Contain_item.Item_id), Hold_item.Store_id, City.City_name, Store.Phone 
            FROM Contain_item LEFT JOIN Orders ON Orders.Order_id = Contain_item.Order_id 
            JOIN Hold_item ON Hold_item.Item_id = Contain_item.Item_id 
            JOIN Store ON Store.Store_id = Hold_item.Store_id 
            JOIN City ON City.City_id = Store.City_id WHERE Orders.Customer_id = {$_POST['customer_id']}";
    $arr[0] = get_array($conn -> query($sql));
    echo json_encode($arr);
} else if ($report_no == '4') {
    $arr = array();
    $sql = "SELECT Hold_item.Store_id , State.Headquarter_addr, City.City_name, City.State
            FROM Hold_item
            LEFT JOIN Store ON Store.Store_id = Hold_item.Store_id
            LEFT JOIN City ON City.City_id = Store.City_id
            LEFT JOIN State ON State.State = City.State
            WHERE Hold_item.Item_id = '{$_POST["item_id"]}'
            AND Hold_item.Quantity_held >= {$_POST["stock_level"]}";
    $arr[0] = get_array($conn -> query($sql));
    echo json_encode($arr);
} else if ($report_no == '5') {
    $arr = array();
    $sql = "SELECT Contain_item.Order_id, Contain_item.Item_id, Item.Description, City.City_name, Hold_item.Store_id FROM Contain_item 
            JOIN Item ON Item.Item_id = Contain_item.Item_id 
            LEFT JOIN Hold_item ON Hold_item.Item_id = Contain_item.Item_id 
            LEFT JOIN Store ON Store.Store_id = Hold_item.Store_id
            LEFT JOIN City ON City.City_id = Store.City_id
            ORDER BY Contain_item.Order_id LIMIT 0, 1000"; // 1000 is for limit the output
    $arr[0] = get_array($conn -> query($sql));
    echo json_encode($arr);
} else if ($report_no == '6') {
    $arr = array();
    $sql = " SELECT C.Customer_id, City.City_name, State.State
            FROM (SELECT *
            FROM Customer
            WHERE Customer.Customer_id = '{$_POST["customer_id"]}') 
            AS C, City, State
            WHERE C.City_id = City.City_id AND City.State = State.State";
    $arr[0] = get_array($conn -> query($sql));
    echo json_encode($arr);
} else if ($report_no == '7') {
    $arr = array();
    $sql = "SELECT SUM(Hold_item.Quantity_held) As 'Stock level'
            FROM Hold_item
            WHERE Hold_item.Store_id IN (SELECT Store.Store_id 
                             FROM Store
                             WHERE Store.City_id = '{$_POST["city_id"]}')
            AND Hold_item.Item_id = '{$_POST["item_id"]}'";
    $arr[0] = get_array($conn -> query($sql));
    echo json_encode($arr);
} else if ($report_no == '8') {
    $arr = array();
    $sql = "SELECT Contain_item.Item_id, Contain_item.Quantity_ordered, Customer.Customer_name, Hold_item.Store_id, City.City_name FROM Contain_item
            JOIN Orders ON Orders.Order_id = Contain_item.Order_id
            JOIN Customer ON Customer.Customer_id = Orders.Customer_id 
            LEFT JOIN Hold_item ON Hold_item.Item_id = Contain_item.Item_id 
            LEFT JOIN Store ON Store.Store_id = Hold_item.Store_id
            LEFT JOIN City ON City.City_id = Store.City_id
            WHERE Contain_item.Order_id = {$_POST['order_id']}
            ORDER BY Contain_item.Order_id";
    $arr[0] = get_array($conn -> query($sql));
    echo json_encode($arr);
} else if ($report_no == '9') {
    $arr = array();
    $sql = "SELECT Customer.Customer_name, Employee.Employee_discount_rate
            FROM Employee, Customer
            WHERE Customer.Customer_id = Employee.Customer_id";
    $arr[0] = get_array($conn -> query($sql));
    echo json_encode($arr);
} else if ($report_no == '10') {
    $arr = array();
    $sql = "SELECT Customer.Customer_name, Regular.Customer_address
            FROM Regular, Customer
            WHERE Customer.Customer_id = Regular.Customer_id";
    $arr[0] = get_array($conn -> query($sql));
    echo json_encode($arr);
} else if ($report_no == '11') {
    $arr = array();
    $sql = "SELECT Orders.Customer_id, SUM(Contain_item.Ordered_price) AS Sale_volume 
    FROM Orders LEFT JOIN Contain_item ON Orders.Order_id = Contain_item.Order_id 
    GROUP BY Orders.Customer_id ORDER BY Sale_volume";
    $arr[0] = get_array($conn -> query($sql));
    echo json_encode($arr);
}

?>