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
        $users = $this->usersRepo->getAll();
        return $this->makeCards($users);
    }

    public function specificUser(){

    }

    public function search($data){
        $users = $this->usersRepo->search($data);
        return $this->makeCards($users);
    }

    public function getFriends($username){
        $users = $this->usersRepo->getFriends($username);
        return $this->makeCards($users);
    }

    private function makeCards($users){
        $usersList = "";
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
        return $usersList;
    }

    private function fullCard($usersList, $user, $loggedUn){
        $usersList .= "
                                <h6 class='card-subtitle text-muted'>Name</h6>
                                <p>".$user->getFirstName()." ". $user->getLastName()."</p>
                                <h6 class='card-subtitle text-muted'>Email</h6>
                                <p>".$user->getEmail()."</p>
                                <h6 class='card-subtitle text-muted'>Position</h6>
                                <p>(".$user->getLat().", ". $user->getLng() .")</p>
                                <div class='container-fluid text-center'>
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
                        <a class='btn btn-outline-danger' href='".$this->generateLink('cancel', $requester, $addressee)."'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-person-x-fill' viewBox='0 0 16 16'>
                          <path fill-rule='evenodd' d='M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm6.146-2.854a.5.5 0 0 1 .708 0L14 6.293l1.146-1.147a.5.5 0 0 1 .708.708L14.707 7l1.147 1.146a.5.5 0 0 1-.708.708L14 7.707l-1.146 1.147a.5.5 0 0 1-.708-.708L13.293 7l-1.147-1.146a.5.5 0 0 1 0-.708z'/>
                        </svg> Cancel</a>";
                }else{
                    $usersList .= "<a class='btn btn-outline-success' href='".$this->generateLink('accept', $requester, $addressee)."'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-person-check-fill' viewBox='0 0 16 16'>
                          <path fill-rule='evenodd' d='M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0z'/>
                          <path d='M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z'/>
                        </svg> Accept</a>
                        <a class='btn btn-outline-danger' href='".$this->generateLink('decline', $requester, $addressee)."'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-person-x-fill' viewBox='0 0 16 16'>
                          <path fill-rule='evenodd' d='M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm6.146-2.854a.5.5 0 0 1 .708 0L14 6.293l1.146-1.147a.5.5 0 0 1 .708.708L14.707 7l1.147 1.146a.5.5 0 0 1-.708.708L14 7.707l-1.146 1.147a.5.5 0 0 1-.708-.708L13.293 7l-1.147-1.146a.5.5 0 0 1 0-.708z'/>
                        </svg> Decline</a>
                        ";

                }
                break;
            case 'A':
                $usersList .= "
                        <a class='btn btn-success disabled'>Friends <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-heart-fill' viewBox='0 0 16 16'>
                          <path fill-rule='evenodd' d='M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z'/>
                        </svg></a>
                        <a class='btn btn-outline-danger' href='".$this->generateLink('cancel', $requester, $addressee)."'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-person-x-fill' viewBox='0 0 16 16'>
                          <path fill-rule='evenodd' d='M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm6.146-2.854a.5.5 0 0 1 .708 0L14 6.293l1.146-1.147a.5.5 0 0 1 .708.708L14.707 7l1.147 1.146a.5.5 0 0 1-.708.708L14 7.707l-1.146 1.147a.5.5 0 0 1-.708-.708L13.293 7l-1.147-1.146a.5.5 0 0 1 0-.708z'/>
                        </svg> Remove</a>
                    ";
                break;
            case 'D':
                $usersList .= "<a class='btn btn-dark disabled'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-x' viewBox='0 0 16 16'>
                      <path d='M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z'/>
                    </svg> Declined </a>
                    ";
                if(!$requesterIsLogged){
                    $usersList .= "<a class='btn btn-outline-primary' href='".$this->generateLink('add2', $addressee, $requester)."'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-person-plus-fill' viewBox='0 0 16 16'>
                      <path d='M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z'/>
                      <path fill-rule='evenodd' d='M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z'/>
                      </svg> Add</a>";
                }
                break;
            case null:
                $usersList .= "<a class='btn btn-outline-primary' href='".$this->generateLink('add', $loggedUn, $user->getUsername())."'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-person-plus-fill' viewBox='0 0 16 16'>
                      <path d='M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z'/>
                      <path fill-rule='evenodd' d='M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z'/>
                      </svg> Add</a>";
                break;
        }
        $usersList .= "</div>";
        return $usersList;
    }

    private function generateLink($action, $requester, $addressee){
        return "index.php?friends=$action&requester=$requester&addressee=$addressee";
    }
}