<?php
// happy hour
if ($CURUSER) {
    require_once (INCL_DIR.'function_happyhour.php');
    if (happyHour("check")) {
        $htmlout.= "
        <li>
        <a class='tooltip' href='browse.php?cat=".happyCheck("check")."'><b><font color='red'>{$lang['gl_happyhour']}</font></b><span class='custom info'><img src='./templates/1/images/Info.png' alt='Happy Hour' height='48' width='48' /><em>{$lang['gl_happyhour']}</em>
        {$lang['gl_happyhour1']} ".((happyCheck("check") == 255) ? "{$lang['gl_happyhour2']}" : "{$lang['gl_happyhour3']}")."<br /><font color='red'><b> ".happyHour("time")." </b></font> {$lang['gl_happyhour4']}</span></a></li>";
    }
}
//==
// End Class
// End File
