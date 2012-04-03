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
require_once INCL_DIR.'bbcode_functions.php';
dbconn(false);
loggedinorreturn();
$lang = array_merge( load_language('global'));
$action = (isset($_GET['action']) ? $_GET['action'] : '');

$HTMLOUT ="";
$HTMLOUT .="<table class='main' width='750' border='0' cellspacing='0' cellpadding='10'>
<tr>
<td class='embedded'>
<h2 align='center'><font size='6'>Announcement History</font></h2>";

$query1 = sprintf('SELECT m.main_id, m.subject, m.body FROM announcement_main AS m '.
 	 'LEFT JOIN announcement_process AS p '.
 	'ON m.main_id = p.main_id AND p.user_id = %s '.
 	'WHERE p.status = 2',
 	sqlesc($CURUSER['id']));

$result = sql_query($query1);

$ann_list = array();

while ($x = mysqli_fetch_array($result)) $ann_list[] = $x ;
unset($x);
unset($result);
reset($ann_list);

if ($action == 'read_announce')
{
 	$id = 0 + (isset($_GET['id']) ? $_GET['id'] : 0 );
 	if (!is_int($id)) { 
 	$HTMLOUT .= stdmsg('Error','Invalid ID'); 
 	echo stdhead('Announcement History') . $HTMLOUT . stdfoot();
 	die(); 
 	}

 	foreach($ann_list AS $x)
 		 if ($x[0] == $id)
 		 list(,$subject,$body) = $x;

 	if (empty($subject) OR empty($body)) { 
 	$HTMLOUT .= stdmsg('Error','ID does not exist');  
 	echo stdhead('Announcement History') . $HTMLOUT . stdfoot();
 	die(); 
 	}

 	$HTMLOUT.="<table width='100%' border='0' cellpadding='4' cellspacing='0'>
 	<tr>
 	<td width='50%' bgcolor='orange'>Subject: <b>".htmlsafechars($subject)."</b></td>
 	</tr>
 	<tr>
 	<td colspan='2' bgcolor='#333333'>".format_comment($body)."</td>
 	</tr>
 	<tr>
 	<td>
 	<a href='".$_SERVER['PHP_SELF']."'>Back</a>
 	</td>
 	</tr>
 	</table>";
}

$HTMLOUT.="<table align='center' width='30%' border='0' cellpadding='4' cellspacing='0'>
<tr>
<td align='center' bgcolor='orange'><b>Subject</b></td>
</tr>";

foreach($ann_list AS $x)
$HTMLOUT .="<tr><td align='center'><a href='?action=read_announce&amp;id=".$x[0]."'>".htmlsafechars($x[1])."</a></td></tr>\n";
$HTMLOUT.="</table>";
$HTMLOUT.="</td></tr></table>";
echo stdhead('Announcement History') . $HTMLOUT . stdfoot();
?>
