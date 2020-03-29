<?php
//include
session_start();
require_once('../php/utilities.php');

/* Once you had authenticated a user and set up a session,
you could safely assume that the session variables were trustworthy, this isn’t exactly
the case. The reason is that it’s possible to use packet sniffing (sampling of data) to
discover session IDs passing across a network. The only truly
secure way of preventing these from being discovered is to implement Secure Sockets
Layer (SSL) and run HTTPS instead of HTTP web pages.*/


// -------------------------------------------------0  check there is input data ----------------------------------------------------------
if (!isset($_POST['email'], $_POST['pass'])) {
    // die('Please complete the registration form!');
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '/~S4502434/login.html');
    exit();
}
// Make sure the submitted values are not empty. One or more values are empty.
if (empty($_POST['email'] || $_POST['pass'])) {
    // die('Please complete the registration form');
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '/~S4502434/login.html');
    exit();
}

// -------------------------------------------------1  connect to DBMS----------------------------------------------------------
require_once('../db/mysql_credentials.php');
$con = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}



// -------------------------------------------------2 Build a query string------------------------------------------------------
$email = sanitize($con, $_POST['email']);
$pass = hash("sha256", salt(sanitize($con, $_POST['pass'])));

function login($email, $pass, $db_connection)
{
    $stmt = $db_connection->prepare('SELECT id, firstname, lastname, email, password FROM users WHERE password = ? AND email = ?');
    $stmt->bind_param('ss', $pass, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $stmt->close();
        return $result;
    } else {
        $stmt->close();
        return null;
    }
}



// -------------------------------------------------3 Perform the query----------------------------------------------------------
$user = login($email, $pass, $con);
$con->close();



// -------------------------------------------------4 Retrieve the results -------------------------
// Get user from login
if ($user) {
    // Welcome message
    $rows = $user->num_rows;
    $user->data_seek(0);
    $row = $user->fetch_array(MYSQLI_ASSOC);


    $_SESSION['id'] = $row['id'];
    $_SESSION['email'] = $row['email'];
    $_SESSION['password'] = $row['password'];
    $_SESSION['firstname'] = $row['firstname'];
    $_SESSION['lastname'] = $row['lastname'];
    // aggiunata per sicurezza
    $_SESSION['login'] = true;
    $_SESSION['check'] = hash('ripemd128', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
    /* When SSL is not a possibility, you can further authenticate users by storing their IP
    address along with their other details
    ho combianato queste altre due informazioni addizionali e le ho salvate assieme dopo un hash*/

    echo "Welcome !";

    echo $row['id'] . "<br>";
    echo $row['email'] . "<br>";

    echo $row['firstname'] . "<br>";
    echo $row['lastname'] . "<br>";

    // header('Location: http://' . $_SERVER['HTTP_HOST'] . '/~S4502434/php/show_profile.php');
    // exit;

    die("<p><a href=../php/show_profile.php>Continue</a></p>");
} else {
    // Error message
    echo "Wrong email or password";
    die("<p><a href=../html/login.html>Click here to log-in</a></p>");
}
