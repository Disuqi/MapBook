<?php
//display the login button or account details depending on whether the user is signed in or not
if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']){
    $view->signInBtn = '
                        <div class="d-flex justify-content-end">
                            <div class="dropdown dropstart">
                              <button class="btn linker" type="button" data-bs-toggle="dropdown" style="height: 50px;" aria-expanded="false"><i style="font-size: 30px" class="bi bi-people-fill"></i></button>
                              <ul class="dropdown-menu dropdown-menu-dark text-end" style="right: calc(50px + 0.4vw)" aria-labelledby="account">
                                '. $userLister->getRequests($_SESSION['username']).'
                              </ul>
                            </div>
                            <div class="dropdown dropstart">
                              <button class="btn account" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background-image: url(' . $_SESSION['profileImage'] . '); margin-right: 0.5vw"></button>
                              <ul class="dropdown-menu dropdown-menu-dark text-end" style="right: calc(50px + 0.4vw)" aria-labelledby="account">
                                <li class="dropdown-item disabled">@'.$_SESSION['username'].'</li>
                                <li><a class="dropdown-item" href="account.php">Account Settings <i class="bi bi-gear-wide-connected"></i></a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../../controllers/index.php?account=signOut">Sign out <i class="bi bi-box-arrow-right"></i></a></li>
                              </ul>
                            </div>
                        </div>
                    ';
} else{
    $view->signInBtn = '
                        <div class="p-1 btn-group">
                                <a class="btn signInBtn" href="signIn.php" >Sign In</a>
                                <a class="btn signInBtn dropdown-toggle-split" href="signUp.php">Up</a>
                        </div>
                    ';
}

//checking if someone is trying to log out of delete the account
if(isset($_GET['account']) && isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']){
    switch ($_GET['account']){
        case "signOut":
            //Siging out the account
            $signer = new Signer();
            $signer->signOut();
            break;
        case "delete":
            //Deleting Account
            //removing user from database

            $userRepo = new UsersRepo();
            $imageRepo = new ImagesRepo();
            $friendshipRepo = new FriendshipRepo();
            $signer = new Signer();
            $un = $_SESSION['username']; //abbrv for username

            //deleting all friendships from database
            $friendshipRepo->deleteAccount($un);
            //deleting Images from database
            $imageRepo->deleteAllImagesOfUser($un);
            //deleting account from database
            $userRepo->deleteObject($un);

            //deleting directory and all images inside
            require_once("../models/Images.php");
            $imageHandler = new Images();
            $imageHandler ->deleteDirectory($un);

            //Signing out
            $signer->signOut();
            break;
    }
}