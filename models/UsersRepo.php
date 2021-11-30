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
        return $this->getUsersFromQuery($sqlQuery);
    }

    public function signIn($username, $password){
        $sqlQuery = "SELECT * FROM users WHERE username= ?";
        $array = [$username];

        $users =  $this->getUsersFromQuery($sqlQuery, $array);
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

    public function signUp($user){
        //adding user to user
        $sqlQuery = "INSERT INTO users(username, firstName, lastName, email, password) VALUES(?,?,?,?,?)";
        $this->executeQuery($sqlQuery, $user->toArray());
        //adding profile image to images
        $sqlQuery = "INSERT INTO images(imageName, username, date, profileImage) VALUES (?,?, NOW(), 1)";
        $username = $user->getUsername();
        $array = [$username . "_1.png", $username];
        $this->executeQuery($sqlQuery, $array);
    }

    public function getUser($username){
        $sqlQuery = "SELECT * FROM users WHERE username= ?";
        $array = [$username];
        return $this->getUsersFromQuery($sqlQuery, $array);
    }
    public function usernameExists($username){
        $sqlQuery = "SELECT * FROM users WHERE username= ?";
        $array = [$username];
        return sizeof($this->getUsersFromQuery($sqlQuery, $array)) > 0;
    }

    public function emailExists($email){
        $sqlQuery = "SELECT * FROM users WHERE email= ?";
        $array = [$email];
        return sizeof($this->getUsersFromQuery($sqlQuery, $array)) > 0;
    }

    private function getUsersFromQuery($sqlQuery, $array = null){
        //preparing the PDO statement
        $statement = $this->executeQuery($sqlQuery, $array);
        //creating an empty array
        $dataset = [];
        //filling up the array with the result gotten from executing the query
        while($row = $statement->fetch()){
            $dataset[] = new UserDTO($row);
        }
        //returning a list of users that match the query
        return $dataset;
    }

    public function deleteAccount($username){
        $sqlQuery = "DELETE FROM users WHERE username = ?";
        $array = [$username];
        $this->executeQuery($sqlQuery, $array);
    }

    //Maybe need to delete
    public function deleteImages($username){
        $sqlQuery = "DELETE FROM images WHERE username = ?";
        $array = [$username];
        $this->executeQuery($sqlQuery, $array);
    }

    public function getProfileImage($username){
        $sqlQuery = "SELECT imageName FROM images WHERE username = ? AND profileImage = 1";
        $array = [$username];
        return $this->executeQuery($sqlQuery, $array)->fetch()[0];
    }
    private function executeQuery($sqlQuery, $array = null){
        //preparing the PDO statement
        $statement = $this->dbHandle->prepare($sqlQuery);
        //executing query
        $statement->execute($array);
        return $statement;
    }
}