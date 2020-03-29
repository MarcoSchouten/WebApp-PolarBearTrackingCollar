<?php



// ------------------------------------------------sanitizzazione input -----------------------------------------------------------
// Functions for preventing both SQL and XSS injection attacks

function sanitize($conn, $string)
{
    // stringa random per il salting", prima di fare l'hash aggiungo questa stringa base
    // Given just the database, and without access to your PHP code, it should now be next
    // to impossible to work out the stored passwords.
    return htmlentities(mysql_fix_string($conn, $string));
}

function mysql_fix_string($conn, $string)
{
    // mysql_fix_string will remove any magic quotes
    // added to a user-inputted string and then properly sanitize it for you. 

    if (get_magic_quotes_gpc()) $string = stripslashes($string);
    return $conn->real_escape_string($string);
    /*get_magic_quotes_gpc function returns TRUE if magic quotes are active. In that
    case, any slashes that have been added to a string have to be removed, or the
    real_escape_string method could end up double-escaping some characters, creating
    corrupted strings. .*/
}

function salt($string)
{
    $str1 = "&%64H";
    $str2 = "c&%£+aF";
    return  $str1 . $string . $str2;
}




// -----------------------------------------------------------------------------------------------------------
// -----------------------------------------------------------------------------------------------------------
// ------------------------------------------------sessioni-----------------------------------------------------------
/*you can further authenticate users by storing their IP
address along with their other details*/

/*user agent string (a string that developers put in their browsers to identify them by
type and version), which might also distinguish users due to the wide variety of
browser types, versions, and computer platform*/

/*Of course, users on the same proxy server, or sharing the
same IP address on a home or business network, will have the same IP address.
Again, if this is a problem we need to use SSL.*/

// OLD SESSION CHECK
function session_check()
{
    regererate_session_id();

    if (
        isset($_SESSION['login'])
        && $_SESSION['check'] == hash('ripemd128', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'])
    ) {
        // echo "Success session check.<br>";
    } else {
        // echo "failed session check";
        destroy_session_and_data();
        header('Location: http://' . $_SERVER['HTTP_HOST'] . '/~S4502434/html/login.html');
        exit();
    }
}


function destroy_session_and_data()
{
    $_SESSION = array();
    setcookie(session_name(), '', time() - 2592000, '/');
    // session_destroy();
}


/*
Session fixation
happens when a malicious user tries to present a session ID to the
server rather than letting the server create one. It can happen when a user takes
advantage of the ability to pass a session ID in the Get part of a URL, like this:
file.php?PHPSESSID=1234
file.php?PHPSESSID=5678
A malicious attacker could try to distribute
these types of URLs to unsuspecting users, and if any of them followed these
links, the attacker would be able to come back and take over any sessions that had not
been deleted or expired because he knows the session_ID!
if we regenerate that id, the attack is defused.

HOW:
check for a special session variable that you arbitrarily invent. If it doesn’t
exist, you know that this is a new session, so you simply change the session ID and set
the special session variable to note the change.
*/
function regererate_session_id()
{
    if (!isset($_SESSION['initiated'])) {
        session_regenerate_id();
        $_SESSION['initiated'] = 1;
    }
}




function set_sessions_preferences()
{
    ini_set('session.save_path', 'C:\xampp\my_session_folder');
    ini_set('session.gc_maxlifetime', 60 * 60);
}



function connect_to_database()
{
    require_once('../db/mysql_credentials.php');
    $con = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }
}
