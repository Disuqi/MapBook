<?php
if(!isset($usersRepo)){
    require_once "init.php";
}
$requesterId = $_GET['requester'];
$addresseeId = $_GET['addressee'];
$pk = ['requesterId' => $requesterId, 'addresseeId' => $addresseeId];
//deals with friendship requests using $_GET
switch($_GET['friends']){
    case 'add':
        if($friendshipRepo->areFriends($pk) != null) {
            $friendshipRepo->deleteFriendship($pk);
        }
        if($usersRepo->objectExists($requesterId) && $usersRepo->objectExists($addresseeId)){
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