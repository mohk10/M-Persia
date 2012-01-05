<?php
// ===========================================================================================
//
// PMakeNoteProcess.php
//
// An implementation of a PHP pagecontroller for a web-site.
//
//
// -------------------------------------------------------------------------------------------
//
// Get pagecontroller helpers. Useful methods to use in most pagecontrollers
//
require_once(TP_SOURCEPATH . 'CPageController.php');

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
//  Allow only access to pagecontrollers through frontcontroller
//
//if(!isset($indexIsVisited)) die('No direct access to pagecontroller is allowed.');

function checkEmail($email) {
	if(preg_match("/^[a-zA-Z0-9_]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$]/", $email)){
return false;
	}
// -------------------------------------------------------------------------------------------
//
//  Kontrollerar om domänen existerar
//	
list($Username, $Domain) = explode("@",$email);
if(getmxrr($Domain, $MXHost)){
return true;
}
else {
try {
if(!@fsockopen($Domain, 25, $errno, $errstr, 30))
throw new Exception (mysql_error());
else{
return true;
}
}
catch (Exception $e) {
return false;
}
}
} 

// -------------------------------------------------------------------------------------------
//
// Take care of _GET variables. Store them in a variable (if they are set).
//
$Note_idComment     = isset($_POST['Note_idComment'])     ? $_POST['Note_idComment']     : '';
$titleNote     = isset($_POST['titleNote'])   ? $_POST['titleNote']     : '';
$textNote   = isset($_POST['textNote']) ? $_POST['textNote']   : '';
$emailNote    = isset($_POST['emailNote'])   ? $_POST['emailNote']     : '';

if(!is_numeric($Note_idComment)) {
    die("Note_idComment måste vara ett integer. Försök igen.");
}

if(!checkEmail($emailNote)){
	die("Epostadress felaktig");
}
// -------------------------------------------------------------------------------------------
//
// Create a new database object, we are using the MySQLi-extension.
//
require_once(TP_SQLPATH."config.php");

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

if (mysqli_connect_error()) {
   echo "Connect failed: ".mysqli_connect_error()."<br>";
   exit();
}

$mysqli->set_charset("utf8");
// -------------------------------------------------------------------------------------------
//
//Förhindra SQL injections
//
$Note_idComment  = $mysqli->real_escape_string($Note_idComment);
$titleNote  = $mysqli->real_escape_string($titleNote);
$textNote   = $mysqli->real_escape_string($textNote);
$emailNote   = $mysqli->real_escape_string($emailNote);

// -------------------------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//
$tableNotes = DB_PREFIX . 'Notes';

$query = <<<EOD
INSERT INTO {$tableNotes}
( Note_idComment,
titleNote,
textNote,
emailNote,
dateNote) 
VALUES ( {$Note_idComment},
'{$titleNote}', 
'{$textNote}',
'{$emailNote}',
NOW());
EOD;

$res = $mysqli->query($query) or die("Could not query database");

$html = <<<EOD
<article>
<h2>Lägga till kommentar för inlägget</h2>
<p>Query={$query}</p><p>Rows affected: {$mysqli->affected_rows}</p>
<p>
[ <a href='?p=visabloggar'>Visa alla bloggar</a> ]
[ <a href='?p=visainlaggkommentar&amp;idComment={$Note_idComment}'>Visa inlägg med kommentarer igen</a> ]
</p>
</article>
EOD;
// -------------------------------------------------------------------------------------------
//
// Close the connection to the database
//
$mysqli->close();

// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
/*require_once(TP_SOURCEPATH.'CHTMLPage.php');

$page = new CHTMLPage();

$page->printHTMLHeader('Lägger till kommentar för inlägget');
$page->printPageHeader();
$page->printStartSection('commentandnotes');
$page->printPageBody($html);
$page->printCloseSection();
$page->printPageFooter();*/
// -------------------------------------------------------------------------------------------
//
// Redirect to another page
//
$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'home';
header('Location: ' . WS_SITELINK . "?m=blog&p={$redirect}");
exit;
?>
