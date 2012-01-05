<?php
// ===========================================================================================
//
// PInstallProcess.php
//
// Creates new tables in the database.
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
$htmlMain  = "";
$htmlLeft  = "";
$htmlRight = "";


// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database.
//
$mysqli = $pc->ConnectToDatabase();


// -------------------------------------------------------------------------------------------
//
// Prepare SQL.
//

$query = "";

require_once(TP_SQLPATH . "SCreateUser.php");
require_once(TP_SQLPATH . "SCreateStyle.php");
require_once(TP_SQLPATH . "SCreateText.php");

$res = $pc->MultiQuery($query);


// -------------------------------------------------------------------------------------------
//
// Retrieve and ignore the results from the above query
// Some may succed and some may fail. Must count the number of succeded 
// statements to really know.
//
$statements = 0;
do {
	$res = $mysqli->store_result();
	$statements++;
} while($mysqli->next_result());


// -------------------------------------------------------------------------------------------
//
// Prepare the text
//
$htmlMain  = "<h1>Installera databas</h1>";
$htmlMain .= "<h2>Query</h2>";
$htmlMain .= "<p><pre>{$query}</pre></p>";
$htmlMain .= "<h2>Status</h2>";
$htmlMain .= "<p>Antal lyckade statements: {$statements}</p>";
$htmlMain .= "<p><a href='?p=home'>Hem</a></p>";


// -------------------------------------------------------------------------------------------
//
// Close the connection to the database
//

$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
require_once(TP_SOURCEPATH . 'CHTMLPage.php');

$page = new CHTMLPage(WS_STYLESHEET);

$page->printPage($htmlLeft, $htmlMain, $htmlRight, 'Template', $displayAs);
exit;


?>