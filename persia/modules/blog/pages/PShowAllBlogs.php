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
// Page specific code
//
// -------------------------------------------------------------------------------------------
//
//  Allow only access to pagecontrollers through frontcontroller
//
//if(!isset($indexIsVisited)) die('No direct access to pagecontroller is allowed.');
// -------------------------------------------------------------------------------------------
//
//  Statistik datum senaste 10 dagarna, senaste månaden, senaste året
//
$today=new DateTime('now',new DateTimeZone('Europe/Stockholm'));
$today->modify('- 10 day');
$latestTenDays=$today;
$latestTenDaysString=$latestTenDays->format('Y-m-d H:i:s');
$today->modify('+ 10 day');
$today->modify('- 1 month');
$latestMonth=$today;
$latestMonthString=$latestMonth->format('Y-m-d H:i:s');
$today->modify('+ 1 month');
$today->modify('- 1 year');
$latestYear=$today;
$latestYearString=$latestYear->format('Y-m-d H:i:s');
//$html="latestTenDays={$latestTenDaysString} latestMonthString={$latestMonthString} latestYearString={$latestYearString}";
//echo($html);
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
//define('DB_PREFIX',   'rpm07_');    // Prefix to use infront of tablename and views

$tableAuthors  = DB_PREFIX . 'Authors';
$tableComments = DB_PREFIX . 'Comments';
$tableNotes    = DB_PREFIX . 'Notes';

$query =<<<EOD
SELECT * ,COUNT(idNote) AS Numbers FROM ({$tableComments} AS C
LEFT OUTER JOIN {$tableNotes} AS N
  ON C.idComment=N.Note_idComment)
JOIN {$tableAuthors} AS A
  ON C.Comment_idAuthor=A.idAuthor
GROUP BY C.idComment
ORDER BY C.dateComment DESC;
EOD;
$query.=<<<EOD
SELECT * FROM
{$tableComments}
JOIN
{$tableAuthors}
  ON Comment_idAuthor=idAuthor
ORDER BY dateComment DESC
LIMIT 10;
EOD;
$query.=<<<EOD
SELECT * FROM
{$tableAuthors};
EOD;
// -------------------------------------------------------------------------------------------
//
// Statistik:senaste tio dagarna,senaste månaden,senaste året
//
$query.=<<<EOD
SELECT COUNT(*) AS ANTAL1 FROM 
{$tableComments}
WHERE
(UNIX_TIMESTAMP(dateComment)-UNIX_TIMESTAMP('{$latestTenDaysString}'))>0
;
EOD;
$query.=<<<EOD
SELECT COUNT(*) AS ANTAL2 FROM 
{$tableComments}
WHERE
(UNIX_TIMESTAMP(dateComment)-UNIX_TIMESTAMP('{$latestMonthString}'))>0
;
EOD;
$query.=<<<EOD
SELECT COUNT(*) AS ANTAL3 FROM 
{$tableComments}
WHERE
(UNIX_TIMESTAMP(dateComment)-UNIX_TIMESTAMP('{$latestYearString}'))>0
;
EOD;
// -------------------------------------------------------------------------------------------
//
// Hämta alla taggar från inläggen.
//
$query.=<<<EOD
SELECT tagsComment FROM 
{$tableComments}
;
EOD;
//echo($query);
$res1 = $mysqli->multi_query($query) or die("Could not query database");

$html = "";
//echo($query);

// -------------------------------------------------------------------------------------------
//
// Show the results of the query
//
$res = $mysqli->store_result() or die("Failed to retrive result from query.");
//echo("1");
require_once($currentDir .'/src/CHTMLPage2.php');
//require_once(TP_MODULESBLOG.'stylesheet2.css');
$stylesheet ="modules/blog/stylesheet2.css" ;

$page = new CHTMLPage2($stylesheet);

//$html.=$page->buildArticleCode(0,"Fogglers nya blogg",0,"Detta är ett enkelt exempel...","pennor.jpg","pennor","Foggler","2011-03-16 16:13:00");

while($row = $res->fetch_object()) {
	

$html.=$page->buildArticleCode($row->idComment,$row->titleComment,$row->Numbers,$row->textComment,$row->imageComment,$row->tagsComment,$row->nameAuthor,$row->dateComment);

}


$res->close();
// -------------------------------------------------------------------------------------------
//
// Show the results of NEXT the query
//
($mysqli->next_result() && ($res = $mysqli->store_result())) 
  or die("Failed to retrive result from query.");
// echo("2"); 
  $firstBox = <<< EOD
  <hr />
  <div class=newPosts>
 Nya inlägg:
<ul>
EOD;

  
  while($row = $res->fetch_object()) {
  
  	  $firstBox.="<li><a href='?m=blog&amp;p=visainlaggkommentar&amp;idComment={$row->idComment}'>{$row->titleComment}({$row->nameAuthor})</a></li>";
  
} // endwhile
$firstBox.=<<<EOD
</ul>
</div>
<hr>
EOD;

$res->close();

// -------------------------------------------------------------------------------------------
//
// Show the results of NEXT the query
//
($mysqli->next_result() && ($res = $mysqli->store_result())) 
  or die("Failed to retrive result from query.");
//echo("3");
 $secondBox = <<< EOD
  <div class=newPosts>
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
</div>
<hr>
EOD;

$res->close();
// -------------------------------------------------------------------------------------------
//
// Show the results of NEXT the query. Statistic
//

($mysqli->next_result() && ($res = $mysqli->store_result())) 
  or die("Failed to retrive result from query.");
//echo("4");
  $row = $res->fetch_object();
  
  $thirdBox="<div class='newStatistic'><h3>Antalet inlägg:</h3><p>Senaste tio dagarna: <strong>{$row->ANTAL1}</strong></p>";
  
  $res->close();
// -------------------------------------------------------------------------------------------
//
// Show the results of NEXT the query. Statistic
//
($mysqli->next_result() && ($res = $mysqli->store_result())) 
  or die("Failed to retrive result from query.");
//echo("5");
  $row = $res->fetch_object();
  
  $thirdBox.="<p>Senaste månaden: <strong>{$row->ANTAL2}</strong></p>";
  
  $res->close();
// -------------------------------------------------------------------------------------------
//
// Show the results of NEXT the query. Statistic
//
($mysqli->next_result() && ($res = $mysqli->store_result())) 
  or die("Failed to retrive result from query.");

  $row = $res->fetch_object();
  
  $thirdBox.="<p>Senaste året: <strong>{$row->ANTAL3}</strong></p></div><hr />";
  
  $res->close();  
// -------------------------------------------------------------------------------------------
//
// Show the results of NEXT the query. Tags manipulation.
//
($mysqli->next_result() && ($res = $mysqli->store_result())) 
  or die("Failed to retrive result from query.");
 
  $row = $res->fetch_object();
  $myTagsArray1=Array();
  $myTagsArray1=explode(",",$row->tagsComment);
  
  while($row = $res->fetch_object()){
         
  	  $myTagsArray2=explode(",",$row->tagsComment);
  	  
  	  foreach($myTagsArray2 as $value){
  	  	  $myTagsArray1[]=$value;
  	  }		  
  }
  $res->close();    
// -------------------------------------------------------------------------------------------
//
// Close the connection to the database
//
$mysqli->close();

// -------------------------------------------------------------------------------------------
//
// Tags manipulation.
//


$tagsArrayFrequency=array_count_values($myTagsArray1);
ksort($tagsArrayFrequency);
//print_r($tagsArrayFrequency);

 $fourthBox = <<< EOD
  <div class='newTags'>
 Taggar:
EOD;

foreach($tagsArrayFrequency as $key=>$value){
	if($key==""){
	}
	else{
 $fourthBox .= <<< EOD
<p>
<strong><a href='?m=blog&amp;p=visatagg&amp;tag={$key}'>{$key}</a>:   {$value}</strong>
</p>
EOD;
	}
  }

$fourthBox.=<<<EOD
</div>
<hr>
EOD;


$page->printHTMLHeader('Fogglers blogg');
$page->printPageHeader('Fogglers blogg');
$page->printStartSection();
$page->printPageBody($html);
$page->printCloseSection();
$page->printAside($firstBox,$secondBox,$thirdBox,$fourthBox);
$page->printPageFooter();
?>
