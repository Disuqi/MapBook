<?php
require_once("FriendshipDTO.php");
require_once("Database.php");
class FriendshipRepo implements Repo{

    protected $dbHandle, $dbInstance;

    public function __construct(){
        //getting the instance of the class Database or creating one if it doesn't already exist
        $this->dbInstance = Database::getInstance();
        //getting the connection to the database
        $this->dbHandle = $this->dbInstance->getdbConnection();
    }

    function getAll()
    {
        //sqlQuery that needs to be executed
        $sqlQuery = "SELECT * FROM friendship";

        //executing the query and getting the result
        return $this->getObjectsFromQuery($sqlQuery);
    }

    function getObject($pk)
    {
        //sqlQuery that needs to be executed. It finds the friendship with the requesterId and addresseeId given
        $sqlQuery = "SELECT * FROM friendship WHERE requesterId = ? AND addresseeId = ?";

        //array of values that need to be inserted in the sql statement instead of the "?"
        $array = [$pk['requesterId'], $pk['addresseeId']];

        //executing statement and getting the first value from the result
        $result = $this->getObjectsFromQuery($sqlQuery, $array);
        return $result == null? null : $result[0];
    }

    function getAttribute($attribute, $pk)
    {
        //sqlQuery that needs to be executed. It finds the friendship with the requesterId and addresseeId given, and it returns the requested attribute
        $sqlQuery = "SELECT " . $attribute . " FROM friendship WHERE requesterId = ? AND addresseeId = ?";
        //array of values that need to be inserted in the sql statement instead of the "?"
        $array = [$pk['requesterId'], $pk['addresseeId']];
        //executing statement and getting the first value from the result
        $result =  $this->executeQuery($sqlQuery, $array)->fetch();
        return $result == null ? null : $result[0];
    }

    function objectExists($pk)
    {
        return $this->getObject($pk) != null;
    }

    function attributeExists($attribute, $value)
    {
        $sqlQuery = "SELECT * FROM friendship WHERE ".$attribute."= ?";
        $array = [$value];
        return sizeof($this->getUsersFromQuery($sqlQuery, $array)) > 0;
    }

    function addObject($object)
    {
        if($this->areFriends($object) == null) {
            $sqlQuery = "INSERT INTO friendship(requesterId, addresseeId, date, statusCode) VALUES(?,?,NOW(),'R')";
            $array = [$object['requesterId'], $object['addresseeId']];
            $this->executeQuery($sqlQuery, $array);
        }
    }

    function deleteObject($pk)
    {
        $sqlQuery = "DELETE FROM friendship WHERE requesterId = ? AND addresseeId = ?";
        $array = [$pk['requesterId'], $pk['addresseeId']];
        $this->executeQuery($sqlQuery, $array);
    }

    function getObjectsFromQuery($sqlQuery, $values = null)
    {
        //preparing the PDO statement
        $statement = $this->executeQuery($sqlQuery, $values);
        //creating an empty array
        $dataset = [];
        //filling up the array with the result gotten from executing the query
        while($row = $statement->fetch()){
            $dataset[] = new FriendshipDTO($row);
        }
        //returning a list of users that match the query
        return $dataset;
    }

    function executeQuery($sqlQuery, $values = null)
    {
        //preparing the PDO statement
        $statement = $this->dbHandle->prepare($sqlQuery);
        //executing query
        $statement->execute($values);
        return $statement;
    }

    function updateStatus($pk, $status){
        $sqlQuery = "UPDATE friendship SET statusCode = ?, date = NOW() WHERE requesterId = ? AND addresseeId = ?";
        $array = [$status, $pk['requesterId'], $pk['addresseeId']];
        $this->executeQuery($sqlQuery, $array);
    }

    function deleteAccount($id){
        $array = [$id];
        $sqlQuery = "DELETE FROM friendship WHERE requesterId = ?";
        $this->executeQuery($sqlQuery, $array);
        $sqlQuery = "DELETE FROM friendship WHERE addresseeId = ?";
        $this->executeQuery($sqlQuery, $array);
    }

    function areFriends($pk){
        //sqlQuery that needs to be executed
        $sqlQuery = "SELECT * FROM friendship WHERE (requesterId = ? AND addresseeId = ?) OR (requesterId = ? AND addresseeId = ?)";
        $array = [$pk['requesterId'], $pk['addresseeId'], $pk['addresseeId'], $pk['requesterId']];
        //executing the query and getting the result
        $result = $this->getObjectsFromQuery($sqlQuery, $array);
        return $result == null ? null : $result[0];
    }
}