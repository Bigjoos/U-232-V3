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
	
    //Do we wanna continue here, or skip to just the overview?
    if (isset($_GET['Do']) && isset($_GET['table'])) 
    { 
      $Do = ($_GET['Do'] === "T") ? sqlesc($_GET['Do']) : ""; //for later use!
      //Make sure the GET only has alpha letters and nothing else
      if(!ereg('[^A-Za-z_]+', $_GET['table'])) 
      { 
        $Table = '`'.$_GET['table'].'`';//add backquotes to GET or we is doomed!
      }
      else
      {
        stderr('MOD ERROR', "Pig Dog!");//Silly boy doh!!
      }
      
      $sql = "OPTIMIZE TABLE $Table";
      //preg match the sql incase it was hijacked somewhere!(will use CHECK|ANALYZE|REPAIR|later
      if (preg_match('@^(CHECK|ANALYZE|REPAIR|OPTIMIZE)[[:space:]]TABLE[[:space:]]'.$Table.'$@i', $sql)) 
      {
        //all good? Do it!
        @sql_query($sql) or sqlerr(__FILE__,__LINE__);
        
        header("Location: {$INSTALLER09['baseurl']}/staffpanel.php?tool=mysql_overview&action=mysql_overview&Do=F");
        exit;
        }
    }


    //byteunit array to prime formatByteDown function
    $GLOBALS["byteUnits"] = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');

    function byteformat($value, $limes = 2, $comma = 0)
    {
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

        if ($unit != $GLOBALS['byteUnits'][0]) 
        {
          $return_value = number_format($value, $comma, '.', ',');
        } 
        else 
        {
          $return_value = number_format($value, 0, '.', ',');
        }

        return array($return_value, $unit);
    } // end of the 'formatByteDown' function
////////////////// END FUNCTION LIST /////////////////////////
    
    
    $HTMLOUT = '';

    $HTMLOUT  .= "<h2>Mysql Server Table Status</h2>

    <!-- Start table -->

    <table class='torrenttable' border='1' cellpadding='4px'>

    <!-- Start table headers -->
    <tr>

    <td class='colhead'>Name</td>
                    
    <td class='colhead'>Size</td>
                    
    <td class='colhead'>Rows</td>
                    
    <td class='colhead'>Avg row lengtd</td>
                    
    <td class='colhead'>Data lengtd</td>
                    
    <!-- <td class='colhead'>Max_data_lengtd</td> -->
                    
    <td class='colhead'>Index length</td>
                    
    <td class='colhead'>Overhead</td>
                    
    <!-- <td class='colhead'>Auto_increment</td> -->
                    
    <!-- <td class='colhead'>Timings</td> -->
                    
    </tr>
            
    <!-- End table headers -->";


    $count = 0;
    
    $res = @sql_query("SHOW TABLE STATUS FROM {$INSTALLER09['mysql_db']}") or stderr(__FILE__,__LINE__);
    
    while ($row = mysqli_fetch_array($res)) 
    {
        list($formatted_Avg, $formatted_Abytes) = byteformat($row['Avg_row_length']);
        list($formatted_Dlength, $formatted_Dbytes) = byteformat($row['Data_length']);
        list($formatted_Ilength, $formatted_Ibytes) = byteformat($row['Index_length']);
        list($formatted_Dfree, $formatted_Fbytes) = byteformat($row['Data_free']);
        $tablesize = ($row['Data_length']) + ($row['Index_length']);
        list($formatted_Tsize, $formatted_Tbytes) = byteformat($tablesize, 3, ($tablesize > 0) ? 1 : 0);
        
        $thispage = "&amp;Do=T&amp;table=".urlencode($row['Name']);
        $overhead = ($formatted_Dfree > 0) ? "<a href='staffpanel.php?tool=mysql_overview&amp;action=mysql_overview$thispage'><font color='red'><b>$formatted_Dfree $formatted_Fbytes</b></font></a>" : "$formatted_Dfree $formatted_Fbytes";
        
        $HTMLOUT .= "<tr align='right'>
          <td align='left'><span style='font-weight:bold;'>".strtoupper($row['Name'])."</span></td>
          <td>{$formatted_Tsize} {$formatted_Tbytes}</td>
          <td>{$row['Rows']}</td>
          <td>{$formatted_Avg} {$formatted_Abytes}</td>
          <td>{$formatted_Dlength} {$formatted_Dbytes}</td>
          <td>{$formatted_Ilength} {$formatted_Ibytes}</td>
          <td>{$overhead}</td>
        </tr>
        <tr>
          <td colspan='7' align='right'><i><b>Row Format:</b></i> {$row['Row_format']}
          <br /><i><b>Create Time:</b></i> {$row['Create_time']}
          <br /><i><b>Update Time:</b></i> {$row['Update_time']}
          <br /><i><b>Check Time:</b></i> {$row['Check_time']}</td>
        </tr>";
        //do sums
        $count++;

      }//end while
      
      
    $HTMLOUT .= "<tr>
      <td><b>Tables: {$count}</b></td>
      <td colspan='6' align='right'>If it's <span style='font-weight:bold;color:red;'>RED</span> it probably needs optimising!!</td>
    </tr>

    <!-- End table -->
    </table>";


    echo stdhead("MySQL Overview") . $HTMLOUT . stdfoot();
?>
