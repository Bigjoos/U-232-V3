<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 *
 */
/*******************************************************
//=== shit list for staff to keep track of bad or suspected members personally
      for BTDev 2010ish
*******************************************************/
if (!defined('IN_INSTALLER09_ADMIN')) {
    $HTMLOUT = '';
    $HTMLOUT.= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
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
require_once INCL_DIR.'user_functions.php';
require_once INCL_DIR.'bbcode_functions.php';
require_once (CLASS_DIR.'class_check.php');
class_check(UC_ADMINISTRATOR);
$lang = array_merge($lang);
$HTMLOUT = $message = $title = '';
//=== check if action2 is sent (either $_POST or $_GET) if so make sure it's what you want it to be
$action2 = (isset($_POST['action2']) ? $_POST['action2'] : (isset($_GET['action2']) ? $_GET['action2'] : ''));
$good_stuff = array(
    'new',
    'add',
    'delete'
);
$action2 = (($action2 && in_array($action2, $good_stuff, true)) ? $action2 : '');
//=== action2 switch... do what must be done!
switch ($action2) {
    //=== action2: new
    
case 'new':
    $shit_list_id = (isset($_GET['shit_list_id']) ? intval($_GET['shit_list_id']) : 0);
    $return_to = str_replace('&amp;', '&', htmlsafechars($_GET['return_to']));
    $mc1->delete_value('shit_list_'.$CURUSER['id']);
    if ($shit_list_id == $CURUSER["id"]) stderr("Error", "Cant add yerself");
    if (!is_valid_id($shit_list_id)) stderr('Error', 'Invalid ID');
    $res_name = sql_query('SELECT username FROM users WHERE id='.sqlesc($shit_list_id));
    $arr_name = mysqli_fetch_assoc($res_name);
    $check_if_there = sql_query('SELECT suspect FROM shit_list WHERE userid='.sqlesc($CURUSER['id']).' AND suspect='.sqlesc($shit_list_id));
    if (mysqli_num_rows($check_if_there) == 1) stderr('Error', 'The member '.htmlsafechars($arr_name['username']).' is already on your shit list!');
    $level_of_shittyness = '';
    $level_of_shittyness.= '<select name="shittyness"><option value="0">level of shittyness</option>';
    $i = 1;
    while ($i <= 10) {
        $level_of_shittyness.= '<option value="'.$i.'">'.$i.' out of 10</option>';
        $i++;
    }
    $level_of_shittyness.= '</select>';
    $HTMLOUT.= '<h1><img src="pic/smilies/shit.gif" alt="*" /> Add '.htmlsafechars($arr_name['username']).' to your Shit List <img src="pic/smilies/shit.gif" alt="*" /></h1>
      <form method="post" action="staffpanel.php?tool=shit_list&amp;action=shit_list&amp;action2=add">
   <table border="0" cellspacing="0" cellpadding="5" align="center">
   <tr>
      <td class="colhead" colspan="2">new <img src="pic/smilies/shit.gif" alt="*" /><img src="pic/smilies/shit.gif" alt="*" /><img src="pic/smilies/shit.gif" alt="*" />
      <img src="pic/smilies/shit.gif" alt="*" /><img src="pic/smilies/shit.gif" alt="*" /><img src="pic/smilies/shit.gif" alt="*" />
      <img src="pic/smilies/shit.gif" alt="*" /><img src="pic/smilies/shit.gif" alt="*" /><img src="pic/smilies/shit.gif" alt="*" /><img src="pic/smilies/shit.gif" alt="*" />
      out of 10, 1 being not so shitty, 10 being really shitty... Please select one.</td>
   </tr>
   <tr>
      <td align="right" valign="top"><b>Shittyness:</b></td>
      <td align="left" valign="top">'.$level_of_shittyness.'</td>
   </tr>
   <tr>
      <td align="right" valign="top"><b>Reason:</b></td>
      <td align="left" valign="top"><textarea cols="60" rows="5" name="text"></textarea></td>
   </tr>
   <tr>
    <td align="center" colspan="2">
      <input type="hidden" name="shit_list_id" value="'.$shit_list_id.'" />
      <input type="hidden" name="return_to" value="'.$return_to.'" />
     
      <input type="submit" class="button" value="add this shit bag!" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" /></td>
   </tr>
   </table></form>';
    break;
    //=== action2: add
    
case 'add':
    $shit_list_id = (isset($_POST['shit_list_id']) ? intval($_POST['shit_list_id']) : 0);
    $shittyness = (isset($_POST['shittyness']) ? intval($_POST['shittyness']) : 0);
    $return_to = str_replace('&amp;', '&', htmlsafechars($_POST['return_to']));
    if (!is_valid_id($shit_list_id) || !is_valid_id($shittyness)) stderr('Error', 'Invalid ID');
    $check_if_there = sql_query('SELECT suspect FROM shit_list WHERE userid='.sqlesc($CURUSER['id']).' AND suspect='.sqlesc($shit_list_id));
    if (mysqli_num_rows($check_if_there) == 1) stderr('Error', 'That user is already on your shit list.');
    sql_query('INSERT INTO shit_list VALUES ('.$CURUSER['id'].','.$shit_list_id.', '.$shittyness.', '.TIME_NOW.', '.sqlesc($_POST['text']).')');
    $mc1->delete_value('shit_list_'.$shit_list_id);
    $message = '<h1>Success! Member added to your personal shitlist!</h1><a class="altlink" href="'.$return_to.'">go back to where you were?</a>';
    break;
    //=== action2: delete
    
case 'delete':
    $shit_list_id = (isset($_GET['shit_list_id']) ? intval($_GET['shit_list_id']) : 0);
    $sure = (isset($_GET['sure']) ? intval($_GET['sure']) : '');
    if (!is_valid_id($shit_list_id)) stderr('Error', 'Invalid ID');
    $res_name = sql_query('SELECT username FROM users WHERE id='.$shit_list_id);
    $arr_name = mysqli_fetch_assoc($res_name);
    if (!$sure) stderr('Delete '.htmlsafechars($arr_name['username']).' from shit list', 'Do you really want to delete <b>'.htmlsafechars($arr_name['username']).'</b> from your shit list?  
         <a class="altlink" href="staffpanel.php?tool=shit_list&amp;action=shit_list&amp;action2=delete&amp;shit_list_id='.$shit_list_id.'&amp;sure=1">here</a> if you are sure.');
    sql_query('DELETE FROM shit_list WHERE userid='.$CURUSER['id'].' AND suspect='.$shit_list_id);
    if (mysqli_affected_rows($GLOBALS["___mysqli_ston"]) == 0) stderr('Error', 'No member found to delete!');
    $mc1->delete_value('shit_list_'.$shit_list_id);
    $message = '<h1>Success! <b>'.htmlsafechars($arr_name['username']).'</b> deleted from your shit list!</h1>';
    break;
} //=== end switch
//=== get stuff ready for page
$res = sql_query('SELECT s.suspect as suspect_id, s.text, s.shittyness, s.added AS shit_list_added, 
                  u.username, u.id, u.added, u.class, u.leechwarn, u.chatpost, u.pirate, u.king, u.avatar, u.donor, u.warned, u.enabled, u.suspended, u.last_access, u.offensive_avatar, u.avatar_rights
                  FROM shit_list AS s 
                  LEFT JOIN users as u ON s.suspect = u.id 
                  WHERE s.userid='.sqlesc($CURUSER['id']).' 
                  ORDER BY shittyness DESC');
//=== default page
$HTMLOUT.= $message.'

      <h1>Shit List for '.htmlsafechars($CURUSER['username']).'</h1>

   <table width="750" border="0" cellspacing="0" cellpadding="5" align="center">
   <tr>
      <td class="colhead" align="center" valign="top" colspan="4">
      <img src="pic/smilies/shit.gif" alt="*" /> shittiest at the top <img src="pic/smilies/shit.gif" alt="*" /></td>
   </tr>';
$i = 1;
if (mysqli_num_rows($res) == 0) {
    $HTMLOUT.= '
   <tr>
      <td class="one" align="center" valign="top" colspan="4">
      <img src="pic/smilies/shit.gif" alt="*" /> Your shit list is empty. <img src="pic/smilies/shit.gif" alt="*" /></td>
   </tr>';
} else while ($shit_list = mysqli_fetch_array($res)) {
    $shit = '';
    for ($poop = 1; $poop <= $shit_list['shittyness']; $poop++) {
        $shit.= ' <img src="pic/smilies/shit.gif" title="'.(int)$shit_list['shittyness'].' out of 10 on the sittyness scale" alt="*" />';
    }
    $HTMLOUT.= (($i % 2 == 1) ? '<tr>' : '').'
      <td class="'.(($i % 2 == 0) ? 'one' : 'two').'" align="center" valign="top" width="80">'.avatar_stuff($shit_list).'

      '.print_user_stuff($shit_list).'

      <b> [ '.get_user_class_name($shit_list['class']).' ]</b>

      <a class="altlink" href="staffpanel.php?tool=shit_list&amp;action=shit_list&amp;action2=delete&amp;shit_list_id='.(int)$shit_list['suspect_id'].'" title="remove this toad from your shit list">Remove</a>


      <a class="altlink" href="sendmessage.php?receiver='.(int)$shit_list['suspect_id'].'" title="send a PM to this evil toad">Send PM</a></td>
      <td class="'.(($i % 2 == 0) ? 'one' : 'two').'" align="left" valign="top">'.$shit.'

      <b>joined:</b> '.get_date($shit_list['added'], '').'
 
      [ '.get_date($shit_list['added'], '', 0, 1).' ]

      <b>added to shit list:</b> '.get_date($shit_list['shit_list_added'], '').'

      [ '.get_date($shit_list['shit_list_added'], '', 0, 1).' ]

      <b>last seen:</b> '.get_date($shit_list['last_access'], '').' 

      [ '.get_date($shit_list['last_access'], '', 0, 1).' ]<hr />
      '.format_comment($shit_list['text']).'</td>'.(($i % 2 == 0) ? '</tr><tr><td class="colhead" align="center" colspan="4"></td></tr>' : '');
    $i++;
} //=== end while
$HTMLOUT.= (($i % 2 == 0) ? '<td class="one" align="center" colspan="2"></td></tr>' : '');
$HTMLOUT.= '</table><p align="center"><a class="altlink" href="users.php">Find Member / Browse Member List</a></p>';
echo stdhead('Shit list for '.htmlsafechars($CURUSER['username'])).$HTMLOUT.stdfoot();
?>
