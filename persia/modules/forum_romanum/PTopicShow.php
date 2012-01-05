<?php
// ===========================================================================================
//
// PTopicShow.php
//
// Show the content of a topic, including topic details and all posts.
//
// Author: Mikael Roos, mos@bth.se
//


// -------------------------------------------------------------------------------------------
//
// Get pagecontroller helpers. Useful methods to use in most pagecontrollers
//
$pc = new CPageController();
//$pc->LoadLanguage(__FILE__);


// -------------------------------------------------------------------------------------------
//
// Interception Filter, controlling access, authorithy and other checks.
//
$intFilter = new CInterceptionFilter();

$intFilter->FrontControllerIsVisitedOrDie();
//$intFilter->UserIsSignedInOrRecirectToSignIn();
//$intFilter->UserIsMemberOfGroupAdminOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$topicId    = $pc->GETisSetOrSetDefault('id', 0);
//$userId        = $_SESSION['idUser'];

// Always check whats coming in...
$pc->IsNumericOrDie($topicId, 0);


// -------------------------------------------------------------------------------------------
//
// User is admin or owner of this post
//
//global $gModule;
//$imageLink = WS_IMAGES;

$urlToEditPost = "?m=rom&amp;p=post-edit&amp;id=";

$postEditMenu = <<<EOD

EOD;

/*
$ownerMenu = "";
if($intFilter->IsUserMemberOfGroupAdminOrIsCurrentUser($owner)) {
    $ownerMenu = <<<EOD
[
<a href="?m=rom&amp;p=post-edit&amp;editor=markItUp&amp;id={$topicId}">edit</a>
]
EOD;
}
*/


// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database, get the query and execute it.
// Relates to files in directory TP_SQLPATH.
//
$db     = new CDatabaseController();
$mysqli = $db->Connect();

// Get the SP names
$spSPGetTopicDetailsAndPosts = DBSP_SPGetTopicDetailsAndPosts;
$SPListFiles2=DBSP_SPListFiles2;

$query = <<< EOD
CALL {$spSPGetTopicDetailsAndPosts}({$topicId});
EOD;

// Perform the query
$results = Array();
$res = $db->MultiQuery($query); 
$db->RetrieveAndStoreResultsFromMultiQuery($results);
    
// Get topic details
$row = $results[0]->fetch_object();
$title                 = $row->title;
$createdBy        = $row->creator;
$createdWhen    = $row->mydate;
$lastPostBy     = $row->lastpostby;
$lastPostWhen    = $row->lastpostwhen;
$numPosts            = $row->postcounter;
$results[0]->close(); 

// Get the list of posts
$posts = <<<EOD
<table class='mywidth1' >
EOD;
while($row = $results[1]->fetch_object()) {

    $isEditable = "<a class='mya' title='Edit this post' href='{$urlToEditPost}{$row->postid}'>?</a>";
    $isEditable = ($intFilter->IsUserMemberOfGroupAdminOrIsCurrentUser($row->userid)) ? $isEditable : '';
    $upLoad = "<a class='mya' title='Upload to this post' href='?m=files&p=upload&postid={$row->postid}'>upL</a>";
    $upLoad = ($intFilter->IsUserMemberOfGroupAdminOrIsCurrentUser($row->userid)) ? $upLoad : '';
    //echo("isEditable=".$isEditable);
    // -------------------------------------------------------------------------------------------
//
// Get content of file archive from database
//

$mysqli = $db->Connect();

// Create the query
$query2     = <<< EOD
CALL {$SPListFiles2}({$row->postid});
EOD;

$myfiles="";
// Perform the query
$results2 = $db->DoMultiQueryRetrieveAndStoreResultset($query2);
//print_r($results2[0]);
while($row2= $results2[0]->fetch_object()) {

if(!empty($isEditable)){
	$myfiles.="<a class='mya' title='Edit/Download this file' href='?m=files&amp;p=file-details&amp;file={$row2->uniquename}'>$row2->name</a><br />";
}
else{
		$myfiles.="<a class='mya' title='Download this file' href='?m=files&amp;p=download&amp;file={$row2->uniquename}'>$row2->name</a><br />";

}
}
//echo("row->gravatar=".$row->gravatar);
if($row->gravatar){
$aSize="105";
$gravatar=md5( strtolower( trim( $row->gravatar ) ) );
$gravatar="http://www.gravatar.com/avatar/".$gravatar.".jpg?s=".$aSize;
$myimage="<img src='{$gravatar}' alt='bild gravatar'><br>";
}else{
	$myimage="<img src='{$row->avatar}' alt='bild avatar' ><br>";
}


    $posts .= <<<EOD
<tr>
<td style='border-bottom: solid 2px #eee;width:20%;'>
{$myimage}
<p class='small'>
{$row->username}<br>
{$row->mydate}
</p>
</td>
<td style='border-bottom: solid 2px #eee; text-align: left; vertical-align: top;'>
<div style='float:right'>
<a class='noUnderline mya' name='post-{$row->postid}' title='Link to this post' href='#post-{$row->postid}'>#</a> | 
{$isEditable} |
{$upLoad}<br />
{$myfiles}
</div>
{$row->text}
</td>
</tr>
EOD;
}
$posts .= "</table>";

$results[1]->close(); 
$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Page specific code
//
/*
<p>{$content}</p>
<p class='notice'>
By {$username}. Updated: {$saved}. {$ownerMenu}
</p>
*/
//global $gModule;

$urlToAddReply = "?m=rom&amp;p=post-edit&amp;topic={$topicId}";

$htmlMain = <<<EOD
<h1>{$title}</h1>
{$posts}
<p>
<a class='mya' href='{$urlToAddReply}'>Add reply</a>
</p>
EOD;

$htmlLeft     = "";
$htmlRight    = <<<EOD
<h3 class='columnMenu'>About This Topic</h3>
<p>
Created by {$createdBy} {$createdWhen}.<br>
</p>
<p>
$numPosts posts.<br>
</p>
<p>
Last reply by {$lastPostBy} {$lastPostWhen}<br>
</p>

<!--
Later...<br>
(num viewed, latest accessed. Tags. Solved. Posted in Category.)
</p>
<h3 class='columnMenu'>Related Topics</h3>
<p>
Later...<br>
(Do search, show equal (and hot/popular) topics)
</p>
<h3 class='columnMenu'>About Author</h3>
<p>
Later...
</p>
<h3 class='columnMenu'>More by this author</h3>
<p>
Later...
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

//$page->PrintPage("Topic: {$title}", $htmlLeft, $htmlMain, $htmlRight);
//exit;
$name="Forum Romanum Mission Statement";




$page->printHTMLHeader($name);
$page->printPageHeader2($name);
$page->printStartSection();
$page->printPageBody($htmlMain);
$page->printCloseSection();
$page->printAside($htmlRight);
$page->printPageFooter();
?>
