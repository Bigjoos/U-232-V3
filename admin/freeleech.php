<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
/** freeleech mod by pdq for TBDev.net 2009**/
if ( ! defined( 'IN_INSTALLER09_ADMIN' ) )
{
	$HTMLOUT='';
	$HTMLOUT .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
		\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<title>Error!</title>
		</head>
		<body>
	<div style='font-size:33px;color:white;background-color:red;text-align:center;'>Incorrect access<br />You cannot access this file directly.</div>
	</body></html>";
	echo $HTMLOUT;
	exit();
}

require_once(INCL_DIR.'user_functions.php');
require_once(CLASS_DIR.'class_check.php');
class_check(UC_ADMINISTRATOR);

$HTMLOUT = '';

if (isset($_GET['remove']))
{
    $configfile = "<"."?php\n\n/**\nThis file created on ".date('M d Y H:i:s').".\nfreeleech mod by pdq for TBDev.net 2009.\n**/\n\n\$free = array(\n";
    $configfile .= "array('modifier'=> 0, 'expires'=> 'Inf.', 'setby'=> 'No One', 'title'=> 'Add title', 'message'=> 'Add Message')";

    $configfile .= "\n);\n\n?".">";
    $filenum = fopen('cache/free_cache.php', 'w');
    ftruncate($filenum, 0);
    fwrite($filenum, $configfile);
    fclose($filenum);
    header("Location: {$INSTALLER09['baseurl']}/staffpanel.php?tool=freeleech");
    die;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $configfile = "<"."?php\n\n/**\nThis file created on ".date('M d Y H:i:s').".\nfreeleech mod by pdq for TBDev.net 2009.\n**/\n\n\$free = array(\n";


    $fl['modifier'] = (isset($_POST['modifier']) ? (int)$_POST['modifier'] : false);
    if (isset($_POST['expires']) && $_POST['expires'] == 255)
    $fl['expires'] = 1;
    else
    $fl['expires'] = (isset($_POST['expires']) ? ($_POST['expires'] * 86400 + TIME_NOW) : false);
    $fl['setby'] = (isset($_POST['setby']) ? htmlsafechars($_POST['setby']) : false);
    $fl['title'] = (isset($_POST['title']) ? htmlsafechars($_POST['title']) : false);
    $fl['message'] = (isset($_POST['message']) ? htmlsafechars($_POST['message']) : false);

    //echo_r($fl);
    if ($fl['modifier'] === false || $fl['expires'] === false || $fl['setby'] === false ||
        $fl['title'] === false || $fl['message'] === false)
    {
        echo 'Error Complete the Form.';
        die;
    }
    $configfile .= "array('modifier'=> {$fl['modifier']}, 'expires'=> {$fl['expires']}, 'setby'=> '{$fl['setby']}', 'title'=> '{$fl['title']}', 'message'=> '{$fl['message']}')";


    $configfile .= "\n);\n\n?".">";
    $filenum = fopen('cache/free_cache.php', 'w');
    ftruncate($filenum, 0);
    fwrite($filenum, $configfile);
    fclose($filenum);
    header("Location: {$INSTALLER09['baseurl']}/staffpanel.php?tool=freeleech");
    die;
}

require_once(CACHE_DIR.'free_cache.php');

if (isset($free) && (count($free) < 1))
{
    $HTMLOUT .= '<h1>Current Freeleech Status</h1>
                 <p align="center"><b>Nothing found</b></p>';
} else
{
    $HTMLOUT .= "<br /><table border='1' cellspacing='0' cellpadding='5'>
        <tr><td class='colhead' align='left'>Free All Torrents</td>
		<td class='colhead' align='left'>Expires</td>
        <td class='colhead' align='left'>Set By</td>
		<td class='colhead' align='left'>Title</td>
		<td class='colhead' align='left'>Message</td>
		<td class='colhead' align='left'>Remove</td></tr>";

    $checked1 = $checked2 = $checked3 = $checked4 = '';
    foreach ($free as $fl)
    {

        switch ($fl['modifier'])
        {
            case 1:
                $checked1 = 'checked=\'checked\'';
                $mode = 'All Torrents Free';
                break;

            case 2:
                $mode = 'All Double Upload';
                $checked2 = 'checked=\'checked\'';
                break;

            case 3:
                $mode = 'All Torrents Free and Double Upload';
                $checked3 = 'checked=\'checked\'';
                break;

            case 4:
                $mode = 'All Torrents Silver';
                $checked4 = 'checked=\'checked\'';
                break;

            default:
                $mode = 'Not Enabled';
        }

        $HTMLOUT .= "<tr><td>$mode
		     </td><td align='left'>".($fl['expires'] != 'Inf.' && $fl['expires'] != 1 ? "Until ".get_date($fl['expires'],
            'DATE')." (".mkprettytime($fl['expires'] - TIME_NOW)." to go)" : 'Unlimited').
            " </td>
			 <td align='left'>{$fl['setby']}</td>
			 <td align='left'>{$fl['title']}</td>
			 <td align='left'>{$fl['message']}</td>
		     <td><a href='staffpanel.php?tool=freeleech&amp;action=freeleech&amp;remove'>Remove</a>
			 </td></tr>";
    }
    $HTMLOUT .= '</table>';

}
$checked = 'checked=\'checked\'';
$HTMLOUT .= "<h2>Set Freeleech</h2>
	<form method='post' action='staffpanel.php?tool=freeleech&amp;action=freeleech'>
	<table border='1' cellspacing='0' cellpadding='5'>
	<tr><td class='rowhead'>Mode</td>
	<td> <table width='100%'>
 <tr>
 <td align='left'>All Torrents Free</td>
 <td><input name=\"modifier\" type=\"radio\" $checked1 value=\"1\" /></td>
 </tr>
 <tr>
 <td>All Torrents Double Upload</td>
 <td><input name=\"modifier\" type=\"radio\" $checked2 value=\"2\" /></td>
 </tr>
 <tr>
 <td >All Torrents Free and Double Upload</td>
 <td><input name=\"modifier\" type=\"radio\" $checked3 value=\"3\" /></td></tr>
 <tr>
 <td >All Torrents Silver</td>
 <td><input name=\"modifier\" type=\"radio\" $checked4 value=\"4\" /></td></tr>
 </table>
    </td></tr>
	<tr><td class='rowhead'>Expires in
	</td><td>
	<select name='expires'>
    <option value='1'>1 day</option>
    <option value='2'>2 days</option>
    <option value='5'>5 days</option>
    <option value='7'>7 days</option>
    <option value='255'>Unlimited</option>
    </select></td></tr>
	<tr><td class='rowhead'>Title</td>
	<td><input type='text' size='40' name='title' value='{$fl['title']}' />
	</td></tr>
		<tr><td class='rowhead'>Message</td>
	<td><input type='text' size='40' name='message' value='{$fl['message']}' />
	</td></tr>
			<tr><td class='rowhead'>Set By</td>
	<td><input type='text' size='40' value ='".$CURUSER['username'].
    "' name='setby' />
	</td></tr>
	<tr><td colspan='2' align='center'>
	<input type='submit' name='okay' value='Do it!' class='btn' />
	</td></tr>
	<tr><td colspan='2' align='center'>
	<input type='hidden' name='cacheit' value='Cache' class='btn' />
	</td></tr>
	</table></form>";

    echo stdhead('Freeleech Status') . $HTMLOUT . stdfoot();
    die;

?>
