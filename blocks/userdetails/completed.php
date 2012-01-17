<?php
//==09 Hnr mod - sir_snugglebunny
    if  ($user['paranoia'] < 2 || $CURUSER['id'] == $id || $CURUSER['class'] >= UC_STAFF) 
    {
    $completed = $count2= '';
    $r = sql_query("SELECT torrents.name,torrents.added AS torrent_added, snatched.start_date AS s, snatched.complete_date AS c, snatched.downspeed, snatched.seedtime, snatched.seeder, snatched.torrentid as tid, snatched.id, categories.id as category, categories.image, categories.name as catname, snatched.uploaded, snatched.downloaded, snatched.hit_and_run, snatched.mark_of_cain, snatched.complete_date, snatched.last_action, torrents.seeders, torrents.leechers, torrents.owner, snatched.start_date AS st, snatched.start_date FROM snatched JOIN torrents ON torrents.id = snatched.torrentid JOIN categories ON categories.id = torrents.category WHERE snatched.finished='yes' AND userid=$id AND torrents.owner != $id ORDER BY snatched.id DESC") or sqlerr(__FILE__, __LINE__);
    //=== completed
    if (mysqli_num_rows($r) > 0){ 
    $completed .= "<table class='main' border='1' cellspacing='0' cellpadding='3'>
    <tr>
    <td class='colhead'>{$lang['userdetails_type']}</td>
    <td class='colhead'>{$lang['userdetails_name']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_s']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_l']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_ul']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_dl']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_ratio']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_wcompleted']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_laction']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_speed']}</td></tr>";
   
    while ($a = mysqli_fetch_assoc($r)){
    //=======change colors
    $count2= (++$count2)%2;
    $class = ($count2 == 0 ? 'one' : 'two');
    $torrent_needed_seed_time = ($a['st'] - $a['torrent_added']);
    //=== get times per class
   switch (true)
			{ 
			case ($user['class'] <= UC_POWER_USER):
				$days_3 = 1*86400; //== 1 days
				$days_14 = 1*86400; //== 1 days
				$days_over_14 = 86400; //== 1 day
				break;
			case ($user['class'] < UC_STAFF):
				$days_3 = 43200; //== 12 hours
				$days_14 = 43200; //== 12 hours
				$days_over_14 = 43200; //== 12 hours
				break;
			case ($user['class'] >= UC_STAFF):
				$days_3 = 43200; //== 12 hours
				$days_14 = 43200; //== 12 hours
				$days_over_14 = 43200; //== 12 hours
				break;
			}
    //=== times per torrent based on age
    $foo = $a['downloaded'] > 0 ? $a['uploaded'] / $a['downloaded'] : 0;
    switch(true)
    {
    case (($a['st'] - $a['torrent_added']) < 7*86400):
    $minus_ratio = ($days_3 - $a['seedtime']) - ($foo * 3 * 86400);
    break;
    case (($a['st'] - $a['torrent_added']) < 21*86400):
    $minus_ratio = ($days_14 - $a['seedtime']) - ($foo * 2 * 86400);
    break;
    case (($a['st'] - $a['torrent_added']) >= 21*86400):
    $minus_ratio = ($days_over_14 - $a['seedtime']) - ($foo * 86400);
    break;
    }
    $color = (($minus_ratio > 0 && $a['uploaded'] < $a['downloaded']) ? get_ratio_color($minus_ratio) : 'limegreen');
    $minus_ratio = mkprettytime($minus_ratio); 
    //=== speed color red fast green slow ;)
    if ($a["downspeed"] > 0)
    $dl_speed = ($a["downspeed"] > 0 ? mksize($a["downspeed"]) : ($a["leechtime"] > 0 ? mksize($a["downloaded"] / $a["leechtime"]) : mksize(0)));
    else
    $dl_speed = mksize(($a["downloaded"] / ( $a['c'] - $a['s'] + 1 )));
    $dlc="";
    switch (true){
    case ($dl_speed > 600):
    $dlc = 'red';
    break;
    case ($dl_speed > 300 ):
    $dlc = 'orange';
    break;
    case ($dl_speed > 200 ):
    $dlc = 'yellow';
    break;
    case ($dl_speed < 100 ):
    $dlc = 'Chartreuse';
    break;
    }
    
    //=== mark of cain / hit and run
    $checkbox_for_delete = ($CURUSER['class'] >=  UC_STAFF ? " [<a href='".$INSTALLER09['baseurl']."/userdetails.php?id=".$id."&amp;delete_hit_and_run=".(int)$a['id']."'>Remove</a>]" : '');
    $mark_of_cain = ($a['mark_of_cain'] == 'yes' ? "<img src='{$INSTALLER09['pic_base_url']}moc.gif' width='40px' alt='Mark Of Cain' title='The mark of Cain!' />".$checkbox_for_delete : '');
    $hit_n_run = ($a['hit_and_run'] > 0 ? "<img src='{$INSTALLER09['pic_base_url']}hnr.gif' width='40px' alt='Hit and run' title='Hit and run!' />" : '');
    $completed .= "<tr><td style='padding: 0px' class='$class'><img src='{$INSTALLER09['pic_base_url']}caticons/{$CURUSER['categorie_icon']}/{$a['image']}' alt='{$a['name']}' title='{$a['name']}' /></td>
    <td class='$class'><a class='altlink' href='{$INSTALLER09['baseurl']}/details.php?id=".(int)$a['tid']."&amp;hit=1'><b>".htmlspecialchars($a['name'])."</b></a>
    <br /><font color='.$color.'>  ".(($CURUSER['class'] >= UC_STAFF || $user['id'] == $CURUSER['id']) ? "seeded for</font>: ".mkprettytime($a['seedtime']).(($minus_ratio != '0:00' && $a['uploaded'] < $a['downloaded']) ? "<br />should still seed for: ".$minus_ratio."&nbsp;&nbsp;" : '').
    ($a['seeder'] == 'yes' ? "&nbsp;<font color='limegreen'> [<b>seeding</b>]</font>" : $hit_n_run."&nbsp;".$mark_of_cain) : '')."</td>
    <td align='center' class='$class'>".(int)$a['seeders']."</td>
    <td align='center' class='$class'>".(int)$a['leechers']."</td>
    <td align='center' class='$class'>".mksize($a['uploaded'])."</td>
    <td align='center' class='$class'>".mksize($a['downloaded'])."</td>
    <td align='center' class='$class'>".($a['downloaded'] > 0 ? "<font color='" . get_ratio_color(number_format($a['uploaded'] / $a['downloaded'], 3)) . "'>".number_format($a['uploaded'] / $a['downloaded'], 3)."</font>" : ($a['uploaded'] > 0 ? 'Inf.' : '---'))."<br /></td>
    <td align='center' class='$class'>".get_date($a['complete_date'], 'DATE')."</td>
    <td align='center' class='$class'>".get_date($a['last_action'], 'DATE')."</td>
    <td align='center' class='$class'><font color='$dlc'>[ DLed at: $dl_speed ]</font></td></tr>";
    }
    $completed .= "</table>\n";
    }
    if ($completed && $CURUSER['class'] >= UC_POWER_USER || $completed && $user['id'] == $CURUSER['id']){ 
    if (!isset($_GET['completed']))
    $HTMLOUT .= tr('<b>'.$lang['userdetails_completedt'].'</b><br />','[ <a href=\'./userdetails.php?id='.$id.'&amp;completed=1#completed\' class=\'sublink\'>Show</a> ]&nbsp;&nbsp;-&nbsp;'.mysqli_num_rows($r), 1);
    elseif (mysqli_num_rows($r) == 0)
    $HTMLOUT .= tr('<b>'.$lang['userdetails_completedt'].'</b><br />','[ <a href=\'./userdetails.php?id='.$id.'&amp;completed=1\' class=\'sublink\'>Show</a> ]&nbsp;&nbsp;-&nbsp;'.mysqli_num_rows($r), 1);
    else
    $HTMLOUT .= tr('<a name=\'completed\'><b>'.$lang['userdetails_completedt'].'</b></a><br />[ <a href=\'./userdetails.php?id='.$id.'#history\' class=\'sublink\'>Hide list</a> ]', $completed, 1);
    } 
    }
//==End hnr
// End Class

// End File
