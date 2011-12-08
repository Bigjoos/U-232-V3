<?php
/* Flush all torrents mod */
if ($CURUSER['class'] >= UC_STAFF){
$username = $user['username'];
$HTMLOUT .= "<tr><td class='rowhead' width='1%'>{$lang['userdetails_flush']}</td><td align='left' width='99%'>{$lang['userdetails_flush1']}<a href='{$INSTALLER09['baseurl']}/staffpanel.php?tool=flush&amp;action=flush&amp;id=$id'><b>".htmlspecialchars($username)."</b></a></td></tr>";
}
//==end
// End Class

// End File