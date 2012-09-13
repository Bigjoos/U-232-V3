<?php
//== Online time
if ($user['onlinetime'] > 0) {
    $onlinetime = time_return($user['onlinetime']);
    $HTMLOUT.= "<tr><td class='rowhead' width='1%'>Total Online</td><td align='left' width='99%'>{$onlinetime}</td></tr>";
} else {
    $onlinetime = "This user has no online time recorded";
    $HTMLOUT.= "<tr><td class='rowhead' width='1%'>Total Online</td><td align='left' width='99%'>{$onlinetime}</td></tr>";
}
// end
// End Class
// End File
