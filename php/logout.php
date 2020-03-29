<?php

require_once('utilities.php');
destroy_session_and_data();
echo "logout successfull. session destroyed";
die("<p><a href=../html/index.html>Continue</a></p>");
//header('Location: http://' . $_SERVER['HTTP_HOST'] . '/~S4502434/html/index.html');
