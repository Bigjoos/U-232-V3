<?php
/**********************************************************
New 2010 forums that don't suck for TB based sites....
pretty much coded page by page, but coming from a 
history ot TBsourse and TBDev and the many many 
coders who helped develop them over time.
proper credits to follow :)

beta fri june 11th 2010 v0.1

thanks to pdq & elephant for suggestions :D

Powered by Bunnies!!!
**********************************************************/
if (!defined('BUNNY_FORUMS')) 
{
	$HTMLOUT ='';
	$HTMLOUT .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
        <head>
        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
        <title>ERROR</title>
        </head><body>
        <h1 style="text-align:center;">ERROR</h1>
        <p style="text-align:center;">How did you get here? silly rabbit Trix are for kids!.</p>
        </body></html>';
	echo $HTMLOUT;
	exit();
}

   $topic_id = (isset($_GET['topic_id']) ? intval($_GET['topic_id']) :  (isset($_POST['topic_id']) ? intval($_POST['topic_id']) :  0));
   $forum_id = (isset($_GET['forum_id']) ? intval($_GET['forum_id']) :  (isset($_POST['forum_id']) ? intval($_POST['forum_id']) :  0));
   

	//=== first see if they are being norty...
	$norty_res = sql_query('SELECT min_class_read FROM forums WHERE id = '.sqlesc($forum_id));
	$norty_arr = mysqli_fetch_row($norty_res);

    if (!is_valid_id($topic_id) || $norty_arr[0] > $CURUSER['class'] || !is_valid_id($forum_id))
    {
	stderr('Error', 'Bad ID.');
    }
	
	//=== see if they are subscribed already
	$res = sql_query('SELECT id FROM subscriptions WHERE user_id = '.sqlesc($CURUSER['id']).' AND topic_id = '.sqlesc($topic_id));
	$arr = mysqli_fetch_row($res);    

	if ($arr[0] > 0)
	{
	stderr('Error', 'You are already subscribed to this topic!');
	}

	//=== ok, that the hell, let's add it \o/
	sql_query('INSERT INTO `subscriptions` (`user_id`, `topic_id`) VALUES ('.sqlesc($CURUSER['id']).', '.sqlesc($topic_id).')');
	
	header('Location: forums.php?action=view_topic&topic_id='.$topic_id.'&s=1'); 
	
die();
?>
