<?php
// ===========================================================================================
//
// PPostEdit.php
//
// A post editor. Create or edit a post.
//
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
//$intFilter->UserIsMemberOfGroupAdminOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
//global $gModule;

$editor        = $pc->GETisSetOrSetDefault('editor', 'plain');
$postId        = $pc->GETisSetOrSetDefault('id', 0);
$topicId    = $pc->GETisSetOrSetDefault('topic', 0);
$userId = $pc->SESSIONisSetOrSetDefault('idUser', 0);

// Always check whats coming in...
$pc->IsNumericOrDie($postId, 0);
$pc->IsNumericOrDie($topicId, 0);
$pc->IsStringOrDie($editor);
//$redirectOnSuccess = "?m={$gModule}&p=post-edit&id=%2\$d&editor={$editor}";

// Publish button is initially disabled
$publishDisabled = 'disabled="disabled"';



// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database, get the query and execute it.
// Relates to files in directory TP_SQLPATH.
//
$title         = "";
$content     = "";

// Connect
$db     = new CDatabaseController();
$mysqli = $db->Connect();

// Get the SP names
$spSPGetTopicDetails = DBSP_SPGetTopicDetails;
$spSPGetPostDetails    = DBSP_SPGetPostDetails;

$query = <<< EOD
CALL {$spSPGetTopicDetails}({$topicId}, {$postId});
CALL {$spSPGetPostDetails}({$postId});
EOD;

// Perform the query
$results = Array();
$res = $db->MultiQuery($query); 
$db->RetrieveAndStoreResultsFromMultiQuery($results);

// Get topic details
$row = $results[0]->fetch_object();
$topicId         = empty($row->topicid)    ? $topicId : $row->topicid;
$topicTitle    = empty($row->title)         ? '' : $row->title;
$topPost        = empty($row->toppost)     ? 0 : $row->toppost;
$results[0]->close(); 

// Get post details
$row = $results[2]->fetch_object();
$title             = empty($row->title)             ? $pc->lang['NEW_TITLE'] : $row->title;
$content         = empty($row->text)         ? '' : $row->text;
$saved             = empty($row->mydate)         ? $pc->lang['NOT_YET'] : $row->mydate;
$isPublished     = empty($row->isPublished)        ? FALSE : $row->isPublished;
$hasDraft             = empty($row->hasDraft)             ? FALSE : $row->hasDraft;
$draftTitle     = empty($row->draftTitle)         ? '' : $row->draftTitle;
$draftContent    = empty($row->draftText)     ? '' : $row->draftText;
$draftSaved         = empty($row->draftModified)     ? '' : $row->draftModified;
$results[2]->close(); 

$mysqli->close();
//
// Use draft version if it exists 
//
if($hasDraft) {
    $title = $draftTitle;
    $content = $draftContent;
    $publishDisabled = '';
}
// -------------------------------------------------------------------------------------------
//
// Use a JavaScript editor
//
$jsEditor = CWYSIWYGEditorFactory::CreateObject($editor);
$jsEditorTextarea = $jsEditor->GetTextareaSettings();
$jsEditorSubmit = $jsEditor->GetSubmitSettings();
$htmlHead = $jsEditor->GetHTMLHead();
$needjQuery = $jsEditor->DependsOnjQuery();

$needjQuery = TRUE;

$redirectOnSuccess = 'json';
$javascript = <<<EOD
// ----------------------------------------------------------------------------------------------
//
//
//
$(document).ready(function() {

    // Just showing off jGrowl to see that it works
    $.jGrowl("Hello World. This is Growl. Page is now loaded, or re-loaded, I'm not sure on which...");

    // ----------------------------------------------------------------------------------------------
    //
    // Upgrade form to make Ajax submit
    //
    $('#form1').ajaxForm({
        // return a datatype of json
        dataType: 'json',
        // do stuff before submitting form
        beforeSubmit: function(data, status) {
        		autosave.callbackBeforeSave();
                        $.jGrowl('Saving (' + $('#action').val() + ')...');
                },    
        // define a callback function
        success: function(data, status) {
                        $('#topic_id').val(data.topicId);
                        $('#post_id').val(data.postId);
                        $('#isPublished').val(data.isPublished);
                        $('#hasDraft').val(data.hasDraft);
                        if(data.isPublished&&(!data.hasDraft)){ $('button#publish').attr('disabled', 'disabled');}
                        $.jGrowl('Saved: ' + status + ' at ' + data.timestamp + ' Topic: ' + data.topicId + ', post: ' + data.postId + ', isPublished=' + data.isPublished + ' hasDraft=' + data.hasDraft);
                }    
    });
// ----------------------------------------------------------------------------------------------
    //
    // Code to enable autosave and integrate it with existing form.
    //
    // 1) Set a callback on all keypress-event in the form. This is to keep track on the form has 
    // changed.
    // 2) In the callback, disable 1), enable the save-button och initiate a timer that submits the 
    // form after a defined time.
    // 3) If user initiates a save, then cancel the timer.
    // 4) When autosaved, disable the save-button and enable the onkeypress-event again.
    //
    // http://api.jquery.com/bind/
    // http://api.jquery.com/unbind/
    // https://developer.mozilla.org/en/DOM/window.setTimeout
    //
    var autosave = {
        //
        // Tme between each autosave fires off.
        //
        time: 5000,

        //
        // Store the id of the timer, use to cancel timer if save is initiated by user.
        //
        id:    null,
        
        //
        // Callback function that carries out the ajax-submit for autosave.
        //
        callbackSave: function() {
        	$('#action').val('draft');
                $('#form1').submit(); 
            },
        
        //
        // Callback function to change state before submitting
        //
        callbackBeforeSave: function() {
                // Disable the button until form has changed again
                $('button#savenow').attr('disabled', 'disabled');
                
                // Clear the timeout, it might have been user initiated, no need for timer than
                clearTimeout(autosave.id);
                
                // Bind the event again to discover when form has changed
                $('#form1').bind('keypress', autosave.callbackDetect);
            },
        
        //
        // Function that detects if the form has changed
        //
        callbackDetect:    function(event){
                // Unbind the event, we already know that the form has changed, no need to detect it twice
                $('#form1').unbind('keypress');
                
                // Enable the save button
                $('button#publish').removeAttr('disabled');
                $('button#savenow').removeAttr('disabled');
                
                // Set the timer that will eventually fire off the autosave event
                autosave.id = setTimeout(autosave.callbackSave, autosave.time);
                
                // Just say that the form changed
                $.jGrowl('Form changed!');
            }
    };
    $('#form1').bind('keypress', autosave.callbackDetect);

    // ----------------------------------------------------------------------------------------------
    //
    // Event handler for buttons in form. Instead of messing up the html-code with javascript.
    // Using Event bubbling as described in this document:
    // http://docs.jquery.com/Tutorials:AJAX_and_Events
    //
    $('#form1').click(function(event) {
        if ($(event.target).is('button#publish')) {
            // Disable the button until form has changed again
            $('#action').val('publish');
            //$('button#publish').attr('disabled', 'disabled');
            }else if ($(event.target).is('button#savenow')) {
        	$('#action').val('draft');           
    } else if ($(event.target).is('button#discard')) {
            history.back();
    } else if ($(event.target).is('a#viewPost')) {
		 $.jGrowl('View published post...');
            if($('#isPublished').val() == 1) {
                $('a#viewPost').attr('href', '?m=rom&p=topic&id=' + $('#topic_id').val() + '#post-' + $('#post_id').val());        
            } else {
                alert('The post is not yet published. Press "Publish" to do so.');
                return(false);
            }
            }
            //if(do_next){buttonPublishClick();}
    });   
});

EOD;
// -------------------------------------------------------------------------------------------
//
// Use a JavaScript editor
//
//$jseditor;
//$jseditor_submit = "";

/*switch($editor) {

    case 'markItUp': {
        $jseditor = new CWYSIWYGEditor_markItUp('text', 'text');
    }
    break;

    case 'WYMeditor': {
        $jseditor = new CWYSIWYGEditor_WYMeditor('text', 'text');
        $jseditor_submit = 'class="wymupdate"'; 
    }
    break;

    case 'NicEdit': {
        $jseditor = new CWYSIWYGEditor_NicEdit('text', 'size98percentx300');
    }
    break;

    case 'plain': 
    default: {
        $jseditor = new CWYSIWYGEditor_Plain('text', 'size98percentx300');
    }
    break;
}*/


// -------------------------------------------------------------------------------------------
//
// Page specific code
//

// Change form depending on usage
$h1                 = '';
$titleForm     = '';

if($topicId == 0 && $postId == 0) {
    $h1                 = $pc->lang['CREATE_NEW_TOPIC'];
    $titleForm     = "Topic: <input class='title' type='text' name='title' value='{$title}'>";
     $publishDisabled = '';
} else if($topicId != 0 && $postId == 0) {
$h1                 = $pc->lang['ADD_REPLY'];
    $titleForm     = "<h2>In topic: \"{$topicTitle}\"</h2>";
} else if($postId != 0 && $topPost == $postId) {
    $h1                    = $pc->lang['EDIT_POST'];
    $titleForm     = "Topic: <input size=50 class='title' type='text' name='title' value='{$title}'>";
} else if($postId != 0) {
$h1                    = $pc->lang['EDIT_POST'];
    $titleForm     = "<h2>In topic: \"{$topicTitle}\"</h2>";
}
//echo("jsEditorSubmit=".$jsEditorSubmit);
// Only show title if new topic
$formTitle = "";
$formTitle = ($topicId == 0) ? $formTitle : '';

$htmlMain = <<<EOD
<h1>{$h1}</h1>
<fieldset class='article'>
<!-- Form to keep status values during ajax-calls-->
<form>
<input type='hidden' id='isPublished' value='{$isPublished}'>
<input type='hidden' id='hasDraft'     value='{$hasDraft}'>
</form>
<!-- The real form -->
<form  id='form1' action='?m=rom&amp;p=post-save' method='POST'>
<input type='hidden' name='redirect_on_success' value='{$redirectOnSuccess}'>
<input type='hidden' name='redirect_on_failure' value=''>
<input type='hidden' id='post_id' name='post_id' value='{$postId}'>
<input type='hidden' id='topic_id' name='topic_id' value='{$topicId}'>
<input type='hidden' id='action'     name='action' value=''>
<p>
{$titleForm}
</p>
<p>
<textarea {$jsEditorTextarea} cols=60 rows=10  name='content'>{$content}</textarea>
</p>
<p class='notice'>
Saved: {$saved}
</p>
<p>
<p>
<button id='publish' {$publishDisabled} {$jsEditorSubmit} type='submit' ><img src='img/silk/accept.png' alt=''>{$pc->lang['PUBLISH']}</button>
<button id='savenow' disabled='disabled' {$jsEditorSubmit} type='submit' ><img src='img/silk/disk.png' alt=''>{$pc->lang['SAVE_NOW']}</button>
<button id='discard' type='reset'><img src='img/silk/cancel.png' alt=''>{$pc->lang['DISCARD']}</button>
<p>
<a id='viewPost' class='mya' title='Click to view the published post' href='?m={$gModule}&amp;p=topic&amp;id={$topicId}#post-{$postId}'>{$pc->lang['VIEW_PUBLISHED_POST_LINK']}</a>
</p>
<!--
<input type='button' value='Delete' onClick='if(confirm("Do you REALLY want to delete it?")) {form.action="?p=article-delete"; form.redirect_on_success.value="?m=rom&amp;p=topics"; submit();}'>
-->
</p>
</form>
</fieldset>
EOD;

$htmlLeft     = "";
$htmlRight    = <<<EOD
<h3 class='columnMenu'>{$pc->lang['CHANGE_EDITOR']}</h3>
<p>
<a class='mya' href='?m=rom&amp;p=post-edit&amp;id={$postId}&amp;topic={$topicId}'>Plain</a> | 
<a class='mya' href='?m=rom&amp;p=post-edit&amp;editor=markItUp&amp;id={$postId}&amp;topic={$topicId}'>markItUp!</a> 
</p>
<!--
<h3 class='columnMenu'>About This Topic</h3>
<p>
Later...Created by, num posts, num viewed, latest accessed. Tags.
</p>
<h3 class='columnMenu'>Related Topics</h3>
<p>
Later...Do search, show equal (and hot/popular) topics
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

//$page->PrintPage("Create/edit post", $htmlLeft, $htmlMain, $htmlRight, $jseditor->GetHTMLHead());
//exit;
$name="Forum Romanum Mission Statement";


if($editor=='markItUp'){
	$page->printHTMLHeader2($name,$javascript,$htmlHead);
}else{
	$page->printHTMLHeader($name,$javascript);
}
$page->printPageHeader2($name);
$page->printStartSection();
$page->printPageBody($htmlMain);
$page->printCloseSection();
$page->printAside($htmlRight);
$page->printPageFooter();
?>
