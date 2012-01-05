<?php
// ===========================================================================================
//
// config.php, config-sample.php
//
// Module specific configurations. This file is the default config-file for all modules. 
// It can be overidden by another config.php-file residing in the module library. 
// For example, a file: modules/core/config.php would replace this file. 
// This way, each module can have their own settings. 
//
// Author: Mikael Roos, mos@bth.se
//


// -------------------------------------------------------------------------------------------
//
// Settings for the database connection
//
define('DB_HOST','');  // The database host
define('DB_USER','' );
define('DB_PASSWORD','' );
define('DB_DATABASE',   '');    // The name of the database to use

define('DB_PREFIX', 	'pe_');		    // Prefix to use infront of tablename and views


// -------------------------------------------------------------------------------------------
//
// Settings for this website (WS), some used as default values in CHTMPLPage.php
//
define('WS_SITELINK',   ''); // Link to site.
define('WS_TITLE',             'Persia File Archive');                                                    // The title of this site.
define('WS_STYLESHEET', 'style/plain/stylesheet_liquid.css');    // Default stylesheet of the site.
define('WS_IMAGES',            WS_SITELINK . 'img/');                                 // Images
define('WS_FAVICON',         WS_IMAGES . 'favicon.ico');                     // Small icon to display in browser
define('WS_FOOTER',         'Persia &copy; 2010 by Mikael Roos Home Copyrights Privacy About');    // Footer at the end of the page.
define('WS_VALIDATORS', TRUE);                        // Show links to w3c validators tools.
define('WS_TIMER',             TRUE);                      // Time generation of a page and display in footer.
define('WS_CHARSET',         'utf-8');                   // Use this charset
define('WS_LANGUAGE',     'en');                      // Default language
define('WS_JAVASCRIPT',    WS_SITELINK . '/js/');    // JavaScript code


// -------------------------------------------------------------------------------------------
//
// Define the application navigation menu.
//
//$menuApps = Array (
//    'Persia'                 => 'http://dev.phpersia.org/persia/',
//    'GitHub'                 => 'http://github.com/mosbth',
    // 'Forum Romanum' => 'http://dev.phpersia.org/persia/?m=rom',
//);
//define('MENU_APPLICATION',         serialize($menuApps));


// -------------------------------------------------------------------------------------------
//
// Define the navigation menu.
//
$menuNavBar = Array (
    'Home'                 => '?m=files&amp;p=home',
    'M-Persia' 				=> '?p=home',
    'Archive'             => '?m=files&amp;p=archive',
    'Upload'            => '?m=files&amp;p=upload',
    'Install'         => '?m=files&amp;p=install',
    'Sourcecode'     => '?m=files&amp;p=ls',
);
define('MENU_NAVBAR',         serialize($menuNavBar));


// -------------------------------------------------------------------------------------------
//
// Server keys for reCAPTCHA. Get your own keys for your server.
// http://recaptcha.net/whyrecaptcha.html
//

// dev.phpersia.org
//define('reCAPTCHA_PUBLIC',    '6LcswbkSAAAAAN4kRL5qcAdiZLRo54fhlCVnt880');    
//define('reCAPTCHA_PRIVATE',    '6LcswbkSAAAAACFVN50SNO6lOC8uAlIB2cJwxknl');    

// www.student.bth.se
define('reCAPTCHA_PUBLIC',    '6LeUxbkSAAAAADjelI32xn2VdBwsMJLLiBO2umtO');    
define('reCAPTCHA_PRIVATE',    '6LeUxbkSAAAAAPRDQ8cAvEOgXMJZwb1rY2C5XauB');    


// -------------------------------------------------------------------------------------------
//
// Choose the hashing algoritm to use for storing new passwords. Can be changed during
// execution since various methods is simoultaneously supported by the database.
//
// Changing to PLAIN may imply writing an own function in PHP to encode the passwords. 
// Storing is then done as plaintext in the database, withouth using a salt.
//
// This enables usage of more complex hashing and encryption algoritms that are currently not
// supported within MySQL.
//
//define('/* <em>DB_PASSWORD,  is removed and hidden for security reasons </em> */ );
//#define('/* <em>DB_PASSWORD,  is removed and hidden for security reasons </em> */ );
//#define('/* <em>DB_PASSWORD,  is removed and hidden for security reasons </em> */ );


// -------------------------------------------------------------------------------------------
//
// Set the default email adress to be used as from in mail sent from the system to 
// account users. Be sure to set a valid domain to avoid spamfilters.
//
define('WS_MAILFROM',                 'Persia Development Team <no-reply@nowhere.org>');
define('WS_MAILSUBJECTLABEL', '[Persia] ');
define('WS_MAILSIGNATURE',     
    "\n\nBest regards,\n" .
    "The Development Team Of Persia\n" .
    "http://phpersia.org\n"
);


// -------------------------------------------------------------------------------------------
//
// Display the following actions if they are enabled.
// Set true to enable, false to disable.
// 
//define('CREATE_NEW_ACCOUNT', true);
//define('FORGOT_PASSWORD', true);


// -------------------------------------------------------------------------------------------
//
// Settings for LDAP and LDAP authentication.
//
//define('LDAP_AUTH_SERVER', 'ldap.dbwebb.se');
//define('LDAP_AUTH_BASEDN', 'dc=dbwebb,dc=se');


// -------------------------------------------------------------------------------------------
//
// Settings for file upload and file archive.
//
define('FILE_ARCHIVE_PATH', ''); // Must be writable by webserver
define('FILE_MAX_SIZE', 30000); // Filesize in bytes


?>
