<?php
//include
require_once('utilities.php');
// -------------------------------------------------1  connect to DBMS----------------------------------------------------------
require_once('../db/mysql_credentials.php');
$con = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$stmt = $con->prepare('SELECT current, goal  FROM crowdfunding WHERE id = 1');
$stmt->execute();
$result = $stmt->get_result();
$con->close();
$row = $result->fetch_array(MYSQLI_ASSOC);

$fraction = ($row['current'] / $row['goal']) * 100;
$width = 'width: ' . $fraction . '%';
$number = $fraction;
$return_arr = array(
    "width" => $width,
    "fraction" => $fraction . "%",
);

echo json_encode($return_arr);
