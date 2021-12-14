<?php
class Images{

    /**
     * @param string $un username
     * @param int $id id of image
     * @return bool if everything went well and false if there was a problem
    */
    public function addImage($un, $id){
        $uploadDir = "../images/" . $un . '/';
        if(!file_exists($uploadDir)) {
            mkdir($uploadDir);
        }
        $uploadedFile = $_FILES['image']['name'];
        $ext = pathinfo($uploadedFile, PATHINFO_EXTENSION);
        $uploadFile = $uploadDir . $id . ".". $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile);
        if(file_exists($uploadFile)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param string $un username
     * @param int $id id of image
     * @param string $ext extension of image e.g. png or jpg
     * @return bool if everything went well and false if there was a problem
     */
    public function deleteImage($un, $id, $ext){
        $file = "../images/" . $un . '/' . $id . '.' . $ext;
        unlink($file);
        if(!file_exists($file)) {
            return true;
        }
        else{
            return false;
        }
    }
    /**
     * @param string $oldUsername
     * @param string $newUsername
     * @return bool if everything went well and false if there was a problem
     */
    //renames the folder of a user to the new username
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

    /**
     * @param string $un username
     * @return bool if everything went well and false if there was a problem
     */
    //deletes a folder and all it's content of a certain user
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