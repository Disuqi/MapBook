<?php
class FriendshipDTO{
    protected $_requesterId, $_addresseeId, $_date, $_statusCode;

    public function __construct($dbRow)
    {
        //initiating all fields
        $this->_requesterId = $dbRow['requesterId'];
        $this->_addresseeId = $dbRow['addresseeId'];
        $this->_date = $dbRow['date'];
        $this->_statusCode = $dbRow['statusCode'];
    }

    //get methods
    public function getRequesterId(){
        return $this->_requesterId;
    }

    public function getAddresseeId()
    {
        return $this->_addresseeId;
    }

    public function getDate()
    {
        return $this->_date;
    }

    public function getStatusCode(){
        return $this->_statusCode;
    }

    public function toArray()
    {
        return [$this->_requesterId, $this->_addresseeId, $this->_date, $this->_statusCode];
    }
}