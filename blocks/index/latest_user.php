<?php
/** latestuser index **/
    $latestuser_cache = $mc1->get_value('latestuser');
    if ($latestuser_cache === false) {
    $latestuser_cache = mysqli_fetch_assoc(sql_query('SELECT id, username FROM users WHERE status="confirmed" ORDER BY id DESC LIMIT 1'))/* or sqlerr(__FILE__, __LINE__)*/;
    $latestuser_cache['id']  = (int)$latestuser_cache['id']; // so is stored as an integer
    /** OOP **/
    $mc1->cache_value('latestuser', $latestuser_cache, $INSTALLER09['expires']['latestuser']);
    }
    
    $latestuser = '<div class="headline">Latest Member</div>
    <div class="headbody">
    Welcome to our newest member 
    <b><a href="userdetails.php?id='.$latestuser_cache['id'].'">'.$latestuser_cache['username'].'</a>!</b>
    </div><br />';
//==MemCached latest user
$HTMLOUT .= $latestuser;
//==
// End Class

// End File
