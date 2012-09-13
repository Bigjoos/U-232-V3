<?php
function calctime($val)
{
    $days = intval($val / 86400);
    $val-= $days * 86400;
    $hours = intval($val / 3600);
    $val-= $hours * 3600;
    $mins = intval($val / 60);
    $secs = $val - ($mins * 60);
    return "&nbsp;$days days, $hours hrs, $mins minutes";
}
//==Irc
if ($user['onirc'] == 'yes') {
    $ircbonus = (!empty($user['irctotal']) ? number_format($user["irctotal"] / $INSTALLER09['autoclean_interval'], 1) : '0.0');
    $HTMLOUT.= "<tr><td class='rowhead' valign='top' align='right'>Irc Bonus</td><td align='left'>{$ircbonus}</td></tr>";
    $irctotal = (!empty($user['irctotal']) ? calctime($user['irctotal']) : htmlsafechars($user['username']).' has never been on IRC!');
    $HTMLOUT.= "<tr><td class='rowhead' valign='top' align='right'>Irc Idle Time</td><td align='left'>{$irctotal}</td></tr>";
}
//==end
// End Class
// End File
