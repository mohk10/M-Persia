<?php
// ===========================================================================================
//
// File: PAccountForgotPassword1Process.php
//
// Description: Language file
//
// Author: Mikael Roos, mos@bth.se
//

$lang = Array(
    'CAPTCHA_FAILED' => "CAPTCHA check failed: The magic word did not match, please try again.",
    'NO_MATCH' => "There is no account with such name nor such mailadress.",
    'NO_MAIL_CONNECTED' => "There is no mail connected with this account. This service needs an mail to be connected".
    "\n".
    " to the account. Otherwise it can not assist.",
    'SUBMIT_ACTION_NOT_SUPPORTED' => "The action is not supported by this page. Report this as an error.",

    // Mail
    'SUCCESSFULLY_SENT_MAIL' => "Successfully sent mail to '%s'.",
    'FAILED_SENDING_MAIL' => "Failed to send mail to '%s'. Perhaps malformed mailadress?",

    // Change email confirmation mail
    'MAIL_LOST_PASSWORD_SUBJECT' => "".WS_MAILSUBJECTLABEL."Have you lost your password?",
    'MAIL_LOST_PASSWORD_BODY' => 
        "Hi," .
        "\n" .
        "It seems like you have asked for help with resetting your password. " . 
        "You can safely ignore this mail if that is not correct." . 
        "\n\n" .
        "The key below is needed to reset your password. Copy and paste it into the webform. " . 
        "The key is active for one hour. After that you will need to redo the procedure again. The key follows: " . 
        "\n\n" .
        "%s" . 
        WS_MAILSIGNATURE,


);

?>
