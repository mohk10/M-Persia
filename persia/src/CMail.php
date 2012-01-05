<?php
// ===========================================================================================
//
// File: CMail.php
//
// Description: Class CMail
//
// A interface to send email.
//
// Author: Mikael Roos, mos@bth.se
//


class CMail {

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
    // Send the mail.
    // http://se.php.net/manual/en/function.mail.php
    //
  public function SendMail($to, $subject, $message, $from=WS_MAILFROM) {

        // To send HTML mail, the Content-type header must be set
        $headers  = '';
        //$headers  = 'MIME-Version: 1.0' . "\r\n";
        //$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        //$headers .= 'To: ' . $to . "\r\n";
        $headers .= 'From: ' . $from . "\r\n";
        //$headers .= 'Cc: birthdayarchive@example.com' . "\r\n";
        //$headers .= 'Bcc: birthdaycheck@example.com' . "\r\n";
        //$headers .= 'Reply-To: ' . $from . "\r\n";
    //$headers .= 'X-Mailer: Persia PHP/' . phpversion();
    
    // In case any of our lines are larger than 70 characters, we should use wordwrap()
        $message = wordwrap($message, 70);

        // Windows only
        //$message = str_replace("\n.", "\n..", $message);

        return mail($to, $subject, $message, $headers);
    }


} // End of Of Class


?>
