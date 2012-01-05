<?php
// ===========================================================================================
//
// File: PFileUploadProcess.php
//
// Description: Language file
//
// Author: Mikael Roos, mos@bth.se
//

$lang = Array(

    // 
    'FILE_UPLOAD_SUCCESS' => "File '%1s' (%2d bytes) was uploaded. The file was recognized having mimetype '%3s'.",
    'FILE_UPLOAD_FAILED' => "Failed to upload the file. Error code = %1d. %2s",

    // Error messages
    'UPLOAD_ERR_INI_SIZE' => "The uploaded file exceeds the 'upload_max_filesize' directive in php.ini.",
    'UPLOAD_ERR_FORM_SIZE' => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
    'UPLOAD_ERR_PARTIAL' => "The uploaded file was only partially uploaded.",
    'UPLOAD_ERR_NO_FILE' => "No file was uploaded. ",
    'UPLOAD_ERR_NO_TMP_DIR' => "Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.",
    'UPLOAD_ERR_CANT_WRITE' => "Failed to write file to disk. Introduced in PHP 5.1.0.",
    'UPLOAD_ERR_EXTENSION' => "A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help. Introduced in PHP 5.2.0. ",        

    // General
    'SUBMIT_ACTION_NOT_SUPPORTED' => "Submit action is not supported.",
    'FILE_NO_PERMISSION' => "You have no permission to the file you are trying to access.",

);

?>
