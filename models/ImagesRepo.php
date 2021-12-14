<?php
require_once('Repo.php');
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

    /**
     * @return array of ImageDTOs of all images in the table
    */
    function getAll()
    {
        //sqlQuery that needs to be executed
        $sqlQuery = "SELECT * FROM images";

        //executing the query and getting the result
        return $this->getObjectsFromQuery($sqlQuery);
    }

    /**
     * @param array-key $pk primary key of table images, which should include id of the image and username of the user
     * @return ImageDTO of the wanted image
     */
    function getObject($pk)
    {
        //query to execute
        $sqlQuery = "SELECT * FROM images WHERE id = ? AND username = ?";
        //values that need binding to the query
        $array = [$pk['id'], $pk['username']];
        //returning array of ImageDTOs that match the query
        return $this->getObjectsFromQuery($sqlQuery, $array);
    }
    /**
     * @param string $attribute wanted form the table
     * @param array-key $pk primary key of table images, which should include id of the image and username of the user
     * @return string attribute of the wanted image
     */
    function getAttribute($attribute, $pk){
        //query to execute
        $sqlQuery = "SELECT " . $attribute . " FROM images WHERE id = ? AND username = ?";
        //values that need binding to the query
        $array = [$pk['id'], $pk['username']];
        //returning the value of the wanted attribute
        return $this->executeQuery($sqlQuery, $array)->fetch()[0];
    }

    /**
     * @param array-key $pk primary key of table images, which should include id of the image and username of the user
     * @return bool true if it exitst and false if it doesn't
     */
    function objectExists($pk)
    {
        //no object is returned then it means it does not exist therefore false is returned
        return $this->getObject($pk) != null;
    }

    /**
     * @param string $attribute wanted form the table
     * @param string $value of the wanted attribute
     * @return bool attribute exists then return true if not then false
     */
    function attributeExists($attribute, $value)
    {
        //query to execute
        $sqlQuery = "SELECT * FROM images WHERE ".$attribute."= ?";
        //values that need binding to the query
        $array = [$value];
        //if the size is bigger than 0 then the attribute must exist
        return sizeof($this->getUsersFromQuery($sqlQuery, $array)) > 0;
    }

    /**
     * @param array-key $object contains all the necessary information to add an image to the images table
     */
    function addObject($object)
    {
        //id is always one bigger than the biggest id
        $id = $this->getMaxId($object['username']) + 1;
        //query to execute
        $sqlQuery = "INSERT INTO images(id, username, date, ext, profileImage) VALUES(?, ?,NOW(), ?, NULL)";
        //values that need binding to the query
        $array = [$id, $object['username'], $object['ext']];
        //execution of query
        $this->executeQuery($sqlQuery,$array);
    }

    /**
     * @param array-key $pk primary key of table images, which should include id of the image and username of the user
     */
    function deleteObject($pk)
    {
        //query to execute
        $sqlQuery = "DELETE FROM images WHERE id = ? AND username = ?";
        //values that need binding to the query
        $array = [$pk['id'], $pk['username']];
        //execution of query
        $this->executeQuery($sqlQuery, $array);
    }

    /**
     * @param string $sqlQuery query that needs execution
     * @param array $values values that need to be bounded, sometimes they are not necessary therefore it is defaulted to null
     * @return array of ImageDTOs that mach the query
    */
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

    /**
     * @param string $sqlQuery query that needs execution
     * @param array $values values that need to be bounded, sometimes they are not necessary therefore it is defaulted to null
     * @return PDOStatement of the query result
     */
    function executeQuery($sqlQuery, $values = null)
    {
        //preparing the PDO statement
        $statement = $this->dbHandle->prepare($sqlQuery);
        //executing query
        $statement->execute($values);
        return $statement;
    }

    //Methods only in this class

    /**
     * @param string $username of the user that wants all the images deleted
     */
    function deleteAllImagesOfUser($username){
        //deletes all images for $username
        //query to execute
        $sqlQuery = "DELETE FROM images WHERE username = ?";
        //values that need binding to the query
        $array = [$username];
        //execution of query
        $this->executeQuery($sqlQuery, $array);
    }

    /**
     * @param string $username of the user that needs the max id
     * @return int max id
     */
    function getMaxId($username){
        //gets the biggest id in the images table where username = $username
        //query to execute
        $sqlQuery = "SELECT MAX(id) FROM images WHERE username = ?";
        //values that need binding to the query
        $array = [$username];
        //execution of query
        return $this->executeQuery($sqlQuery, $array)->fetch()[0];
    }

    /**
     * @param string $username of the user that needs his profile image
     * @return mixed ImageDTO if they have a profile image or null if they don't
     */
    function getProfileImage($username){
        //find the profileImage of the user
        //query to execute
        $sqlQuery = "SELECT * FROM images WHERE username = ? AND profileImage = 1";
        //values that need binding to the query
        $array = [$username];
        //execution of query
        $result = $this->getObjectsFromQuery($sqlQuery, $array);
        //do they have a profile image? returns null if they don't and ImageDTO if they do
        if($result != []) {
            return $result[0];
        }else{
            return null;
        }
    }


    /**
     * @param array-key $pk primary key of table images, which should include id of the image and username of the user
     */
    function setProfileImage($pk){
        //changes the profileImage of a user
        //query that removes the previous profile Image
        $sqlQuery = 'update images set profileImage = null where profileImage = 1 and username = ?';
        //values that need binding to the query
        $array = [$pk['username']];
        //first query is executed
        $this->executeQuery($sqlQuery, $array);
        //query that sets the new image as the profile image
        $sqlQuery = 'update images set profileImage = 1 where id = ? and username = ?';
        //values that need binding to the query
        $array = [$pk['id'], $pk['username']];
        //query executed
        $this->executeQuery($sqlQuery, $array);
    }


}