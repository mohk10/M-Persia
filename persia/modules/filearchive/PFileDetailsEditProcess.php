<?php
// ===========================================================================================
//
// File: PFileDetailsProcess.php
//
// Description: Save details/metadata about a file.
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
$submitAction    = $pc->POSTisSetOrSetDefault('do-submit');
$redirect            = $pc->POSTisSetOrSetDefault('redirect');
$redirectFail    = $pc->POSTisSetOrSetDefault('redirect-fail');
$postId    = $pc->GETisSetOrSetDefault('postid',0);
//$postId=5;
//echo("postId=".$postId);
$userId        = $_SESSION['idUser'];


// -------------------------------------------------------------------------------------------
//
// Depending on the submit-action, do whats to be done. If, else if, else, replaces switch.
// 


// -------------------------------------------------------------------------------------------
//
// Do some insane checking to avoid misusage, errormessage if not correct.
// 
if(false) {

}


// -------------------------------------------------------------------------------------------
//
// Save details/metadata on a file.
// 
else if($submitAction == 'save-file-details') {

    $db = new CDatabaseController();
    $SPFileDetailsUpdate=DBSP_SPFileDetailsUpdate;
    $cCSizeFileName=CSizeFileName;
    $cCSizeMimetype=CSizeMimetype;
    // Get the input
    $fileid        = $pc->POSTisSetOrSetDefault('fileid');
    $name         = $pc->POSTisSetOrSetDefault('name');
    $mimetype = $pc->POSTisSetOrSetDefault('mimetype');
    //$fileid=1;
    // Check boundaries for whats coming in
    // is name within size?
    if(!(is_numeric($fileid) && $fileid > 0)) {
        $pc->SetSessionMessage('failed', $pc->lang['FILEID_INVALID']);
        $pc->RedirectTo($redirectFail);
    }

    // is name within size?
    if(mb_strlen($name) > $cCSizeFileName) {
        $pc->SetSessionMessage('failed', sprintf($pc->lang['FILENAME_TO_LONG'], $cCSizeFileName));
        $pc->RedirectTo($redirectFail);
    }

    // is mimetype within size?
    if(mb_strlen($mimetype) > $cCSizeMimetype) {
        $pc->SetSessionMessage('failed', sprintf($pc->lang['MIMETYPE_TO_LONG'], $cCSizeMimetype));
        $pc->RedirectTo($redirectFail);
    }
    if(!(is_numeric($postId))||($postId<0)){
    	    $pc->SetSessionMessage('failed', sprintf($pc->lang['FILE_NO_PERMISSION'], $cCSizeMimetype));
    	    $pc->RedirectTo($redirectFail);
    }
    if($postId){

    $udfFCheckUserIsOwnerOrAdmin2=DBUDF_FCheckUserIsOwnerOrAdmin2;

    $mysqli = $db->Connect();

$query=<<< EOD
SELECT {$udfFCheckUserIsOwnerOrAdmin2}('{$postId}', '{$userId}') AS isAllowed ;
EOD;

// Perform the query
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);
$row = $results[0]->fetch_object();
if(!$row->isAllowed){
	   $pc->SetSessionMessage('failed', $pc->lang['FILE_NO_PERMISSION']);
        $pc->RedirectTo($redirectFail);
}
    }    
    // Save metadata of the file in the database
    $mysqli = $db->Connect();

    // Create the query
    $query     = <<< EOD
CALL {$SPFileDetailsUpdate}({$fileid}, '{$userId}', '{$name}', '{$mimetype}', @success);
SELECT @success AS success;
EOD;

    // Perform the query and manage results
    $results = $db->DoMultiQueryRetrieveAndStoreResultset($query);
    $row = $results[1]->fetch_object();
       // echo("row->success=".$row->success);

if($row->success==1) {
    $pc->SetSessionMessage('failed',$pc->lang['FILE_NO_PERMISSION']);
    $pc->RedirectTo($redirectFail);
}
else if($row->success==2){
        $pc->SetSessionMessage('failed',$pc->lang['FILE_DOES_NOT_EXISTS']);
        $pc->RedirectTo($redirectFail);
	
}
else{
}
    $results[1]->close();
    $mysqli->close();
    
    $pc->SetSessionMessage('success', $pc->lang['FILE_DETAILS_UPDATED']);
    $pc->RedirectTo($redirect);
}


// -------------------------------------------------------------------------------------------
//
// Set a file to be deleted/not deleted.
// 
else if($submitAction == 'delete-file' || $submitAction == 'restore-file') {

    // Get the input
    $fileid        = $pc->POSTisSetOrSetDefault('fileid');
    $deleteOrRestore = ($submitAction == 'delete-file') ? 1 : (($submitAction == 'restore-file') ? 2 : 0);
    //$fileid=4;
    // Save metadata of the file in the database
    $db = new CDatabaseController();
    $SPFileDetailsDeleted=DBSP_SPFileDetailsDeleted;
    $udfFCheckUserIsOwnerOrAdmin2=DBUDF_FCheckUserIsOwnerOrAdmin2;

  if(!(is_numeric($postId))||($postId<0)){
    	    $pc->SetSessionMessage('failed', sprintf($pc->lang['FILE_NO_PERMISSION'], $cCSizeMimetype));
    	    $pc->RedirectTo($redirectFail);
    }
    if($postId){

    $udfFCheckUserIsOwnerOrAdmin2=DBUDF_FCheckUserIsOwnerOrAdmin2;

    $mysqli = $db->Connect();

$query=<<< EOD
SELECT {$udfFCheckUserIsOwnerOrAdmin2}('{$postId}', '{$userId}') AS isAllowed ;
EOD;

// Perform the query
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);
$row = $results[0]->fetch_object();
if(!$row->isAllowed){
	   $pc->SetSessionMessage('failed', $pc->lang['FILE_NO_PERMISSION']);
        $pc->RedirectTo($redirectFail);
}
    }  
    $mysqli = $db->Connect();

    // Create the query
    $query     = <<< EOD
CALL {$SPFileDetailsDeleted}({$fileid}, '{$userId}', '{$deleteOrRestore}', @success);
SELECT @success AS success;
EOD;

    // Perform the query and manage results
    $results = $db->DoMultiQueryRetrieveAndStoreResultset($query);
    
    $row = $results[1]->fetch_object();
    if($row->success==1) {
    $pc->SetSessionMessage('failed',$pc->lang['FILE_NO_PERMISSION']);
    $pc->RedirectTo($redirectFail);
}
else if($row->success==2){
        $pc->SetSessionMessage('failed',$pc->lang['FILE_DOES_NOT_EXISTS']);
        $pc->RedirectTo($redirectFail);
	
}
    

    $results[1]->close();
    $mysqli->close();
    
    $pc->SetSessionMessage('success', $pc->lang['FILE_DETAILS_UPDATED']);
    $pc->RedirectTo($redirect);
}


// -------------------------------------------------------------------------------------------
//
// Default, submit-action not supported, show error and die.
// 
die($pc->lang['SUBMIT_ACTION_NOT_SUPPORTED']);
?>
