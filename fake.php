<?php 

require('keys.php');
$conn = new mysqli($db_info['host'], $db_info['user'], $db_info['pass'], $db_info['database']);
if(mysqli_connect_errno()) {
    die("MySQL connection failed: ". mysqli_connect_error());
}


require_once('/var/www/html/Faker/src/autoload.php');

$faker = Faker\Factory::create();

$states = array();
// fake state
for ($i=0; $i < 30; $i++) { 
    $name = $faker->state;
    $address = $faker->address;
    echo $name . '   ' . $address . '
';
    $states[$i] = $name;
    $sql = "INSERT INTO State 
        VALUES ('{$name}', '{$address}')";
    echo $conn -> query($sql) . '
';
}

// fake city
for ($i=0; $i < 200; $i++) { 
    $name = $faker->city;
    $state = $states[rand(0, 29)];
    echo $name . '
';
    $sql = "INSERT INTO City 
        VALUES ('{$i}', '{$name}', 
            '{$state}')";
    echo $conn -> query($sql) . '
';
}

// generate customer
for ($i=0; $i < 600; $i++) {
    $type = 'Person';
    $name = $faker->name;
    if ($i >= 500) {
        $name = $faker->catchPhrase;
        $type = 'Company';
    }
    if ($i >= 550) {
        $name = $faker->country;
        $type = 'Government';
    }
    $city = rand(0, 199);
    $fod = $faker->date($format = 'Y-m-d', $max = 'now');
    echo $name . '
';
    $sql = "INSERT INTO Customer 
        VALUES ('{$i}', '{$name}', 
            '{$fod}', {$city})";
    echo $conn -> query($sql) . $conn->error . '
';
    $sql = "INSERT INTO {$type} 
        VALUES ('{$i}')";
    echo $conn -> query($sql) . $conn->error . '
';
    if ($i < 500 && $i % 5 == 0 ) {
        // employee
        $discount = rand(0, 100);
        $sql = "INSERT INTO Employee 
        VALUES ('{$i}', {$discount})";
        echo $conn -> query($sql) . $conn->error . '
';
    } else {
        // regular
        $address = $faker->address;
        $sql = "INSERT INTO Regular 
        VALUES ('{$i}', '{$address}')";
        echo $conn -> query($sql) . $conn->error . '
';
    }
}

// generate items
for ($i=0; $i < 500; $i++) { 
    $description = $faker->sentence($nbWords = 4);
    $size = rand(5, 199);
    $weight = rand(5, 1999) / 10.0;
    $price = rand(5, 19999) / 100.0;
    $sql = "INSERT INTO Item 
        VALUES ('{$i}', '{$description}', {$size}, {$weight},
            {$price})";
    echo $conn -> query($sql) . $conn->error . '
';
}

// generate stores
for ($i=0; $i < 100; $i++) { 
    $phone = $faker->postcode . $faker->buildingNumber;
    $city = rand(0, 199);
    $sql = "INSERT INTO Store 
        VALUES ('{$i}', {$phone}, {$city})";
    echo $conn -> query($sql) . $conn->error . '
';
}

// generate holds
for ($i=0; $i < 20000; $i++) { 
    $store = rand(0, 99);
    $item = rand(0, 499);
    $quan = rand(10, 2000);
    $sql = "INSERT INTO Hold_item 
        VALUES ('{$store}', {$item}, {$quan})";
    echo $conn -> query($sql) . $conn->error . '
';
}

// insert order
for ($i=0; $i < 200; $i++) { 
    $customer = rand(0, 600);
    $date = $faker->date($format = 'Y-m-d', $max = 'now');
    $sql = "INSERT INTO Orders 
        VALUES ('{$i}', '{$date}', {$customer})";
    echo $conn -> query($sql) . $conn->error . '
';
}

// insert contain
for ($i=0; $i < 200; $i++) { 
    for ($j=0; $j < 5; $j++) { 
        $item = rand(0, 499);
        $order = rand(0, 199);
        $num = rand(1, 400);
        $price = rand(5, 30000);
        $sql = "INSERT INTO Contain_item 
        VALUES ('{$item}', {$order}, {$num}, {$price})";
    echo $conn -> query($sql) . $conn->error . '
';
    }
}


?>