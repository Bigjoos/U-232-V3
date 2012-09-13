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
require_once INCL_DIR.'html_functions.php';
require_once INCL_DIR.'bbcode_functions.php';
require_once CLASS_DIR.'page_verify.php';
dbconn(false);
loggedinorreturn();
$lang = array_merge(load_language('global') , load_language('upload'));
if (function_exists('parked')) parked();
$newpage = new page_verify();
$newpage->create('tamud');
$HTMLOUT = '';
if ($CURUSER['class'] < UC_UPLOADER OR $CURUSER["uploadpos"] == 0 || $CURUSER["uploadpos"] > 1 || $CURUSER['suspended'] == 'yes') stderr($lang['upload_sorry'], $lang['upload_no_auth']);
$s = '';
$cats = genrelist();
foreach ($cats as $row) {
    $s.= "<option value='{$row["id"]}'>".htmlspecialchars($row["name"])."</option>\n";
}
$HTMLOUT.= "<div align='center'>
<form enctype='multipart/form-data' action='takemultiupload.php' method='post'>
<input type='hidden' name='MAX_FILE_SIZE' value='{$INSTALLER09['max_torrent_size']}' />
<p>{$lang['upload_announce_url']}<b><input type=\"text\" size=\"38\" readonly=\"readonly\" value=\"{$INSTALLER09['announce_urls'][0]}\" onclick=\"select()\" /></b></p>
<p><strong><font color='#FF0000'>Remember : You must add an NFO for all the torrents AND redownload all 5 .torrent file's!</font></strong></p>
<table class='table'  cellspacing='0' cellpadding='5'>
<tr><td class='heading' valign='top' align='right'>Torrent#1</td><td valign='top' align='left'>&nbsp;&nbsp;&nbsp;File: <input type='file' name='file1' size='50' /><br /><br />&nbsp;&nbsp;&nbsp;NFO:<input type='file' name='nfo1' size='50' /><br /><br />&nbsp;&nbsp;Type:<select name='type1'>\n<option value='0'>(choose type)</option>{$s}</select>&nbsp;&nbsp;Anonymous upload:<input type='checkbox' name='uplver1' value='yes' />&nbsp;&nbsp;Vip torrent:<input type='checkbox' name='vip1' value='1' /></td></tr>
<tr><td class='heading' valign='top' align='right'>Torrent#2</td><td valign='top' align='left'>&nbsp;&nbsp;&nbsp;File: <input type='file' name='file2' size='50' /><br /><br />&nbsp;&nbsp;&nbsp;NFO:<input type='file' name='nfo2' size='50' /><br /><br />&nbsp;&nbsp;Type:<select name='type2'>\n<option value='0'>(choose type)</option>{$s}</select>&nbsp;&nbsp;Anonymous upload:<input type='checkbox' name='uplver2' value='yes' />&nbsp;&nbsp;Vip torrent:<input type='checkbox' name='vip2' value='1' /></td></tr>
<tr><td class='heading' valign='top' align='right'>Torrent#3</td><td valign='top' align='left'>&nbsp;&nbsp;&nbsp;File: <input type='file' name='file3' size='50' /><br /><br />&nbsp;&nbsp;&nbsp;NFO:<input type='file' name='nfo3' size='50' /><br /><br />&nbsp;&nbsp;Type:<select name='type3'>\n<option value='0'>(choose type)</option>{$s}</select>&nbsp;&nbsp;Anonymous upload:<input type='checkbox' name='uplver3' value='yes' />&nbsp;&nbsp;Vip torrent:<input type='checkbox' name='vip3' value='1' /></td></tr>
<tr><td class='heading' valign='top' align='right'>Torrent#4</td><td valign='top' align='left'>&nbsp;&nbsp;&nbsp;File: <input type='file' name='file4' size='50' /><br /><br />&nbsp;&nbsp;&nbsp;NFO:<input type='file' name='nfo4' size='50' /><br /><br />&nbsp;&nbsp;Type:<select name='type4'>\n<option value='0'>(choose type)</option>{$s}</select>&nbsp;&nbsp;Anonymous upload:<input type='checkbox' name='uplver4' value='yes' />&nbsp;&nbsp;Vip torrent:<input type='checkbox' name='vip4' value='1' /></td></tr>
<tr><td class='heading' valign='top' align='right'>Torrent#3</td><td valign='top' align='left'>&nbsp;&nbsp;&nbsp;File: <input type='file' name='file5' size='50' /><br /><br />&nbsp;&nbsp;&nbsp;NFO:<input type='file' name='nfo5' size='50' /><br /><br />&nbsp;&nbsp;Type:<select name='type5'>\n<option value='0'>(choose type)</option>{$s}</select>&nbsp;&nbsp;Anonymous upload:<input type='checkbox' name='uplver5' value='yes' />&nbsp;&nbsp;Vip torrent:<input type='checkbox' name='vip5' value='1' /></td></tr>
<tr>
<td class='rowhead' style='padding: 3px'><b>Settings</b></td>
<td>These settings will apply to all above torrents.
<br />Please note: Torrent names are taken from their .torrent filenames. 
<br />Use descriptive names in .torrent files.
<br /> 
<br />If you forget to specify a torrent category type in any of the above it will use the one from below.
<br />
<br />
<select name='alltype'>
<option value='0'>(choose type)</option>{$s}</select>
<input type='checkbox' name='custom' />Custom message
<br /> 
<textarea name='description'  rows='6' cols='60'>See NFO</textarea>
</td>
</tr>
<tr>
<td align='center' colspan='2'>
<input type='submit' class='btn' value='Do it!' /></td></tr>
</table>
</form></div>";
echo stdhead("Multi-Upload").$HTMLOUT.stdfoot();
?>
