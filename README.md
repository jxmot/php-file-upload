# php-file-upload

Notes and demonstration code for uploading files using PHP

# History

I had been looking for a way to upload files via my browser to my website. I could have used SFTP but this was to be part of a larger application. And that application required a means to upload files.

After reading and researching I came accross a tutorial (<https://www.tutorialrepublic.com/php-tutorial/php-file-upload.php>) that showed me what I needed to get started.

The code found in the *root* of this repository is my **modified** version of the original tutorial code. For reference purposes the original code can be found in the `/orig` folder in this repo.

Please keep in mind that purpose was to *customize* the original code to suit the requirements of my application.

# Requirements

The following is required in order to run this application :

* Text editor - to modify files as needed
* Web Server with PHP - I'm using XAMPP with PHP 5.6.31, a hosted server with PHP >= 5.0 will also work
* A web browser - I use Chrome for testing & debugging
* Miscellaneous files to upload (*.htm, .html, .md, and .txt*)


# Application Overview

This application consists of two PHP files - 

* index.php (*this file was named `file-upload-form.php`, the original can be found in `/orig`*)
* upload.php (*this file was named `upload-manager.php`, the original can be found in `/orig`*)

## Changed Behaviors

The following items have been modified : 

* File types - I've modified the original code to accept: .htm, .html, .md, and .txt *instead* of .jpg, .jpeg, .gif, and .png
* Form page behavior:
    * The original code would redirect to the page output created by `upload.php` (*originally* `file-upload-form.php`). I've modified the form with `target="file-iframe"`.
    * Found a missing closing `>`, added it.
    * Changed the *name* of the `<input type="file">` tag from `photo` to `uploadfile`.
    * Added the style `style="width:25%;"` to `<input type="file">` so that the upload path+file is visible in browsers that display it.
    * Added an `iframe`, this will contain the output from `upload.php`.
    * Added a listener for an event trigger. This event is sent from `upload.php` and is used to indicate completion. It will be triggered on success or failure to upload.
* The file `upload.php` has been extensively modified. The logic around the calls to `die()` have been rewritten to fall through to the event trigger. The event payload contains an error code and an error message string.
* Added comments everywhere.

## Additional Modifications



# Running the Application


## Browser Behaviors