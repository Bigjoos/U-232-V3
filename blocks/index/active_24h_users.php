<?php
//== Last24 start - pdq
$keys['last24'] = 'last24';
if (($last24_cache = $mc1->get_value($keys['last24'])) === false) {
    $last24_cache = array();
    $time24 = $_SERVER['REQUEST_TIME'] - 86400;
    $activeusers24 = '';
    $arr = mysqli_fetch_assoc(sql_query('SELECT * FROM avps WHERE arg = "last24"'));
    $res = sql_query('SELECT id, username, class, donor, title, warned, enabled, chatpost, leechwarn, pirate, king '.'FROM users WHERE last_access >= '.$time24.' '.'AND perms < '.bt_options::PERMS_STEALTH.' ORDER BY username ASC') or sqlerr(__FILE__, __LINE__);
    $totalonline24 = mysqli_num_rows($res);
    $_ss24 = ($totalonline24 != 1 ? 's' : '');
    $last24record = get_date($arr['value_u'], '');
    $last24 = $arr['value_i'];
    if ($totalonline24 > $last24) {
        $last24 = $totalonline24;
        $period = $_SERVER['REQUEST_TIME'];
        sql_query('UPDATE avps SET value_s = 0, '.'value_i = '.sqlesc($last24).', '.'value_u = '.sqlesc($period).' '.'WHERE arg = "last24"') or sqlerr(__FILE__, __LINE__);
    }
    while ($arr = mysqli_fetch_assoc($res)) {
        if ($activeusers24) $activeusers24.= ",\n";
        $activeusers24.= '<b>'.format_username($arr).'</b>';
    }
    $last24_cache['activeusers24'] = $activeusers24;
    $last24_cache['totalonline24'] = number_format($totalonline24);
    $last24_cache['last24record'] = $last24record;
    $last24_cache['last24'] = number_format($last24);
    $last24_cache['ss24'] = $_ss24;
    $mc1->cache_value($keys['last24'], $last24_cache, $INSTALLER09['expires']['last24']);
}
if (!$last24_cache['activeusers24']) $last24_cache['activeusers24'] = 'There&nbsp;have&nbsp;been&nbsp;no&nbsp;active&nbsp;users&nbsp;in&nbsp;the&nbsp;last&nbsp;15&nbsp;minutes.';
$last_24 = '<div class="headline">'.$lang['index_active24'].'<small>&nbsp;-&nbsp;List&nbsp;updated&nbsp;hourly</small></div>
     <div class="headbody">
     <!--<a href=\'javascript: klappe_news("a2")\'><img border=\'0\' src=\'pic/plus.gif\' id=\'pica2\' alt=\'[Hide/Show]\' /></a><div id=\'ka2\' style=\'display: none;\'>-->
     <!--<a class="altlink"  title="Click for more info" id="div_open" style="font-weight:bold;cursor:pointer;"><img border=\'0\' src=\'pic/plus.gif\' alt=\'[Hide/Show]\' /></a>
     <div id="div_info" style="display:none;background-color:#FEFEF4;max-width:940px;padding: 5px 5px 5px 10px;">-->
     <p align="center"><b>'.$last24_cache['totalonline24'].' Member'.$last24_cache['ss24'].' visited during the last 24 hours</b></p>
     <p align="center">'.$last24_cache['activeusers24'].'</p>
     <p align="center"><b>Most ever visited in 24 hours was '.$last24_cache['last24'].' Member'.$last24_cache['ss24'].' on '.$last24_cache['last24record'].'</b></p>
     </div><!--</div>--><br />';
$HTMLOUT.= $last_24;
//== last24 end
// End Class
// End File
