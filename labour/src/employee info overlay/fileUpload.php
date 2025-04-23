<?php
if (isset($_POST['immediate'])) {
    require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
    security_check_and_connect("POST", ['id']);
    $result = image_validate($_FILES['upload']);
    if ($result[0]) {
        $resultcodesave = save_user_image($_POST['id'], $_FILES['upload']);
        if ($resultcodesave[0]) // success saving and compressing image
            terminate(json_encode($resultcodesave), 200);
        else
            terminate(json_encode($resultcodesave), 500);
    } else {
        terminate(json_encode($result), 400);
    }
}

// validates file array, used on uploading background image and upload employee image
function image_validate($file)
{
    $check = array(false, "Server error when uploading image"); //Default returned value
    if (file_exists($file['tmp_name']) || is_uploaded_file($file['tmp_name'])) {
        $allowed = ["png", "jpg", "jpeg", "gif"];
        $imagetype = strtolower(end((explode(".", $file['name'])))); //Returns the values in the array, or something else
        if (in_array($imagetype, $allowed)) {
            if ($file['size'] < (10 * 1048576)) { //10megabytes, might want to make it smaller, first value is in mb
                $check = array(true, $imagetype);
            } else {
                $check[1] = "File too large, Max File size: 10Mb";
            }
        } else {
            $check[1] = "Incorrect file type ($imagetype), PNG, GIF or JPEG Allowed";
        }
    }
    return $check;
}

// crops, compressess, and saves employee image
// user id is the id of the employee
function save_user_image($userid, $file)
{
    $check = array(false, "Database Error"); //Default return value
    include $_SERVER['DOCUMENT_ROOT'] . "/database-con.php";
    $relpath = "img/emp/" . $userid . ".jpg"; //userid image path
    $path = $_SERVER['DOCUMENT_ROOT'] . "/labour/src/" . $relpath; //absolute path

    // max size of image
    $maxsize = 256;

    try {
        // create new Imagick object
        $image = new Imagick($file['tmp_name']);

        // Resizes to whichever is larger, width or height
        if ($image->getImageHeight() <= $image->getImageWidth()) {
            // Resize image using the lanczos resampling algorithm based on width
            $image->resizeImage($maxsize, 0, Imagick::FILTER_LANCZOS, 1);
            $min = $image->getImageHeight();
            $corner = ($maxsize - $min) / 2;
            if (!$image->cropImage($min, $min, $corner, 0)) {
                $check = array(false, "Image Crop Error");
                $pdo = null;
                return $check;
            }
        } else {
            // Resize image using the lanczos resampling algorithm based on height
            $image->resizeImage(0, $maxsize, Imagick::FILTER_LANCZOS, 1);
            $min = $image->getImageWidth();
            $corner = ($maxsize - $min) / 2;
            if (!$image->cropImage($min, $min, 0, $corner)) {
                $check = array(false, "Image Crop Error");
                $pdo = null;
                return $check;
            }
        }
        // Set to use jpeg compression
        $image->setImageCompression(Imagick::COMPRESSION_JPEG);
        // Set compression level (1 lowest quality, 100 highest quality)
        $image->setImageCompressionQuality(60);
        // Strip out unneeded meta data
        $image->stripImage();
    } catch (ImagickException $e) {
        $check = array(false, "Crop/Compression Error: " . var_dump($e));
        return $check;
    }

    // Check image size
    if ($image->getImageSize() > (5 * 1048576)) {
        $image->destroy();
        $check = array(false, "Image Compression Error: went big");
        $pdo = null;
        return $check;
    } else {
        // Check if file already exists
        if (file_exists($path)) {
            unlink($path);
        }

        // Writes resultant image to output directory
        $image->writeImage($path);
        // Destroys Imagick object, freeing allocated resources in the process
        $image->destroy();

        $query = "UPDATE employee SET img=? WHERE id=?"; //update the db
        $stmt = $pdo->prepare($query);
        $stmt->execute([$relpath, $userid]);
        $check[0] = true;
        $check[1] = "../" . $relpath;
    }
    $pdo = null; //return variables
    return $check;
}