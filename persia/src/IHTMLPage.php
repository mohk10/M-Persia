<?php
// ===========================================================================================
//
// Class CHTMLPage
//
// Creating and printing out a HTML page.
//

class CHTMLPage {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	protected $iMenu;
	protected $iStylesheet;
	protected $iPageBodyLeft;
	protected $iPageBodyRight;
	protected $iPageBodyMain;
	

	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	public function __construct($aStylesheet = WS_STYLESHEET) {
		$this->iStylesheet 		= $aStylesheet;
		$this->iMenu 			= unserialize(MENU_NAVBAR);
		$this->iPageBodyLeft	= "";
		$this->iPageBodyRight	= "";
		$this->iPageBodyMain	= "";
	}


	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() {
		;
	}


	// ------------------------------------------------------------------------------------
	//
	// Print out everything within the HEAD
	//
	public function printHTMLHeader($aTitle, $aMedia = 'screen') {

		$favicon = WS_FAVICON;

		$html = <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8;" />

	<meta name="keywords" content="" />
	<meta name="description" content="" />

	<link rel="shortcut icon" href="{$favicon}" />

	<title>{$aTitle}</title>
	<link 
	    rel='stylesheet' 
	    href='{$this->iStylesheet}' 
	    type='text/css' 
	    media='{$aMedia}'
	/>
</head>

EOD;

		echo $html;
	}


	// ------------------------------------------------------------------------------------
	//
	// Print the header-div of the webapplication
	//
	public function printPageHeader($aHeader = WS_TITLE) {
	
		global $gPage;
		
		$nav = "<ul>";
		foreach($this->iMenu as $key => $value) {
			$selected = (strcmp($gPage, substr($value, 3)) == 0) ? " class='sel'" : "";
			$nav .= "<li{$selected}><a href='{$value}'>{$key}</a></li>";
		}
		$nav .= '</ul>';
	
		$top = $this->getLoginLogoutMenu();

		$html = <<<EOD
<body>
<div id='wrap'> <!-- Start Of #wrap, wrapper for all divs -->
	<div id='top'> <!-- Top Of page (#top), above title -->
		{$top}
	</div> <!-- End Of #top -->
	<div id='head'> <!-- Start Of #head -->
		<div id='title'> <!-- Start Of #title -->
			<p>{$aHeader}</p>
		</div>  <!-- End Of #title -->
		<div id='nav'> <!-- Start Of (#nav) navigation bar -->
			{$nav}
		</div> <!-- End Of (#nav) navigation bar -->
	</div> <!-- End Of #head -->

EOD;

		echo $html;	
	}


	// ------------------------------------------------------------------------------------
	//
	// Print out everything within the body-div
	//
	//
	public function printPageBody($aBody = "") {

		// General error message from session
		$htmlErrorMessage = $this->getErrorMessage();
		
		// 1, 2 or 3-column layout? 
		// LMR, show left, main and right column
		// LM,  show left and main column
		// MR,  show main and right column
		// M,   show main column
		//
		$cols  = empty($this->iPageBodyLeft)  ? '' : 'L';
		$cols .= empty($this->iPageBodyMain)  ? '' : 'M';
		$cols .= empty($this->iPageBodyRight) ? '' : 'R';

		// Get content for each column, if defined, else empty
		$pageBodyLeft  = empty($this->iPageBodyLeft)  ? "" : "<div id='left_{$cols}'>{$this->iPageBodyLeft}</div>";
		$pageBodyRight = empty($this->iPageBodyRight) ? "" : "<div id='right_{$cols}'>{$this->iPageBodyRight}</div>";
		$pageBodyMain  = empty($this->iPageBodyMain)  ? "" : "<div id='main_{$cols}'>{$this->iPageBodyMain}<p class='last'>&nbsp;</p></div>";

		$html = <<<EOD
<div id='body'> <!-- Start Of #body -->
	{$htmlErrorMessage}
	{$aBody}
	<div id='container_{$cols}'>
		<div id='content_{$cols}'>
			{$pageBodyLeft}
			{$pageBodyMain}
		</div> <!-- End Of #content -->
	</div> <!-- End Of #container -->
	{$pageBodyRight}
	<div class='clear'>&nbsp;</div> <!-- Clearer -->
</div> <!-- End Of #body -->
EOD;

		print $html;
	}


	// ------------------------------------------------------------------------------------
	//
	// Print out the footer-div
	//
	public function printPageFooter($aFooter = WS_FOOTER) {

		$refToThisPage 			= "http://" . $_SERVER['HTTP_HOST'] . ":" . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		$linkToCSSValidator	 	= "<a href='http://jigsaw.w3.org/css-validator/check/referer'>CSS</a>";
		$linkToMarkupValidator 	= "<a href='http://validator.w3.org/check/referer'>XHTML</a>";
		$linkToCheckLinks	 	= "<a href='http://validator.w3.org/checklink?uri={$refToThisPage}'>Links</a>";

		// Format w3c statement
		$w3c = "";
		if(WS_VALIDATORS) {
			$w3c = "<br />{$linkToCSSValidator} {$linkToMarkupValidator} {$linkToCheckLinks}";
		}
		
		// Display timer if configured
		if(WS_TIMER) {
			global $gTimerStart;
			$timed = 'Page generated in ' . round(microtime(TRUE) - $gTimerStart, 5) . ' seconds.';
		}

		$html = <<<EOD
<div id='footer'> <!-- Start Of #footer -->
	<p>
	{$aFooter} 
	</p>
</div> <!-- div #footer -->
<div id='bottom'> <!-- Start Of #bottom -->
	<p>
	{$timed}
	{$w3c}
	</p>
</div> <!-- div #bottom -->
</div> <!-- div #wrap -->
</body>
</html>
EOD;

		echo $html;
	}


	// ------------------------------------------------------------------------------------
	//
	// Create the login-menu, changes look if user is logged in or not
	//
	public function getLoginLogoutMenu() {

		$html		= "";

		// If user is logged in, show details about user and some links.
		// If user is not logged in, show link to login-page
        if(isset($_SESSION['accountUser'])) {
        
        	$admHtml = "";
        	if(isset($_SESSION['groupMemberUser']) && $_SESSION['groupMemberUser'] == 'adm') {
        		$admHtml = "<a href='?p=admin'>Admin</a> ";
        	}
        
			$html = <<<EOD
<div id='loginbar'>
<p>{$_SESSION['accountUser']}   
<a href='?p=account-details'>Inställningar</a>  
{$admHtml} 
<a href='?p=logoutp'>Logga ut</a>
</p>
</div>

EOD;
        }
		else {
		
			$html = <<<EOD
<div id='loginbar'>
<a href='?p=login'>Logga in</a>
</div>
EOD;
		}
		
		return $html;	
	}


	// ------------------------------------------------------------------------------------
	//
	// Create a errormessage if its set in the SESSION
	//
	public function getErrorMessage() {

        $html = "";

        if(isset($_SESSION['errorMessage'])) {
        
            $html = <<<EOD
<div class='errorMessage'>
{$_SESSION['errorMessage']}
</div>
EOD;

            unset($_SESSION['errorMessage']);
        }

        return $html;   

	}

/*
	// ------------------------------------------------------------------------------------
	//
	// Add html to be written out in the left column
	//
	public function addPageBodyLeft($aBody) {

		$this->iPageBodyLeft .= $aBody;
	}


	// ------------------------------------------------------------------------------------
	//
	// Add html to be written out in the right column
	//
	public function addPageBodyRight($aBody) {

		$this->iPageBodyRight .= $aBody;
	}


	// ------------------------------------------------------------------------------------
	//
	// Add html to be written out in the main column
	//
	public function addPageBodyMain($aBody) {

		$this->iPageBodyMain .= $aBody;
	}

*/

	// ------------------------------------------------------------------------------------
	//
	// Print out a resulting page according to arguments
	//
	public function printPage($aHTMLLeft, $aHTMLMain, $aHTMLRight, $aTitle, $aType) {

		$this->iPageBodyLeft  .= $aHTMLLeft;
		$this->iPageBodyMain  .= $aHTMLMain;
		$this->iPageBodyRight .= $aHTMLRight;

		switch($aType) {		
			case 'BODY_ONLY': {
				$this->printHTMLHeader($aTitle);
				$this->printPageBody();
				break;
			}
			case 'FULL_PAGE':
			default: {
				$this->printHTMLHeader($aTitle);
				$this->printPageHeader();
				$this->printPageBody();
				$this->printPageFooter();
				break;
			}
		}
	}


	// ------------------------------------------------------------------------------------
	//
	// Static function
	// Redirect to another page
	// Support $aUri to be local uri within site or external site (starting with http://)
	//
	public static function redirectTo($aUri) {

		if(strncmp($aUri, "http://", 7)) {
			$aUri = WS_SITELINK . "?p={$aUri}";
		}

		header("Location: {$aUri}");
		exit;
	}


} // End of Of Class

?>