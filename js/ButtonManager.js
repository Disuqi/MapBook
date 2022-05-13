class ButtonManager{

    constructor(loggedUn) {
        this.loggedUn = loggedUn;//username of the logged in user
        this.users = {};//all the cards currently being displayed, this will later be filled
    }

    getRequestedBtn(){//requested  disabled button no onclick event
        return "<button class='btn btn-primary disabled m-1'><i class=\"bi bi-envelope-check-fill\"></i> Requested</button>";
    }

    getDeclinedBtn(){//declined disabled button no onclick event
        return "<button class='btn btn-dark disabled m-1'><i class='bi bi-x'></i> Declined</button>";
    }

    getFriendsBtn(){//friends disabled button no onclick event
        return "<button class='btn btn-success disabled m-1'><i class='bi bi-heart-fill'></i> Friends</button>"
    }

    //add button, to request a friendship
    getAddBtn(user){
        this.users[user.username] = user;
        let html = "<button class='btn btn-outline-primary' id='"+user.username+"' "+
            "onclick='btnManager.friendship(\"add\", id)'" +
            "><i class='bi bi-person-plus-fill'></i> Add</button>";
        return html;
    }
    //cancel button, to cancel a request sent
    getCancelBtn(user){
        this.users[user.username] = user;
        let html = "<button class='btn btn-outline-danger m-1' id='"+user.username+"' " +
            "onclick='btnManager.friendship(\"cancel\", id)'" +
            "><i class='bi bi-person-x-fill'></i> Cancel</button>";
        return html;
    }
    // remove button to remove a friend
    getRemoveBtn(user){
        this.users[user.username] = user;
        let html =   "<button class='btn btn-outline-danger m-1' id='"+user.username+"' " +
            "onclick='btnManager.friendship(\"cancel\", id)'" +
            "><i class=\"bi bi-heartbreak-fill\"></i> Remove</button>"
        return html;
    }
    //accept button to accept a request
    getAcceptBtn(user){
        this.users[user.username] = user;
        let html = "<button class='btn btn-outline-success m-1' id='"+user.username+"' " +
            "onclick='btnManager.friendship(\"accept\", id)'" +
            "><i class='bi bi-person-check-fill'></i> Accept</button>";
        return html;
    }

    //decline button to decline a request
    getDeclineBtn(user){
        this.users[user.username] = user;
        let html = "<button class='btn btn-outline-danger m-1' id='"+user.username+"' " +
            "onclick='btnManager.friendship(\"decline\", id)'" +
            "><i class='bi bi-person-x-fill'></i>  Decline</button>";
        return html;
    }

    //sends a request to the server to update the database and the friendship status
    friendship(action, id){
        //if the friendship does not exist then the requester will be the logged in user and the addressee will be the other user
        let user = this.users[id];
        let requester = this.loggedUn;
        let addressee = user.username;
        if(user.statusCode != "null"){//if there is already a friendship just get the data
            requester = user.requester;
            addressee = user.addressee;
        }
        let xhr = new XMLHttpRequest();//creating http request
        xhr.open("GET", "../controllers/friendship.php?friends=" + action + "&requester=" + requester + "&addressee=" + addressee);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                if (xhr.status == 200) {//the request was successful and the friendship status has been updated
                    let html = "";
                    switch(action){//check what has been done and display the appropriate buttons
                        case "add"://a request has been sent
                            html = btnManager.getRequestedBtn() +
                                btnManager.getCancelBtn(user);
                            break;
                        case "accept"://friendship has been accepted
                            html = btnManager.getFriendsBtn() + btnManager.getRemoveBtn(user);
                            break;
                        case "decline"://friendship has been declined
                            html = btnManager.getDeclinedBtn(user) + btnManager.getAddBtn(user);
                            break;
                        case "cancel"://friendship has been removed or deleted
                            html = btnManager.getAddBtn(user);
                            break;
                        default://no changed so just display an add button (the default)
                            html = btnManager.getAddBtn(user);
                            break;
                    }
                    document.getElementById(user.username).innerHTML = html;
                } else {
                    console.log("Error: " + xhr.status);
                }
            }
        }
        xhr.send(null);
    }
}