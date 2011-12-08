<?php
//== iphistory
   if ($user['paranoia'] < 2 || $CURUSER['id'] == $id ) 
   {
    error_reporting(0);
    $iphistory = $mc1->get_value('ip_history_'.$id);
        if ($iphistory === false) {
            $ipto = sql_query("SELECT COUNT(id),enabled FROM `users` AS iplist WHERE `ip` = '" . $user["ip"] . "' group by enabled") or sqlerr(__FILE__, __LINE__);
            $row12 = mysqli_fetch_row($ipto);
            $row13 = mysqli_fetch_row($ipto);
            $ipuse[$row12[1]] = $row12[0];
            $ipuse[$row13[1]] = $row13[0];
            if (($ipuse['yes'] == 1 && $ipuse['no']==0) || ($ipuse['no']==1 && $ipuse['yes']==0))
                $use = "";
            else {
                $ipcheck=$user["ip"];
                $enbl  = $ipuse['yes'] ? $ipuse['yes'].' enabled ':'';
                $dbl =   $ipuse['no'] ? $ipuse['no'].' disabled ':'';
                $mid = $enbl && $dbl ?'and' :'';
                $iphistory['use'] =  "<b>(<font color='red'>Warning :</font> <a href='staffpanel.php?tool=usersearch&amp;action=usersearch&amp;ip=$ipcheck'>Used by $enbl $mid $dbl users!</a>)</b>";
            }
            $resip = sql_query("SELECT ip FROM ips WHERE userid = ".sqlesc($id)." GROUP BY ip") or sqlerr(__FILE__, __LINE__);
            $iphistory['ips'] = mysqli_num_rows($resip);
            $mc1->cache_value('ip_history_'.$id, $iphistory, $INSTALLER09['expires']['iphistory']);
        }
        if (isset($addr))
        if ($CURUSER['id'] == $id || $CURUSER['class'] >= UC_STAFF)
        $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_address']}</td><td align='left'>{$addr}{$iphistory['use']}&nbsp;(<a class='altlink' href='staffpanel.php?tool=iphistory&amp;action=iphistory&amp;id=$user[id]'><b>History</b></a>)&nbsp;(<a class='altlink' href='staffpanel.php?tool=iphistory&amp;action=iplist&amp;id=$user[id]'><b>List</b></a>)</td></tr>\n";
        if ($CURUSER["class"] >= UC_STAFF && $iphistory['ips'] > 0)
        $HTMLOUT .="<tr><td class='rowhead'>IP History</td><td align='left'>This user has earlier used <b><a href='{$INSTALLER09['baseurl']}/staffpanel.php?tool=iphistory&amp;action=iphistory&amp;id=" .$user['id'] ."'>{$iphistory['ips']} different IP addresses</a></b></td></tr>\n";
        }
//==end
// End Class

// End File
