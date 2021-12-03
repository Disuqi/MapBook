<?php
if(session_id() == ''){
    session_start();
}
$view = new stdClass();
$view->pageTitle = "Home";
require_once('cookie.php');
if(!isset($_SESSION["doNotAllowCookies"]) && !isset($_COOKIE["allowCookies"])) {
    echo '
        <div class="col-12 text-center copyright">
            <button id="cookieBtn" data-bs-toggle="modal" data-bs-target="#cookiesModal"></button>
        </div>
';
}
require_once("../views/index.phtml");

