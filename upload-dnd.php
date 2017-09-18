<?php
global $error,$errmsg,$filename,$filetype,$filesize,$filepath,$tmpfile;

$error     = 0;
$errmsg    = "success";
$filename  = "";
$filetype  = "";
$filesize  = "";
$filepath  = "";
$tmpfile   = "";

// Check if the form was submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if file was uploaded without errors
    if(isset($_FILES["uploadfile"]) && $_FILES["uploadfile"]["error"] == 0) {

        $allowed = array("htm" => "text/html", "html" => "text/html", "md" => "text/html", "txt" => "text/plain");

        $filename = $_FILES["uploadfile"]["name"];

        // NOTE: This is the browser supplied file type, which 
        // is totally wrong in most cases. Don't use it. It will 
        // be a LIE! Instead check the file type of the file
        // after it's been uploaded to the temporary folder.
        //
        // see : https://stackoverflow.com/questions/1201945/how-is-mime-type-of-an-uploaded-file-determined-by-browser
        // 
        $tmpfile  = $_FILES["uploadfile"]["tmp_name"];
        // NOTE: The exact location is determined by settings in your HTTP server.
    
        $filesize = $_FILES["uploadfile"]["size"];
        $filepath = $_POST["path"];

        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        // This is the real file type...
        $filetype = mime_content_type($tmpfile);

        // is the extension allowed?
        if(!array_key_exists($ext, $allowed)) {
            $errmsg = "Please select a valid file format, {$ext} is not allowed";
            $error  = -5;
        } else {
            // Verify file size - 100k maximum
            $maxsize = 100 * 1024;
            if($filesize > $maxsize) {
                $errmsg = "File size of {$filesize} is larger than the allowed limit of {$maxsize}";
                $error  = -6;
            } else {
                // Verify MIME type of the file
                if(in_array($filetype, $allowed)){
                    // Check whether file exists before uploading it
                    if(file_exists($filepath . $filename)) {
                        $errmsg = "{$filename} already exists in {$filepath}";
                        $error  = -1;
                    } else {
                        if(move_uploaded_file($_FILES["uploadfile"]["tmp_name"], $filepath . $filename)) {
                            $errmsg = "The file {$filename} uploaded successfully";
                            $error  = 0;
                        } else {
                            $errmsg = "The file {$filename} could not be moved to {$filepath}";
                            $error  = -7;
                        }
                    } 
                } else {
                    $errmsg = "the file type {$filetype} is not allowed";
                    $error  = -2;
                }
            }
        }
    } else {
        $errmsg = "upload error - {$_FILES["uploadfile"]["error"]}";
        $error  = -3;
    }
} else {
    $errmsg = "bad request - {$_SERVER["REQUEST_METHOD"]}";
    $error  = -4;
}

respond();

/*
    Responds to the client with a JSON string
*/
function respond() {
    global $error,$errmsg,$filename,$filetype,$filesize,$filepath,$tmpfile;
    echo "{\"detail\": {\"file\": \"{$filename}\", \"type\": \"{$filetype}\", \"size\": {$filesize}, \"path\": \"{$filepath}\", \"status\": {\"msg\": \"{$errmsg}\", \"code\": {$error}}}}";
}
?>
