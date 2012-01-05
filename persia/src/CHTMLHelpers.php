<?php
// ===========================================================================================
//
// File: CHTMLHelpers.php
//
// Description: Class CHTMLHelpers
//
// Small code snippets to reduce coding in the pagecontrollers. The snippets are mainly for
// creating HTML code.
//
// Author: Mikael Roos, mos@bth.se
//


class CHTMLHelpers {

    // ------------------------------------------------------------------------------------
    //
    // Internal variables
    //
    

    // ------------------------------------------------------------------------------------
    //
    // Constructor
    //
    public function __construct() { ;    }
    

    // ------------------------------------------------------------------------------------
    //
    // Destructor
    //
    public function __destruct() { ; }
// ------------------------------------------------------------------------------------
//
// Create a positive (Ok/Success) feedback message for the user.
// OBSOLETE REPLACED BY GetHTMLUserFeedback('positive', $aMessage)
//
public static function GetHTMLUserFeedbackPositive($aMessage) {
return "<span class='userFeedbackPositive'>{$aMessage}</span>";
}


// ------------------------------------------------------------------------------------
//
// Create a negative (Failed) feedback message for the user.
// OBSOLETE REPLACED BY GetHTMLUserFeedback('negative', $aMessage)
//
public static function GetHTMLUserFeedbackNegative($aMessage) {
return "<span class='userFeedbackNegative'>{$aMessage}</span>";
}


// ------------------------------------------------------------------------------------
//
// Create a feedback message for the user.
// Type can be any of:
// positive, negative, info, warning, notice
// CSS for these are in blockquote.css
//
public static function GetHTMLUserFeedback($aType, $aMessage, $spanOrDiv='span') {

$aClass = '';
switch($aType) {
case 'positive': {$aClass = 'userFeedbackPositive';} break;
case 'negative': {$aClass = 'userFeedbackNegative';} break;
case 'warning': {$aClass = 'userFeedbackWarning';} break;
case 'info': {$aClass = 'userFeedbackInfo';} break;
case 'notice':
case 'default': {$aClass = 'userFeedbackNotice';} break;
}

$tag = '';
if(!empty($aMessage)) {
$tag = "<{$spanOrDiv} class='{$aClass}'>{$aMessage}</{$spanOrDiv}>";
}

return $tag;
}

    
    // ------------------------------------------------------------------------------------
    //
    // Create feedback notices if functions was successful or not. The messages are stored
    // in the session. This is useful in submitting form and providing user feedback.
    // This method reviews arrays of messages and stores them all in an resulting array.
    //
    public function GetHTMLForSessionMessages($aSuccessList, $aFailedList) {
    
        $imageLink = WS_IMAGES;
        $messages = Array();
        foreach($aSuccessList as $val) {
            $m = CPageController::GetSessionMessage($val);
            $messages[$val] = empty($m) ? '' : "<div class='userFeedbackPositiv' style=\"background: url('img/silk/accept.png') no-repeat;\"><br />{$m}</div>";
        }
        foreach($aFailedList as $val) {
            $m = CPageController::GetSessionMessage($val);
            $messages[$val] = empty($m) ? '' : "<div class='userFeedbackNegativ' style=\"background: url('img/silk/cancel.png') no-repeat;\"><br />{$m}</div>";
        }

        return $messages;
    }
// ------------------------------------------------------------------------------------
//
// Create a horisontal sidebar menu, a navgation bar, should be updated when nav
// is improved in CSS.
//
public static function GetSidebarMenu($aMenuitems, $aTarget="") {

global $gPage;

$target = empty($aTarget) ? $gPage : $aTarget;

$menu = "<ul>";
foreach($aMenuitems as $key => $value) {
$selected = (strcmp($target, substr($value, 3)) == 0) ? " class='sel'" : "";
$menu .= "<li{$selected}><a href='{$value}'>{$key}</a></li>";
}
$menu .= '</ul>';

return $menu;
}


} // End of Of Class


?>
