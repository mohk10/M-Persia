<?php
// ===========================================================================================
//
// File: PAccountForgotPassword1Process.php
//
// Description: Aid when forgetting passwords, sends email to the accountowner,
// using the email related to the account.
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
//$intFilter->UserIsSignedInOrRecirectToSignIn();
//$intFilter->UserIsMemberOfGroupAdminOrDie();
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
else if($submitAction == 'send-rescue-mail') {

    // Get the input and check it
    $account = $pc->POSTisSetOrSetDefault('account');
    $_SESSION['account'] = $account;

    //
    // Check the CAPTCHA
    //
    $captcha = new CCaptcha();
    if(!$captcha->CheckAnswer()) {
        $pc->SetSessionMessage('forgotPwdFailed', $pc->lang['CAPTCHA_FAILED']);
        $pc->RedirectTo($redirectFail);        
    }
    
    //
    // Execute the database query to find mailadress
    //
    $db = new CDatabaseController();
$spSPGetMailAdressFromAccount=DBSP_SPGetMailAdressFromAccount;
$spSPPasswordResetGetKey=DBSP_SPPasswordResetGetKey;
    
    $mysqli = $db->Connect();

    // Prepare query
    $account     = $mysqli->real_escape_string($account);

    $query = <<<EOD
CALL {$spSPGetMailAdressFromAccount}('{$account}', @aAccount, @aMail);
SELECT 
    @aAccount AS account,
    @aMail AS mail;
EOD;

    // Perform the query
    $results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

    // Get details from resultset
    $row = $results[1]->fetch_object();

    //
    // Did something fail?
    //
    if(!$row->mail) {
            $pc->SetSessionMessage('forgotPwdFailed', $pc->lang['NO_MAIL_CONNECTED']);
            $pc->RedirectTo($redirectFail);    
        }
        
    $mailadress = $row->mail;
    
    //
    // Initiate to re-set password, store key1 in the session and the get the key2 to send to the user
    // The resulting key3 is stored in the database.
    //
    $_SESSION['key1'] = md5(uniqid());
    $query = <<<EOD
SET @aKey = '{$_SESSION['key1']}';
CALL {$spSPPasswordResetGetKey}('{$row->account}', @aKey);
SELECT @aKey AS key2;
EOD;
 
     // Perform the query
    $results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

    // Get details from resultset
    $row = $results[2]->fetch_object();
    //echo("key2=".$row->key2);
    //
    // Send a mail to the mailadress with the key2
    //
    $mail = new CMail();
    $r = $mail->SendMail(    $mailadress, $pc->lang['MAIL_LOST_PASSWORD_SUBJECT'], sprintf($pc->lang['MAIL_LOST_PASSWORD_BODY'], $row->key2));
    if(!$r) {
        $pc->SetSessionMessage('mailFailed', sprintf($pc->lang['FAILED_SENDING_MAIL'], $mailadress));
        $pc->RedirectTo($redirectFail);    
    }
        
    $results[2]->close();
    $mysqli->close();
    unset($_SESSION['account']);
    
    // Enable custom filter
    $intFilter->CustomFilterIsSetOrDie('resetPassword', 'set');

    $pc->SetSessionMessage('mailSuccess',"key2=".$row->key2." ".sprintf($pc->lang['SUCCESSFULLY_SENT_MAIL'], $mailadress));
    $pc->RedirectTo($redirect);    
}


// -------------------------------------------------------------------------------------------
//
// Default, submit-action not supported, show error and die.
// 
die($pc->lang['SUBMIT_ACTION_NOT_SUPPORTED']);


?>
