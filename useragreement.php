<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once(INCL_DIR.'user_functions.php');
require_once(INCL_DIR.'html_functions.php');


dbconn();
    
    $lang = array_merge( load_language('global'), load_language('useragreement') );
    
    $HTMLOUT = '';
    
    $HTMLOUT .= begin_main_frame();
    $HTMLOUT .= begin_frame($INSTALLER09['site_name']." {$lang['frame_usragrmnt']}");

    $HTMLOUT .= "<p></p> {$lang['text_usragrmnt']}"; 

    $HTMLOUT .= end_frame();
    $HTMLOUT .= end_main_frame();
    echo stdhead("{$lang['stdhead_usragrmnt']}") . $HTMLOUT . stdfoot();
?>
