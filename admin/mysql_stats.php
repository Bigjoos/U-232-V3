<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
if ( ! defined( 'IN_INSTALLER09_ADMIN' ) )
{
	$HTMLOUT='';
	$HTMLOUT .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
		\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<title>Error!</title>
		</head>
		<body>
	<div style='font-size:33px;color:white;background-color:red;text-align:center;'>Incorrect access<br />You cannot access this file directly.</div>
	</body></html>";
	echo $HTMLOUT;
	exit();
}

require_once(INCL_DIR.'user_functions.php');
require_once(CLASS_DIR.'class_check.php');
class_check(UC_SYSOP);

    $GLOBALS["byteUnits"] = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');

    $day_of_week = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
    $month = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
    // See http://www.php.net/manual/en/function.strftime.php to define the
    // variable below
    $datefmt = '%B %d, %Y at %I:%M %p';
    $timespanfmt = '%s days, %s hours, %s minutes and %s seconds';
////////////////// FUNCTION LIST /////////////////////////

function byteformat($value, $limes = 2, $comma = 0) {
        $dh           = pow(10, $comma);
        $li           = pow(10, $limes);
        $return_value = $value;
        $unit         = $GLOBALS['byteUnits'][0];

        for ( $d = 6, $ex = 15; $d >= 1; $d--, $ex-=3 ) 
        {
            if (isset($GLOBALS['byteUnits'][$d]) && $value >= $li * pow(10, $ex)) 
            {
                $value = round($value / ( pow(1024, $d) / $dh) ) /$dh;
                $unit = $GLOBALS['byteUnits'][$d];
                break 1;
            } // end if
        } // end for

        if ($unit != $GLOBALS['byteUnits'][0]) {
            $return_value = number_format($value, $comma, '.', ',');
        } 
        else 
        {
            $return_value = number_format($value, 0, '.', ',');
        }

        return array($return_value, $unit);
    } // end of the 'formatByteDown' function

function timespanFormat($seconds) {

        $return_string = '';
        
        $days = floor($seconds / 86400);
        
        if ($days > 0) 
        {
            $seconds -= $days * 86400;
        }
        
        $hours = floor($seconds / 3600);
        
        if ($days > 0 || $hours > 0) 
        {
            $seconds -= $hours * 3600;
        }
        
        $minutes = floor($seconds / 60);
        
        if ($days > 0 || $hours > 0 || $minutes > 0) 
        {
            $seconds -= $minutes * 60;
        }
        
        return (string)$days." Days ". (string)$hours." Hours ". (string)$minutes." Minutes ". (string)$seconds." Seconds ";
    }


function localisedDate($timestamp = -1, $format = '') {

        global $datefmt, $month, $day_of_week;

        if ($format == '') 
        {
            $format = $datefmt;
        }

        if ($timestamp == -1) 
        {
            $timestamp = time();
        }

        $date = preg_replace('@%[aA]@', $day_of_week[(int)strftime('%w', $timestamp)], $format);
        $date = preg_replace('@%[bB]@', $month[(int)strftime('%m', $timestamp)-1], $date);

        return strftime($date, $timestamp);
    } // end of the 'localisedDate()' function
    
////////////////////// END FUNCTION LIST /////////////////////////////////////

    $HTMLOUT = '';
    
    $HTMLOUT .="<h2>Mysql Server Status</h2>";


    //$res = @mysql_query('SHOW STATUS') or sqlerr(__FILE__,__LINE__);
    $res = @sql_query('SHOW GLOBAL STATUS') or sqlerr(__FILE__,__LINE__);
    while ($row = mysqli_fetch_row($res)) 
    {
      $serverStatus[$row[0]] = $row[1];
    }
    @((mysqli_free_result($res) || (is_object($res) && (get_class($res) == "mysqli_result"))) ? true : false);
    unset($res);
    unset($row);


    $res = @sql_query('SELECT UNIX_TIMESTAMP() - ' . $serverStatus['Uptime']);
    $row = mysqli_fetch_row($res);

    $HTMLOUT .= "<table class='torrenttable' border='1'>
      <tr>
        <td>This MySQL server has been running for ". timespanFormat($serverStatus['Uptime']) .". It started up on ". localisedDate($row[0]) . "


        </td>
      </tr>
      </table><br />";


    ((mysqli_free_result($res) || (is_object($res) && (get_class($res) == "mysqli_result"))) ? true : false);
    unset($res);
    unset($row);
    
    //Get query statistics
    $queryStats = array();
    $tmp_array = $serverStatus;
    
    foreach($tmp_array AS $name => $value) 
    {
        if (substr($name, 0, 4) == 'Com_') 
        {
          $queryStats[str_replace('_', ' ', substr($name, 4))] = $value;
          unset($serverStatus[$name]);
        }
      }
    unset($tmp_array);

    $TRAFFIC_STATS = '';
    
    $TRAFFIC_STATS_HEAD = "<!-- Server Traffic -->
        <b>Server traffic:</b> These tables show the network traffic statistics of this MySQL server since its startup";
      
    $TRAFFIC_STATS .= "<table class='torrenttable' width='100%' border='0'>
            <tr>
                <td colspan='3' bgcolor='grey'>&nbsp;Traffic&nbsp;&nbsp;&nbsp;Per Hour&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;Received&nbsp;</td>
                <td  align='right'>&nbsp;". join(' ', byteformat($serverStatus['Bytes_received']))."&nbsp;</td>
                <td  align='right'>&nbsp;". join(' ', byteformat($serverStatus['Bytes_received'] * 3600 / $serverStatus['Uptime']))."&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;Sent&nbsp;</td>
                <td  align='right'>&nbsp;". join(' ', byteformat($serverStatus['Bytes_sent']))."&nbsp;</td>
                <td  align='right'>&nbsp;". join(' ', byteformat($serverStatus['Bytes_sent'] * 3600 / $serverStatus['Uptime']))."&nbsp;</td>
            </tr>
            <tr>
                <td bgcolor='grey'>&nbsp;Total&nbsp;</td>
                <td bgcolor='grey' align='right'>&nbsp;". join(' ', byteformat($serverStatus['Bytes_received'] + $serverStatus['Bytes_sent']))."&nbsp;</td>
                <td bgcolor='grey' align='right'>&nbsp;". join(' ', byteformat(($serverStatus['Bytes_received'] + $serverStatus['Bytes_sent']) * 3600 / $serverStatus['Uptime']))."&nbsp;</td>
            </tr>
        </table>";
            
    $TRAFFIC_STATS2 = "<table class='torrenttable' width='100%' border='0'>
        <tr>
            <td colspan='4' bgcolor='grey'>&nbsp;Connections&nbsp;&nbsp;&oslash;&nbsp;Per Hour&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;Failed Attempts&nbsp;</td>
            <td  align='right'>&nbsp;". number_format($serverStatus['Aborted_connects'], 0, '.', ',')."&nbsp;</td>
            <td  align='right'>&nbsp;". number_format(($serverStatus['Aborted_connects'] * 3600 / $serverStatus['Uptime']), 2, '.', ',')."&nbsp;</td>
            <td  align='right'>&nbsp;". (($serverStatus['Connections'] > 0 ) ? number_format(($serverStatus['Aborted_connects'] * 100 / $serverStatus['Connections']), 2, '.', ',') . "&nbsp;%" : "---"."&nbsp;")."</td>
        </tr>
        <tr>
            <td>&nbsp;Aborted Clients&nbsp;</td>
            <td  align='right'>&nbsp;". number_format($serverStatus['Aborted_clients'], 0, '.', ',')."&nbsp;</td>
            <td  align='right'>&nbsp;". number_format(($serverStatus['Aborted_clients'] * 3600 / $serverStatus['Uptime']), 2, '.', ',')."&nbsp;</td>
            <td  align='right'>&nbsp;". (($serverStatus['Connections'] > 0 ) ? number_format(($serverStatus['Aborted_clients'] * 100 / $serverStatus['Connections']), 2 , '.', ',') . '&nbsp;%' : '---')."&nbsp;</td>
        </tr>
        <tr>
            <td bgcolor='grey'>&nbsp;Total&nbsp;</td>
            <td bgcolor='grey' align='right'>&nbsp;". number_format($serverStatus['Connections'], 0, '.', ',')."&nbsp;</td>
            <td bgcolor='grey' align='right'>&nbsp;". number_format(($serverStatus['Connections'] * 3600 / $serverStatus['Uptime']), 2, '.', ',')."&nbsp;</td>
            <td bgcolor='grey' align='right'>&nbsp;". number_format(100, 2, '.', ',')."&nbsp;%&nbsp;</td>
        </tr>
    </table>";
  
    
    $QUERY_STATS = '';
    
    $QUERY_STATS .= "<!-- Queries -->
    <b>Query Statistics:</b> Since it's start up, ". number_format($serverStatus['Questions'], 0, '.', ',')." queries have been sent to the server.<br />

    <table class='torrenttable' width='100%' border='0'>
        <tr>
            <td bgcolor='grey'>&nbsp;Total&nbsp;</td>
            <td bgcolor='grey'>&nbsp;&oslash;&nbsp;Per&nbsp;Hour&nbsp;</td>
            <td bgcolor='grey'>&nbsp;&oslash;&nbsp;Per&nbsp;Minute&nbsp;</td>
            <td bgcolor='grey'>&nbsp;&oslash;&nbsp;Per&nbsp;Second&nbsp;</td>
        </tr>
        <tr>
            <td  align='right'>&nbsp;". number_format($serverStatus['Questions'], 0, '.', ',')."&nbsp;</td>
            <td  align='right'>&nbsp;". number_format(($serverStatus['Questions'] * 3600 / $serverStatus['Uptime']), 2, '.', ',')."&nbsp;</td>
            <td  align='right'>&nbsp;". number_format(($serverStatus['Questions'] * 60 / $serverStatus['Uptime']), 2, '.', ',')."&nbsp;</td>
            <td  align='right'>&nbsp;". number_format(($serverStatus['Questions'] / $serverStatus['Uptime']), 2, '.', ',')."&nbsp;</td>
        </tr>
    </table><br />";


    $QUERY_STATS .= "<table class='torrenttable' width='100%' border='0'>
        <tr>
            <td colspan='2' bgcolor='grey'>&nbsp;Query&nbsp;Type&nbsp;</td>
            <td bgcolor='grey'>&nbsp;&oslash;&nbsp;Per&nbsp;Hour&nbsp;</td>
            <td bgcolor='grey'>&nbsp;%&nbsp;</td>
        </tr>";


    $useBgcolorOne = TRUE;
    $countRows = 0;
    foreach ($queryStats as $name => $value) 
    {

      // For the percentage column, use Questions - Connections, because
      // the number of connections is not an item of the Query types
      // but is included in Questions. Then the total of the percentages is 100.

      $QUERY_STATS .= "<tr>
          <td>&nbsp;". htmlspecialchars($name)."&nbsp;</td>
          <td  align='right'>&nbsp;". number_format($value, 0, '.', ',')."&nbsp;</td>
          <td  align='right'>&nbsp;". number_format(($value * 3600 / $serverStatus['Uptime']), 2, '.', ',')."&nbsp;</td>
          <td  align='right'>&nbsp;". number_format(($value * 100 / ($serverStatus['Questions'] - $serverStatus['Connections'])), 2, '.', ',')."&nbsp;%&nbsp;</td>
      </tr>";

    }
    unset($countRows);
    unset($useBgcolorOne);
    $QUERY_STATS .= "</table>";

    //Unset used variables
    unset($serverStatus['Aborted_clients']);
    unset($serverStatus['Aborted_connects']);
    unset($serverStatus['Bytes_received']);
    unset($serverStatus['Bytes_sent']);
    unset($serverStatus['Connections']);
    unset($serverStatus['Questions']);
    unset($serverStatus['Uptime']);


    $STATUS_TABLE = '';
    
    if (!empty($serverStatus)) 
    {

      $STATUS_TABLE .= "<!-- Other status variables -->
          <b>More status variables</b><br />
          
      <table class='torrenttable' border='0' width='100%'>
          <tr>
              <td bgcolor='grey'>&nbsp;Variable&nbsp;</td>
              <td bgcolor='grey'>&nbsp;Value&nbsp;</td>
          </tr>";

      $useBgcolorOne = TRUE;
      $countRows = 0;
      foreach($serverStatus AS $name => $value) 
      {

        $STATUS_TABLE .= "<tr>
            <td>&nbsp;". htmlspecialchars(str_replace('_', ' ', $name))."&nbsp;</td>
            <td  align='right'>&nbsp;". htmlspecialchars($value)."&nbsp;</td>
        </tr>";

      }
      unset($useBgcolorOne);
      $STATUS_TABLE .= "</table>";
                   
    }

    $HTMLOUT .= "<table class='torrenttable' width='80%' cellpadding='4px'>
    <tr>
      <td colspan='2' class='colhead'>$TRAFFIC_STATS_HEAD</td>
    </tr>
    <tr>
      <td valign='top'>$TRAFFIC_STATS</td><td valign='top'>$TRAFFIC_STATS2</td>
    </tr>
    <tr>
      <td width='50%' valign='top'>$QUERY_STATS</td><td  valign='top'>$STATUS_TABLE</td>
    </tr>
    </table>";

    echo stdhead("Stats Overview") . $HTMLOUT . stdfoot();
?>
