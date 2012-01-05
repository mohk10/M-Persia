<?php
// ===========================================================================================
//
// File: PAccountForgotPassword3.php
//
// Description: Aid for those who forgets their password. Step 3.
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
// Enable custom filter (för att funka)
//$intFilter->CustomFilterIsSetOrDie('resetPassword', 'set');
$intFilter->CustomFilterIsSetOrDie('resetPassword');


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$account    = $pc->SESSIONisSetOrSetDefault('accountName', '');


// -------------------------------------------------------------------------------------------
//
// Show the form
//
global $gModule;

$action             = "?m={$gModule}&amp;p=account-forgot-pwd3p";
$redirect         = "?m={$gModule}&amp;p=account-forgot-pwd4";
$redirectFail = "?m={$gModule}&amp;p=account-forgot-pwd3";
$silentLogin     = "?m={$gModule}&amp;p=loginp";

// Get and format messages from session if they are set
$helpers = new CHTMLHelpers();
$messages = $helpers->GetHTMLForSessionMessages(
    Array('keySuccess'), 
    Array('changePwdFailed'));

$htmlMain = <<<EOD
<h1>{$pc->lang['FORGOT_PWD_HEADER']}</h1>

{$messages['keySuccess']}
<p>{$pc->lang['FORGOT_PWD_DESCRIPTION']}</p>

<form action='{$action}' method='POST'>
<input type='hidden' name='redirect'             value='{$redirect}'>
<input type='hidden' name='redirect-fail'    value='{$redirectFail}'>
<input type='hidden' name='silent-login'     value='{$silentLogin}'>

<fieldset class='accountsettings'>
<table width='99%'>
<tr>
<td><label for="account">{$pc->lang['ACCOUNT_NAME_LABEL']}</label></td>
<td style='text-align: right;'><input class='account-dimmed' type='text' name='account' readonly value='{$account}'></td>
</tr>
<tr>
<td><label for="password1">{$pc->lang['ACCOUNT_PASSWORD_LABEL']}</label></td>
<td style='text-align: right;'><input class='password' type='password' name='password1'></td>
</tr>
<tr>
<td><label for="password2">{$pc->lang['ACCOUNT_PASSWORD_AGAIN_LABEL']}</label></td>
<td style='text-align: right;'><input class='password' type='password' name='password2'></td>
</tr>
<tr>
<td colspan='2' style='text-align: right;'>
<button type='submit' name='submit' value='change-password'>{$pc->lang['CHANGE_PASSWORD']}</button>
</td>
</tr>

<tr><td colspan='2'>{$messages['changePwdFailed']}</td></tr>

</table>
</fieldset>
</form>

EOD;

//
// 
//
$htmlLeft     = "";
$htmlRight    = <<<EOD
<h3 class='columnMenu'></h3>
<p></p>

EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->printPage($pc->lang['FORGOT_PWD_TITLE'], $htmlLeft, $htmlMain, $htmlRight);
exit;

?>
