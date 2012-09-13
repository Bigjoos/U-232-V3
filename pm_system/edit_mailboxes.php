<?php
//== Php poop
$all_my_boxes = $curuser_cache = $user_cache = '';
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
if (isset($_POST['action2'])) {
    $good_actions = array(
        'add',
        'edit_boxes',
        'change_pm',
        'message_settings'
    );
    $action2 = (isset($_POST['action2']) ? strip_tags($_POST['action2']) : '');
    $worked = $deleted = '';
    if (!in_array($action2, $good_actions)) stderr('Error', 'His wit\'s as thick as a Tewkesbury mustard.');
    //=== add more boxes...
    switch ($action2) {
    case 'change_pm':
        $change_pm_number = (isset($_POST['change_pm_number']) ? intval($_POST['change_pm_number']) : 20);
        sql_query('UPDATE users SET pms_per_page = '.sqlesc($change_pm_number).' WHERE id = '.sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);
        $mc1->begin_transaction('user'.$CURUSER['id']);
        $mc1->update_row(false, array(
            'pms_per_page' => $change_pm_number
        ));
        $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
        $mc1->begin_transaction('MyUser_'.$CURUSER['id']);
        $mc1->update_row(false, array(
            'pms_per_page' => $change_pm_number
        ));
        $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
        header('Location: pm_system.php?action=edit_mailboxes&pm=1');
        die();
        break;

    case 'add':
        //=== make sure they posted something...
        if ($_POST['new'] === '') stderr('Error', 'to add new PM boxes you MUST enter at least one PM box name!');
        //=== Get current highest box number
        $res = sql_query('SELECT boxnumber FROM pmboxes WHERE userid = '.sqlesc($CURUSER['id']).' ORDER BY boxnumber  DESC LIMIT 1') or sqlerr(__FILE__, __LINE__);
        $box_arr = mysqli_fetch_row($res);
        $box = ($box_arr[0] < 2 ? 2 : ($box_arr[0] + 1));
        //=== let's add the new boxes to the DB
        $new_box = $_POST['new'];
        foreach ($new_box as $key => $add_it) {
            if (validusername($add_it) && $add_it !== '') {
                $name = htmlsafechars($add_it);
                sql_query('INSERT INTO pmboxes (userid, name, boxnumber) VALUES ('.sqlesc($CURUSER['id']).', '.sqlesc($name).', '.sqlesc($box).')') or sqlerr(__FILE__, __LINE__);
                $mc1->delete_value('get_all_boxes'.$CURUSER['id']);
                $mc1->delete_value('insertJumpTo'.$CURUSER['id']);
            }
            ++$box;
            $worked = '&boxes=1';
        }
        //=== redirect back with messages :P
        header('Location: pm_system.php?action=edit_mailboxes'.$worked);
        die();
        break;
        //=== edit boxes
        
    case 'edit_boxes':
        //=== get info
        $res = sql_query('SELECT * FROM pmboxes WHERE userid='.sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);
        if (mysqli_num_rows($res) === 0) stderr(' Error', 'No Mailboxes to edit');
        while ($row = mysqli_fetch_assoc($res)) {
            //=== if name different AND safe, update it
            if (validusername($_POST['edit'.$row['id']]) && $_POST['edit'.$row['id']] !== '' && $_POST['edit'.$row['id']] !== $row['name']) {
                $name = htmlsafechars($_POST['edit'.$row['id']]);
                sql_query('UPDATE pmboxes SET name='.sqlesc($name).' WHERE id='.sqlesc($row['id']).' LIMIT 1') or sqlerr(__FILE__, __LINE__);
                $mc1->delete_value('get_all_boxes'.$CURUSER['id']);
                $mc1->delete_value('insertJumpTo'.$CURUSER['id']);
                $worked = '&name=1';
            }
            //=== if name is empty, delete the box(es) and send the PMs back to the inbox..
            if ($_POST['edit'.$row['id']] == '') {
                //=== get messages to move
                $remove_messages_res = sql_query('SELECT id FROM messages WHERE location='.sqlesc($row['boxnumber']).'  AND receiver='.sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);
                //== move the messages to the inbox
                while ($remove_messages_arr = mysqli_fetch_assoc($remove_messages_res)) {
                    sql_query('UPDATE messages SET location=1 WHERE id='.sqlesc($remove_messages_arr['id'])) or sqlerr(__FILE__, __LINE__);
                }
                //== delete the box
                sql_query('DELETE FROM pmboxes WHERE id='.sqlesc($row['id']).'  LIMIT 1') or sqlerr(__FILE__, __LINE__);
                $mc1->delete_value('get_all_boxes'.$CURUSER['id']);
                $mc1->delete_value('insertJumpTo'.$CURUSER['id']);
                $deleted = '&box_delete=1';
            }
        }
        //=== redirect back with messages :P
        header('Location: pm_system.php?action=edit_mailboxes'.$deleted.$worked);
        die();
        break;
        //=== message settings     yes/no/friends
        
    case 'message_settings':
        $updateset = array();
        $change_pm_number = (isset($_POST['change_pm_number']) ? intval($_POST['change_pm_number']) : 20);
        $updateset[] = 'pms_per_page = '.sqlesc($change_pm_number);
        $curuser_cache['pms_per_page'] = $change_pm_number;
        $user_cache['pms_per_page'] = $change_pm_number;
        $show_pm_avatar = ((isset($_POST['show_pm_avatar']) && $_POST['show_pm_avatar'] == 'yes') ? 'yes' : 'no');
        $updateset[] = 'show_pm_avatar = '.sqlesc($show_pm_avatar);
        $curuser_cache['show_pm_avatar'] = $show_pm_avatar;
        $user_cache['show_pm_avatar'] = $show_pm_avatar;
        $acceptpms = ((isset($_POST['acceptpms']) && $_POST['acceptpms'] == 'yes') ? 'yes' : ((isset($_POST['acceptpms']) && $_POST['acceptpms'] == 'friends') ? 'friends' : 'no'));
        $updateset[] = 'acceptpms = '.sqlesc($acceptpms);
        $curuser_cache['acceptpms'] = $acceptpms;
        $user_cache['acceptpms'] = $acceptpms;
        $save_pms = ((isset($_POST['save_pms'])) ? 'yes' : 'no');
        $updateset[] = 'savepms = '.sqlesc($save_pms);
        $curuser_cache['savepms'] = $save_pms;
        $user_cache['savepms'] = $save_pms;
        $deletepms = ((isset($_POST['deletepms']) && $_POST['deletepms'] == 'yes') ? 'yes' : 'no');
        $updateset[] = 'deletepms = '.sqlesc($deletepms);
        $curuser_cache['deletepms'] = $deletepms;
        $user_cache['deletepms'] = $deletepms;
        $pmnotif = (isset($_POST['pmnotif']) ? $_POST['pmnotif'] : '');
        $emailnotif = (isset($_POST['emailnotif']) ? $_POST['emailnotif'] : '');
        $notifs = ($pmnotif == 'yes' ? '[pm]' : '');
        $notifs.= ($emailnotif == 'yes' ? '[email]' : '');
        $cats = genrelist2();
        if (count($cats) > 0);
        foreach ($cats as $cat) {
            $subcategoriesarray = isset($_POST["cats".$cat['tabletype']]) ? $_POST["cats".$cat['tabletype']] : array();
            if (count($subcategoriesarray) > 0) {
                foreach ($subcategoriesarray as $subcategory) {
                    if (!is_valid_id($subcategory)) stderr("Error", "Not valid category");
                    if (validsubcat($subcategory, $cats));
                    $notifs.= "[cat$subcategory]";
                }
            }
        }
        $updateset[] = "notifs = ".sqlesc($notifs)."";
        $curuser_cache['notifs'] = $notifs;
        $user_cache['notifs'] = $notifs;
        if ($curuser_cache) {
            $mc1->begin_transaction('MyUser_'.$CURUSER['id']);
            $mc1->update_row(false, $curuser_cache);
            $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
        }
        if ($user_cache) {
            $mc1->begin_transaction('user'.$CURUSER['id']);
            $mc1->update_row(false, $user_cache);
            $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
        }
        sql_query('UPDATE users SET '.implode(', ', $updateset).' WHERE id = '.sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);
        $worked = '&pms=1';
        //=== redirect back with messages :P
        header('Location: pm_system.php?action=edit_mailboxes'.$worked);
        die();
        break;
    } //=== end of case / switch
    
} //=== end of $_POST stuff
//=== main page here :D
$res = sql_query('SELECT * FROM pmboxes WHERE userid='.sqlesc($CURUSER['id']).' ORDER BY name ASC') or sqlerr(__FILE__, __LINE__);
if (mysqli_num_rows($res) > 0) {
    //=== get all PM boxes for editing
    while ($row = mysqli_fetch_assoc($res)) {
        //==== get count from PM boxes
        $res_count = sql_query('SELECT COUNT(id) FROM messages WHERE  location = '.sqlesc($row['boxnumber']).' AND receiver = '.sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);
        $arr_count = mysqli_fetch_row($res_count);
        $messages = $arr_count[0];
        $all_my_boxes.= '
                    <tr>
                        <td class="one" align="right">
                        <form action="pm_system.php" method="post">
                        <input type="hidden" name="action" value="edit_mailboxes" />
                        <input type="hidden" name="action2" value="edit_boxes" />
                        Box # '.($row['boxnumber'] - 1).' <span style="font-weight: bold;">'.htmlsafechars($row['name']).':</span></td>
                        <td class="one" colspan="2" align="left"><input type="text" name="edit'.(0 + $row['id']).'" value="'.htmlsafechars($row['name']).'" style="text_default" /> [ contains '.$messages.' messages ]</td>
                    </tr>';
    }
    $all_my_boxes.= '
                    <tr>
                        <td class="one"></td>
                        <td class="one" colspan="2" align="left">You may edit the names of your PM boxes here.<br />
                        If you wish to delete 1 or more PM boxes, remove the name from the text field leaving it blank.</td>
                    </tr>
                    <tr>
                        <td class="one"></td>
                        <td class="one" align="left" width colspan="2"><span style="font-weight: bold;">Please note!!!</span>
                        <ul>
                            <li>If you delete the name of one or more boxes,  all messages in that directory will be sent to your inbox!!!</li>
                            <li>If you wish to delete the messages as well, you can do that from the <a class="altlink" href="pm_system.php?action=view_mailbox">main page</a>.</li>
                        </ul></td>
                    </tr>
                    <tr>
                        <td class="one" align="center" width colspan="3">
                        <input type="submit" class="button" value="Edit" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" /></form></td>
                    </tr>';
} else {
    $all_my_boxes.= '
                    <tr>
                        <td class="one"></td>
                        <td class="one" colspan="2" align="center"><span style="font-weight: bold;">There are currently no PM boxes to edit.</span><br /></td>
                    </tr>';
}
//=== per page drop down
$per_page_drop_down = '<select name="change_pm_number">';
$i = 20;
while ($i <= ($maxbox > 200 ? 200 : $maxbox)) {
    $per_page_drop_down.= '<option class="body" value="'.$i.'" '.($CURUSER['pms_per_page'] == $i ? ' selected="selected"' : '').'>'.$i.' PMs per page</option>';
    $i = ($i < 100 ? $i = $i + 10 : $i = $i + 25);
}
$per_page_drop_down.= '</select>';
//==Subcats
$cats = genrelist2();
$wherecatina = array();
if ($CURUSER["notifs"]) {
    $i = 0;
    foreach ($cats as $cat) {
        $subcats = $cat['subcategory'];
        if (count($subcats) > 0) {
            foreach ($subcats as $subcat) {
                if (strpos($CURUSER["notifs"], "[cat{$subcat['id']}]") !== false) {
                    $wherecatina[] = $subcat['id'];
                }
            }
        }
        if (count($subcats) > 0) {
            foreach ($subcats as $subcat) {
                if (in_array($subcat['id'], $wherecatina)) {
                    $cats[$i]['checked'] = true;
                } else {
                    $cats[$i]['checked'] = false;
                    break;
                }
            }
        }
        $i++;
    }
}
$categories = categories_table($cats, $wherecatina);
//=== make up page
$HTMLOUT.= '
<script type="text/javascript">
/*<![CDATA[*/
$(document).ready(function()	{
//=== cats
$("#cat_open").click(function() {
  $("#cat").slideToggle("slow", function() {

  });
});
});
/*]]>*/
</script>';
$HTMLOUT.= '<h1>Mailbox Manager / Message settings</h1>'.$h1_thingie.$top_links.'
        <form action="pm_system.php" method="post">
        <input type="hidden" name="action" value="edit_mailboxes" />
        <input type="hidden" name="action2" value="add" />
    <table border="0" cellspacing="5" cellpadding="5" align="center" style="max-width:800px">
    <tr>
        <td class="colhead" align="left" colspan="3"><h1>Add mail boxes</h1></td>
    </tr>
    <tr>
        <td class="one" align="left"></td>
        <td class="one" align="left" colspan="2">As a '.get_user_class_name($CURUSER['class']).' you may have up to '.$maxboxes.' 
        PM box'.($maxboxes !== 1 ? 'es' : '').' other then your in, sent and draft boxes.<br />
        Currently you have '.mysqli_num_rows($res).' custom box'.(mysqli_num_rows($res) !== 1 ? 'es' : '').' 
        You may add up to '.($maxboxes - mysqli_num_rows($res)).' more extra mailboxes.<br /><br />
        <span style="font-weight: bold;">The following characters can be used: </span> a-z, A-Z, 1-9, - and _ 
        [ all other characters will be ignored ]<br /></td>
    </tr>';
//=== make loop for oh let's say 5 boxes...
for ($i = 1; $i < 6; $i++) {
    $HTMLOUT.= '
            <tr>
                <td class="one" align="right"><span style="font-weight: bold;">box '.$i.':</span></td>
                <td class="one" align="left"><input type="text" name="new[]" class="text_default" maxlength="100" /></td>
                <td class="one" align="left"></td>
            </tr>';
}
$HTMLOUT.= '
    <tr>
        <td class="one" align="left"></td>
        <td class="one" align="left">Only fill in add as many boxes that you would like to add and click "Add" <br />
        Blank entries will be ignored.</td>
        <td class="one" align="left"><input type="submit" class="button_tiny" name="move" value="Add" onmouseover="this.className=\'button_tiny_hover\'" onmouseout="this.className=\'button_tiny\'" /></form></td>
    </tr>
    <tr>
        <td class="colhead" colspan="3" align="left"><h1>Edit / Delete mail boxes</h1></td>
    </tr>
        '.$all_my_boxes.'
    <tr>
        <td class="colhead" colspan="3" align="left"><h1>Message settings</h1></td>
    </tr>
    <tr>
        <td class="one" align="right"><span style="font-weight: bold;">PMs per page:</span></td>
        <td class="one" align="left">
        <form action="pm_system.php" method="post">
        <input type="hidden" name="action" value="edit_mailboxes" />
        <input type="hidden" name="action2" value="message_settings" />
        '.$per_page_drop_down.' [ Select how many PMs you would like to see per page. ]</td>
        <td class="one" align="left"></td>
    </tr>
    <tr>
        <td class="one" align="right"><span style="font-weight: bold;">Avatars:</span></td>
        <td class="one" align="left">
        <select name="show_pm_avatar">
        <option value="yes" '.($CURUSER['show_pm_avatar'] === 'yes' ? ' selected="selected"' : '').'>show avatars on view mailbox</option>
        <option value="no" '.($CURUSER['show_pm_avatar'] === 'no' ? ' selected="selected"' : '').'>don\'t show avatars on view mailbox</option>
        </select> [ Show avatars when viewing your mailboxes. ]</td>
        <td class="one" align="left"></td>
    </tr>
    <tr>
        <td class="one" align="right"><span style="font-weight: bold;">Accept PMs:</span></td>
        <td class="one" align="left">
        <input type="radio" name="acceptpms" '.($CURUSER['acceptpms'] == 'yes' ? ' checked="checked"' : '').' value="yes" />All (except blocks)
        <input type="radio" name="acceptpms" '.($CURUSER['acceptpms'] == 'friends' ? ' checked="checked"' : '').' value="friends" />Friends only
        <input type="radio" name="acceptpms" '.($CURUSER['acceptpms'] == 'no' ? ' checked="checked"' : '').' value="no" />Staff only</td>
        <td class="one" align="left"></td>
    </tr>
    <tr>
        <td class="one" align="right"><span style="font-weight: bold;">Save PMs:</span></td>
        <td class="one" align="left"><input type="checkbox" name="save_pms" '.($CURUSER['savepms'] == 'yes' ? ' checked="checked"' : '').'  /> [ Default for "Save PM to Sentbox" ]</td>
        <td class="one" align="left"></td>
    </tr>
    <tr>
        <td class="one" align="right"><span style="font-weight: bold;">Delete PMs:</span></td>
        <td class="one" align="left"><input type="checkbox" name="deletepms" '.($CURUSER['deletepms'] == 'yes' ? ' checked="checked"' : '').' /> [ Default for "Delete PM on reply" ]</td>
        <td class="one" align="left"></td>
    </tr>
    <tr>
        <td class="one" align="right"><span style="font-weight: bold;">Email notification:</span></td>
        <td class="one" align="left"><input type="checkbox" name="pmnotif" '.(strpos($CURUSER['notifs'], '[pm]') !== false ? ' checked="checked"' : '').'  value="yes" /> Notify me when I have received a PM</td>
        <td class="one" align="left"></td>
    </tr>
    <tr>
        <td class="one" align="right"></td>
        <td class="one" align="left"><input type="checkbox" name="emailnotif" '.(strpos($CURUSER['notifs'], '[email]') !== false ? ' checked="checked"' : '').'  value="yes" /> Notify me when a torrent is uploaded in one of my default browsing categories.</td>
        <td class="one" align="left"></td>
    </tr>
    <tr>
        <td class="one" align="right" valign="top"><span style="font-weight: bold;">Categories:</span></td>
        <td class="one" align="left"><a class="altlink"  title="Click for more info" id="cat_open" style="font-weight:bold;cursor:pointer;">show / hide categories</a> [ for torrent notifications ]
        <div id="cat" style="display:none;">Your default categories can be changed here as well.<br />'.$categories.'</div></td>
        <td class="one" align="left"></td>
    </tr>
    <tr>
        <td class="one" align="center" colspan="3">
        <input type="submit" class="button" value="change" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" /></form></td>
    </tr>
    </table></form>';
?>
