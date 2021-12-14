<?php
//checks if the user is seaching for something
if(isset($_GET['search'])){
    if(isset($_SESSION['loggedIn']))
    {
        switch($_GET['search']){
            case 'friends'://looking for friends
                $users = $userLister->getFriends($_SESSION['username']);
                $view->usersList = $users == null? "<div style='margin-top: 80px; margin-left: 20px'><i class='m-2 bi bi-emoji-frown' style='font-size: 25px'> No Friends</i></div>" :  $users;
                break;
            case 'requests'://looking for all the friend requests
                $users = $userLister->getAllRequests($_SESSION['username']);
                $view->usersList = $users == null? "<div style='margin-top: 80px; margin-left: 20px'><i class='m-2 bi bi-inbox-fill' style='font-size: 25px'> No Pending Requests</i></div>" :  $users;
                break;
            case 'declined'://looking for declined requests
                $users= $userLister->getAllDeclined($_SESSION['username']);
                $view->usersList = $users == null? "<div style='margin-top: 80px; margin-left: 20px'><i class='m-2 bi bi-inbox-fill' style='font-size: 25px'> No Declined Requests</i></div>" :  $users;
                break;
            case ''://not looking for anything so just show all users
                $view->usersList = '<h1 style="margin-top: 80px; margin-left: 20px">All Users</h1>';
                $users = $userLister->getAllUsers();
                $view->usersList .= $users == null ? "<div style='margin-top: 40px; margin-left: 20px'><i class='m-2 bi bi-inbox-fill' style='font-size: 25px'> No Users</i></div>" : $users;
                break;
            default:
                $searchResult = $userLister->search($_GET['search']);//searching for something specific
                $view->usersList = $searchResult == null? "<div style='margin-top: 80px; margin-left: 20px'><i class='m-2 bi bi-search' style='font-size: 25px'> No Users Found</i></div>" : $searchResult;
                break;
        }}else{//anonymous search if not loggedin
        $searchResult = $userLister->anonymousSearch($_GET['search']);
        $view->usersList = $searchResult == null? "<div style='margin-top: 80px; margin-left: 20px'><i class='m-2 bi bi-search' style='font-size: 25px'> No Users Found</i></div>" : $searchResult;
    }
}else {
    //if he is not searching for something then display all the registered users
    $view->usersList = '<h1 style="margin-top: 80px; margin-left: 20px">All Users</h1>';
    $view->usersList .= $userLister->getPageOfUsers($currentPage);
}