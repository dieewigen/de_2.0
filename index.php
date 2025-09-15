<?php
session_start();
include 'inc/sv.inc.php';
include 'inc/lang/'.$sv_server_lang.'_index.lang.php';
include 'inc/'.$sv_server_lang.'_links.inc.php';
include 'functions.php';
require_once 'vendor/autoload.php';

$detect = new \Detection\MobileDetect();

//cookie als loginhilfe setzen
if (!isset($_COOKIE["loginhelp"])) {
    $time = time() + 32000000;
    $pwstring = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $loginhelpstr='';
    for ($i = 1; $i <= 32; $i++) {
        $loginhelpstr .= $pwstring[rand(0, strlen($pwstring) - 1)];
    }
    setcookie("loginhelp", $loginhelpstr, $time);
    $_COOKIE["loginhelp"] = $loginhelpstr;
}

$fehlermsg = '';
$_SESSION['ums_rasse'] = 1;

//wenn die variable logout gesetzt ist, dann ausloggen und session zerstören
if (isset($_REQUEST['logout'])) {
    session_destroy();
    session_start();
    header("Location: index.php");
}

//login ist jetzt auch über den loginkey möglich, dieser ist jedoch nur 5 minuten gültig
if (isset($_REQUEST['loginkey']) && $_REQUEST['loginkey'] != '') {
    //db connect herstellen
    include('inccon.php');

    if (isset($_REQUEST['loginkey'])) {
        $_REQUEST['loginkey'] = SecureValue($_REQUEST['loginkey']);
    } else {
        $_REQUEST['loginkey'] = '';
    }

    $sql = "SELECT * FROM de_login WHERE loginkey='".$_REQUEST['loginkey']."' AND loginkeytime > UNIX_TIMESTAMP( ) - 600;";
    $result = mysqli_execute_query($GLOBALS['dbi'], $sql, []) or die(mysqli_error($GLOBALS['dbi']));
    $num = mysqli_num_rows($result);

    if ($num == 1) {

        session_regenerate_id(true);
        $row = mysqli_fetch_array($result);
        $ums_status = $row["status"];
        $_SESSION["ums_owner_id"] = $row["owner_id"];

        if ($ums_status == 3) {//urlaubsmodus/l�schmodus
            //den account aus dem l�schmodus holen
            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET delmode=0 WHERE user_id=?", [$row['user_id']]);
            //pa reaktivieren, wenn notwendig
            $patime = time();
            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET premium=1 WHERE patime>? AND user_id=?", [$patime, $row['user_id']]);


            //abbruchtermin
            $lc = $row["last_click"];
            $fat = strtotime($lc) + 259200;
            $zstatus = '';
            //1. m�glichkeit: account noch nicht l�nger als 3 tage im umode
            if ($fat > time()) {

                //fr�hestm�glicher abbruchtermin
                $fat = date("d.m.Y - G:i", $fat);

                //endtermin
                $et = $row["last_login"];
                $et = strtotime($et);
                $et = date("d.m.Y - G:i", $et);

                $fehlermsg = $index_lang['umode1'].$et;
                if ($et > $fat) {
                    $fehlermsg .= '<br>'.$index_lang['umode2'].$fat;
                }
                $zstatus = '(x Tage sind noch nicht um, kein Login m&oouml;glich)';
            }
            //2. m�glichkeit: account bereits 3 tage im umode
            else {
                $fehlermsg = $index_lang['umodebeendet'];
                //umode beenden
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET status=1 WHERE user_id=?", [$row['user_id']]);
                $ums_status = 1;
                $zstatus = '(x Tage sind um, Login m&ouml;glich)';
            }

            //info im kommentarfeld hinterlegen
            $datum = date("Y-m-d H:i:s", time());
            $comment = mysqli_execute_query($GLOBALS['dbi'], "SELECT kommentar FROM de_user_info WHERE user_id=?", [$row['user_id']]);
            $rowz = mysqli_fetch_array($comment);
            $eintrag = "$rowz[kommentar]\n$datum Loginversuch Account Status 3(Umode/L&ouml;schmode)! $zstatus\n$time";
            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_info SET kommentar=? WHERE user_id=?", [$eintrag, $row['user_id']]);


        }

        if ($ums_status == 1) {//alles richtig, spieler einloggen
            //logincheck, wie oft wurde bereits die grafikaufgabe falsch eingegeben
            $summe_fehleingaben = $row["points"] + 1;
            //spielerdaten aus der de_login uns sv.inc.php in die session packen
            $_SESSION['ums_user_id'] = $row["user_id"];
            $_SESSION['ums_user_id'] = $row["user_id"];
            $ums_nic = $row["nic"];
            $ums_servid = $sv_servid;
            $ums_zeitstempel = time();
            $ums_session_start = $ums_zeitstempel;
            //$ums_one_way_bot_protection=0;

            //spielerdaten aus de_user_data holen und in die session packen
            $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE user_id=?", [$_SESSION['ums_user_id']]) or die(mysqli_error($GLOBALS['dbi']));
            $row = mysqli_fetch_array($result);

            $techs = $row["techs"];
            //$_SESSION["ums_chatoff"]=$row["chatoff"];
            $_SESSION["ums_chatoff"] = 0;
            //$_SESSION["ums_chatoffallg"]=$row["chatoffallg"];
            $_SESSION['ums_spielername'] = $row["spielername"];
            $_SESSION['ums_rasse'] = $row["rasse"];

            if (isset($_REQUEST['mobi'])) {
                $_SESSION['ums_mobi'] = intval($_REQUEST['mobi']);
            } else {
                $_SESSION['ums_mobi'] = 0;
            }

            $_SESSION['desktop_version'] = isset($_COOKIE["desktop_version"]) ? intval($_COOKIE["desktop_version"]) : 0;

            //////////////////////////////////////////////////////////////////////
            //check ob die mobile Version verwendet werden soll
            //////////////////////////////////////////////////////////////////////
            //zuerst auf Cookie checken
            //wenn es kein Cookie gibt, dann eins per MobileDetect setzen
            if (isset($_COOKIE['use_mobile_version'])) {
                $_SESSION['ums_mobi'] = intval($_COOKIE['use_mobile_version']);

            } else {

                if ($detect->isMobile() || $detect->isTablet()) {
                    $value = 1;
                } else {//desktop
                    $value = 0;
                }

                $time = time() + 3600 * 24 * 365 * 5;
                setcookie("use_mobile_version", $value, $time);
                $_SESSION['ums_mobi'] = $value;
            }

            //////////////////////////////////////////////////////////////////////
            //spielerdaten aus de_user_info holen und in die session packen
            //////////////////////////////////////////////////////////////////////
            $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT submit, gpfad FROM de_user_info WHERE user_id=?", [$_SESSION['ums_user_id']]) or die(mysqli_error($GLOBALS['dbi']));
            $row = mysqli_fetch_array($result);
            $ums_submit = $row["submit"];

            //vote
            $schonabgestimmt = mysqli_execute_query($GLOBALS['dbi'], "SELECT vote_id FROM de_vote_stimmen WHERE user_id=?", [$_SESSION['ums_user_id']]) or die(mysqli_error($GLOBALS['dbi']));
            $i = 0;
            $gevotetevotes = array();
            while ($rew = mysqli_fetch_array($schonabgestimmt)) {
                $gevotetevotes[$i] = $rew['vote_id'];
                $i++;
            }
            $i = 0;
            $votevorhanden = 0;

            $db_umfrage = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_vote_umfragen.id, de_vote_umfragen.frage,de_vote_umfragen.startdatum FROM de_vote_umfragen, de_login WHERE de_vote_umfragen.status=1 AND UNIX_TIMESTAMP(de_login.register)<UNIX_TIMESTAMP(de_vote_umfragen.startdatum) AND de_login.user_id=? ORDER BY de_vote_umfragen.id", [$_SESSION['ums_user_id']]);
            while ($row = mysqli_fetch_array($db_umfrage)) {
                $i = 0;
                while ($i <= count($gevotetevotes) + 1) {
                    if ($gevotetevotes[$i] == $row['id']) {
                        $schongestimmt = 1;
                    }
                    $i++;
                }
                if ($schongestimmt != "1") {
                    $votevorhanden = 1;
                }
                $schongestimmt = 0;
            }

            if ($votevorhanden == "0") {
                $ums_vote = 0;
            } else {
                $ums_vote = 1;
            }

            //die ganzen sessionvariablen definieren
            $_SESSION['ums_user_id'] = $_SESSION['ums_user_id'];
            $_SESSION['ums_nic'] = $ums_nic;
            $_SESSION['ums_spielername'] = $_SESSION['ums_spielername'];
            $_SESSION['ums_user_ip'] = $_SERVER['REMOTE_ADDR'];

            $_SESSION['ums_servid'] = $ums_servid;
            $_SESSION['ums_zeitstempel'] = $ums_zeitstempel;
            $_SESSION['ums_session_start'] = $ums_session_start;
            $_SESSION['ums_rasse'] = $_SESSION['ums_rasse'];

            $_SESSION['ums_submit'] = $ums_submit;
            $_SESSION['ums_vote'] = $ums_vote;

            //überprüfen ob es der 1. login ist, in dem fall den beitritt im allgemeinen chat hinterlegen
            $db_daten = mysqli_execute_query(
                $GLOBALS['dbi'],
                "SELECT logins FROM de_login WHERE user_id=?",
                [$_SESSION['ums_user_id']]
            );
            $row = mysqli_fetch_assoc($db_daten);
            if ($row['logins'] == 1) {
                insert_chat_msg(0, 2, $_SESSION['ums_spielername'], ' <font color="#FFFF00">Ich habe mich '.$sv_server_tag.' angeschlossen.</font>');
                mysqli_execute_query(
                    $GLOBALS['dbi'],
                    "UPDATE de_login SET logins=logins+1 WHERE user_id=?",
                    [$_SESSION['ums_user_id']]
                );
            }

            //testen ob er das alternativ-pw verwendet hat
            $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_login WHERE user_id=? AND newpass = MD5(?)", [$_SESSION['ums_user_id'], ($_POST['pass'] ?? '')]) or die(mysqli_error($GLOBALS['dbi']));
            $num = mysqli_num_rows($result);

            if ($num == 1 and $_REQUEST["loginkey"] == '') {//er hat das alternative pw benutzt
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET pass=newpass WHERE user_id=?", [$_SESSION['ums_user_id']]);
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET newpass='' WHERE user_id=?", [$_SESSION['ums_user_id']]);
            }

            //testen ob er �ber den loginkey reingekommen ist
            $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_login WHERE user_id=? AND loginkey=?", [$_SESSION['ums_user_id'], $_REQUEST['loginkey']]) or die(mysqli_error($GLOBALS['dbi']));
            $num = mysqli_num_rows($result);

            if ($num == 1 and $_REQUEST['loginkey'] != '') {//er hat den loginkey benutzt
                $loginsystemlogin = 1;
                $_SESSION["ums_session_start"] = 0;

                $botfilename = '../botcheck/'.$_SESSION["ums_owner_id"].'.txt';
                //botschutzzeit aktualisieren
                if (file_exists($botfilename)) {
                    $botfile = fopen($botfilename, 'r');
                    $bottime = trim(fgets($botfile, 1000));
                    fclose($botfile);
                    if ($bottime > $_SESSION['ums_session_start']) {
                        $_SESSION['ums_session_start'] = $bottime;
                    }
                }

                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET loginkey='', loginkeytime=0, loginkeyip='' WHERE user_id=?", [$_SESSION['ums_user_id']]);
            }

            //loginzeit und ip aktualisieren
            //ip loggen
            $ip = getenv("REMOTE_ADDR");
            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET last_login=NOW(), last_ip=?, logins=logins+1, inaktmail = 0, delmode = 0 WHERE user_id=?", [$ip, $_SESSION['ums_user_id']]);
            $loginhelpstr = $_COOKIE["loginhelp"];

            $ip_adresse = $_SERVER['REMOTE_ADDR'];
            $parts = explode(".", $ip_adresse);
            $ip_adresse = $parts[0].'.x.'.$parts[2].'.'.$parts[3];
            mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_ip (user_id,ip,time,browser, loginhelp) VALUES(?,?,NOW(), ?, ?)", [$_SESSION['ums_user_id'], $ip_adresse, $_SERVER['HTTP_USER_AGENT'], $loginhelpstr]);

            //Logout anzeige für den title
            $sekundenbiszumlogout = ($_SESSION['ums_session_start'] + $sv_session_lifetime) - time();
            $restminuten = floor($sekundenbiszumlogout / 60);
            $restsekunden = $sekundenbiszumlogout - ($restminuten * 60);

            //die sessionzeit nullen, damit direkt die botprotection kommt
            $_SESSION['ums_session_start'] = 0;

            echo '<!DOCTYPE HTML>
  <html>
  <head>
  <title>DIE EWIGEN - '.$sv_server_tag.' - '.$sv_server_name.'�</title>';
            echo '</head>';

            //////////////////////////////////////////////////////////////
            //de-frameset ausgeben
            //////////////////////////////////////////////////////////////
			if ($_SESSION["ums_mobi"] == 1) { //mobile version
				//in der mobilen version auf die overview.php weiterleiten
				echo '<script>';
				echo 'location.href="overview.php";';
				echo '</script>';
			} else {
				//neue DE-Version, hier zwischen Standard und Classic-Dektopsicht unterscheiden
				if ($_SESSION['desktop_version'] == 0) {
					//Standard
					header('Location: dm.php');
				} else {
					//Classic
					header('Location: de_frameset.php');
				}
			}
            /*
            elseif ($_SESSION["ums_chatoff"] == 1) { //ohne chat
				echo '<frameset ID="gf" framespacing="0" border="0" cols="209,*,0,0" frameborder="0" '.$titlecounter.'>';
				echo '<frame name="Inhalt" target="h" src="menu.php" noresize marginwidth="0" marginheight="0">';
				echo '<frame name="h" src="overview.php" noresize target="_blank">';
				echo '</frameset>';
			} else { //mit chat
				echo '<frameset ID="gf" framespacing="0" border="0" cols="209,620,*,0,0" frameborder="0" '.$titlecounter.'>';
				echo '<frame name="Inhalt" target="h" src="menu.php" noresize marginwidth="0" marginheight="0">';
				echo '<frame name="h" src="overview.php" noresize target="_blank">';
				echo '<frame name="c" src="chat.php?frame=1" noresize target="_blank">';
				echo '</frameset>';
			}
            echo '</html>';
            */
            exit;
        } elseif ($ums_status == 0) {
            $fehlermsg = $index_lang['accnochnichtaktiv'];
        } elseif ($ums_status == 2) {
            $sel_supporter = mysqli_execute_query($GLOBALS['dbi'], "SELECT supporter FROM de_login WHERE user_id=?", [$row['user_id']]);
            $row_supporter = mysqli_fetch_array($sel_supporter);

            if ($row_supporter['supporter'] == "") {
                $row_supporter['supporter'] = "Support@Die-Ewigen.com";
            }
            //$fehlermsg=$fehlermsg.$index_lang[accountistgesperrt].' <a href="mailto:'.$row_supporter[supporter].'"><font color="#FF0000">'.$row_supporter[supporter].'</font></a>';
            $fehlermsg = $fehlermsg.'Der Account ist gesperrt. Wende Dich bitte per Ticketsystem (Accountverwaltung -> Support) an den Support.';
        } elseif ($ums_status == 4) {
            $fehlermsg = $index_lang['accumzug'];
        } elseif ($ums_status == 5) {
            $fehlermsg = $index_lang['falscheip'];
        }
    } else {
        $fehlermsg = $index_lang['falschezugangsdaten'];
    }
}
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
			<div class="info_box text3" style="margin: 30px auto 0 auto; font-size: 14px;">';

    if ($fehlermsg != '') {
        echo '<div style="color: #FF0000; margin-top: 20px;">'.$fehlermsg.'</div>';
    }

    echo '
				<div style="color: #FF0000; margin-bottom: 25px; margin-top: 20px;">
					Du bist nicht eingeloggt. Logge Dich bitte &uuml;ber die zentrale Accountverwaltung neu ein.
				</div>

				<div style="color: #00FF00; margin-bottom: 20px;">
					Du kannst das Fenster/Tab jetzt schließen, oder die zentrale Accountverwaltung <a href="'.$sv_link[1].'">öffnen</a>
				<div>

			</div>
		</div>
	</body>
</html>';
    exit;
