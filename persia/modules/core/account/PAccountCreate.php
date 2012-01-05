<?php
// ===========================================================================================
//
// File: PAccountCreate.php
//
// Description: Form to create a new account.
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
$account = $pc->GETisSetOrSetDefault('account', "");

// -------------------------------------------------------------------------------------------
//
// Always redirect to latest visited page on success.
//
$redirectTo = $pc->SESSIONisSetOrSetDefault('history2');
$page = new CHTMLPage();
$session=$pc->GetSessionMessage('errorMessage');
//$errorm = isset($_SESSION['errorMessage']) ? $_SESSION['errorMessage'] : '';
//$_SESSION['errorMessage'];
//echo($errorm);
// -------------------------------------------------------------------------------------------
//
// Prepare the CAPTCHA
//
$captcha = new CCaptcha();
$captchaStyle = $pc->GETIsSetOrSetDefault('captcha-style', 'custom');
$captchaLang = $pc->GETIsSetOrSetDefault('captcha-lang', 'en');
$captchaHtml = $captcha->GetHTMLToDisplay($captchaStyle,$captchaLang);
// -------------------------------------------------------------------------------------------
//
// Adjustment of style for custom kff CAPTCHA
//
if(($captchaStyle=="custom")&&(empty($session))){
	$mycustomstyle="style='position:absolute;top:42em;left:35em;'";
	$mycustomstyle2="style='height:26em;'";
	$mycustomstyle3="style='position:absolute;top:44em;'";
}else if(($captchaStyle=="custom")&&($session)){
	$mycustomstyle="style='position:absolute;top:42em;left:35em;'";
	$mycustomstyle2="style='height:28em;'";
	$mycustomstyle3="style='position:absolute;top:44em;'";
}else{
	$mycustomstyle="style='text-align: right;'";
	$mycustomstyle2="";
	$mycustomstyle3="";
}
// -------------------------------------------------------------------------------------------
//
// Show the login-form
//
global $gModule;

$action = "?m={$gModule}&amp;p=account-createp";
$redirect = "?m={$gModule}&amp;p=account-settings";
$redirectFail = "?m={$gModule}&amp;p=account-create";
$silentLogin = "?m={$gModule}&amp;p=loginp";

$htmlMain = <<<EOD
<h1>{$pc->lang['CREATE_NEW_ACCOUNT_TITLE']}</h1>

<p>{$pc->lang['CHOOSE_NAME_AND_PASSWORD']}</p>

<form action='{$action}' method='POST' >
<input type='hidden' name='redirect' value='{$redirect}'>
<input type='hidden' name='redirect-fail' value='{$redirectFail}'>
<input type='hidden' name='silent-login' value='{$silentLogin}'>

<fieldset class='accountsettings' {$mycustomstyle2}>
<table class="mywidth1">
<tr>
<td><label for="account">{$pc->lang['ACCOUNT_NAME_LABEL']}</label></td>
<td style='text-align: right;'><input id='account' class='account' type='text' name='account' value='{$account}' size=60 placeholder="account max 20 chars" ></td>
</tr>
<tr>
<td><label for="password1">{$pc->lang['ACCOUNT_PASSWORD_LABEL']}</label></td>
<td style='text-align: right;'><input class='password' type='password' name='password1' id='password1' size=60 placeholder="password max 32 chars"></td>
</tr>
<tr>
<td><label for="password2">{$pc->lang['ACCOUNT_PASSWORD_AGAIN_LABEL']}</label></td>
<td style='text-align: right;'><input class='password' type='password' name='password2' id='password2' size=60 placeholder="password(again)" ></td>
</tr>
<tr>
<td><label for="recaptcha_response_field">{$pc->lang['ACCOUNT_NAME_MAGIC']}</label></td>
<td><div style='float: right'>{$captchaHtml}</div></td>
</tr>
<tr><td colspan='2' {$mycustomstyle3}>{$session}</td></tr>
<tr>
<td colspan='2' {$mycustomstyle}>
<button type='submit' name='submit' value='account-create'>{$pc->lang['CREATE_ACCOUNT']}</button>
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

$page->printPage($pc->lang['CREATE_NEW_ACCOUNT_TITLE'], $htmlLeft, $htmlMain, $htmlRight);
exit;

?>
