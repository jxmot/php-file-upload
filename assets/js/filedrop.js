// partially obtained from - 
//      https://www.html5rocks.com/en/tutorials/file/dndfiles/
//
// except MIME comments and alot of other stuff
//
function handleFileSelect(evt) {
    evt.stopPropagation();
    evt.preventDefault();

    var files = evt.dataTransfer.files;

    var output = [];
    for(var ix = 0, file; file = files[ix]; ix++) {
        // NOTE: This is another place where the MIME types can be 
        // wrong. 
        // 
        // For example, a .md file is text/plain but here file.type
        // is undefined. This happens because it's up to the client's
        // OS to determine the file type. And in the case of Windows
        // it seems that application/octet-stream is the "default". 
        // 
        // The work-around used here is that the POST handler (written 
        // in php) will check the file directly and see its correct 
        // MIME type.
        //
        var fname = escape(file.name);
        fname = fname.substr(0, fname.indexOf('.'));
        output.push('<li><strong>', escape(file.name), '</strong>',
                    ' (<i id='+fname+'-type>', file.type || 'n/a', '</i>) - ',
                    file.size, ' bytes, last modified: ',
                    file.lastModifiedDate ? file.lastModifiedDate.toLocaleDateString() : 'n/a',
                    '&nbsp;&nbsp;&nbsp;<strong><span id ='+fname+'>please wait...</span></strong>',
                    '</li>');

        uploadFile(file);
    }
    document.getElementById('drop_list').innerHTML = '<ul>' + output.join('') + '</ul>';
}

function handleDragOver(evt) {
    evt.stopPropagation();
    evt.preventDefault();
    // Explicitly show this is a copy.
    evt.dataTransfer.dropEffect = 'copy';
}

// Setup the dnd listeners.
var dropZone = document.getElementById('drop_zone');
dropZone.addEventListener('dragover', handleDragOver, false);
dropZone.addEventListener('drop', handleFileSelect, false);

// partially obtained from - 
//      https://www.webcodegeeks.com/html5/html5-file-upload-example/
// 
// except alot of other stuff
//
function uploadFile(file) {
    var url = 'upload.php';
    var xhr = new XMLHttpRequest();
    var fd = new FormData();
    xhr.open("POST", url, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Every thing ok, file uploaded
            console.log('GOT IT : ' + xhr.responseText);
            var resp = JSON.parse(xhr.responseText);
            var fname = escape(resp.detail.file);
            fname = fname.substr(0, fname.indexOf('.'));
            $('#'+fname).html((resp.detail.status.code >= 0 ? 'Success!' : resp.detail.status.msg));

            $('#'+fname+'-type').html(resp.detail.type);
        }
    };
    // desired destination path on the server
    fd.append('path', 'upload/');
    /* 
        set the desired reponse type :

            html - outputs an HTML formatted page suitable for display and sends
                and event to the parent if located in an iframe.

            json - outputs a JSON formatted string, this is the default
    */
    fd.append('rtype', 'json');
    // here's the file...
    fd.append('uploadfile', file);
    xhr.send(fd);
}

