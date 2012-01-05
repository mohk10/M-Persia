<?php
// ===========================================================================================
//
// P3Columns.php
//
// Showing example of a 3 column layout page.
//


// -------------------------------------------------------------------------------------------
//
// Interception Filter, access, authorithy and other checks.
//
if(!isset($indexIsVisited)) die('No direct access to pagecontroller is allowed.');


// -------------------------------------------------------------------------------------------
//
// Page specific code
//

$htmlMain = <<<EOD
<h2>3-kolumners layout</h2>
<p>
Denna sidan använder en stylesheet som stödjer 3 kolumner. CHTMLPage uppdaterad för att klara 
denna layout.
</p>
<p>
Stylesheeten ser du här: <a href='?p=style&dir=&file=original_3cols.css'>original_3cols.css</a>.
</p>
<p>
CHTMLPage ser du här: <a href='?p=ls&dir=src&file=CHTMLPage.php'>CHTMLPage.php</a>.
</p>
<p>
P3Columns ser du här: <a href='?p=ls&dir=pages/app_syw&file=P3Columns.php'>P3Columns.php</a>.
</p>
EOD;

$htmlLeft = <<<EOD
<h3 class='menu'>Bra att ha vänster</h3>
<p>
Här finns nu en meny kolumn som går att använda till bra att ha saker.
</p>
EOD;

$htmlRight = <<<EOD
<h3 class='menu'>Bra att ha höger</h3>
<p>
Här finns nu en meny kolumn som går att använda till bra att ha saker.
</p>
EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
require_once(TP_SOURCEPATH . 'CHTMLPage.php');

$style = 'pages/app_syw/stylesheets/original_3cols.css';

$page = new CHTMLPage($style);

$page->addPageBodyLeft($htmlLeft);
$page->addPageBodyRight($htmlRight);
$page->addPageBodyMain($htmlMain);

$page->printHTMLHeader('3-kolumners layout');
$page->printPageHeader();
$page->printPageBody();
$page->printPageFooter();
exit;
 
?>
