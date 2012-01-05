<?php
// ===========================================================================================
//
// PShowCommentAndNotes.php
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

// -------------------------------------------------------------------------------------------
//
// Page specific code
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
// Take care of _GET variables. Store them in a variable (if they are set).
//
$idComment = isset($_GET['idComment']) ? $_GET['idComment'] : '';

if(!is_numeric($idComment)) {
    die("idComment måste vara ett integer. Försök igen.");
}

// -------------------------------------------------------------------------------------------
//
// Prepare and perform SQL query.
//
$tableComments = DB_PREFIX . 'Comments';
$tableNotes    = DB_PREFIX . 'Notes';
$query=<<<EOD
SELECT * FROM(
 {$tableComments} AS C
 LEFT OUTER JOIN {$tableNotes} AS N
   ON C.idComment=N.Note_idComment)
WHERE C.idComment={$idComment}
ORDER BY N.dateNote;
EOD;

$res=$mysqli->query($query) or die("Could not query database");
// -------------------------------------------------------------------------------------------
//
// Show the results of the query
//

//require_once(TP_SOURCEPATH.'CHTMLPage.php');

//$stylesheet = WS_STYLESHEET;
require_once($currentDir .'src/CHTMLPage2.php');
//require_once(TP_MODULESBLOG.'stylesheet2.css');
$stylesheet ="modules/blog/stylesheet2.css" ;

$page = new CHTMLPage2($stylesheet);


//$page = new CHTMLPage($stylesheet);

$row = $res->fetch_object();

$html=$page->buildArticleCode2($row->titleComment,$row->textComment,$row->imageComment,$row->tagsComment,$row->dateComment);
if(!is_null($row->idNote)){
		$html.=$page->buildArticleCode3($row->titleNote,$row->textNote,$row->emailNote,$row->dateNote);

}
while((!is_null($row->idNote))&&$row = $res->fetch_object()) {
	

$html.=$page->buildArticleCode3($row->titleNote,$row->textNote,$row->emailNote,$row->dateNote);

}
$html.="<br /><br />";

$res->close();
// -------------------------------------------------------------------------------------------
//
// Close the connection to the database
//
$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//


$page->printHTMLHeader('Visa inlägg med kommentarer');
$page->printPageHeader('Fogglers blogg');
$page->printStartSection('commentandnotes');
$page->printPageBody($html);
$page->printCloseSection();
$page->printPageFooter();
?>
