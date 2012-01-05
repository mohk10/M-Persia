<?php
// ===========================================================================================
//
// PDeleteCommentAndNotes.php
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
// Take care of GET-variables.
//


$idComment   = isset($_GET['idComment'])   ? $_GET['idComment']   : '';

if(!is_numeric($idComment)) {
  die("idComment måste vara en integer. Välj vilket inlägg du vill radera och försök igen.");
}
$idAuthor   = isset($_GET['idAuthor'])   ? $_GET['idAuthor']   : '';

if(!is_numeric($idAuthor)) {
  die("idAuthor måste vara en integer. Välj vilket inlägg du vill radera och försök igen.");
}
// -------------------------------------------------------------------------------------------
//
// interception filter
//
if(isset($_SESSION['idAuthor'])&&($idAuthor!=$_SESSION['idAuthor'])){
	die("Du har inte behörighet till denna sida");
}
//-------------------------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//
// http://se.php.net/manual/en/mysqli.query.php
//        
require_once(TP_SQLPATH."config.php");


$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);



if (mysqli_connect_error()) {
   echo "Connect failed: ".mysqli_connect_error()."<br>";
   exit();
}

$mysqli->set_charset("utf8");

$tableComments = DB_PREFIX . 'Comments';
$tableNotes = DB_PREFIX . 'Notes';

$query ="DELETE FROM {$tableComments} WHERE idComment = {$idComment} LIMIT 1;";

$query.="DELETE FROM {$tableNotes} WHERE Note_idComment = {$idComment};";

$res = $mysqli->multi_query($query) or die("Could not query database");

$html = "<h2>Radera inlägg och kommentarer</h2>";
$html .= "<p>Query={$query}</p><p>Rows affected: {$mysqli->affected_rows}</p>";
$html .= "<p><a href='?p=visabloggar'>Visa alla bloggar</a></p>";


// -------------------------------------------------------------------------------------------
//
// Close the connection to the database
//
// http://se.php.net/manual/en/mysqli.close.php
//

$mysqli->close();

$_GET['redirect']="visanamnbloggedit"."&idAuthor=".$idAuthor;
// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
/*require_once(TP_SOURCEPATH.'CHTMLPage.php');

$page = new CHTMLPage();

$page->printHTMLHeader('Radera inlägg och kommentarer');
$page->printPageHeader();
$page->printPageBody($html);
$page->printPageFooter();*/

$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'home';
header('Location: ' . WS_SITELINK . "?m=blog&p={$redirect}");
exit;
?>
