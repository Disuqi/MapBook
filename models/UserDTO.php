<?php
require_once "JsonSerializer.php";
class UserDTO implements JsonSerializer {

    protected $_username, $_firstName, $_lastName, $_email, $_password, $_lat, $_lng, $_friendship, $_profileImage;


    public function __construct($dbRow){
        //initiating all fields
        $this->_username = $dbRow['username'];
        $this->_firstName = $dbRow['firstName'];
        $this->_lastName = $dbRow['lastName'];
        $this->_email = $dbRow['email'];
        $this->_password = $dbRow['password'];
        $this->_lat = $dbRow['lat'];
        $this->_lng = $dbRow['lng'];
        $this->_friendship = null;
        $this->_profileImage = "../images/noProfilePic.svg";
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
    public function getFriendship(){
        return $this->_friendship;
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
    public function getProfileImage(){
        return $this->_profileImage;
    }
    public function getPosition(){
        return "($this->_lat, $this->_lng)";
    }


    public function setProfileImage($profileImage){
        $this->_profileImage = $profileImage;
    }

    public function setFriendship($friendship){
        $this->_friendship = $friendship;
    }

    public function toJson(){
        $toReturn = '{
            "username" : "'. $this->_username.'",
            "firstName" : "'. $this->_firstName.'",
            "lastName" : "'. $this->_lastName.'",
            "email" : "'. $this->_email.'",
            "lat" : '. $this->_lat.',
            "lng" : '. $this->_lng.', ';

        if($this->_friendship == null){
            $toReturn .= '
                "statusCode": "null",
            ';
        }else{
            $toReturn .= '
                "statusCode" : "'.$this->_friendship->getStatusCode() .'",
                "requester" : "'.$this->_friendship->getRequesterId().'",
                "addressee" : "'.$this->_friendship->getAddresseeId().'", 
            ';
        }

        $toReturn .= '"profileImage" : "'. $this->_profileImage.'"}';
        return $toReturn;
    }

    //converting to all the values into an array
    public function toArray(){
        return [$this->_username, $this->_firstName, $this->_lastName, $this->_email, $this->_password, $this->_lat, $this->_lng];
    }
}