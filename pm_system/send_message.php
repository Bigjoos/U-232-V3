<?php
$draft = $subject = $body = '';
flood_limit('messages');
//=== don't allow direct access
if (!defined('BUNNY_PM_SYSTEM')) {
    $HTMLOUT = '';
    $HTMLOUT.= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
//=== check to see if it's a preview or a post
if (isset($_POST['buttonval']) && $_POST['buttonval'] == 'Send') {
    //=== check to see they have everything or...
    $receiver = sqlesc(isset($_POST['receiver']) ? intval($_POST['receiver']) : 0);
    $subject = sqlesc(htmlsafechars($_POST['subject']));
    $body = sqlesc(trim($_POST['body']));
    $save = ((isset($_POST['save']) && $_POST['save'] === 1) ? '1' : '0');
    $delete = sqlesc((isset($_POST['delete']) && $_POST['delete'] !== 0) ? intval($_POST['delete']) : 0);
    $urgent = sqlesc((isset($_POST['urgent']) && $_POST['urgent'] == 'yes' && $CURUSER['class'] >= UC_STAFF) ? 'yes' : 'no');
    $returnto = htmlsafechars(isset($_POST['returnto']) ? $_POST['returnto'] : '');
    //$returnto = htmlsafechars($_POST['returnto']);
    //=== get user info from DB
    $res_receiver = sql_query('SELECT id, acceptpms, notifs, email, class, username FROM users WHERE id='.sqlesc($receiver)) or sqlerr(__FILE__, __LINE__);
    $arr_receiver = mysqli_fetch_assoc($res_receiver);
    if (!is_valid_id(intval($_POST['receiver'])) || !is_valid_id($arr_receiver['id'])) stderr('Error', 'Member not found!!!');
    if (!isset($_POST['body'])) stderr('Error', 'No body text... Please enter something to send!');
    //=== allow suspended users to PM / forward to staff only
    if ($CURUSER['suspended'] === 'yes') {
        $res = sql_query('SELECT class FROM users WHERE id = '.sqlesc($receiver)) or sqlerr(__FILE__, __LINE__);
        $row = mysqli_fetch_assoc($res);
        if ($row['class'] < UC_STAFF) stderr('Error', 'Your account is suspended, you may only contact staff members!');
    }
    //=== make sure they have space
    $res_count = sql_query('SELECT COUNT(*) FROM messages WHERE receiver = '.sqlesc($receiver).' AND location = 1') or sqlerr(__FILE__, __LINE__);
    $arr_count = mysqli_fetch_row($res_count);
    if ($arr_count[0] >= $maxbox && $CURUSER['class'] < UC_STAFF) stderr('Sorry', 'Members PM box is full.');
    //=== Make sure recipient wants this message
    if ($CURUSER['class'] < UC_STAFF) {
        $should_i_send_this = ($arr_receiver['acceptpms'] == 'yes' ? 'yes' : ($arr_receiver['acceptpms'] == 'no' ? 'no' : ($arr_receiver['acceptpms'] == 'friends' ? 'friends' : '')));
        switch ($should_i_send_this) {
        case 'yes':
            $r = sql_query('SELECT id FROM blocks WHERE userid = '.sqlesc($receiver).' AND blockid = '.sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);
            $block = mysqli_fetch_row($r);
            if ($block[0] > 0) stderr('Refused', htmlsafechars($arr_receiver['username']).' has blocked PMs from you.');
            break;

        case 'friends':
            $r = sql_query('SELECT id FROM friends WHERE userid = '.sqlesc($receiver).' AND friendid = '.sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);
            $friend = mysqli_fetch_row($r);
            if ($friend[0] > 0) stderr('Refused', htmlsafechars($arr_receiver['username']).' only accepts PMs from members in their friends list.');
            break;

        case 'no':
            stderr('Refused', htmlsafechars($arr_receiver['username']).' does not accept PMs.');
            break;
        }
    }
    //=== ok all is well... post the message :D
    sql_query('INSERT INTO messages (poster, sender, receiver, added, msg, subject, saved, location, urgent) VALUES 
                            ('.sqlesc($CURUSER['id']).', '.sqlesc($CURUSER['id']).', '.sqlesc($receiver).', '.TIME_NOW.', '.$body.', '.$subject.', '.$save.', 1,'.$urgent.')') or sqlerr(__FILE__, __LINE__);
    $mc1->delete_value('inbox_new_'.$receiver);
    $mc1->delete_value('inbox_new_sb_'.$receiver);
    $mc1->delete_value('shoutbox_');
    //=== make sure it worked then...
    if (mysqli_affected_rows($GLOBALS["___mysqli_ston"]) === 0) stderr('Error', 'Messages wasn\'t sent!');
    //=== if they just have to know about it right away... send them an email (if selected if profile)
    if (strpos($arr_receiver['notifs'], '[pm]') !== false) {
        $username = htmlsafechars($CURUSER['username']);
        $body = <<<EOD
You have received a PM from $username!

You can use the URL below to view the message (you may have to login).

{$INSTALLER09['baseurl']}/pm_system.php

--
{$INSTALLER09['site_name']}
EOD;
        @mail($user['email'], 'You have received a PM from '.$username.'!', $body, "From: {$INSTALLER09['site_email']}");
    }
    //=== if they don't want to keep the message they are replying to then delete it!
    if ($delete != 0) {
        //=== be sure they should be deleting this...
        $res = sql_query('SELECT saved, receiver FROM messages WHERE id='.sqlesc($delete)) or sqlerr(__FILE__, __LINE__);
        if (mysqli_num_rows($res) > 0) {
            $arr = mysqli_fetch_assoc($res);
            //if ($arr['receiver'] !== $CURUSER['id'])
            if ($arr['receiver'] != $CURUSER['id']) stderr('Quote!', 'Thou spongy prick-eared bag of guts!');
            if ($arr['saved'] == 'no') {
                sql_query('DELETE FROM messages WHERE id = '.sqlesc($delete)) or sqlerr(__FILE__, __LINE__);
            } elseif ($arr['saved'] == 'yes') {
                sql_query('UPDATE messages SET location = 0 WHERE id = '.sqlesc($delete)) or sqlerr(__FILE__, __LINE__);
            }
        }
    }
    //=== if returnto sent
    if ($returnto) header('Location: '.$returnto);
    else header('Location: pm_system.php?action=view_mailbox&sent=1');
    die();
} //=== end of takesendmessage script
//=== basic page :D
$receiver = (isset($_GET['receiver']) ? intval($_GET['receiver']) : (isset($_POST['receiver']) ? intval($_POST['receiver']) : 0));
$replyto = (isset($_GET['replyto']) ? intval($_GET['replyto']) : (isset($_POST['replyto']) ? intval($_POST['replyto']) : 0));
$returnto = htmlsafechars(isset($_POST['returnto']) ? $_POST['returnto'] : '');
if ($receiver === 0) stderr('Error', 'you can\'t PM Sys-Bot... It won\'t write you back!');
if (!is_valid_id($receiver)) stderr('Error', 'No member with that ID!');
$res_member = sql_query('SELECT username FROM users WHERE id = '.sqlesc($receiver)) or sqlerr(__FILE__, __LINE__);
$arr_member = mysqli_fetch_row($res_member);
//=== if reply
if ($replyto != 0) {
    if (!validusername($arr_member[0])) stderr('Error', 'No member with that ID!');
    //=== make sure they should be replying to this PM...
    $res_old_message = sql_query('SELECT receiver, sender, subject, msg FROM messages WHERE id = '.sqlesc($replyto)) or sqlerr(__FILE__, __LINE__);
    $arr_old_message = mysqli_fetch_assoc($res_old_message);
    //print $arr_old_message['sender'];
    //exit();
    if ($arr_old_message['sender'] == $CURUSER['id']) stderr('Error', 'Slander, whose edge is sharper than the sword, whose tongue out venoms all the worms of Nile');
    $body.= "\n\n\n-------- $arr_member[0] wrote: --------\n$arr_old_message[msg]\n";
    $subject = 'Re: '.htmlsafechars($arr_old_message['subject']);
}
//=== if preview or not replying
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject = trim($_POST['subject']);
    $body = trim($_POST['body']);
}
//=== and finally print the basic page  :D
$avatar = (($CURUSER['avatars'] === 'no') ? '' : (empty($CURUSER['avatar']) ? '
        <img width="80" src="pic/default_avatar.gif" alt="no avatar" />' : (($CURUSER['offensive_avatar'] === 'yes' && $CURUSER['view_offensive_avatar'] === 'no') ? '<img width="80" src="pic/fuzzybunny.gif" alt="fuzzy!" />' : '<img width="80" src="'.htmlsafechars($CURUSER['avatar']).'" alt="avatar" />')));
//=== Code for preview Retros code
if (isset($_POST['buttonval']) && $_POST['buttonval'] == 'Preview') {
    $HTMLOUT.= '<h1>Preview PM</h1>
    <table border="0" cellspacing="0" cellpadding="5" align="center" style="max-width:800px">
    <tr>
        <td align="left" colspan="2" class="colhead"><span style="font-weight: bold;">subject: </span>'.htmlsafechars($subject).'</td>
    </tr>
    <tr>
        <td align="center" valign="top" class="one" width="0px" id="photocol">'.$avatar.'</td>
        <td class="two" style="min-width:400px;padding:10px;vertical-align: top;text-align: left;">'.format_comment($body).'</td>
    </tr>
    </table><br />';
}
$HTMLOUT.= '<form name="compose" method="post" action="pm_system.php">
            <input type="hidden" name="action" value="send_message" />
            <input type="hidden" name="returnto" value="'.$returnto.'" />
            <input type="hidden" name="replyto" value="'.$replyto.'" />
            <input type="hidden" name="receiver" value="'.$receiver.'" />
        <h1>Message to <a class="altlink" href="userdetails.php?id='.$receiver.'">'.$arr_member[0].'</a></h1>
    <table border="0" cellspacing="0" cellpadding="5" align="center" style="max-width:800px">
    <tr>
        <td align="left" colspan="2" class="colhead">Send message</td>
    </tr>
    <tr>
        <td align="right" class="one"><span style="font-weight: bold;">Subject:</span></td>
        <td align="left" class="one"><input name="subject" type="text" class="text_default" value="'.$subject.'" /></td>
    </tr>
    <tr>
        <td align="right" class="one"><span style="font-weight: bold;">Body:</span></td>
        <td align="left" class="one">'.BBcode($body, FALSE).'</td>
    </tr>
    <tr>
        <td align="center" colspan="2" class="one">'.($CURUSER['class'] >= UC_STAFF ? '
        <input type="checkbox" name="urgent" value="yes" '.((isset($_POST['urgent']) && $_POST['urgent'] === 'yes') ? ' checked="checked"' : '').' /> 
        <span style="font-weight: bold;color:red;">Mark as URGENT!</span>' : '').'
        <input type="checkbox" name="delete" value="'.$replyto.'" '.((isset($_POST['delete']) && $_POST['delete'] > 0) ? ' checked="checked"' : ($CURUSER['deletepms'] == 'yes' ? ' checked="checked"' : '')).' />Delete PM 
        <input type="checkbox" name="save" value="1" '.((isset($_POST['draft']) && $_POST['draft'] == 1) ? ' checked="checked"' : '').' />Save PM 
        <input type="submit" class="button" name="buttonval" value="Preview" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" />
        <input type="submit" class="button" name="buttonval" value="'.((isset($_POST['draft']) && $_POST['draft'] == 1) ? 'Save' : 'Send').'" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" /></td>
    </tr>
    </table></form>';
?>
