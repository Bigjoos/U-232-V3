<?php
function crazyhour() {
global $CURUSER, $INSTALLER09, $mc1;
$htmlout = $cz  = '';
$crazy_hour = (TIME_NOW + 3600);
$crazyhour['crazyhour'] = $mc1->get_value('crazyhour');
if ($crazyhour['crazyhour'] === false) {
$crazyhour['crazyhour_sql'] = sql_query('SELECT var, amount FROM freeleech WHERE type = "crazyhour"') or sqlerr(__FILE__, __LINE__);
$crazyhour['crazyhour'] = array();
if (mysqli_num_rows($crazyhour['crazyhour_sql']) !== 0)
$crazyhour['crazyhour'] = mysqli_fetch_assoc($crazyhour['crazyhour_sql']);
else {
$crazyhour['crazyhour']['var'] = mt_rand(TIME_NOW, (TIME_NOW + 86400));
$crazyhour['crazyhour']['amount'] = 0;
sql_query('UPDATE freeleech SET var = '.$crazyhour['crazyhour']['var'].', amount = '.$crazyhour['crazyhour']['amount'].'
WHERE type = "crazyhour"') or sqlerr(__FILE__, __LINE__);
}
$mc1->cache_value('crazyhour', $crazyhour['crazyhour'], 0);
}
$cimg = '<img src="'.$INSTALLER09['pic_base_url'].'cat_free.gif" alt="FREE!" />';
if ($crazyhour['crazyhour']['var'] < TIME_NOW) { // if crazyhour over
$cz_lock = $mc1->add_value('crazyhour_lock', 1, 10);

if ($cz_lock !== false) {
$crazyhour['crazyhour_new'] = mktime(23, 59, 59, date('m'), date('d'), date('y'));
$crazyhour['crazyhour']['var'] = mt_rand($crazyhour['crazyhour_new'], ($crazyhour['crazyhour_new'] + 86400));
$crazyhour['crazyhour']['amount'] = 0;
$crazyhour['remaining'] = ($crazyhour['crazyhour']['var'] - TIME_NOW);
sql_query('UPDATE freeleech SET var = '.$crazyhour['crazyhour']['var'].', amount = '.$crazyhour['crazyhour']['amount'].'
WHERE type = "crazyhour"') or sqlerr(__FILE__, __LINE__);
$mc1->cache_value('crazyhour', $crazyhour['crazyhour'], 0);
write_log('Next [color=#FFCC00][b]Crazyhour[/b][/color] is at '.get_date($crazyhour['crazyhour']['var'] + ($CURUSER['time_offset'] - 3600), 'LONG').'');
$text = 'Next [color=orange][b]Crazyhour[/b][/color] is at '.get_date($crazyhour['crazyhour']['var'] + ($CURUSER['time_offset'] - 3600), 'LONG');
$text_parsed = 'Next <span style="font-weight:bold;color:orange;">Crazyhour</span> is at '.get_date($crazyhour['crazyhour']['var'] + ($CURUSER['time_offset'] - 3600), 'LONG');
sql_query('INSERT INTO shoutbox (userid, date, text, text_parsed) '.'
VALUES (2, '.TIME_NOW.', '.sqlesc($text).', '.sqlesc($text_parsed).')') or sqlerr(__FILE__, __LINE__);
$mc1->delete_value('shoutbox_');
}
}
elseif (($crazyhour['crazyhour']['var'] < $crazy_hour) && ($crazyhour['crazyhour']['var'] >= TIME_NOW)) { // if crazyhour
if ($crazyhour['crazyhour']['amount'] !== 1) {
$crazyhour['crazyhour']['amount'] = 1;
$cz_lock = $mc1->add_value('crazyhour_lock', 1, 10);

if ($cz_lock !== false) {
sql_query('UPDATE freeleech SET amount = '.$crazyhour['crazyhour']['amount'].'
WHERE type = "crazyhour"') or sqlerr(__FILE__, __LINE__);
$mc1->cache_value('crazyhour', $crazyhour['crazyhour'], 0);
write_log('w00t! It\'s [color=#FFCC00][b]Crazyhour[/b][/color]!');
$text = 'w00t! It\'s [color=orange][b]Crazyhour[/b][/color] :w00t:';
$text_parsed = 'w00t! It\'s <span style="font-weight:bold;color:orange;">Crazyhour</span> <img src="pic/smilies/w00t.gif" alt=":w00t:" />';
sql_query('INSERT INTO shoutbox (userid, date, text, text_parsed) '.
'VALUES (2, '.TIME_NOW.', '.sqlesc($text).', '.sqlesc($text_parsed).')') or sqlerr(__FILE__, __LINE__);
$mc1->delete_value('shoutbox_');
}
}

$crazyhour['remaining'] = ($crazyhour['crazyhour']['var'] - TIME_NOW);
$crazytitle = 'w00t It\'s Crazyhour!';
$crazymessage = 'All torrents <b>FREE</b> and upload stats are <strong>TRIPLED</strong>!';

$htmlout .= '<li>
<a class="tooltip" href="#"><b><font color="green">CrazyHour ON</font></b><span class="custom info"><img src="./templates/1/images/Info.png" alt="CrazyHours" height="48" width="48" /><em>CrazyHour</em>
'.$crazytitle.'...'.$crazymessage.' '.' Ends in '.mkprettytime($crazyhour['remaining']).'<br />
&nbsp;at '.get_date($crazyhour['crazyhour']['var'], 'LONG').'</span></a></li>';
return $htmlout;
}

$htmlout .= '<li>
<a class="tooltip" href="#"><b><font color="red">Crazyhour</font></b>
<span class="custom info"><img src="./templates/1/images/Info.png" alt="CrazyHours" height="48" width="48" /><em>CrazyHour</em>
Crazyhour...All torrents free<br />and triple upload credit!<br /> '.'starts in '.mkprettytime($crazyhour['crazyhour']['var'] - 3600 - TIME_NOW).'<br />
&nbsp;at '.get_date($crazyhour['crazyhour']['var'] + ($CURUSER['time_offset'] - 3600), 'LONG').'</span></a></li>';
return $htmlout;
}
$htmlout .= crazyhour();
// End Class

// End File
