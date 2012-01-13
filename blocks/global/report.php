<?php
   if($INSTALLER09['report_alert'] && $CURUSER['class'] >= UC_STAFF) {
   if(($delt_with = $mc1->get_value('new_report_')) === false) {
   $res_reports = sql_query("SELECT COUNT(id) FROM reports WHERE delt_with = '0'");
   list($delt_with) = mysqli_fetch_row($res_reports);
   $mc1->cache_value('new_report_', $delt_with, $INSTALLER09['expires']['alerts']);
   }
   if ($delt_with > 0){
   $htmlout.="
    <li>
    <a class='tooltip' href='staffpanel.php?tool=reports&amp;action=reports'><b><font color='red'>Reports</font></b><span class='custom info'><img src='./templates/1/images/Info.png' alt='New Report' height='48' width='48' /><em>New Report</em>
    Hey {$CURUSER['username']}! $delt_with report" . ($delt_with > 1 ? "s" : "") . " to be dealt with<br />
    click headline above to view reports
    </span></a></li>";
   }
   }
//==End
// End Class

// End File
