<?php
// ===========================================================================================
//
// File: PAccountForgotPassword1.php
//
// Description: Aid for those who forgets their password. Step 1.
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
$account    = strip_tags($pc->POSTorSESSIONisSetOrSetDefaultClearSESSION('account', ''));


// -------------------------------------------------------------------------------------------
//
// Prepare the CAPTCHA
//
$captcha = new CCaptcha();
$captchaStyle = $pc->GETIsSetOrSetDefault('captcha-style', 'custom');
$captchaLang = $pc->GETIsSetOrSetDefault('captcha-lang', 'en');
$captchaHtml = $captcha->GetHTMLToDisplay($captchaStyle,$captchaLang);

$errorm = isset($_SESSION['errorMessage']) ? $_SESSION['errorMessage'] : '';
// Get and format messages from session if they are set
$helpers = new CHTMLHelpers();
$messages = $helpers->GetHTMLForSessionMessages(
    Array(), 
    Array('forgotPwdFailed'));
// -------------------------------------------------------------------------------------------
//
// Adjustment of style for custom kff CAPTCHA
//
if(($captchaStyle=="custom")&&(!$messages['forgotPwdFailed'])){
	$mycustomstyle="style='position:absolute;top:38em;left:35em;'";
	$mycustomstyle2="style='height:20em;'";
	$mycustomstyle3="";
}else if(($captchaStyle=="custom")&&($messages['forgotPwdFailed'])){
	$mycustomstyle="style='position:absolute;top:38em;left:35em;'";
	$mycustomstyle2="style='height:24em;'";
	$mycustomstyle3="style='position:absolute;top:41em;left:6em;'";

}else{
	$mycustomstyle="style='text-align: right;'";
	$mycustomstyle2="";
	$mycustomstyle3="";
}

// -------------------------------------------------------------------------------------------
//
// Show the form
//
global $gModule;

$action             = "?m={$gModule}&amp;p=account-forgot-pwd1p";
$redirect            = "?m={$gModule}&amp;p=account-forgot-pwd2";
$redirectFail = "?m={$gModule}&amp;p=account-forgot-pwd1";


$htmlMain = <<<EOD
<h1>{$pc->lang['FORGOT_PWD_HEADER']}</h1>

<p>{$pc->lang['FORGOT_PWD_DESCRIPTION']}</p>

<form action='{$action}' method='POST'>
<input type='hidden' name='redirect'             value='{$redirect}'>
<input type='hidden' name='redirect-fail' value='{$redirectFail}'>

<fieldset class='accountsettings' {$mycustomstyle2}>
<table class="mywidth1">

<tr>
<td><label for="account">{$pc->lang['FORGOT_PWD_ACCOUNT_NAME_LABEL']}</label></td>
<td style='text-align: right;'><input id='account' class='account' type='text' name='account' value='{$account}' size=60 autofocus></td>
</tr>

<tr>
<td><label for="recaptcha_response_field">{$pc->lang['FORGOT_PWD_MAGIC']}</label></td>
<td><div style='float: right'>{$captchaHtml}</div></td>
</tr>
<tr><td colspan='2' {$mycustomstyle3}>{$messages['forgotPwdFailed']}</td></tr>
<tr>
<td colspan='2' {$mycustomstyle}>
<button type='submit' name='submit' value='send-rescue-mail'>{$pc->lang['SEND_RESCUE_MAIL']}</button>
</td>
</tr>
</table>
</fieldset>

</form>

EOD;

//
// Enable changing and referencing parts of the current url
//
$links  = "<a href='" . $pc->ModifyCurrentURL('captcha-style=red') .                 "'>red</a> ";
$links .= "<a href='" . $pc->ModifyCurrentURL('captcha-style=white') .             "'>white</a> ";
$links .= "<a href='" . $pc->ModifyCurrentURL('captcha-style=blackglass') . "'>blackglass</a> ";
$links .= "<a href='" . $pc->ModifyCurrentURL('captcha-style=clean') .             "'>clean</a> ";
$links .= "<a href='" . $pc->ModifyCurrentURL('captcha-style=custom') .         "'>custom kff</a> ";
$links2= "<ul><li><a href='" . $pc->ModifyCurrentURL('captcha-lang=en') .         "'>english</a></li>";
$links2.= "<li><a href='" . $pc->ModifyCurrentURL('captcha-lang=es') .         "'>spanish</a></li> ";
$links2.= "<li><a href='" . $pc->ModifyCurrentURL('captcha-lang=de') .         "'>german</a></li>";
$links2.= "<li><a href='" . $pc->ModifyCurrentURL('captcha-lang=fr') .         "'>french</a></li>";
$links2.= "<li><a href='" . $pc->ModifyCurrentURL('captcha-lang=sv') .         "'>SWEDISH</a></li></ul>";

$htmlLeft     = "";
$htmlRight    = <<<EOD
<h3 class='columnMenu'>Style the reCAPTCHA widget</h3>
<p>
{$links}
</p>
<p>
{$links2}
</p>
EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->printPage($pc->lang['FORGOT_PWD_TITLE'], $htmlLeft, $htmlMain, $htmlRight);
exit;

?>
