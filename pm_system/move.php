<?php
//=== don't allow direct access
if (!defined('BUNNY_PM_SYSTEM')) {
    $HTMLOUT = '';
    $HTMLOUT.= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
sql_query('UPDATE messages SET location = '.sqlesc($mailbox).' WHERE id='.sqlesc($pm_id).' AND receiver = '.sqlesc($CURUSER['id']));
if (mysqli_affected_rows($GLOBALS["___mysqli_ston"]) === 0) stderr('Error', 'Message could not be moved! <a class="altlink" href="pm_system.php?action=view_message&id='.$pm_id.'>BACK</a> to message.');
header('Location: pm_system.php?action=view_mailbox&singlemove=1&box='.$mailbox);
die();
?>
