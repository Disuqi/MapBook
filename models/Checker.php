<?php
require_once('Repo.php');
require_once('UsersRepo.php');
class Checker{

    protected $repo;
    public function __construct(){
        $this->repo = new UsersRepo();
    }

    /**
     * @param array-key $dbRow all the user input usually it's simply $_POST
     * @return string code depending on the problem, and OK if there is no problem
    */
    //checks all the things inputted by the user in the sign up page
    public function checkSingUp($dbRow){
        $repo = new UsersRepo();
        //checking the username
        $usernameCheck = $this->checkUsername($dbRow['username']);
        if($usernameCheck != null){
            return $usernameCheck;
        }
        //checking the email
        $emailCheck = $this->checkEmail($dbRow['email']);
        if($emailCheck != null){
            return $emailCheck;
        }
        //checking the firstname &last name
        $nameCheck = $this->checkName(trim($dbRow['firstName'] . " " . $dbRow['lastName']));
        if($nameCheck != null){
            return $nameCheck;
        }
        //checkingPassword
        $passwordCheck = $this->checkPassword($dbRow['password'], $dbRow['password2']);
        if($passwordCheck != null){
            return $passwordCheck;
        }
        return "OK"; //everything is ok
    }

    /**
     * @param string $username to be checked
     * @return string code depending on the problem, and null if there is no problem
     */
    public function checkUsername($username){
        //checks for illegal characters
        if(!preg_match("'^[A-Za-z]+([A-Za-z]*[!#$%^&*_\-?]*[0-9]*)*$'", $username)){
            return "IU";//invalid username
        }else if($this->repo->attributeExists("username", $username)){//checks if the username exists
            return "EU"; //Existing Username
        }else{
            return null;
        }
    }

    /**
     * @param string $email to be checked
     * @return string code depending on the problem, and null if there is no problem
     */
    public function checkEmail($email){
        //checks for illegal characters
        if(!preg_match("'^.+@.+..*$'", $email)){
            return "IE";//invalid email
        }
        //checks if the email exists
        else if($this->repo->attributeExists("email", $email)){
            return "EE"; //Existing email
        }else{
            return null;
        }
    }

    /**
     * @param string $name to be checked
     * @return string code depending on the problem, and null if there is no problem
     *
     */
    public function checkName($name){
        //separated into first name and last name
        $name = explode(" ", $name);
        //checks that only first name and last name where inputted
        if(count($name) != 2){
            return "IN"; //Invalid name
            //checks for illegal characters in the first name
        }else if (!preg_match("'^[A-Za-z]+$'", $name[0])){
            return "IF";
            //checks for illegal characters in the last name
        }else if (!preg_match("'^[A-Za-z]+$'", $name[1])){
            return "IL";
        }else{
            return null;
        }
    }

    /**
     * @param string $password1 first inputted password
     * @param string $password2 second inputted password
     * @param string $username username of the user that is inputting the password, this is used when an existing user is trying to change password
     * @param string $oldPassword old password of existing user, default = null
     * @return string code depending on the problem, and null if there is no problem
     */
    public function checkPassword($password1, $password2, $username = null, $oldPassword = null){
        if($password1 == ""){
            return "NP"; //No password
        }
        else if(strlen($password1) < 6){
            return "SP"; //short password
        }
        else if(preg_match("'^([A-Za-z]*[0-9]*)+$'", $password1)){
            return "EP"; //easy password
        }
        else if($password1 != $password2){
            return "NM"; //not matching passwords
        }else{
            if($username != null){
                if($this->repo->signIn($username, $oldPassword) != "T"){
                    return "WP"; //wrong old password
                }
                else if($this->repo->signIn($username, $password1) == "T"){
                    return "OP"; //old password is the same as the new one
                }
            }else {
                return null;
            }
        }
    }

}