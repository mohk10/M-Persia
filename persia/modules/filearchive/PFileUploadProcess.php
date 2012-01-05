<?php
// ===========================================================================================
//
// File: PFileUploadProcess.php
//
// Description: Upload and store files in the users file archive.
//
// Author: Mikael Roos, mos@bth.se
//


// -------------------------------------------------------------------------------------------
//
// Get pagecontroller helpers. Useful methods to use in most pagecontrollers
//
$pc = new CPageController();
$pc->LoadLanguage(__FILE__);


// -------------------------------------------------------------------------------------------
//
// Interception Filter, controlling access, authorithy and other checks.
//
$intFilter = new CInterceptionFilter();

$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$submitAction    = $pc->POSTisSetOrSetDefault('do-submit');
$redirect            = $pc->POSTisSetOrSetDefault('redirect');
$redirectFail    = $pc->POSTisSetOrSetDefault('redirect-fail');
$postId    = $pc->GETisSetOrSetDefault('postid',0);

$userId        = $_SESSION['idUser'];

$account = $pc->SESSIONisSetOrSetDefault('accountUser');
$archivePath = FILE_ARCHIVE_PATH  . $account . DIRECTORY_SEPARATOR;
if(!is_dir($archivePath)) {
	echo($archivePath);
    mkdir($archivePath);
}
//$postId=5;
//echo("postId=".$postId);
// Always check whats coming in...
//$pc->IsNumericOrDie($articleId, 0);

//echo("submitAction=".$submitAction);

// -------------------------------------------------------------------------------------------
//
// Depending on the submit-action, do whats to be done. If, else if, else, replaces switch.
// 


// -------------------------------------------------------------------------------------------
//
// Do some insane checking to avoid misusage, errormessage if not correct.
// 
if(false) {

}
// -------------------------------------------------------------------------------------------
//
// Upload single file and return html success/failure message.
// 
/*else if($submitAction == 'upload-return-html') {

    // http://www.php.net/manual/en/features.file-upload.errors.php
    $errorMessages = Array (
        UPLOAD_ERR_INI_SIZE     => $pc->lang['UPLOAD_ERR_INI_SIZE'],
        UPLOAD_ERR_FORM_SIZE     => $pc->lang['UPLOAD_ERR_FORM_SIZE'],
        UPLOAD_ERR_PARTIAL         => $pc->lang['UPLOAD_ERR_PARTIAL'],
        UPLOAD_ERR_NO_FILE         => $pc->lang['UPLOAD_ERR_NO_FILE'],
        UPLOAD_ERR_NO_TMP_DIR => $pc->lang['UPLOAD_ERR_NO_TMP_DIR'],
        UPLOAD_ERR_CANT_WRITE => $pc->lang['UPLOAD_ERR_CANT_WRITE'],
        UPLOAD_ERR_EXTENSION     => $pc->lang['UPLOAD_ERR_EXTENSION'],        
    );
        
    $html = '';
    if (move_uploaded_file($_FILES['file']['tmp_name'], $archivePath . basename($_FILES['file']['name']))) {
        $html = CHTMLHelpers::GetHTMLUserFeedbackPositive(sprintf($pc->lang['FILE_UPLOAD_SUCCESS'], $_FILES['file']['name'], $_FILES['file']['size'], $_FILES['file']['type']));
    } else {
        $html = CHTMLHelpers::GetHTMLUserFeedbackNegative(sprintf($pc->lang['FILE_UPLOAD_FAILED'], $_FILES['file']['error'], $errorMessages[$_FILES['file']['error']]));
    }

    echo $html;
    exit;
}*/
// -------------------------------------------------------------------------------------------
//
// Upload single file and return html success/failure message. Ajax-like.
//
else if($submitAction == 'upload-return-html') {

// http://www.php.net/manual/en/features.file-upload.errors.php
$errorMessages = Array (
UPLOAD_ERR_INI_SIZE => $pc->lang['UPLOAD_ERR_INI_SIZE'],
UPLOAD_ERR_FORM_SIZE => $pc->lang['UPLOAD_ERR_FORM_SIZE'],
UPLOAD_ERR_PARTIAL => $pc->lang['UPLOAD_ERR_PARTIAL'],
UPLOAD_ERR_NO_FILE => $pc->lang['UPLOAD_ERR_NO_FILE'],
UPLOAD_ERR_NO_TMP_DIR => $pc->lang['UPLOAD_ERR_NO_TMP_DIR'],
UPLOAD_ERR_CANT_WRITE => $pc->lang['UPLOAD_ERR_CANT_WRITE'],
UPLOAD_ERR_EXTENSION => $pc->lang['UPLOAD_ERR_EXTENSION'],
);
if(!(is_numeric($postId))||($postId<0)){
exit(CHTMLHelpers::GetHTMLUserFeedbackNegative($pc->lang['FILE_NO_PERMISSION']));
}


// Check that uploaded filesize is within limit
if ($_FILES['file']['size'] > FILE_MAX_SIZE) {
exit(CHTMLHelpers::GetHTMLUserFeedbackNegative(sprintf($pc->lang['FILE_UPLOAD_FAILED_MAXSIZE'], FILE_MAX_SIZE)));
}

// Create a unique filename
do {
$file = uniqid();
$path = $archivePath . $file;
} while(file_exists($path));

// Move the uploaded file
if (!move_uploaded_file($_FILES['file']['tmp_name'], $archivePath . $file)) {
exit(CHTMLHelpers::GetHTMLUserFeedbackNegative(sprintf($pc->lang['FILE_UPLOAD_FAILED'], $_FILES['file']['error'], $errorMessages[$_FILES['file']['error']])));
}

// Store metadata of the file in the database
$db = new CDatabaseController();
$SPInsertFile=DBSP_SPInsertFile;
$udfFCheckUserIsOwnerOrAdmin2=DBUDF_FCheckUserIsOwnerOrAdmin2;
//echo("postId=".$postId);
if($postId){
$mysqli = $db->Connect();

$query=<<< EOD
SELECT {$udfFCheckUserIsOwnerOrAdmin2}('{$postId}', '{$userId}') AS isAllowed ;
EOD;

// Perform the query
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);
$row = $results[0]->fetch_object();
if(!$row->isAllowed)die($pc->lang['FILE_NO_PERMISSION']);
}
$mysqli = $db->Connect();

// Create the query
$query = <<< EOD
CALL {$SPInsertFile}('{$userId}','{$postId}', '{$_FILES['file']['name']}', '{$file}', '{$path}', {$_FILES['file']['size']}, '{$_FILES['file']['type']}');
EOD;

// Perform the query
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

// Check if the unique key was accepted, else, create a new one and try again
//$row = $results[1]->fetch_object();
//$status = $row->status;
//$fileid = $row->fileid;
//$results[1]->close();

// Did the unique key update correctly?
/*if($row->status) {
// Create query to set new unique name
do {
$newid = uniqid();
$query = <<< EOD
CALL {$db->_['PFileUpdateUniqueName']}('{$fileid}', '{$newid}', @status);
SELECT @status AS status;
EOD;

$row = $results[1]->fetch_object();
$status = $row->status;
$results[1]->close();
} while ($status != 0);
}
*/
// Assume it all whent okey
$mysqli->close();

// Echo out the result
exit(CHTMLHelpers::GetHTMLUserFeedbackPositive(sprintf($pc->lang['FILE_UPLOAD_SUCCESS'], $_FILES['file']['name'], $_FILES['file']['size'], $_FILES['file']['type'])));
}

// -------------------------------------------------------------------------------------------
//
// Upload a single file by a traditional form
// 
else if($submitAction == 'single-by-traditional-form') {

    echo '<pre>';
    if (move_uploaded_file($_FILES['file']['tmp_name'], $archivePath . basename($_FILES['file']['name']))) {
            echo "File is valid, and was successfully uploaded.\n";
    } else {
            echo "Possible file upload attack!\n";
    }
    
    echo 'Here is some more debugging info:';
    print_r($_FILES);
    
    print "</pre>";
    exit;
}


// -------------------------------------------------------------------------------------------
//
// Upload multiple files by a traditional form
// 
else if($submitAction == 'multiple-by-traditional-form') {

    echo '<pre>';
    foreach ($_FILES["file"]["error"] as $key => $error) {
        if ($error == UPLOAD_ERR_OK) {
            echo "File '{$key}' is valid, and was successfully uploaded.\n";
            move_uploaded_file($_FILES["file"]["tmp_name"][$key], $archivePath . basename($_FILES["file"]["name"][$key]));
        } else {
            echo "Possible file upload attack!\n";        
        }
    }
    
    echo 'Here is some more debugging info:';
    print_r($_FILES);
    
    print "</pre>";
    exit;
}


/*
// Get the input and check it
    $account        = $pc->POSTisSetOrSetDefault('account');
    $password1    = $pc->POSTisSetOrSetDefault('password1');
    $password2    = $pc->POSTisSetOrSetDefault('password2');

    $_SESSION['account'] = $account;
    //
    // Check the characters in the username
    //
    trim($account);
    if(preg_replace('/[a-zA-Z0-9]/', '', $account)) {
        $pc->SetSessionMessage('createAccountFailed', $pc->lang['INVALID_ACCOUNT_NAME']);
        $pc->RedirectTo($redirectFail);        
    }

    //
    // Check the passwords
    //
    if(empty($password1) || empty($password2)) {
        $pc->SetSessionMessage('createAccountFailed', $pc->lang['PASSWORD_CANNOT_BE_EMPTY']);
        $pc->RedirectTo($redirectFail);
    } 
    else if($password1 != $password2) {
        $pc->SetSessionMessage('createAccountFailed', $pc->lang['PASSWORD_DOESNT_MATCH']);
        $pc->RedirectTo($redirectFail);
    }

    //
    // Check the CAPTCHA
    //
    $captcha = new CCaptcha();
    if(!$captcha->CheckAnswer()) {
        $pc->SetSessionMessage('createAccountFailed', $pc->lang['CAPTCHA_FAILED']);
        $pc->RedirectTo($redirectFail);        
    }

    //
    // Execute the database query to make the update
    //
    $db = new CDatabaseController();
    $mysqli = $db->Connect();

    // Prepare query
    $account     = $mysqli->real_escape_string($account);
    $password = $mysqli->real_escape_string($password1);
    $hashingalgoritm = DB_PASSWORDHASHING;

    $query = <<<EOD
CALL {$db->_['PCreateAccount']}(@accountId, '{$account}', '{$password}', '{$hashingalgoritm}', @status);
SELECT 
    @accountId AS accountid,
    @status AS status;
EOD;

    // Perform the query
    $results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

    // Get details from resultset
    $row = $results[1]->fetch_object();

    if($row->status == 1) {
        $pc->SetSessionMessage('createAccountFailed', $pc->lang['ACCOUNTNAME_ALREADY_EXISTS']);
        $pc->RedirectTo($redirectFail);    
    }
    
    $results[1]->close();
    $mysqli->close();

    //
    // Do a silent login and then proceed to $redirect
    //
    unset($_SESSION['account']);
    $_SESSION['silentLoginAccount']     = $account;
    $_SESSION['silentLoginPassword']     = $password;
    $_SESSION['silentLoginRedirect']     = $redirect;
    $pc->RedirectTo($silentLogin);
}
*/

// -------------------------------------------------------------------------------------------
//
// Default, submit-action not supported, show error and die.
// 
die($pc->lang['SUBMIT_ACTION_NOT_SUPPORTED']);
?>
