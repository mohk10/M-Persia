<?php
// -------------------------------------------------------------------------------------------
//
// PShowNameBlogEdit.php
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
// interception filter
//
if(isset($_SESSION['idAuthor'])&&($idAuthor!=$_SESSION['idAuthor'])){
	die("Du har inte behörighet till denna sida");
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
SELECT *  FROM (({$tableComments} AS C
LEFT OUTER JOIN {$tableNotes} AS N
  ON C.idComment=N.Note_idComment)
JOIN {$tableAuthors} AS A
  ON C.Comment_idAuthor=A.idAuthor)
  WHERE idAuthor={$idAuthor}  
ORDER BY C.dateComment DESC;
EOD;




$res = $mysqli->query($query) or die("Could not query database");

$html="";
if(isset($_SESSION['errorMessage'])&&isset($_SESSION['accountAuthor'])){
	$html.="<div class='notification'>
     {$_SESSION['errorMessage']}
</div>";
unset($_SESSION['errorMessage']);
}
$html.= "<br /><br /><a href='?m=blog&amp;p=nyttinlagg&amp;idAuthor={$idAuthor}'>Nytt inlägg</a>";


// -------------------------------------------------------------------------------------------
//
// Show the results of the query
//

//require_once(TP_SOURCEPATH.'CHTMLPage.php');

//$stylesheet = WS_STYLESHEET;

//$page = new CHTMLPage($stylesheet);
require_once($currentDir .'src/CHTMLPage2.php');
//require_once(TP_MODULESBLOG.'stylesheet2.css');
$stylesheet ="modules/blog/stylesheet2.css" ;

$page = new CHTMLPage2($stylesheet);

$latest=0;


while($row = $res->fetch_object()) {
	
if($latest!=$row->idComment){
$html.=$page->buildArticleCodeEdit($row->idComment,$row->titleComment,$row->textComment,$row->imageComment,$row->tagsComment,$row->nameAuthor,$row->dateComment);
if(is_null($row->idNote)){
	;
}
else{
	$html.=$page->buildArticleCodeEdit3($row->idNote,$row->titleNote,$row->textNote,$row->emailNote,$row->dateNote);
}
}

else {
	$html.=$page->buildArticleCodeEdit3($row->idNote,$row->titleNote,$row->textNote,$row->emailNote,$row->dateNote);
	}
	$latest=$row->idComment;
}



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



$name=$_SESSION['nameBlog'];



$page->printHTMLHeader("NameBlogEdit");
$page->printPageHeader($name);
$page->printStartSection('commentandnotes');
$page->printPageBody($html);
$page->printCloseSection();
//$page->printAside($firstBox,$secondBox);
$page->printPageFooter();
?>
