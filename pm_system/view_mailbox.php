<?php
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

    //=== get mailbox name   
    if ($mailbox > 1)
        {
        //== get name of PM box if not in or out
        $res_box_name = sql_query('SELECT name FROM pmboxes WHERE userid = '.$CURUSER['id'].' AND boxnumber='.$mailbox.' LIMIT 1') or sqlerr(__FILE__,__LINE__);
        $arr_box_name = mysqli_fetch_row($res_box_name);
        
        if (mysqli_num_rows($res_box_name) === 0) 
                stderr('Error','Invalid Mailbox');
                
        $mailbox_name = htmlspecialchars($arr_box_name[0]);

            $other_box_info = '<p align="center"><span style="color: red;">***</span><span style="font-weight: bold;">please note:</span>
                                            you have a max of <span style="font-weight: bold;">'.$maxbox.'</span> PMs for all mail boxes that are not either 
                                            <span style="font-weight: bold;">inbox</span> or <span style="font-weight: bold;">sentbox</span>.</p>';
        }

//==== get count from PM boxs & get image & % box full
    //=== get stuff for the pager
    $res_count = sql_query('SELECT COUNT(id) FROM messages WHERE '.
                                            ($mailbox === PM_INBOX ? 'receiver = '.$CURUSER['id'].' AND location = 1' : 
                                            ($mailbox === PM_SENTBOX ? 'sender = '.$CURUSER['id'].' AND (saved = \'yes\' || unread= \'yes\') AND draft = \'no\' ' : 
                                            'receiver = '.$CURUSER['id'].' AND location = '.$mailbox))) or sqlerr(__FILE__,__LINE__);
    $arr_count = mysqli_fetch_row($res_count);
    $messages = $arr_count[0];
    
    //==== get count from PM boxs & get image & % box full
    $filled = $messages > 0 ? (($messages / $maxbox) * 100) : 0;
    //$filled = (($messages / $maxbox) * 100);
    $mailbox_pic = get_percent_completed_image(round($filled), $maxpic);
    $num_messages = number_format($filled,0);
    $link = 'pm_system.php?action=view_mailbox&amp;box='.$mailbox.($perpage < $messages ? '&amp;page='.$page : '').'&amp;order_by='.$order_by.$desc_asc;
	  list($menu, $LIMIT) = pager_new($messages, $perpage, $page, $link); 
    //=== get message info we need to display then all nice and tidy like \o/
    $res = sql_query('SELECT m.id AS message_id, m.sender, m.receiver, m.added, m.subject, m.unread, m.urgent,
                            u.id, u.username, u.uploaded, u.downloaded, u.warned, u.suspended, u.enabled, u.donor, u.class, u.avatar, u.offensive_avatar, u.leechwarn, u.chatpost, u.pirate, u.king, f.id AS friend, b.id AS blocked
                            FROM messages AS m 
                            LEFT JOIN users AS u ON u.id=m.'.($mailbox === PM_SENTBOX ? 'receiver' : 'sender').' 
                            LEFT JOIN friends AS f ON f.userid = '.$CURUSER['id'].' AND f.friendid = m.sender
                            LEFT JOIN blocks AS b ON b.userid = '.$CURUSER['id'].' AND b.blockid = m.sender
                            WHERE '.($mailbox === PM_INBOX ? 'receiver = '.$CURUSER['id'].' AND location = 1' : 
                            ($mailbox === PM_SENTBOX ? 'sender = '.$CURUSER['id'].' AND (saved = \'yes\' || unread= \'yes\') AND draft = \'no\' ' : 'receiver = '.$CURUSER['id'].' AND location = '.$mailbox)).' 
                            ORDER BY '.$order_by.(isset($_GET['ASC']) ? ' ASC ' : ' DESC ').$LIMIT) or sqlerr(__FILE__,__LINE__);

//=== Start Page
//echo stdhead(htmlspecialchars($mailbox_name)); 

//=== let's make the table
$HTMLOUT .= $h1_thingie.$top_links.'
    <a name="pm"></a>
    <form action="pm_system.php" method="post" name="checkme" onsubmit="return ValidateForm(this,\'pm\')">
    
    <table border="0" cellspacing="0" cellpadding="5" align="center" style="max-width:900px">
    <tr>
        <td class="colhead" align="center" colspan="5"><span class="font_size_1">'.$messages.' / '.$maxbox.'</span>
        '.$spacer.'<span class="font_size_5">'.$mailbox_name.'</span>'.$spacer.'
        <span class="font_size_1">[ '.$num_messages.'% full ]</span><br />
        '.$mailbox_pic.'</td>
    </tr>
    <tr>
    <td class="one" align="right" colspan="5">
    '.insertJumpTo($mailbox).$other_box_info.($perpage < $messages ? $menu.'' : '').'
    </td>
    </tr>
    <tr>
        <td class="colhead" width="1%">&nbsp;&nbsp;
        
        <input type="hidden" name="action" value="move_or_delete_multi" /></td>
        <td class="colhead" align="left"><a class="altlink" href="pm_system.php?action=view_mailbox&amp;box='.$mailbox.
        ($perpage == 20 ? '' : '&amp;perpage='.$perpage).($perpage < $messages ? '&amp;page='.$page : '').'&amp;order_by=subject'.$desc_asc.'#pm" title="order by subject '.$desc_asc_2.'">Subject</a></td>
        <td class="colhead" align="left"><a class="altlink" href="pm_system.php?action=view_mailbox&amp;box='.$mailbox.
        ($perpage == 20 ? '' : '&amp;perpage='.$perpage).($perpage < $messages ? '&amp;page='.$page : '').'&amp;order_by=username'.$desc_asc.'#pm" title="order by member name '.$desc_asc_2.'">'.($mailbox === PM_SENTBOX ? 'Sent to' : 'Sender').'</a></td>
        <td class="colhead" align="left"><a class="altlink" href="pm_system.php?action=view_mailbox&amp;box='.$mailbox.
        ($perpage == 20 ? '' : '&amp;perpage='.$perpage).($perpage < $messages ? '&amp;page='.$page : '').'&amp;order_by=added'.$desc_asc.'#pm" title="order by date '.$desc_asc_2.'">Date</a></td>
        <td class="colhead" width="1%"></td>
    </tr>';
    
    if (mysqli_num_rows($res) === 0)
        {
        $HTMLOUT .= '
        <tr>
            <td class="two" align="center" colspan="5"><span style="font-weight: bold;">No Messages. in '.$mailbox_name.'</span></td>
        </tr>';
        }
    else 
        {

        while ($row = mysqli_fetch_assoc($res))  
            {
                //=======change colors
                $count2= (++$count2)%2;
                $class = ($count2 == 0 ? 'one' : 'two');
                $class2 = ($count2 == 0 ? 'two' : 'one');

                            //=== if not system or themselves, see if  they are a friend yet?
                            if ($mailbox === PM_DRAFTS || $row['id'] === 0)
                                {
                                $friends = '';
                                }
                            else
                                {
                              if ($row['friend'] > 0)
                              $friends = ' [ <span class="font_size_1"><a href="friends.php?action=delete&amp;type=friend&amp;targetid='.$row['id'].'">remove from friends</a></span> ]';
                              elseif ($row['blocked'] > 0)
                              $friends = ' [ <span class="font_size_1"><a href="friends.php?action=delete&amp;type=block&amp;targetid='.$row['id'].'">remove from blocks</a></span> ]';
                              else
                              $friends = ' [ <span class="font_size_1"><a href="friends.php?action=add&amp;type=friend&amp;targetid='.$row['id'].'">add to friends</a></span> ] 
                                           [ <span class="font_size_1"><a href="friends.php?action=add&amp;type=block&amp;targetid='.$row['id'].'">add to blocks</a></span> ]';
                              }

                $subject = (!empty($row['subject']) ? htmlspecialchars($row['subject']) : 'No Subject');
                $who_sent_it = ($row['id'] == 0 ? '<span style="font-weight: bold;">System</span>' : print_user_stuff($row).$friends);
                $read_unread = ($row['unread'] === 'yes' ? '<img src="pic/pn_inboxnew.gif" title="Unread Message" alt="Unread" />' : '<img src="pic/pn_inbox.gif" title="Read Message" alt="Read" />');
                $extra = ($row['unread'] === 'yes' ? $spacer.'[ <span style="color: red;"> un-read</span> ]' : '').($row['urgent'] === 'yes' ? $spacer.'<span style="color: red;">URGENT!</span>' : '');
                $avatar = (($CURUSER['avatars'] === 'no' || $CURUSER['show_pm_avatar'] === 'no' || $row['id'] == 0)? '' : (empty($row['avatar']) ? '
                <img width="40" src="pic/default_avatar.gif" alt="no avatar" />' : (($row['offensive_avatar'] === 'yes' && $CURUSER['view_offensive_avatar'] === 'no') ? 
                '<img width="40" src="pic/fuzzybunny.gif" alt="fuzzy!" />' : '<img width="40" src="'.htmlspecialchars($row['avatar']).'" alt="avatar" />')));

                $HTMLOUT .= '
                <tr>
                    <td class="'.$class.'" align="center">'.$read_unread.'</td>
                    <td class="'.$class.'" align="left"><a class="altlink"  href="pm_system.php?action=view_message&amp;id='.$row['message_id'].'">'.$subject.'</a>'.$extra.'</td>
                    <td class="'.$class.'" align="left">'.$avatar.$who_sent_it.'</td>
                    <td class="'.$class.'" align="left">'.get_date($row['added'], '').'</td>
                    <td class="'.$class.'" align="center"><input type="checkbox" name="pm[]" value="'.$row['message_id'].'" /></td>
                </tr>';
                }
        }

    //=== per page drop down 
    $per_page_drop_down = '<form action="pm_system.php" method="post"><select name="amount_per_page" onchange="location = this.options[this.selectedIndex].value;">';
    $i = 20;
    while($i <= ($maxbox > 200 ? 200 : $maxbox))
        {
        $per_page_drop_down .= '<option class="body" value="'.$link.'&amp;change_pm_number='.$i.'"  '.($CURUSER['pms_per_page'] == $i ? ' selected="selected"' : '').'>'.$i.' PMs per page</option>';
        $i = ($i < 100 ? $i = $i + 10 : $i = $i + 25);
        }
        $per_page_drop_down .= '</select><input type="hidden" name="box" value="'.$mailbox.'" /></form>';

    //=== avatars on off 
$show_pm_avatar_drop_down = '
    <form method="post" action="pm_system.php">
        <select name="show_pm_avatar" onchange="location = this.options[this.selectedIndex].value;">
            <option value="'.$link.'&amp;show_pm_avatar=yes" '.($CURUSER['show_pm_avatar'] === 'yes' ? ' selected="selected"' : '').'>show avatars on PM list</option>
            <option value="'.$link.'&amp;show_pm_avatar=no" '.($CURUSER['show_pm_avatar'] === 'no' ? ' selected="selected"' : '').'>don\'t show avatars on PM list</option>
        </select>
            <input type="hidden" name="box" value="'.$mailbox.'" /></form>';

//=== the bottom      
$HTMLOUT .= (mysqli_num_rows($res) > 0 ? '
    <tr>
        <td colspan="5" align="right" class="'.$class2.'">
         [ <a class="altlink" href="javascript:SetChecked(1,\'pm[]\')"> select all</a> ] [ <a class="altlink" href="javascript:SetChecked(0,\'pm[]\')">un-select all</a> ]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$spacer.'
        <input type="submit" class="button" name="move" value="Move to" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" /> '.get_all_boxes().' or  
        <input type="submit" class="button" name="delete" value="Delete" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" /> selected messages.</td>
    </tr>
    <tr>
        <td colspan="5" align="left">
        <img src="pic/pn_inboxnew.gif" title="Unread Message" alt="Unread" /> Unread Messages.<br />
        <img src="pic/pn_inbox.gif" title="Read Message" alt="Read" /> Read Messages.</td>
    </tr>' : '').'
    </table>
        '.($perpage < $messages ? ''.$menu.'<br />' : '').'
    <div align="center">
    <table border="0" cellspacing="0" cellpadding="5" align="center" style="max-width:800px">
    <tr>

        <td align="center">'.$per_page_drop_down.'</td>
        <td align="center">'.$show_pm_avatar_drop_down.'</td>
    </tr>
    </table><br /></div></form>';
?>