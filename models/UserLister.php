<?php
require_once('Repo.php');
require_once('UsersRepo.php');
require_once('ImagesRepo.php');
require_once('FriendshipRepo.php');
class UserLister{

    protected $usersRepo, $imagesRepo, $friendshipRepo;

    public function __construct()
    {
        $this->usersRepo = new UsersRepo();
        $this->imagesRepo = new ImagesRepo();
        $this->friendshipRepo = new FriendshipRepo();
    }

    public function getAllUsers(){
        $users = $this->usersRepo->getPageOfUsers(0);
        return $this->makeCards($users);
    }

    public function getLastPageNumber(){
        return $this->usersRepo->getLastPageNumber();
    }

    public function getPageOfUsers($page){
        $users = $this->usersRepo->getPageOfUsers($page-1);
        return $this->makeCards($users);
    }

    public function getAllRequests($username){
        $users = $this->usersRepo->getAllStatusCode("R", $username);
        $result = $this->makePageTitle('Requests', count($users));
        $result .= $this->makeCards($users);
        return $result;
    }
    public function getAllDeclined($username){
        $users = $this->usersRepo->getAllStatusCode("D", $username);
        $result = $this->makePageTitle('Declined', count($users));
        $result .= $this->makeCards($users);
        return $result;
    }
    public function getRequests($username){
        $users = $this->usersRepo->getRequests($username);
        return $this->makeListItems($users);
    }
    public function getFriends($username){
        $users = $this->usersRepo->getAllStatusCode("A", $username);
        $result = $this->makePageTitle('Friends', count($users));
        $result .= $this->makeCards($users);
        return $result;
    }

    public function search($data){
        $users = $this->usersRepo->search($data);
        $result = $this->makePageTitle('You searched for "' . $data . '"', count($users));
        $result .= $this->makeCards($users);
        return $result;
    }

    public function anonymousSearch($username){
        $users = $this->usersRepo->search2($username);
        $result = $this->makePageTitle('You searched for "' . $username . '"', count($users));
        $result .= $this->makeCards($users);
        return $result;
    }

    private function makePageTitle($title, $numOfUsers){
        $needS = $numOfUsers == 1 ? null : 's';
        $pageTitle = '<div class="container-fluid" style="margin-top: 80px; margin-left: 5px"><h1>'.$title.'</h1><h5>'.$numOfUsers.' result' .$needS.'</h5></div>';
        if($numOfUsers == 0){
            return null;
        }else{
            return $pageTitle;
        }
    }

    private function makeListItems($users){
        $usersList = "";
        if($users == null){
            $usersList .= "<li class='listItem'>No Requests <i style='font-size: 20px' class='bi bi-inbox'></i></li>";
        }
        foreach($users as $user){
            $un = $user->getUsername();
            $prImageDTO = $this->imagesRepo->getProfileImage($un);//profile image dto
            $loggedUn = $_SESSION['username'];
            if($prImageDTO == null){
                $profileImage = "../images/noProfilePic.svg";
            }else{
                $profileImage = $prImageDTO->getImagePath();
            }
            //<li><a class="dropdown-item" href="../../controllers/index.php?account=signOut">Sign out</a></li>
            $usersList .= "<li class='listItem'>$un
                <a class='btn btn-outline-success' href='".$this->generateLink('accept', $un, $loggedUn)."'><i class='bi bi-person-check-fill'></i></a>
                        <a class='btn btn-outline-danger' href='".$this->generateLink('decline', $un, $loggedUn)."'><i class='bi bi-person-x-fill'></i></a>
                </li>";
        }
        return $usersList;
    }

    private function makeCards($users){
        if($users == null){
            return null;
        }
        $usersList = "<div class='row justify-content-start m-0'>";
        foreach ($users as $user){
            $un = $user->getUsername();
            $prImageDTO = $this->imagesRepo->getProfileImage($un);//profile image dto
            $loggedUn = isset($_SESSION['username'])? $_SESSION['username'] : null;
            if($prImageDTO == null){
                $profileImage = "../images/noProfilePic.svg";
            }else{
                $profileImage = $prImageDTO->getImagePath();
            }
            if(strcasecmp($loggedUn, $un) != 0) {
                $usersList .= "
                    <div class='card userCard'>
                        <img src=". $profileImage ." class='card-img-top' style='max-height: 10rem; object-fit: contain' alt='profileImage'>
                        <div class='card-body'>
                        <h5>@$un</h5>
                    ";
                if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']){
                    $usersList = $this->fullCard($usersList, $user, $loggedUn);
                }
                $usersList .= "</div></div>";
            }
        }
        $usersList .= '</div>';
        return $usersList;
    }

    private function fullCard($usersList, $user, $loggedUn){
        $usersList .= "
                                <h6 class='card-subtitle text-muted'>Name</h6>
                                <p>".$user->getFullName()."</p>
                                <h6 class='card-subtitle text-muted'>Email</h6>
                                <p>".$user->getEmail()."</p>
                                <h6 class='card-subtitle text-muted'>Position</h6>
                                <p>".$user->getPosition()."</p>
                                <div class='d-flex  align-items-center justify-content-center text-center'>
                                ";

        $friendship = $this->friendshipRepo->areFriends(["requesterId" => $loggedUn, "addresseeId" => $user->getUsername()]);
        if($friendship != null){
            $requester = $friendship->getRequesterId();
            $addressee = $friendship->getAddresseeId();
            $statusCode = $friendship->getStatusCode();
            $requesterIsLogged =  strcasecmp($requester, $loggedUn) == 0;
        }else{
            $statusCode = null;
            $requester = null;
            $addressee = null;
            $requesterIsLogged = false;
        }
        switch($statusCode){
            case 'R':
                if($requesterIsLogged){
                    $usersList .= "<a class='btn btn-primary disabled'>Requested</a>
                        <a class='btn btn-outline-danger' href='".$this->generateLink('cancel', $requester, $addressee)."'><i class='bi bi-person-x-fill'></i> Cancel</a>";
                }else{
                    $usersList .= "<a class='btn btn-outline-success' href='".$this->generateLink('accept', $requester, $addressee)."'><i class='bi bi-person-check-fill'></i> Accept</a>
                        <a class='btn btn-outline-danger' href='".$this->generateLink('decline', $requester, $addressee)."'><i class='bi bi-person-x-fill'></i>  Decline</a>
                        ";

                }
                break;
            case 'A':
                $usersList .= "
                        <a class='btn btn-success disabled'>Friends <i class='bi bi-heart-fill'></i></a>
                        <a class='btn btn-outline-danger' href='".$this->generateLink('cancel', $requester, $addressee)."'><i class='bi bi-person-x-fill'></i> Remove</a>
                    ";
                break;
            case 'D':
                $usersList .= "<a class='btn btn-dark disabled'><i class='bi bi-x'></i>  Declined </a>
                    ";
                if(!$requesterIsLogged){
                    $usersList .= "<a class='btn btn-outline-primary' href='".$this->generateLink('add2', $addressee, $requester)."'><i class='bi bi-person-plus-fill'></i> Add</a>";
                }
                break;
            case null:
                $usersList .= "<a class='btn btn-outline-primary' href='".$this->generateLink('add', $loggedUn, $user->getUsername())."'><i class='bi bi-person-plus-fill'></i> Add</a>";
                break;
        }
        $usersList .= "</div>";
        return $usersList;
    }

    private function generateLink($action, $requester, $addressee){
        $base = "index.php?";
        if(isset($_GET['search'])){
            $base.= "search=".$_GET['search']."&";
        }
        return $base .= "friends=$action&requester=$requester&addressee=$addressee";
    }
}