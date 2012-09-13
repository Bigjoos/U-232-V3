<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 *
 */
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once (INCL_DIR.'user_functions.php');
//require_once(CLASS_DIR.'class_check.php');
//class_check(UC_SYSOP);
dbconn();
loggedinorreturn();
$lang = array_merge(load_language('global'));
$HTMLOUT = '';
$allowed_ids = array(
    1
); //== 1 Is Sysop - add userids you want access
if (!in_array($CURUSER['id'], $allowed_ids)) stderr('Error', 'Access Denied!');
if ($INSTALLER09['staff_viewcode_on'] == true) {
    $THIS_FILE = "view.php";
    if (isset($_GET["file"])) {
        $file = $_GET["file"];
    } else {
        $file = $THIS_FILE;
    }
    // Changed from a sanitization approach to just quitting on match.
    if (!preg_match('/\.\.\//', $file)) {
        $fullFilename = "C:\webdev/htdocs/$file";
        /** CHANGE THIS LINE TO YOUR PATH **/
    }
    $path_parts = pathinfo("$fullFilename");
    if (isset($fullFilename) && is_file($fullFilename) && is_readable($fullFilename) && $path_parts["extension"] == "php") {
        $HTMLOUT.= '<div class="source">';
        $HTMLOUT.= show_source($fullFilename);
        $HTMLOUT.= '</div>';
    } elseif (!isset($fullFilename)) {
        $HTMLOUT.= '<p>Hey, wise guy!  What do you think youre doing?  Tampering with the filename is not allowed.  Go hack somebody elses web server, and leave me alone.  Punk.</p>';
    } elseif ($path_parts["extension"] != "php") {
        $HTMLOUT.= '<p>Whoops!  You can only view the source of files with a .php extension.  It wouldnt make sense to view the source of, say, an image, now would it?</p>';
    }
} else {
    $HTMLOUT = '';
    $HTMLOUT.= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
    \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
    <html xmlns='http://www.w3.org/1999/xhtml'>
    <head>
    <title>Sorry</title>
    </head>
    <body><div style='font-size:33px;color:white;background-color:red;text-align:center;'>View source code option disabled currently !!</div></body></html>";
    echo $HTMLOUT;
    exit();
}
echo $HTMLOUT;
?>
