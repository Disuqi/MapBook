<?php

class ImageDTO
{

    protected $_id, $_username, $_date, $_ext, $_profileImage;

    public function __construct($dbRow)
    {
        //initiating all fields
        $this->_id = $dbRow['id'];
        $this->_username = $dbRow['username'];
        $this->_date = $dbRow['date'];
        $this->_ext = $dbRow['ext'];
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

    public function getExt(){
        return $this->_ext;
    }

    public function getProfileImage()
    {
        return $this->_profileImage;
    }

    public function getImagePath(){
        return "../images/$this->_username/$this->_id.$this->_ext";
    }

    public function toArray()
    {
        return [$this->_id, $this->_username, $this->_date, $this->_profileImage];
    }
}