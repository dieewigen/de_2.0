<?php
include "inc/sv.inc.php";
include "inc/lang/".$sv_server_lang."_geloescht.lang.php";
include 'inc/'.$sv_server_lang.'_links.inc.php';

//nach dem Löschauftrag (options.php), die Session ist schon beendet; cssinclude.php setzt Rasse/Ansicht dann auf Standardwerte
echo '<!DOCTYPE html>
<html lang="de">
<head>
<title>'.$gel_lang['title'].'</title>
<script>
if(top.frames.length > 0)
top.location.href=self.location;
</script>';

include "cssinclude.php";

echo '</head>';
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

//wer sich innerhalb von 3 Tagen wieder einloggt, behält den Account (dann normaler Urlaubsmodus)
echo '<div class="mod bc-seite">
	<div class="bc-kopf"><span class="mod-typ">Die Ewigen</span><b>'.$gel_lang['accountloeschung'].'</b></div>
	<div class="mod-meldung mod-meldung-warn">'.$gel_lang['msg1'].'</div>
	<p class="bc-text">'.$gel_lang['msg2'].'</p>
	<div class="mod-hinweis">'.$gel_lang['msg3'].'</div>
	<div class="bc-aktionen"><a href="'.$sv_link[1].'" class="mod-btn">Zum Login</a></div>
</div>';

echo '</body>
</html>';
exit;
