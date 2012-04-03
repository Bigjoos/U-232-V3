<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/

//putyn's rate mod
function getRate($id,$what) { 
		global $CURUSER;
		if($id == 0 || !in_array($what,array("topic","torrent")))
			return;
	$qy = sql_query("SELECT sum(r.rating) as sum, count(r.rating) as count, r2.id as rated, r2.rating  FROM rating as r LEFT JOIN rating as r2 ON (r2.".$what." = ".$id." AND r2.user = ".sqlesc($CURUSER["id"]).") WHERE r.".$what." = ".sqlesc($id)." GROUP BY r.".$what );
	$a = mysqli_fetch_assoc($qy);
	
		$p = ($a["count"] > 0 ? round((($a["sum"] / $a["count"]) * 20), 2) : 0);
		if($a["rated"])
			$rate = "<ul class=\"star-rating\" title=\"You rated this ".$what." ".htmlsafechars($a["rating"])." star".(htmlsafechars($a["rating"]) >1 ? "s" : "")."\"><li style=\"width: ".$p."%;\" class=\"current-rating\">.</li></ul>";
		else {
			$i=1;
			$rate = "<ul class=\"star-rating\"><li style=\"width: ".$p."%;\" class=\"current-rating\">.</li>";
		foreach(array("one-star","two-stars","three-stars","four-stars","five-stars") as $star) {
			$rate .= "<li><a href=\"rating.php?id=".(int)$id."&amp;rate=".$i."&amp;ref=".urlencode($_SERVER["REQUEST_URI"])."&amp;what=".$what."\" class=\"".$star."\" onclick=\"do_rate(".$i.",".$id.",'".$what."'); return false\" title=\"".$i." star".($i > 1 ? "s" : "" )." out of 5\" >$i</a></li>";
			$i++;
		}
			$rate .="</ul>";
		}
		switch($what) {
			case "torrent" : $return = "<div id=\"rate_".$id."\">".$rate."</div>";
			break;
			case "topic" : $return = "<div id=\"rate_".$id."\">".$rate."</div>";
			break;
		}
		return $return;
	}
	
	function showRate($rate_sum,$rate_count)
	{
		$p = ($rate_count > 0 ? round((($rate_sum/ $rate_count) * 20), 2) : 0);
		return "<ul class=\"star-rating\"><li style=\"width: ".$p."%;\" class=\"current-rating\" >.</li></ul>";
	}
//end putyn's rate mode

?>
