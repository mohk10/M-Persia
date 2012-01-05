<?php
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
// Create a new database object, connect to the database.
//
require_once(TP_SQLPATH."config.php");

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

if(mysqli_connect_error()) {
 echo "Connect failed: " . mysqli_connect_error() . "<br/>";
 exit();
}

$mysqli->set_charset("utf8");

// -------------------------------------------------------------------------------------------
//
// Prepare and perform SQL query.
//
$tableComments    = DB_PREFIX . 'Comments';

$query = <<<EOD
SELECT
  *
FROM {$tableComments}
ORDER BY dateComment DESC
LIMIT 5;
EOD;

$res = $mysqli->query($query) or die("Could not query database");
// -------------------------------------------------------------------------------------------
//
// Create comments.
//
$ws_sitelink = WS_SITELINK;
$ws_title = "Fogglers blogg";
$sequence="";
$comments="";

date_default_timezone_set("Europe/Stockholm");

while($row = $res->fetch_object()) {


 $row->dateComment=date("c", strtotime($row->dateComment));	
 $sequence .= "<rdf:li resource='{$ws_sitelink}?p=visainlaggkommentar&amp;idComment={$row->idComment}' />";
 $comments .=<<<EOD
 <item rdf:about='{$ws_sitelink}?p=visainlaggkommentar&amp;idComment={$row->idComment}'>
  <title>{$row->titleComment}</title>
  <link>{$ws_sitelink}?p=visainlaggkommentar&amp;idComment={$row->idComment}</link>
  <description>{$row->textComment}></description>
  <dc:date>{$row->dateComment}</dc:date>
 </item>
EOD;
}

// -------------------------------------------------------------------------------------------
//
// Create xml code
//

$xml = <<<EOD
<?xml version="1.0" encoding="UTF-8" ?>
<rdf:RDF 
 xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
 xmlns="http://purl.org/rss/1.0/"
 xmlns:dc="http://purl.org/dc/elements/1.1/"
>
 <channel rdf:about="{$ws_sitelink}?p=rss10">
  <title>{$ws_title}</title>
  <link>{$ws_sitelink}</link>
  <description>Senaste 5 inläggen från {$ws_title}</description>
  <items>
   <rdf:Seq>
    {$sequence}
   </rdf:Seq>
  </items>
 </channel>
  
 {$comments}

</rdf:RDF>
EOD;

$mysqli->close();

header("Content-Type: application/rss+xml; charset=UTF-8");
echo $xml;
?> 
