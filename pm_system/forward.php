<?php
$body = '';
//=== don't allow direct access 
if (!defined('BUNNY_PM_SYSTEM')) 
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

    //=== Get the info
    $res = sql_query('SELECT * FROM messages WHERE id='.sqlesc($pm_id)) or sqlerr(__FILE__,__LINE__);
    $message = mysqli_fetch_assoc($res);

        if ($message['sender'] == $CURUSER['id'] && $message['sender'] == $CURUSER['id'] || mysqli_num_rows($res) === 0)  
            stderr('Error','Come, you are a tedious fool.');

    //=== if not from curuser then get who from
    if($message['sender'] !== $CURUSER['id'])
        {
        $res_forward = sql_query('SELECT username FROM users WHERE id='.sqlesc($message['sender'])) or sqlerr(__FILE__,__LINE__);
        $arr_forward = mysqli_fetch_assoc($res_forward);
        $forwarded_username = ($message['sender'] === 0 ? 'System' : (mysqli_num_rows($res_forward) === 0 ? 'Un-known' : $arr_forward['username']));
        }
    else
        $forwarded_username = htmlsafechars($CURUSER['username']);

//=== print out the forwarding page
$HTMLOUT .='<h1>Fwd: '.htmlsafechars($message['subject']).'</h1>
        <form action="pm_system.php" method="post">
        <input type="hidden" name="id" value="'.$pm_id.'" />
        <input type="hidden" name="action" value="forward_pm" />
    <table border="0" cellspacing="0" cellpadding="5" align="center" style="max-width:600px;">
    <tr>
        <td align="left" colspan="2" class="colhead" valign="top"><h1>forward message 
        <img src="pic/arrow_next.gif" alt=":" />Fwd: '.htmlsafechars($message['subject']).'</h1></td>
    </tr>
    <tr>
        <td align="right" class="one" valign="top"><span style="font-weight: bold;">To:</span></td>
        <td align="left" class="one" valign="top"><input type="text" name="to" value="Enter Username" class="member" onfocus="this.value=\'\';" /></td>
    </tr>
    <tr>
        <td align="right" class="one" valign="top"><span style="font-weight: bold;">Orignal Sender:</span></td>
        <td align="left" class="one" valign="top"><span style="font-weight: bold;">'.$forwarded_username.'</span></td>
    </tr>
    <tr>
        <td align="right" class="one" valign="top"><span style="font-weight: bold;">From:</span></td>
        <td align="left" class="one" valign="top"><span style="font-weight: bold;">'.$CURUSER['username'].'</span></td>
    </tr>
    <tr>
        <td align="right" class="one" valign="top"><span style="font-weight: bold;">Subject:</span></td>
        <td align="left" class="one" valign="top"><input type="text" class="text_default" name="subject" value="Fwd: '.htmlsafechars($message['subject']).'" /></td>
    </tr>
    <tr>
        <td align="center" class="one"></td>
        <td align="left" class="two">-------- Original Message from '.$forwarded_username.': --------<br />'.format_comment($message['msg']).'</td>
    </tr>
    <tr>
        <td align="right" class="one" valign="top"></td>
        <td align="left" class="one"><span style="font-weight: bold;">You can add your own message, it will appear above the PM being forwarded.</span></td>
    </tr>
    <tr>
        <td align="right" class="one" valign="top"><span style="font-weight: bold;">Message:</span></td>
        <td align="left" class="one" valign="top">'.BBcode($body, FALSE).'</td>
    </tr>
    <tr>
        <td colspan="2" align="center" class="one">'.($CURUSER['class'] >= UC_STAFF ? '<span style="font-weight: bold;color:red;">Mark as URGENT!</span>
        <input type="checkbox" name="urgent" value="yes" />&nbsp' : '').' Save Message 
        <input type="checkbox" name="save" value="1" />
        <input type="hidden" name="first_from" value="'.$forwarded_username.'" /> 
        <input type="submit" class="button" name="move" value="Forward" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" /></td>
    </tr>
    </table></form>';
?>
