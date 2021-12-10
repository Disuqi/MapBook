<?php
require_once('../models/Repo.php');
require_once('../models/UsersRepo.php');
require_once('../models/FriendshipRepo.php');
$usersRepo = new UsersRepo();
$friendshipRepo = new FriendshipRepo();
$requesterId = $_GET['requester'];
$addresseeId = $_GET['addressee'];
$pk = ['requesterId' => $requesterId, 'addresseeId' => $addresseeId];
switch($_GET['friends']){
    case 'add':
        if($usersRepo->objectExists($requesterId) && $usersRepo->objectExists($addresseeId) && $friendshipRepo->areFriends($pk) == null){
            $friendshipRepo->addObject($pk);
        }
        break;
    case 'add2':
        if($friendshipRepo->areFriends($pk) != null) {
            $friendshipRepo->deleteFriendship($pk);
            $friendshipRepo->addObject($pk);
        }
        break;
    case 'cancel':
        if($friendshipRepo->objectExists($pk)){
            $friendshipRepo->deleteObject($pk);
        }
        break;
    case 'decline':
        if($friendshipRepo->objectExists($pk)){
            $friendshipRepo->updateStatus($pk, 'D');
        }
        break;
    case 'accept':
        if($friendshipRepo->objectExists($pk)){
            $friendshipRepo->updateStatus($pk, 'A');
        }
        break;
}