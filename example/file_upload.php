<?php
/*
 * Required parameters
 */
$appId = "";
$apiKey = "";
$email = "";
$password = "";

if ($appId == "" || $apiKey == "" || $email == "" || $password == "")
{
    exit("<pre>One or more required parameters are missing.<br /><br />" .
        "Please open in this file in any text editor to fill all the required " .
        "parameters and try again.</pre>");
}

/*
 * Show the upload form
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <title>MediaFire API PHP Library - File Uploading</title>
    </head>
    <body>
        <form method="post" enctype="multipart/form-data" action="">
            <p>Upload a file to a MediaFire account:</p>
            <p>
                <label title="Choose a Local File to a MediaFire account" for="file">File:</label>
                <input type="file" id="file" name="file" size="30" />
                <input type="submit" id="upload" name="upload" value="Upload" />
            </p>
            <?php
            if (isset($_POST["upload"]))
            {
                /*
                 * Move uploaded file to current script folder and start uploading
                 */
                $uploadedFile = "./" . basename($_FILES["file"]["name"]);

                if (move_uploaded_file($_FILES["file"]["tmp_name"], $uploadedFile))
                {
                    /*
                     * Initilize a new instance of the class
                     */
                    include("../mflib.php");

                    $mflib = new mflib($appId, $apiKey);
                    $mflib->email = $email;
                    $mflib->password = $password;

                    /*
                     * Select a file to be uploaded. The third argurment of method fileUpload() 
                     * is the quickkey of the destination folder. In this case it's omitted, which 
                     * means the file will be stored in the root folder - 'My files'
                     */
                    $sessionToken = $mflib->userGetSessionToken();
                    $uploadKey = $mflib->fileUpload($sessionToken, $uploadedFile);
                }

                /*
                 * Print the upload result
                 */
                if ($uploadKey)
                {
                    /*
                     * Poll on a cadence until the upload is complete
                     */
                    do {
                        sleep(5);
                        $pollUpload = $mflib->filePollUpload($sessionToken, $uploadKey);
                    } while ($pollUpload["status"] != 99);
                    var_dump($pollUpload);
                }
            }
            ?>
        </form>
    </body>
</html>
