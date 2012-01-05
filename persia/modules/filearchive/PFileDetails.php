<?php
// ===========================================================================================
//
// File: PFileDetails.php
//
// Description: Show (and edit) metadata of a file.
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
$intFilter->UserIsSignedInOrRedirectToSignIn();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$filename    = $pc->GETisSetOrSetDefault('file');
$userId        = $_SESSION['idUser'];

// Always check whats coming in...
//$pc->IsNumericOrDie($articleId, 0);

/*
// Link to images
$imageLink = WS_IMAGES;

// -------------------------------------------------------------------------------------------
//
// Add JavaScript and html head stuff related to JavaScript
//
$js = WS_JAVASCRIPT;
$needjQuery = TRUE;
$htmlHead = <<<EOD
<!-- jQuery Form Plugin -->
<script type='text/javascript' src='{$js}/form/jquery.form.js'></script>  

EOD;

$javaScript = <<<EOD

// ----------------------------------------------------------------------------------------------
//
// Initiate JavaScript when document is loaded.
//
$(document).ready(function() {

    // Preload loader image
    var loaderImg = new Image();
    loaderImg.src = "{$imageLink}/loader.gif";
    loaderImg.align = "baseline";


    // ----------------------------------------------------------------------------------------------
    //
    // Upgrade form to make Ajax submit
    //
    // http://malsup.com/jquery/form/
    //
    $('#form1').ajaxForm({
        // $.ajax options can be used here too, for example: 
        //timeout: 1000, 

        // do stuff before submitting form
        beforeSubmit: function(data, status) {
                        $('#status1').html(loaderImg);
                },
                
        // define a callback function
        success: function(data, status) {
                        $('#status1').html(data);
                }    
        });
    });

EOD;
*/


// -------------------------------------------------------------------------------------------
//
// Get file details/metadata from database
//
$db         = new CDatabaseController();
$SPFileDetails=DBSP_SPFileDetails;
$cCSizeFileName=CSizeFileName;
$cCSizeMimetype=CSizeMimetype;
$mysqli = $db->Connect();

// Create the query
$query     = <<< EOD
CALL {$SPFileDetails}('{$userId}', '{$filename}', @success);
SELECT @success AS success;
EOD;

// Perform the query
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);
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

$row = $results[0]->fetch_object();
$fileid         = $row->fileid;
$name             = $row->name;
$article	=$row->article;
$uniquename = $row->uniquename;
$path             = $row->path;
$size             = $row->size;
$mimetype     = $row->mimetype;
$created         = $row->created;
$modified     = $row->modified;
$deleted         = $row->deleted;
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
global $gModule;

/*$action             = "?m={$gModule}&amp;p=file-detailsp";
$redirect         = "?m={$gModule}&amp;p=file-details&amp;file={$filename}";
$redirectFail = "?m={$gModule}&amp;p=file-details&amp;file={$filename}";

// Get and format messages from session if they are set
$helpers = new CHTMLHelpers();
$messages = $helpers->GetHTMLForSessionMessages(
    Array('success'), 
    Array('failed'));

$hideDeleteButton     = empty($deleted) ? '' : 'hide' ;
$hideRestoreButton     = empty($deleted) ? 'hide' : '' ;*/

$editDetails     = "?m={$gModule}&amp;p=file-details-edit&amp;file={$uniquename}";
$download         = "?m={$gModule}&amp;p=download&amp;file={$uniquename}";

$caption = sprintf($pc->lang['FILE_DETAILS_CAPTION'], $name);

$htmlMain = <<<EOD
<div class='section'>
<h1>{$pc->lang['FILE_DETAILS_HEADER']}</h1>
<p>{$pc->lang['FILE_DETAILS_DESCRIPTION']}</p>

<div class='nav-standard'>
<ul>
<li><a href='{$editDetails}'>{$pc->lang['FILE_DETAILS_EDIT']}</a> 
<li><a href='{$download}'>{$pc->lang['FILE_DOWNLOAD_PAGE']}</a>
</ul>
<div class='clear'>&nbsp;</div>
</div>
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
<td>{$pc->lang['FILE_DETAILS_UNIQUENAME']}</td>
<td title='{$path}'>{$uniquename}</td>
</tr>
<tr>
<td>{$pc->lang['FILE_DETAILS_ARTICLE']}</td>
<td>{$article}</td>
</tr>
<tr>
<td>{$pc->lang['FILE_DETAILS_PATH']}</td>
<td>{$path}</td>
</tr>
<tr>
<td>{$pc->lang['FILE_DETAILS_SIZE']}</td>
<td>{$size}</td>
</tr>
<tr>
<td>{$pc->lang['FILE_DETAILS_MIMETYPE']}</td>
<td>{$mimetype}</td>
</tr>
<tr>
<td>{$pc->lang['FILE_DETAILS_CREATED']}</td>
<td>{$created}</td>
</tr>
<tr>
<td>{$pc->lang['FILE_DETAILS_MODIFIED']}</td>
<td>{$modified}</td>
</tr>
<tr>
<td>{$pc->lang['FILE_DETAILS_DELETED']}</td>
<td>{$deleted}</td>
</tr>
</tbody>
<tfoot></tfoot>
</table>
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

$page->PrintPage($pc->lang['FILE_DETAILS_TITLE'], $htmlLeft, $htmlMain, $htmlRight);
exit;
?>
