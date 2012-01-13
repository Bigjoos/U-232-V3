<?php
   if($INSTALLER09['uploadapp_alert'] && $CURUSER['class'] >= UC_STAFF) {
   if(($newapp = $mc1->get_value('new_uploadapp_')) === false) {
   $res_newapps = sql_query("SELECT count(id) FROM uploadapp WHERE status = 'pending'");
   list($newapp) = mysqli_fetch_row($res_newapps);
   $mc1->cache_value('new_uploadapp_', $newapp, $INSTALLER09['expires']['alerts']);
   }
   if ($newapp > 0){
   $htmlout.="
   <li>
   <a class='tooltip' href='staffpanel.php?tool=uploadapps&amp;action=app'><b><font color='red'>New Uploader App Waiting</font></b><span class='custom info'><img src='./templates/1/images/Info.png' alt='Upload App' height='48' width='48' /><em>New Uploader App Waiting</em>
   Hey {$CURUSER['username']}! $newapp uploader application" . ($newapp > 1 ? "s" : "") . " to be dealt with 
   click at the headling above here to view the application</span></a></li>";
   }
   }
//==End
// End Class

// End File
