<?php
// ===========================================================================================
//
// File: PAccountSettings.php
//
// Description: Show the users profile information in a form and make it possible to edit
// the information.
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
//$intFilter->UserIsSignedInOrRedirectToSignIn();
//$intFilter->UserIsMemberOfGroupAdminOrDie();

// Get messages from session if they are set
$mailMessage = $pc->GetSessionMessage('mailMessage');
$mailMessage = empty($mailMessage) ? '' : "<div class='userFeedback' style=\"background: url('img/silk/accept.png') no-repeat;\"><br />{$mailMessage}</div>";

// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
//$topicId = $pc->GETisSetOrSetDefault('id', 0);
//$userId=$pc->SESSIONisSetOrSetDefault('idUser',0);
$userId = $_SESSION['idUser'];

// Always check whats coming in...
//$pc->IsNumericOrDie($topicId, 0);

// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database, get the query and execute it.
// Relates to files in directory TP_SQLPATH.
//
$db = new CDatabaseController();
$spSPGetAccountDetails= DBSP_SPGetAccountDetails;

$mysqli = $db->Connect();

$query = <<< EOD
CALL {$spSPGetAccountDetails}({$userId});
EOD;

// Perform the query
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

// Get account details
$row = $results[0]->fetch_object();
$account = $row->account;
//$name = $row->name;
$email = $row->email;
$avatar = $row->avatar;
$gravatar = $row->gravatar;
$gravatarsmall    = $row->gravatarsmall;
$groupakronym = $row->groupakronym;
$groupdesc = $row->groupdesc;
$results[0]->close();

$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Page specific code
//
global $gModule;

$action = "?m={$gModule}&amp;p=account-update";
$redirect = "?m={$gModule}&amp;p=account-settings";
$imageLink = WS_IMAGES;
//echo($redirect);
$htmlMain = <<< EOD
<h1>{$pc->lang['MANAGE_ACCOUNT']}</h1>

<h2 id='basic'>{$pc->lang['BASIC_ACCOUNT_INFO']}</h2>
<form action='{$action}' method='POST'>
<input type='hidden' name='redirect' value='{$redirect}#basic'>
<input type='hidden' name='redirect-failure' value='{$redirect}'>
<input type='hidden' name='accountid' value='{$userId}'>
<fieldset class='accountsettings'>
<table class="mywidth1">
<tr>
<td><label for="account">{$pc->lang['ACCOUNT_NAME_LABEL']}</label></td>
<td style='text-align: right;'><input class='account-dimmed' type='text' name='account' size=60 readonly value='{$account}'></td>
</tr>
<tr>
<td><label for="account">{$pc->lang['ACCOUNT_PASSWORD_LABEL']}</label></td>
<td style='text-align: right;'><input class='password' type='password' name='password1' size=60 ></td>
</tr>
<tr>
<td><label for="account">{$pc->lang['ACCOUNT_PASSWORD_AGAIN_LABEL']}</label></td>
<td style='text-align: right;'><input class='password' type='password' name='password2' size=60 ></td>
</tr>
<tr>
<td colspan='2' style='text-align: right;'>
<button type='submit' name='submit' value='change-password'>{$pc->lang['CHANGE_PASSWORD']}</button>
</td>
</tr>
</table>
</fieldset>
</form>

<h2 id='email'>{$pc->lang['EMAIL_SETTINGS']}</h2>
<form action='{$action}' method='POST'>
<input type='hidden' name='redirect' value='{$redirect}#email'>
<input type='hidden' name='redirect-failure' value='{$redirect}'>
<input type='hidden' name='accountid' value='{$userId}'>
<fieldset class='accountsettings'>
<table class="mywidth1">
<tr>
<td><label for="account">{$pc->lang['EMAIL_LABEL']}</label></td>
<td style='text-align: right;'>
<input class='email' type='email' name='email' value='{$email}' placeholder="{$pc->lang['INSERT_EMAIL_HERE']}" autocomplete
required pattern="^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*\.(\w{2}|(com|net|org|edu|int|mil|gov|arpa|biz|aero|name|coop|info|pro|museum))$" size=60 title="{$pc->lang['EMAIL_FORMAT_REQUIRED']}">
</td>
</tr>
<tr>
<td colspan='2' style='text-align: right;'>
<button type='submit' name='submit' value='change-email'>{$pc->lang['UPDATE_EMAIL']}</button>
</td>
</tr>
<tr>
<td colspan='2'>
<div class='userFeedback'>{$mailMessage}</div>
</td>
</tr>
</table>
</fieldset>
</form>

<h2 id='avatar'>{$pc->lang['AVATAR_SETTINGS']}</h2>
<form action='{$action}' method='POST'>
<input type='hidden' name='redirect' value='{$redirect}#avatar'>
<input type='hidden' name='redirect-failure' value='{$redirect}'>
<input type='hidden' name='accountid' value='{$userId}'>
<fieldset class='accountsettings'>
<table width='99%'>
<tr>
<td><label for="account">{$pc->lang['AVATAR_LABEL']}</label></td>
<td style='text-align: right;'>
<input class='avatar' type='url' list='avatars' name='avatar' value='{$avatar}' placeholder="{$pc->lang['INSERT_LINKT_TO_AVATAR_HERE']}" autocomplete size=60 >

<!-- datalist only supported in Opera
<datalist id='avatars'>
<option>{$imageLink}man_60x60.png</option>
<option>{$imageLink}woman_60x60.png</option>
</datalist>
-->

</td>
</tr>
<tr>
<td>
<img src='{$row->avatar}' alt=':)'>
</td>
<td style='text-align: right;'>
<button type='submit' name='submit' value='change-avatar'>{$pc->lang['UPDATE_AVATAR']}</button>
</td>
</tr>
</table>
</fieldset>
</form>
<h2 id='gravatar'>{$pc->lang['GRAVATAR_SETTINGS']}</h2>
<form action='{$action}' method='POST'>
<input type='hidden' name='redirect'                     value='{$redirect}#gravatar'>
<input type='hidden' name='redirect-failure'     value='{$redirect}'>
<input type='hidden' name='accountid'                 value='{$userId}'>
<fieldset class='accountsettings'>
<table width='99%'>
<tr>
<td colspan='2'>
<p>
{$pc->lang['GRAVATAR_INFO']}
</p>
</td>
</tr>
<td><label for="gravatar">{$pc->lang['GRAVATAR_LABEL']}</label></td>
<td style='text-align: right;'>
<input class='gravatar' type='email' name='gravatar' value='{$gravatar}' size=60 placeholder="{$pc->lang['INSERT_EMAIL_FOR_GRAVATAR_HERE']}" autocomplete>
</td>
</tr>
<tr>
<td>
<img src='{$row->gravatarsmall}' alt=''>
</td>
<td style='text-align: right;'>
<button type='submit' name='submit' value='change-gravatar'>{$pc->lang['UPDATE_GRAVATAR']}</button>
</td>
</tr>
</table>
</fieldset>
</form>
<!--
<h2>{$pc->lang['GROUP_SETTINGS']}</h2>
<fieldset class='accountsettings'>
<table width='99%'>
<tr>
<td>{$pc->lang['GROUPMEMBER_OF_LABEL']}</td>
<td style='text-align: right;'><input class='groups' type='text' name='groups' value='{$groupakronym}'></td>
</tr>
<tr>
<td colspan='2' style='text-align: right;'>
<button type='submit' name='submit' value='change-groups'>{$pc->lang['UPDATE_GROUPS']}</button>
</td>
</tr>
</table>
</fieldset>
-->
EOD;


$htmlLeft = "";
$htmlRight = <<<EOD
<h3 class='columnMenu'>About Privacy</h3>
<p>
Later...
</p>

EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->PrintPage(sprintf($pc->lang['SETTINGS_FOR'], $account), $htmlLeft, $htmlMain, $htmlRight);
exit;

?>


