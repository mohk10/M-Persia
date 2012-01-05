<?php
// -------------------------------------------------------------------------------------------
//
// PShowArticle.php
//
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
// Prepare and perform a SQL query.
//
//$tableAuthors  = DB_PREFIX . 'Authors';
$tableArticles =DB_PREFIX.'Article';
//$tableNotes    = DB_PREFIX . 'Notes';
// Create the query
$query = <<< EOD
CALL pe_SPDisplayArticle({$articleId}, '{$userId}');
EOD;



$html = "";
$res 	= $db->MultiQuery($query); 



// -------------------------------------------------------------------------------------------
//
// Show the results of the query
//
$results = Array();
 
$results[0] = $mysqli->store_result();

        
// Check if there is a database error
!$mysqli->errno 
          or die("<p>Failed retrieving resultsets.</p><p>Query =<br/><pre>{$query}</pre><br/>Error code: {$this->iMysqli->errno} ({$this->iMysqli->error})</p>");
//$res = $mysqli->store_result() or die("Failed to retrive result from query.");
$row = $results[0]->fetch_object();
require_once(TP_SOURCEPATH.'CHTMLPage3.php');
$stylesheet = WS_STYLESHEET2;

$page = new CHTMLPage($stylesheet);

$html.=$page->buildArticleCode($articleId,$row->title,$row->content,$row->title,$row->mydate);
$results[0]->close(); 
//$row->close();
$mysqli = $db->Connect();


$query=<<<EOD
CALL pe_SPListArticles({$articleId}, '{$userId}');
EOD;


$res 	= $db->MultiQuery($query); 

//$res = $mysqli->multi_query($query) or die("Could not query database2");
$results2 = Array();
 
$results2[0] = $mysqli->store_result();

        
// Check if there is a database error
!$mysqli->errno 
          or die("<p>Failed retrieving resultsets.</p><p>Query =<br/><pre>{$query}</pre><br/>Error code: {$this->iMysqli->errno} ({$this->iMysqli->error})</p>");
//$res = $mysqli-


//echo("results2[0]=".$results2[0]);
// Get the list of articles
//$html = "";
while($row = $results2[0]->fetch_object()) {    
	$html.="<a class='mya' href='?p=show-article&article-id=".$row->id."'>".$row->title."</a>";
}
$results2[0]->close(); 

$mysqli->close();



// -------------------------------------------------------------------------------------------
//
// Show the results of NEXT the query
//
/*($mysqli->next_result() && ($res = $mysqli->store_result())) 
  or die("Failed to retrive result from query.");
  
  $firstBox = <<< EOD
  <div class='newPosts'>
  <hr />
 Nya inlägg:
<ul>
EOD;

  
  while($row = $res->fetch_object()) {
  
  	  $firstBox.="<li><a href='?p=visainlaggkommentar&amp;idComment={$row->idComment}'>{$row->titleComment}({$row->nameAuthor})</a></li>";
  
} // endwhile
$firstBox.=<<<EOD
</ul>
</div>
EOD;

$res->close();

// -------------------------------------------------------------------------------------------
//
// Show the results of NEXT the query
//
($mysqli->next_result() && ($res = $mysqli->store_result())) 
  or die("Failed to retrive result from query.");

 $secondBox = <<< EOD
  <div class='newPosts'>
  <hr />
 Författare:
EOD;
  while($row = $res->fetch_object()){ 
  $secondBox .= <<< EOD
<p>
<strong><a href='?p=visanamnblogg&amp;idAuthor={$row->idAuthor}'>{$row->nameAuthor}</a>:  {$row->sphereAuthor}</strong>
</p>
EOD;
  }
  


$secondBox.=<<<EOD
<hr />
</div>
EOD;

$res->close();*/

// -------------------------------------------------------------------------------------------
//
// Close the connection to the database
//
//$mysqli->close();

// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$name="Artikel";




$page->printHTMLHeader($name);
$page->printPageHeader($name);
$page->printStartSection();
$page->printPageBody($html);
$page->printCloseSection();
//$page->printAside();
$page->printPageFooter();
?>
