<?php
/*
    upload.php - Uploads files via a POST request. It responds with a JSON
    string.
*/
$error     = 0;
$errmsg    = "";
$filename  = "n/a";
$filetype  = "n/a";
$filesize  = 0;
$filepath  = "n/a";
$resptype  = "n/a";
$_filetype = "n/a";

// Verify that the correct method was used
if($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if file was uploaded without errors
    if(isset($_FILES["uploadfile"]) && ($_FILES["uploadfile"]["error"] === 0)) {
        // valid file types and extensions
        // edit as needed
        $valid_types = array("text/html", "text/plain");
        $valid_exts  = array("htm", "html", "md", "txt" );
        // necessary pieces of file information
        $filename = $_FILES["uploadfile"]["name"];
        $tmpfile  = $_FILES["uploadfile"]["tmp_name"];
        $filesize = $_FILES["uploadfile"]["size"];
        $filepath = $_POST["path"];
        $resptype = $_POST["rtype"];
        // extract the extension from the file name
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        // This is the real file type...
        $filetype = mime_content_type($tmpfile);
        // this can be wrong for some file types, it's up 
        // to the client's OS to determine the type.
        $_filetype = $_FILES["uploadfile"]["type"];

        // is the extension allowed?
        if(!in_array($ext, $valid_exts)) {
            $errmsg = "Please select a valid file format, {$ext} is not allowed";
            $error  = -5;
        } else {
            // Verify MIME type of the file
            if(!in_array($filetype, $valid_types)){
                $errmsg = "The file type {$filetype} is not allowed";
                $error  = -2;
            } else {
                // Verify file size - 100k maximum
                $maxsize = 100 * 1024;
                if($filesize > $maxsize) {
                    $errmsg = "File size of {$filesize} is larger than the allowed limit of {$maxsize}";
                    $error  = -6;
                } else {
                    // Check if the file exists in the destination before 
                    // moving it from the PHP's temporary folder
                    if(file_exists($filepath . $filename)) {
                        $errmsg = "A file named {$filename} already exists in {$filepath}";
                        $error  = -1;
                    } else {
                        // move it...
                        if(!move_uploaded_file($_FILES["uploadfile"]["tmp_name"], $filepath . $filename)) {
                            $errmsg = "The file {$filename} could not be moved to {$filepath}";
                            $error  = -7;
                        } else {
                            $errmsg = "The file {$filename} uploaded successfully";
                            $error  = 0;
                        }
                    } 
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

if($resptype !== "html") {
    echo "{\"detail\": {\"file\": \"{$filename}\", \"type\": \"{$filetype}\", \"size\": {$filesize}, \"path\": \"{$filepath}\", \"status\": {\"msg\": \"{$errmsg}\", \"code\": {$error}}}}";
} else {
    echo "
<!DOCTYPE html>\n
<html lang=\"en\">\n
<head>\n
    <meta charset=\"UTF-8\">\n
    <title>File Upload Form</title>\n
    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js\"></script>\n
</head>\n
<body>\n
    <p>";
    echo "        <h3>{$errmsg}</h3>";
    echo "        File Name : {$filename}<br>";
    echo "        File Type : {$filetype}<br>";
    if($error >= 0) {
        $kb = ($filesize / 1024) | 0;
        echo "        File Size : {$kb} KB<br>";
        echo "        Uploaded to : {$tmpfile}<br>";
        echo "        Moved to :  {$filepath}{$filename}<br>";
    }
    echo "        <br><br>";
    echo "    </p><br>";

    echo "
    <script>
        var err      =  {
                            msg: '{$errmsg}', 
                            code: {$error}
                        };
    
        var fileinfo =  {
                            file: '{$filename}', 
                            type: '{$filetype}', 
                            size:  {$filesize}, 
                            path: '{$filepath}', 
                            status: err
                        };
        var event = new CustomEvent('upload_complete_evt', {detail: fileinfo});
        window.parent.document.dispatchEvent(event);
    </script>
</body>
</html>
";

}
?>
