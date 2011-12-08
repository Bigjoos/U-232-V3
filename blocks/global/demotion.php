<?php
//==Temp demotion
if ($CURUSER['override_class'] != 255 && $CURUSER) // Second condition needed so that this box isn't displayed for non members/logged out members.
{
$htmlout .= "<li>
<a class='tooltip' href='./restoreclass.php'><b><font color='red'>{$lang['gl_temp_demotion']}</font></b><span class='custom info'><img src='./templates/1/images/Info.png' alt='Demotion' height='48' width='48' /><em>{$lang['gl_temp_demotion1']}</em>   
{$lang['gl_temp_demotion2']}</span></a></li>";
}
//==End
// End Class

// End File