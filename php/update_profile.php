<?php

require_once('../php/utilities.php');
session_start();
session_check();
/*
// -------------------------------------------------0  check POST has data ----------------------------------------------------------
if (!isset($_POST['email'], $_POST['firstname'], $_POST['lastname'])) {
    die('1Please complete the  form!');
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '/~S4502434/php/show_profile.php');
    exit();
}
// Make sure the submitted values are not empty. One or more values are empty.
if (empty($_POST['email'] ||  $_POST['lastname'] || $_POST['firstname'])) {
    die('2Please complete the  form');
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '/~S4502434/php/show_profile.php');
    exit();
}
*/

// -------------------------------------------------1  connect to DBMS----------------------------------------------------------

// require_once is better than include statement, as it will generate a fatal error if the file is not found.
// Also, using require_once instead of require means that the file will be read in only
// when it has not previously been included, which prevents wasteful duplicate disk
// accesses.
require_once('../db/mysql_credentials.php');
$con = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
echo "Connected to db successfully <br>";



// -------------------------------------------------2 Build Query----------------------------------------------------------
// Get value from $_SESSION
$email = $_SESSION['email'];

// Get values from $_POST, but do it IN A SECURE WAY
$first_name = sanitize($con, $_POST['firstname']);
$last_name = sanitize($con, $_POST['lastname']);
$note = sanitize($con, $_POST['note']);
$website = sanitize($con, $_POST['website']);



function update_user($email, $first_name, $last_name, $note, $website, $db_connection)
{
    $stmt = $db_connection->prepare('UPDATE users SET  firstname = ?, lastname = ?, note = ?, website = ? WHERE email = ?');
    $stmt->bind_param('sssss', $first_name, $last_name, $note, $website, $email);
    $stmt->execute();
    printf("%d Row affected.<br>", $stmt->affected_rows);
    if ($stmt->affected_rows) {
        $stmt->close();
        return true;
    } else {
        $stmt->close();
        return false;
    }
}

// -------------------------------------------------3 Execute Query----------------------------------------------------------
$successful = update_user($email, $first_name, $last_name, $note, $website, $con);

if ($successful) {
    // Success message
    // $_SESSION['email'] = $email;
    $_SESSION['firstname'] = $first_name;
    $_SESSION['lastname'] = $last_name;
    header("Location: show_profile.php");
    // exit();
} else {
    // Error message
    echo "There was an error in the update process.";
}
