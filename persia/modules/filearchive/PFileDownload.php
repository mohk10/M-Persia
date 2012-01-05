<?php
// ===========================================================================================
//
// File: PFileDownload.php
//
// Description: Show details on a file and enable public download.
//
// Author: Mikael Roos, mos@bth.se
//


// -------------------------------------------------------------------------------------------
//
// Get pagecontroller helpers. Useful methods to use in most pagecontrollers
//
$pc = new CPageController();
$pc->LoadLanguage(__FILE__);


// -------------------------------------------------------------------------------------------
//
// Interception Filter, controlling access, authorithy and other checks.
//
$intFilter = new CInterceptionFilter();
$intFilter->FrontControllerIsVisitedOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$file    = $pc->GETisSetOrSetDefault('file');


// -------------------------------------------------------------------------------------------
//
// Get file details/metadata from database
//
$db         = new CDatabaseController();
$SPFileDetails=DBSP_SPFileDetails;
$cCSizeFileName=CSizeFileName;
$cCSizeMimetype=CSizeMimetype;
$mysqli = $db->Connect();
// Sätt idUser=1 och 1 är satt som admin. Så att alla kan nå downloadsidan.
// Create the query
$file     = $mysqli->real_escape_string($file);
$query     = <<< EOD
CALL {$SPFileDetails}('1', '{$file}', @success);
SELECT @success AS success;
EOD;

// Perform the query
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

//echo("results[2]=".empty($results[2]));
//print_r($results[0]);
if(empty($results[2])){
	$row = $results[1]->fetch_object();
}
else{
	$row = $results[2]->fetch_object();
}
//echo("row->success".$row->success);

if($row->success==1) {
    $pc->RedirectToModuleAndPage('', 'p403', '',$pc->lang['FILE_NO_PERMISSION']);
}
else if($row->success==2){
        $pc->RedirectToModuleAndPage('', 'p403', '',$pc->lang['FILE_DOES_NOT_EXISTS']);
	
}
else{
}
$row1 = $results[0]->fetch_object();
$fileid         = $row1->fileid;
$owner             = $row1->owner;
$name             = $row1->name;
$uniquename = $row1->uniquename;
$path             = $row1->path;
$size             = $row1->size;
$mimetype     = $row1->mimetype;
$created         = $row1->created;
$modified     = $row1->modified;
$deleted         = $row1->deleted;

if(empty($results[2])){
	$results[1]->close();
}
else{
	$results[2]->close();

}
$results[0]->close();
$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Create the HTML
//
$downloadNow = "?m={$gModule}&amp;p=download-now&amp;file={$uniquename}";
//$downloadNow2=WS_SITELINK.$downloadNow;
$header     = sprintf($pc->lang['FILE_DOWNLOAD_HEADER'], $name);
$caption = sprintf($pc->lang['FILE_DOWNLOAD_CAPTION'], $created, $owner);

// Start download automatically
$secondsBeforeDownloadStart = 10;
$timeMessage = sprintf($pc->lang['FILE_DOWNLOAD_STARTS_SOON'], $secondsBeforeDownloadStart);
if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE){
$htmlHead="";
$js=<<<EOD
function myReload(){
//alert(window.location.href.slice(-2));
 if(window.location.href.slice(-2) != "&r") {
 	window.location.href='{$downloadNow}';
      setInterval('myReloadOnce()',9500);
    }
}
function myReloadOnce(){
     window.location = window.location.href + "&amp;r";
}
EOD;
$enr=TRUE;
}
//elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== FALSE)
//	echo 'Mozilla Firefox'; 
//elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== FALSE)
//	echo 'Google Chrome'; 
else{
//echo 'Something else';
$htmlHead = <<<EOD
<meta http-equiv='refresh' content="{$secondsBeforeDownloadStart}; url={$downloadNow}" >
EOD;
$js="";
$enr=FALSE;
}
$htmlMain = <<<EOD
<div class='section'>
<h1>{$header}</h1>
<p>{$pc->lang['FILE_DOWNLOAD_DESCRIPTION']}</p>
</div> <!-- section -->

<div class='section'>
<table class='standard filedetails-show'>
<caption>{$caption}</caption>
<colgroup><col class='header'><col></colgroup>
<thead></thead>
<tbody>
<tr>
<td>{$pc->lang['FILE_DETAILS_FILENAME']}</td>
<td>{$name}</td>
</tr>
<tr>
<td>{$pc->lang['FILE_DETAILS_SIZE']}</td>
<td>{$size}</td>
</tr>
<tr>
<td>{$pc->lang['FILE_DETAILS_MIMETYPE']}</td>
<td>{$mimetype}</td>
</tr>
</tbody>
<tfoot></tfoot>
</table>
</div> <!-- section -->

<div class='section'>
<p>{$timeMessage}</p>
<div class='nav-standard nav-button'>
<ul>
<li><a href='{$downloadNow}'>{$pc->lang['FILE_DOWNLOAD_NOW']}</a>
</ul>
<div class='clear'>&nbsp;</div>
</div>
</div> <!-- section -->


EOD;

$htmlLeft     = "";
$htmlRight    = <<<EOD
<h3 class='columnMenu'></h3>
<p>
Later...
</p>

EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->PrintPage(sprintf($pc->lang['FILE_DOWNLOAD_TITLE'], $name), $htmlLeft, $htmlMain, $htmlRight, $htmlHead,$js,FALSE,$enr);
exit;
?>
