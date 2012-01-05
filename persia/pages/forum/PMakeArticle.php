<?php
// ===========================================================================================
//
// PMakeArticle.php
//
// An implementation of a PHP pagecontroller for a web-site.
//
// -------------------------------------------------------------------------------------------
//
// Get pagecontroller helpers. Useful methods to use in most pagecontrollers
//
require_once(TP_SOURCEPATH . 'CPageController.php');

$pc = new CPageController();


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

// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$articleId    = $pc->GETisSetOrSetDefault('article-id', 0);
$userId        = $_SESSION['idUser'];

// Always check whats coming in...
$pc->IsNumericOrDie($articleId, 0);



$html = <<<EOD
<h2>Ny Artikel</h2>
EOD;



// -------------------------------------------------------------------------------------------
//
// Form to create the note
//
$html .= <<<EOD
<fieldset>
<legend><strong>Ny Artikel</strong></legend>
<form action='?p=make-articlep' method='POST'>
<input type='hidden' name='redirect' value='show-article'>
<table>
<tr>
<td>Titel:</td><td> <input type='text' name='title' /></td>
</tr>
<tr>
<td>Text:</td>
<td><textarea rows='6' cols='80' name='content'>
</textarea></td>
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
require_once(TP_SOURCEPATH.'CHTMLPage3.php');
$stylesheet = WS_STYLESHEET2;

$page = new CHTMLPage($stylesheet);

$page->printHTMLHeader('Ny Artikel');
$page->printPageHeader('Artikel');
$page->printPageBody($html);
$page->printPageFooter();
?>
