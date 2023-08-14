class CardManager{
    //initializing everything
    constructor(btnManager, loggedUn, cardsContainer) {
        this.btnManager = btnManager;//button manager deals with buttons and onclicks
        this.loggedUn = loggedUn;//if loggedIn this will be the username otherwise it will be blank
        this.cardsContainer = cardsContainer;//simply the HTML DOM container where the cards will be inserted
    }

    addUserCards(users){
        let html = "";//empty string which will be filled and added to the container
        for(let i = 0; i < users.length; i++){//for loop that goes through every user given to the function
            //this is done for simplicity and legibility
            let user = users[i];
            //if the user is the logged in user then do not make a card for him and go to the next user
            if(this.loggedUn == user.username) continue;
            //html elements with the user's information
            html += "<div class='card userCard'>" +
                "<img src='"+ user.profileImage +"' class='card-img-top cardProfileImage' alt='profileImage'/>" +
                "<div class='card-body d-flex flex-column'><h5>@" + user.username + "</h5>";

            if(this.loggedUn != ""){
                let loc = "N/A"
                if (user.statusCode == 'A') {//if the user is a friend then add the location
                    loc = "(" + user.lat.toFixed(3)+", "+user.lng.toFixed(3) + ")";
                }

                html += "<div id='"+user.username+"Info'><h6 class='card-subtitle text-muted'>Name</h6><p>" + user.firstName + " " + user.lastName + "</p>" +
                    "<h6 class='card-subtitle text-muted'>Email</h6><p>" + user.email + "</p></div>" + 
                    "<h6 class='card-subtitle text-muted'>Location</h6><p>"+loc+"</p>"+
                    "<div class='d-flex align-items-center justify-content-center text-center m-auto' id='"+user.username+"' >";
                //depending on the friendship status different buttons will be displayed, what the buttons can do is dealt with in the button manager class
                switch(user.statusCode){
                    case 'R'://requested by the logged in user
                        if(this.loggedUn == user.requester){
                            html += this.btnManager.getRequestedBtn() + this.btnManager.getCancelBtn(user);
                        }else{//else therefore requested by the other user
                            html += this.btnManager.getAcceptBtn(user) + this.btnManager.getDeclineBtn(user);
                        }
                        break;
                    case 'A'://accepted/friends
                        html += this.btnManager.getFriendsBtn() + this.btnManager.getRemoveBtn(user);
                        break;
                    case 'D'://declined
                        if(user.requester != this.loggedUn){//declined by the logged in user as he is not the requester
                            html += this.btnManager.getAddBtn(user);
                        }
                        html += this.btnManager.getDeclinedBtn();
                        break;
                    default://no friendship so simply have an add button
                        html += this.btnManager.getAddBtn(user);//
                        break;
                }
                html += "</div>";
            }
            html += "</div></div>";
        }
        //add everything to the HTML container
        this.cardsContainer.innerHTML += html;
    }
}