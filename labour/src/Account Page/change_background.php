<?php
if (isset($_SERVER["REQUEST_METHOD"])) { 
    require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
    security_check_and_connect("POST");

    // imported for image_validate() function
    require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/employee info overlay/fileUpload.php";

    //// validate image
    // no file chosen
    if ($_FILES["background_upload"]['size'] == 0) {
        terminate("No image selected", 400);
    }
    // all other checks
    $resultcode = image_validate($_FILES['background_upload']);
    if ($resultcode[0]) { //if okay
        $resultcodesave = save_background_image($_SESSION['user'], $_FILES['background_upload']);
        if ($resultcodesave[0]) // success saving and compressing image
            terminate("Background Image Uploaded Successfully", 200);
        else // error compressing and saving image
            terminate($resultcodesave[1], 500);
    } else { // if error validating image
        terminate($resultcode[1], 400);
    }
}
// crops, compressess, and saves background
// user id is the id of the user
function save_background_image($userid, $file)
{
    $check = array(false, "Database Error"); //Default return value
    $relpath = "img/backgrounds/" . $userid . ".jpg"; //userid image path
    $path = $_SERVER['DOCUMENT_ROOT'] . "/labour/src/" . $relpath; //absolute path

    // max image size
    $maxsize = 1440;

    // create new Imagick object
    try {
        $image = new Imagick($file['tmp_name']);

        // resize image to be max 1440p width
        if ($image->getImageWidth() > $maxsize) {
            $image->resizeImage($maxsize, 0, Imagick::FILTER_LANCZOS, 1);
        }
        // Set to use jpeg compression
        $image->setImageCompression(Imagick::COMPRESSION_JPEG);
        // Set compression level (1 lowest quality, 100 highest quality)
        $image->setImageCompressionQuality(90); // set to 90 for background image, still compresses 10MB file to 0.5MB
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

        $check[0] = true;
        $check[1] = "../" . $relpath;
    }
    return $check;
}
?>