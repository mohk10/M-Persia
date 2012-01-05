<?php
// ===========================================================================================
//
// PEditArticleProcess.php
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
$intFilter->UserIsSignedInOrRedirectToSignin();
//$intFilter->userIsMemberOfGroupAdminOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$title        = $pc->POSTisSetOrSetDefault('title', 'No title');
$content    = $pc->POSTisSetOrSetDefault('content', 'No content');
$articleId    = $pc->POSTisSetOrSetDefault('article_id', 0);
$success    = $pc->POSTisSetOrSetDefault('redirect_on_success', '');
$failure    = $pc->POSTisSetOrSetDefault('redirect_on_failure', '');
$userId        = $_SESSION['idUser'];

// Always check whats coming in...
$pc->IsNumericOrDie($articleId, 0);

// Clean up HTML-tags
$tagsAllowed = '<h1><h2><h3><h4><h5><h6><p><a><br><i><em><li><ol><ul>';
$title         = strip_tags($title, $tagsAllowed);
$content     = strip_tags($content, $tagsAllowed);


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
// Prepare and perform a SQL query.
//
//$tableArticles = 'Articles';
//$tArticle =DB_PREFIX.'Articles';


// Create the query
$query = <<< EOD
CALL pe_SPUpdateArticle({$articleId},'{$userId}', '{$title}', '{$content}');
EOD;

//echo($query);

// Perform the query
$res = $db->MultiQuery($query);


$html = <<<EOD
<h2>Editerat</h2>
<p>Query={$query}</p><p>Rows affected: {$mysqli->affected_rows}</p>
<p>
[ <a href='?p=show-article'>Visa artikel</a> ]
[ <a href='?p=show-article&amp;article-id={$articleId}'>Visa artikel igen</a> ]
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
header('Location: ' . WS_SITELINK . "?p={$redirect}");
exit;
?>
