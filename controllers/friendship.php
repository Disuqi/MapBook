<?php
if(isset($_GET['friends'])){
    $requesterId = $_GET['requester'];
    $addresseeId = $_GET['addressee'];
    $pk = ['requesterId' => $requesterId, 'addresseeId' => $addresseeId];
    switch($_GET['friends']){
        case 'add':
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
}