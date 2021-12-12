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

    public function updateAttribute($attribute, $value, $username){
        if($attribute == 'name'){
            $sqlQuery = "UPDATE users SET firstName = ?, lastName = ? WHERE username = ?";
            $value = explode(" ", $value);
            $firstName = $value[0];
            $firstName = strtolower($firstName);
            $firstName = ucfirst($firstName);
            $lastName = $value[1];
            $lastName = strtolower($lastName);
            $lastName = ucfirst($lastName);
            $array = [$firstName, $lastName, $username];
        }else if($attribute == 'username'){
            $sqlQuery1 = "UPDATE friendship SET requesterId = ? WHERE requesterId = ?";
            $sqlQuery2 = "UPDATE friendship SET addresseeId = ? WHERE addresseeId = ?";
            $array = [$value, $username];
            $this->executeQuery($sqlQuery1, $array)->errorInfo();
            $this->executeQuery($sqlQuery2, $array)->errorInfo();
            $sqlQuery3 = "SET FOREIGN_KEY_CHECKS = 0";
            $this->executeQuery($sqlQuery3);
            $sqlQuery4 = "UPDATE users INNER JOIN images ON users.username = images.username
                         SET images.username = ?, users.username = ?
                         WHERE users.username = ?";
            $array = [$value, $value, $username];
            $this->executeQuery($sqlQuery4, $array);
            $sqlQuery = "SET FOREIGN_KEY_CHECKS = 1";
            $array = [];
        }
        else{
            $sqlQuery = "UPDATE users SET " . $attribute . " = ? WHERE username = ?";
            if($attribute == 'password'){
                $value = password_hash($value, PASSWORD_DEFAULT);
            }
            $array = [$value, $username];
        }
        $this->executeQuery($sqlQuery, $array)->errorInfo();
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

    public function getRequests($username){
        $sqlQuery = 'SELECT * FROM users WHERE username IN (SELECT requesterId FROM friendship WHERE addresseeId = ? AND statusCode = "R")';
        $array = [$username];
        return $this->getObjectsFromQuery($sqlQuery, $array);
    }

    public function getAllStatusCode($statusCode, $username){
        $sqlQuery = 'SELECT * FROM users WHERE username IN (SELECT requesterId FROM friendship WHERE addresseeId = ? AND statusCode = "'.$statusCode.'" UNION select addresseeId FROM friendship WHERE requesterId = ? AND statusCode = "'.$statusCode.'")';
        $array = [$username, $username];
        return $this->getObjectsFromQuery($sqlQuery, $array);
    }
    public function search($data){
        $sqlQuery = 'SELECT * FROM users WHERE instr(username, ?) > 0 OR instr(firstName, ?) > 0  OR instr(lastName, ?) > 0  OR instr(email, ?) > 0 ';
        $array = [$data, $data, $data, $data];
        return $this->getObjectsFromQuery($sqlQuery, $array);
    }

    public function search2($username){
        $sqlQuery = 'SELECT * FROM users WHERE instr(username, ?) > 0';
        $array = [$username];
        return $this->getObjectsFromQuery($sqlQuery, $array);
    }
}