<?php
// === Staff shoutbox 09
   if ($CURUSER['show_staffshout'] === "yes") {
   $commandbutton = $refreshbutton = $smilebutton = $custombutton = $staffsmiliebutton = '';
   if ($CURUSER['class'] >= UC_STAFF){
   $staffsmiliebutton .="<span style='float:right;'><a href=\"javascript:PopStaffSmiles('staff_shbox','staff_shbox_text')\">{$lang['index_shoutbox_ssmilies']}</a></span>";
   }
   if(get_smile() != '0')
   $custombutton .="<span style='float:right;'><a href=\"javascript:PopCustomSmiles('staff_shbox','staff_shbox_text')\">{$lang['index_shoutbox_csmilies']}</a></span>";
   if ($CURUSER['class'] >= UC_STAFF){
   $commandbutton = "<span style='float:right;'><a href=\"javascript:popUp('shoutbox_commands.php')\">{$lang['index_shoutbox_commands']}</a></span>\n";}
   $refreshbutton = "<span style='float:right;'><a href='staff_shoutbox.php' target='staff_shoutbox'>{$lang['index_shoutbox_refresh']}</a></span>\n";
   $smilebutton = "<span style='float:right;'><a href=\"javascript:PopMoreSmiles('staff_shbox','staff_shbox_text')\">{$lang['index_shoutbox_smilies']}</a></span>\n";
   $HTMLOUT .= "<form action='staff_shoutbox.php' method='get' target='staff_shoutbox' name='staff_shbox' onsubmit='mysubmit()'>
   <div class='headline'>
   Staff ShoutBox
   <span class='shouthis'>";
   if ($CURUSER['class'] >= UC_STAFF){
   $HTMLOUT .= "<a href='{$INSTALLER09['baseurl']}/staffpanel.php?tool=shistory&amp;action=shistory'><b>{$lang['index_shoutbox_history']}</b></a>";
   }
   $HTMLOUT .= "</span>
   </div>
   <div class='headbody'>
   <iframe src='{$INSTALLER09['baseurl']}/staff_shoutbox.php' width='100%' height='200' frameborder='0' name='staff_shoutbox' marginwidth='0' marginheight='0'></iframe>
   <br />
	 <div align='center'>
   <b>{$lang['index_shoutbox_shout']}</b>
   <input type='text' maxlength='680' name='staff_shbox_text' size='1' style='width:500px;' />
   <input class='btn' type='submit' value='{$lang['index_shoutbox_send']}' />
   <input type='hidden' name='staff_sent' value='yes' />
   <br /> 
	 <a href=\"javascript:SmileIT(':-)','staff_shbox','staff_shbox_text')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/smile1.gif' alt='Smile' title='Smile' /></a> 
   <a href=\"javascript:SmileIT(':smile:','staff_shbox','staff_shbox_text')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/smile2.gif' alt='Smiling' title='Smiling' /></a> 
   <a href=\"javascript:SmileIT(':-D','staff_shbox','staff_shbox_text')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/grin.gif' alt='Grin' title='Grin' /></a> 
   <a href=\"javascript:SmileIT(':lol:','staff_shbox','staff_shbox_text')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/laugh.gif' alt='Laughing' title='Laughing' /></a> 
   <a href=\"javascript:SmileIT(':w00t:','staff_shbox','staff_shbox_text')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/w00t.gif' alt='W00t' title='W00t' /></a> 
   <a href=\"javascript:SmileIT(':blum:','staff_shbox','staff_shbox_text')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/blum.gif' alt='Rasp' title='Rasp' /></a> 
   <a href=\"javascript:SmileIT(';-)','staff_shbox','staff_shbox_text')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/wink.gif' alt='Wink' title='Wink' /></a> 
   <a href=\"javascript:SmileIT(':devil:','staff_shbox','staff_shbox_text')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/devil.gif' alt='Devil' title='Devil' /></a> 
   <a href=\"javascript:SmileIT(':yawn:','staff_shbox','staff_shbox_text')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/yawn.gif' alt='Yawn' title='Yawn' /></a> 
   <a href=\"javascript:SmileIT(':-/','staff_shbox','staff_shbox_text')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/confused.gif' alt='Confused' title='Confused' /></a> 
   <a href=\"javascript:SmileIT(':o)','staff_shbox','staff_shbox_text')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/clown.gif' alt='Clown' title='Clown' /></a> 
   <a href=\"javascript:SmileIT(':innocent:','staff_shbox','staff_shbox_text')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/innocent.gif' alt='Innocent' title='innocent' /></a> 
   <a href=\"javascript:SmileIT(':whistle:','staff_shbox','staff_shbox_text')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/whistle.gif' alt='Whistle' title='Whistle' /></a> 
   <a href=\"javascript:SmileIT(':unsure:','staff_shbox','staff_shbox_text')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/unsure.gif' alt='Unsure' title='Unsure' /></a> 
   <a href=\"javascript:SmileIT(':blush:','staff_shbox','staff_shbox_text')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/blush.gif' alt='Blush' title='Blush' /></a> 
   <a href=\"javascript:SmileIT(':hmm:','staff_shbox','staff_shbox_text')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/hmm.gif' alt='Hmm' title='Hmm' /></a> 
   <a href=\"javascript:SmileIT(':hmmm:','staff_shbox','staff_shbox_text')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/hmmm.gif' alt='Hmmm' title='Hmmm' /></a> 
   <a href=\"javascript:SmileIT(':huh:','staff_shbox','staff_shbox_text')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/huh.gif' alt='Huh' title='Huh' /></a> 
   <a href=\"javascript:SmileIT(':look:','staff_shbox','staff_shbox_text')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/look.gif' alt='Look' title='Look' /></a> 
   <a href=\"javascript:SmileIT(':rolleyes:','staff_shbox','staff_shbox_text')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/rolleyes.gif' alt='Roll Eyes' title='Roll Eyes' /></a> 
   <a href=\"javascript:SmileIT(':kiss:','shbox','staff_shbox_text')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/kiss.gif' alt='Kiss' title='Kiss' /></a> 
   <a href=\"javascript:SmileIT(':blink:','shbox','staff_shbox_text')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/blink.gif' alt='Blink' title='Blink' /></a> 
   <a href=\"javascript:SmileIT(':baby:','shbox','staff_shbox_text')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/baby.gif' alt='Baby' title='Baby' /></a>
   <span class='shoutextra'><a href='{$INSTALLER09['baseurl']}/staff_shoutbox.php?show_staffshout=1&amp;show_staff=no'>[&nbsp;{$lang['index_shoutbox_close']}&nbsp;]</a>
   {$staffsmiliebutton}{$smilebutton}{$custombutton}{$refreshbutton}{$commandbutton}</span>
   </div>
	 </div>
   </form><br />\n";
   }
   if ($CURUSER['show_staffshout'] === "no") {
   $HTMLOUT .="<div class='headline'><b>{$lang['index_staff_shoutbox']}&nbsp;</b></div><div class='headbody'>[&nbsp;<a href='{$INSTALLER09['baseurl']}/staff_shoutbox.php?show_staffshout=1&amp;show_staff=yes'>{$lang['index_shoutbox_open']}&nbsp;]</a></div><br />";
   }
//==end 09 Staff shoutbox
// End Class

// End File
