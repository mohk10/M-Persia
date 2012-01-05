<?php
// -------------------------------------------------------------------------------------------
//
// PShowTag.php
//
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

// -------------------------------------------------------------------------------------------
//
// Take care of _GET variables. Store them in a variable (if they are set).
//
$tag = isset($_GET['tag']) ? $_GET['tag'] : '';



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
// Prepare and perform a SQL query.
//

$tableComments = DB_PREFIX . 'Comments';


$query=<<<EOD
SELECT * FROM
{$tableComments}
WHERE tagsComment LIKE '%{$tag}%'    
ORDER BY dateComment DESC
EOD;



$res = $mysqli->query($query) or die("Could not query database");

$html = "";


// -------------------------------------------------------------------------------------------
//
// Show the results of the query
//

//require_once(TP_SOURCEPATH.'CHTMLPage.php');

//$stylesheet = WS_STYLESHEET;

//$page = new CHTMLPage($stylesheet);
require_once($currentDir .'src/CHTMLPage2.php');
//require_once(TP_MODULESBLOG.'stylesheet2.css');
$stylesheet ="modules/blog/stylesheet2.css" ;

$page = new CHTMLPage2($stylesheet);

while($row = $res->fetch_object()) {
	


$html.=$page->buildArticleCode2Tag($row->idComment,$row->titleComment,$row->textComment,$row->imageComment,$row->tagsComment,$row->dateComment);

}


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


$name="Taggen : {$tag}";



$page->printHTMLHeader($name);
$page->printPageHeader($name);
$page->printStartSection('commentandnotes');
$page->printPageBody($html);
$page->printCloseSection();
$page->printPageFooter();
?>
