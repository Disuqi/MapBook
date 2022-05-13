class Map{
    constructor(loggedUser, map, friends) {
        this.oldLoc = {};//dictionary of friends' old location to check if they moved
        this.map = map;//the Google Maps map
        this.loggedUser = loggedUser;//logged in user data
        this.myMarker = null;//the logged in user marker
        this.markers = {};//the logged in user friends' markers\
        this.makeFriendsMarkers(friends);//make markers for all the friends
    }

    makeFriendsMarkers(friends){
        if(friends == undefined){
            return
        }
        for(let i = 0; i < friends.length; i++){
            this.markers[friends[i].username] = this.createMarker(friends[i]);
            this.oldLoc[friends[i].username] = { lat: friends[i].lat, lng: friends[i].lng};//store their location for later user
        }
    }

    createMarker(user){
        //creating basic marker
        let marker = new google.maps.Marker(
            {
                position: { lat: user.lat, lng: user.lng},
                map: this.map,
                label: {
                    text: "\ue87d",
                    fontFamily: "Material Icons",
                    color: "white",
                    fontSize: "18px",
                }
            }
        );
        //making info window with the user's (friend) info
        const htmlContent = "" +
            "<div class='d-flex align-items-center justify-content-center'>" +
            "<img class='account m-2' src='"+ user.profileImage +"'>" +
            "<h5>" + user.username + "</h5>" +
            "</div>";
        const infoWindow = new google.maps.InfoWindow({
            content: htmlContent
        });

        //adding hovering listeners
        marker.addListener('mouseover', () => {
            infoWindow.open(marker.get("map"), marker);
        }, false);
        marker.addListener('mouseout', () => {
            infoWindow.close();
        }, false);

        //return the marker which will be added to the map
        return marker;
    }

    updateMarkers(friends){
        if(friends == undefined){
            return;
        }
        let movingFriends = [];
        //for each friend
        for(let i = 0; i < friends.length; i++){
            let username = friends[i].username;
            let friendLat = friends[i].lat;
            let friendLng = friends[i].lng;
            //if their location has changed then a notification needs to be made
            if(this.oldLoc[username].lat != friendLat || this.oldLoc[username].lng != friendLng){
                movingFriends.push(friends[i]);//push the friends position in the list
                this.oldLoc[username] = {lat : friendLat, lng : friendLng};//update their location for the next time they move
            }
            if(username in this.markers){
                this.markers[username].setPosition({lat: friendLat, lng: friendLng});//update the markers position if the marker exists
            }else{
                this.markers[username] = createMarker(this.friends[i]);//create a marker if it doesnt exist
            }
        }
        Notifier.notify(movingFriends);//create the notification and notify the logged in user of the moving friends
        setTimeout(this.updateMarkers, 3000);//repeat this function after 3 seconds (3000 milliseconds)
    }

    center(lat, lng){
        this.map.setCenter({lat: lat, lng: lng});
    }

    getUserLat(){
        return this.loggedUser.lat;
    }

    getUserLng(){
        return this.loggedUser.lng;
    }

    setUserPos(lat, lng){
        this.loggedUser.lat = lat;
        this.loggedUser.lng = lng;
    }

    getMyMarker(){
        return this.myMarker;
    }

    createMyMarker(){
        this.myMarker = this.createMarker(this.loggedUser);
    }

    setMyMarkerPos(lat, lng){
        this.myMarker.setPosition({lat: lat, lng: lng});
    }


}