<?php

require_once('../php/utilities.php');
session_start();
session_check();

// -------------------------------------------------0  check POST has data ----------------------------------------------------------
if (!isset($_POST['oldpass'], $_POST['newpass'], $_POST['confirm'])) {
    die('1 Please complete the  form!');
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '/~S4502434/php/show_profile_password.php');
    exit();
}
// Make sure the submitted values are not empty. One or more values are empty.
if (empty($_POST['oldpass'] ||  $_POST['newpass'] || $_POST['confirm'])) {
    die('2 Please complete the  form');
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '/~S4502434/php/show_profile_password.php');
    exit();
}

// -------------------------------------------------1  connect to DBMS----------------------------------------------------------
require_once('../db/mysql_credentials.php');
$con = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// -------------------------------------------------2 Build Query----------------------------------------------------------
// Get values from $_POST, but do it IN A SECURE WAY
$old_pass = hash("sha256", salt(sanitize($con, $_POST['oldpass'])));
$new_pass = hash("sha256", salt(sanitize($con, $_POST['newpass'])));
$confirm = hash("sha256", salt(sanitize($con, $_POST['confirm'])));

function update_pass($old_pass, $new_pass, $confirm, $db_connection)
{

    if (strcmp($old_pass, $_SESSION['password']) == 0) {
        echo "la vecchia password corrisponde <br>";
        if (strcmp($new_pass, $confirm) == 0) {
            echo "la nuova pass matcha con la confirm <br>";
            $stmt = $db_connection->prepare('UPDATE users SET password = ? WHERE email = ?');
            $stmt->bind_param('ss', $new_pass, $_SESSION['email']);
            $stmt->execute();
            printf("%d Row updated.<br>", $stmt->affected_rows);
            if ($stmt->affected_rows) {
                $stmt->close();
                return true;
            } else {
                $stmt->close();
                return false;
            }
        } else {
            "la nuova password non matcha con la confirm. ha sbagliato a scrivere <br>";
            return false;
        }
    } else {
        echo "hai sbagliato ad inserire la vecchia password <br<";
        return false;
    }
}

// -------------------------------------------------3 Execute Query----------------------------------------------------------
$successful = update_pass($old_pass, $new_pass, $confirm, $con);

if ($successful) {
    // Success message
    $_SESSION['password'] = $new_pass;
    echo "password changed successfully <br>";
    die("<p><a href=./show_profile.php>Click here to show_profile</a></p>");
} else {
    // Error message
    echo "There was an error in the update process. <br>";
    die("<p><a href=./show_profile.php>Click here to show_profile-in</a></p>");
}
