<?php

require_once('utilities.php');

// -------------------------------------------------0  check there is input data ----------------------------------------------------------
if (!isset($_POST['email'], $_POST['firstname'], $_POST['confirm'], $_POST['lastname'], $_POST['pass'])) {
    // die('Please complete the registration form!');
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '/~S4502434/html/login.html');
    exit();
}
// Make sure the submitted values are not empty. One or more values are empty.
if (empty($_POST['email'] ||  $_POST['confirm'] || $_POST['lastname'] || $_POST['firstname'] || $_POST['pass'])) {
    // die('Please complete the registration form');
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '/~S4502434/html/login.html');
    exit();
}



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



// -------------------------------------------------2 Build the query----------------------------------------------------------
// Get values from $_POST, but do it IN A SECURE WAY
$first_name = sanitize($con, $_POST['firstname']);
$last_name = sanitize($con, $_POST['lastname']);
$email = sanitize($con, $_POST['email']);
$password = hash("sha256", salt(sanitize($con, $_POST['pass'])));
$password_confirm = hash("sha256", salt(sanitize($con, $_POST['confirm'])));

function insert_user($email, $first_name, $last_name, $password, $password_confirm, $db_connection)
{
    // check if passwords match
    if (strcmp($password, $password_confirm) == 0) {
        echo 'Passwords match! <br>';
        // registration logic here
        $stmt = $db_connection->prepare('INSERT INTO users (id, firstname, lastname, email, password) VALUES(null,?,?,?,?)');
        $stmt->bind_param('ssss', $first_name, $last_name, $email, $password);
        $stmt->execute();
        printf("%d Row inserted.<br>", $stmt->affected_rows);
        $stmt->close();
        return true;
    } else {
        echo 'Strings do not match. <br>';

        return false;
    }
}


// -------------------------------------------------3 Perform the query----------------------------------------------------------
// Get user from login
$successful = insert_user($email, $first_name, $last_name, $password, $password_confirm, $con);


// -------------------------------------------------4 Retrieve the results and output them to a web page-------------------------
if ($successful) {
    // Success message
    echo "$email registered successfully!";
    die("<p><a href=../html/login.html>Continue</a></p>");
    // header('Location: http://' . $_SERVER['HTTP_HOST'] . '/~S4502434/html/index.html');

} else {
    // Error message
    echo "There was an error in the registration process.";
    // header('Location: http://' . $_SERVER['HTTP_HOST'] . '/~S4502434/html/index.html');
    die("<p><a href=../html/login.html>Continue</a></p>");
}
