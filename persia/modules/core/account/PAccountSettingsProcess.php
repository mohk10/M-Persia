<?php
// ===========================================================================================
//
// File: PAccountSettingsProcess.php
//
// Description: Save changes in profile and account settings.
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
//$intFilter->UserIsMemberOfGroupAdminOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$submitAction = $pc->POSTisSetOrSetDefault('submit');
$accountId = $pc->POSTisSetOrSetDefault('accountid');
$redirect = $pc->POSTisSetOrSetDefault('redirect');
$redirectFail = $pc->POSTisSetOrSetDefault('redirect-failure');
$userId = $_SESSION['idUser'];

//echo($redirectFail);
// -------------------------------------------------------------------------------------------
//
// Depending on the submit-action, do whats to be done. If, else if, else, replaces switch.
//

// -------------------------------------------------------------------------------------------
//
// Do some insane checking to avoid misusage, errormessage if not correct.
//
// Are we trying to change the same user profile as is signed in? Must be Yes.
//
if($userId != $accountId) {
$pc->SetSessionErrorMessage($pc->lang['MISMATCH_SESSION_AND_SETTINGS']);
$pc->RedirectTo($redirectFail);
}


// -------------------------------------------------------------------------------------------
//
// Change the password
//
else if($submitAction == 'change-password') {

$password1 = $pc->POSTisSetOrSetDefault('password1');
$password2 = $pc->POSTisSetOrSetDefault('password2');

if(empty($password1) || empty($password2)) {
$pc->SetSessionErrorMessage($pc->lang['PASSWORD_CANNOT_BE_EMPTY']);
$pc->RedirectTo($redirectFail);
}
else if($password1 != $password2) {
$pc->SetSessionErrorMessage($pc->lang['PASSWORD_DOESNT_MATCH']);
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

$query = <<<EOD
CALL {$spSPChangeAccountInformation}(1,{$userId}, '{$password}');
EOD;
//@aUserId
// Perform the query, ignore the results
$db->DoMultiQueryRetrieveAndStoreResultset($query);

$mysqli->close();

// Redirect to resultpage
$pc->RedirectTo($redirect);
}


// -------------------------------------------------------------------------------------------
//
// Change email
//
else if($submitAction == 'change-email') {

$email = $pc->POSTisSetOrSetDefault('email');
// Check for stringlength. Don't cut off anything 
if(strlen($email)>100){
		//echo($redirectFail);
	$pc->SetSessionErrorMessage($pc->lang['EMAILLENGTH_TO_LONG']);
	$pc->RedirectTo($redirectFail);
}
// Execute the database query to make the update
$db = new CDatabaseController();
$spSPChangeAccountInformation=	DBSP_SPChangeAccountInformation;

$mysqli = $db->Connect();

// Prepare query
$email = $mysqli->real_escape_string($email);

$query = <<<EOD
CALL {$spSPChangeAccountInformation}(2,{$userId}, '{$email}');
EOD;

// Perform the query, ignore the results
$db->MultiQuery($query);
//$db->DoMultiQueryRetrieveAndStoreResultset($query);

//echo("mysqli->affected_rows=".$mysqli->affected_rows);
//echo("results->affected_rows".$mysqli->affected_rows);
    if($mysqli->affected_rows== 1) {
    
        // Send a mail to the new mailadress
        $mail = new CMail();
        $from = "no-reply@nowhere.org";
        
        $message = <<<EOD
Välkommen,
Här kommer ett mail till din nya mailadress.

Bästa hälsningar,
M-Persia
EOD;

        $r = $mail->SendMail($email, $from, "Ny mailadress registrerad", $message);

        if($r) {
            $pc->SetSessionMessage('mailMessage', "Successfully sent mail to {$email}.");
        }else {
            $pc->SetSessionMessage('mailMessage', "Failed to send mail to {$email}. Perhaps malformed mailadress?");
        }
    
    }

$mysqli->close();

// Redirect to resultpage
$pc->RedirectTo($redirect);
}


// -------------------------------------------------------------------------------------------
//
// Change avatar
//
else if($submitAction == 'change-avatar') {

$avatar = $pc->POSTisSetOrSetDefault('avatar');

// Execute the database query to make the update
$db = new CDatabaseController();
$spSPChangeAccountInformation=	DBSP_SPChangeAccountInformation;

$mysqli = $db->Connect();

// Prepare query
$avatar = $mysqli->real_escape_string($avatar);

$query = <<<EOD
CALL {$spSPChangeAccountInformation}(3,{$userId}, '{$avatar}');
EOD;

// Perform the query, ignore the results
$db->DoMultiQueryRetrieveAndStoreResultset($query);

$mysqli->close();

// Redirect to resultpage
$pc->RedirectTo($redirect);
}
// -------------------------------------------------------------------------------------------
//
// Change gravatar
// 
else if($submitAction == 'change-gravatar') {

    $gravatar    = $pc->POSTisSetOrSetDefault('gravatar');
    //echo($redirectFail);
// Check for stringlength. Don't cut off anything     
    if(strlen($gravatar)>100){
		//echo($redirectFail);
	$pc->SetSessionErrorMessage($pc->lang['EMAILLENGTH_TO_LONG']);
	$pc->RedirectTo($redirectFail);
}

    // Execute the database query to make the update
    $db = new CDatabaseController();
    $spSPChangeAccountInformation=	DBSP_SPChangeAccountInformation;
    
    $mysqli = $db->Connect();

    // Prepare query
    $avatar = $mysqli->real_escape_string($gravatar);

    $query = <<<EOD
CALL {$spSPChangeAccountInformation}(4,{$userId}, '{$gravatar}');
EOD;

    // Perform the query, ignore the results
    $db->DoMultiQueryRetrieveAndStoreResultset($query);

    $mysqli->close();

    // Redirect to resultpage
    $pc->RedirectTo($redirect);
}

// -------------------------------------------------------------------------------------------
//
// Default, submit-action not supported, show error and die.
//
die($pc->lang['SUBMIT_ACTION_NOT_SUPPORTED']);


?>



