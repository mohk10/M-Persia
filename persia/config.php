<?php
// ===========================================================================================
//
// config.php, config-sample.php
//
// Module specific configurations. This file is the default config-file for modules. It can
// be overidden by another config.php-file residing in the module library. For example,
// a file: modules/core/config.php would replace this file.
// This way can each module can have their own settings.
// All definitions must be made if done in a module specific config.php.
//
// Author: Mikael Roos, mos@bth.se
//

// -------------------------------------------------------------------------------------------
//
// Settings for the database connection
//
define('DB_HOST','');  // The database host
define('DB_USER','' ); // The user name
define('DB_PASSWORD','' ); // Password
define('DB_DATABASE',   '');    // The name of the database to use

define('DB_PREFIX', 	'pe_');		    // Prefix to use infront of tablename and views


// -------------------------------------------------------------------------------------------
//
// Settings for this website (WS), some used as default values in CHTMPLPage.php
//
// PERHAPS SPLIT THIS WHEN UPDATING CHTMLPAGE
//
define('WS_SITELINK',   ''); // Link to site.
define('WS_TITLE', 		'M-Persia');		    // The title of this site.
define('WS_STYLESHEET', 'style/plain/stylesheet_liquid.css');	// Default stylesheet of the site.
define('WS_STYLESHEET2', 'style/stylesheet2.css');	// Default 2 stylesheet of the site.
define('WS_FAVICON', 	'img/favicon.ico'); // Small icon to display in browser
define('WS_FOOTER', 	'M-Persia &copy; 2011 by Morgan Hagberg');	// Footer at the end of the page.
define('WS_VALIDATORS', TRUE);	            // Show links to w3c validators tools.
define('WS_TIMER', 		TRUE);              // Time generation of a page and display in footer.
define('WS_CHARSET', 	'utf-8');           // Use this charset
define('WS_LANGUAGE', 	'en');              // Default language
define('WS_IMAGES','img/');

// -------------------------------------------------------------------------------------------
//
// Server keys for reCAPTCHA. Get your own keys for your server.
// http://recaptcha.net/whyrecaptcha.html
//

// dev.phpersia.org
//define('reCAPTCHA_PUBLIC', '6LcswbkSAAAAAN4kRL5qcAdiZLRo54fhlCVnt880');
//define('reCAPTCHA_PRIVATE', '6LcswbkSAAAAACFVN50SNO6lOC8uAlIB2cJwxknl');

// www.student.bth.se
//
// My own keys. Created at reCAPTCHA website.
//
define('reCAPTCHA_PUBLIC', '6LcCbcoSAAAAAJpoIqaB2ZTKrRF-zomjmnsVH47Y');
define('reCAPTCHA_PRIVATE', '6LcCbcoSAAAAAEx9jkcOIgS4Kxe8mcFVCgwOAJ0u'); 
// -------------------------------------------------------------------------------------------
//
// Define the navigation menu.
//
// MOVE THIS TO CHTMLPAGE OR OTHER CONFIG-FILE OR LEAVE IT AS IT IS?
//
$menuNavBar = Array (
	'Home' 				=> '?p=home',
	'Me'				=> '?p=myme' ,
	'Template'	 		=> '?p=template',
	'Blog'				=> '?m=blog&amp;p=visabloggar',
	'Forum romanum'			=> '?m=rom',
	'Filearchive'			=> '?m=files&amp;p=archive',
	'About' 			=> '?p=about',
	'404' 				=> '?p=NOT_EXISTING',
	'Install' 			=> '?p=install',
	'Sourcecode' 		=> '?p=ls',
);
define('MENU_NAVBAR', 		serialize($menuNavBar));
// -------------------------------------------------------------------------------------------
//
// Define the application navigation menu.
//
//$menuApps = Array (
//'Persia' => 'http://dev.phpersia.org/persia/',
//'GitHub' => 'http://github.com/mosbth',
//'Forum Romanum' => 'http://dev.phpersia.org/persia/?m=rom',
//);
//define('MENU_APPLICATION', serialize($menuApps));
// -------------------------------------------------------------------------------------------
//
// Set the default email adress to be used as from in mail sent from the system to 
// account users. Be sure to set a valid domain to avoid spamfilters.
//
define('WS_MAILFROM',                 'M-Persia Development Team <no-reply@nowhere.org>');
define('WS_MAILSUBJECTLABEL', '[M-Persia] ');
define('WS_MAILSIGNATURE',     
    "\n\nBest regards,\n" .
    "The Development Team Of M-Persia\n" .
    "http://www.student.bth.se/~mohk10/dbwebb2/persia\n"
);
// -------------------------------------------------------------------------------------------
//
// Settings for file upload and file archive.
//
define('FILE_ARCHIVE_PATH', ''); // Must be writable by webserver
define('FILE_MAX_SIZE', 30000); // Filesize in bytes


?>
