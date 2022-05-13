class Notifier{
    static notify(movingFriends){
        if(this.toast == null){
            this.toastBody = document.getElementById("movingFriends");
            this.toast = document.getElementById("movingFriendsToast");
            this.notification = new bootstrap.Toast(this.toast);
        }
        this.toastBody = "";
        //check if any friend has moved
        if(movingFriends.length != 0){
            this.toastBody.innerHTML = "";//empty the body of the notification

            movingFriends.forEach(function(user){//for each friend that has moved
                this.toastBody.innerHTML +=  "" +//create html notification
                    "<div class='d-flex align-items-center justify-content-between' style='width=100%'>" +
                    "<img class='account m-2' src='"+ user.profileImage +"'>" +
                    "<h5 class='m-2'>" + user.username + "</h5>" +
                    //create a button which when clicked will center the map on this friend
                    "<button class='btn btn-primary m-2' onclick='map.center("+user.lat+", "+user.lng+")'>See <i class=\"bi bi-eye-fill\"></i></button" +
                    "</div>";
            });

            this.notification.show();//show the notification
        }
    }
}