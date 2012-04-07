<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
/**********************************************************
New 2010 forums that don't suck for TB based sites....

Powered by Bunnies!!!
***************************************************************/

if ( ! defined( 'IN_INSTALLER09_ADMIN' ) )
{
	$HTMLOUT.='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
        <head>
        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
        <title>ERROR</title>
        </head><body>
        <h1 style="text-align:center;">ERROR</h1>
        <p style="text-align:center;">How did you get here? silly rabbit Trix are for kids!.</p>
        </body></html>';
	echo $HTMLOUT;
	exit();
}

require_once(INCL_DIR.'html_functions.php');
require_once(CLASS_DIR.'class_check.php');
class_check(UC_ADMINISTRATOR);

$lang = array_merge( $lang );

$HTMLOUT = $time_drop_down = $accepted_file_extension = $accepted_file_types = $member_class_drop_down ='';

$settings_saved = false;

//=== be sure to set your id (below) in the DB. as well as setting your upload dir to something unique
$config_id = 13;

	
	if (isset($_POST['do_it'])) 
	{
	$delete_for_real = (isset($_POST['delete_for_real']) ? intval($_POST['delete_for_real']) :  0);
	$min_delete_view_class = ((isset($_POST['min_delete_view_class']) &&  valid_class($_POST['min_delete_view_class'])) ? intval($_POST['min_delete_view_class']) : 0);
	$readpost_expiry = (isset($_POST['readpost_expiry']) ? intval($_POST['readpost_expiry']) :  0);
	$min_upload_class = ((isset($_POST['min_upload_class']) &&  valid_class($_POST['min_upload_class'])) ? intval($_POST['min_upload_class']) : 0);
	$accepted_file_extension = (isset($_POST['accepted_file_extension']) ? preg_replace('/\s\s+/', ' ', $_POST['accepted_file_extension']) : ''); 
	$accepted_file_extension = explode(' ', $accepted_file_extension);	
	$accepted_file_extension = serialize($accepted_file_extension);
	$accepted_file_types = (isset($_POST['accepted_file_types']) ? preg_replace('/\s\s+/', ' ', $_POST['accepted_file_types']) : ''); 	
	$accepted_file_types = explode(' ', $accepted_file_types);	
	$accepted_file_types = serialize($accepted_file_types);
	$max_file_size = (isset($_POST['max_file_size']) ? intval($_POST['max_file_size']) :  0);
	$upload_folder = (isset($_POST['upload_folder']) ? htmlsafechars(trim($_POST['upload_folder'])) :  '');

	sql_query('UPDATE forum_config SET delete_for_real = '.sqlesc($delete_for_real).', min_delete_view_class = '.sqlesc($min_delete_view_class).', readpost_expiry = '.sqlesc($readpost_expiry).', min_upload_class = '.sqlesc($min_upload_class).', accepted_file_extension = '.sqlesc($accepted_file_extension).',  accepted_file_types = '.sqlesc($accepted_file_types).', max_file_size = '.$max_file_size.', upload_folder = '.sqlesc($upload_folder).' WHERE id = '.sqlesc($config_id));
	header('Location: staffpanel.php?tool=forum_config&action=forum_config'); 
	die();
	}
	
$main_links = '<p><a class="altlink" href="staffpanel.php?tool=over_forums&amp;action=over_forums">Over Forums</a> :: 
						<a class="altlink" href="staffpanel.php?tool=forum_manage&amp;action=forum_manage">Forum Manager</a> :: 
						<span style="font-weight: bold;">Configure Forums</span><br /></p>';


	$res = sql_query ('SELECT delete_for_real, min_delete_view_class, readpost_expiry, min_upload_class, accepted_file_extension, 
								accepted_file_types, max_file_size, upload_folder FROM forum_config WHERE id = '.sqlesc($config_id));

	$arr = mysqli_fetch_array($res);
		
	$weeks =  1;
	for ($i = 7; $i <= 365; $i = $i + 7)
	{
	$time_drop_down .= '<option class="body" value="'.$i.'"'.($arr['readpost_expiry'] == $i ? ' selected="selected"' : '').'>'.$weeks.($weeks === 1 ? ' week' : ' weeks').'</option>';
	$weeks = $weeks + 1;
	}

$accepted_file_extension1 = (!empty($arr['accepted_file_extension'])) ? unserialize($arr['accepted_file_extension']) : array();
	foreach ($accepted_file_extension1 as $x) 
	{
	$accepted_file_extension .= $x.' ';
	}
	
$accepted_file_types1 = (!empty($arr['accepted_file_types'])) ? unserialize($arr['accepted_file_types']) : array();
	foreach ($accepted_file_types1 as $x) 
	{
	$accepted_file_types .= $x.' ';
	}	
	
$HTMLOUT .=  $main_links.'<form method="post" action="staffpanel.php?tool=forum_config&amp;action=forum_config">
			<input type="hidden" name="do_it" value="1" />
		<table  border="0" cellspacing="0" cellpadding="3" align="center">
		<tr>
		    <td colspan="2" class="forum_head_dark">Edit Forums Configure</td>
		</tr>
		<tr>
		    <td align="right" class="three" valign="top"><span style="font-weight: bold;">Delete posts / topics:</span></td>
		    <td align="left" class="three">
			<input type="radio" name="delete_for_real" value="1" '.($arr['delete_for_real'] == 1 ? 'checked="checked"' : '').' /> Yes  
			<input type="radio" name="delete_for_real" value="0" '.($arr['delete_for_real'] == 0 ? 'checked="checked"' : '').' /> No<br />
			Setting this to No will give the option forselected class and above to see deleted posts and threads and decide if they should be deleted.</td>
		</tr>
		<tr>
		    <td align="right" class="three" valign="top"><span style="font-weight: bold;">Min delete class:</span></td>
		    <td align="left" class="three">
			<select name="min_delete_view_class"> '.member_class_drop_down($arr['min_delete_view_class']).'</select><br />
			Set this to the lowest member class you wish to be able to view deleted posts and threads.<br />[Default is Admin]</td>
		</tr>
		<tr>
		    <td align="right" class="three" valign="top"><span style="font-weight: bold;">Read Post Expiry:</span></td>
		    <td align="left" class="three">
			<select name="readpost_expiry"> '.$time_drop_down.'</select><br />
			All posts older then this are set as "read".<br />[Retro\'s code that is now default]</td>
		</tr>
		<tr>
		    <td align="right" class="three" valign="top"><span style="font-weight: bold;">Min upload class:</span></td>
		    <td align="left" class="three">
			<select name="min_upload_class"> '.member_class_drop_down($arr['min_upload_class']).'</select><br />
			Set this to the lowest member class you wish to give the right to add attachments to a post.</td>
		</tr>
		  <tr>
		    <td align="right"  class="three"><span style="font-weight: bold;">Accepted file ext:</span>  </td>
		    <td align="left" class="three">
			<input name="accepted_file_extension" type="text" class="text_default" size="30" maxlength="200" value="'.htmlsafechars($accepted_file_extension).'" /><br />
			Defaults are: zip and rar. Add more at your own risk! Each entry must be separated by a single space.</td>
 		 </tr>
		  <tr>
		    <td align="right"  class="three"><span style="font-weight: bold;">Accepted file types:</span>  </td>
		    <td align="left" class="three">
			<input name="accepted_file_types" type="text" class="text_default" size="30" maxlength="200" value="'.htmlsafechars($accepted_file_types).'" /><br />
			Must match the above accepted file ext\'s. Add more at your own risk! Each entry must be separated by a single space</td>
 		 </tr>
		  <tr>
		    <td align="right"  class="three"><span style="font-weight: bold;">Max file size:</span>  </td>
		    <td align="left" class="three">
			<input name="max_file_size" type="text" class="text_default" size="30" maxlength="200" value="'.intval($arr['max_file_size']).'" /><br />
			Default is 2 MBs, currently set to: '.mksize($arr['max_file_size']).'.</td>
 		 </tr>
		  <tr>
		    <td align="right"  class="three"><span style="font-weight: bold;">Upload folder:</span>  </td>
		    <td align="left" class="three">
			<input name="upload_folder" type="text" class="text_default" size="30" maxlength="200" value="'.htmlsafechars($arr['upload_folder']).'" /><br />
			Must be writable, and must be followed by a forward slash.<br />
			Name this something unique for added security, the location is never visible to members.</td>
 		 </tr>
		<tr>
		    <td colspan="2" class="three" align="center">
			<input type="submit" name="button" class="button_big" value="Save Configuration Settings" onmouseover="this.className=\'button_big_hover\'" onmouseout="this.className=\'button_big\'" /></td>
		</tr>
		</table></form>';
	
  function member_class_drop_down($member_class)
  {
	$member_class_drop_down ='';
	for ($i = 0; $i <= UC_MAX; ++$i)
	{
	$member_class_drop_down .= '<option class="body" value="'.$i.'"'.($member_class == $i ? ' selected="selected"' : '').'>'.get_user_class_name($i).'</option>';
	}
return $member_class_drop_down;
}
	
echo stdhead('Configure Forums') . $HTMLOUT . stdfoot();
?>
