<?php
/*
//////////////////////////////////
** Function fsplit($filename,$targetfolder,$numb) **
//////////////////////////////////
function fsplit($filename,$targetfolder,$numb) Splits the file into split folder from the tmp folder.

how do you use it? 
$filename = the filename of the file to split must pass the tmp folder exmple; tmp/file.png

$targetfolder = the folder you wish to place the split files in. 

$numb = the unit number the unit number must be greater then 1

Example of use:
$unit = 0;
$upload->fsplit('tmp/tmpfile.png','split',$unit);

//////////////////////////////////
** Function check($sessionToken, $filename, $name) **
//////////////////////////////////
function check($sessionToken, $filename, $name) Checks the file to receive info back from MediaFire or returns errors

echos a "yes"" if file exists || returns "yes" if file hash exists || Return the number of chunk units if the there is enough available space to upload the particular file.

how do you use it? 

$filename = the filepath of the file to check - example: dir/file.png or can just use the file name.
$sessionToken  = the MediaFire Sesson Key
$name = The Name of the file you are checking - example: file.png.

Example of use:
$unit = $upload->check($sessionToken, 'tmp/file.png', 'file.png');

/////////////////////////////////
** Function uploadResume($unitid, $sessionToken, $filename, $files = null, $uploadKey = "myfiles", $customName = null) **
/////////////////////////////////
function uploadResume($unitid, $sessionToken, $filename, $files = null, $uploadKey = "myfiles", $customName = null) uploads a file using the resume upload feature.

how do you use it?

$unitid = Unit number of the chunk being uploaded.
$sessionToken = Mediafire Session Token.
$filename = Path to file that been splitt or if $files is gonna be "null" path to tmp file location
$files = "null" if the file under 4mb. Otherwise is the path to the unsplit file tmp location 
$uploadKey = Upload Folder Location On MediaFire. Currently Leave Unset 
$customName = Leave unset.

Example of use:

$place = 0;
$upload->fsplit('tmp/' . $email . '~*' . $filen, 'split', $unit); // Splits file into chunks
while ($place < $unit) { // loop to upload each chunk
$newpath = "split/" . $place . $email . '~*' . $filen; // File path to split files
$resume  = $upload->uploadResume($place, $sessionToken, $newpath, 'tmp/' . $email . '~*' . $filen);
//echo $items['name'].' - Moved to MediaFire Completed<br>';
sleep(1); // sleeps for 1 seconds to insure the upload command is called.
$place++; // increase the current unit number
} //$place < $unit

//////////////////////////////////
** Function instant($sessionToken, $filename, $name) **
//////////////////////////////////
function instant($sessionToken, $filename, $name) Instantly upload file if file hash already exists in mediafire network

how do you use it? 

$filename = the filepath of the file to uploading - example: dir/file.png or can just use the file name.
$sessionToken  = the MediaFire Sesson Key
$name = The Name of the file you are upload - example: file.png.

Example of use:
$unit = $upload->instant($sessionToken, 'tmp/file.png', 'file.png');;

*/
class upload
{
    public $dupe; // Variable for deciding what to do with duplicate files this can be skip,keep,replace.
    //array for which api url to call
    public $signature; // Variable for setting signature retrevied from mflib "userGetSignature()" class function. Set outside Class!
    public $apiUrl = array("UPLOAD_INSTANT" => "http://www.mediafire.com/api/1.1/upload/instant.php", 
                           "UPLOAD_CHECK" => "http://www.mediafire.com/api/upload/check.php", 
                           "UPLOAD_RESUME" => "http://www.mediafire.com/api/1.1/upload/resumable.php");
    protected function fsplit($filename, $targetfolder, $numb)
    {
        $targetfolder = $targetfolder; // target file
        $filesize     = filesize($filename);
        $filesize     = round(($filesize / 1048576), 2); // Gets Filesize in Mb
        // Below checks files size to chunk needed and gets file chunksize 
        if ($filesize >= 4) {
            $piecesize = 1; // chuck size equals 1Mb if filesize is bigger then 4Mb
        } //$filesize >= 4
        if ($filesize >= 16) {
            $piecesize = 2; // chuck size equals 2Mb if filesize is bigger then 16Mb
        } //$filesize >= 16
        if ($filesize >= 64) {
            $piecesize = 4; // chuck size equals 4Mb if filesize is bigger then 64Mb
        } //$filesize >= 64
        if ($filesize >= 256) {
            $piecesize = 8; // chuck size equals 8Mb if filesize is bigger then 256Mb
        } //$filesize >= 256
        if ($filesize >= 1000) {
            $piecesize = 16; // chuck size equals 16Mb if filesize is bigger then 1Gb    
        } //$filesize >= 1000
        if ($filesize >= 4000) {
            $piecesize = 32; // chuck size equals 32Mb if filesize is bigger then 4Gb
        } //$filesize >= 4000
        if ($filesize >= 16000) {
            $piecesize = 64; // chuck size equals 64Mb if filesize is bigger then 16Gb
        } //$filesize >= 16000
        $buffer   = 1024; //Buffer size of chuck to write
        $piece    = 1048576 * $piecesize; // Size of chunk in bytes
        $current  = 0;
        $splitnum = 0;
        if (!file_exists($targetfolder)) {
            if (mkdir($targetfolder)) { // makes directory if split directory does not exist
            } //mkdir($targetfolder)
        } //!file_exists($targetfolder)
        if (!$handle = fopen($filename, "rb")) {
            die("Unable to open $filename for read!"); // script dies with this error if file cannot be edited 
        } //!$handle = fopen($filename, "rb")
        $base_filename = basename($filename); // returns the base file name
        $piece_name    = $targetfolder . '/' . $splitnum . $base_filename; // is the chunk file name plus it location
        if (!$fw = fopen($piece_name, "w")) {
            die("Unable to open $piece_name for write."); // if can't write content into file displays this error
        } //!$fw = fopen($piece_name, "w")
        while (!feof($handle)) { // loop to split file
            if ($splitnum < $numb) {
                if ($current < $piece) {
                    if ($content = fread($handle, $buffer)) { // tells the script how much chunk to read before placing in split file
                        if (fwrite($fw, $content)) { // writes content to file1
                            $current += $buffer; // updates the current file content position for the next loop
                        } //fwrite($fw, $content)
                        else {
                            die("unable to write to target folder."); // dies if folder is unwritable
                        }
                    } //$content = fread($handle, $buffer)
                } //$current < $piece
                else {
                    fclose($fw);
                    $current = 0;
                    $splitnum++; // updates the chunk file position
                    $piece_name = $targetfolder . '/' . $splitnum . $base_filename;
                    $fw         = fopen($piece_name, "w"); // checks to make sure file exists
                }
            } //$splitnum < $numb
        } //!feof($handle)
        //below closes file write
        fclose($fw);
        fclose($handle);
    }
    public function instant($sessionToken, $filename, $name, $folderkey = null)
    {
        $filen = explode('/', $filename);
        $query = array( // sets params of the file to check
            "session_token" => $sessionToken,
            "signature" => $this->signature,
            "filename" => $name,
            "hash" => hash_file("sha256", $filename),
            "size" => filesize($filename),
            "folder_key" => $folderkey,
            "response_format" => 'json'
        );
        if ($this->dupe != NULL) {
            $query = array( // sets params of the file to check
                "session_token" => $sessionToken,
                "signature" => $this->signature,
                "filename" => $name,
                "hash" => hash_file("sha256", $filename),
                "size" => filesize($filename),
                "action_on_duplicate" => $this->dupe,
                "folder_key" => $folderkey,
                "response_format" => 'json'
            );
        } //$this->dupe != NULL
        $url   = $this->apiUrl["UPLOAD_INSTANT"] . "?" . http_build_query($query, "", "&");
        $mflib = new mflib();
        $data  = $mflib->getContents($url);
        if (!$data) {
            return false;
        } //!$data
        return TRUE;
    }
    public function check($sessionToken, $filename, $name)
    {
        $filen = explode('/', $filename);
        $query = array( // sets params of the file to check
            "session_token" => $sessionToken,
            "signature" => $this->signature,
            "filename" => $name,
            "resumable" => "yes",
            "hash" => hash_file("sha256", $filename),
            "size" => filesize($filename),
            "response_format" => 'json'
        );
        $url   = $this->apiUrl["UPLOAD_CHECK"] . "?" . http_build_query($query, "", "&");
        $mflib = new mflib();
        $data  = $mflib->getContents($url);
        if (!$data) {
            return false;
        } //!$data
        $oXML      = $data;
        $filexist  = $oXML['file_exists']; // gets response value file_exists
        $available = $oXML['available_space']; // gets reponse value available_space
        $hashexist = $oXML['hash_exists']; // gets hash_exists value from response
        @$quickkey = $oXML['duplicate_quickkey'];
        $unit = $oXML['resumable_upload']['number_of_units']; // gets number of units from response
        if ($filexist == 'no') {
            if ($hashexist != 'yes') {
                if (filesize($filename) <= $available) {
                    return $unit;
                } //filesize($filename) <= $available
                else {
                    echo 'Not enough space to upload file';
                }
            } //$hashexist != 'yes'
            else {
                return $hashexist; // returns yes or no based on if hash exists
            }
        } //$filexist == 'no'
        else {
            return 'yes';
        }
    }
    public function uploadResume($unitid, $sessionToken, $filename, $files = null, $uploadKey = "myfiles", $customName = null)
    {
        $mimetype = "application/octet-stream";
        if (strpos($filename, ";type=") !== false) {
            $parts    = explode(";type=", $filename, 2);
            $filename = $parts[0];
            if (!empty($parts[1])) {
                $mimetype = $parts[1];
            } //!empty($parts[1])
        } //strpos($filename, ";type=") !== false
        if (!file_exists($filename) || !is_readable($filename)) {
            echo "File is not exist or not readable";
            return false;
        } //!file_exists($filename) || !is_readable($filename)
        $filesize = filesize($filename);
        if ($filesize == 0) {
            echo "File has no content";
            return false;
        } //$filesize == 0
        $file     = $filename;
        $filename = explode('~*', $filename);
        if ($files != null) {
            $httpOptions = array(
                "method" => "POST",
                "file" => array(
                    "name" => "Filedata",
                    "path" => $file,
                    "type" => $mimetype
                ),
                "header" => array(
                    "Referer: http://www.mediafire.com",
                    'x-filename: ' . $filename['1'],
                    'x-filesize: ' . filesize($files),
                    'x-filehash: ' . hash_file("sha256", $files),
                    'x-unit-size: ' . $filesize,
                    'x-unit-id: ' . $unitid,
                    'x-unit-hash: ' . hash_file("sha256", $file)
                )
            );
        } //$files != null
        else {
            $httpOptions = array(
                "method" => "POST",
                "file" => array(
                    "name" => "Filedata",
                    "path" => $file,
                    "type" => $mimetype
                ),
                "header" => array(
                    "Referer: http://www.mediafire.com",
                    'x-filename: ' . $filename['1'],
                    'x-filesize: ' . $filesize,
                    'x-filehash: ' . hash_file("sha256", $file),
                    'x-unit-size: ' . $filesize,
                    'x-unit-id: 0',
                    'x-unit-hash: ' . hash_file("sha256", $file)
                )
            );
        }
        if (is_string($customName) && !empty($customName)) {
            $httpOptions["header"][] = "x-filename: $customName";
        } //is_string($customName) && !empty($customName)
        $query = array(
            //"uploadkey" => $uploadKey,
            //"quick_key" => 'jeug758k187df35',
            "session_token" => $sessionToken,
            "signature" => $this->signature,
            "response_format" => "xml"
            //"path" => "MyFiles/folder"
        );
        if ($this->dupe != NULL) {
            $query = array(
                //"uploadkey" => $uploadKey,
                //"quick_key" => 'jeug758k187df35',
                "session_token" => $sessionToken,
                "signature" => $this->signature,
                "action_on_duplicate" => $this->dupe,
                "response_format" => "xml"
                //"path" => "MyFiles/folder"
            );
        } //$this->dupe != NULL
        //$this->actions[] = "Uploading";
        $url             = $this->apiUrl["UPLOAD_RESUME"] . "?" . http_build_query($query, "", "&");
        $mflib           = new mflib();
        $data            = $mflib->getContents($url, $httpOptions);
        if (!$data) {
            $upload->uploadResume($unitid, $sessionToken, $filename, $files);
            return false;
        } //!$data
        @unlink($filename);
        @unlink($files);
        if (!isset($data["doupload"]["key"]) || trim($data["doupload"]["key"]) == "") {
            //$this->showError("Unable to upload file - Code '" . $data["doupload"]["result"] . "'");
        } //!isset($data["doupload"]["key"]) || trim($data["doupload"]["key"]) == ""
        return $data["doupload"]["key"];
    }
}
?>
