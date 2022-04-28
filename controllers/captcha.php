<?php

//captcha for sign up page to prevent spam
if(isset($_SESSION['numToPress'])){
    $_SESSION['numToPress'] = null;
    unset($_SESSION['numToPress']);
}
$amountOfNum = rand(2, 5);
$view->ranNumArray = [];
for($i = 0; $i<$amountOfNum; $i++){
    $view->ranNumArray[$i] = rand(0, 100);
}
$view->numToPress = array_rand($view->ranNumArray) + 1;
$_SESSION['numToPress'] = $view->ranNumArray[$view->numToPress - 1];