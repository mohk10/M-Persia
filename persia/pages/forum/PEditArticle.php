<?php
// ===========================================================================================
//
// PEditArticle.php
//
// An implementation of a PHP pagecontroller for a web-site.
//
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


// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database, get the query and execute it.
// Relates to files in directory TP_SQLPATH.
//
$db 	= new CDatabaseController();
$mysqli = $db->Connect();
//$query 	= $db->LoadSQL('SQLLoginUser.php');

// -------------------------------------------------------------------------------------------
//
// Prepare and perform SQL query.
//
// Create the query
$query = <<< EOD
CALL pe_SPDisplayArticle({$articleId}, '{$userId}');
EOD;



$html = "";
$res 	= $db->MultiQuery($query); 

$results = Array();
 
$results[0] = $mysqli->store_result();

        
// Check if there is a database error
!$mysqli->errno 
          or die("<p>Failed retrieving resultsets.</p><p>Query =<br/><pre>{$query}</pre><br/>Error code: {$this->iMysqli->errno} ({$this->iMysqli->error})</p>");
//$res = $mysqli->store_result() or die("Failed to retrive result from query.");
$row = $results[0]->fetch_object();
//if(is_null($row)){
	


$html = <<<EOD
<h2>Editera artikel</h2>
EOD;



// -------------------------------------------------------------------------------------------
//
// Form to create the note
//
$html .= <<<EOD
<fieldset>
<legend><strong>Editera artikel</strong></legend>
<form action='?p=edit-articlep' method='POST'>
<input type='hidden' name='article_id' value={$articleId}>
<input type='hidden' name='redirect' value='show-article&amp;article-id={$articleId}'>
<table>
<tr>
<td>Titel:</td><td> <input type='text' name='title' size='40' value='{$row->title}' /></td>
</tr>
<tr>
<td>Text:</td>
<td><textarea rows='6' cols='80' name='content' >{$row->content}
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
// Close the connection to the database
//
$mysqli->close();




// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
require_once(TP_SOURCEPATH.'CHTMLPage3.php');
$stylesheet = WS_STYLESHEET2;

$page = new CHTMLPage($stylesheet);

$page->printHTMLHeader('Editera artikel');
$page->printPageHeader('Artikel');
$page->printPageBody($html);
$page->printPageFooter();
?>
