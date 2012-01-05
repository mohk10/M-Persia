<?php
// ===========================================================================================
//
// PInstall.php
//
// Info page for installation. Links to page for creating tables in the database.
//


// -------------------------------------------------------------------------------------------
//
// Get pagecontroller helpers. Useful methods to use in most pagecontrollers
//
require_once(TP_SOURCEPATH . 'CPagecontroller.php');

$pc = new CPagecontroller();


// -------------------------------------------------------------------------------------------
//
// Interception Filter, access, authorithy and other checks.
//
require_once(TP_SOURCEPATH . 'CInterceptionFilter.php');

$intFilter = new CInterceptionFilter();

$intFilter->frontcontrollerIsVisitedOrDie();
//$intFilter->userIsSignedInOrRecirectToSign_in();
//$intFilter->userIsMemberOfGroupAdminOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of global pageController settings, can exist for several pagecontrollers.
// Decide how page is displayed, review CHTMLPage for supported types.
//
$displayAs = $pc->GETisSetOrSetDefault('pc_display', '');


// -------------------------------------------------------------------------------------------
//
// Page specific code
//
$database 	= DB_DATABASE;
$prefix		= DB_PREFIX;

$htmlMain = <<<EOD
<h1>Installation</h1>
<h2>Skapa tabeller</h2>
<p>
Klicka på nedanstående länk för att radera databasen på allt innehåll och skapa nya tabeller. 
Du har valt databasen '{$database}' och tabellerna kommer skapas med prefixet '{$prefix}'. Ändra i
config.php om detta inte stämmer.
</p>
<p>
<a href='?p=installp'>Töm databasen och skapa nya tabeller</a>.
</p>
EOD;

$htmlLeft 	= "";
$htmlRight 	= "";


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
require_once(TP_SOURCEPATH . 'CHTMLPage.php');

$page = new CHTMLPage(WS_STYLESHEET);

$page->printPage($htmlLeft, $htmlMain, $htmlRight, 'Template', $displayAs);
exit;


?>