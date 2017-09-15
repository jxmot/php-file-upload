<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Upload Form</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
</head>
<body>
<?php
// will be copied in to the event data payload a the end of this file
$error = 0;
$errmsg = "success";

// placed here due to needed scope
$filename  = "";
$filetype  = "";
$filesize  = "";
$filepath  = "";
$tmpfile   = "";
$ufiletype = "";

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
        // BAD :
        // $filetype = $_FILES["uploadfile"]["type"];
        // GOOD :
        $tmpfile  = $_FILES["uploadfile"]["tmp_name"];
        // UGLY: The exact location is determined by settings 
        // in your HTTP server.
    
        // This is the real file type...
        $filetype = mime_content_type($tmpfile);

        $filesize = $_FILES["uploadfile"]["size"];
        $filepath = $_POST["path"];

        echo "File Name : " . $filename . "<br>";
        echo "File Type : " . $filetype . "<br>";
        echo "File Size : " . (($filesize / 1024) | 0) . " KB<br>";

        echo "Uploaded to : {$tmpfile}<br><br><br>";

        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        // is the extension allowed?
        if(!array_key_exists($ext, $allowed)) {
            $errmsg = "Please select a valid file format, {$ext} is not allowed";
            $error  = -5;
            echo "Error: " . $errmsg . "<br>";
        } else {
            // Verify file size - 100k maximum
            $maxsize = 100 * 1024;
            if($filesize > $maxsize) {
                $errmsg = "File size of {$filesize} is larger than the allowed limit of {$maxsize}";
                $error  = -6;
                echo "Error: " . $errmsg . "<br>";
            } else {
                // Verify MYME type of the file
                if(in_array($filetype, $allowed)){
                    // Check whether file exists before uploading it
                    if(file_exists($filepath . $filename)) {
                        $errmsg = "{$filename} already exists in {$filepath}";
                        $error  = -1;
                        echo "Error: " . $errmsg . "<br>";
                    } else {
                        if(move_uploaded_file($_FILES["uploadfile"]["tmp_name"], $filepath . $filename)) {
                            echo "Your file was uploaded & moved successfully.<br>";
                            $ufiletype = mime_content_type($filepath . $filename);
                            echo "uploaded file type : {$ufiletype}<br>";
                            echo "path     : " . $filepath . "<br><br>";
    
                            $errmsg = "The file {$filename} uploaded successfully";
                            $error  = 0;
                        } else {
                            $errmsg = "The file {$filename} could not be moved to {$filepath}";
                            $error  = -7;
                            echo "Error: " . $errmsg . "<br>";
                        }
                    } 
                } else {
                    $ret = in_array($filetype, $allowed);
                    echo "in_array : " . $ret . "<br>";
                    echo "filetype : " . $filetype . "<br>";
        
                    $errmsg = "the file type {$filetype} is not allowed";
                    $error  = -2;
                    echo "Error: " . $errmsg . "<br>";
                }
            }
        }
    } else {
        $errmsg = "upload error - {$_FILES["uploadfile"]["error"]}";
        $error  = -3;
        echo "Error: " . $errmsg . "<br>";
    }
} else {
    $errmsg = "bad request - {$_SERVER["REQUEST_METHOD"]}";
    $error  = -4;
    echo "Error: " . $errmsg . "<br>";
}
?>
<script>
    var err      = {msg: '<?php echo $errmsg; ?>', code: <?php echo $error; ?>};
    var fileinfo = {file: '<?php echo $filename; ?>', type: '<?php echo $ufiletype; ?>', size: <?php echo $filesize; ?>, path: '<?php echo $filepath; ?>', status: err};
    var event = new CustomEvent('upload_complete_evt', {detail: fileinfo});
    window.parent.document.dispatchEvent(event);
</script>
</body>
</html>

