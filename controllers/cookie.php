<?php

//checking if cookie is allowed or not
if(isset($_GET["cookies"])){
    if($_GET["cookies"] == "true"){
        setcookie("allowCookies", "true", time() +(86400 * 365), "/");
        $_COOKIE["allowCookies"] = "true";
    }else if($_GET["cookies"] == "false"){
        $_SESSION["doNotAllowCookies"] = "true";
    }else if($_GET["cookies"] == "reset"){
        setcookie("allowCookies", "", -1, "/");
        unset($_COOKIE["allowCookies"]);
        unset($_SESSION["doNotAllowCookies"]);
    }
}

//Cookies button or auto login
if(isset($_COOKIE['username'])){
    $signer->signIn($_COOKIE['username']);
}