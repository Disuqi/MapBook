<?php
if(session_id() == ''){
    session_start();
}
$view = new stdClass();
$view->pageTitle = 'Home';
require_once("../views/index.phtml");

