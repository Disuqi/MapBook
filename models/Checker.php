<?php
require_once('UsersRepo.php');
class Checker{

    public static function checkSingUp($profilePic, $username, $email, $firstName, $lastName, $password, $password2){
        if(!preg_match("'%\b(\'|\"|\`|@|\ )\b%i'", $username)){
            return "IU";//invalid username
        }
        else{
            $repo = new UsersRepo();
            if(count($repo->getUser($username)) > 0){
                return "UE"; //Username already exists
            }
        }
        if(!preg_match(".+@.+\..*", $email)){
            return "IE";//invalid email
        }
    }
    public static function checkUsername($username){
        if(!preg_match("'%\b(\'|\"|\`|@|\ )\b%i'", $username)){
            return true;
        }
        else{
            return false;
        }
    }
}