<?php
require_once('Repo.php');
require_once('UsersRepo.php');
class Checker{

    protected $repo;
    public function __construct(){
        $this->repo = new UsersRepo();
    }

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

    public function checkUsername($username){
        if(!preg_match("'^[A-Za-z]+([A-Za-z]*[!#$%^&*_\-?]*[0-9]*)*$'", $username)){
            return "IU";//invalid username
        }else if($this->repo->attributeExists("username", $username)){
            return "EU"; //Existing Username
        }else{
            return null;
        }
    }

    public function checkEmail($email){
        if(!preg_match("'^.+@.+..*$'", $email)){
            return "IE";//invalid email
        }
        else if($this->repo->attributeExists("email", $email)){
            return "EE"; //Existing email
        }else{
            return null;
        }
    }
    public function checkName($name){
        $name = explode(" ", $name);
        if(count($name) != 2){
            return "IN"; //Invalid name
        }else if (!preg_match("'^[A-Za-z]+$'", $name[0])){
            return "IF";
        }else if (!preg_match("'^[A-Za-z]+$'", $name[1])){
            return "IL";
        }else{
            return null;
        }
    }

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