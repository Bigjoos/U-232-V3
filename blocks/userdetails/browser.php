<?php
//==Browser/Os
if ($user['browser'] != '') $browser = htmlsafechars($user['browser']);
else $browser = "No browser recorded yet";
$HTMLOUT.= "<tr><td class='rowhead'>{$lang['userdetails_user_browser']}</td><td align='left'>{$browser}</td></tr>";
//==end
// End Class
// End File
