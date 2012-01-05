<?php
// ===========================================================================================
//
// File: PAccountCreateProcess.php
//
// Description: Create a new account.
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

$passwordhashing=DB_APASSWORDHASHING;

// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$submitAction = $pc->POSTisSetOrSetDefault('submit');
$redirect = $pc->POSTisSetOrSetDefault('redirect');
$redirectFail = $pc->POSTisSetOrSetDefault('redirect-fail');
$silentLogin = $pc->POSTisSetOrSetDefault('silent-login');

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
// Change the account
//
else if($submitAction == 'account-create') {
//echo("test1");
// Get the input and check it
$account = $pc->POSTisSetOrSetDefault('account');
$password1 = $pc->POSTisSetOrSetDefault('password1');
$password2 = $pc->POSTisSetOrSetDefault('password2');

//
// Check the CAPTCHA
//
$captcha = new CCaptcha();
if(!$captcha->CheckAnswer()) {
$pc->SetSessionErrorMessage($pc->lang['CAPTCHA_FAILED']);
$pc->RedirectTo($redirectFail);
}
//echo("test2");
if(empty($account)){
	$pc->SetSessionErrorMessage($pc->lang['ACCOUNT_CANNOT_BE_EMPTY']);
	$pc->RedirectTo($redirectFail);
}
if(strlen($account)>20){
	$pc->SetSessionErrorMessage($pc->lang['ACCOUNTLENGTH_TO_LONG']);
	$pc->RedirectTo($redirectFail);
}

if(empty($password1) || empty($password2)) {
$pc->SetSessionErrorMessage($pc->lang['PASSWORD_CANNOT_BE_EMPTY']);
$pc->RedirectTo($redirectFail);
}
else if($password1 != $password2) {
$pc->SetSessionErrorMessage($pc->lang['PASSWORD_DOESNT_MATCH']);
$pc->RedirectTo($redirectFail);
}

if(strlen($password1)>32){
	$pc->SetSessionErrorMessage($pc->lang['PASSWORDLENGTH_TO_LONG']);
	$pc->RedirectTo($redirectFail);
}
// Execute the database query to make the update
$db = new CDatabaseController();
$spSPCheckORAuthenticateAccount= DBSP_SPCheckORAuthenticateAccount;
$mysqli = $db->Connect();

// Prepare query
$account = $mysqli->real_escape_string($account);
$password = $mysqli->real_escape_string($password1);

$query = <<<EOD
CALL {$spSPCheckORAuthenticateAccount}(@accountId,1,'{$account}', '{$password}', @status);
SELECT
@accountId AS accountid,
@status AS status;
EOD;

// Perform the query
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

// Get details from resultset
$row = $results[1]->fetch_object();
//echo("test3");
//echo($row->status);
if($row->status == 0) {
$pc->SetSessionErrorMessage($pc->lang['ACCOUNTNAME_ALREADY_EXISTS']);
//echo($redirectFail);
$pc->RedirectTo($redirectFail);
}
//echo("test4");
// Execute the database query to make the update
//$db = new CDatabaseController();
$spSPCreateAccount= DBSP_SPCreateAccount;
//$mysqli = $db->Connect();
//echo("test5");

// Prepare query
//$account = $mysqli->real_escape_string($account);
//$password = $mysqli->real_escape_string($password1);

$query = <<<EOD
CALL {$spSPCreateAccount}(@accountId, '{$account}', '{$password}','{$passwordhashing}', @status);
SELECT
@accountId AS accountid,
@status AS status;
EOD;
//echo("test6");

// Perform the query
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

// Get details from resultset
$row = $results[1]->fetch_object();


$results[1]->close();
$mysqli->close();
//echo("test7");

// Do a silent login and then proceed to $redirect
$_SESSION['silentLoginAccount'] = $account;
$_SESSION['silentLoginPassword'] = $password;
$_SESSION['silentLoginRedirect'] = $redirect;
$pc->RedirectTo($silentLogin);
}


// -------------------------------------------------------------------------------------------
//
// Default, submit-action not supported, show error and die.
//
die($pc->lang['SUBMIT_ACTION_NOT_SUPPORTED']);


?>
