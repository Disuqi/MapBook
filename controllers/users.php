<?php
//checks if the user is seaching for something
if(isset($_GET['search'])){
    if(isset($_SESSION['loggedIn']))
    {
        $un = $_SESSION['username'];
        switch($_GET['search']){
            case 'friends'://looking for friends
                $view->title = "Friends";
                $view->usersList = $usersRepo->getAllStatusCode("A", $un);
                break;
            case 'requests'://looking for all the friend requests
                $view->title = "Requests";
                $view->usersList = $usersRepo->getAllStatusCode("R", $un);
                break;
            case 'declined'://looking for declined requests
                $view->title = "Declined";
                $view->usersList = $usersRepo->getAllStatusCode("D", $un);
                break;
            case ''://not looking for anything so just show all users
                $view->title = "All Users";
                $view->usersList = $usersRepo->getPageOfUsers(0);
                break;
            default:
                $view->title = "You searched for \"" . $_GET['search'] ."\"";
                $view->usersList = $usersRepo->search($_GET['search']);
                break;
        }
    }else{//anonymous search if not logged in
        if($_GET['search'] == ""){
            $view->title = "All Users";
            $view->usersList = $usersRepo->getAll();
        }else{
            $view->title = "You searched for \"" . $_GET['search'] ."\"";
            $view->usersList = $usersRepo->search2($_GET['search']);
        }
    }
}else {
    //if he is not searching for something then display all the registered users
    $view->title = "All Users";
    $view->usersList = $usersRepo->getPageOfUsers(0);
}

foreach($view->usersList as $user){
    if(isset($_SESSION['loggedIn'])){
        $friendship = $friendshipRepo->areFriends(["requesterId" => $_SESSION['username'], "addresseeId" => $user->getUsername()]);
        $user->setFriendship($friendship);
    }
}




