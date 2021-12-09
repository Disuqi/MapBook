<?php

//checking if a session has already been started, if not then start one
if(session_id() == ''){
    session_start();
}
//making an empty class that will be used in the view
$view = new stdClass();
$view->pageTitle = "Home";

//required later in the code
require_once('../models/Signer.php');
require_once('cookie.php');
require_once('../models/Repo.php');
require_once('../models/UsersRepo.php');
require_once('../models/ImagesRepo.php');
require_once('../models/FriendshipRepo.php');


$usersRepo = new UsersRepo();
$imagesRepo = new ImagesRepo();
$friendshipRepo = new FriendshipRepo();
$users = $usersRepo->getAll();
$view->userList = "";

require_once('friendship.php');

foreach ($users as $user){
    $un = $user->getUsername();
    $pImageDTO = $imagesRepo->getProfileImage($un);//profile image dto
    $loggedUn = $_SESSION['username'];
    if($pImageDTO == null){
        $profileImage = "../images/noProfilePic.svg";
    }else{
        $profileImage = $pImageDTO->getImagePath();
    }
    if(strtolower($loggedUn) != strtolower($un)) {
        $view->userList .= "
            <div class='card userCard'>
                <img src=". $profileImage ." class='card-img-top' style='max-height: 10rem; object-fit: contain' alt='profileImage'>
                <div class='card-body'>
                <h5>@$un</h5>
            ";

        if($_SESSION['loggedIn']){
            $view->userList .= "
                                <h6 class='card-subtitle text-muted'>Name</h6>
                                <p>".$user->getFirstName()." ". $user->getLastName()."</p>
                                <h6 class='card-subtitle text-muted'>Email</h6>
                                <p>".$user->getEmail()."</p>
                                <h6 class='card-subtitle text-muted'>Position</h6>
                                <p>(".$user->getLat().", ". $user->getLng() .")</p>
                                <div class='container-fluid text-center'>
                                ";
            $friendship = $friendshipRepo->areFriends(["requesterId" => $loggedUn, "addresseeId" => $un]);
            $statusCode = $friendship == null? null : $friendship->getStatusCode();
            switch($statusCode){
                case 'R':
                    if($friendship->getRequesterId() == $loggedUn){
                        $view->userList .= "<a class='btn btn-primary disabled'>Requested</a>
                        <a class='btn btn-outline-danger' href='index.php?friends=cancel&requester=".$friendship->getRequesterId()."&addressee=".$friendship->getAddresseeId()."'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-person-x-fill' viewBox='0 0 16 16'>
                          <path fill-rule='evenodd' d='M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm6.146-2.854a.5.5 0 0 1 .708 0L14 6.293l1.146-1.147a.5.5 0 0 1 .708.708L14.707 7l1.147 1.146a.5.5 0 0 1-.708.708L14 7.707l-1.146 1.147a.5.5 0 0 1-.708-.708L13.293 7l-1.147-1.146a.5.5 0 0 1 0-.708z'/>
                        </svg> Cancel</a>";
                    }else{
                        $view->userList .= "<a class='btn btn-outline-success' href='index.php?friends=accept&requester=".$friendship->getRequesterId()."&addressee=".$friendship->getAddresseeId()."'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-person-check-fill' viewBox='0 0 16 16'>
                          <path fill-rule='evenodd' d='M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0z'/>
                          <path d='M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z'/>
                        </svg> Accept</a>
                        <a class='btn btn-outline-danger' href='index.php?friends=decline&requester=".$friendship->getRequesterId()."&addressee=".$friendship->getAddresseeId()."'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-person-x-fill' viewBox='0 0 16 16'>
                          <path fill-rule='evenodd' d='M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm6.146-2.854a.5.5 0 0 1 .708 0L14 6.293l1.146-1.147a.5.5 0 0 1 .708.708L14.707 7l1.147 1.146a.5.5 0 0 1-.708.708L14 7.707l-1.146 1.147a.5.5 0 0 1-.708-.708L13.293 7l-1.147-1.146a.5.5 0 0 1 0-.708z'/>
                        </svg> Decline</a>
                        ";

                    }
                    break;
                case 'A':
                    $view->userList .= "
                        <a class='btn btn-success disabled'>Friends</a>
                        <a class='btn btn-outline-danger' href='index.php?friends=cancel&requester=".$friendship->getRequesterId()."&addressee=".$friendship->getAddresseeId()."'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-person-x-fill' viewBox='0 0 16 16'>
                          <path fill-rule='evenodd' d='M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm6.146-2.854a.5.5 0 0 1 .708 0L14 6.293l1.146-1.147a.5.5 0 0 1 .708.708L14.707 7l1.147 1.146a.5.5 0 0 1-.708.708L14 7.707l-1.146 1.147a.5.5 0 0 1-.708-.708L13.293 7l-1.147-1.146a.5.5 0 0 1 0-.708z'/>
                        </svg> Remove</a>
                    ";
                    break;
                case 'D':
                    if($friendship->getRequesterId() == $loggedUn){
                        $view->userList .= "<p>Declined :(</p>";
                    }else{
                        $view->userList .= "<p>Declined :(</p>";
                    }
                    break;
                case null:
                    $view->userList .= "<a class='btn btn-outline-primary' href='index.php?friends=add&requester=$loggedUn&addressee=$un'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-person-plus-fill' viewBox='0 0 16 16'>
                      <path d='M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z'/>
                      <path fill-rule='evenodd' d='M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z'/>
                      </svg> Add</a>";
                    break;
            }
            $view->userList .= "</div>";
        }
        $view->userList .= "</div></div>";
    }
}

require_once("header.php");
require_once("../views/index.phtml");
