<?php
if(isset($_GET["cookies"])){
    if($_GET["cookies"] == "true"){
        setcookie("allowCookies", "true", time() +(86400 * 365), "/");
        $_COOKIE["allowCookies"] = "true";
    }else if($_GET["cookies"] == "false"){
        $_SESSION["doNotAllowCookies"] = "true";
    }else if($_GET["cookies"] == "reset"){
        setcookie("allowCookies", null, -1, "/");
        unset($_COOKIE["allowCookies"]);
        unset($_SESSION["doNotAllowCookies"]);
    }
}
require_once("../views/template/cookie.phtml");