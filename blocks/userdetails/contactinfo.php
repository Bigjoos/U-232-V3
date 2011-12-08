<?php
//=== member contact stuff
	  $HTMLOUT .= (($CURUSER['class'] >= UC_STAFF || $user['show_email'] === 'yes') ? '
		<tr>
			<td class="rowhead">Email</td>
			<td align="left"><a class="altlink" href="mailto:'.htmlspecialchars($user['email']).'"  title="click to email" target="_blank"><img src="pic/email.gif" alt="email" width="25" /> Send Email</a></td>
		</tr>' : '').($user['google_talk'] !== '' ? '
		<tr>
			<td class="rowhead">Google Talk</td>
			<td align="left"><a class="altlink" href="http://talkgadget.google.com/talkgadget/popout?member='.htmlspecialchars($user['google_talk']).'" title="click for google talk gadget"  target="_blank"><img src="pic/forums/google_talk.gif" alt="google_talk" /> Open</a></td>
		</tr>' : '').($user['msn'] !== '' ? '
		<tr>
			<td class="rowhead">MSN</td>
			<td align="left"><a class="altlink" href="http://members.msn.com/'.htmlspecialchars($user['msn']).'" target="_blank" title="click to see msn details"><img src="pic/forums/msn.gif" alt="msn" /> Open</a></td>
		</tr>' : '').($user['yahoo'] !== '' ? '
		<tr>
			<td class="rowhead">Yahoo</td>
			<td align="left"><a class="altlink" href="http://webmessenger.yahoo.com/?im='.htmlspecialchars($user['yahoo']).'" target="_blank" title="click to open yahoo"><img src="pic/forums/yahoo.gif" alt="yahoo" /> Open</a></td>
		</tr>' : '').($user['aim'] !== '' ? '
		<tr>
			<td class="rowhead">AIM</td>
			<td align="left"><a class="altlink" href="http://aim.search.aol.com/aol/search?s_it=searchbox.webhome&amp;q='.htmlspecialchars($user['aim']).'" target="_blank" title="click to search on aim... you will need to have an AIM account!"><img src="pic/forums/aim.gif" alt="AIM" /> Open</a></td>
		</tr>' : '').($user['icq'] !== '' ? '
		<tr>
			<td class="rowhead">ICQ</td>
			<td align="left"><a class="altlink" href="http://people.icq.com/people/&amp;uin='.htmlspecialchars($user['icq']).'" title="click to open icq page" target="_blank"><img src="pic/forums/icq.gif" alt="icq" /> Open</a></td>
		</tr>' : '').($user['website'] !== '' ? '
		<tr>
			<td class="rowhead">Website </td>
			<td align="left"><a class="altlink" href="'.htmlspecialchars($user['website']).'" target="_blank" title="click to go to website"><img src="pic/forums/www.gif" width="18" alt="website" /> '.htmlspecialchars($user['website']).'</a></td>
		</tr>' : '');
//==end
// End Class

// End File