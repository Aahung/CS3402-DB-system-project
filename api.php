<?php
header('Content-Type: application/json');
$arr = array();
for ($i=0; $i < 30; $i++) 
    array_push($arr, array('this is placeholder' => rand(), 'and is a random number' => rand()));
echo json_encode($arr);
?>