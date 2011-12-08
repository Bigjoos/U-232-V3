<?php
$subject = $friends = '';
//=== don't allow direct access 
if (!defined('BUNNY_PM_SYSTEM')) 
{
	$HTMLOUT ='';
	$HTMLOUT .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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

    //=== Get the message
    $res = sql_query('SELECT m.*, f.id AS friend, b.id AS blocked
                            FROM messages AS m LEFT JOIN friends AS f ON f.userid = '.$CURUSER['id'].' AND f.friendid = m.sender
                            LEFT JOIN blocks AS b ON b.userid = '.$CURUSER['id'].' AND b.blockid = m.sender WHERE m.id = '.$pm_id.' AND (receiver='.$CURUSER['id'].' OR (sender='.$CURUSER['id'].' AND (saved = \'yes\' || unread= \'yes\'))) LIMIT 1') or sqlerr(__FILE__,__LINE__);
    $message = mysqli_fetch_assoc($res);

        if (!$res) 
            stderr('Error','You do not have permission to view this message.');

    //=== get user stuff
    $res_user_stuff = sql_query('SELECT id, username, uploaded, warned, suspended, enabled, donor, class, avatar, leechwarn, chatpost, pirate, king, offensive_avatar, view_offensive_avatar  
                                                    FROM users WHERE id='.($message['sender'] === $CURUSER['id'] ? sqlesc($message['receiver']) : sqlesc($message['sender']))) or sqlerr(__FILE__,__LINE__);
    $arr_user_stuff = mysqli_fetch_assoc($res_user_stuff);
    $id = (int)$arr_user_stuff['id'];

    //=== Mark message read
    sql_query('UPDATE messages SET unread=\'no\' WHERE id='.$pm_id.' AND receiver='.$CURUSER['id'].' LIMIT 1') or sqlerr(__FILE__,__LINE__);
    $mc1->delete_value('inbox_new_'.$CURUSER['id']);
    $mc1->delete_value('inbox_new_sb_'.$CURUSER['id']);

        if ($message['friend'] > 0)
            $friends = ' [ <span class="font_size_1"><a href="friends.php?action=delete&amp;type=friend&amp;targetid='.$id.'">remove from friends</a></span> ]';
        elseif ($message['blocked'] > 0)
            $friends = ' [ <span class="font_size_1"><a href="friends.php?action=delete&amp;type=block&amp;targetid='.$id.'">remove from blocks</a></span> ]';
        elseif ($id  > 0)
            $friends = ' [ <span class="font_size_1"><a href="friends.php?action=add&amp;type=friend&amp;targetid='.$id.'">add to friends</a></span> ]  
                                [ <span class="font_size_1"><a href="friends.php?action=add&amp;type=block&amp;targetid='.$id.'">add to blocks</a></span> ] ';


    $avatar = ($CURUSER['avatars'] === 'no' ? '' : (empty($arr_user_stuff['avatar']) ? '
    <img width="80" src="pic/default_avatar.gif" alt="no avatar" />' : (($arr_user_stuff['offensive_avatar'] === 'yes' && $CURUSER['view_offensive_avatar'] === 'no') ? 
    '<img width="80" src="pic/fuzzybunny.gif" alt="fuzzy!" />' : '<a href="'.htmlspecialchars($arr_user_stuff['avatar']).'"><img width="80" src="'.htmlspecialchars($arr_user_stuff['avatar']).'" alt="avatar" /></a>')));

$the_buttons = '<input type="submit" class="button_tiny" value="move" onmouseover="this.className=\'button_tiny_hover\'" onmouseout="this.className=\'button_tiny\'" /></form>
            <a class="buttlink"  href="pm_system.php?action=delete&amp;id='.$pm_id.'"><input type="submit" class="button_tiny" value="delete" onmouseover="this.className=\'button_tiny_hover\'" onmouseout="this.className=\'button_tiny\'" /></a>'.
            ($message['draft'] === 'no' ? '
            <a class="buttlink"  href="pm_system.php?action=save_or_edit_draft&amp;id='.$pm_id.'"><input type="submit" class="button" value="save as draft" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" /></a>'.
            (($id < 1 || $message['sender'] === $CURUSER['id']) ? '' : ' 
            <a class="buttlink"  href="pm_system.php?action=send_message&amp;receiver='.$message['sender'].'&amp;replyto='.$pm_id.'"><input type="submit" class="button_tiny" value="reply" onmouseover="this.className=\'button_tiny_hover\'" onmouseout="this.className=\'button_tiny\'" /></a>  
            <a class="buttlink"  href="pm_system.php?action=forward&amp;id='.$pm_id.'"><input type="submit" class="button_tiny" value="fwd" onmouseover="this.className=\'button_tiny_hover\'" onmouseout="this.className=\'button_tiny\'" /></a>  ') : '
            <a class="buttlink"  href="pm_system.php?action=save_or_edit_draft&amp;edit=1&amp;id='.$pm_id.'"><input type="submit" class="button" value="edit draft" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" /></a>
            <a class="buttlink"  href="pm_system.php?action=use_draft&amp;send=1&amp;id='.$pm_id.'"><input type="submit" class="button" value="use draft" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" /></a>');

    //=== get mailbox name  
    if ($message['location'] > 1)
        {
        //== get name of PM box if not in or out
        $res_box_name = sql_query('SELECT name FROM pmboxes WHERE userid = '.$CURUSER['id'].' AND boxnumber='.$mailbox.' LIMIT 1') or sqlerr(__FILE__,__LINE__);
        $arr_box_name = mysqli_fetch_row($res_box_name);
        
        if (mysqli_num_rows($res) === 0) 
                stderr('Error','Invalid Mailbox');
                
        $mailbox_name = htmlspecialchars($arr_box_name[0]);

            $other_box_info = '<p align="center"><span style="color: red;">***</span><span style="font-weight: bold;">please note:</span>
                                            you have a max of <span style="font-weight: bold;">'.$maxbox.'</span> PMs for all mail boxes that are not either 
                                            <span style="font-weight: bold;">inbox</span> or <span style="font-weight: bold;">sentbox</span>.</p>';
        }

//=== Display the message already!
//echo stdhead('PM '.htmlspecialchars($subject)); 

$HTMLOUT .= $h1_thingie.($message['draft'] === 'yes' ? '<h1>This is a draft</h1>' : '<h1>Mailbox: '.$mailbox_name.'</h1>').$top_links.'
    <table border="0" cellspacing="0" cellpadding="5" align="center" style="max-width:800px">
    <tr>
        <td align="center" colspan="2" class="colhead"><h1>subject: 
        <span style="font-weight: bold;">'.($message['subject'] !== '' ? htmlspecialchars($message['subject']) : 'No Subject').'</span></h1></td>
        </tr>
    <tr>
        <td align="left" colspan="2" class="one"><span style="font-weight: bold;">'.($message['sender'] === $CURUSER['id'] ? 'To' : 'From').':</span>   
        '.($arr_user_stuff['id'] == 0 ? 'System' : print_user_stuff($arr_user_stuff)).$spacer.$friends.$spacer.$spacer.'
        <span style="font-weight: bold;">sent:</span> '.get_date($message['added'], '').$spacer.
        (($message['sender'] === $CURUSER['id'] && $message['unread'] == 'yes') ? '[ <span style="font-weight: bold;color:red;">Un-read</span> ]' : '').
        ($message['urgent'] === 'yes' ? '<span style="font-weight: bold;color:red;">URGENT!</span>' : '').'</td>
    </tr>
    <tr>
        <td align="center" valign="top" class="one" width="0px" id="photocol">'.$avatar.'</td>
        <td class="two" style="min-width:400px;padding:10px;vertical-align: top;text-align: left;">'.format_comment($message['msg']).'</td>
    </tr>
    <tr>
        <td class="one" align="right" colspan="2">
        <form action="pm_system.php" method="post">
        <input type="hidden" name="id" value="'.$pm_id.'" />
        <input type="hidden" name="action" value="move" /><span style="font-weight: bold;">Move to:</span> '.get_all_boxes().$the_buttons.'</td>
    </tr></table><br />'.insertJumpTo(0);
?>