<?php
class Images{
    public function addProfileImage(){
        $uploaddir = "../images/" . $_POST['username'] . '/';
        mkdir($uploaddir);
        if($_FILES['profileImage']['name'] != ""){
            $uploadfile = $uploaddir . $_POST['username'] . '_' . 1 . '.png';
            move_uploaded_file($_FILES['profileImage']['tmp_name'], $uploadfile);
            if(file_exists($uploadfile)){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }

    }
    public function addImage(){

    }

    public function getProfilePic(){

    }
}