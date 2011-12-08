<?php
if ($CURUSER["id"] != $user["id"])
      if ($CURUSER['class'] >= UC_STAFF)
        $showpmbutton = 1;
      elseif ($user["acceptpms"] == "yes")
      {
        $r = sql_query("SELECT id FROM blocks WHERE userid={$user['id']} AND blockid={$CURUSER['id']}") or sqlerr(__FILE__,__LINE__);
        $showpmbutton = (mysqli_num_rows($r) == 1 ? 0 : 1);
      }
      elseif ($user["acceptpms"] == "friends")
      {
        $r = sql_query("SELECT id FROM friends WHERE userid={$user['id']} AND friendid={$CURUSER['id']}") or sqlerr(__FILE__,__LINE__);
        $showpmbutton = (mysqli_num_rows($r) == 1 ? 1 : 0);
      }
    if (isset($showpmbutton))
      $HTMLOUT .= "<tr>
      <td colspan='2' align='center'>
      <form method='get' action='pm_system.php?'>
        <input type='hidden' name='action' value='send_message' />
        <input type='hidden' name='receiver' value='{$user["id"]}' />
        <input type='hidden' name='returnto' value='".urlencode($_SERVER['REQUEST_URI'])."' />
        <input type='submit' value='{$lang['userdetails_msg_btn']}' class='btn' />
      </form>
      </td></tr>";
//==end
// End Class

// End File
