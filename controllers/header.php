<?php
//checking if someone is trying to log out of delete the account
if(isset($_SESSION['loggedIn'])){
    $view->requests = $usersRepo->getRequests($_SESSION['username']);
}
if(isset($_GET['account']) && isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']){
    switch ($_GET['account']){
        case "signOut":
            //Siging out the account
            $signer->signOut();
            break;
        case "delete":
            //Deleting Account
            //removing user from database
            $un = $_SESSION['username']; //abbrv for username

            //deleting all friendships from database
            $friendshipRepo->deleteAccount($un);
            //deleting Images from database
            $imagesRepo->deleteAllImagesOfUser($un);
            //deleting account from database
            $usersRepo->deleteObject($un);

            //deleting directory and all images inside
            $imageHandler ->deleteDirectory($un);

            //Signing out
            $signer->signOut();
            break;
    }
}