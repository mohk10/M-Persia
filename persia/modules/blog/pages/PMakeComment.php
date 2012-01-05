<?php
// ===========================================================================================
//
// PMakeComment.php
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
$idAuthor = isset($_GET['idAuthor']) ? $_GET['idAuthor'] : '';



if(!is_numeric($idAuthor)) {
    die("idAuthor måste vara ett integer. Försök igen.");
}
// -------------------------------------------------------------------------------------------
//
// interception filter
//
if(isset($_SESSION['idAuthor'])&&($idAuthor!=$_SESSION['idAuthor'])){
	die("Du har inte behörighet till denna sida");
}

$html = <<<EOD
<h2>Nytt inlägg</h2>
EOD;



// -------------------------------------------------------------------------------------------
//
// Form to create the note
//
$html .= <<<EOD
<fieldset>
<legend><strong>Nytt inlägg</strong></legend>
<form action='?m=blog&amp;p=nyttinlaggp' method='POST'>
<input type='hidden' name='Comment_idAuthor' value='{$idAuthor}'>
<input type='hidden' name='redirect' value='visanamnbloggedit&amp;idAuthor={$idAuthor}'>
<table>
<tr>
<td>Titel:</td><td> <input type='text' name='titleComment' /></td>
</tr>
<tr>
<td>Text:</td>
<td><textarea rows='6' cols='80' name='textComment'>
</textarea></td>
</tr>
<tr>
<td>Image:</td><td><input type='text' name='imageComment' /></td>
</tr>
<tr>
<td>Tags:</td><td><input type='text' name='tagsComment' /></td>
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
// Create and print out the resulting page
//
//require_once(TP_SOURCEPATH.'CHTMLPage.php');

//$page = new CHTMLPage();
require_once($currentDir .'src/CHTMLPage2.php');
//require_once(TP_MODULESBLOG.'stylesheet2.css');
$stylesheet ="modules/blog/stylesheet2.css" ;

$page = new CHTMLPage2($stylesheet);
$page->printHTMLHeader('Nytt inlägg');
$page->printPageHeader('Fogglers blogg');
$page->printPageBody($html);
$page->printPageFooter();
?>
