<?php
// ===========================================================================================
//
// File: PAccountForgotPassword3Process.php
//
// Description: Aid when forgetting passwords, change password and perform silent login. 
// Step 3.
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
$silentLogin    = $pc->POSTisSetOrSetDefault('silent-login');

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
else if($submitAction == 'change-password') {

    $userId = $pc->SESSIONisSetOrSetDefault('accountId');
    // 
    // IAccountChangePasswordProcess
    // 
    // Preconditions:
    //
    // Variables must be defined by pagecontroller:
    // $pc
    // $userId
    // $password1
    // $password2
    // $redirectFail
    //
    // Include from pagecontroller using:
    // include(dirname(__FILE__) . '/IAccountChangePasswordProcess.php');
    //
    // Messages that may be set in session reflecting the outcome of the action:
    // changePwdFailed
    // changePwdSuccess
    //
    include(dirname(__FILE__) . '/IAccountChangePasswordProcess.php');

    // Use the account and the password to do a silent login    
    $_SESSION['silentLoginAccount']     = $pc->SESSIONisSetOrSetDefault('accountName');
    $_SESSION['silentLoginPassword']     = $password1;
    $_SESSION['silentLoginRedirect']     = $redirect;
    $pc->RedirectTo($silentLogin);
}


// -------------------------------------------------------------------------------------------
//
// Default, submit-action not supported, show error and die.
// 
die($pc->lang['SUBMIT_ACTION_NOT_SUPPORTED']);


?>
