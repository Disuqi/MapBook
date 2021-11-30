<?php
if(session_id() == '') {
    session_start();
}
$_SESSION['loggedIn'] = false;
$_SESSION['username'] = null;
$_SESSION['profileImage'] = null;
header("Location: http://" . $_SERVER['HTTP_HOST'] . "/controllers/index.php");
exit;