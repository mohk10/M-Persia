<?php
// ===========================================================================================
//
// File: PFileArchive.php
//
// Description: Show the content of the users filearchive.
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
$intFilter->UserIsSignedInOrRedirectToSignIn();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$account = $pc->SESSIONisSetOrSetDefault('accountUser');
$userId        = $_SESSION['idUser'];
// Always check whats coming in...
//$pc->IsNumericOrDie($articleId, 0);


// -------------------------------------------------------------------------------------------
//
// Open and read a directory, show its content
//
$dir = FILE_ARCHIVE_PATH . DIRECTORY_SEPARATOR . $account;

$list = Array();
if(is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            if($file != '.' && $file != '..') {
                $list[$file] = "{$file}";
            }
        }
    closedir($dh);
    }
}

ksort($list);
$archiveDisk = "<table><tr><th>Name</th></tr>";
foreach($list as $key => $value) {
    $archiveDisk .= "<tr><td>{$value}</td></tr>";
}
$archiveDisk .= '</table>';
// -------------------------------------------------------------------------------------------
//
// Get content of file archive from database
//
$db         = new CDatabaseController();
$SPListFiles=DBSP_SPListFiles;

$mysqli = $db->Connect();

// Create the query
$query     = <<< EOD
CALL {$SPListFiles}('{$userId}');
EOD;

// Perform the query
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

// Get results
$archiveDb = <<<EOD
<table class='mywidth1'>
<thead>
<th>Name</th>
<th>Unique</th>
<th>Article</th>
<th>Size</th>
<th>Type</th>
<th>Created</th>
<th>Modified</th>
</thead>
<tbody>
EOD;

global $gModule;
$editDetails = "?m={$gModule}&amp;p=file-details&amp;file=";

while($row = $results[0]->fetch_object()) {    
    $archiveDb .= <<<EOD
<tr>
<td><a href='{$editDetails}{$row->uniquename}' title='Click to view/edit details.'>{$row->name}</a></td>
<td title='{$row->path}'>{$row->uniquename}</td>
<td>{$row->article}</td>
<td>{$row->size}</td>
<td>{$row->mimetype}</td>
<td>{$row->created}</td>
<td>{$row->modified}</td>
</tr>
EOD;
}
$archiveDb .= <<<EOD
</tbody>
<tfoot>
</tfoot>
</table>
EOD;

$results[0]->close();
$mysqli->close();
// -------------------------------------------------------------------------------------------
//
// Create HTML for page
//
$htmlMain = <<<EOD
<div class='section'>
<h1>File archive</h1>
<h2>Databaseview of the archive</h2>
{$archiveDb} 
</div>

<div class='section'>
<h2>Actual content on the disk</h2>
{$archiveDisk} 
</div>

EOD;

$htmlLeft     = "";
$htmlRight    = <<<EOD
<h3 class='columnMenu'>Tags</h3>
<p>
Later...
</p>

<!--
<h3 class='columnMenu'>Hot Tags</h3>
<p>
Later...<br>
(Complete Tag Cloud)
</p>
<h3 class='columnMenu'>Recent Activity</h3>
<p>
Later...
</p>
-->
EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->PrintPage("File archive for user '{$account}'", $htmlLeft, $htmlMain, $htmlRight);
exit;
?>
