<?php 
require('keys.php');
$conn = new mysqli($db_info['host'], $db_info['user'], $db_info['pass'], $db_info['database']);
if(mysqli_connect_errno()) {
    die("MySQL connection failed: ". mysqli_connect_error());
}

$sql = "CREATE TABLE State
(
State VARCHAR(255) NOT NULL,
Headquarter_addr VARCHAR(255),
PRIMARY KEY (State)
);

CREATE TABLE City
(
City_id INT NOT NULL,
City_name VARCHAR(255),
State VARCHAR(255),
PRIMARY KEY (City_id),
FOREIGN KEY (State) REFERENCES State(State)
);

CREATE TABLE Store
(
Store_id INT NOT NULL,
Phone INT,
City_id INT,
PRIMARY KEY (Store_id),
FOREIGN KEY (City_id) REFERENCES City(City_id)
);

CREATE TABLE Item
(
Item_id INT NOT NULL,
Description VARCHAR(255),
Size INT,
Weight DECIMAL,
Unit_price DECIMAL,
PRIMARY KEY (Item_id)
);

CREATE TABLE Hold_item
(
Store_id INT NOT NULL,
Item_id INT NOT NULL,
Quantity_held INT,
PRIMARY KEY (Store_id, Item_id),
FOREIGN KEY (Store_id) REFERENCES Store(Store_id),
FOREIGN KEY (Item_id) REFERENCES Item(Item_id)
);

CREATE TABLE Company
(
Customer_id INT NOT NULL,
PRIMARY KEY (Customer_id)
);

CREATE TABLE Government
(
Customer_id INT NOT NULL,
PRIMARY KEY (Customer_id)
);

CREATE TABLE Person
(
Customer_id INT NOT NULL,
PRIMARY KEY (Customer_id)
);

CREATE TABLE Customer
(
Customer_id INT NOT NULL,
Customer_name VARCHAR(255),
First_order_date DATE,
City_id INT,
PRIMARY KEY (Customer_id),
FOREIGN KEY (City_id) REFERENCES City(City_id)
);

CREATE TABLE Orders
(
Order_id INT NOT NULL,
Order_date DATE,
Customer_id INT,
PRIMARY KEY (Order_id),
FOREIGN KEY (Customer_id) REFERENCES Customer(Customer_id)
);

CREATE TABLE Contain_item
(
Item_id INT NOT NULL,
Order_id INT NOT NULL,
Quantity_ordered INT,
Ordered_price DECIMAL,
PRIMARY KEY (Item_id, Order_id),
FOREIGN KEY (Item_id) REFERENCES Item(Item_id),
FOREIGN KEY (Order_id) REFERENCES Orders(Order_id)
);

CREATE TABLE Employee
(
Customer_id INT NOT NULL,
Employee_discount_rate DECIMAL,
PRIMARY KEY (Customer_id),
FOREIGN KEY (Customer_id) REFERENCES Customer(Customer_id)
);

CREATE TABLE Regular
(
Customer_id INT NOT NULL,
Customer_address VARCHAR(255),
PRIMARY KEY (Customer_id),
FOREIGN KEY (Customer_id) REFERENCES Customer(Customer_id)
);";

echo $conn -> query($sql) . $conn->error . '
';
?>