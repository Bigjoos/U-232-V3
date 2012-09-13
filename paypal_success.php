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
require_once (INCL_DIR.'html_functions.php');
dbconn(false);
loggedinorreturn();
$lang = array_merge(load_language('global'));
$HTMLOUT = "";
if (isset($_GET['echeck']) && $_GET['echeck'] == 1) {
    $HTMLOUT.= begin_main_frame();
    $HTMLOUT.= "<div align='center'>
<br />
<table width='80%' border='0' align='center'>
<tr><td align='center' valign='middle' class='colhead'><h1>Pending Payment!</h1></td></tr>
<tr><td align='center' valign='middle' class='one'><br />
<b>Thank you for your support {$CURUSER["username"]}!</b><br /><br />Your e-check is <font color='red'>pending</font>.
Upon confirmation of your payment you will recieve your bonus and VIP status. <br /><br />cheers,<br />{$INSTALLER09['site_name']} Staff</td></tr></table></div><br /><br /><br />";
    $HTMLOUT.= end_main_frame();
    echo stdhead('Donate').$HTMLOUT.stdfoot();
    die();
}
$HTMLOUT.= begin_main_frame();
$HTMLOUT.= "<div align='center'><br /><table width='80%' border='0' align='center'>
<tr><td align='center' valign='middle' class='colhead'><h1>Success!</h1></td></tr>
<tr><td align='center' valign='middle' class='one'><br /><b>Thank you for your support {$CURUSER["username"]}!</b><br /><br />
It's people like you that make it better for the whole community :)<br /><br />cheers,<br />{$INSTALLER09['site_name']} Staff</td></tr></table></div><br /><br /><br />";
$HTMLOUT.= end_main_frame();
echo stdhead('Donate').$HTMLOUT.stdfoot();
die();
?>
