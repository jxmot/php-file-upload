<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Upload Form</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
</head>
<body>
<?php
/* will be copied in to the event data payload a the end of this file. */
$error = 0;
$errmsg = "success";

$filename = "";
$filetype = "";
$filesize = "";
$ufiletype = "";

// Check if the form was submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if file was uploaded without errors
    if(isset($_FILES["uploadfile"]) && $_FILES["uploadfile"]["error"] == 0) {
        $allowed = array("htm" => "text/html", "html" => "text/html", "md" => "text/html", "txt" => "text/plain", "log" => "application/octet-stream");
        $filename = $_FILES["uploadfile"]["name"];
        $filetype = $_FILES["uploadfile"]["type"];
        $filesize = $_FILES["uploadfile"]["size"];

        echo "File Name : " . $filename . "<br>";
        echo "File Type : " . $filetype . "<br>";
        echo "File Size : " .($filesize / 1024) . " KB<br>";
        echo "Uploaded to : " . $_FILES["uploadfile"]["tmp_name"] . "<br><br><br>";

        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        // NOTE: jmotyl - the call to array_key_exists() here is NOT enough.
        // That's because if a file comes across as a known type, but isn't 
        // associated by extension that file will upload. The proper fix
        // would be to change the parent so that correct type is sent.

        if(!array_key_exists($ext, $allowed)) {
            //die("Error: Please select a valid file format.");
            $errmsg = "Please select a valid file format, {$ext} is not allowed";
            $error  = -5;
        } else {
            // Verify file size - 100k maximum
            $maxsize = 100 * 1024;
            if($filesize > $maxsize) {
                //die("Error: File size is larger than the allowed limit.");
                $errmsg = "File size of {$filesize} is larger than the allowed limit of {$maxsize}";
                $error  = -6;
            } else {
                // Verify MYME type of the file
                if(in_array($filetype, $allowed)){
                    // Check whether file exists before uploading it
                    if(file_exists("upload/" . $filename)) {
                        echo $filename . " is already present.";
                        $errmsg = "file exists";
                        $error  = -1;
                    } else {
                        move_uploaded_file($_FILES["uploadfile"]["tmp_name"], "upload/" . $filename);
                        echo "Your file was uploaded & moved successfully.<br>";
                        $ufiletype = mime_content_type("upload/" . $filename);
                        echo "uploaded file type : {$ufiletype}<br>";

                        $errmsg = "The file {$filename} uploaded successfully";
                        $error  = 0;
                    } 
                } else {
                    echo "Error: There was a problem uploading your file. Please try again.<br><br>";
                    $ret = in_array($filetype, $allowed);
                    echo "in_array : " . $ret . "<br><br>";
                    echo "filetype : " . $filetype . "<br><br>";
        
                    $errmsg = "the file type {$filetype} is not allowed";
                    $error  = -2;
                }
            }
        }
    } else {
        echo "Error: " . $_FILES["uploadfile"]["error"];

        $errmsg = "Error: {$_FILES["uploadfile"]["error"]}";
        $error  = -3;
    }
} else {
        $errmsg = "bad request - {$_SERVER["REQUEST_METHOD"]}";
        $error  = -4;
}
?>
<script>
    var err      = {msg: '<?php echo $errmsg; ?>', code: <?php echo $error; ?>};
    var fileinfo = {file: '<?php echo $filename; ?>', type: '<?php echo $ufiletype; ?>', status: err};
    var event = new CustomEvent('upload_complete_evt', {detail: fileinfo});
    window.parent.document.dispatchEvent(event);
</script>
</body>
</html>

