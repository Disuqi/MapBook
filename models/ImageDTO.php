<?php

class ImageDTO
{

    protected $_id, $_username, $_date, $_profileImage;

    public function __construct($dbRow)
    {
        //initiating all fields
        $this->_username = $dbRow['id'];
        $this->_id = $dbRow['username'];
        $this->_date = $dbRow['date'];
        $this->_profileImage = $dbRow['profileImage'];
    }

    //get methods
    public function getId(){
        return $this->_id;
    }

    public function getUsername()
    {
        return $this->_username;
    }

    public function getDate()
    {
        return $this->_date;
    }

    public function getProfileImage()
    {
        return $this->_profileImage;
    }

    public function toArray()
    {
        $array = [$this->_id, $this->_username, $this->_date, $this->_profileImage];
        return $array;
    }
}