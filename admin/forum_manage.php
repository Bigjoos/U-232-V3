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

Beta Thurs Sept 9th 2010 v0.5

//===  forum manager by Retro, but newer \o/ march 2010
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

$HTMLOUT = $options = $options_2 = $options_3 = $options_4 = $options_5 = $options_6 = $option_7 = $option_8 = $option_9 = $option_10 = $option_11  = $count = $forums_stuff  = '';
$row=0;

//=== defaults:
$maxclass = $CURUSER['class'];
$id = (isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0));
$name = strip_tags(isset($_POST['name']) ? $_POST['name'] : '');
$desc = strip_tags(isset($_POST['desc']) ? $_POST['desc'] : '');
$sort = (isset($_POST['sort']) ? intval($_POST['sort']) : 0);
$parent_forum = (isset($_POST['parent_forum']) ? intval($_POST['parent_forum']) : 0);
$over_forums = (isset($_POST['over_forums']) ? intval($_POST['over_forums']) : 0);
$min_class_read = (isset($_POST['min_class_read']) ? intval($_POST['min_class_read']) : 0);
$min_class_write = (isset($_POST['min_class_write']) ? intval($_POST['min_class_write']) : 0);
$min_class_create = (isset($_POST['min_class_create']) ? intval($_POST['min_class_create']) : 0);	

$main_links = '<p><a class="altlink" href="staffpanel.php?tool=over_forums&amp;action=over_forums">Over Forums</a> :: 
						<span style="font-weight: bold;">Forum Manager</span> :: 
						<a class="altlink" href="staffpanel.php?tool=forum_config&amp;action=forum_config">Configure Forums</a><br /></p>';
	
 
	//=== post / get action posted so we know what to do :P
	$posted_action = (isset($_GET['action2']) ? $_GET['action2'] : (isset($_POST['action2']) ? $_POST['action2'] : ''));
	
	//=== add all possible actions here and check them to be sure they are ok
	$valid_actions = array('delete', 'edit_forum', 'add_forum', 'edit_forum_page');
	
	$action = (in_array($posted_action, $valid_actions) ? $posted_action : 'no_action');

//=== here we go with all the possibilities \\o\o/o//
	switch ($action)
	{
	//=== delete forums
	case 'delete':
	
		if (!$id) 
		{ 
		header('Location: staffpanel.php?tool=forum_manage&action=forum_manage'); 
		die();
		}

			$res = sql_query ('SELECT * FROM topics where forum_id = '.$id);
			$row = mysqli_fetch_array($res);
				sql_query ('DELETE FROM posts where topic_id ='.$row['id']);
				sql_query ('DELETE FROM topics where forum_id = '.$id);
				sql_query ('DELETE FROM forums where id = '.$id);

				header('Location: staffpanel.php?tool=forum_manage&action=forum_manage'); 
				die();
	break;
	
	//=== edit forum
	case 'edit_forum':	

			if (!$name && !$desc && !$id) 
			{ 
			header('Location: staffpanel.php?tool=forum_manage&action=forum_manage'); 
			die();
			}

			sql_query('UPDATE forums SET sort = '.$sort.', name = '.sqlesc($name).', parent_forum = '.$parent_forum.', description = '.sqlesc($desc).', forum_id = '.$over_forums.', min_class_read = '.$min_class_read.', min_class_write = '.$min_class_write.', min_class_create = '.$min_class_create.' where id = '.$id);
			header('Location: staffpanel.php?tool=forum_manage&action=forum_manage'); 
			die();
	
	break;
	
	//=== add forum
	case 'add_forum':	

			if (!$name && !$desc) 
			{ 
			header('Location: staffpanel.php?tool=forum_manage&action=forum_manage'); 
			die();
			}

		sql_query('INSERT INTO forums (sort, name, parent_forum, description,  min_class_read,  min_class_write, min_class_create, forum_id) VALUES ('.$sort.', '.sqlesc($name).', '.$parent_forum.', '.sqlesc($desc).', '.$min_class_read.', '.$min_class_write.', '.$min_class_create.', '.$over_forums.')');
		header('Location: staffpanel.php?tool=forum_manage&action=forum_manage'); 
		die();

	break;
	
	//=== edit forum stuff
	case 'edit_forum_page':

	$res = sql_query ('SELECT * FROM forums where id = '.$id);
	
		if (mysqli_num_rows($res) > 0)
				{
				$row = mysqli_fetch_array($res);
				
		$HTMLOUT .= $main_links.'<form method="post" action="staffpanel.php?tool=forum_manage&amp;action=forum_manage">
					<table  border="0" cellspacing="0" cellpadding="3" align="center">
					<tr>
					<td colspan="2" class="forum_head_dark">Edit forum: '.htmlentities($row['name'], ENT_QUOTES).'</td>
					</tr>
					<tr>
					<td align="right" class="three"><span style="font-weight: bold;">Forum name:</span></td>
					<td align="left" class="three"><input name="name" type="text" class="text_default" size="20" maxlength="60" value="'.htmlentities($row['name'], ENT_QUOTES).'" /></td>
					</tr>
					<tr>
					<td align="right" class="three"><span style="font-weight: bold;">Forum description:</span></td>
					<td align="left" class="three"><input name="desc" type="text" class="text_default" size="30" maxlength="200" value="'.htmlentities($row['description'], ENT_QUOTES).'" /></td>
					</tr>
					<tr>
					<td align="right" class="three"><span style="font-weight: bold;">OverForum:</span></td>
					<td  align="left" class="three">
					<select name="over_forums">';
					
		$forum_id = $row['forum_id'];
		$res = sql_query('SELECT * FROM over_forums');

			while ($arr = mysqli_fetch_array($res)) 
			{
			$i = $arr['id'];
			$options .= '<option class="body" value="'.$i.'"'.($forum_id == $i ? ' selected="selected"' : '').'>'.htmlentities($arr['name'], ENT_QUOTES).'</option>';
			}
					
	$HTMLOUT .= $options.'</select></td></tr>
				<tr>
				<td align="right" class="three"><span style="font-weight: bold;">Sub-Forum of? </span></td>
				<td align="left" class="three">
				<select name="parent_forum">
				<option class="body" value="0"'.($parent_forum == 0 ? ' selected="selected"' : '').'> - select parent forum if sub-forum</option>';
            
            $res = sql_query('SELECT name, id FROM forums');
			
			while ($arr = mysqli_fetch_array($res)) 
			{
			 if (is_valid_id($arr['id']))
			$options_2 .= '<option class="body" value="'.$arr['id'].'"'.($parent_forum == $arr['id'] ? ' selected="selected"' : '').'>'.htmlentities($arr['name'], ENT_QUOTES).'</option>';
			}

	$HTMLOUT .= $options_2.'</select></td></tr>
				<tr>
				<td align="right" class="three"><span style="font-weight: bold;">Minimun read permission:</span></td>
				<td  align="left" class="three">
				<select name="min_class_read">';

	for ($i = 0; $i <= $maxclass; ++$i)
	{
        $options_3 .= '<option class="body" value="'.$i.'"'.($row['min_class_read'] == $i ? ' selected="selected"' : '').'>'.get_user_class_name($i).'</option>';
        }

 $HTMLOUT .= $options_3.'</select></td></tr><tr>
    			<td align="right" class="three"><span style="font-weight: bold;">Minimun write permission:</span></td>
    			<td align="left" class="three"><select name="min_class_write">';

          for ($i = 0; $i <= $maxclass; ++$i)
          {
          $options_4 .= '<option class="body" value="'.$i.'"'.($row['min_class_write'] == $i ? ' selected="selected"' : '').'>'.get_user_class_name($i).'</option>';
          }

$HTMLOUT .= $options_4.'</select></td></tr><tr>
			<td align="right" class="three"><span style="font-weight: bold;">Minimun create topic permission:</span></td>
			<td align="left" class="three"><select name="min_class_create">';


          for ($i = 0; $i <= $maxclass; ++$i)
          {
          $options_5 .= '<option class="body" value="'.$i.'"'.($row['min_class_create'] == $i ? ' selected="selected"' : '').'>'.get_user_class_name($i).'</option>';
          }
          
$HTMLOUT .= $options_5.'</select></td></tr><tr>
			<td align="right" class="three"><span style="font-weight: bold;">Forum rank:</span> </td>
			<td align="left" class="three">
			<select name="sort">';

	$res = sql_query ('SELECT sort FROM forums');
	$nr = mysqli_num_rows($res);
        $maxclass = $nr + 1;
        	for ($i = 0; $i <= $maxclass; ++$i)
        	{
            	$options_6 .= '<option class="body" value="'.$i.'"'.($row['sort'] == $i ? ' selected="selected"' : '').'>'.$i.'</option>'; 
			}				
					
					
$HTMLOUT .= $options_6. '</select></td></tr>
			<tr>
			<td colspan="2" align="center" class="three">
			<input type="hidden" name="action2" value="edit_forum" />
			<input type="hidden" name="id" value="'.$id.'" />
			<input type="submit" name="button" class="button" value="Edit forum" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" />
			</td>
			</tr></table></form><br /><br />';
				}
	break;
	} //=== end switch
	
//=== basic page
$HTMLOUT .= $main_links.'<table width="750"  border="0" align="center" cellpadding="2" cellspacing="0">
		<tr><td class="forum_head_dark" align="left">Name</td>
		<td class="forum_head_dark" align="center">Sub-Forum of</td>
		<td class="forum_head_dark" align="center">OverForum</td>
		<td class="forum_head_dark" align="center">Read</td>
		<td class="forum_head_dark" align="center">Write</td>
		<td class="forum_head_dark" align="center">Create topic</td>
		<td class="forum_head_dark" align="center">Modify</td></tr>';
		
	$res = sql_query('SELECT * FROM forums ORDER BY forum_id ASC');
	
	if (mysqli_num_rows($res) > 0) 
	{
	while($row = mysqli_fetch_array($res))
	{
	
	$forum_id = $row['forum_id'];
		
		$res2 = sql_query('SELECT name FROM over_forums WHERE id='.$forum_id);
		$arr2 = mysqli_fetch_assoc($res2);

			$name = htmlentities($arr2['name'], ENT_QUOTES);
			$subforum = $row['parent_forum'];

				if ($subforum)
				{
				$res3 = sql_query('SELECT name FROM forums WHERE id='.$subforum);
				$arr3 = mysqli_fetch_assoc($res3);
				$subforum_name = htmlentities($arr3['name'], ENT_QUOTES);
				}
				else
				{
				$subforum_name = '';
				}
				
	//=== change colors
	$count= (++$count)%2;
	$class = ($count == 0 ? 'one' : 'two');
		
$HTMLOUT .= '<tr><td class="'.$class.'"><a class="altlink" href="forums.php?action=view_forum&amp;forum_id='.$row['id'].'">
			<span style="font-weight: bold;">'.htmlentities($row['name'], ENT_QUOTES).'</span></a><br />
			'.htmlentities($row['description'], ENT_QUOTES).'</td>
			<td class="'.$class.'" align="center"><span style="font-weight: bold;">'.$subforum_name.'</span></td>
			<td class="'.$class.'" align="center">'.$name.'</td>
			<td class="'.$class.'" align="center">'.get_user_class_name($row['min_class_read']).'</td>
			<td class="'.$class.'" align="center">'.get_user_class_name($row['min_class_write']).'</td>
			<td class="'.$class.'" align="center">'.get_user_class_name($row['min_class_create']).'</td>
			<td align="center" class="'.$class.'"><a href="staffpanel.php?tool=forum_manage&amp;action=forum_manage&amp;action2=edit_forum_page&amp;id='.$row['id'].'">
			<span style="font-weight: bold;">Edit</span></a>&nbsp;
			<a href="javascript:confirm_delete(\''.$row['id'].'\');"><span style="font-weight: bold;">Delete</span></a>
			</td></tr>';

	
	}
}
	
$HTMLOUT .= '</table><br /><br />
			<form method="post" action="staffpanel.php?tool=forum_manage&amp;action=forum_manage">
			<table width="600"  border="0" cellspacing="0" cellpadding="3" align="center">
			<tr>
			<td colspan="2" class="forum_head_dark">Make new forum</td>
			</tr>
			<tr>
			<td align="right" class="three"><span style="font-weight: bold;">Forum name</span></td>
			<td align="left" class="three"><input name="name" type="text" class="text_default" size="20" maxlength="60" /></td>
			</tr>
			<tr>
			<td align="right" class="three"><span style="font-weight: bold;">Forum description:</span>  </td>
			<td align="left" class="three"><input name="desc" type="text" class="text_default" size="30" maxlength="200" /></td>
			</tr>
			<tr>
			<td align="right" class="three"><span style="font-weight: bold;">OverForum:</span> </td>
			<td align="left" class="three">
			<select name="over_forums">';

            $forum_id = $row['forum_id'];
            $res = sql_query('SELECT * FROM over_forums');
             
             	while ($arr = mysqli_fetch_array($res)) 
		{
		$i = $arr['id'];
		$option_7 .= '<option class="body" value="'.$i.'"'.($forum_id == $i ? ' selected="selected"' : '').'>'.htmlentities($arr['name'], ENT_QUOTES).'</option>';
		}


$HTMLOUT .= $option_7.'</select></td></tr>
			<tr>
			<td align="right" class="three"><span style="font-weight: bold;">Sub-Forum of?:</span></td>
			<td align="left" class="three">
			<select name="parent_forum">
			<option class="body" value="0"> none </option>';

            $forum_id = $row['forum_id'];
            $res = sql_query('SELECT * FROM forums');
             	
             	while ($arr = mysqli_fetch_array($res)) 
             	{
		$i = $arr['id'];
		$option_8 .= '<option class="body" value="'.$i.'"'.($forum_id == $i ? ' selected="selected"' : '').'>'.htmlentities($arr['name'], ENT_QUOTES).'</option>';
		}

$HTMLOUT .= $option_8.'</select></td></tr><tr>
			<td align="right" class="three"><span style="font-weight: bold;">Minimun read permission:</span> </td>
			<td align="left" class="three">
			<select name="min_class_read">';

          for ($i = 0; $i <= $maxclass; ++$i)
          {
          $option_9 .= '<option class="body" value="'.$i.'">'.get_user_class_name($i).'</option>';
          }

$HTMLOUT .= $option_9.'</select></td></tr>
			<tr>
			<td align="right" class="three"><span style="font-weight: bold;">Minimun write permission:</span> </td>
			<td align="left" class="three">
			<select name="min_class_write">';

          for ($i = 0; $i <= $maxclass; ++$i)
          {
          $option_10 .= '<option class="body" value="'.$i.'">'.get_user_class_name($i).'</option>';
          }
          
 $HTMLOUT .=$option_10.'</select></td></tr>
			<tr>
			<td align="right" class="three"><span style="font-weight: bold;">Minimun create topic permission:</span> </td>
			<td align="left" class="three">
			<select name="min_class_create">';

          for ($i = 0; $i <= $maxclass; ++$i)
          {
          $option_10 .= '<option class="body" value="'.$i.'">'.get_user_class_name($i).'</option>';
          }
          
$HTMLOUT .= $option_10.'</select></td></tr>
			<tr>
			<td align="right" class="three"><span style="font-weight: bold;">Forum rank:</span> </td>
			<td align="left" class="three">
			<select name="sort">';

	$res = sql_query ('SELECT sort FROM forums');
	$nr = mysqli_num_rows($res);
        $maxclass = $nr + 1;
        
          for ($i = 0; $i <= $maxclass; ++$i)
          {
          $option_11 .= '<option class="body" value="'.$i.'">'.$i.'</option>';
          }

 $HTMLOUT .= $option_11.'</select></td></tr>
			<tr>
			<td colspan="2" align="center" class="three">
			<input type="hidden" name="action2" value="add_forum" />
			<input type="submit" name="button" class="button" value="Make forum" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" /></td>
			</tr>
			</table></form>

	<script type="text/javascript">
			<!--
			function confirm_delete(id)
			{
			   if(confirm(\'Are you sure you want to delete this forum?\'))
			   {
			      self.location.href=\'staffpanel.php?tool=forum_manage&amp;action=forum_manage&action2=delete&id=\'+id;
			   }
			}
		//-->
	</script>';
	
echo stdhead('Forum Management Tools') . $HTMLOUT . stdfoot();
?>
