<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
//=== over forum manager by Retro, but newer \o/ march 2010
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
$HTMLOUT = $over_forums = $count = $min_class_viewer = $sorted = '';

$main_links = '<p><span style="font-weight: bold;">Over Forums</span> :: 
						<a class="altlink" href="staffpanel.php?tool=forum_manage&amp;action=forum_manage">Forum Manager</a> :: 
						<a class="altlink" href="staffpanel.php?tool=forum_config&amp;action=forum_config">Configure Forums</a><br /></p>';

$id = (isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0));
$maxclass = $CURUSER['class'];
$name = strip_tags(isset($_POST['name']) ? $_POST['name'] : '');
$desc = strip_tags(isset($_POST['desc']) ? $_POST['desc'] : '');
$sort = (isset($_POST['sort']) ? intval($_POST['sort']) : 0);
$min_class_view = (isset($_POST['min_class_view']) ? intval($_POST['min_class_view']) : 0);

	//=== post / get action posted so we know what to do :P
	$posted_action = (isset($_GET['action2']) ? $_GET['action2'] : (isset($_POST['action2']) ? $_POST['action2'] : ''));
	
	//=== add all possible actions here and check them to be sure they are ok
	$valid_actions = array('delete', 'edit_forum', 'add_forum', 'edit_forum_page');
	
	$action = (in_array($posted_action, $valid_actions) ? $posted_action : 'forum');

//=== here we go with all the possibilities \\o\o/o//
	switch ($action)
	{
	//=== delete over forum
	case 'delete':
	
		if (!$id) 
		{ 
		stderr('ERROR', 'Bad ID'); 
		}

		sql_query ('DELETE FROM over_forums where id = '.sqlesc($id));
		header('Location: staffpanel.php?tool=over_forums');
		die();
				
	break;

	//=== edit forum
	case 'edit_forum':	

			if (!$name && !$desc && !$id) 
			{ 
			stderr('ERROR', 'Missing form data!'); 
			}
			
			$res = sql_query ('SELECT sort FROM over_forums WHERE sort = '.sqlesc($sort));
			
			if (mysqli_num_rows($res) > 0)
			{
			stderr('ERROR', 'Over forum Sort number in use. Please select another Over forum Sort number!'); 
			}

			sql_query('UPDATE over_forums SET sort = '.sqlesc($sort).', name = '.sqlesc($name).', description = '.sqlesc($desc).', min_class_view = '.sqlesc($min_class_view).' WHERE id = '.sqlesc($id));
		  header('Location: staffpanel.php?tool=over_forums');
			die();
	
	break;
	
	//=== add forum
	case 'add_forum':	

			if (!$name && !$desc) 
			{ 
			stderr('ERROR', 'Missing form data'); 
			}
			
			$res = sql_query ('SELECT sort FROM over_forums WHERE sort = '.sqlesc($sort));
			
			if (mysqli_num_rows($res) > 0)
			{
			stderr('ERROR', 'Over forum Sort number in use. Please select another Over forum Sort number!'); 
			}
			
			sql_query('INSERT INTO over_forums (sort, name,  description,  min_class_view) VALUES ('.sqlesc($sort).', '.sqlesc($name).', '.sqlesc($desc).', '.sqlesc($min_class_view).')');
			header('Location: staffpanel.php?tool=over_forums');
			die();
	
	break;
	
	//=== edit over forum stuff
	case 'edit_forum_page':
	
	$res = sql_query ('SELECT * FROM over_forums where id ='.sqlesc($id));
	
	if (mysqli_num_rows($res) > 0) 
	{

		$row = mysqli_fetch_array($res);

$HTMLOUT .=  $main_links.'<form method="post" action="staffpanel.php?tool=over_forums&amp;action=over_forums">
			<input type="hidden" name="action2" value="edit_forum">
			<input type="hidden" name="id" value="'.$id.'">
		<table width="600"  border="0" cellspacing="0" cellpadding="3" align="center">
		<tr>
		    <td colspan="2" class="forum_head_dark">edit overforum: '.htmlsafechars($row['name'], ENT_QUOTES).'</td>
		  </tr>
		    <td align="right" class="three"><span style="font-weight: bold;">Overforum name:</span></td>
		    <td align="left" class="three"><input name="name" type="text" class="text_default" size="20" maxlength="60" value="'.htmlsafechars($row['name'], ENT_QUOTES).'" /></td>
		  </tr>
		  <tr>
		    <td align="right"  class="three"><span style="font-weight: bold;">Overforum description:</span>  </td>
		    <td align="left" class="three"><input name="desc" type="text" class="text_default" size="30" maxlength="200" value="'.htmlsafechars($row['description'], ENT_QUOTES).'" /></td>
 		 </tr>
		    <tr>
		    <td align="right" class="three"><span style="font-weight: bold;">Minimun view permission: </span></td>
		    <td align="left" class="three">
		    <select name="min_class_view">';

	for ($i = 0; $i <= $maxclass; ++$i)
	{
	$over_forums .= '<option class="body" value="'.$i.'"'.($row['min_class_view'] == $i ? ' selected="selected"' : '') .'>'.get_user_class_name($i).'</option>';
	}

$HTMLOUT .= $over_forums.'</select></td></tr><tr>
		    <td align="right" class="three"><span style="font-weight: bold;">Over forum Sort:</span></td>
		    <td align="left" class="three">
		    <select name="sort">';

		$res = mysqli_query($GLOBALS["___mysqli_ston"], 'SELECT sort FROM over_forums');
		$nr = mysqli_num_rows($res);
		$maxclass = $nr + 1;
			
			for ($i = 0; $i <= $maxclass; ++$i)
			{
			$sorted .= '<option class="body" value="'.$i.'"'.($row['sort'] == $i ? ' selected="selected"' : '').'>'.$i.'</option>';
			}

$HTMLOUT .= $sorted.'</select></td></tr>
			<tr>
			    <td colspan="2" class="three" align="center">
				<input type="submit" name="button" class="button" value="Edit overforum" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" />
			    </td>
		  </tr>
		</table></form>';
}
	break;

	//=== over forum stuff
	case 'forum':

$HTMLOUT .=  $main_links.'<table width="750"  border="0" align="center" cellpadding="2" cellspacing="0">
		<tr><td class="forum_head_dark" align="center">Sort</td>
			<td class="forum_head_dark" align="left">Name</td>
			<td class="forum_head_dark" align="center">Minimun Class View</td>
			<td class="forum_head_dark" align="center">Modify</td>
		</tr>';
		
	$res = sql_query ('SELECT * FROM over_forums ORDER BY sort ASC');

	if (mysqli_num_rows($res) > 0) 
	{
		while($row = mysqli_fetch_array($res))
		{
		//=== change colors
		$count= (++$count)%2;
		$class = ($count == 0 ? 'one' : 'two');	
		
	$over_forums .= '<tr>
			<td class="'.$class.'" align="center">'.(int)$row['sort'].'</td>
			<td class="'.$class.'">
			<a class="altlink" href="forums.php?action=forum_view&amp;fourm_id='.(int)$row['id'].'">'.htmlsafechars($row['name'], ENT_QUOTES).'</a><br />
			'.htmlsafechars($row['description'], ENT_QUOTES).'</td>
			<td class="'.$class.'" align="center">'. get_user_class_name($row['min_class_view']).'</td>
			<td align="center" class="'.$class.'">
			<a class="altlink" href="staffpanel.php?tool=over_forums&amp;action=over_forums&amp;action2=edit_forum_page&amp;id='.(int)$row['id'].'">Edit</a>&nbsp;|&nbsp;
			<a href="javascript:confirm_delete(\''.(int)$row['id'].'\');"><span style="font-weight: bold;">Delete</span></a></td>
			</tr>';
		} //=== end while	
	}//=== end if
	
$HTMLOUT .= $over_forums. '</table><br /><br />
			<form method="post" action="staffpanel.php?tool=over_forums&amp;action=over_forums">
			<input type="hidden" name="action2" value="add_forum" />
			<table width="600"  border="0" cellspacing="0" cellpadding="3" align="center">
			<tr align="center">
 			   <td colspan="2" class="forum_head_dark">Make new over forum</td>
			  </tr>
			  <tr>
			    <td align="right" class="three"><span style="font-weight: bold;">Overforum name:</span></td>
			    <td align="left" class="three"><input name="name" type="text" class="text_default" size="20" maxlength="60" /></td>
			  </tr>
			  <tr>
 			   <td align="right" class="three"><span style="font-weight: bold;">Overforum description:</span>  </td>
			    <td align="left" class="three"><input name="desc" type="text" class="text_default" size="30" maxlength="200" /></td>
			  </tr>
			<tr>
			    <td align="right" class="three"><span style="font-weight: bold;">Minimun view permission:</span> </td>
			    <td align="left" class="three">
 			   <select name="min_class_view">';


			for ($i = 0; $i <= $maxclass; ++$i)
			{
			$min_class_viewer .= '<option class="body" value="'.$i.'">'.get_user_class_name($i).'</option>';
			}

$HTMLOUT .= $min_class_viewer.'</select>
			</td>
			</tr>
			<tr>
			<td align="right" class="three"><span style="font-weight: bold;">Overforum sort:</span> </td>
			<td align="left" class="three">
			<select name="sort">';

				$res = sql_query ('SELECT sort FROM over_forums');
				$nr = mysqli_num_rows($res);
				$maxclass = $nr + 1;
					for ($i = 0; $i <= $maxclass; ++$i)
					{
					$sorted .= '<option class="body" value="'.$i.'">'.$i.'</option>';
					}

$HTMLOUT .= $sorted.'</select></td></tr>
			 <tr>
			<td colspan="2" class="three" align="center">
			<input type="submit" name="button" class="button" value="Make overforum" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" /></td>
			</tr>
			</table></form>';
 	break;
} //=== end switch

$HTMLOUT .= '<script type="text/javascript">
			<!--
			function confirm_delete(id)
			{
			   if(confirm(\'Are you sure you want to delete this overforum?\'))
			   {
			      self.location.href=\'staffpanel.php?tool=over_forums&action=over_forums&action2=delete&id=\'+id;
			   }
			}
		//-->
	</script>';

echo stdhead('Over Forum Manage') . $HTMLOUT . stdfoot();
?>
