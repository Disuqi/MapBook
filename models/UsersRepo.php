<?php
require_once("Database.php");
require_once("UserDTO.php");
class UsersRepo
{
    protected $dbHandle, $dbInstance;

    public function __construct(){
        //getting the instance of the class Database or creating one if it doesn't already exist
        $this->dbInstance = Database::getInstance();
        //getting the connection to the database
        $this->dbHandle = $this->dbInstance->getdbConnection();
    }

    public function getAllUsers()
    {
        //sqlQuery that needs to be executed
        $sqlQuery = "SELECT * FROM users";

        //executing the query and getting the result
        return $this->executeQuery($sqlQuery);
    }

    public function signIn($username, $password){
        $sqlQuery = "SELECT * FROM users WHERE username= ?";
        $array = [$username];

        $users =  $this->executeQuery($sqlQuery, $array);
        count($users) > 0 ? $user = $users[0] : $user = null;
        if($user != null){
            if($user->getPassword() == $password){
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

    public function getUser($username){
        $sqlQuery = "SELECT * FROM users WHERE username= ?";
        $array = [$username];
        return $this->executeQuery($sqlQuery, $array);
    }
    public function usernameExists($username){
        $sqlQuery = "SELECT * FROM users WHERE username= ?";
        $array = [$username];
        return sizeof($this->executeQuery($sqlQuery, $array)) > 0;
    }

    public function emailExists($email){
        $sqlQuery = "SELECT * FROM users WHERE email= ?";
        $array = [$email];
        return sizeof($this->executeQuery($sqlQuery, $array)) > 0;
    }

    public function executeQuery($sqlQuery, $array = null){
        //preparing the PDO statement
        $statement = $this->dbHandle->prepare($sqlQuery);
        //executing query
        $statement->execute($array);
        //creating an empty array
        $dataset = [];
        //filling up the array with the result gotten from executing the query
        while($row = $statement->fetch()){
            $dataset[] = new UserDTO($row);
        }
        //returning a list of users that match the query
        return $dataset;
    }
}