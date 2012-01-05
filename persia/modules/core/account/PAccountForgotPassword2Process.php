<?php
// ===========================================================================================
//
// File: PAccountForgotPassword2Process.php
//
// Description: Aid when forgetting passwords, sends email to the account owner,
// using the email related to the account. Step 2.
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
$intFilter->CustomFilterIsSetOrDie('resetPassword');


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$submitAction    = $pc->POSTisSetOrSetDefault('submit');
$redirect            = $pc->POSTisSetOrSetDefault('redirect');
$redirectFail    = $pc->POSTisSetOrSetDefault('redirect-fail');

// Always check whats coming in...
//$pc->IsNumericOrDie($topicId, 0);


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
// Find the mail adress and send a rescue mail  
// 
else if($submitAction == 'verify-key') {

    // Get the input and check it
    $key2 = strip_tags($pc->POSTisSetOrSetDefault('key2'));
    $_SESSION['key2'] = $key2;

    //
    // Check key1 from the session
    //
    $key1 = $pc->SESSIONisSetOrSetDefault('key1', '');
    if(empty($key1)) {
        $pc->SetSessionMessage('forgotPwdFailed', $pc->lang['SESSION_KEY_LOST']);
        $pc->RedirectTo($redirectFail);        
    }

    //
    // Check the CAPTCHA
    //
    $captcha = new CCaptcha();
    if(!$captcha->CheckAnswer()) {
        $pc->SetSessionMessage('forgotPwdFailed', $pc->lang['CAPTCHA_FAILED']);
        $pc->RedirectTo($redirectFail);        
    }
    
    //
    // Execute the database query to verify the key
    //
    $db = new CDatabaseController();
    $spSPPasswordResetActivate=DBSP_SPPasswordResetActivate;

    $mysqli = $db->Connect();

    // Prepare query
    $key2    = $mysqli->real_escape_string($key2);
    
    $query = <<<EOD
CALL {$spSPPasswordResetActivate}(@aAccountId, @aAccountName, '{$key1}', '{$key2}');
SELECT 
    @aAccountName AS accountName,
    @aAccountId AS accountId;
EOD;

    // Perform the query
    $results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

    // Get details from resultset
    $row = $results[1]->fetch_object();

    //
    // Did something fail?
    //
    if(!$row->accountName) {
            $pc->SetSessionMessage('forgotPwdFailed', $pc->lang['KEY_TIME_EXPIRED_OR_NO_MATCH']);
            $pc->RedirectTo($redirectFail);    
        }
        
    
    $_SESSION['accountName']    = $row->accountName;
    $_SESSION['accountId']         = $row->accountId;
    unset($_SESSION['key1']);
    unset($_SESSION['key2']);
    $mysqli->close();

    $pc->SetSessionMessage('keySuccess', $pc->lang['SUCCESSFULLY_VERIFIED_KEY']);
    $pc->RedirectTo($redirect);    
}


// -------------------------------------------------------------------------------------------
//
// Default, submit-action not supported, show error and die.
// 
die($pc->lang['SUBMIT_ACTION_NOT_SUPPORTED']);


?>
