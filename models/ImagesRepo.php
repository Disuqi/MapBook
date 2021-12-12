<?php
require_once('ImageDTO.php');
require_once('Database.php');
class ImagesRepo implements Repo{

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
        $sqlQuery = "SELECT * FROM images";

        //executing the query and getting the result
        return $this->getObjectsFromQuery($sqlQuery);
    }

    function getObject($pk)
    {
        $sqlQuery = "SELECT * FROM images WHERE id = ? AND username = ?";
        $array = [$pk['id'], $pk['username']];
        return $this->getObjectsFromQuery($sqlQuery, $array);
    }

    function getAttribute($attribute, $pk){
        $sqlQuery = "SELECT " . $attribute . " FROM images WHERE id = ? AND username = ?";
        $array = [$pk['id'], $pk['username']];
        return $this->executeQuery($sqlQuery, $array)->fetch()[0];
    }

    function objectExists($pk)
    {
        return $this->getObject($pk) != null;
    }

    function attributeExists($attribute, $value)
    {
        $sqlQuery = "SELECT * FROM images WHERE ".$attribute."= ?";
        $array = [$value];
        return sizeof($this->getUsersFromQuery($sqlQuery, $array)) > 0;
    }

    function addObject($object)
    {
        $id = $this->getMaxId($object['username']) + 1;
        $sqlQuery = "INSERT INTO images(id, username, date, ext, profileImage) VALUES(?, ?,NOW(), ?, NULL)";
        $array = [$id, $object['username'], $object['ext']];
        $this->executeQuery($sqlQuery,$array);
    }

    function deleteObject($pk)
    {
        $sqlQuery = "DELETE FROM images WHERE id = ? AND username = ?";
        $array = [$pk['id'], $pk['username']];
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
            $dataset[] = new ImageDTO($row);
        }
        //returning a list of images that match the query
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

    //Methods only in this class
    function deleteAllImagesOfUser($username){
        $sqlQuery = "DELETE FROM images WHERE username = ?";
        $array = [$username];
        $this->executeQuery($sqlQuery, $array);
    }

    function getMaxId($username){
        $sqlQuery = "SELECT MAX(id) FROM images WHERE username = ?";
        $array = [$username];
        return $this->executeQuery($sqlQuery, $array)->fetch()[0];
    }

    function getAllProfileImages(){
        $sqlQuery = "SELECT * FROM images WHERE profileImage = 1";
        return $this->getObjectsFromQuery($sqlQuery);
    }

    function getProfileImage($username){
        $sqlQuery = "SELECT * FROM images WHERE username = ? AND profileImage = 1";
        $array = [$username];
        $result = $this->getObjectsFromQuery($sqlQuery, $array);
        if($result != []) {
            return $result[0];
        }else{
            return null;
        }
    }

    function setProfileImage($pk){
        $sqlQuery = 'update images set profileImage = null where profileImage = 1 and username = ?';
        $array = [$pk['username']];
        $this->executeQuery($sqlQuery, $array);
        $sqlQuery = 'update images set profileImage = 1 where id = ? and username = ?';
        $array = [$pk['id'], $pk['username']];
        $this->executeQuery($sqlQuery, $array);
    }


}