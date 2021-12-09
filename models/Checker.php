<?php
require_once('Repo.php');
require_once('UsersRepo.php');
class Checker{

    public function checkSingUp($dbRow){
        $repo = new UsersRepo();
        //checking the username
        if(!preg_match("'^[A-Za-z]+([A-Za-z]*[!#$%^&*_\-?]*[0-9]*)*$'", $dbRow['username'])){
            return "IU";//invalid username
        }
        else if($repo->attributeExists("username", $dbRow['username'])){
                return "EU"; //Existing Username
        }
        //checking the email
        else if(!preg_match("'^.+@.+..*$'", $dbRow['email'])){
            return "IE";//invalid email
        }
        else if($repo->attributeExists("email", $dbRow['email'])){
                return "EE"; //Existing email
        }
        //checking the firstname &last name
        else if(!preg_match("'^[A-Za-z]+$'", $dbRow['firstName'])){
            return "IF"; //invalid firstname
        }else if (!preg_match("'^[A-Za-z]+$'", $dbRow['lastName'])){
            return "IL"; //invalid lastname
        }
        //checkingPassword
        else if($dbRow['password'] == ""){
            return "NP"; //No password
        }
        else if(strlen($dbRow['password']) < 6){
            return "SP"; //short password
        }
        else if(preg_match("'^([A-Za-z]*[0-9]*)+$'", $dbRow['password'])){
            return "EP"; //easy password
        }
        else if($dbRow['password'] != $dbRow['password2']){
            return "NM"; //not matching passwords
        }
        else{
            return "OK"; //everything is ok
        }
    }

}