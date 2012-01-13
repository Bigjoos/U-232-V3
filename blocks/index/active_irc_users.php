<?php

function calctime($val)
	{
		$days=intval($val / 86400);
		$val-=$days*86400;
		$hours=intval($val / 3600);
		$val-=$hours*3600;
		$mins=intval($val / 60);
		$secs=$val-($mins*60);
		return "<br>&nbsp;&nbsp;&nbsp;$days days, $hours hrs, $mins minutes";
	}
     //== Start activeircusers pdq
     $keys['activeircusers']    = 'activeircusers';
     if(($active_irc_users_cache = $mc1->get_value($keys['activeircusers'])) === false) {
     $dt = $_SERVER['REQUEST_TIME'] - 180;                       
     $activeircusers = '';
     $active_irc_users_cache = array();
     $res = sql_query('SELECT id, username, irctotal, class, donor, title, warned, enabled, chatpost, leechwarn, pirate, king '.
     'FROM users WHERE onirc = "yes" '.'ORDER BY username ASC') or sqlerr(__FILE__, __LINE__);
     $actcount = mysqli_num_rows($res);
     
     while ($arr = mysqli_fetch_assoc($res)) {
     if ($activeircusers) 
     $activeircusers .= ",\n";
     $activeircusers .= '<b>'.format_username($arr).'</b>';
     //$ircbonus   = (!empty($arr['irctotal'])?number_format($arr["irctotal"] / ($INSTALLER09['autoclean_interval'] * 4), 1):'0.0');	
     //$irctotal = (!empty($arr['irctotal'])?calctime($arr['irctotal']):'');
     //$activeircusers .= '<span class="tool"><b>'.format_username($arr).'</b><span class="tip">'.$ircbonus.' points. - '.$irctotal.'</span></span>';
     }
     $active_irc_users_cache['activeircusers'] = $activeircusers;
     $active_irc_users_cache['actcount']    = $actcount;
     $mc1->cache_value($keys['activeircusers'] , $active_irc_users_cache, $INSTALLER09['expires']['activeircusers']);
     }
     if (!$active_irc_users_cache['activeircusers'])
     $active_irc_users_cache['activeircusers'] = 'There have been no active irc users in the last 15 minutes.';
     $active_irc_users = '<div class="headline">'.$lang['index_active_irc'].'&nbsp;('.$active_irc_users_cache['actcount'].')</div>
     <div class="headbody">
     <!--<a href=\'javascript: klappe_news("a1")\'><img border=\'0\' src=\'pic/plus.gif\' id=\'pica1\' alt=\'[Hide/Show]\' /></a><div id=\'ka1\' style=\'display: none;\'>-->
      '.$active_irc_users_cache['activeircusers'].'
     </div><!--</div>--><br />';
     $HTMLOUT .= $active_irc_users;
//== end activeusers
// End Class

// End File
