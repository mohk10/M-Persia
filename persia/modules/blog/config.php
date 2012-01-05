<?php
// ===========================================================================================
//
// config.php
//
// Website specific configurations.
//
// Settings fo the template (TP) structure, where are everything?
// Support for storing in directories
//echo(dirname(__FILE__));
// -------------------------------------------------------------------------------------------
//
// Settings for the database connection
//
define('DB_HOST','');  // The database host
define('DB_USER','' );
define('DB_PASSWORD','' );
define('DB_DATABASE',   '');    // The name of the database to use

//
// The following supports having many databases in one database by using table/view prefix.
//
define('DB_PREFIX',   'rpm07_');    // Prefix to use infront of tablename and views

// -------------------------------------------------------------------------------------------
//
// Settings for this website (WS), used as default values in CHTMPLPage.php
//http://localhost/bth/dbwebb1/kmom05/?p=kommentera&idLarare=1
//
define('WS_SITELINK',   ''); // Link to site.
define('WS_TITLE',       'Foggler');  // The H1 label of this site.
define('WS_STYLESHEET',   'stylesheet2.css');      // Default stylesheet of the site.
define('WS_FOOTER',     '&copy;2011 Morgan Hagberg');  // Footer at the end of the page.
define('WS_TIMER', 		TRUE);              // Time generation of a page and display in footer.

//
// Define the menu-array, slight workaround using serialize.
//
$wsMenu = Array (
  'Hem'           => '?m=blog&amp;p=visabloggar',
  'Installera db' => '?m=blog&amp;p=install2',
  'Visa filer'     => '?m=blog&amp;p=ls',
  'ER-diagram'    => 'img/ER_diagram_projekt.pdf',
   'M-Persia' => '?m=core',
  '<img src=\'img/rss.jpg\' height=\'20\' width=\'20\'  alt=\'rss bild\' />'=>'?m=blog&amp;p=rss10',
 
);
define('WS_MENU',     serialize($wsMenu));    // The menu
?>
