<?php
/** latestuser index **/
    if(($latestuser_cache = $mc1->get_value('latestuser')) === false) {
    $latestuser_cache = mysqli_fetch_assoc(sql_query('SELECT id, username, class, donor, warned, enabled, chatpost, leechwarn, pirate, king FROM users WHERE status="confirmed" ORDER BY id DESC LIMIT 1'));
    $latestuser_cache['id']  = (int)$latestuser_cache['id']; // so is stored as an integer
    $latestuser_cache['class']  = (int)$latestuser_cache['class']; // so is stored as an integer
    $latestuser_cache['warned']  = (int)$latestuser_cache['warned']; // so is stored as an integer
    $latestuser_cache['chatpost']  = (int)$latestuser_cache['chatpost']; // so is stored as an integer
    $latestuser_cache['leechwarn']  = (int)$latestuser_cache['leechwarn']; // so is stored as an integer
    $latestuser_cache['pirate']  = (int)$latestuser_cache['pirate']; // so is stored as an integer
    $latestuser_cache['king']  = (int)$latestuser_cache['king']; // so is stored as an integer
    /** OOP **/
    $mc1->cache_value('latestuser', $latestuser_cache, $INSTALLER09['expires']['latestuser']);
    }
    
    $latestuser = '<div class="headline">Latest Member</div>
    <div class="headbody">
    Welcome to our newest member 
    <b><a href="userdetails.php?id='.$latestuser_cache['id'].'">'.format_username($latestuser_cache).'</a>!</b>
    </div><br />';
//==MemCached latest user
$HTMLOUT .= $latestuser;
//==
// End Class

// End File
