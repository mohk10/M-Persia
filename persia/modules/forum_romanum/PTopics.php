
<?php
// ===========================================================================================
//
// PTopics.php
//
// Show the title of all/some topics
//
// Author: Mikael Roos, mos@bth.se
//


// -------------------------------------------------------------------------------------------
//
// Get pagecontroller helpers. Useful methods to use in most pagecontrollers
//
$pc = new CPageController();
//$pc->LoadLanguage(__FILE__);


// -------------------------------------------------------------------------------------------
//
// Interception Filter, controlling access, authorithy and other checks.
//
$intFilter = new CInterceptionFilter();

$intFilter->FrontControllerIsVisitedOrDie();
//$intFilter->UserIsSignedInOrRecirectToSignIn();
//$intFilter->UserIsMemberOfGroupAdminOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
//$articleId    = $pc->GETisSetOrSetDefault('article-id', 0);
//$userId        = $_SESSION['idUser'];

// Always check whats coming in...
//$pc->IsNumericOrDie($articleId, 0);


// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database, get the query and execute it.
// Relates to files in directory TP_SQLPATH.
//
$db     = new CDatabaseController();
$mysqli = $db->Connect();

// Get the SP names
$spSPGetTopicList = DBSP_SPGetTopicList;

$query = <<< EOD
CALL {$spSPGetTopicList}();
EOD;

// Perform the query
$results = Array();
$res = $db->MultiQuery($query); 
$db->RetrieveAndStoreResultsFromMultiQuery($results);

// Get the list of topics
$list = <<<EOD
<table class='mywidth1'>
<tr>
<th class='mywidth2'>
Topic
</th>
<th>
Posts
</th>
<th colspan='2'>
Most recent
</th>
</tr>
EOD;
while($row = $results[0]->fetch_object()) {    
    $list .= <<<EOD
<tr>
<td>
<a href='?m=rom&amp;p=topic&amp;id={$row->topicid}' class='mya' >{$row->title}</a>
</td>
<td style='text-align: center;'>
{$row->postcounter}
</td>
<td>
{$row->latestby}
</td>
<td>
{$row->latest}
</td>
</tr>
EOD;
}
$list .= "</table>";

$results[0]->close(); 
$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Page specific code
//
$htmlMain = <<<EOD
<h1>Latest discussions</h1>
{$list} 
EOD;

$htmlLeft     = "";
$htmlRight    = <<<EOD
<h3 class='columnMenu'>Categories</h3>
<p>
Later...
</p>
<h3 class='columnMenu'>Tags</h3>
<p>
Later...
</p>

<!--
<h3 class='columnMenu'>Hot Tags</h3>
<p>
Later...<br>
(Complete Tag Cloud)
</p>
<h3 class='columnMenu'>Recent Activity</h3>
<p>
Later...
</p>
-->
EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
require_once(TP_SOURCEPATH.'CHTMLPage3.php');
$stylesheet = WS_STYLESHEET2;

$page = new CHTMLPage3($stylesheet);
//$page = new CHTMLPage();

//$page->PrintPage("Topics", $htmlLeft, $htmlMain, $htmlRight);
//exit;
$name="Forum Romanum Mission Statement";




$page->printHTMLHeader($name);
$page->printPageHeader2($name);
$page->printStartSection();
$page->printPageBody($htmlMain);
$page->printCloseSection();
$page->printAside($htmlRight);
$page->printPageFooter();
?>

