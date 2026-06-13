<?php

class GalleryModel
{
    public static function getAllImages()
    {
        $db = DatabaseFactory::getFactory()->getConnection();
        $user_id = Session::get('user_id');

        // Get own images AND public images from others
        $sql = "SELECT * FROM gallery WHERE owner_id = :u OR is_shared = 1";
        $query = $db->prepare($sql);
        $query->execute(array(':u' => $user_id));

        return $query->fetchAll();
    }

    public static function uploadImage()
    {
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $user_id = Session::get('user_id');
        $name = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];

        // Build path
        $folder = Config::get('PATH_GALLERY') . $user_id . "/";
        
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $target = $folder . time() . "_" . $name;
        $filename = time() . "_" . $name;

        if (move_uploaded_file($tmp, $target)) {
            $db = DatabaseFactory::getFactory()->getConnection();
            $sql = "INSERT INTO gallery (owner_id, filename, is_shared) VALUES (:owner, :file, 0)";
            $query = $db->prepare($sql);
            $query->execute(array(':owner' => $user_id, ':file' => $filename));
            return true;
        }
        return false;
    }

    public static function displayImage($id)
    {
        $db = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT * FROM gallery WHERE image_id = :id LIMIT 1";
        $query = $db->prepare($sql);
        $query->execute(array(':id' => $id));
        $image = $query->fetch();

        if (!$image) {
            die("Image not found");
        }

        $path = Config::get('PATH_GALLERY') . $image->owner_id . "/" . $image->filename;
        
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($path);

        header("Content-Type: " . $mime);
        readfile($path);
        exit;
    }

    public static function deleteImage($id)
    {
        $db = DatabaseFactory::getFactory()->getConnection();
        $user_id = Session::get('user_id');

        $sql = "SELECT * FROM gallery WHERE image_id = :id AND owner_id = :user LIMIT 1";
        $query = $db->prepare($sql);
        $query->execute(array(':id' => $id, ':user' => $user_id));
        $image = $query->fetch();

        if ($image) {
            $path = Config::get('PATH_GALLERY') . $user_id . "/" . $image->filename;
            if (file_exists($path)) {
                unlink($path);
            }
            $sql_del = "DELETE FROM gallery WHERE image_id = :id";
            $query_del = $db->prepare($sql_del);
            $query_del->execute(array(':id' => $id));
        }
    }

    public static function toggleShared($id)
    {
        $db = DatabaseFactory::getFactory()->getConnection();
        $user_id = Session::get('user_id');

        $sql = "SELECT * FROM gallery WHERE image_id = :id AND owner_id = :user LIMIT 1";
        $query = $db->prepare($sql);
        $query->execute(array(':id' => $id, ':user' => $user_id));
        $image = $query->fetch();

        if ($image) {
            $new_val = ($image->is_shared == 1) ? 0 : 1;
            $sql_upd = "UPDATE gallery SET is_shared = :val WHERE image_id = :id";
            $query_upd = $db->prepare($sql_upd);
            $query_upd->execute(array(':val' => $new_val, ':id' => $id));
        }
    }
}
