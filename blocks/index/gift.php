<?php
   $xmasday = mktime(0,0,0,12,25,date("Y"));
   $today = mktime(date("G"), date("i"), date("s"), date("m"),date("d"),date("Y"));
   if ($CURUSER["gotgift"] == 'no' && $today <> $xmasday) {
   $HTMLOUT .="<div class='headline'>
   Xmas Gift
   </div>
   <div class='headbody'>
   <a href='{$INSTALLER09['baseurl']}/gift.php?open=1'><img src='{$INSTALLER09['pic_base_url']}gift.png' style='float: center;border-style: none;' alt='Xmas Gift' title='Xmas Gift' /></a><br /><br /><br /><br /></div><br />";
   }
//==
// End Class

// End File
