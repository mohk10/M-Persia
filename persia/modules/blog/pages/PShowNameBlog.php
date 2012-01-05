<?php
// -------------------------------------------------------------------------------------------
//
// PShowAllBlogs.php
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
$idAuthor = isset($_GET['idAuthor']) ? $_GET['idAuthor'] : '';

if(!is_numeric($idAuthor)) {
    die("idAuthor måste vara ett integer. Försök igen.");
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
// Prepare and perform a SQL query.
//
$tableAuthors  = DB_PREFIX . 'Authors';
$tableComments = DB_PREFIX . 'Comments';
$tableNotes    = DB_PREFIX . 'Notes';

$query =<<<EOD
SELECT * ,COUNT(idNote) AS Numbers FROM (({$tableComments} AS C
LEFT OUTER JOIN {$tableNotes} AS N
  ON C.idComment=N.Note_idComment)
JOIN {$tableAuthors} AS A
  ON C.Comment_idAuthor=A.idAuthor)
  WHERE idAuthor={$idAuthor}  
GROUP BY C.idComment
ORDER BY C.dateComment DESC;
EOD;
$query.=<<<EOD
SELECT * FROM
({$tableComments}
JOIN
{$tableAuthors}
  ON Comment_idAuthor=idAuthor)
WHERE idAuthor={$idAuthor}    
ORDER BY dateComment DESC
LIMIT 2;
EOD;
$query.=<<<EOD
SELECT * FROM
{$tableAuthors};
EOD;


$res = $mysqli->multi_query($query) or die("Could not query database");

$html = "";


// -------------------------------------------------------------------------------------------
//
// Show the results of the query
//
$res = $mysqli->store_result() or die("Failed to retrive result from query.");

//require_once(TP_SOURCEPATH.'CHTMLPage.php');

//$stylesheet = WS_STYLESHEET;
require_once($currentDir .'src/CHTMLPage2.php');
//require_once(TP_MODULESBLOG.'stylesheet2.css');
$stylesheet ="modules/blog/stylesheet2.css" ;

$page = new CHTMLPage2($stylesheet);
//$page = new CHTMLPage($stylesheet);


while($row = $res->fetch_object()) {
	
$name=$row->nameBlog;

$html.=$page->buildArticleCode($row->idComment,$row->titleComment,$row->Numbers,$row->textComment,$row->imageComment,$row->tagsComment,$row->nameAuthor,$row->dateComment);

}


$res->close();
// -------------------------------------------------------------------------------------------
//
// Show the results of NEXT the query
//
($mysqli->next_result() && ($res = $mysqli->store_result())) 
  or die("Failed to retrive result from query.");
  
  $firstBox = <<< EOD
  <div class='newPosts'>
  <hr />
 Nya inlägg:
<ul>
EOD;

  
  while($row = $res->fetch_object()) {
  
  	  $firstBox.="<li><a href='?m=blog&amp;p=visainlaggkommentar&amp;idComment={$row->idComment}'>{$row->titleComment}({$row->nameAuthor})</a></li>";
  
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
<strong><a href='?m=blog&amp;p=visanamnblogg&amp;idAuthor={$row->idAuthor}'>{$row->nameAuthor}</a>:  {$row->sphereAuthor}</strong>
</p>
EOD;
  }
  


$secondBox.=<<<EOD
<hr />
</div>
EOD;

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





$page->printHTMLHeader($name);
$page->printPageHeader($name);
$page->printStartSection();
$page->printPageBody($html);
$page->printCloseSection();
$page->printAside($firstBox,$secondBox);
$page->printPageFooter();
?>
