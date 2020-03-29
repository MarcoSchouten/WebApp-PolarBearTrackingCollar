<?php
session_start();

require_once('../php/utilities.php');


// -------------------------------------------------0  check there is input data ----------------------------------------------------------
if (!isset($_POST['email'], $_POST['nickname'], $_POST['amount'])) {
    // die('Please complete the registration form!');
    echo "data not recieved";
    // header('Location: http://' . $_SERVER['HTTP_HOST'] . '/~S4502434/html/login.html');
    // exit();
}
// Make sure the submitted values are not empty. One or more values are empty.
if (empty($_POST['email'] || $_POST['nickname'] || $_POST['amount'])) {
    // die('Please complete the registration form');
    // header('Location: http://' . $_SERVER['HTTP_HOST'] . '/~S4502434/html/login.html');
    echo "data not recieved";
    // exit();
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
$nickname = sanitize($con, $_POST['nickname']);
$amount = sanitize($con, $_POST['amount']);
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


function insert_donation($email, $nickname, $amount, $db_connection)
{
    if (isset($_POST['message'])) {
        $message = sanitize($db_connection, $_POST['message']);
        $stmt = $db_connection->prepare('INSERT INTO donation (id, userID, nickname, amount, message) VALUES(null,?,?,?,?)');
        $stmt->bind_param('ssss', $email, $nickname, $amount, $message);
        $stmt->execute();
        if ($stmt->affected_rows) {
            $stmt = $db_connection->prepare('UPDATE crowdfunding
                                        SET current = (SELECT SUM(amount) AS TotDonation FROM donation)
                                        WHERE id = 1;');
            $stmt->execute();
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    } else {
        $stmt = $db_connection->prepare('INSERT INTO donation (id, userID, nickname, amount, message) VALUES(null,?,?,?, null)');
        $stmt->bind_param('sss', $_SESSION['email'], $nickname, $amount);
        $stmt->execute();
        if ($stmt->affected_rows) {
            $stmt = $db_connection->prepare('UPDATE crowdfunding
                                        SET current = (SELECT SUM(amount) AS TotDonation FROM donation)
                                        WHERE id = 1;');
            $stmt->execute();
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }
}


// -------------------------------------------------3 Perform the query----------------------------------------------------------
// Get user from login
$user = login($email, $pass, $con);
if ($user) {
    $successful = insert_donation($email, $nickname, $amount, $con);
    if ($successful) {
        // Success message
        echo "Donation successfull!";
        header('Location: http://' . $_SERVER['HTTP_HOST'] . '/~S4502434/html/donate.html');
        exit();
    } else {
        // Error message
        echo "There was an error in the donation process process.";
        // header('Location: http://' . $_SERVER['HTTP_HOST'] . '/~S4502434/html/index.html');
        exit();
    }
} else {
    // Error message
    echo "Wrong email or password";
    die("<p><a href=../html/donate.html>Continue</a></p>");
}
