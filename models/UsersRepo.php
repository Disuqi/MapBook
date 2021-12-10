<?php
require_once("Database.php");
require_once("UserDTO.php");
class UsersRepo implements Repo
{
    protected $dbHandle, $dbInstance;

    public function __construct(){
        //getting the instance of the class Database or creating one if it doesn't already exist
        $this->dbInstance = Database::getInstance();
        //getting the connection to the database
        $this->dbHandle = $this->dbInstance->getdbConnection();
    }


    public function addObject($object){
        //adding user to user
        $sqlQuery = "INSERT INTO users(username, firstName, lastName, email, password, lat, lng) VALUES(?,?,?,?,?,?,?)";
        $array = $object->toArray();
        $array[4] = password_hash($array[4], PASSWORD_DEFAULT);
        $this->executeQuery($sqlQuery,$array);
    }

    function getAll()
    {
        //sqlQuery that needs to be executed
        $sqlQuery = "SELECT * FROM users";

        //executing the query and getting the result
        return $this->getObjectsFromQuery($sqlQuery);
    }

    function getObject($pk)
    {
        $sqlQuery = "SELECT * FROM users WHERE username= ?";
        $array = [$pk];
        return $this->getObjectsFromQuery($sqlQuery, $array)[0];
    }

    function getAttribute($attribute, $pk)
    {
        $sqlQuery = "SELECT " . $attribute ." FROM users WHERE username= ?";
        $array = [$pk];
        return $this->getObjectsFromQuery($sqlQuery, $array)->fetch()[0];
    }

    function objectExists($pk)
    {
        return $this->getObject($pk) != null;
    }

    function attributeExists($attribute, $value)
    {
        $sqlQuery = "SELECT * FROM users WHERE ".$attribute."= ?";
        $array = [$value];
        return sizeof($this->getObjectsFromQuery($sqlQuery, $array)) > 0;
    }

    function deleteObject($pk)
    {
        $sqlQuery = "DELETE FROM users WHERE username = ?";
        $array = [$pk];
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
            $dataset[] = new UserDTO($row);
        }
        //returning a list of users that match the query
        return $dataset;
    }

    function executeQuery($sqlQuery, $values = null){
        //preparing the PDO statement
        $statement = $this->dbHandle->prepare($sqlQuery);
        //executing query
        $statement->execute($values);
        return $statement;
    }

    public function signIn($username, $password){
        $sqlQuery = "SELECT * FROM users WHERE username= ?";
        $array = [$username];

        $users =  $this->getObjectsFromQuery($sqlQuery, $array);
        count($users) > 0 ? $user = $users[0] : $user = null;
        if($user != null){
            if( password_verify($password, $user->getPassword())){
                return "T";//True
            }
            else{
                return "WP";//wrong password
            }
        }
        else{
            return "WU";//wrong username
        }
    }

    public function getFriends($username){
        $sqlQuery = 'select * from users WHERE username in (select requesterId from friendship WHERE addresseeId = ? UNION select addresseeId from friendship where requesterId = ?)';
        $array = [$username, $username];
        return $this->getObjectsFromQuery($sqlQuery, $array);
    }

    public function search($data){
        $sqlQuery = 'select * from users where instr(username, ?) > 0 or instr(firstName, ?) > 0  or instr(lastName, ?) > 0  or instr(email, ?) > 0 ';
        $array = [$data, $data, $data, $data];
        return $this->getObjectsFromQuery($sqlQuery, $array);
    }
}