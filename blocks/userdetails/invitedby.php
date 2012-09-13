<?php
//=== invite stuff - who invited this member by snuggles
if ($user['invitedby'] > 0) {
    //=== Fetch inviter info
    $res_get_invitor = sql_query('SELECT id, class, username, warned, suspended, enabled, donor, chatpost, leechwarn, pirate, king FROM users WHERE id='.sqlesc($user['invitedby'])) or sqlerr(__FILE__, __LINE__);
    $user_get_invitor = mysqli_fetch_assoc($res_get_invitor);
    $HTMLOUT.= '<tr><td class="rowhead">Invited&nbsp;By</td><td align="left">'.format_username($user_get_invitor).'</td></tr>';
} else {
    $HTMLOUT.= '<tr><td class="rowhead">Invited&nbsp;By</td><td align="left"><b>Open Signups</b></td></tr>';
}
//=== members invites by snuggles
$rez_invited = sql_query('SELECT id, class, username, email, uploaded, downloaded, status, warned, suspended, enabled, donor, email, ip, chatpost, leechwarn, pirate, king FROM users WHERE invitedby = '.sqlesc($user['id']).' ORDER BY added');
$inviteted_by_this_member = '';
if (mysqli_num_rows($rez_invited) < 1) $inviteted_by_this_member.= 'No invitees yet.';
else {
    $inviteted_by_this_member.= '<table width="100%" border="1" cellspacing="0" cellpadding="5">
		<tr><td class="colhead"><b>Username / IP</b></td>
		<td class="colhead"><b>Email</b></td>
		<td class="colhead"><b>Uploaded</b></td>
		'.($INSTALLER09['ratio_free'] ? '' : '<td class="colhead"><b>Downloaded</b></td>').'
		<td class="colhead"><b>Ratio</b></td>
		<td class="colhead"><b>Status</b></td></tr>';
    while ($arr_invited = mysqli_fetch_assoc($rez_invited)) {
        $inviteted_by_this_member.= '<tr><td>'.($arr_invited['status'] == 'pending' ? htmlsafechars($arr_invited['username']) : format_username($arr_invited).'<br />'.$arr_invited['ip']).'</td>
		<td>'.htmlsafechars($arr_invited['email']).'</td>
		<td>'.mksize($arr_invited['uploaded']).'</td>
		'.($INSTALLER09['ratio_free'] ? '' : '<td>'.mksize($arr_invited['downloaded']).'</td>').'
		<td>'.member_ratio($arr_invited['uploaded'], $INSTALLER09['ratio_free'] ? '0' : $arr_invited['downloaded']).'</td>
		<td>'.($arr_invited['status'] == 'confirmed' ? '<span style="color: green;">Confirmed</span></td></tr>' : '<td><span style="color: red;">Pending</span></td></tr>');
    }
    $inviteted_by_this_member.= '</table>';
}
$the_flip_box_5 = '[ <a name="invites"></a><a class="altlink" href="#invites" onclick="javascript:flipBox(\'5\')" name="b_5" title="Open / Close Members Invites">view <img onclick="javascript:flipBox(\'5\')" src="pic/panel_on.gif" name="b_5" style="vertical-align:middle;"  width="8" height="8" alt="Open / Close Members Invitees" title="Open / Close Members Invitees" /></a> ] [ <a class="altlink" href="staffpanel.php?tool=invite_tree&amp;action=invite_tree&amp;id='.(int)$user['id'].'" title="Click to view members invite tree">view invite tree</a> ]';
$HTMLOUT.= '<tr><td class="rowhead">Invitees</td><td align="left">'.(mysqli_num_rows($rez_invited) > 0 ? $the_flip_box_5.'<div align="left" id="box_5" style="display:none">
    <br />'.$inviteted_by_this_member.'</div>' : 'No invitees yet.').'</td></tr>';
// End Class
// End File
