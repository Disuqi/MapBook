<?php
class UserDTO{

    protected $_username, $_firstName, $_lastName, $_email, $_password, $_lat, $_lng;

    public function __construct($dbRow){
        //initiating all fields
        $this->_username = $dbRow['username'];
        $this->_firstName = $dbRow['firstName'];
        $this->_lastName = $dbRow['lastName'];
        $this->_email = $dbRow['email'];
        $this->_password = $dbRow['password'];
        $this->_lat = $dbRow['lat'];
        $this->_lng = $dbRow['lng'];
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

    public function getFullName(){
        return $this->_firstName . " " . $this->_lastName;
    }
    public function getEmail(){
        return $this->_email;
    }
    public function getPassword(){
        return $this->_password;
    }
    public function getLat(){
        return $this->_lat;
    }
    public function getLng(){
        return $this->_lng;
    }

    public function getPosition(){
        return "($this->_lat, $this->_lng)";
    }
    public function toArray(){
        return [$this->_username, $this->_firstName, $this->_lastName, $this->_email, $this->_password, $this->_lat, $this->_lng];
    }
}