<?php
// ===========================================================================================
//
// PEditComment.php
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
// Take care of _GET variables. Store them in a variable (if they are set).
//
$idComment = isset($_GET['idComment']) ? $_GET['idComment'] : '';



if(!is_numeric($idComment)) {
    die("idComment måste vara ett integer. Försök igen.");
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
// Prepare and perform SQL query.
//
$tableComments = DB_PREFIX . 'Comments';

$query = <<<EOD
SELECT 
  *
FROM {$tableComments} 
WHERE 
  idComment = {$idComment}
;
EOD;



$res=$mysqli->query($query) or die("Could not query database");

$row = $res->fetch_object();

// -------------------------------------------------------------------------------------------
//
// interception filter
//
if(isset($_SESSION['idAuthor'])&&($row->Comment_idAuthor!=$_SESSION['idAuthor'])){
	die("Du har inte behörighet till denna sida");
}

$html = <<<EOD
<h2>Editera inlägg</h2>
EOD;



// -------------------------------------------------------------------------------------------
//
// Form to create the note
//
$html .= <<<EOD
<fieldset>
<legend><strong>Editera inlägg</strong></legend>
<form action='?m=blog&amp;p=editinlaggp' method='POST'>
<input type='hidden' name='Comment_idAuthor' value='{$row->Comment_idAuthor}'>
<input type='hidden' name='idComment' value='{$row->idComment}'>
<input type='hidden' name='redirect' value='visanamnbloggedit&amp;idAuthor={$row->Comment_idAuthor}'>
<table>
<tr>
<td>Titel:</td><td> <input type='text' name='titleComment' size='40' value='{$row->titleComment}' /></td>
</tr>
<tr>
<td>Text:</td>
<td><textarea rows='6' cols='80' name='textComment' >{$row->textComment}
</textarea></td>
</tr>
<tr>
<td>Image:</td><td><input type='text' name='imageComment' size='40' value='{$row->imageComment}' /></td>
</tr>
<tr>
<td>Tags:</td><td><input type='text' name='tagsComment' size='40' value='{$row->tagsComment}' /></td>
</tr>
<tr>
<td colspan='2'>
<button name='back' value='undo' type='button' onClick='history.back();'>Tillbaka</button>
<button name='undo' value='undo' type='reset'>Återställ</button>
<button name='save' value='save' type='submit'>Spara</button>
</td>
</tr>
</table>
</form>
</fieldset>
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
//require_once(TP_SOURCEPATH.'CHTMLPage.php');

//$page = new CHTMLPage();
require_once($currentDir .'src/CHTMLPage2.php');
//require_once(TP_MODULESBLOG.'stylesheet2.css');
$stylesheet ="modules/blog/stylesheet2.css" ;

$page = new CHTMLPage2($stylesheet);

$page->printHTMLHeader('Editera inlägg');
$page->printPageHeader('Fogglers blogg');
$page->printPageBody($html);
$page->printPageFooter();
?>
