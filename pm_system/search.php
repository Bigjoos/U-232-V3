<?php
$num_resault = $and_member = '';
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
            //=== get post / get stuff
            $keywords = (isset($_POST['keywords']) ? htmlspecialchars($_POST['keywords']) : '');
            $member = (isset($_POST['member']) ? htmlspecialchars($_POST['member']) : '');
            $all_boxes = (isset($_POST['all_boxes']) ? intval($_POST['all_boxes']) : '');
            $sender_reciever = ($mailbox >= 1 ? 'sender' : 'receiver');

            //== query stuff
            $what_in_out = ($mailbox >= 1 ? 'AND receiver = '.$CURUSER['id'].' AND saved = \'yes\'' : 'AND sender = '.$CURUSER['id'].' AND saved = \'yes\'');
            $location = (isset($_POST['all_boxes']) ? 'AND location != 0' : 'AND location = '.$mailbox);
            $limit = (isset($_POST['limit']) ? intval($_POST['limit']) : 25); 
            $as_list_post = (isset($_POST['as_list_post']) ? intval($_POST['as_list_post']) : 2); 
            $desc_asc = (isset($_POST['ASC']) == 1 ? 'ASC' : 'DESC');

            //=== search in
            $subject = (isset($_POST['subject']) ? intval($_POST['subject']) : '');
            $text = (isset($_POST['text']) ? intval($_POST['text']) : '');
            $member_sys =(isset($_POST['system']) ? 'system' : '');

            //=== get sort and check to see if it's ok...
            $possible_sort = array('added', 'subject', 'sender', 'receiver', 'relevance');      
            $sort = (isset($_GET['sort']) ? htmlspecialchars($_GET['sort']) : (isset($_POST['sort']) ? htmlspecialchars($_POST['sort']) : 'relevance'));
            
                if (!in_array($sort, $possible_sort)) 
                    {
                    stderr('Error', 'A ruffian that will swear, drink, dance, revel the night, rob, murder and commit the oldest of sins the newest kind of ways.');
                    }
                else
                    {
                    $sort = htmlspecialchars(isset($_POST['sort']));
                    }

        //=== Try finding a user with specified name
        if ($member)
            {
            $res_username = sql_query('SELECT id, username, class, warned, suspended, leechwarn, chatpost, pirate, king, enabled, donor FROM users WHERE LOWER(username)=LOWER('.sqlesc($member).') LIMIT 1') or sqlerr(__FILE__,__LINE__);
            $arr_username = mysqli_fetch_assoc($res_username);
            
                if (mysqli_num_rows($res_username) === 0) 
                    stderr('Error','Sorry, there is no member with that username.');
                    
            //=== if searching by member...
            $and_member = ($mailbox >= 1 ? ' AND sender = '.$arr_username['id'].' AND saved = \'yes\' ' : ' AND receiver = '.$arr_username['id'].' AND saved = \'yes\' ');
            $the_username = print_user_stuff($arr_username);
            }

                if ($member_sys)
                    {
                    $and_member = ' AND sender = 0 ';
                    $the_username = '<span style="font-weight: bold;">sys-bot</span>';
                    }

            //=== get all boxes
            $res = sql_query('SELECT boxnumber, name FROM pmboxes WHERE userid = '.$CURUSER['id']. ' ORDER BY boxnumber') or sqlerr(__FILE__,__LINE__);
            
                $get_all_boxes = '<select name="box">
                                            <option class="body" value="1" '.($mailbox == PM_INBOX ? 'selected="selected"' : '').'>Inbox</option>
                                            <option class="body" value="-1" '.($mailbox == PM_SENTBOX ? 'selected="selected"' : '').'>Sentbox</option>
                                            <option class="body" value="-2" '.($mailbox == PM_DRAFTS ? 'selected="selected"' : '').'>drafts</option>';
                                            
                while ($row = mysqli_fetch_assoc($res))
                    {
                    $get_all_boxes .= '<option class="body" value="'.$row['boxnumber'].'" '.($row['boxnumber'] == $mailbox ? 'selected="selected"' : '').'>'.htmlspecialchars($row['name']).'</option>';
                    }
                
                $get_all_boxes .= '</select>';
                    
    //=== make up page
    //echo stdhead('Search Messages'); 

    $HTMLOUT .= '<h1>Search Messages</h1>'.$top_links.'
        <form action="pm_system.php" method="post">
        <input type="hidden" name="action"  value="search" />
    <table border="0" cellspacing="0" cellpadding="5" align="center" style="max-width:800px">
    <tr>
        <td class="colhead" align="left" colspan="2">Search</td>
    </tr>
    <tr>
        <td class="one" align="right" valign="middle"><span style="font-weight: bold;">Search terms:</span></td>
        <td class="one" align="left"><input type="text" class="search" name="keywords" value="'.$keywords.'" /> [ words to search for. common words are ignored ]</td>
    </tr>
    <tr>
        <td class="one" align="right" valign="middle"><span style="font-weight: bold;">Search box:</span></td>
        <td class="one" align="left">'.$get_all_boxes.'</td>
    </tr>
    <tr>
        <td class="one" align="right" valign="middle"><span style="font-weight: bold;">Search all boxes:</span></td>
        <td class="one" align="left"><input name="all_boxes" type="checkbox" value="1" '.($all_boxes == 1 ? ' checked="checked"' : '').' /> [ if checked the above box selection will be ignored ]</td>
    </tr>
    <tr>
        <td class="one" align="right" valign="middle"><span style="font-weight: bold;">By member:</span></td>
        <td class="one" align="left"><input type="text" class="member" name="member" value="'.$member.'" /> [ search messages by this member only ]</td>
    </tr>
    <tr>
        <td class="one" align="right" valign="middle"><span style="font-weight: bold;">System messages:</span></td>
        <td class="one" align="left"><input name="system" type="checkbox" value="system" '.($member_sys == 'system' ? ' checked="checked"' : '').' /> System [ search system messages only ]</td>
    </tr>
    <tr>
        <td class="one" align="right" valign="middle"><span style="font-weight: bold;">Search in:</span></td>
        <td class="one" align="left"><input name="subject" type="checkbox" value="1" '.($subject == 1 ? ' checked="checked"' : '').' /> subject [ default ] 
        <input name="text" type="checkbox" value="1" '.($text === 1 ? ' checked="checked"' : '').' /> message text [ select one or both. if none selected, both are assumed ]</td>
    </tr>
    <tr>
        <td class="one" align="right" valign="middle"><span style="font-weight: bold;">Sort by:</span></td>
        <td class="one" align="left">
        <select name="sort">
            <option value="relevance" '.($sort === 'relevance' ? ' selected="selected"' : '').'>Relevance</option>
            <option value="subject" '.($sort === 'subject' ? ' selected="selected"' : '').'>Subject</option>
            <option value="added" '.($sort === 'added' ? ' selected="selected"' : '').'>Added</option>
            <option value="'.$sender_reciever.'" '.($sort === $sender_reciever ? ' selected="selected' : '').'>Member</option>
        </select>
            <input name="ASC" type="radio" value="1" '.((isset($_POST['ASC']) && $_POST['ASC'] == 1) ? ' checked="checked"' : '').' /> Ascending 
            <input name="ASC" type="radio" value="2" '.((isset($_POST['ASC']) && $_POST['ASC'] == 2 || !isset($_POST['ASC'])) ? ' checked="checked"' : '').' />  Descending</td>
    </tr>
    <tr>
        <td class="one" align="right" valign="middle"><span style="font-weight: bold;">Show:</span></td>
        <td class="one" align="left">
        <select name="limit">
            <option value="25"'.(($limit == 25 || !$limit) ? ' selected="selected"' : '').'>first 25 results</option>
            <option value="50"'.($limit == 50 ? ' selected="selected"' : '').'>first 50 results</option>
            <option value="75"'.($limit == 75 ? ' selected="selected"' : '').'>first 75 results</option>
            <option value="100"'.($limit == 100 ? ' selected="selected"' : '').'>first 100 results</option>
            <option value="150"'.($limit == 150 ? ' selected="selected"' : '').'>first 150 results</option>
            <option value="200"'.($limit == 200 ? ' selected="selected"' : '').'>first 200 results</option>
            <option value="1000"'.($limit == 1000 ? ' selected="selected"' : '').'>all results</option>
        </select></td>
    </tr>'.($limit < 100 ?'
    <tr>
        <td class="one" align="right" valign="middle"><span style="font-weight: bold;">Display as:</span></td>
        <td class="one" align="left"><input name="as_list_post" type="radio" value="1" '.($as_list_post == 1 ? ' checked="checked"' : '').' /> <span style="font-weight: bold;">List </span> 
        <input name="as_list_post" type="radio" value="2" '.($as_list_post == 2 ? ' checked="checked"' : '').' /> <span style="font-weight: bold;"> Message</span></td>
    </tr>' : '').'
    <tr>
        <td colspan="2" align="center" class="one">
        <input type="submit" class="button" name="change" value="search" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" /></td>
    </tr>
    </table></form>';

    //=== do the search and print page :)
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {

        //=== remove common words first. add more if you like...
        $remove_me = array('a','the','and','to','for','by');
        $search = preg_replace('/\b('.implode('|',$remove_me).')\b/','',$keywords);

        //=== do the search!
        switch(true)
            {
            //=== if only member name is entered and no search string... get all messages by that member
            case (!$keywords && $member):
                    $res_search = sql_query("SELECT * FROM messages WHERE sender = ".sqlesc($arr_username['id'])." AND saved = 'yes' $location AND receiver = ".$CURUSER['id']." ORDER BY ".sqlesc($sort)." $desc_asc LIMIT ".$limit) or sqlerr(__FILE__,__LINE__);
                break;
            //=== if system entered default both ...  
            case (!$keywords && $member_sys):
                    $res_search = sql_query("SELECT * FROM messages WHERE sender = 0 $location AND receiver = ".$CURUSER['id']." ORDER BY ".sqlesc($sort)." $desc_asc LIMIT ".$limit) or sqlerr(__FILE__,__LINE__);
                break;
            //=== if just subject
            case ($subject && !$text):
                    $res_search = sql_query("SELECT *, MATCH(subject) AGAINST(".sqlesc($search)." IN BOOLEAN MODE) AS relevance FROM messages WHERE ( MATCH(subject) AGAINST (".sqlesc($search)." IN BOOLEAN MODE) ) $and_member $location $what_in_out ORDER BY ".sqlesc($sort)." $desc_asc LIMIT ".$limit) or sqlerr(__FILE__,__LINE__);
                break;
            //=== if just message
            case (!$subject && $text):
                    $res_search = sql_query("SELECT *, MATCH(msg) AGAINST(".sqlesc($search)." IN BOOLEAN MODE) AS relevance FROM messages WHERE ( MATCH(msg) AGAINST (".sqlesc($search)." IN BOOLEAN MODE) ) $and_member $location $what_in_out ORDER BY ".sqlesc($sort)." $desc_asc LIMIT ".$limit) or sqlerr(__FILE__,__LINE__);;
                break;
            //=== if subject and message
            case ($subject && $text || !$subject && !$text):
                    $res_search = sql_query("SELECT *, ( (1.3 * (MATCH(subject) AGAINST (".sqlesc($search)." IN BOOLEAN MODE))) + (0.6 * (MATCH(msg) AGAINST (".sqlesc($search)." IN BOOLEAN MODE)))) AS relevance FROM messages WHERE ( MATCH(subject,msg) AGAINST (".sqlesc($search)." IN BOOLEAN MODE) ) $and_member $location $what_in_out ORDER BY ".sqlesc($sort)." $desc_asc LIMIT ".$limit) or sqlerr(__FILE__,__LINE__);
                break;
            }

        $num_resault = mysqli_num_rows($res_search);

    //=== show the search resaults \o/o\o/o\o/
    $HTMLOUT .='<h1>your search for '.($keywords ? '"'.$keywords.'"' : ($member ? 'member '.htmlspecialchars($arr_username['username']).'\'s PMs' : ($member_sys ? 'system messages' : '' ))).'</h1>
        <div style="text-align: center;">'.($num_resault < $limit ? 'returned' : 'showing first').' <span style="font-weight: bold;">'.$num_resault.'</span> 
        match'.($num_resault === 1 ? '' : 'es').'! '.($num_resault === 0 ? ' better luck next time...' : '').'</div>';

    //=== let's make the table
    $HTMLOUT .=($num_resault > 0 ? '
        <form action="pm_system.php" method="post"  name="messages" onsubmit="return ValidateForm(this,\'pm\')">
        <input type="hidden" name="action" value="move_or_delete_multi" />
    <table border="0" cellspacing="0" cellpadding="5" align="center" style="max-width:800px">
        '.($as_list_post == 2 ? '' : '
    <tr>
        <td colspan="5" class="colhead" align="center"><h1>'.$mailbox_name.'</h1></td>
    </tr>
    <tr>
        <td width= "1%" class="colhead">&nbsp;</td>
        <td class="colhead">Subject </td>
        <td width="35%" class="colhead">'.($mailbox === PM_SENTBOX ? 'Sent to' : 'Sender').'</td>
        <td width="1%" class="colhead"> Date</td>
        <td width="1%" class="colhead"></td>
    </tr>') : '');

            while ($row = mysqli_fetch_assoc($res_search))
                {
                //=======change colors
                $count2= (++$count2)%2;
                $class = ($count2 == 0 ? 'one' : 'two');
                $class2 = ($count2 == 0 ? 'two' : 'one');

                //=== if not searching one member...
                if (!$member)
                    {
                    $res_username = sql_query('SELECT id, username, warned, suspended, enabled, donor, leechwarn, chatpost, pirate, king, class FROM users WHERE id = '.$row[$sender_reciever].' LIMIT 1') or sqlerr(__FILE__,__LINE__);
                    $arr_username = mysqli_fetch_assoc($res_username);
                    $the_username = print_user_stuff($arr_username);
                    }

            //=== if searching all boxes...
            $arr_box = ($row['location'] == 1 ? 'Inbox' : ($row['location'] == -1 ? 'Sentbox' : ($row['location'] == -2 ? 'Drafts' : '')));

                if($all_boxes && $arr_box === '')
                    {
                    $res_box_name = sql_query('SELECT name FROM pmboxes WHERE userid = '.$CURUSER['id']. ' AND boxnumber = '.$row['location']) or sqlerr(__FILE__,__LINE__);
                    $arr_box_name = mysqli_fetch_assoc($res_box_name);
                    $arr_box = htmlspecialchars($arr_box_name['name']);
                    }

        //==== highlight search terms... from Jaits search forums mod
        $body = str_ireplace($keywords,'<span style="background-color:yellow;font-weight:bold;color:black;">'.$keywords.'</span>', format_comment($row['msg']));
        $subject = str_ireplace($keywords,'<span style="background-color:yellow;font-weight:bold;color:black;">'.$keywords.'</span>', htmlspecialchars($row['subject']));

    //=== print the damn thing ... if it's as a list or as posts...
    $HTMLOUT .= ($as_list_post == 2 ? '
    <tr>
        <td class="colhead" colspan="4">message from: '.($row[$sender_reciever] == 0 ? 'Sys-bot' : $the_username).'</td>
    </tr>
    <tr>
        <td class="'.$class2.'" colspan="4"><span style="font-weight: bold;">subject:</span> 
        <a class="altlink"  href="pm_system.php?action=view_message&amp;id='.$row['id'].'">'.($row['subject'] !== '' ? $subject : 'No Subject').'</a> 
        '.($all_boxes ? '[ found in '.$arr_box.' ]' : '').'  at: '.get_date($row['added'], '').' GMT ['.get_date($row['added'],'',0,1).']</td>
    </tr>
    <tr>
        <td class="'.$class.'" colspan="4">'.$body.'</td>
    </tr>' : '
    <tr>
        <td class="'.$class.'"><img src="pic/readpm.gif" title="Message" alt="Read" /></td>
        <td class="'.$class.'"><a class="altlink" href="pm_system.php?action=view_message&amp;id='.$row['id'].'">'.($row['subject'] !== '' ? $subject : 'No Subject').'</a> '.($all_boxes ? '[ found in '.$arr_box.' ]' : '').'</td>
        <td class="'.$class.'">'.($row[$sender_reciever] == 0 ? 'Sys-bot' : $the_username).'</td>
        <td class="'.$class.'">'.get_date($row['added'], '').' GMT ['.get_date($row['added'],'',0,1).'] </td>
        <td class="'.$class.'"><input type="checkbox" name="pm[]" value="'.$row['id'].'" /></td>
    </tr>');
    }
}

//=== the bottom
$HTMLOUT .= ($num_resault > 0 ?'
    <tr>
        <td colspan="4" align="right" class="colhead">
        <a class="altlink" href="javascript:SetChecked(1,\'pm[]\')"> select all</a> - 
        <a class="altlink" href="javascript:SetChecked(0,\'pm[]\')">un-select all</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="submit" class="button" name="move" value="Move to" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" /> '.get_all_boxes().' or  
        <input type="submit" class="button" name="delete" value="Delete" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" /> selected messages.</td>
    </tr>
    </table></form>' : '').'<br />';
?>