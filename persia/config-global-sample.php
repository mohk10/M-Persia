<?php
// ===========================================================================================
//
// config-global.php, config-global-sample.php
//
// Global site specific configurations. Same for all modules and pagecontrollers.
// Usually not needed to change.
//
// Author: Mikael Roos, mos@bth.se
//

// -------------------------------------------------------------------------------------------
//
// Settings for the template (TP) structure, show where everything are stored.
// Support for storing in directories, no need to store everything under one directory
//
define('TP_ROOT', dirname(__FILE__) . '/'); // The root of installation
define('TP_SOURCEPATH', dirname(__FILE__) . '/src/'); // Classes, functions, code
define('TP_MODULESPATH', dirname(__FILE__) . '/modules/'); // Modules
define('TP_PAGESPATH', dirname(__FILE__) . '/modules/core/'); // Pagecontrollers
define('TP_LANGUAGEPATH', dirname(__FILE__) . '/lang/'); // Multi-language support
define('TP_SQLPATH', dirname(__FILE__) . '/sql/'); // SQL code
define('TP_MEPATH', dirname(__FILE__) . '/me/'); // SQL code

define('DB_APASSWORDHASHING', 'SHA-1');

// -------------------------------------------------------------------------------------------
//
// Settings for commonly used external resources, for example javascripts.
//
define('JS_JQUERY', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js');


// -------------------------------------------------------------------------------------------
//
// These modules (TP_MODULESPATH) are available.
//
$gModulesAvailable = Array(
'core' => TP_MODULESPATH . 'core', // The core, always included
'rom' => TP_MODULESPATH . 'forum_romanum', // Forum Romanum, included by default
// 'dada' => TP_MODULESPATH . 'dada', // Dada, optional module
'blog' => TP_MODULESPATH . 'blog',
 // Filearchive, sample user interface to work with file uploads.
'files'    => TP_MODULESPATH . 'filearchive',
);


?>
