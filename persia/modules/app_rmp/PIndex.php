<?php
// ===========================================================================================
//
// PIndex.php
//
// Documenting my small template-site.
//


// -------------------------------------------------------------------------------------------
//
// Page specific code
//

$html = <<<EOD
<h2>Välkommen</h2>
<h3>Introduktion</h3>
<p>
I vår strävan efter maximal kvalitet vill vi få direktkoppling till vad studenterna anser om 
lärarnas prestation. Resultaten från denna site kommer att ha en direkt påverkan av löneutvecklingen 
hos läraren.
</p>
EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
require_once(TP_SOURCEPATH . 'CHTMLPage.php');

$page = new CHTMLPage();

$page->printHTMLHeader('Välkommen');
$page->printPageHeader();
$page->printPageBody($html);
$page->printPageFooter();

 
?>
