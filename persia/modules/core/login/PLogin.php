<?php
// ===========================================================================================
//
// PLogin.php
//
// Show a login-form, ask for user name and password.
//
// Author: Mikael Roos
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
// Always redirect to latest visited page on success.
//
$redirectTo = $pc->SESSIONisSetOrSetDefault('history2');
//echo($redirectTo);


// -------------------------------------------------------------------------------------------
//
// Enable access to protected pages through direct-links.
// Try access a protected file, redirect to login, redirect back to protected page.
//
//$redirectTo = 'home';
//if($gPage != 'login') {
//	$refToThisPage	= CHTMLPage::CurrentURL();
//	$redirectTo 	= $refToThisPage;
//}
$action = "?m=core&amp;p=loginp";
$redirect = $redirectTo;
$redirectFail = "?m=core&amp;p=login";



// -------------------------------------------------------------------------------------------
//
// Show the login-form
//
$htmlMain = <<<EOD
<h1>{$pc->lang['LOGIN']}</h1>

<p>{$pc->lang['LOGIN_INTRO_TEXT']}</p> <!-- {$pc->lang['LOGIN_USING_ACCOUNT_OR_EMAIL']} -->

<form action='{$action}' method='POST'>
<input type='hidden' name='redirect' value='{$redirect}'>
<input type='hidden' name='redirect-fail' value='{$redirectFail}'>

<fieldset class='accountsettings'>
<table class='mywidth1'>
<tr>
<td><label for="account">{$pc->lang['ACCOUNT']}</label></td>
<td style='text-align: right;'><input id='account' class='account' type='text' size=60 name='accountORemail' placeholder="account or email max 100 chars" ></td>
</tr>
<tr>
<td><label for="password">{$pc->lang['PASSWORD']}</label></td>
<td style='text-align: right;'><input  id='password' class='password' type='password' size=60 name='password' placeholder="password max 32 chars" ></td>
</tr>
<tr>
<td colspan='2' style='text-align: right;'>
<button type='submit' name='submit' value='account-create'>{$pc->lang['LOGIN']}</button>
</td>
</tr>
</table>

</fieldset>

</form>
<p>
[<a href="?m=core&amp;p=account-create">{$pc->lang['CREATE_NEW_ACCOUNT']}</a>]
[<a href="?m=core&amp;p=account-forgot-pwd1">{$pc->lang['FORGOT_PASSWORD']}</a>]
</p>
EOD;

$htmlLeft = "";

$htmlRight = <<<EOD
<section>
<h3 class='columnMenu'>Various ways to sign in</h3>
<p>
Later...
</p>
</section>
EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->printPage('Template', $htmlLeft, $htmlMain, $htmlRight);
exit;


?>