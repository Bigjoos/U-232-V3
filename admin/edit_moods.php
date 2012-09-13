<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 *
 */
// edit_moods.php for PTF by pdq 2011
if (!defined('IN_INSTALLER09_ADMIN')) require_once (ROOT_DIR.'404.html');
require_once (CLASS_DIR.'class_check.php');
class_check(UC_MODERATOR);
$lang = array_merge($lang);
$HTMLOUT = '';
if (isset($_POST) || isset($_GET)) $edit_params = array_merge($_GET, $_POST);
$edit_mood['action'] = (isset($edit_params['action']) ? $edit_params['action'] : 0);
$edit_mood['id'] = (isset($edit_params['id']) ? (int)$edit_params['id'] : 0);
$edit_mood['name'] = (isset($edit_params['name']) ? $edit_params['name'] : 0);
$edit_mood['image'] = (isset($edit_params['image']) ? $edit_params['image'] : 0);
$edit_mood['bonus'] = (isset($edit_params['bonus']) ? (int)$edit_params['bonus'] : 0);
if ($edit_mood['action'] == 'added') {
    if ($edit_mood['name'] && $edit_mood['name'] && $edit_mood['name'] && ($edit_mood['name'] != 'is example mood' && $edit_mood['image'] != 'smile1.gif')) {
        sql_query('INSERT INTO moods (name, image, bonus) VALUES ('.sqlesc($edit_mood['name']).', '.sqlesc($edit_mood['image']).', '.$edit_mood['bonus'].')') or sqlerr(__FILE__, __LINE__);
        $mc1->delete_value('topmoods');
        write_log('<b>Mood Added</b> '.$CURUSER['username'].' '.$edit_mood['name'].'<img src="'.$INSTALLER09['pic_base_url'].'smilies/'.$edit_mood['image'].'" alt="" />');
    }
} elseif ($edit_mood['action'] == 'edited') {
    if ($edit_mood['name'] && $edit_mood['name'] && $edit_mood['name']) {
        sql_query('UPDATE moods SET name = '.sqlesc($edit_mood['name']).', image = '.sqlesc($edit_mood['image']).', bonus = '.$edit_mood['bonus'].' WHERE id = '.$edit_mood['id']) or sqlerr(__FILE__, __LINE__);
        $mc1->delete_value('topmoods');
        write_log('<b>Mood Edited</b> '.$CURUSER['username'].' '.$edit_mood['name'].'<img src="'.$INSTALLER09['pic_base_url'].'smilies/'.$edit_mood['image'].'" alt="" />');
    }
}
/*
elseif ($edit_mood['action'] == 'remove') {
   sql_query('DELETE FROM moods WHERE id = '.$edit_mood['id'].' LIMIT 1') or sqlerr(__FILE__, __LINE__);
   $mc1->del_value('topmoods');
   write_log2('<b>Mood Deleted</b> '.$CURUSER['username'].' '.$edit_mood['id']);
}
*/
if ($edit_mood['action'] == 'edit' && $edit_mood['id']) {
    $edit_mood['res'] = sql_query('SELECT * FROM moods WHERE id = '.$edit_mood['id']) or sqlerr(__FILE__, __LINE__);
    if (mysqli_num_rows($edit_mood['res'])) {
        $edit_mood['arr'] = mysqli_fetch_assoc($edit_mood['res']);
        $HTMLOUT.= "<h1>Edit Mood</h1>
            <form method='post' action='staffpanel.php?tool=edit_moods&amp;action=edited'>
            <table border='1' cellspacing='0' cellpadding='5'>
            <tr><td class='colhead'>Name</td>
            <td><input type='text' name='name' size='40' value ='".htmlsafechars($edit_mood['arr']['name'])."' /></td></tr>
            <tr><td class='colhead'>Image</td>
            <td><input type='text' name='image' size='40' value ='".htmlsafechars($edit_mood['arr']['image'])."' /></td></tr>
            <tr><td class='colhead'>Bonus</td>
            <td><input type='checkbox' name='bonus'".($edit_mood['arr']['bonus'] ? ' checked="checked"' : '')." /></td></tr>
            <tr><td colspan='2' align='center'>
            <input type='hidden' name='id' value='".$edit_mood['id']."' />
            <input type='submit' name='okay' value='Add' class='btn' />
            </td></tr>
            </table></form>";
    }
} else {
    $HTMLOUT.= "<h1>Add New Mood</h1>
         <form method='post' action='staffpanel.php?tool=edit_moods&amp;action=added'>
         <table border='1' cellspacing='0' cellpadding='5'>
         <tr><td class='colhead'>Name</td>
         <td><input type='text' name='name' size='40' value ='is example mood' /></td></tr>
         <tr><td class='colhead'>Image</td>
         <td><input type='text' name='image' size='40' value ='smiley1.gif' /></td></tr>
         <tr><td class='colhead'>Bonus</td>
         <td><input type='checkbox' name='bonus' /></td></tr>
         <tr><td colspan='2' align='center'>
         <input type='submit' name='okay' value='Add' class='btn' />
         </td></tr>
         </table></form>";
}
$HTMLOUT.= '<h1>Current Moods</h1>';
$HTMLOUT.= "<table border='1' width='85%' cellspacing='0' cellpadding='5'>
      <tr><td class='colhead'>Added</td>
      <td class='colhead' align='left'>Name</td>
      <td class='colhead' align='left'>Image</td>
      <td class='colhead' align='left'>Bonus</td>
      <td class='colhead'>Edit</td>".
//<td class='colhead'>Remove</td>
"</tr>";
$res = sql_query('SELECT * FROM moods ORDER BY id ASC') or sqlerr(__FILE__, __LINE__);
if (mysqli_num_rows($res)) {
    $color = true;
    while ($arr = mysqli_fetch_assoc($res)) {
        $HTMLOUT.= '<tr '.(($color = !$color) ? ' style="background-color:#000000;"' : 'style="background-color:#0f0f0f;"').'>
      <td><img src="'.$INSTALLER09['pic_base_url'].'smilies/'.htmlsafechars($arr['image']).'" alt="" /></td>
      <td align="left">'.htmlsafechars($arr['name']).'</td>
      <td align="left">'.htmlsafechars($arr['image']).'</td>
      <td align="left">'.($arr['bonus'] != 0 ? 'Yes' : 'No').'</td>
      <td><a style="color:#FF0000" href="staffpanel.php?tool=edit_moods&amp;id='.(int)$arr['id'].'&amp;action=edit">Edit</a></td></tr>'.
        //<td><a style="color:#FF0000" href="staffpanel.php?tool=edit_moods&amp;action=remove$amp;id='.$arr['id'].'&amp;hash='.$form_hash.'>Remove</a></td></tr>
        '';
    }
    $HTMLOUT.= '</table>';
}
echo stdhead("Edit Moods").$HTMLOUT.stdfoot();
?>
