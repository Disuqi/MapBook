<?php
if(isset($_SESSION['numToPress'])){
    $_SESSION['numToPress'] = null;
    unset($_SESSION['numToPress']);
}
$amountOfNum = rand(2, 5);
$ranNumArray = [];
for($i = 0; $i<$amountOfNum; $i++){
    $ranNumArray[$i] = rand(0, 100);
}
$view->numToPress = array_rand($ranNumArray) + 1;
$_SESSION['numToPress'] = $ranNumArray[$view->numToPress - 1];
$view->captcha = '<div class="btn-group" role="group" aria-label="Basic radio toggle button group">';

for($i = 0; $i <count($ranNumArray); $i++){
    $view->captcha .= '  
      <input type="radio" class="btn-check" name="'.$ranNumArray[$i].'" id="btnradio'.$i.'" autocomplete="off">
      <label class="btn btn-outline-primary" for="btnradio'.$i.'">'.$ranNumArray[$i].'</label>';
}
$view->captcha .= '</div>';