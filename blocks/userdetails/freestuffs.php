<?php
//==Freeleech info
$HTMLOUT .= "<tr><td class='rowhead'>Freeleech Slots</td><td align='left'>".(int)$user['freeslots']."</td></tr>";
$HTMLOUT .= "<tr><td class='rowhead'>Freeleech Status</td><td align='left'>".($user['free_switch'] != 0 ? 'FREE Status '.($user['free_switch'] > 1 ? 'Expires: '.get_date($user['free_switch'], 'DATE').' ('.mkprettytime($user['free_switch'] - TIME_NOW).' to go) <br />':'Unlimited<br />'):'None')."</td></tr>";
//==end   
// End Class

// End File
