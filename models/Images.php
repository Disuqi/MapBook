<?php
class Images{

    public function addImage($un, $id){
        $uploadDir = "../images/" . $un . '/';
        if(!file_exists($uploadDir)) {
            mkdir($uploadDir);
        }
        $uploadedFile = $_FILES['image']['name'];
        if($uploadedFile != ""){
            $ext = pathinfo($uploadedFile, PATHINFO_EXTENSION);
            $uploadFile = $uploadDir . $id . ".". $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile);
            if(file_exists($uploadFile)){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function deleteImage($un, $id){
        $file = "../images/" . $un . '/' . $id;
        unlink($file);
        if(!file_exists($file)) {
            return true;
        }
        else{
            return false;
        }
    }

    public function renameDirectory($oldUsername, $newUsername){
        $oldDir = "../images/" . $oldUsername . "/";
        $newDir = "../images/" . $newUsername . "/";
        if(file_exists($oldDir)){
            rename($oldDir, $newDir);
            if(file_exists($newDir)){
                return true;
            }
        }else{
            return false;
        }
    }

    public function deleteDirectory($un) {
        $dirname = "../images/" . $un;
        if (is_dir($dirname))
            $dir_handle = opendir($dirname);
        if (!$dir_handle)
            return false;
        while($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname."/".$file))
                    unlink($dirname."/".$file);
                else
                    delete_directory($dirname.'/'.$file);
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
    }
}