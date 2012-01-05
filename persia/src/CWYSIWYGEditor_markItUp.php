<?php
// ===========================================================================================
//
// File: CWYSIWYGEditor_markItUp.php
//
// Description: Class CWYSIWYGEditor_markItUp
//
// Support for WYSIWYG JavaScript editor markItUp.
// http://markitup.jaysalvat.com/home/
//
// Author: Mikael Roos, mos@bth.se
//


class CWYSIWYGEditor_markItUp extends CWYSIWYGEditor {

// ------------------------------------------------------------------------------------
//
// Internal variables
//

// ------------------------------------------------------------------------------------
//
// Constructor
//
public function __construct($aTextareaId='', $aTextareaClass='', $aSubmitId='', $aSubmitClass='') {
parent::__construct($aTextareaId, $aTextareaClass, $aSubmitId, $aSubmitClass);
}


// ------------------------------------------------------------------------------------
//
// Destructor
//
public function __destruct() { ; }


// ------------------------------------------------------------------------------------
//
// Does this editor need the jQuery JavaScript library?
// Subclasses who does should reimplement this method and return TRUE.
//
public function DependsOnjQuery() {
return TRUE;
}


// ------------------------------------------------------------------------------------
//
// Return the HTML header for the editor, usually stylesheet, js-file and javascript
// code to instantiate editor.
//
public function GetHTMLHead() {

$tpJavaScript = WS_JAVASCRIPT;

$head = <<<EOD
<script type="text/javascript" src="js/markitup/markitup/jquery.markitup.js"></script>
<script type="text/javascript" src="js/markitup/markitup/sets/html/set.js"></script>
<link rel="stylesheet" type="text/css" href="js/markitup/markitup/skins/live/style.css" />
<link rel="stylesheet" type="text/css" href="js/markitup/markitup/sets/html/style.css" />
<!-- jGrowl latest -->
<link rel='stylesheet' href='js/jGrowl/jquery.jgrowl.css' type='text/css' />
<script type='text/javascript' src='js/jGrowl/jquery.jgrowl.js'></script>  
<!-- jQuery Form Plugin, included with jquery.autosave -->
<script type='text/javascript' src='js/jquery-autosave/jquery.form.js'></script> 
<!-- jquery.autosave latest -->
<!--<script type='text/javascript' src='js/jGrowl/jquery-autosave.js'></script>--> 
<!-- ==================================================================================== -->
EOD;

return $head;
}


} // End of Of Class

?>
