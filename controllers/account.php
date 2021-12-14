<?php
if(session_id() == ''){
    session_start();
}
$view = new stdClass();
$view->pageTitle = 'Account';

//if logged in then show account settings
if(isset($_SESSION['loggedIn'])){
    require_once '../models/UsersRepo.php';
    require_once '../models/ImagesRepo.php';

    $usersRepo = new UsersRepo();
    $imageRepo = new ImagesRepo();
    $un = $_SESSION['username'];
    $view->validation = "<span class='link-secondary disabled mt-1'>Account Settings <i class='bi bi-gear-wide-connected'></i></span>";
    //chekcking if the user is trying to change something and making relevant checks and changes
    if(isset($_POST['Submit'])){
        require_once '../models/Checker.php';
        require_once '../models/Images.php';
        $checker = new Checker();
        $imageHandler = new Images();
        $validation = "OK";
        $attribute = "";
        $value = "";
        $view->validation = "<span class='btn btn-outline-danger disabled mt-1'>";
        if(isset($_POST['Username'])){
            $attribute = "username";
            $value = $_POST['Username'];
            $validation = $checker->checkUsername($value);
        }
        else if(isset($_POST['Email'])){
            $attribute = "email";
            $value = $_POST['Email'];
            $validation = $checker->checkEmail($value);
        }
        else if(isset($_POST['Name'])){
            $attribute = "name";
            $value = $_POST['Name'];
            $validation = $checker->checkName($value);
        }else if(isset($_POST['oldPassword'])){
            $attribute = "password";
            $value = $_POST["newPassword"];
            $validation = $checker->checkPassword($_POST['newPassword'], $_POST['newPassword2'], $un, $_POST['oldPassword']);
        } else if(isset($_FILES["image"]) && $_FILES['image']['name'] != ""){
            $validation = "PI";
            $profileImage = $imageHandler->addImage($un, 1);
            if(!$profileImage) {
                $validation = "IF";//invalid file
            }else {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $_SESSION['profileImage'] = "../images/" . $un . '/1.' . $ext;
                if($imageRepo->getProfileImage($un) == null) {
                    $imageRepo->addObject(["username" => $un, "ext" => $ext]);
                    $imageRepo->setProfileImage(["id"=>1, "username" => $un]);
                }
            }

        } else if($_POST['Submit'] == 'delete'){
            $validation = "PI";
            $pk = ['id'=> 1, 'username' => $un];
            $ext = $imageRepo->getAttribute('ext', $pk);
            $imageHandler->deleteImage($un, 1, $ext);
            $imageRepo->deleteObject($pk);
            $_SESSION['profileImage'] = '../images/noProfilePic.svg';
        }
        //checking if there was something wrong with the input and setting the message sent to the user
        switch($validation){
            case "IU":
                $view->validation .= "Invalid Username <i class='bi bi-x-circle-fill'></i></span>";
                break;
            case "EU":
                $view->validation .= "The Username is already in use <i class='bi bi-x-circle-fill'></i></span>";
                break;
            case "IE":
                $view->validation .= "Invalid Email <i class='bi bi-x-circle-fill'></i></span>";
                break;
            case "EE":
                $view->validation .= "The email is already in use <i class='bi bi-x-circle-fill'></i></span>";
                break;
            case "IN":
                $view->validation .= "Invalid Name <i class='bi bi-x-circle-fill'></i></span>";
                break;
            case "IF":
                $view->validation .= "Invalid First Name <i class='bi bi-x-circle-fill'></i></span>";
                break;
            case "IL":
                $view->validation .= "Invalid Last Name <i class='bi bi-x-circle-fill'></i></span>";
                break;
            case "WP":
                $view->validation .= "Wrong Password <i class='bi bi-x-circle-fill'></i></p>";
                break;
            case "OP":
                $view->validation .= "Old and New Password are the same <i class='bi bi-x-circle-fill'></i></span>";
                break;
            case "NP":
                $view->validation .= "No password inserted <i class='bi bi-x-circle-fill'></i></span>";
                break;
            case "SP":
                $view->validation .= "The password is too short <i class='bi bi-x-circle-fill'></i></span>";
                break;
            case "EP":
                $view->validation .= "The password is too simple<br>Try adding special characters <i class='bi bi-x-circle-fill'></i></span>";
                break;
            case "NM":
                $view->validation .= "The passwords do not match <i class='bi bi-x-circle-fill'></i></span>";
                break;
            case "IF":
                $view->validation .= "There is something wrong with your images <i class='bi bi-x-circle-fill'></i></span>";
                break;
            case "PI":
                $view->validation = "<span class='btn btn-outline-success disabled mt-1'>Profile Image Changed <i class='bi bi-check-circle-fill'></i></span>";
                break;
            default:
                $usersRepo->updateAttribute($attribute, $value, $un);
                if($attribute == 'username'){
                    $imageHandler->renameDirectory($un, $value);
                    $_SESSION['username'] = $value;
                    $profileImage = $imageRepo->getProfileImage($value);
                    if($profileImage != null){
                        $profileImage = $profileImage->getImagePath();
                    }else{
                        $profileImage = "../images/noProfilePic.svg";
                    }
                    $_SESSION['profileImage'] = $profileImage;
                    $un = $value;
                }
                $view->validation = "<span class='btn btn-outline-success disabled mt-1'>".ucfirst($attribute). " Changed <i class='bi bi-check-circle-fill'></i></span>";
                break;
        }
    }
    //Making the view, basically making the html code, that changes according to the buttons pressed
    $user = $usersRepo->getObject($un);
    $profileImage = $imageRepo->getProfileImage($un);
    if($profileImage != null){
        $view->profileImage = $profileImage->getImagePath();
    }else{
        $view->profileImage = "../images/noProfilePic.svg";
    }
    $view->information = "";
    $infoList = ['Username' => $user->getUsername(), 'Name' => $user->getFullName(), 'Email' => $user->getEmail(), 'Location' => $user->getPosition()];
    foreach($infoList as $key=>$value){
        $view->information .= '<div class="d-flex align-items-center justify-content-between mt-2">';

        if($key == 'Username'){
            if(isset($_GET['change']) && $_GET['change'] == $key){
                $view->information .= '                
                <form class="order-0" id="editValue" action = '. $_SERVER["PHP_SELF"] .' method = "post" >
                    <div class="input-group mb-0">
                        <span class="input-group-text" id="basic-addon1">@</span>
                        <input type="text" name="Username" class="form-control" placeholder="New Username" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                </form>
                <div class="order-2">
                    <button class="btn btn-primary" type="submit" form="editValue" name="Submit">Change <i class="bi bi-check-lg"></i></button>
                    <a class="btn btn-danger" type="submit" href="account.php"><i class="bi bi-x-lg"></i></a>
                </div>';
            }else {
                $view->information .= '<h1 class="card-title order-0">@' . $value . '</h1>
                    <a type="button" class="btn btn-outline-secondary order-2" href="account.php?change=Username"><i class="bi bi-pencil-fill"></i></a>';
            }
        }else{
            $view->information .= '<div class="order-1">
                <h3 class="card-subtitle text-muted">'.$key.'</h3>';
            if(isset($_GET['change']) && $_GET['change'] == $key){
                $view->information .= '
                <form id="editValue" action = '. $_SERVER["PHP_SELF"] .' method = "post">
                    <input type="text" name="'.$key.'" class="form-control" placeholder="New '.$key.'" aria-label="Username" aria-describedby="basic-addon1">
                </form>
                </div>
                <div class="order-2">
                    <button class="btn btn-primary" type="submit" form="editValue" name="Submit">Change <i class="bi bi-check-lg"></i></button>
                    <a class="btn btn-danger" type="submit" href="account.php"><i class="bi bi-x-lg"></i></a>
                </div>
                ';
            }else{
                $view->information .= '
                        <p class="card-text">'.$value.'</p>
                    </div>
                    <a type="button" class="btn btn-outline-secondary order-2" href="account.php?change='.$key.'"><i class="bi bi-pencil-fill"></i></a>';
            }
        }
        $view->information .= '</div>';
    }
}else{//if not logged in then send to the sign in page
    header("Location: http://" . $_SERVER['HTTP_HOST'] . "/controllers/signIn.php");
    exit;
}

require_once '../views/account.phtml';