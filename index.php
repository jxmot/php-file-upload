<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Upload Form</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
</head>
<body>
<script>
    // when the upload.php file has loaded it will trigger an 
    // event that will be recevied here.
    window.document.addEventListener('upload_complete_evt', handleEvent, false)
    function handleEvent(e) {
        console.log('GOT IT : ' + JSON.stringify(e.detail));
        $('#file-status').show();
    }
</script>
    <!-- see : http://php.net/manual/en/features.file-upload.post-method.php -->
    <!-- see : https://stackoverflow.com/questions/1201945/how-is-mime-type-of-an-uploaded-file-determined-by-browser -->

    <!-- after reading through the posts in the link just above it becomes 
         clear that using the browser supplied MIME type is unreliable. 

         That's because Windows (or the browser's OS) determines the MIME 
         type. You can view some of the types using regedit. Go to - 

            Computer\HKEY_CLASSES_ROOT\MIME\Database\Content Type

         to see the default types. 

         IMPORTANT: I do NOT recommend editing any of the registry entries.

         It appears that if a file type (".md" for example) isn't found in the
         registry that the default type will be application/octet-stream

         It's probably better to call mime_content_type() to determine the file's
         MIME type. However, with the code below it isn't possible to check the 
         type prior to initiating the form's "action".

    -->
    <form action="upload.php" method="post" enctype="multipart/form-data" target="file-iframe">
        <h2>Upload File</h2>
        <label for="fileSelect">Filename:</label>
        <!-- Also see : https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/file -->
        <!--            https://html.spec.whatwg.org/multipage/input.html#fakepath-srsly -->

        <!-- NOTE: The file dialog is different between FF and chrome. In FF the discrete
             file types are seen in the dropdown. But in chrome you will only see "Custom Files".

             It's even more different in IE/Edge, there when a file is selected IE/Edge will display 
             the file path in a read-only text box to the left of the file selection button (which 
             is also labeled differently).

             NOTE: In production code the contents of the `accept` attribute would most likely
             come from a database or other configuration resource.
        -->
        <input type="file" id="fileselect" name="uploadfile" accept=".htm, .html, .md, .txt" style="width:25%;">
        <!-- NOTE: in "production" code the value would most likely come from a database
             or some other configuration source. The intent would be to reuse this code
             for other files where the destination is different.
        -->
        <input hidden type="text" id="uploadpath" name="path" value="upload/">
        <!-- 
            set the desired reponse type :

                html - outputs an HTML formatted page suitable for display and sends
                       and event to the parent if located in an iframe.

                json - outputs a JSON formatted string, this is the default
        -->
        <input hidden type="text" id="uploadresp" name="rtype" value="html">
        <br>
        <br>
        <input type="submit" name="submit" value="Upload">
        <p><strong>Note:</strong> Only .htm, .html, .md, .txt formats allowed to a max size of 100k.</p>
    </form>

    <div id="file-status" hidden>
        <iframe name="file-iframe" style="width:50%;"></iframe>
    </div>

</body>
</html>

