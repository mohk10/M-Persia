<?php
// ===========================================================================================
//
// PTemplate.php
//
// A standard template page for a pagecontroller.
//
//
// Global $_GET:
// ['display']: Vary how CHTMLPage prints out the resulting page. Look in CHTMLPage for 
// supported arguments.
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

$htmlMain = <<<EOD
<h2>Template</h2>
<h3>Introduktion</h3>
<p>
Kopiera denna template sida för att skapa nya pagecontrollers. 
</p>
<p>
<a href='?p=ls&amp;dir=pages/home&amp;file=PTemplate.php'>Källkoden till sidan ser du här</a>.
</p>
EOD;

$htmlLeft = <<<EOD
<h3 class='columnMenu'>Bra att ha vänster</h3>
<p>
Här finns nu en meny kolumn som går att använda till bra att ha saker.
</p>
EOD;

$htmlRight = <<<EOD
<h3 class='columnMenu'>Bra att ha höger</h3>
<p>
Här finns nu en meny kolumn som går att använda till bra att ha saker.
</p>
EOD;

// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database.
//
/*
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

if (mysqli_connect_error()) {
   echo "Connect failed: ".mysqli_connect_error()."<br>";
   exit();
}
*/


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
/*
$user 		= isset($_POST['nameUser']) ? $_POST['nameUser'] : '';
$password 	= isset($_POST['passwordUser']) ? $_POST['passwordUser'] : '';

// Prevent SQL injections
$user 		= $mysqli->real_escape_string($user);
$password 	= $mysqli->real_escape_string($password);
*/


// -------------------------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//
/*
$tableTable	= DB_PREFIX . 'Table';

$query = <<< EOD
;
EOD;

$res = $mysqli->query($query) 
                    or die("<p>Could not query database,</p><code>{$query}</code>");
*/


// -------------------------------------------------------------------------------------------
//
// Use the results of the query 
//
/*
$res->close();
*/

// -------------------------------------------------------------------------------------------
//
// Close the connection to the database
//
/*
$mysqli->close();
*/


// -------------------------------------------------------------------------------------------
//
// Redirect to another page
// Support $redirect to be local uri within site or external site (starting with http://)
//
/*
require_once(TP_SOURCEPATH . 'CHTMLPage.php');

$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'home';

CHTMLPage::redirectTo($redirect);
exit,
*/

// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
require_once(TP_SOURCEPATH . 'CHTMLPage.php');

$page = new CHTMLPage(WS_STYLESHEET);

$page->printPage($htmlLeft, $htmlMain, $htmlRight, 'Template', $displayAs);
exit;

?>
