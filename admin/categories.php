<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
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
require_once(INCL_DIR.'function_subcat.php');
require_once(CLASS_DIR.'class_check.php');
class_check(UC_SYSOP);

    $params = array_merge( $_GET, $_POST );
    
    $params['mode'] = isset($params['mode']) ? $params['mode'] : '';
    
    switch($params['mode'])
    {
      case 'takemove_cat':
        move_cat();
        break;
        
      case 'move_cat':
        move_cat_form();
        break;
        
      case 'takeadd_cat':
        add_cat();
        break;
        
      case 'takedel_cat':
        delete_cat();
        break;
        
      case 'del_cat':
        delete_cat_form();
        break;
        
      case 'takeedit_cat':
        edit_cat();
        break;
        
      case 'edit_cat':
        edit_cat_form();
        break;
        
      case 'cat_form':
        show_cat_form();
        break;

      default:
        show_categories();
        break;
    }


function move_cat() {
    
    global $INSTALLER09, $params, $mc1;
    
    if( ( !isset($params['id']) OR !is_valid_id($params['id']) ) OR ( !isset($params['new_cat_id']) OR !is_valid_id($params['new_cat_id']) ) )
    {
      stderr( 'MOD ERROR', 'No category ID selected' );
    }
    
    if( !is_valid_id($params['new_cat_id']) OR ($params['id'] == $params['new_cat_id']) )
    {
      stderr( 'MOD ERROR', 'You can not move torrents into the same category' );
    }
    
    $old_cat_id = intval($params['id']);
    $new_cat_id = intval($params['new_cat_id']);
    
    // make sure both categories exist
    $q = sql_query( "SELECT id FROM categories WHERE id IN($old_cat_id, $new_cat_id)" );
    
    if( 2 != mysqli_num_rows($q) )
    {
      stderr( 'MOD ERROR', 'That category does not exist or has been deleted' );
    }
    
    //all go
    sql_query( "UPDATE torrents SET category = $new_cat_id WHERE category = $old_cat_id" );
    $mc1->delete_value('genrelist');
    $mc1->delete_value('categories');
    if( -1 != mysqli_affected_rows($GLOBALS["___mysqli_ston"]) )
    {
      header( "Location: {$INSTALLER09['baseurl']}/staffpanel.php?tool=categories&action=categories" );
    }
    else
    {
      stderr( 'MOD ERROR', 'There was an error deleting the category' );
    }
}



function move_cat_form() {

    global $params;
    
    if( !isset($params['id']) OR !is_valid_id($params['id']) )
    {
      stderr( 'MOD ERROR', 'No category ID selected' );
    }
    
    $q = sql_query( "SELECT * FROM categories WHERE id = ".intval($params['id']) );
    
    if( false == mysqli_num_rows($q) )
    {
      stderr( 'MOD ERROR', 'That category does not exist or has been deleted' );
    }
    
    $r = mysqli_fetch_assoc($q);
    
    
    $check = '';
    
    $select = "<select name='new_cat_id'>\n<option value='0'>Select Category</option>\n";

    $cats = genrelist2();
  
    foreach ($cats as $c)
    {
      $select .= ($c['id'] != $r['id']) ? "<option value='{$c["id"]}'>" . htmlentities($c['name'], ENT_QUOTES) . "</option>\n" : "";
    }
    
    $select .= "</select>\n";
    
    $check .= "<tr>
      <td align='right' width='50%'><span style='color:limegreen;font-weight:bold;'>Select a new category:</span></td>
      <td>$select</td>
    </tr>";
    
    
    $htmlout = '';
    
    $htmlout .= "<form action='staffpanel.php?tool=categories&amp;action=categories' method='post'>
      <input type='hidden' name='mode' value='takemove_cat' />
      <input type='hidden' name='id' value='{$r['id']}' />
    
      <table class='torrenttable' align='center' width='80%' bgcolor='#cecece' cellspacing='2' cellpadding='4px'>
      <tr>
        <td colspan='2' class='colhead'>You are about to move category: ".htmlentities($r['name'], ENT_QUOTES)."</td>
      </tr>
      <tr>
        <td colspan='2'>Note: This tool will move ALL torrents FROM one category to ANOTHER category only! It will NOT delete any categories or torrents.</td>
      </tr>
      <tr>
        <td align='right' width='50%'><span style='color:red;font-weight:bold;'>Old Category Name:</span></td>
        <td>".htmlentities($r['name'], ENT_QUOTES)."</td>
      </tr>
      {$check}
      <tr>
        <td colspan='2' align='center'>
         <input type='submit' class='btn' value='Move' /><input type='button' class='btn' value='Cancel' onclick=\"history.go(-1)\" /></td>
      </tr>
      </table>
      </form>";
      
      echo stdhead("Move category {$r['name']}") . $htmlout . stdfoot();
}


function add_cat() {

    global $INSTALLER09, $params, $mc1;
    
    foreach( array( 'new_cat_name', 'new_cat_desc', 'new_cat_image', 'new_cat_parent_id', 'new_cat_tabletype') as $x )
    {
      if( !isset($params[ $x ]) OR empty($params[ $x ]) )
        stderr( 'MOD ERROR', 'Some fields were left blank' );
    }
    
    if ( !preg_match( "/^cat_[A-Za-z0-9_]+\.(?:gif|jpg|jpeg|png)$/i", $params['new_cat_image'] ) )
    {
					stderr( 'MOD ERROR', 'File name is not allowed' );
    }
    
    $cat_name = sqlesc($params['new_cat_name']);
    $cat_desc = sqlesc($params['new_cat_desc']);
    $cat_image = sqlesc($params['new_cat_image']);
	 $cat_parent = sqlesc($params['new_cat_parent_id']);
	 $cat_tabletype = sqlesc($params['new_cat_tabletype']);
    
    sql_query( "INSERT INTO categories (name, cat_desc, image, parent_id, tabletype)
                  VALUES($cat_name, $cat_desc, $cat_image, $cat_parent, $cat_tabletype)" );
    $mc1->delete_value('genrelist');
    $mc1->delete_value('categories');
    if( -1 == mysqli_affected_rows($GLOBALS["___mysqli_ston"]) )
    {
      stderr( 'MOD ERROR', 'That category does not exist or has been deleted' );
    }
    else
    {
      header( "Location: {$INSTALLER09['baseurl']}/staffpanel.php?tool=categories&action=categories" );
    }
}

function delete_cat() {

    global $INSTALLER09, $params, $mc1;
    
    if( !isset($params['id']) OR !is_valid_id($params['id']) )
    {
      stderr( 'MOD ERROR', 'No category ID selected' );
    }
    
    $q = @mysqli_query($GLOBALS["___mysqli_ston"],  "SELECT * FROM categories WHERE id = ".intval($params['id']) );
    
    if( false == mysqli_num_rows($q) )
    {
      stderr( 'MOD ERROR', 'That category does not exist or has been deleted' );
    }
    
    $r = mysqli_fetch_assoc($q);
    
    $old_cat_id = intval($r['id']);
    
    if( isset($params['new_cat_id']) )
    {
      if( !is_valid_id($params['new_cat_id']) OR ($r['id'] == $params['new_cat_id']) )
      {
        stderr( 'MOD ERROR', 'That category does not exist or has been deleted' );
      }
      
      $new_cat_id = intval($params['new_cat_id']);
      
      //make sure category isn't out of range before moving torrents! else orphans!
      $q = sql_query( "SELECT COUNT(*) FROM categories WHERE id = $new_cat_id" );
      
      $count = mysqli_fetch_array($q,  MYSQLI_NUM);
      
      if( !$count[0] )
      {
        stderr( 'MOD ERROR', 'That category does not exist or has been deleted' );
      }
      
      //all go
      sql_query( "UPDATE torrents SET category = $new_cat_id WHERE category = $old_cat_id" );
    }
    
    sql_query( "DELETE FROM categories WHERE id = $old_cat_id" );
    $mc1->delete_value('genrelist');
    $mc1->delete_value('categories');
    if( mysqli_affected_rows($GLOBALS["___mysqli_ston"]) )
    {
      header( "Location: {$INSTALLER09['baseurl']}/staffpanel.php?tool=categories&action=categories" );
    }
    else
    {
      stderr( 'MOD ERROR', 'There was an error deleting the category' );
    }
}



function delete_cat_form() {

    global $params;
    
    if( !isset($params['id']) OR !is_valid_id($params['id']) )
    {
      stderr( 'MOD ERROR', 'No category ID selected' );
    }
    
    $q = @mysqli_query($GLOBALS["___mysqli_ston"],  "SELECT * FROM categories WHERE id = ".intval($params['id']) );
    
    if( false == mysqli_num_rows($q) )
    {
      stderr( 'MOD ERROR', 'That category does not exist or has been deleted' );
    }
    
    $r = mysqli_fetch_assoc($q);
    
    $q = sql_query( "SELECT COUNT(*) FROM torrents WHERE category = ".intval($r['id']) );
    
    $count = mysqli_fetch_array($q,  MYSQLI_NUM);
    
    $check = '';
    
    if($count[0])
    {
      $select = "<select name='new_cat_id'>\n<option value='0'>Select Category</option>\n";

      $cats = genrelist2();
    
      foreach ($cats as $c)
      {
        $select .= ($c['id'] != $r['id']) ? "<option value='{$c["id"]}'>" . htmlentities($c['name'], ENT_QUOTES) . "</option>\n" : "";
      }
      
      $select .= "</select>\n";
      
      $check .= "<tr>
        <td align='right' width='50%'>Select a new category:<br /><span style='color:red;font-weight:bold;'>Warning: There are torrents in this category, so you need to select a category to move them to.</span></td>
        <td>$select</td>
      </tr>";
    }
    
    $htmlout = '';
    
    $htmlout .= "<form action='staffpanel.php?tool=categories&amp;action=categories' method='post'>
      <input type='hidden' name='mode' value='takedel_cat' />
      <input type='hidden' name='id' value='{$r['id']}' />
    
      <table class='torrenttable' align='center' width='80%' bgcolor='#cecece' cellspacing='2' cellpadding='2'>
      <tr>
        <td colspan='2' class='colhead'>You are about to delete category: ".htmlentities($r['name'], ENT_QUOTES)."</td>
      </tr>
      <tr>
        <td align='right' width='50%'>Cat Name:</td>
        <td>".htmlentities($r['name'], ENT_QUOTES)."</td>
      </tr>
	        <tr>
        <td align='right' width='50%'>Parent ID:</td>
        <td>".htmlentities($r['parent_id'], ENT_QUOTES)."</td>
      </tr>
	        <tr>
        <td align='right' width='50%'>Table Type:</td>
        <td>".htmlentities($r['tabletype'], ENT_QUOTES)."</td>
      </tr>
      <tr>
        <td align='right'>Description:</td>
        <td>".htmlentities($r['cat_desc'], ENT_QUOTES)."</td>
      </tr>
      <tr>
        <td align='right'>Image:</td>
        <td>".htmlentities($r['image'], ENT_QUOTES)."</td>
      </tr>
      {$check}
      <tr>
        <td colspan='2' align='center'>
         <input type='submit' class='btn' value='Delete' /><input type='button' class='btn' value='Cancel' onclick=\"history.go(-1)\" /></td>
      </tr>
      </table>
      </form>";
      
      echo stdhead("Deleting category {$r['name']}") . $htmlout . stdfoot();
}


function edit_cat() {

    global $INSTALLER09, $params, $mc1;
    
    if( !isset($params['id']) OR !is_valid_id($params['id']) )
    {
      stderr( 'MOD ERROR', 'No category ID selected' );
    }
    
    foreach( array( 'cat_parent_id', 'cat_tabletype', 'cat_name', 'cat_desc', 'cat_image' ) as $x )
    {
      if( !isset($params[ $x ]) OR empty($params[ $x ]) )
        stderr( 'MOD ERROR', 'Some fields were left blank '.$x.'' );
    }
    
    if ( !preg_match( "/^cat_[A-Za-z0-9_]+\.(?:gif|jpg|jpeg|png)$/i", $params['cat_image'] ) )
    {
					stderr( 'MOD ERROR', 'File name is not allowed' );
    }
    
	$cat_parent = sqlesc($params['cat_parent_id']);
	$cat_tabletype = sqlesc($params['cat_tabletype']);
    $cat_name = sqlesc($params['cat_name']);
    $cat_desc = sqlesc($params['cat_desc']);
    $cat_image = sqlesc($params['cat_image']);
    $cat_id = intval($params['id']);
    
    sql_query( "UPDATE categories SET parent_id = $cat_parent, tabletype = $cat_tabletype, name = $cat_name, cat_desc = $cat_desc, image = $cat_image WHERE id = $cat_id" );
    $mc1->delete_value('genrelist');
    $mc1->delete_value('categories');
    if( -1 == mysqli_affected_rows($GLOBALS["___mysqli_ston"]) )
    {
      stderr( 'MOD ERROR', 'That category does not exist or has been deleted' );
    }
    else
    {
      header( "Location: {$INSTALLER09['baseurl']}/staffpanel.php?tool=categories&action=categories" );
    }
}



function edit_cat_form() {

    global $INSTALLER09, $params;
    
    if( !isset($params['id']) OR !is_valid_id($params['id']) )
    {
      stderr( 'MOD ERROR', 'No category ID selected' );
    }
    
    $htmlout = '';
    
    $q = sql_query( "SELECT * FROM categories WHERE id = ".intval($params['id']) );
    
    if( false == mysqli_num_rows($q) )
    {
      stderr( 'MOD ERROR', 'That category does not exist or has been deleted' );
    }
    
    $r = mysqli_fetch_assoc($q);
    
    $dh = opendir( $INSTALLER09['pic_base_url'].'caticons/1' );
		
		$files = array();
		
 		while ( FALSE !== ( $file = readdir( $dh ) ) )
 		{
 			if ( ($file != ".") && ($file != "..") )
 			{
				if ( preg_match( "/^cat_[A-Za-z0-9_]+\.(?:gif|jpg|jpeg|png)$/i", $file ) )
				{
					$files[] = $file;
				}
 			}
 		}
 		
 		closedir( $dh );
 		
 		if( is_array($files) AND count($files) )
 		{
      $select = "<select name='cat_image'>\n<option value='0'>Select Image</option>\n";

      foreach ($files as $f)
      {
        $selected = ($f == $r['image']) ? " selected='selected'" : "";
        $select .= "<option value='" . htmlentities($f, ENT_QUOTES) . "'$selected>" . htmlentities($f, ENT_QUOTES) . "</option>\n";
        
      }
      
      $select .= "</select>\n";
      
      $check = "<tr>
        <td align='right' width='50%'>Select a new image:<br /><span style='color:limegreen;font-weight:bold;'>Info: If you want a new image, you have to upload it to the /caticon/ directory first.</span></td>
        <td>$select</td>
      </tr>";
 		}
 		else
 		{
      $check = "<tr>
        <td align='right' width='50%'>Select a new image:</td>
        <td><span style='color:red;font-weight:bold;'>Warning: There are no images in the directory, please upload one.</span></td>
      </tr>";
 		}
 		
    $htmlout .= "<form action='staffpanel.php?tool=categories&amp;action=categories' method='post'>
      <input type='hidden' name='mode' value='takeedit_cat' />
      <input type='hidden' name='id' value='{$r['id']}' />
    
      <table class='torrenttable' align='center' width='80%' bgcolor='#cecece' cellspacing='2' cellpadding='2'>
      <tr>
        <td align='right'>New Cat Name:</td>
        <td><input type='text' name='cat_name' class='option' size='50' value='".htmlentities($r['name'], ENT_QUOTES)."' /></td>
      </tr>
	        <tr>
        <td align='right'>New Parent ID:</td>
        <td><input type='text' name='cat_parent_id' class='option' size='50' value='".htmlentities($r['parent_id'], ENT_QUOTES)."' /></td>
      </tr>
	        <tr>
        <td align='right'>New Table Type:</td>
        <td><input type='text' name='cat_tabletype' class='option' size='50' value='".htmlentities($r['tabletype'], ENT_QUOTES)."' /></td>
      </tr>
      <tr>
        <td align='right'>Description:</td>
        <td><textarea cols='50' rows='5' name='cat_desc'>".htmlentities($r['cat_desc'], ENT_QUOTES)."</textarea></td>
      </tr>
      {$check}
      <tr>
        <td colspan='2' align='center'>
         <input type='submit' class='btn' value='Edit' /><input type='button' class='btn' value='Cancel' onclick=\"history.go(-1)\" /></td>
      </tr>
      </table>
      </form>";

      echo stdhead( "Editing category: {$r['name']}") . $htmlout . stdfoot();
}


function show_categories() {
    
    global $INSTALLER09;
    
    $htmlout = '';
    
    $dh = opendir( $INSTALLER09['pic_base_url'].'caticons/1' );
		
		$files = array();
		
 		while ( FALSE !== ( $file = readdir( $dh ) ) )
 		{
 			if ( ($file != ".") && ($file != "..") )
 			{
				if ( preg_match( "/^cat_[A-Za-z0-9_]+\.(?:gif|jpg|jpeg|png)$/i", $file ) )
				{
					$files[] = $file;
				}
 			}
 		}
 		
 		closedir( $dh );
 		
 		if( is_array($files) AND count($files) )
 		{
      $select = "<select name='new_cat_image'>\n<option value='0'>Select Image</option>\n";

      foreach ($files as $f)
      {
        $i = 0;
        $select .= "<option value='" . htmlentities($f, ENT_QUOTES) . "'>" . htmlentities($f, ENT_QUOTES) . "</option>\n";
        $i++;
      }
      
      $select .= "</select>\n";
      
      $check = "<tr>
        <td align='right' width='50%'>Select a new image:<br /><span style='color:limegreen;font-weight:bold;'>Warning: If you want a new image, you have to upload it to the /caticon/ directory first.</span></td>
        <td>$select</td>
      </tr>";
 		}
 		else
 		{
      $check = "<tr>
        <td align='right' width='50%'>Select a new image:</td>
        <td><span style='color:red;font-weight:bold;'>Warning: There are no images in the directory, please upload one.</span></td>
      </tr>";
 		}
 		
 		
    $htmlout .= "<form action='staffpanel.php?tool=categories&amp;action=categories' method='post'>
    <input type='hidden' name='mode' value='takeadd_cat' />
    
    <table class='torrenttable' border='1' width='80%' bgcolor='#cecece' cellspacing='2' cellpadding='2'>
    <tr>
      <td class='colhead' colspan='2' align='center'>
        <b>Make a new category:</b>
      </td>
    </tr>
    <tr>
      <td align='right'>New Cat Name:</td>
      <td align='left'><input type='text' name='new_cat_name' size='50' maxlength='50' /></td>
    </tr>
	    <tr>
      <td align='right'>New Parent ID:</td>
      <td align='left'><input type='text' name='new_cat_parent_id' size='50' maxlength='50' /></td>
    </tr>
	    <tr>
      <td align='right'>New Table Type:</td>
      <td align='left'><input type='text' name='new_cat_tabletype' size='50' maxlength='50' /></td>
    </tr>
    <tr>
      <td align='right'>New Cat Description:</td>
      <td align='left'><textarea cols='50' rows='5' name='new_cat_desc'></textarea></td>
    </tr>
    <!--<tr>
      <td align='right'>New Filename (Eg: films.gif or films.png):</td>
      <td align='left'><input type='text' name='new_cat_image' class='option' size='50' /></td>
    </tr>-->
    {$check}
    <tr>
      <td colspan='2' align='center'>
        <input type='submit' value='Add New' class='btn' />
        <input type='reset' value='Reset' class='btn' />
      </td>
    </tr>
    </table>
    </form>


    <h2>Current Categories:</h2>
    <table class='torrenttable' border='1' width='80%' bgcolor='#cecece' cellpadding='5px'>
    <tr>
      <td class='colhead' width='60'>Cat ID</td>
	  <td class='colhead' width='60'>Parent ID</td>
	  <td class='colhead' width='60'>Table Type</td>
      <td class='colhead' width='60'>Cat Name</td>
      <td class='colhead' width='200'>Cat Description</td>
      <td class='colhead' width='45'>Image</td>
      <td class='colhead' width='40'>Edit</td>
      <td class='colhead' width='40'>Delete</td>
      <td class='colhead' width='40'>Move</td>
    </tr>";
             

    $query = sql_query( "SELECT * FROM categories" );
   
    if( false == mysqli_num_rows($query) ) 
    {
      $htmlout = '<h1>Ooops!!</h1>';
    } 
    else 
    {
      while($row = mysqli_fetch_assoc($query))
      {
        $cat_image = file_exists($INSTALLER09['pic_base_url'].'caticons/1/'.$row['image']) ? "<img border='0' src='{$INSTALLER09['pic_base_url']}caticons/1/{$row['image']}' alt='{$row['id']}' />" : "No Image";
        
        $htmlout .= "<tr>
          <td height='48' width='60'><b>ID({$row['id']})</b></td>
		  <td height='48' width='60'><b>({$row['parent_id']})</b></td>
		  <td height='48' width='60'><b>({$row['tabletype']})</b></td>		
          <td width='120'>{$row['name']}</td>
          <td width='250'>{$row['cat_desc']}</td>
          <td align='center' width='45'>$cat_image</td>
          <td align='center' width='18'><a href='staffpanel.php?tool=categories&amp;action=categories&amp;mode=edit_cat&amp;id={$row['id']}'>
            <img src='{$INSTALLER09['pic_base_url']}aff_tick.gif' alt='Edit Category' title='Edit' width='12' height='12' border='0' /></a></td>
          <td align='center' width='18'><a href='staffpanel.php?tool=categories&amp;action=categories&amp;mode=del_cat&amp;id={$row['id']}'>
            <img src='{$INSTALLER09['pic_base_url']}aff_cross.gif' alt='Delete Category' title='Delete' width='12' height='12' border='0' /></a></td>
          <td align='center' width='18'><a href='staffpanel.php?tool=categories&amp;action=categories&amp;mode=move_cat&amp;id={$row['id']}'>
            <img src='{$INSTALLER09['pic_base_url']}plus.gif' alt='Move Category' title='Move' width='12' height='12' border='0' /></a></td>
        </tr>";
      }
          
      
    } //endif
    
    $htmlout .= '</table>';
    
    echo stdhead('Admin Categories') . $htmlout . stdfoot();
}

?>
