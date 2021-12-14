<?php
require_once('Repo.php');
require_once('UsersRepo.php');
require_once('ImagesRepo.php');
require_once('FriendshipRepo.php');
class UserLister{

    protected $usersRepo, $imagesRepo, $friendshipRepo;

    //connects to database
    public function __construct()
    {
        $this->usersRepo = new UsersRepo();
        $this->imagesRepo = new ImagesRepo();
        $this->friendshipRepo = new FriendshipRepo();
    }

    /**
     * @return string of html code containing all users found in (bootstrap) card format
    */
    public function getAllUsers(){
        $users = $this->usersRepo->getPageOfUsers(0);
        return $this->makeCards($users);
    }

    /**
     * @return int last page number depending on how many users there are
     */
    public function getLastPageNumber(){
        return $this->usersRepo->getLastPageNumber();
    }

    /**
     * @param int $page the page number that the user wants to see
     * @return string of html code containing all users found in (bootstrap) card format
     */
    public function getPageOfUsers($page){
        //gets all the users on that page
        $users = $this->usersRepo->getPageOfUsers($page-1);
        //makes html code to display the users and returns it
        return $this->makeCards($users);
    }

    /**
     * @param string $username of user that wants to see the requests
     * @return string of html code containing all users found in (bootstrap) card format
     */
    public function getAllRequests($username){
        //gets all the users who have a friendship with $username and their status is on request
        $users = $this->usersRepo->getAllStatusCode("R", $username);
        //makes a page title
        $result = $this->makePageTitle('Requests', count($users));
        //makes html code to display the users and returns it
        $result .= $this->makeCards($users);
        return $result;
    }

    /**
     * @param string $username of user that wants to see the declined friendships
     * @return string of html code containing all users found in (bootstrap) card format
     */
    public function getAllDeclined($username){
        //gets all the users who have a friendship with $username and their status is on declined
        $users = $this->usersRepo->getAllStatusCode("D", $username);
        //makes a page title
        $result = $this->makePageTitle('Declined', count($users));
        //makes html code to display the users and returns it
        $result .= $this->makeCards($users);
        return $result;
    }

    /**
     * @param string $username of user that wants to see the requests
     * @return string of html code containing all users found in (bootstrap) list item format
     */
    public function getRequests($username){
        //gets all the users that have a friendship with $username and $username is the addressee
        $users = $this->usersRepo->getRequests($username);
        //makes html code to display the users and returns it
        return $this->makeListItems($users);
    }

    /**
     * @param string $username of user that wants to see all his friends
     * @return string of html code containing all users found in (bootstrap) card format
     */
    public function getFriends($username){
        //gets all the friendships with status accepted for the user $username
        $users = $this->usersRepo->getAllStatusCode("A", $username);
        //makes the title
        $result = $this->makePageTitle('Friends', count($users));
        //makes html code to display the users and returns it
        $result .= $this->makeCards($users);
        return $result;
    }

    /**
     * @param string $data inputted by the user
     * @return string of html code containing all users found in (bootstrap) card format
     */
    public function search($data){
        //gets all the users that match the data
        $users = $this->usersRepo->search($data);
        //makes the title
        $result = $this->makePageTitle('You searched for "' . $data . '"', count($users));
        //makes html code to display the users and returns it
        $result .= $this->makeCards($users);
        return $result;
    }

    /**
     * @param string $username inputted by the user
     * @return string of html code containing all users found in (bootstrap) card format
     */
    public function anonymousSearch($username){
        //gets all the users that match the username given
        $users = $this->usersRepo->search2($username);
        //makes the title
        $result = $this->makePageTitle('You searched for "' . $username . '"', count($users));
        //makes html code to display the users and returns it
        $result .= $this->makeCards($users);
        return $result;
    }

    /**
     * @param string $title of the page
     * @param int $numOfUsers number of users found
     * @return string html code containing the page title
    */
    private function makePageTitle($title, $numOfUsers){
        //checks if the word result will need an s at the end
        $needS = $numOfUsers == 1 ? null : 's';
        //makes the page title with the infromation given
        $pageTitle = '<div class="container-fluid" style="margin-top: 80px; margin-left: 5px"><h1>'.$title.'</h1><h5>'.$numOfUsers.' result' .$needS.'</h5></div>';
        if($numOfUsers == 0){
            //if there are no users then no title is needed
            return null;
        }else{
            //else return the page title
            return $pageTitle;
        }
    }

    /**
     * @param array $users of UserDTOs
     * @return string html code containing all the users in (bootstrap) list item format
     */
    private function makeListItems($users){
        //initializing the result
        $usersList = "";
        //if there are no requests then return one list item letting them know there are none
        if($users == null){
            $usersList .= "<li class='listItem'>No Requests <i style='font-size: 20px' class='bi bi-inbox'></i></li>";
        }
        //for each user in the array make a list item
        foreach($users as $user){
            //get the username of the user in the array
            $un = $user->getUsername();
            //get the username of the loggedin user
            $loggedUn = $_SESSION['username'];

            //make html code
            $usersList .= "<li class='listItem d-flex align-items-center justify-content-end w-auto mx-3'>$un 
                <a class='btn btn-outline-success mx-2' href='".$this->generateLink('accept', $un, $loggedUn)."'><i class='bi bi-person-check-fill'></i></a>
                        <a class='btn btn-outline-danger' href='".$this->generateLink('decline', $un, $loggedUn)."'><i class='bi bi-person-x-fill'></i></a>
                </li>";
        }
        //return html code
        return $usersList;
    }

    /**
     * @param array $users of UserDTOs
     * @return string html code containing all the users in (bootstrap) card format
     */
    private function makeCards($users){
        //if there are no users return null
        if($users == null){
            return null;
        }
        //make a div of class row so that bootstrap automatically organises the cards
        $usersList = "<div class='row justify-content-start  m-0'>";
        foreach ($users as $user){
            //get username
            $un = $user->getUsername();
            //get profile image
            $prImageDTO = $this->imagesRepo->getProfileImage($un);//profile image dto
            //get logged in user username
            $loggedUn = isset($_SESSION['username'])? $_SESSION['username'] : null;
            //in they have no image then set the default image
            if($prImageDTO == null){
                $profileImage = "../images/noProfilePic.svg";
            }else{
                $profileImage = $prImageDTO->getImagePath();
            }
            //if the username of the two users doesn't match then make the card (don't make a card if the user is the logged in user)
            if(strcasecmp($loggedUn, $un) != 0) {
                //making basic card
                $usersList .= "
                    <div class='card userCard'>
                        <img src=". $profileImage ." class='card-img-top' style='max-height: 10rem; object-fit: contain' alt='profileImage'>
                        <div class='card-body'>
                        <h5>@$un</h5>
                    ";
                //if loggedIn then make full card
                if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']){
                    $usersList .= $this->fullCard($user, $loggedUn);
                }
                //close all tags that need closing
                $usersList .= "</div></div>";
            }
        }
        $usersList .= '</div>';
        //return the html code in string format
        return $usersList;
    }

    /**
     * @param UserDTO $user user that needs the full card
     * @param string $loggedUn username of the loggedIn user
     * @return string html code containing the full card of $user
     */
    private function fullCard($user, $loggedUn){
        //initializing full card with all the details of the user
        $fullCard = "
                                <h6 class='card-subtitle text-muted'>Name</h6>
                                <p>".$user->getFullName()."</p>
                                <h6 class='card-subtitle text-muted'>Email</h6>
                                <p>".$user->getEmail()."</p>
                                <h6 class='card-subtitle text-muted'>Position</h6>
                                <p>".$user->getPosition()."</p>
                                <div class='d-flex  align-items-center justify-content-center text-center'>
                                ";
        //checking the relationship with the logged user and making the necessary adjustments
        $friendship = $this->friendshipRepo->areFriends(["requesterId" => $loggedUn, "addresseeId" => $user->getUsername()]);
        if($friendship != null){
            //if they are friends then store all the necessary values in variables later used
            $requester = $friendship->getRequesterId();
            $addressee = $friendship->getAddresseeId();
            $statusCode = $friendship->getStatusCode();
            //checking if the logged user is also the requester
            $requesterIsLogged =  strcasecmp($requester, $loggedUn) == 0;
        }else{
            //if they are not friends set everything to null
            $statusCode = null;
            $requester = null;
            $addressee = null;
            $requesterIsLogged = false;
        }
        switch($statusCode){
            case 'R'://requested friendship
                //if the logged user is also the requester then add the necessary buttons to edit friendship
                if($requesterIsLogged){
                    $fullCard .= "<a class='btn btn-primary disabled'>Requested</a>
                        <a class='btn btn-outline-danger' href='".$this->generateLink('cancel', $requester, $addressee)."'><i class='bi bi-person-x-fill'></i> Cancel</a>";
                }else{//if the logged user is not the requester then add the necessary buttons to edit friendship
                    $fullCard .= "<a class='btn btn-outline-success' href='".$this->generateLink('accept', $requester, $addressee)."'><i class='bi bi-person-check-fill'></i> Accept</a>
                        <a class='btn btn-outline-danger' href='".$this->generateLink('decline', $requester, $addressee)."'><i class='bi bi-person-x-fill'></i>  Decline</a>
                        ";

                }
                break;
            case 'A'://accepted friendship (They are friends)
                $fullCard .= "
                        <a class='btn btn-success disabled'>Friends <i class='bi bi-heart-fill'></i></a>
                        <a class='btn btn-outline-danger' href='".$this->generateLink('cancel', $requester, $addressee)."'><i class='bi bi-person-x-fill'></i> Remove</a>
                    ";
                break;
            case 'D'://decline friendship
                $fullCard .= "<a class='btn btn-dark disabled'><i class='bi bi-x'></i>  Declined </a>
                    ";
                if(!$requesterIsLogged){
                    $fullCard .= "<a class='btn btn-outline-primary' href='".$this->generateLink('add2', $addressee, $requester)."'><i class='bi bi-person-plus-fill'></i> Add</a>";
                }
                break;
            case null://no friendship
                $fullCard .= "<a class='btn btn-outline-primary' href='".$this->generateLink('add', $loggedUn, $user->getUsername())."'><i class='bi bi-person-plus-fill'></i> Add</a>";
                break;
        }
        $fullCard .= "</div>";
        return $fullCard;
    }

    /**
     * @param string $action action to be performed on the frienship
     * @param string $requester username of the user that made the request
     * @param string $addressee username of the user that received the request
     * @return string link used to edit friendship
    */
    private function generateLink($action, $requester, $addressee){
        $base = "index.php?";
        if(isset($_GET['search'])){
            $base.= "search=".$_GET['search']."&";
        }
        return $base .= "friends=$action&requester=$requester&addressee=$addressee";
    }
}