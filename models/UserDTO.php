<?php
class UserDTO{

    protected $_username, $_firstName, $_lastName, $_email, $_password;

    public function __construct($dbRow){
        //initiating all fields
        $this->_username = $dbRow['username'];
        $this->_firstName = $dbRow['firstName'];
        $this->_lastName = $dbRow['lastName'];
        $this->_email = $dbRow['email'];
        $this->_password = $dbRow['password'];
    }
    //getter methods
    public function getUsername(){
        return $this->_username;
    }
    public function getFirstName(){
        return $this->_firstName;
    }
    public function getLastName(){
        return $this->_lastName;
    }
    public function getEmail(){
        return $this->_email;
    }
    public function getPassword(){
        return $this->_password;
    }
    public function toArray(){
        $username = $this->_username;
        $firstName = $this->_firstName;
        $lastName = $this->_lastName;
        $email = $this->_email;
        $password = $this->_password;
        $array = [$username, $firstName, $lastName, $email, $password];
        return $array;
    }
}