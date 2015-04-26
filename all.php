<?php 
require('keys.php');
$conn = new mysqli($db_info['host'], $db_info['user'], $db_info['pass'], $db_info['database']);
if(mysqli_connect_errno()) {
    die("MySQL connection failed: ". mysqli_connect_error());
}

$table = $_GET['table'];
$sql = "SELECT * FROM {$table}";
echo $sql;

function get_array($result) {
    $data = array();
    if ($result){
        while ($row = $result -> fetch_assoc()){
            array_push($data, $row);
        }
    }
    return $data;
}

$arr = get_array($conn -> query($sql));
?><table><tbody>
<tr>
<?php 
$keys = array_keys($arr[0]);
foreach ($keys as $key) {
    echo '<th>' . $key . '</th>';
}
?>
</tr>
<?php 
foreach ($arr as $a) {
    ?> <tr> <?php
    foreach ($keys as $key) {
        echo '<td>' . $a[$key] . '</td>';
    }
    ?> </tr> <?php 
}
?>
</tbody></table>