<?php
include "inc/sv.inc.php";
include "inc/lang/".$sv_server_lang."_urlaub.lang.php";
include 'inc/'.$sv_server_lang.'_links.inc.php';

//nach dem Wechsel in den Urlaubsmodus (options.php), die Session ist schon beendet; cssinclude.php setzt Rasse/Ansicht dann auf Standardwerte
echo '<!DOCTYPE html>
<html lang="de">
<head>
<title>'.$urlaub_lang['title'].'</title>
<script>
if(top.frames.length > 0)
top.location.href=self.location;
</script>';

include "cssinclude.php";

echo '</head>';
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

echo '<div class="mod bc-seite">
	<div class="bc-kopf"><span class="mod-typ">Die Ewigen</span><b>'.$urlaub_lang['urlaubsmodus'].'</b></div>
	<div class="mod-meldung mod-meldung-ok">'.$urlaub_lang['msg1'].'</div>
	<div class="bc-aktionen"><a href="'.$sv_link[3].'" class="mod-btn mod-btn-leise">Zur Startseite</a></div>
</div>';

echo '</body>
</html>';
exit;
