<?php
// ===========================================================================================
//
// config.php
//
// Website specific configurations.
//
// Settings fo the template (TP) structure, where are everything?
// Support for storing in directories
//
// Classes, functions, code
define('TP_SOURCEPATH', dirname(__FILE__).'/src/');
// Pagecontrollers and modules
define('TP_PAGESPATH', dirname(__FILE__).'/pages/');
//echo(dirname(__FILE__));


// -------------------------------------------------------------------------------------------
//
// Settings for this website (WS), used as default values in CHTMPLPage.php
//http://localhost/bth/dbwebb1/kmom05/?p=kommentera&idLarare=1
//
//define('WS_SITELINK',   'http://www.student.bth.se/~mohk10/dbwebb1/kmom07-10/'); // Link to site.
define('WS_TITLE',       'Foggler');  // The H1 label of this site.
//define('WS_STYLESHEET',   'stylesheet2.css');      // Default stylesheet of the site.
define('WS_FOOTER',     '&copy;2011 Morgan Hagberg');  // Footer at the end of the page.

//
// Define the menu-array, slight workaround using serialize.
//
$wsMenu = Array (
  'Hem'           => '?p=visabloggar',
  'M-Persia'	=>'?p=home',
  'Installera db' => '?p=install2',
  'Visa filer'     => '?p=ls',
  'ER-diagram'    =>'modules/blog/images/ER_diagram_projekt.pdf',
  '<img src=\'modules/blog/images/rss.jpg\' height=\'20\' width=\'20\' />'=>'?p=rss10',
);
define('WS_MENU',     serialize($wsMenu));    // The menu
?>
