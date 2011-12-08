<?php
     //==Start birthdayusers pdq
     $current_date = getdate();
     $keys['birthdayusers'] = 'birthdayusers';
     $birthday_users_cache = $mc1->get_value($keys['birthdayusers']);
     if ($birthday_users_cache === false) {                     
     $birthdayusers = '';
     $birthday_users_cache = array();
     $res = sql_query("SELECT id, username, class, donor, title, warned, enabled, chatpost, leechwarn, pirate, king, birthday FROM users WHERE MONTH(birthday) = ".sqlesc($current_date['mon'])." AND DAYOFMONTH(birthday) = ".sqlesc($current_date['mday'])." ORDER BY username ASC") or sqlerr(__FILE__, __LINE__);
     $actcount = mysqli_num_rows($res);
     while ($arr = mysqli_fetch_assoc($res)) {
     if ($birthdayusers) 
     $birthdayusers .= ",\n";
     $birthdayusers .= '<b>'.format_username($arr).'</b>';
     }
     $birthday_users_cache['birthdayusers'] = $birthdayusers;
     $birthday_users_cache['actcount']    = $actcount;
     $mc1->cache_value($keys['birthdayusers'] , $birthday_users_cache, $INSTALLER09['expires']['birthdayusers']);
     }
     if (!$birthday_users_cache['birthdayusers'])
     $birthday_users_cache['birthdayusers'] = 'There is no members birthdays today.';
     $birthday_users = '<div class="headline">'.$lang['index_birthday'].'&nbsp;('.$birthday_users_cache['actcount'].')</div>
     <div class="headbody">
     <!--<a href=\'javascript: klappe_news("a1")\'><img border=\'0\' src=\'pic/plus.gif\' id=\'pica1\' alt=\'[Hide/Show]\' /></a><div id=\'ka1\' style=\'display: none;\'>-->
      '.$birthday_users_cache['birthdayusers'].'
     </div><!--</div>--><br />';
     $HTMLOUT .= $birthday_users;
//== end birthdayusers
// End Class

// End File