<?php
// ===========================================================================================
//
// PEditCommentProcess.php
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
// Take care of _GET variables. Store them in a variable (if they are set).
//

$Comment_idAuthor= isset($_POST['Comment_idAuthor'])     ? $_POST['Comment_idAuthor']     : '';
$idComment     = isset($_POST['idComment'])     ? $_POST['idComment']     : '';
$titleComment     = isset($_POST['titleComment'])   ? $_POST['titleComment']     : '';
$textComment   = isset($_POST['textComment']) ? $_POST['textComment']   : '';
$imageComment    = isset($_POST['imageComment'])   ? $_POST['imageComment']     : '';
$tagsComment    = isset($_POST['tagsComment'])   ? $_POST['tagsComment']     : '';
// -------------------------------------------------------------------------------------------
//
//Förhindra SQL injections
//
$Comment_idAuthor  = $mysqli->real_escape_string($Comment_idAuthor);
$idComment  = $mysqli->real_escape_string($idComment);
$titleComment  = $mysqli->real_escape_string($titleComment);
$textComment   = $mysqli->real_escape_string($textComment);
$imageComment   = $mysqli->real_escape_string($imageComment);
$tagsComment   = $mysqli->real_escape_string($tagsComment);

if(!is_numeric($idComment)) {
    die("idComment måste vara ett integer. Försök igen.");
}
if(!is_numeric($Comment_idAuthor)) {
    die("Comment_idAuthor måste vara ett integer. Försök igen.");
}
// -------------------------------------------------------------------------------------------
//
// interception filter
//
if(isset($_SESSION['idAuthor'])&&($Comment_idAuthor!=$_SESSION['idAuthor'])){
	die("Du har inte behörighet till denna sida");
}

// -------------------------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//
$tableComments = DB_PREFIX . 'Comments';

$query = <<<EOD
UPDATE {$tableComments}
SET
   titleComment='{$titleComment}',
   textComment='{$textComment}',
   imageComment='{$imageComment}',
   tagsComment='{$tagsComment}',
   dateComment=NOW() 
WHERE 
  idComment = {$idComment}
;
EOD;


$res = $mysqli->query($query) or die("Could not query database");

$html = <<<EOD
<h2>Editerat</h2>
<p>Query={$query}</p><p>Rows affected: {$mysqli->affected_rows}</p>
<p>
[ <a href='?p=visabloggar'>Visa alla bloggar</a> ]
[ <a href='?p=visainlaggkommentar&amp;idComment={$idComment}'>Visa inlägg med kommentarer igen</a> ]
</p>
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

$page->printHTMLHeader('Editerat');
$page->printPageHeader();
$page->printPageBody($html);
$page->printPageFooter();*/
// -------------------------------------------------------------------------------------------
//
// Redirect to another page
//
$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'home';
header('Location: ' . WS_SITELINK . "?m=blog&p={$redirect}");
exit;
?>
