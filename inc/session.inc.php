<?php

if (!isset($_SESSION)) {
    session_start();
}
//sprachdatei laden
if (isset($session_subdir) && $session_subdir == 1) {
    $session_path = '../';
} else {
    $session_path = '';
}
include_once $session_path."inc/lang/".$sv_server_lang."_session.lang.php";
include_once $session_path."inc/".$sv_server_lang."_links.inc.php";

//wenn nötig, die get/post/request-daten restaurieren
if (isset($_SESSION['restore_botcheck_data'])) {
    $_GET =		$_SESSION['save_get'];
    $_POST =		$_SESSION['save_post'];
    $_REQUEST =	$_SESSION['save_request'];

    unset($_SESSION['restore_botcheck_data']);
    unset($_SESSION['save_get']);
    unset($_SESSION['save_post']);
    unset($_SESSION['save_request']);
}

//schauen ob man eingeloggt ist
if (!isset($_SESSION['ums_user_id'])) {
    echo '
<!DOCTYPE html>
<html lang="de">
  	<head>
  		<script>
			if(top.frames.length > 0)
			top.location.href=self.location;
		</script>';

    include "cssinclude.php";

    echo '
		</head>';
	echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';
	
	echo '
		<div style="width: 100%;">
			<div class="info_box text3" style="margin: 30px auto 0 auto; font-size: 14px;">		
				<div style="color: #FF0000; margin-bottom: 25px; margin-top: 20px;">
					'.$session_lang['error1'].'
				</div>

				<div style="color: #00FF00; margin-bottom: 20px;">
					'.$session_lang['error3'].' <a href="'.$sv_link[1].'">'.$session_lang['error4'].'</a>
				<div>

			</div>
		</div>
	</body>
</html>';
    exit;
}

//session nach maximal einer zeit X durch den botschutz unterbrechen
//globale sessiondatei auslesen
$botfilename = '../botcheck/'.$_SESSION["ums_owner_id"].'.txt';
if (file_exists($botfilename)) {
    $botfile = fopen($botfilename, 'r');
    $bottime = trim(fgets($botfile, 1000));
    fclose($botfile);
    if ($bottime > $_SESSION['ums_session_start']) {
        $_SESSION['ums_session_start'] = $bottime;
        //$_SESSION['ums_one_way_bot_protection']=0;
    }
}

if (!isset($eftachatbotdefensedisable)) {
    $eftachatbotdefensedisable = 0;
}

if ((($_SESSION['ums_session_start'] + $sv_session_lifetime) < time()) && ($eftachatbotdefensedisable != 1)) {
    echo '<!DOCTYPE html>
<html lang="de">
<head>';

    include "cssinclude.php";

    //mitloggen wie oft die botschutzgrafik hintereinander neu geladen wird um scripter zu erkennen
    if (isset($_SESSION['botaccesscounter'])) {
        $_SESSION['botaccesscounter']++;
    } else {
        $_SESSION['botaccesscounter'] = 1;
    }


    if ($_SESSION['botaccesscounter'] > 10) {
        @mail($GLOBALS['env_admin_email'], $sv_server_tag.'botaccesscounter '.$_SESSION['botaccesscounter'].' user_id '.$_SESSION['ums_user_id'], time(), 'FROM: '.$GLOBALS['env_admin_email']);
    }

    //dateiname speichern um später darauf weiterleiten zu können
    $_SESSION['ums_bot_protection_filename'] = $_SERVER['PHP_SELF'];

    //beim ersten erscheinen des Botschutzes die $_GET/$_POST/$_REQUEST-Daten zwischenspeichern
    //unset($_SESSION['save_request']);
    if (!isset($_SESSION['save_get'])) {
        $_SESSION['save_get'] =		$_GET;
    }
    if (!isset($_SESSION['save_post'])) {
        $_SESSION['save_post'] =	$_POST;
    }
    if (!isset($_SESSION['save_request'])) {
        $_SESSION['save_request'] =	$_REQUEST;
    }

	echo '<meta http-equiv="expires" content="0">
	</head>';
	echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';
	echo '
	<script src="js/'.$sv_server_lang.'_jssammlung.js" type="text/javascript"></script>
	<div align="center">';

	if ($GLOBALS['sv_ang'] == 1) {
		echo '
		<script>
		$( document ).ready(function() {
			$("#iframe_main_container", window.parent.document).css("display", "");
		});
		</script>
		';
	}

	echo'
	<table border="0" cellpadding="0" cellspacing="0" class="cell">
	<tr align="center">
	<td width="13" height="37" class="rol">&nbsp;</td>
	<td colspan="4" align="center" class="ro text2">'.$session_lang['botschutzabfrage'].': '.$session_lang['botschutzinfo'].'</td>
	<td width="13" class="ror">&nbsp;</td>
	</tr>
	<tr align="center">
	<td height="25" class="rl">&nbsp;</td>
	<td colspan="4"><a href="'.$_SESSION['ums_bot_protection_filename'].'"><img src="imagegenerator.php?dummy='.time().'" alt="Bild" border="0"></a></td>
	<td class="rr">&nbsp;</td>
	</tr>
	<tr align="center">
	<td height="25" class="rl">&nbsp;</td>
	<td colSpan="4">
	<div style="width: 500px;">';

	for ($botschutz_c = 1;$botschutz_c <= 100;$botschutz_c++) {
		echo '<a href="botcheck.php?nummer='.$botschutz_c.'">
		<div style="float:left; width: 48px; 
		border: 2px solid #666666; padding: 0px; margin-top: 3px; margin-left: 1px; margin-right: 1px; font-size: 26px; background-color: #111111; color: #FFFFFF; text-decoration: none; white-space:nowrap;
		">'.$botschutz_c.'</div></a>';
	}

	echo '</div>
	</td>
	<td class="rr">&nbsp;</td>
	</tr>
	<tr>
	<td class="rul">&nbsp;</td>
	<td class="ru" colspan="4">&nbsp;</td>
	<td class="rur">&nbsp;</td>
	</tr>
	</table>
	</body></html>';
	exit;
}

