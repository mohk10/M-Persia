<?php
// ===========================================================================================
//
// File: IAccountChangePasswordProcess.php
//
// Description: Submitaction to change password. 
// To be included in a pagecontroller that has the correct environment set.
// Its in a own file since several pagecontroller needs this action.
// I considered doing this in a function, class or some other way. But
// I have not found a better solution than to simply include it. 
// This type should actually have a name, for example pagecontrollerinclude.
// Its a way to organise the functions in a webbapplication.
//
// Author: Mikael Roos, mos@bth.se
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


// -------------------------------------------------------------------------------------------
//
// General settings for this file.
//
$pc->LoadLanguage(__FILE__);


// -------------------------------------------------------------------------------------------
//
// Change the password
// 
$password1    = $pc->POSTisSetOrSetDefault('password1');
$password2    = $pc->POSTisSetOrSetDefault('password2');

if(empty($password1) || empty($password2)) {
    $pc->SetSessionMessage('changePwdFailed', $pc->lang['PASSWORD_CANNOT_BE_EMPTY']);
    $pc->RedirectTo($redirectFail);
} 
else if($password1 != $password2) {
    $pc->SetSessionMessage('changePwdFailed', $pc->lang['PASSWORD_DOESNT_MATCH']);
    $pc->RedirectTo($redirectFail);
}
// Check for stringlength. Don't cut off anything 
if(strlen($password1)>32){
	$pc->SetSessionErrorMessage($pc->lang['PASSWORDLENGTH_TO_LONG']);
	$pc->RedirectTo($redirectFail);
}

// Execute the database query to make the update
$db = new CDatabaseController();
$spSPChangeAccountInformation=	DBSP_SPChangeAccountInformation;
$mysqli = $db->Connect();

// Prepare query
$password = $mysqli->real_escape_string($password1);

//
// Can use either userid or account to find the user. 
// See implementation of the procedure.
//
//$userId=7;
$query = <<<EOD
CALL {$spSPChangeAccountInformation}(1,{$userId}, '{$password}');
EOD;

// Perform the query, 
//$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);
$db->MultiQuery($query);
//$row = $results[0]->fetch_object();

if($mysqli->affected_rows== 1) {
    $pc->SetSessionMessage('changePwdSuccess', $pc->lang['CHANGE_PASSWORD_SUCCESS']);
} else {
    $pc->SetSessionMessage('changePwdFailed', $pc->lang['PASSWORD_NOT_UPDATED']);
    $pc->RedirectTo($redirectFail);
}

//$results[1]->close();
$mysqli->close();


?>
