<?php
Class Signer{
    protected $imageHandler, $userRepo, $imageRepo;

    //starting session if needed, and initializing fields
    public function __construct(){
        if(session_id() == ''){
            session_start();
        }
        require_once("UsersRepo.php");
        $this->userRepo = new UsersRepo();
        require_once("Images.php");
        $this->imageHandler = new Images();
        require_once("ImagesRepo.php");
        $this->imageRepo = new ImagesRepo();

    }

    /**
     * @param string $un username of the user that wants to sign in
    */
    public function signIn($un){
        //setting the session username and logged in to the appropriate values to let the website know that a user is logged in
        $_SESSION['loggedIn'] = true;
        $_SESSION['username'] = $un;
        $_SESSION['loggedUser'] = $this->userRepo->getObject($un);
        //getting the profile image
        $profileImage = $this->imageRepo->getProfileImage($un);
        if($profileImage != ""){
            $_SESSION['profileImage'] = $profileImage->getImagePath();
        }else{
            $_SESSION['profileImage'] = "../images/noProfilePic.svg";
        }
    }
    public function signOut(){
        //setting everything to null and sending the user back to the home page
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

    /**
     * @param array-key $dbRow contains all the information inputted by the user
    */
    public function signUp($dbRow){
        $un = $dbRow['username']; //un is an abbreviation for username
        //adding the user to the database table users
        $this->userRepo->addObject(new UserDTO($dbRow));
        //if he uploaded an image then save it
        if($_FILES['image']['name'] != ""){
            //making a folder for the user and creating the file
            $profileImage = $this->imageHandler->addImage($un, 1);
            if(!$profileImage) {
                //error
                return "There is something wrong with your image";
            }else {
                //saving the file in the database table images
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $_SESSION['profileImage'] = "../images/" . $un . '/1.' . $ext;
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $this->imageRepo->addObject(["username" => $un, "ext" => $ext]);
                $this->imageRepo->setProfileImage(["id"=>1, "username" => $un]);
            }
        }else{
            //no image uploaded then set the image to the stock no image
            $_SESSION['profileImage'] = "../images/noProfilePic.svg";
        }
        //sign him in and send back to the home page
        $this->signIn($un);
        $this->index();
    }

    //send back to home page
    private function index(){
        header("Location: http://" . $_SERVER['HTTP_HOST'] . "/controllers/index.php");
        exit;
    }


}