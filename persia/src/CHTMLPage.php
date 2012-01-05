<?php
// ===========================================================================================
//
// File: CHTMLPage.php
//
// Description: Create and print a HTML page.
//
//
// Author: Mikael Roos, mos@bth.se
//

class CHTMLPage {

// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	protected $iPc;


	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	public function __construct() { 
	
		$this->iPc = new CPageController();
		$this->iPc->LoadLanguage(__FILE__);
	}



// ------------------------------------------------------------------------------------
//
// Destructor
//
public function __destruct() { ; }


// ------------------------------------------------------------------------------------
//
// Singleton, get the instance or create a new one.
//
public static function GetInstance() {

if(self::$iInstance == NULL) {
self::$iInstance = new CHTMLPage();
}
return self::$iInstance;
}


// ------------------------------------------------------------------------------------
//
// Print out a resulting page according to arguments
//
public function PrintPage($aTitle="", $aHTMLLeft="", $aHTMLMain="", $aHTMLRight="", $aHTMLHead="", $aJavaScript="", $enablejQuery=FALSE,$enableReload=FALSE) {

$titlePage = $aTitle;
$titleSite = WS_TITLE;
$language = WS_LANGUAGE;
$charset = WS_CHARSET;
$stylesheet = WS_STYLESHEET;
$favicon = WS_FAVICON;

//$apps = $this->PrepareApplicationMenu();
$apps="";
$login = $this->PrepareLoginLogoutMenu();
$nav = $this->PrepareNavigationBar();
$body = $this->PreparePageBody($aHTMLLeft, $aHTMLMain, $aHTMLRight);
//$footer = $this->PrepareFooter();
$footer="";
$w3c = $this->PrepareValidatorTools();
$w3c=str_replace("&","&amp;",$w3c);
$timer = $this->PrepareTimer();
$track = $this->PrepareGoogleAnalytics();

$jQuery = ($enablejQuery) ? "<script type='text/javascript' src='" . JS_JQUERY . "'></script> <!-- jQuery --> " : '';
$javascript = (empty($aJavaScript)) ? '' : "<script type='text/javascript'>{$aJavaScript}</script>";
if($enableReload){
	$reload="onload=\"setInterval('myReload()',10000);\"";
}else{
	$reload="";	
}
//    <body onLoad="if (location.href.indexOf('reload')==-1) location.replace(location.href+'?reload')">
//At the bottom of your page, just above the </body> you should put this:-
//
//<script type="text/javascript">
//   if(window.location.href.substr(-2) !== "?r") {
//      window.location = window.location.href + "?r";
//    }
//</script>
$reloadOnce="";

$html = <<<EOD
<!doctype html>
<html lang="{$language}">
<head>
<meta charset="{$charset}" />
<title>{$titlePage}</title>
<link rel="shortcut icon" href="{$favicon}" />
<link rel="stylesheet" href="{$stylesheet}" />
{$jQuery}
{$aHTMLHead}
{$javascript}
<!-- HTML5 support for IE -->
<!--[if IE]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</head>
<body {$reload}>
<div id='wrap'>
<div id='top'>{$login}{$apps}</div>
<div id='head'>
<div id='title'><p>{$titleSite}</p></div>
<div id='nav'>{$nav}</div>
</div>
{$body}
<div id='footer'><p>{$footer}</p></div>
<div id='bottom'><p>{$timer}{$w3c}</p></div>
</div>
{$track}
</body>
</html>

EOD;

// Print the header and page
header("Content-Type: text/html; charset={$charset}");
echo $html;
}


// ------------------------------------------------------------------------------------
//
// Prepare the apps-menu
//
public function PrepareApplicationMenu() {

$menu = unserialize(MENU_APPLICATION);

$apps = "<div id='apps'><p>";
foreach($menu as $key => $value) {
$apps .= "<a class='noUnderline' href='{$value}'>{$key}</a> ";
}
$apps .= "</p></div>";

return $apps;
}


// ------------------------------------------------------------------------------------
//
// Prepare the footer
//
public function PrepareFooter() {

$footItems = unserialize(WS_FOOTER_MENU);
$footer = WS_FOOTER;
foreach($footItems as $key => $value) {
$footer .= " <a class='noUnderline' href='{$value}'>{$key}</a> ";
}

return $footer;
}


// ------------------------------------------------------------------------------------
//
// Prepare the login-menu, changes look if user is logged in or not
//
public function PrepareLoginLogoutMenu2() {

global $gModule;

$m = "m={$gModule}&amp;";
$pc = $this->iPc;
$uc = CUserController::GetInstance();
$gravatar = $uc->GetGravatar();
$gravatar = empty($gravatar) ? '' : "<a href='?{$m}p=ucp'><img src='{$gravatar}' alt=''></a>";

$html = "";

// If user is logged in, show details about user and some links.
// If user is not logged in, show link to login-page
if($uc->IsAuthenticated()) {

     $admHtml = "";
      if($uc->IsAdministrator()) {
       $admHtml = "<a href='?{$m}p=acp'>{$pc->lang['ADMIN']}</a> ";
      }
       
      $accountname = $uc->GetAccountName();
$html = <<<EOD
<div id='loginbar'>
<p>
{$gravatar}
<a href='?{$m}p=ucp'>{$accountname}</a>
{$admHtml}
<a href='?{$m}p=logoutp'>{$pc->lang['LOGOUT']}</a>
</p>
</div>

EOD;
        }
else {

$html = <<<EOD
<div id='loginbar'>
<p>
<a href='?{$m}p=login'>{$pc->lang['LOGIN_OR_CREATE_ACCOUNT']}</a>
</p>
</div>

EOD;
}

return $html;
}
// ------------------------------------------------------------------------------------
//
// Prepare the login-menu, changes look if user is logged in or not
//
public function PrepareLoginLogoutMenu() {

global $gModule;

$m = "m={$gModule}&amp;";
$pc = $this->iPc;
$html = "";

// If user is logged in, show details about user and some links.
// If user is not logged in, show link to login-page
if(isset($_SESSION['accountUser'])) {
     $admHtml = "";

      if(isset($_SESSION['groupMemberUser']) && $_SESSION['groupMemberUser'] == 'adm') {
       $admHtml = "<a href='?{$m}p=admin'>{$pc->lang['ADMIN']}</a> ";
      }
        
$html = <<<EOD
<div id='loginbar'>
<p>
<a href='?{$m}p=account-settings'>{$_SESSION['accountUser']}</a>
<!--
{$admHtml}
-->
<a href='?{$m}p=logoutp'>{$pc->lang['LOGOUT']}</a>
</p>
</div>

EOD;
        }
else {

$html = <<<EOD
<div id='loginbar'>
<p>
<a href='?{$m}p=login'>{$pc->lang['LOGIN_OR_CREATE_ACCOUNT']}</a>
</p>
</div>

EOD;
}

return $html;
}



// ------------------------------------------------------------------------------------
//
// Prepare the navigation bar and display the item that is currently "selected".
//
public function PrepareNavigationBar() {

global $gModule, $gPage;
$menu = unserialize(MENU_NAVBAR);

$urlNav = Array();
$nav = "<ul>";
foreach($menu as $key => $value) {
$sel = '';
parse_str(parse_url(htmlspecialchars_decode($value), PHP_URL_QUERY), $urlNav);
if(!isset($urlNav['m'])) {
$urlNav['m'] = $gModule;
}
if(!isset($urlNav['p'])) {
$urlNav['p'] = $gPage;
}
if($urlNav['m'] == $gModule) {
if($urlNav['p'] == $gPage) {
$sel = " class='sel'";
} else {
// Support two levels of navigation menu
}
}
$nav .= "<li{$sel}><a href='{$value}'>{$key}</a></li>";
}
$nav .= '</ul>';

return $nav;
}


// ------------------------------------------------------------------------------------
//
// Prepare everything within the body-div
//
//
public function PreparePageBody($aBodyLeft, $aBodyMain, $aBodyRight) {

// General error/success message from session
$htmlMessage = CHTMLHelpers::GetHTMLUserFeedback('positive', CPageController::GetAndClearSessionMessage('userFeedbackPositive'));
$htmlMessage .= CHTMLHelpers::GetHTMLUserFeedback('negative', CPageController::GetAndClearSessionMessage('userFeedbackNegative'));
$htmlMessage .= CHTMLHelpers::GetHTMLUserFeedback('info', CPageController::GetAndClearSessionMessage('userFeedbackInfo'));
$htmlMessage .= CHTMLHelpers::GetHTMLUserFeedback('warning', CPageController::GetAndClearSessionMessage('userFeedbackWarning'));
$htmlMessage .= CHTMLHelpers::GetHTMLUserFeedback('notice', CPageController::GetAndClearSessionMessage('userFeedbackNotice'));

// Stylesheet must support this
// 1, 2 or 3-column layout?
// LMR, show left, main and right column
// LM, show left and main column
// MR, show main and right column
// M, show main column
//
$cols = empty($aBodyLeft) ? '' : 'L';
$cols .= empty($aBodyMain) ? '' : 'M';
$cols .= empty($aBodyRight) ? '' : 'R';

// Get content for each column, if defined, else empty
$bodyLeft = empty($aBodyLeft) ? "" : "<div id='left_{$cols}'>{$aBodyLeft}</div>";
$bodyRight = empty($aBodyRight) ? "" : "<div id='right_{$cols}'>{$aBodyRight}</div>";
$bodyMain = empty($aBodyMain) ? "" : "<div id='main_{$cols}'>{$aBodyMain}<p class='last'>&nbsp;</p></div>";

$html = <<<EOD
<div id='body'>
<div class='userFeedback'>{$htmlMessage}</div>
<div id='container_{$cols}'>
<div id='content_{$cols}'>
{$bodyLeft}
{$bodyMain}
</div> <!-- End Of #content -->
</div> <!-- End Of #container -->
{$bodyRight}
<div class='clear'>&nbsp;</div>
</div> <!-- End Of #body -->

EOD;

return $html;
}


// ------------------------------------------------------------------------------------
//
// Prepare html for validator tools
//
public function PrepareValidatorTools() {

if(!WS_VALIDATORS) { return ""; }

  $refToThisPage = CPageController::CurrentURL();
  $linkToCSSValidator = "<a href='http://jigsaw.w3.org/css-validator/check/referer'>CSS</a>";
$linkToMarkupValidator = "<a href='http://validator.w3.org/check/referer'>XHTML</a>";
$linkToCheckLinks = "<a href='http://validator.w3.org/checklink?uri={$refToThisPage}'>Links</a>";
  $linkToHTML5Validator = "<a href='http://html5.validator.nu/?doc={$refToThisPage}'>HTML5</a>";
 
return "<br />{$linkToCSSValidator} {$linkToMarkupValidator} {$linkToCheckLinks} {$linkToHTML5Validator}";
}


// ------------------------------------------------------------------------------------
//
// Prepare html for the timer
//
public function PrepareTimer() {

if(WS_TIMER) {
global $gTimerStart;
return 'Page generated in ' . round(microtime(TRUE) - $gTimerStart, 5) . ' seconds.';
}
}


// ------------------------------------------------------------------------------------
//
// Prepare code to enable Google Analytics, if enabled
//
public function PrepareGoogleAnalytics() {

$html = "";
if(defined('GA_TRACKERID') && defined('GA_DOMAIN')) {

$trackerid = GA_TRACKERID;
$domain = GA_DOMAIN;

$html = <<<EOD
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("{$trackerid}");
pageTracker._setDomainName("{$domain}");
pageTracker._trackPageview();
} catch(err) {}</script>
EOD;
}

return $html;
}
// ------------------------------------------------------------------------------------
	//
	// Static function
	// Create a URL to the current page.
	//
	public static function CurrentURL() {

		// Create link to current page
		$refToThisPage = "http";
		$refToThisPage .= (@$_SERVER["HTTPS"] == "on") ? 's' : '';
		$refToThisPage .= "://";
		$serverPort = ($_SERVER["SERVER_PORT"] == "80") ? '' : ":{$_SERVER['SERVER_PORT']}";
		$refToThisPage .= $serverPort . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		//$refToThisPage= mb_convert_encoding($refToThisPage, "utf-8", "HTML-ENTITIES" );
		//$refToThisPage = htmlspecialchars_decode(utf8_encode($refToThisPage));
		return $refToThisPage;
	}

} // End of Of Class

?>