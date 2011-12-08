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
require_once(INCL_DIR.'html_functions.php');
require_once(CLASS_DIR.'class_check.php');
class_check(UC_ADMINISTRATOR);

    $lang = array_merge( $lang, load_language('ad_index') );
    
    $inbound = array_merge( $_GET, $_POST );
    
    if( !isset($inbound['mode']) )
      $inbound['mode'] = '';
      
    $form_code = '';
	
    $month_names = array( 1 => 'January', 'February', 'March'     , 'April'  , 'May'     , 'June',
										 'July'   , 'August'  , 'September' , 'October', 'November', 'December'
								  );

		switch($inbound['mode'])
		{
			case 'show_reg':
        result_screen('reg');
				break;
				
			case 'show_topic':
        result_screen('topic');
				break;
					
			case 'topic':
        main_screen('topic');
				break;
			
			case 'show_comms':
        result_screen('comms');
				break;
					
			case 'comms':
        main_screen('comms');
				break;
			
			case 'show_torrents':
        result_screen('torrents');
				break;
					
			case 'torrents':
        main_screen('torrents');
				break;
			
			case 'show_reps':
        result_screen('reps');
				break;
					
			case 'reps':
        main_screen('reps');
				break;
			
			case 'show_post':
        result_screen('post');
				break;
					
			case 'post':
        main_screen('post');
				break;
			
			case 'show_msg':
        result_screen('msg');
				break;
					
			case 'msg':
        main_screen('msg');
				break;
				
			case 'show_views':
        show_views();
				break;
					
			case 'views':
        main_screen('views');
				break;
			
			default:
        main_screen('reg');
				break;
		}

	


function show_views(){
	
		global $inbound, $month_names;
		
		$page_title = "Statistic Center Results";
		$page_detail = "Showing topic view statistics";
		
		/* This function not available in this version, you need tbdev2010 */
		stderr('ATTENTION', 'This operation not available in tbdev2009');
		
		if ( ! checkdate($inbound['to_month']   ,$inbound['to_day']   ,$inbound['to_year']) )
		{
			stderr('USER ERROR', "The 'Date To:' time is incorrect, please check the input and try again");
		}
		
		if ( ! checkdate($inbound['from_month'] ,$inbound['from_day'] ,$inbound['from_year']) )
		{
			stderr('USER ERROR', "The 'Date From:' time is incorrect, please check the input and try again");
		}
		
		
		$to_time   = mktime(12 ,0 ,0 ,$inbound['to_month'],$inbound['to_day'],$inbound['to_year']  );
		$from_time = mktime(12 ,0 ,0 ,$inbound['from_month'],$inbound['from_day'],$inbound['from_year']);
		
		
		$human_to_date   = getdate($to_time);
		$human_from_date = getdate($from_time);
		
		$sql = array( 'from_time' => $from_time, 'to_time' => $to_time, 'sortby' => $inbound['sortby'] );
		
		$q = sql_query( "SELECT SUM(t.views) as result_count, t.forumid, f.name as result_name
					FROM topics t
					LEFT JOIN forums f ON (f.id=t.forumid)
					WHERE t.start_date > '{$sql['from_time']}'
					AND t.start_date < '{$sql['to_time']}'
					GROUP BY t.forumid
					ORDER BY result_count {$sql['sortby']}" ) or sqlerr(__FILE__,__LINE__);

		$running_total = 0;
		$max_result    = 0;
		
		$results       = array();
		
		$menu = make_side_menu();
		
		$heading = "Topic Views ({$human_from_date['mday']} {$month_names[$human_from_date['mon']]} {$human_from_date['year']} to {$human_to_date['mday']} {$month_names[$human_to_date['mon']]} {$human_to_date['year']})";
		
		$htmlout = "<div>
      <div style='background: grey; height: 25px;'>
      <span style='font-weight: bold; font-size: 12pt;'>Statistics Center</span>
      </div><br />
    {$menu}
		
		<div><table align='center' border='0' cellpadding='5' cellspacing='0' width='70%'>
		<tr>
    <td colspan='3' align='left'>{$heading}</td>
    </tr>
		<tr>
    <td align='center' width='20%'>Date</td>
    <td align='center' width='70%'>Result</td>
    <td align='center' width='10%'>Count</td>
    </tr>";
		

		if ( mysqli_num_rows($q) )
		{
		
			while ($row = mysqli_fetch_assoc($q) )
			{
			
				if ( $row['result_count'] >  $max_result )
				{
					$max_result = $row['result_count'];
				}
				
				$running_total += $row['result_count'];
			
				$results[] = array(
									 'result_name'     => $row['result_name'],
									 'result_count'    => $row['result_count'],
								  );
								  
			}
			
			foreach( $results as $data )
			{
			
    			$img_width = intval( ($data['result_count'] / $max_result) * 100 - 8);
    			
    			if ($img_width < 1)
    			{
    				$img_width = 1;
    			}
    			
    			$img_width .= '%';
    			
    			$htmlout .= "<tr>
    			<td valign='middle'>$date</td>
    			<td valign='middle'><img src='{$INSTALLER09['pic_base_url']}/bar_left.gif' border='0' width='4' height='11' align='middle' alt='' /><img src='{$INSTALLER09['pic_base_url']}/bar.gif' border='0' width='$img_width' height='11' align='middle' alt='' /><img src='{$INSTALLER09['pic_base_url']}/bar_right.gif' border='0' width='4' height='11' align='middle' alt='' /></td>
					<td valign='middle'><center>{$data['result_count']}</center></td>
					</tr>";
			}
			
			$htmlout .= "<tr>
<td valign='middle' width='20%'>&nbsp;</td>
<td valign='middle' width='70%'><div align='right'><b>Total</b></div></td>
<td valign='middle' width='10%'><center><b>{$running_total}</b></center></td>
</tr>";
		
		}
		else
		{
			$htmlout .= "<tr><td colspan='3' align='center'>No results found</td></tr>";
		}
		
		$htmlout .= '</table></div></div>';
		
		echo stdhead($page_title) . $htmlout . stdfoot();
		
	}
	

function result_screen($mode='reg'){

		global $INSTALLER09, $inbound, $month_names;
		
		$page_title = "Statistic Center Results";
		$page_detail = "&nbsp;";
		
		if ( ! checkdate($inbound['to_month']   ,$inbound['to_day']   ,$inbound['to_year']) )
		{
			stderr('USER ERROR', "The 'Date To:' time is incorrect, please check the input and try again");
		}
		
		if ( ! checkdate($inbound['from_month'] ,$inbound['from_day'] ,$inbound['from_year']) )
		{
			stderr('USER ERROR', "The 'Date From:' time is incorrect, please check the input and try again");
		}
		
		
		$to_time   = mktime(0 ,0 ,0 ,$inbound['to_month']   ,$inbound['to_day']   ,$inbound['to_year']  );
		$from_time = mktime(0 ,0 ,0 ,$inbound['from_month'] ,$inbound['from_day'] ,$inbound['from_year']);
		
		
		$human_to_date   = getdate($to_time);
		$human_from_date = getdate($from_time);
		
		if ($mode == 'reg')
		{
			$table     = 'Registration Statistics';
			
			$sql_table = 'users';
			$sql_field = 'added';
			
			$page_detail = "Showing the number of users registered. (Note: All times based on GMT)";
		}
		else if ($mode == 'topic')
		{
			$table     = 'New Topic Statistics';
			
			$sql_table = 'topics';
			$sql_field = 'added';
			
			$page_detail = "Showing the number of topics started. (Note: All times based on GMT)";
		}
		else if ($mode == 'post')
		{
			$table     = 'Post Statistics';
			
			$sql_table = 'posts';
			$sql_field = 'added';
			
			$page_detail = "Showing the number of posts. (Note: All times based on GMT)";
		}
		else if ($mode == 'msg')
		{
			$table     = 'PM Sent Statistics';
			
			$sql_table = 'messages';
			$sql_field = 'added';
			
			$page_detail = "Showing the number of sent messages. (Note: All times based on GMT)";
		}
		else if ($mode == 'comms')
		{
			$table     = 'Comment Statistics';
			
			$sql_table = 'comments';
			$sql_field = 'added';
			
			$page_detail = "Showing the number of sent comments. (Note: All times based on GMT)";
		}
	  else if ($mode == 'torrents')
		{
			$table     = 'Torrents Statistics';
			
			$sql_table = 'torrents';
			$sql_field = 'added';
			
			$page_detail = "Showing the number of Torrents. (Note: All times based on GMT)";
		}
	  else if ($mode == 'reps')
		{
			$table     = 'Reputation Statistics';
			
			$sql_table = 'reputation';
			$sql_field = 'dateadd';
			
			$page_detail = "Showing the number of Reputations. (Note: All times based on GMT)";
		}
	  
	  
    switch ($inbound['timescale'])
    {
      case 'daily':
        $sql_date = "%w %U %m %Y";
        $php_date = "F jS - Y";
        break;
        
      case 'monthly':
        $sql_date = "%m %Y";
          $php_date = "F Y";
          break;
          
      default:
        // weekly
        $sql_date = "%U %Y";
        $php_date = " [F Y]";
        break;
		}
		
		$sort_by = ($inbound['sortby'] == 'DESC') ? 'DESC' : 'ASC';
		
		$sql = array( 'from_time' => $from_time,
                  'to_time'   => $to_time,
                  'sortby'    => $sort_by,
                  'sql_field' => $sql_field,
									'sql_table' => $sql_table,
									'sql_date'  => $sql_date );
		
		$q1 = sql_query( "SELECT MAX({$sql['sql_field']}) as result_maxdate,
				 COUNT(*) as result_count,
				 DATE_FORMAT(from_unixtime({$sql['sql_field']}),'{$sql['sql_date']}') AS result_time
				 FROM {$sql['sql_table']}
				 WHERE {$sql['sql_field']} > '{$sql['from_time']}'
				 AND {$sql['sql_field']} < '{$sql['to_time']}'
				 GROUP BY result_time
				 ORDER BY {$sql['sql_field']} {$sql['sortby']}" );

		
		$running_total = 0;
		$max_result = 0;
		
		$results = array();
		
		$heading = ucfirst($inbound['timescale'])." $table ({$human_from_date['mday']} {$month_names[$human_from_date['mon']]} {$human_from_date['year']} to {$human_to_date['mday']} {$month_names[$human_to_date['mon']]} {$human_to_date['year']})";
		
		
		$menu = make_side_menu();
		
		$htmlout = "<div>
      <div style='background: grey; height: 25px;'>
      <span style='font-weight: bold; font-size: 12pt;'>Statistics Center</span>
      </div><br />
    {$menu}
		
		<div><table align='center' border='0' cellpadding='5' cellspacing='0' width='70%'>
		<tr>
    <td colspan='3' align='left'>{$heading}<br />{$page_detail}</td>
    </tr>
		<tr>
    <td align='center' width='20%'>Date</td>
    <td align='center' width='70%'>Result</td>
    <td align='center' width='10%'>Count</td>
    </tr>";
		
		if ( mysqli_num_rows($q1) )
		{
		
			while ($row = mysqli_fetch_assoc($q1) )
			{
			
				if ( $row['result_count'] >  $max_result )
				{
					$max_result = $row['result_count'];
				}
				
				$running_total += $row['result_count'];
			
				$results[] = array(
									 'result_maxdate'  => $row['result_maxdate'],
									 'result_count'    => $row['result_count'],
									 'result_time'     => $row['result_time'],
								  );
								  
			}
			
			foreach( $results as $data )
			{
			
    			$img_width = intval( ($data['result_count'] / $max_result) * 100 - 8);
    			
    			if ($img_width < 1)
    			{
    				$img_width = 1;
    			}
    			
    			$img_width .= '%';
    			
    			if ($inbound['timescale'] == 'weekly')
    			{
    				$date = "Week #".strftime("%W", $data['result_maxdate']) . date( $php_date, $data['result_maxdate'] );
    			}
    			else
    			{
    				$date = date( $php_date, $data['result_maxdate'] );
    			}
    			
    			$htmlout .= "<tr>
    			<td valign='middle'>$date</td>
    			<td valign='middle'><img src='{$INSTALLER09['pic_base_url']}/bar_left.gif' border='0' width='4' height='11' align='middle' alt='' /><img src='{$INSTALLER09['pic_base_url']}/bar.gif' border='0' width='$img_width' height='11' align='middle' alt='' /><img src='{$INSTALLER09['pic_base_url']}/bar_right.gif' border='0' width='4' height='11' align='middle' alt='' /></td>
					<td valign='middle'><center>{$data['result_count']}</center></td>
					</tr>";
			}
			
			$htmlout .= "<tr>
<td valign='middle' width='20%'>&nbsp;</td>
<td valign='middle' width='70%'><div align='right'><b>Total</b></div></td>
<td valign='middle' width='10%'><center><b>{$running_total}</b></center></td>
</tr>";
		
		}
		else
		{
			$htmlout .= "<tr><td colspan='3' align='center'>No results found</td></tr>";
		}
		
		$htmlout .= '</table></div></div>';
		
		echo stdhead($page_title) . $htmlout . stdfoot();
		
	}
	

function main_screen($mode='reg'){

		global $INSTALLER09;
		
		$page_title = "Statistic Center";
		$page_detail = "Please define the date ranges and other options below.<br />Note: The statistics generated are based on the information currently held in the database.";
		
		if ($mode == 'reg')
		{
			$form_code = 'show_reg';
			
			$table     = 'Registration Statistics';
		}
		else if ($mode == 'topic')
		{
			$form_code = 'show_topic';
			
			$table     = 'New Topic Statistics';
		}
		else if ($mode == 'post')
		{
			$form_code = 'show_post';
			
			$table     = 'Post Statistics';
		}
		else if ($mode == 'msg')
		{
			$form_code = 'show_msg';
			
			$table     = 'PM Statistics';
		}
		else if ($mode == 'views')
		{
			$form_code = 'show_views';
			
			$table     = 'Topic View Statistics';
		}
		else if ($mode == 'comms')
		{
			$form_code = 'show_comms';
			
			$table     = 'Comment Statistics';
		}
		else if ($mode == 'torrents')
		{
			$form_code = 'show_torrents';
			
			$table     = 'Torrrents Statistics';
		}
		else if ($mode == 'reps')
		{
			$form_code = 'show_reps';
			
			$table     = 'Reputation Statistics';
		}
		
		
		$old_date = getdate(time() - (3600 * 24 * 90));
		$new_date = getdate(time() + (3600 * 24));
		
		
		$menu = make_side_menu();

		$htmlout = "<div>
      <div style='background: grey; height: 25px;'>
      <span style='font-weight: bold; font-size: 12pt;'>Statistics Center</span>
      </div><br />
    {$menu}
    <form action='{$INSTALLER09['baseurl']}/staffpanel.php?tool=stats_extra&amp;action=stats_extra' method='post' name='StatsForm'>
    <input name='mode' value='{$form_code}' type='hidden' />

	
    <div style='text-align: left; width: 50%; border: 1px solid blue; padding: 5px;'>
		<div style='background: grey; height: 25px; margin-bottom:20px;'>
      <span style='font-weight: bold; font-size: 12pt;'>{$table}</span>
    </div>
    <fieldset><legend><strong>Info</strong></legend>
    {$page_detail}</fieldset>
		<fieldset><legend><strong>Date From</strong></legend>";
		
		$htmlout .= make_select( 'from_month', make_month(), $old_date['mon']  ).'&nbsp;&nbsp;';
		
    $htmlout .= make_select( 'from_day', make_day()  , $old_date['mday'] ).'&nbsp;&nbsp;';
		$htmlout .= make_select( 'from_year', make_year() , $old_date['year'] ).'</fieldset>';
									     
		$htmlout .= "<fieldset><legend><strong>Date To</strong></legend>";
		$htmlout .= make_select( 'to_month', make_month(), $new_date['mon']  ).'&nbsp;&nbsp;';
		$htmlout .= make_select( 'to_day', make_day()  , $new_date['mday'] ).'&nbsp;&nbsp;';
		$htmlout .= make_select( 'to_year', make_year() , $new_date['year'] ).'</fieldset>';
		
		if ($mode != 'views')
		{
			$htmlout .= "<fieldset><legend><strong>Time scale</strong></legend>";
			$htmlout .= make_select( 'timescale' , array( 0 => array( 'daily', 'Daily'), 1 => array( 'weekly', 'Weekly' ), 2 => array( 'monthly', 'Monthly' ) ) ).'</fieldset>';
		}
						     
		$htmlout .= "<fieldset><legend><strong>Result Sorting</strong></legend>";
		$htmlout .= make_select( 'sortby' , array( 0 => array( 'asc', 'Ascending - Oldest dates first'), 1 => array( 'desc', 'Descending - Newest dates first' ) ), 'desc' ).'</fieldset>';
									     									     
		$htmlout .= "<fieldset><legend><strong>Submit it!</strong></legend>
				<input value='Show' class='btn' accesskey='s' type='submit' />
			</fieldset>

		</div>
	
    </form></div>";
		
		echo stdhead($page_title) . $htmlout . stdfoot();

	}
	

function make_year(){

		$time_now = getdate();
		
		$return = array();
		
		$start_year = 2005;
		
		$latest_year = intval($time_now['year']);
		
		if ($latest_year == $start_year)
		{
			$start_year -= 1;
		}
		
		for ( $y = $start_year; $y <= $latest_year; $y++ )
		{
			$return[] = array( $y, $y);
		}
		
		return $return;
}
	

function make_month(){

		global $month_names;
		
		$return = array();
		
		for ( $m = 1 ; $m <= 12; $m++ )
		{
			$return[] = array( $m, $month_names[$m] );
		}
		
		return $return;
}
	

function make_day(){

		$return = array();
		
		for ( $d = 1 ; $d <= 31; $d++ )
		{
			$return[] = array( $d, $d );
		}
		
		return $return;
}

function make_select($name, $in=array(), $default="") {
	
		$html = "<select name='$name' class='dropdown'>\n";
		
		foreach ($in as $v)
		{
			$selected = "";
			
			if ( ($default != "") and ($v[0] == $default) )
			{
				$selected = " selected='selected'";
			}
			
			$html .= "<option value='{$v[0]}'{$selected}>{$v[1]}</option>\n";
		}
		
		$html .= "</select>\n\n";
		
		return $html;
}


function make_side_menu() {

    global $INSTALLER09;
    
    $htmlout = "<div style='float:left;border: 1px solid black;padding:5px;'>
    <div align='left'><strong>Statistic Menu</strong></div>
    <div align='left'>&nbsp;&nbsp;<a href='{$INSTALLER09['baseurl']}/staffpanel.php?tool=stats_extra&amp;action=stats_extra&amp;mode=reg' style='text-decoration: none;'>Registration Stats</a></div>
    <div align='left'>&nbsp;&nbsp;<a href='{$INSTALLER09['baseurl']}/staffpanel.php?tool=stats_extra&amp;action=stats_extra&amp;mode=topic' style='text-decoration: none;'>New Topic Stats</a></div>
    <div align='left'>&nbsp;&nbsp;<a href='{$INSTALLER09['baseurl']}/staffpanel.php?tool=stats_extra&amp;action=stats_extra&amp;mode=post' style='text-decoration: none;'>Post Stats</a></div>
    <div align='left'>&nbsp;&nbsp;<a href='{$INSTALLER09['baseurl']}/staffpanel.php?tool=stats_extra&amp;action=stats_extra&amp;mode=msg' style='text-decoration: none;'>Personal Message</a></div>
    <div align='left'>&nbsp;&nbsp;<a href='{$INSTALLER09['baseurl']}/staffpanel.php?tool=stats_extra&amp;action=stats_extra&amp;mode=views' style='text-decoration: none;'>Topic Views</a></div>
    <div align='left'>&nbsp;&nbsp;<a href='{$INSTALLER09['baseurl']}/staffpanel.php?tool=stats_extra&amp;action=stats_extra&amp;mode=comms' style='text-decoration: none;'>Comment Stats</a></div>
    <div align='left'>&nbsp;&nbsp;<a href='{$INSTALLER09['baseurl']}/staffpanel.php?tool=stats_extra&amp;action=stats_extra&amp;mode=torrents' style='text-decoration: none;'>Torrents Stats</a></div>
    <div align='left'>&nbsp;&nbsp;<a href='{$INSTALLER09['baseurl']}/staffpanel.php?tool=stats_extra&amp;action=stats_extra&amp;mode=reps' style='text-decoration: none;'>Reputation Stats</a></div>
</div>";

    return $htmlout;

}
?>
