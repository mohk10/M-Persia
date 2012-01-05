<?php
// ===========================================================================================
//
// File: PLoginProcess.php
//
// Description: Verify user and password. Create a session and store userinfo in.
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
$accountORemail = $pc->POSTisSetOrSetDefault('accountORemail');
$password = $pc->POSTisSetOrSetDefault('password');
$redirect = $pc->POSTisSetOrSetDefault('redirect');
$redirectFail = $pc->POSTisSetOrSetDefault('redirect-fail');
$silent = $pc->SESSIONIsSetOrSetDefault('silentLoginAccount');

//
// Check if this is a silent login attempt where another page is initiating the login process.
// For example when creating a new account and login simoultaneously.
// Then get the login info from the session instead from the POST.
//
if(!empty($silent)) {

$accountORemail = $pc->SESSIONIsSetOrSetDefault('silentLoginAccount');
$password = $pc->SESSIONIsSetOrSetDefault('silentLoginPassword');
$redirect = $pc->SESSIONIsSetOrSetDefault('silentLoginRedirect');

unset($_SESSION['silentLoginAccount']);
unset($_SESSION['silentLoginPassword']);
unset($_SESSION['silentLoginRedirect']);
}


// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database, get the query and execute it.
// Relates to files in directory TP_SQLPATH.
//
$db = new CDatabaseController();
$spSPCheckORAuthenticateAccount=	DBSP_SPCheckORAuthenticateAccount;
$spSPGetAccountDetails=			DBSP_SPGetAccountDetails;
$mysqli = $db->Connect();

if(empty($accountORemail) || empty($password)) {
$pc->RedirectTo($redirectFail);
}

// -------------------------------------------------------------------------------------------
//
// First: Prepare and do query to see if account exists and matches password
//
$accountORemail = $mysqli->real_escape_string($accountORemail);
$password = $mysqli->real_escape_string($password);

if(strlen($accountORemail)>100){
	$pc->SetSessionErrorMessage($pc->lang['ACCOUNTLENGTH_TO_LONG']);
	$pc->RedirectTo($redirectFail);
}

if(strlen($password)>32){
	$pc->SetSessionErrorMessage($pc->lang['PASSWORDLENGTH_TO_LONG']);
	$pc->RedirectTo($redirectFail);
}

$query = <<<EOD
CALL {$spSPCheckORAuthenticateAccount}(@accountId,2, '{$accountORemail}', '{$password}', @status);
SELECT
@accountId AS accountid,
@status AS status;
EOD;

// Perform the query
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

// Get details from resultset
$row = $results[1]->fetch_object();

if($row->status == 1) {
$pc->SetSessionErrorMessage($pc->lang['AUTHENTICATION_FAILED']);
$pc->RedirectTo($redirectFail);
}

$accountId = $row->accountid;

$results[1]->close();


// -------------------------------------------------------------------------------------------
//
// Second: Get details about this account, populate in an account-object.
//
$query = <<< EOD
CALL {$spSPGetAccountDetails}({$accountId});
EOD;

// Perform the query
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

// Get account details
$row = $results[0]->fetch_object();
$account = $row->account;
//$name = $row->name;
$email = $row->email;
$avatar = $row->avatar;
$groupakronym = $row->groupakronym;
$groupdesc = $row->groupdesc;
$results[0]->close();

$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Third: Populate the session
//
// Destroy the current session (logout user), if it exists.
// Remember where we are going, this enables us to redirect to the initial pagerequest,
// even after several unsuccessfull login attempts.
//
require_once(TP_SOURCEPATH . 'FDestroySession.php');
//$_SESSION['history1'] = $pc->POSTisSetOrSetDefault('redirect');

session_start(); // Must call it since we destroyed it above.
session_regenerate_id(); // To avoid problems

$_SESSION['idUser'] = $accountId;
$_SESSION['accountUser'] = $account;
$_SESSION['groupMemberUser'] = $groupakronym;


// -------------------------------------------------------------------------------------------
//
// Redirect to another page
//
$pc->RedirectTo($redirect);

?>