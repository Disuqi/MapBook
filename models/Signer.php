<?php
Class Signer{
    protected $repo, $imageHandler, $userRepo, $imageRepo;
    public function __construct(){
        if(session_id() == ''){
            session_start();
        }
        require_once("Repo.php");
        require_once("UsersRepo.php");
        $this->userRepo = new UsersRepo();
        require_once("Images.php");
        $this->imageHandler = new Images();
        require_once("ImagesRepo.php");
        $this->imageRepo = new ImagesRepo();

    }
    public function signIn($un){
        $_SESSION['loggedIn'] = true;
        $_SESSION['username'] = $un;
        //getting the profile image
        $profileImage = $this->imageRepo->getProfileImage($un);
        if($profileImage != ""){
            $_SESSION['profileImage'] = $profileImage->getImagePath();
        }else{
            $_SESSION['profileImage'] = "../images/noProfilePic.svg";
        }
    }
    public function signOut(){
        $_SESSION['loggedIn'] = null;
        unset($_SESSION['loggedIn']);
        $_SESSION['username'] = null;
        unset($_SESSION['loggedIn']);
        $_SESSION['profileImage'] = null;
        unset($_SESSION['loggedIn']);
        if(isset($_COOKIE['username'])){
            setcookie('username', null, -1, '/');
            unset($_COOKIE['username']);
        }
        $this->index();
    }

    public function signUp($dbRow){
        $un = $dbRow['username']; //un is an abbreviation for username
        $profileImage = $this->imageHandler->addImage($un, 1);
        if($_FILES['image']['name'] != ""){
            if(!$profileImage) {
                return "There is something wrong with your image";
            }else {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $_SESSION['profileImage'] = "../images/" . $un . '/1.' . $ext;
            }
        }else{
            $_SESSION['profileImage'] = "../images/noProfilePic.svg";
        }
        $this->userRepo->addObject(new UserDTO($dbRow));
        if($_FILES['image']['name'] != ""){
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $this->imageRepo->addObject(["username" => $un, "ext" => $ext]);
            $this->imageRepo->setProfileImage(["id"=>1, "username" => $un]);
        }
        $this->signIn($un);
        $this->index();
    }

    private function index(){
        header("Location: http://" . $_SERVER['HTTP_HOST'] . "/controllers/index.php");
        exit;
    }


}