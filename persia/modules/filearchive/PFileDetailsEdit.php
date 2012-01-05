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

$action             = "?m={$gModule}&amp;p=file-details-editp&amp;postid={$article}";
$redirect         = "?m={$gModule}&amp;p=file-details-edit&amp;file={$filename}";
$redirectFail = "?m={$gModule}&amp;p=file-details-edit&amp;file={$filename}";

// Get and format messages from session if they are set
$helpers = new CHTMLHelpers();
$messages = $helpers->GetHTMLForSessionMessages(
    Array('success'), 
    Array('failed'));

$hideDeleteButton     = empty($deleted) ? '' : 'hide' ;
$hideRestoreButton     = empty($deleted) ? 'hide' : '' ;

$htmlMain = <<<EOD
<div class='section'>
<h1>{$pc->lang['FILE_DETAILS_HEADER']}</h1>
<p>{$pc->lang['FILE_DETAILS_DESCRIPTION']}</p>

<form id='form1' action="{$action}" method="post">
<input type='hidden' name='fileid'                 value='{$fileid}'>
<input type='hidden' name='redirect'             value='{$redirect}'>
<input type='hidden' name='redirect-fail' value='{$redirectFail}'>

<fieldset class='standard filedetails'>
<legend>{$pc->lang['FILE_DETAILS_LEGEND']}</legend>
<div class='form-wrapper'>

<label for='name'>{$pc->lang['FILE_DETAILS_FILENAME']}</label>
<input name='name' type='text' value='{$name}' maxlength='{$cCSizeFileName}' autofocus>

<label for='uniquename'>{$pc->lang['FILE_DETAILS_UNIQUENAME']}</label>
<input name='uniquename' type='text' value='{$uniquename}' disabled>

<label for='uniquename'>{$pc->lang['FILE_DETAILS_ARTICLE']}</label>
<input name='article' type='text' value='{$article}' disabled>

<label for='path'>{$pc->lang['FILE_DETAILS_PATH']}</label>
<input name='path' type='text' value='{$path}' disabled>

<label for='size'>{$pc->lang['FILE_DETAILS_SIZE']}</label>
<input name='size' type='text' value='{$size}' disabled>

<label for='mimetype'>{$pc->lang['FILE_DETAILS_MIMETYPE']}</label>
<input name='mimetype' type='text' value='{$mimetype}' maxlength='{$cCSizeMimetype}'>

<label for='created'>{$pc->lang['FILE_DETAILS_CREATED']}</label>
<input name='created' type='datetime' value='{$created}' disabled>

<label for='modified'>{$pc->lang['FILE_DETAILS_MODIFIED']}</label>
<input name='modified' type='datetime' value='{$modified}' disabled placeholder='{$pc->lang['FILE_TIME_FOR_MODIFIED']}'>

<label for='deleted'>{$pc->lang['FILE_DETAILS_DELETED']}</label>
<input name='deleted' type='datetime' value='{$deleted}' disabled placeholder='{$pc->lang['FILE_TIME_FOR_DELETED']}'>

<div class='buttonbar'>
<button type='submit' class='save' name='do-submit' value='save-file-details'>{$pc->lang['FILE_DETAILS_SAVE']}</button>
<button type='submit' class='delete {$hideDeleteButton}' name='do-submit' value='delete-file'>{$pc->lang['DELETE_FILE']}</button>
<button type='submit' class='restore {$hideRestoreButton}' name='do-submit' value='restore-file'>{$pc->lang['RESTORE_FILE']}</button>
</div> <!-- buttonbar -->

<div class='form-status'>{$messages['success']}{$messages['failed']}</div> 

</div> <!-- wrapper -->
</fieldset>
</form>
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

//$page->PrintPage($pc->lang['FILE_DETAILS_TITLE'], $htmlLeft, $htmlMain, $htmlRight, $htmlHead, $javaScript, $needjQuery);
$page->PrintPage($pc->lang['FILE_DETAILS_TITLE'], $htmlLeft, $htmlMain, $htmlRight);
exit;

?>
