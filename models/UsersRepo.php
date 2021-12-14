<?php
//files necessary for this class to function
require_once("Database.php");
require_once("UserDTO.php");
require_once("Repo.php");
class UsersRepo implements Repo
{
    protected $dbHandle, $dbInstance;

    //connect to database
    public function __construct(){
        //getting the instance of the class Database or creating one if it doesn't already exist
        $this->dbInstance = Database::getInstance();
        //getting the connection to the database
        $this->dbHandle = $this->dbInstance->getdbConnection();
    }



    /**
     *  This method adds a User to the table users in the Database, this could also be considered a signUp method.
     * @param ImageDTO $object the User that needs to be inserted into the database
     */
    public function addObject($object){
        //sql query that will insert the data into the table
        $sqlQuery = "INSERT INTO users(username, firstName, lastName, email, password, lat, lng) VALUES(?,?,?,?,?,?,?)";
        //array of data that needs to be bounded with the query above (replace the '?' with actual data)
        $array = $object->toArray();
        //Replacing password with the hashed password which will always be at position 4
        $array[4] = password_hash($array[4], PASSWORD_DEFAULT);
        //passing the data and query to a method that will bind the data and execute the query
        $this->executeQuery($sqlQuery,$array);
    }
    /**
     * @return array of UserDTOs, selects every user in the database, it then converts the values of every user into a UserDTO which is added to an array which is then returned
    */
    function getAll()
    {
        //sqlQuery that needs to be executed (selects everything in the table users)
        $sqlQuery = "SELECT * FROM users";

        //executing the query and converting the data from the query into UserDTOs which will be returned
        return $this->getObjectsFromQuery($sqlQuery);
    }

    /**
     * @param string $pk this is just the username (primary key) of the user we want
     * @return UserDTO it will return the wanted user as a UserDTO
     */
    function getObject($pk)
    {
        //selects all the users where the username is the passed $pk, this will always return one row as the username is the primaryKey
        $sqlQuery = "SELECT * FROM users WHERE username= ?";
        //putting the username into an array as this is necessary to be bounded into the query
        $array = [$pk];
        //returning the fist object from an array of UserDTOs.
        return $this->getObjectsFromQuery($sqlQuery, $array)[0];
    }

    /**
     * @param string $attribute the name of the attribute wanted
     * @param string $pk username of user
     * @return string returns the value of the wanted attribute
    */
    function getAttribute($attribute, $pk)
    {
        //query that needs execution, $attribute is not inputted by the user therefore no need for binding.
        $sqlQuery = "SELECT " . $attribute ." FROM users WHERE username= ?";
        //username added to an array, so it can be bound to the query
        $array = [$pk];
        //executing query and returning the first result.
        return $this->executeQuery($sqlQuery, $array)->fetch()[0];
    }

    /**
     * @param string $pk username that needs to be checked
     * @return bool true if user exists and false if he doesn't
    */
    function objectExists($pk)
    {
        //tries to get the user if nothing is returned then the user doesn't exist
        return $this->getObject($pk) != null;
    }

    /**
     * @param string $attribute that needs to be checked
     * @param string $value value that the $attribute needs to be equal to
    */
    function attributeExists($attribute, $value)
    {
        //query that checks all the users and returns the users that have the attribute = to the value
        $sqlQuery = "SELECT * FROM users WHERE ".$attribute."= ?";
        //adding the value to an array, so it can be bound to the query
        $array = [$value];
        //checks if any user was returned, if a user was returned then the attribute does exist.
        return sizeof($this->getObjectsFromQuery($sqlQuery, $array)) > 0;
    }

    /**
     * @param string $pk username of the user that needs deleting
    */
    function deleteObject($pk)
    {
        //query which will delete the user from the table
        $sqlQuery = "DELETE FROM users WHERE username = ?";
        //username added to string, so it can be bound to the query
        $array = [$pk];
        //executing the query
        $this->executeQuery($sqlQuery, $array);
    }

    /**
     * @param string $sqlQuery the query that needs to executed on the database
     * @param array $values values that need binding in order for the query to function, however, not all queries need this therefore it is set to null by default
     * @return array of UserDTOs with all the users found in the query
    */
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

    /**
     * @param string $sqlQuery the query that needs to executed on the database
     * @param array $values values that need binding in order for the query to function, however, not all queries need this therefore it is set to null by default
     * @return PDOStatement result of executing the query in a statement format, which can be used to get the data.
     */
    function executeQuery($sqlQuery, $values = null){
        //preparing the PDO statement
        $statement = $this->dbHandle->prepare($sqlQuery);
        //executing query
        $statement->execute($values);
        return $statement;
    }

    /**
     * @param string $attribute attribute that needs to be modified
     * @param string $value new value of the attribute that will be modified
     * @param string $username of user, also the primary key
    */
    public function updateAttribute($attribute, $value, $username){
        //if the attribute is name then it needs to be treated differently
        if($attribute == 'name'){
            //query that needs to be executed
            $sqlQuery = "UPDATE users SET firstName = ?, lastName = ? WHERE username = ?";
            //the full name will be passed as one therefore it needs to be split into firstName and lastName
            $value = explode(" ", $value);
            //the first name and last name are then modified so only the firstLetter is in caps
            $firstName = $value[0];
            $firstName = strtolower($firstName);
            $firstName = ucfirst($firstName);
            $lastName = $value[1];
            $lastName = strtolower($lastName);
            $lastName = ucfirst($lastName);
            //they are then added into an array so that they can be later bound to the query
            $array = [$firstName, $lastName, $username];
        }else if($attribute == 'username'){//if the attribute is username then it needs to be treated differently
            //To be able to modify foreign keys, the foreign key check needs to be set to 0, therefore that is the first thing done
            $setUpSqlQuery = "SET FOREIGN_KEY_CHECKS = 0";
            $this->executeQuery($setUpSqlQuery);
            //The friendship table will be updated. The query will find all friendships where the user is either the requester of addressee
            $sqlQuery1 = "UPDATE friendship SET requesterId = ? WHERE requesterId = ?";
            $sqlQuery2 = "UPDATE friendship SET addresseeId = ? WHERE addresseeId = ?";
            //values that need binding
            $array = [$value, $username];
            //executing both queries therefore updating the username in the friendship table
            $this->executeQuery($sqlQuery1, $array);
            $this->executeQuery($sqlQuery2, $array);
            //both the images and users table are updated together
            $sqlQuery3 = "UPDATE users INNER JOIN images ON users.username = images.username
                         SET images.username = ?, users.username = ?
                         WHERE users.username = ?";
            //all the values that need to be bound are added to an array
            $array = [$value, $value, $username];
            //executing the query that will modify the user and images table
            $this->executeQuery($sqlQuery3, $array);
            //resetting the foreign key checks as that is a necessary check when executing other queries
            $sqlQuery = "SET FOREIGN_KEY_CHECKS = 1";
            //emptying array as no data needs binding
            $array = [];
        }
        else{//any other attribute will be updated like this
            //query updates the attribute with the new value where the username is equal to the given $username
            $sqlQuery = "UPDATE users SET " . $attribute . " = ? WHERE username = ?";
            //if the attribute is password then the value will first need hashing for security reasons
            if($attribute == 'password'){
                $value = password_hash($value, PASSWORD_DEFAULT);
            }
            //values that need binding are added to an array
            $array = [$value, $username];
        }
        //executing the query
        $this->executeQuery($sqlQuery, $array);
    }

    /**
     * @param string $username inputted by the user trying to log in
     * @param string $password inputted by the user trying to log in
     * @return string code which indicated what's wrong with the input
    */
    public function signIn($username, $password){
        //query which will find the user with the corresponding username
        $sqlQuery = "SELECT * FROM users WHERE username= ?";
        //input is put into an array, so it can be bound to the query and avoid sql injections.
        $array = [$username];
        //get the user with the matching username
        $users =  $this->getObjectsFromQuery($sqlQuery, $array);
        //it will be returned as an array if the array is bigger than 0 then there is a user with that username; if not then that user does not exist
        //therefore $user will be = to the user if he exists and null if he doesn't
        $user = count($users) > 0 ? $users[0] : null;

        //if the user is not null the password is checked
        if($user != null){
            //checking password inputted with the password in the database table
            if( password_verify($password, $user->getPassword())){
                //if they match then return code T which stands for True indicating everything is correct, and they can sign In
                return "T";//True
            }
            else{
                //the password did not match, WP is returned indicating that the password must be wrong.
                return "WP";//wrong password
            }
        }
        else{
            //if user is null the there is no user with the username and the code WU is returned which indicated that the username inputted must be wrong
            return "WU";//wrong username
        }
    }

    /**
     * @param string $username of the user which wants to see all his requests
     * @return array of UserDTOs which contains all the users that have the Request status and a friendship with the username given
    */
    public function getRequests($username){
        //query finds all users that have the given username as a requester or addressee
        $sqlQuery = 'SELECT * FROM users WHERE username IN (SELECT requesterId FROM friendship WHERE addresseeId = ? AND statusCode = "R")';
        //necessary values are put into an array to later be bound to the query
        $array = [$username];
        //executing query and getting an array of UserDTOs
        return $this->getObjectsFromQuery($sqlQuery, $array);
    }

    /**
     * @param char $statusCode letter indicating the type of friendship
     * @param string $username of user that wants the information
     * @return array of UserDTOs that match the query
    */
    public function getAllStatusCode($statusCode, $username){
        //selects all the users that have a friendship with the given user and their status code matches the requested status code
        $sqlQuery = 'SELECT * FROM users WHERE username IN (SELECT requesterId FROM friendship WHERE addresseeId = ? AND statusCode = "'.$statusCode.'" UNION select addresseeId FROM friendship WHERE requesterId = ? AND statusCode = "'.$statusCode.'")';
        //necessary values are put into an array to later be bound to the query
        $array = [$username, $username];
        //execute query and return all the users that match the query in an array of UserDTOs
        return $this->getObjectsFromQuery($sqlQuery, $array);
    }

    /**
     * @param string $data inputted by the user in the search bar
     * @return array of UserDTOs that have the data somewhere in their attributes
    */
    public function search($data){
        //removing extra spaces and lowering all the letters
        $data = trim(strtolower($data));
        //if it starts with @
        if($data[0] == "@"){
            //checking only for username
            $sqlQuery = 'SELECT * FROM users WHERE instr(username, ?) > 0';
            //removes the extra bits of data like @ or name
            $data = trim(substr($data, 1));
            //necessary values are put into an array to later be bound to the query
            $array = [$data];

        // if it contains the word first name
        }else if(strpos(strtolower($data), 'first name') !== false){
            //checking only first names
            $sqlQuery = 'SELECT * FROM users WHERE instr(firstName, ?) > 0';
            //removes the extra bits of data like @ or name
            $data = trim(str_replace('first name', '', $data));
            //necessary values are put into an array to later be bound to the query
            $array = [$data];

        //if it contains the word last name
        }else if(strpos(strtolower($data), 'last name') !== false){
            //checking only last names
            $sqlQuery = 'SELECT * FROM users WHERE instr(lastName, ?) > 0';
            //removes the extra bits of data like @ or name
            $data = trim(str_replace('last name', '', $data));
            //necessary values are put into an array to later be bound to the query
            $array = [$data];

        //if it contains the word name
        }else if(strpos(strtolower($data), 'name') !== false){
            //checking only names
            $sqlQuery = 'SELECT * FROM users WHERE instr(firstName, ?) > 0  OR instr(lastName, ?) > 0';
            //removes the extra bits of data like @ or name
            $data = trim(str_replace('name', '', $data));
            //necessary values are put into an array to later be bound to the query
            $array = [$data, $data];

        //if it contains the word email
        }else if(strpos(strtolower($data), 'email') !== false){
            //checking only emails
            $sqlQuery = 'SELECT * FROM users WHERE instr(email, ?) > 0';
            //removes the extra bits of data like @ or name
            $data = trim(str_replace('email', '', $data));
            //necessary values are put into an array to later be bound to the query
            $array = [$data];
        }
        else{
            //checking everything
            $sqlQuery = 'SELECT * FROM users WHERE instr(username, ?) > 0 OR instr(firstName, ?) > 0  OR instr(lastName, ?) > 0  OR instr(email, ?) > 0 ';
            //necessary values are put into an array to later be bound to the query
            $array = [$data, $data, $data, $data];
        }
        //executing query and returning users that match the query
        return $this->getObjectsFromQuery($sqlQuery, $array);
    }

    /**
     * @param string username the username given for searching
     * @reuturn array of UserDTOs that match the query
    */
    //anonymous search only checks usernames
    public function search2($username){
        if($username[0] == "@"){
            //removes the @ is it starts with it
            $username = trim(substr($username, 1));
        }
        //check usernames
        $sqlQuery = 'SELECT * FROM users WHERE instr(username, ?) > 0';
        //necessary values are put into an array to later be bound to the query
        $array = [$username];
        //executing and returning matching users
        return $this->getObjectsFromQuery($sqlQuery, $array);
    }

    /**
     * @param int $page number
     * @return array of UserDTOs of all the users on that page
    */
    public function getPageOfUsers($page){
        //select everything in the first 18 rows, offest 18 times the page number
        $sqlQuery = 'SELECT * FROM users LIMIT 18 OFFSET ' . $page * 18;
        //executing and returning first 18 users
        return $this->getObjectsFromQuery($sqlQuery);
    }

    /**
     * @return int the number of pages it will have
    */
    public function getLastPageNumber(){
        //counts all the users and divides them by 18 as that is the max amount of users for each page
        $sqlQuery = 'SELECT COUNT(username)/18 FROM users';

        //if the number is a decimal then it's rounded up to th nearest highest number
        return ceil($this->executeQuery($sqlQuery)->fetch()[0]);
    }
}