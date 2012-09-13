<?php
/* Flush all torrents mod */
//=== flush torrents staff or members own torrents
if ($CURUSER['class'] >= UC_STAFF || $CURUSER['id'] == $user['id']) {
    $HTMLOUT.= '<tr valign="top"><td class="rowhead"><a name="flush"></a>Flush&nbsp;torrents</td>
			<td align="left">
			<form method="post" id="form" action="" name="flush_thing">
			<input id="id" type="hidden" value="'.(int)$user['id'].'" name="id" />
			<input id="action2" type="hidden" value="flush_torrents" name="action2" />
			<span id="success" style="display:none;color:green;font-weight: bold;">Torrents Flushed from the system. You may now start your client again!<br />
			Please remember to put the seat down.</span>
			<span id="flush_error" style="display:none;color:red;font-weight: bold;">*** Error Torrents not flushed ***<br />
			Try again in a few minutes, or wait. The tracker updates every 15 minutes.</span>
			<span id="flush">Ensure all torrents have been stopped before clicking this button.
			<br /><input id="flush_button" type="submit" value="Flush Torrents!" class="btn" name="flush_button"/>
			<br /><span style="font-size: x-small;color:red;">*all flushes are logged, please do not abuse this feature*</span></span>
			</form>
			</td></tr>';
}
//==end
// End Class
// End File
