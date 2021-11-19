<?php
class Images{
    public function addProfileImage(){
        $uploaddir = "../images/" . $_POST['username'] . '/';
        $uploadfile = $uploaddir . $_POST['username'] . '_' . 1 . '.png';
        if(!file_exists($uploaddir)) {
            mkdir($uploaddir);
        }
        move_uploaded_file($_FILES['profileImage']['tmp_name'], $uploadfile);
        if(file_exists($uploadfile)){
            return true;
        }else{
            return false;
        }

    }
    public function addImage(){

    }
}