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

//Cookies button or auto login
if(!isset($_SESSION["doNotAllowCookies"]) && !isset($_COOKIE["allowCookies"])) {
    echo '
        <div class="col-12 text-center copyright">
            <button id="cookieBtn" data-bs-toggle="modal" data-bs-target="#cookiesModal"></button>
        </div>
';
} else if(isset($_COOKIE['username'])){
    $signer = new Signer();
    $signer->signIn($_COOKIE['username']);
}