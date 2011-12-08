<?php
$browser = $_SERVER['HTTP_USER_AGENT'];
   if(preg_match("/MSIE/i",$browser))//browser is IE
   {
   $HTMLOUT .="
   <div class='headline'>
    Warning - Internet Explorer Browser
   </div>
   <div class='headbody'>
   It appears as though you are running Internet Explorer, this site was <b>NOT</b> intended to be viewed with internet explorer and chances are it will not look right and may not even function correctly.
   {$INSTALLER09['site_name']} suggests that you <a href='http://browsehappy.com'><b>browse happy</b></a> and consider switching to one of the many better alternatives.
   <br /><br /><center><a href='http://www.mozilla.com/firefox'><img border='0' alt='Get Firefox!' title='Get Firefox!' src='{$INSTALLER09['pic_base_url']}getfirefox.gif' /></a>
   <br /><strong>Get a SAFER browser !</center>
   </div><br />";
   }
//==
// End Class

// End File
