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
            // Apply Watermarker plugin logic if active
            $active_plugins = PluginModel::activePlugins($user_id);
            $watermark_active = false;
            foreach ($active_plugins as $plugin) {
                if ($plugin->plugin_name === 'Watermarker') {
                    $watermark_active = true;
                    break;
                }
            }

            if ($watermark_active) {
                $info = getimagesize($target);
                $mime = $info['mime'];
                $image = null;

                if ($mime == 'image/jpeg') {
                    $image = imagecreatefromjpeg($target);
                } elseif ($mime == 'image/png') {
                    $image = imagecreatefrompng($target);
                }

                if ($image) {
                    $user_name = Session::get('user_name') ? Session::get('user_name') : 'User';
                    $watermark_text = "@" . $user_name;

                    $font_size = 5;
                    $font_width = imagefontwidth($font_size);
                    $font_height = imagefontheight($font_size);
                    $text_width = strlen($watermark_text) * $font_width;

                    $x = imagesx($image) - $text_width - 15;
                    $y = imagesy($image) - $font_height - 15;

                    if ($x < 0) $x = 10;
                    if ($y < 0) $y = 10;

                    // Black semi-transparent background block for readability
                    $bg_color = imagecolorallocatealpha($image, 0, 0, 0, 80);
                    imagefilledrectangle($image, $x - 5, $y - 3, $x + $text_width + 5, $y + $font_height + 3, $bg_color);

                    // White text
                    $text_color = imagecolorallocate($image, 255, 255, 255);
                    imagestring($image, $font_size, $x, $y, $watermark_text, $text_color);

                    if ($mime == 'image/jpeg') {
                        imagejpeg($image, $target, 90);
                    } elseif ($mime == 'image/png') {
                        imagepng($image, $target);
                    }
                    imagedestroy($image);
                }
            }

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

    public static function downloadImage($id)
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

        if (!file_exists($path)) {
            die("File not found on server");
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($path);

        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . $image->filename . '"');
        header('Content-Length: ' . filesize($path));

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
