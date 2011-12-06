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

dbconn(false);
loggedinorreturn();


    $lang = array_merge( load_language('global'), load_language('friends') );
    $stdhead = array(/** include the css **/'css' => array('friends'));
    parked();
    $userid = isset($_GET['id']) ? (int)$_GET['id'] : $CURUSER['id'];
    $action = isset($_GET['action']) ? $_GET['action'] : '';


    if (!is_valid_id($userid))
      stderr($lang['friends_error'], $lang['friends_invalid_id']);

    if ($userid != $CURUSER["id"])
    stderr($lang['friends_error'], $lang['friends_no_access']);


    if ($action == 'add')
    {
      $targetid = 0+$_GET['targetid'];
      $type = $_GET['type'];

      if (!is_valid_id($targetid))
        stderr($lang['friends_error'], $lang['friends_invalid_id']);

      if ($type == 'friend')
      {
        $table_is = $frag = 'friends';
        $field_is = 'friendid';
      }
      elseif ($type == 'block')
      {
        $table_is = $frag = 'blocks';
        $field_is = 'blockid';
      }
      else
       stderr($lang['friends_error'], $lang['friends_unknown']);

      $r = sql_query("SELECT id FROM $table_is WHERE userid=$userid AND $field_is=$targetid") or sqlerr(__FILE__, __LINE__);
      if (mysqli_num_rows($r) == 1)
       stderr($lang['friends_error'], sprintf($lang['friends_already'], htmlspecialchars($table_is)));
       

      sql_query("INSERT INTO $table_is VALUES (0,$userid, $targetid)") or sqlerr(__FILE__, __LINE__);
      $mc1 -> delete_value('Blocks_'.$userid);
      $mc1 -> delete_value('Friends_'.$userid);
      $mc1 -> delete_value('Blocks_'.$targetid);
      $mc1 -> delete_value('Friends_'.$targetid);
      header("Location: {$INSTALLER09['baseurl']}/friends.php?id=$userid#$frag");
      die;
    }

    if ($action == 'delete')
    {
      $targetid = (int)$_GET['targetid'];
      $sure = isset($_GET['sure']) ? htmlspecialchars($_GET['sure']) : false;
      $type = isset($_GET['type']) ? ($_GET['type'] == 'friend' ? 'friend' : 'block') : stderr($lang['friends_error'], 'LoL');

      if (!is_valid_id($targetid))
      stderr($lang['friends_error'], $lang['friends_invalid_id']);

      if (!$sure)
        stderr("{$lang['friends_delete']} $type", sprintf($lang['friends_sure'], $type, $userid, $type, $targetid) );

      if ($type == 'friend')
      {
        sql_query("DELETE FROM friends WHERE userid=$userid AND friendid=$targetid") or sqlerr(__FILE__, __LINE__);
        $mc1 -> delete_value('Friends_'.$userid);
        $mc1 -> delete_value('Friends_'.$targetid);
        if (mysqli_affected_rows($GLOBALS["___mysqli_ston"]) == 0)
         stderr($lang['friends_error'], $lang['friends_no_friend']);
        $frag = "friends";
      }
      elseif ($type == 'block')
      {
        sql_query("DELETE FROM blocks WHERE userid=$userid AND blockid=$targetid") or sqlerr(__FILE__, __LINE__);
        $mc1 -> delete_value('Blocks_'.$userid);
        $mc1 -> delete_value('Blocks_'.$targetid);
        if (mysqli_affected_rows($GLOBALS["___mysqli_ston"]) == 0)
        stderr($lang['friends_error'], $lang['friends_no_block']);
        $frag = "blocks";
      }
      else
      stderr($lang['friends_error'], $lang['friends_unknown']);

      header("Location: {$INSTALLER09['baseurl']}/friends.php?id=$userid#$frag");
      die;
    }

    $res = sql_query("SELECT * FROM users WHERE id=$userid") or sqlerr(__FILE__, __LINE__);
    $user = mysqli_fetch_assoc($res) or stderr($lang['friends_error'], $lang['friends_no_user']);

    $HTMLOUT = '';
    
    $res = sql_query("SELECT f.friendid as id, u.username AS name, u.username, u.class, u.avatar, u.title, u.leechwarn, u.chatpost, u.pirate, u.king, u.donor, u.warned, u.enabled, u.last_access FROM friends AS f LEFT JOIN users as u ON f.friendid = u.id WHERE userid=$userid ORDER BY name") or sqlerr(__FILE__, __LINE__);
    
    $count = mysqli_num_rows($res);
    $friends = '';
    
    if( !$count)
    {
      $friends = "<em>{$lang['friends_friends_empty']}.</em>";
    }
    else
    {
      
      while ($friend = mysqli_fetch_assoc($res))
      {
        $title = $friend["title"];
        if (!$title)
          $title = get_user_class_name($friend["class"]);
        
        $userlink = "<a href='userdetails.php?id={$friend['id']}'><b></b></a>";
        $userlink .= format_username($friend) . " ($title)<br />{$lang['friends_last_seen']} " . get_date( $friend['last_access'],'');
        
        $delete = "<span class='btn'><a href='friends.php?id=$userid&amp;action=delete&amp;type=friend&amp;targetid={$friend['id']}'>{$lang['friends_remove']}</a></span>";
          
        $pm = "&nbsp;<span class='btn'><a href='pm_system.php?action=send_message&amp;receiver={$friend['id']}'>{$lang['friends_pm']}</a></span>";
          
        $avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($friend["avatar"]) : "");
        if (!$avatar)
          $avatar = "{$INSTALLER09['pic_base_url']}default_avatar.gif";
          
        $friends .= "<div style='border: 1px solid black;padding:5px;'>".($avatar ? "<img width='50px' src='$avatar' style='float:right;' alt='' />" : ""). "<p >{$userlink}<br /><br />{$delete}{$pm}</p></div><br />";
        
      }
      
    }


    $res = sql_query("SELECT b.blockid as id, u.username AS name, u.username, u.class, u.donor, u.warned, u.leechwarn, u.chatpost, u.pirate, u.king, u.enabled, u.last_access FROM blocks AS b LEFT JOIN users as u ON b.blockid = u.id WHERE userid=$userid ORDER BY name") or sqlerr(__FILE__, __LINE__);
    
    $blocks = '';
    
    if(mysqli_num_rows($res) == 0)
    {
      $blocks = "{$lang['friends_blocks_empty']}<em>.</em>";
    }
    else
    {

      while ($block = mysqli_fetch_assoc($res))
      {
        $blocks .= "<div style='border: 1px solid black;padding:5px;'>";
        $blocks .= "<span class='btn' style='float:center;'><a href='friends.php?id=$userid&amp;action=delete&amp;type=block&amp;targetid={$block['id']}'>{$lang['friends_delete']}</a></span><br />";
        $blocks .= "<p><a href='userdetails.php?id={$block['id']}'><b></b></a>";
        $blocks .= format_username($block) . "</p></div><br />";
        
      }
      
    }
    $HTMLOUT .= '
    <!--<div ><img src="images/global.design/friends.png" alt="" title="Friends" class="global_image" width="25"/></div>
    <div >'.htmlentities($user['username'], ENT_QUOTES).'\'s friends</div><br />-->
    <div >';
    //$HTMLOUT .= "<div style='border: 1px solid black;padding:5px;'><span class='btn' style='float:right;'>{$lang['friends_personal']} ".htmlspecialchars($user['username'])."</span>";
    $HTMLOUT .= "<br /><table class='main' width='90%' border='0' cellspacing='0' cellpadding='0'>
    <!--<tr>
    <td class='colhead'><h2  style='width:50%; text-align:left;'><a name='friends'>{$lang['friends_friends_list']}</a></h2></td>
    <td align='right' class='colhead'><h2  style='width:50%; text-align:right; vertical-align:top;'><a name='blocks'>{$lang['friends_blocks_list']}</a></h2></td>
    </tr>-->
    <tr>
    <td style='padding:10px;width:50%;'><br />$friends</td>
    <td align='right' style='padding:10px; vertical-align:top;'><br />$blocks</td>
    </tr>
    </table>";
    
    $HTMLOUT .= "<p><a href='users.php'><b>{$lang['friends_user_list']}</b></a></p>";

    $HTMLOUT .= "<!--</div>--></div>";
    
    echo stdhead('Friends', true, $stdhead) . $HTMLOUT . stdfoot();

?>
