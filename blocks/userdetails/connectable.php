<?php
//==Connectable and port shit
    if  ($user['paranoia'] < 1 || $CURUSER['id'] == $id || $CURUSER['class'] >= UC_STAFF) 
    {
    if(($port_data = $mc1->get_value('port_data_'.$id)) === false) {
    $q1 = sql_query('SELECT connectable, port,agent FROM peers WHERE userid = '.$id.' LIMIT 1') or sqlerr(__FILE__,__LINE__);
    $port_data = mysqli_fetch_row($q1);
    $mc1->cache_value('port_data_'.$id, $port_data, $INSTALLER09['expires']['port_data']);
    }
    if($port_data > 0){
    $connect = $port_data[0];
    $port = $port_data[1];
    $agent = $port_data[2];
    if($connect == "yes"){
    $connectable = "<img src='{$INSTALLER09['pic_base_url']}tick.png' alt='Yes' title='Sorted Yer connectable' style='border:none;padding:2px;' /><font color='green'><b>{$lang['userdetails_yes']}</b></font>";
    }else{
    $connectable = "<img src='{$INSTALLER09['pic_base_url']}cross.png' alt='No' title='Contact Site Staff' style='border:none;padding:2px;' /><font color='red'><b>{$lang['userdetails_no']}</b></font>";
    }
    }else{
    $connectable = "<font color='orange'><b>{$lang['userdetails_unknown']}</b></font>";
    }
    $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_connectable']}</td><td align='left'>".$connectable."</td></tr>";
    if (!empty($port))
    $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_port']}</td><td class='tablea' align='left'>".htmlentities($port)."</td></tr>
    <tr><td class='rowhead'>{$lang['userdetails_client']}</td><td class='tablea' align='left'>".htmlentities($agent)."</td></tr>";
    }
//==End
// End Class

// End File
