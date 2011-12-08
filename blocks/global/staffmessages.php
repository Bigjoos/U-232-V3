<?php
   if($INSTALLER09['staffmsg_alert'] && $CURUSER['class'] >= UC_STAFF) {
	 $answeredby = $mc1->get_value('staff_mess_');
	 if ($answeredby === false) {
	 $res1 = sql_query("SELECT count(id) FROM staffmessages WHERE answeredby = 0");
	 list($answeredby) = mysqli_fetch_row($res1);
	 $mc1->cache_value('staff_mess_', $answeredby, $INSTALLER09['expires']['alerts']);
	 }
	 if ($answeredby > 0) {
	 $htmlout .= "<li>
    <a class='tooltip' href='staffbox.php'><b><font color='red'>Staff Message</font></b><span class='custom info'><img src='./templates/1/images/Info.png' alt='New Staff Message' height='48' width='48' /><em>New Staff message</em>
   <b>Hey {$CURUSER['username']}! ".sprintf($lang['gl_staffmsg_alert'], $answeredby). "!</b>
   click headline above to view staff messages
   </span></a></li>";
	 }
   }
//==End
// End Class

// End File
