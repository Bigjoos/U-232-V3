<?php
if ($CURUSER) {
    if (isset($free) && (count($free) >= 1)) {
        foreach ($free as $fl) {
            switch ($fl['modifier']) {
            case 1:
                $mode = 'All Torrents Free';
                break;

            case 2:
                $mode = 'All Double Upload';
                break;

            case 3:
                $mode = 'All Torrents Free and Double Upload';
                break;

            case 4:
                $mode = 'All Torrents Silver';
                break;

            default:
                $mode = 0;
            }
            $htmlout.= ($fl['modifier'] != 0 && $fl['expires'] > TIME_NOW ? '
     <li>
     <a class="tooltip" href="#"><b><font color="red">'.$lang['gl_freeleech'].'</font></b><span class="custom info"><img src="./templates/1/images/Info.png" alt="Freeleech" height="48" width="48" />
     <em>'.$fl['title'].'</em>
     '.$mode.'<br />
     '.$fl['message'].' '.$lang['gl_freeleech_sb'].' '.$fl['setby'].'<br />'.($fl['expires'] != 1 ? ''.$lang['gl_freeleech_u'].' '.get_date($fl['expires'], 'DATE').' ('.mkprettytime($fl['expires'] - TIME_NOW).' '.$lang['gl_freeleech_tg'].')' : '').'  
     </span></a></li>' : '');
        }
    }
}
//=== free addon end
// End Class
// End File
