<?php

if (!isset($_SESSION)) {
    session_start(['cookie_httponly' => true]); // JavaScript kann das Login-Cookie nicht auslesen
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
		<div class="mod bc-seite">
			<div class="bc-kopf"><span class="mod-typ">Sitzung</span><b>Nicht eingeloggt</b></div>
			<div class="mod-meldung mod-meldung-fehler">'.$session_lang['error1'].'</div>
			<p class="bc-text">'.$session_lang['error3'].' '.$session_lang['error4'].'.</p>
			<div class="bc-aktionen"><a href="'.$sv_link[1].'" class="mod-btn">Zur Accountverwaltung</a></div>
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
    //SCRIPT_NAME statt PHP_SELF: PHP_SELF enthält PATH_INFO und damit fremde Zeichen (XSS/Weiterleitung);
    //zusätzlich nur einfache Dateinamen, der Wert landet später in header("Location: ...")
    $bot_protection_filename = basename($_SERVER['SCRIPT_NAME']);
    if (!preg_match('/^[a-z0-9_]+\.php$/i', $bot_protection_filename)) {
        $bot_protection_filename = 'overview.php';
    }
    $_SESSION['ums_bot_protection_filename'] = $bot_protection_filename;

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

    //einmal-token fuer die antwort-links und zeitstempel der anzeige;
    //eine evtl. vorher angezeigte aufgabe verfaellt beim neu-rendern,
    //der imagegenerator erzeugt zum neuen token eine neue aufgabe
    $_SESSION['botcheck_token'] = bin2hex(random_bytes(8));
    $_SESSION['botcheck_page_time'] = time();
    unset($_SESSION['botcheck_task']);
    unset($_SESSION['botcheck_answer']);

	echo '<meta http-equiv="expires" content="0">
	</head>';
	echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';
	echo '
	<script src="js/'.$sv_server_lang.'_jssammlung.js" type="text/javascript"></script>';

	if ($GLOBALS['sv_ang'] == 1) {
		echo '
		<script>
		$( document ).ready(function() {
			$("#iframe_main_container", window.parent.document).css("display", "");
		});
		</script>
		';
	}

	//Abfrage: Bild (Klick lädt die Seite neu und bringt eine neue Aufgabe), Hinweis, Zahlen 1-100 in Zehnerreihen
	echo '
	<div class="mod bc-seite">
		<div class="bc-kopf">
			<span class="mod-typ">'.$session_lang['botschutzabfrage'].'</span>
			<b>'.$session_lang['botschutzinfo'].'</b>
		</div>
		<a href="'.htmlspecialchars($_SESSION['ums_bot_protection_filename']).'" class="bc-bild"><img src="imagegenerator.php?dummy='.$_SESSION['botcheck_token'].'" alt="Rechenaufgabe" width="500" height="160"></a>
		<div class="mod-hinweis bc-hinweis">'.$session_lang['botschutzhinweis'].'</div>
		<div class="bc-zahlen">';

	for ($botschutz_c = 1;$botschutz_c <= 100;$botschutz_c++) {
		if ($botschutz_c % 10 == 1) {
			echo '<div class="bc-reihe">';
		}
		echo '<a href="botcheck.php?nummer='.$botschutz_c.'&amp;t='.$_SESSION['botcheck_token'].'">'.$botschutz_c.'</a>';
		if ($botschutz_c % 10 == 0) {
			echo '</div>';
		}
	}

	echo '</div>
	</div>
	</body></html>';
	exit;
}

