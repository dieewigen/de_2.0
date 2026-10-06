<?php

use DieEwigen\DE2\Model\Npc\FleetControl;
use DieEwigen\DE2\Model\Npc\NPCBuildControl;
use DieEwigen\DE2\Model\Npc\FleetRecycler;
use DieEwigen\DE2\Model\Npc\Types\FleetPresetConfig;

include "inc/header.inc.php";
include "inc/artefakt.inc.php";
include 'inc/lang/' . $sv_server_lang . '_politics.lang.php';
include "lib/transaction.lib.php";
include_once "functions.php";

$db_daten = mysqli_execute_query(
    $GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, techs, sector, `system`, score, newtrans, newnews, secmoves, secatt, votefor 
     FROM de_user_data 
     WHERE user_id=?",
    [$_SESSION['ums_user_id']]
);
$row = mysqli_fetch_array($db_daten);
$restyp01 = $row[0];
$restyp02 = $row[1];
$restyp03 = $row[2];
$restyp04 = $row[3];
$restyp05 = $row[4];
$punkte = $row["score"];
$newtrans = $row["newtrans"];
$newnews = $row["newnews"];
$sector = $row["sector"];
$system = $row["system"];
$techs = $row["techs"];
$secmoves = $row["secmoves"];
$secatt = $row["secatt"];
$votefor = $row["votefor"];

$db_daten = mysqli_execute_query(
    $GLOBALS['dbi'],
    "SELECT name, url, bk, skmes, techs, ssteuer, e1, e2, pass, votecounter, col, ekey 
     FROM de_sector 
     WHERE sec_id=?",
    [$sector]
);
$row = mysqli_fetch_array($db_daten);
$url = $row["url"];
$name = $row["name"];
$bk = $row["bk"];
$stext = $row["skmes"];
$sectechs = $row["techs"];
$ssteuer = $row["ssteuer"];
$dbsecpass = $row["pass"];
$secfleet = $row["e1"] + $row["e2"];
$votecounter = $row["votecounter"];
$seccol = $row["col"];
$secekey = $row["ekey"];


//maximalen tick auslesen
//$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT MAX(tick) AS tick FROM de_user_data", []);
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT wt AS tick FROM de_system LIMIT 1", []);
$row = mysqli_fetch_array($result);
$maxtick = $row["tick"];

//anzahl der spieler im sektor auslesen
$result = mysqli_execute_query(
    $GLOBALS['dbi'],
    "SELECT COUNT(*) AS wert FROM de_user_data WHERE sector=?",
    [$sector]
);
$row = mysqli_fetch_array($result);
$spielerimsektor = $row['wert'];

?>
<!doctype html>
<html>

<head>
    <title><?php echo $politics_lang["politik"] ?></title>
    <?php include "cssinclude.php"; ?>
</head>

<?php
echo '<body class="theme-rasse' . $_SESSION['ums_rasse'] . ' ' . (($_SESSION['ums_mobi'] == 1) ? 'mobile' : 'desktop') . '">';

include "resline.php";

//Meldungen der Aktionen, ausgegeben unter den Reitern; $art: ok, fehler, warn
$meldungen = array();
function pol_meldung($text, $art = 'ok')
{
    global $meldungen;
    $meldungen[] = '<div class="mod-meldung mod-meldung-' . $art . '">' . $text . '</div>';
}

//SK - sekstat sichtbar
if (isset($_REQUEST["do"]) && $_REQUEST["do"] == 2 and $system == issectorcommander()) {

    $sys = intval($_REQUEST["sys"]);
    //daten des spielers auslesen
    $db_daten = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT spielername, secstatdisable 
     FROM de_user_data 
     WHERE sector=? AND `system`=?",
        [$sector, $sys]
    );
    if (mysqli_num_rows($db_daten) == 1) {
        $row = mysqli_fetch_array($db_daten);
        $spielername = $row["spielername"];
        $secstatdisable = $row["secstatdisable"];
        if ($secstatdisable == 0) $secstatdisable = 1;
        elseif ($secstatdisable == 1) $secstatdisable = 0;
        mysqli_execute_query(
            $GLOBALS['dbi'],
            "UPDATE de_user_data SET secstatdisable=? WHERE sector=? AND `system`=?",
            [$secstatdisable, $sector, $sys]
        );
        //info in die sektorhistorie packen - komplette spielerlöschung
        if ($secstatdisable == 0)
            mysqli_execute_query(
                $GLOBALS['dbi'],
                "INSERT INTO de_news_sector(wt, typ, sector, text) VALUES (?, ?, ?, ?)",
                [$maxtick, '5', $sector, $spielername]
            );
        if ($secstatdisable == 1)
            mysqli_execute_query(
                $GLOBALS['dbi'],
                "INSERT INTO de_news_sector(wt, typ, sector, text) VALUES (?, ?, ?, ?)",
                [$maxtick, '6', $sector, $spielername]
            );
        pol_meldung('Sektorstatus f&uuml;r <b>' . $spielername . '</b> ' . ($secstatdisable == 0 ? 'einsehbar.' : 'nicht mehr einsehbar.'));
    }
}

//für einen sk stimmen
$letsgo = isset($_POST['letsgo']) ? $_POST['letsgo'] : '';
if (!empty($letsgo) && $sector > 1) {
    $userslist = intval($_POST['userslist']);
    mysqli_execute_query(
        $GLOBALS['dbi'],
        "UPDATE de_user_data SET votefor=? WHERE user_id=?",
        [$userslist, $_SESSION['ums_user_id']]
    );
    $votefor = $userslist;
    pol_meldung($userslist > 0 ? 'Deine Stimme ist gespeichert.' : 'Du stimmst jetzt f&uuml;r niemanden.');
}

//einen neuen sektor beantragen
$getnewsec = isset($_REQUEST['getnewsec']) ?? '';
if (!empty($getnewsec) && $secmoves < $sv_max_secmoves && $techs[26] == '1') {
    //abfrage da die funktion  zum erstellen nur noch f�r premium accounts zul�ssig ist
    if (1 == 1) {
        //schauen ob derjenige schon einen sektor beantragt hat
        $result1 = mysqli_execute_query(
            $GLOBALS['dbi'],
            "SELECT user_id FROM de_sector_umzug WHERE user_id=?",
            [$_SESSION['ums_user_id']]
        );
        $anz1 = mysqli_num_rows($result1);
        if ($anz1 == 0) //es l�uft noch kein umzug
        {
            //pw generieren und umzug in der db eintragen
            //neues pw generieren
            $ok = 0;
            while ($ok == 0) {
                $newpass = '';
                $pwstring = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $newpass = $pwstring[rand(0, strlen($pwstring) - 1)];
                //10 Zeichen wie die Spalte pass (vorher 11, ohne strikten SQL-Modus wurde das letzte abgeschnitten)
                for ($i = 1; $i < 10; $i++) $newpass .= $pwstring[rand(0, strlen($pwstring) - 1)];

                $result1 = mysqli_execute_query(
                    $GLOBALS['dbi'],
                    "SELECT user_id FROM de_sector_umzug WHERE pass=?",
                    [$newpass]
                );
                $result2 = mysqli_execute_query(
                    $GLOBALS['dbi'],
                    "SELECT sec_id FROM de_sector WHERE pass=?",
                    [$newpass]
                );

                if (mysqli_num_rows($result1) == 0 and mysqli_num_rows($result2) == 0) $ok = 1;
            }
            //eintrag in der db machen, dass er umzieht
            mysqli_execute_query(
                $GLOBALS['dbi'],
                "INSERT INTO de_sector_umzug (user_id, typ, sector, `system`, pass, ticks) VALUES (?, 1, 0, 0, ?, 192)",
                [$_SESSION['ums_user_id'], $newpass]
            );
            pol_meldung('Der Sektor ist beantragt. Gib das Passwort an die Spieler weiter, die mitkommen sollen.');
        }
    } else pol_meldung($politics_lang["msg_4"], 'fehler');
}

//einem bestehenden sektor joinen
$joinsec  = $_REQUEST['joinsec']  ?? '';
$secpass  = $_REQUEST['secpass']  ?? '';
if (!empty($joinsec) && $secmoves < $sv_max_secmoves && $secpass != '' && $techs[26] == '1') {
    //es gibt 2 m�glichkeiten zu joinen
    //1. es gibt den sektor schon
    //2. der sektor wird beantragt
    //art des joinens festellen

    //anzahl der leute die in den sektor ziehen wollen
    $result1 = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT user_id FROM de_sector_umzug WHERE pass=?",
        [$secpass]
    );
    $anz1 = mysqli_num_rows($result1);

    //gibt es den sektor schon?
    $result2 = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT sec_id FROM de_sector WHERE pass=?",
        [$secpass]
    );
    $anz2 = mysqli_num_rows($result2);

    //hat man evtl. schon was am laufen
    $result3 = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT user_id FROM de_sector_umzug WHERE user_id=?",
        [$_SESSION['ums_user_id']]
    );
    $anz3 = mysqli_num_rows($result3);

    if ($anz2 > 0 and $anz3 == 0) //der sektor besteht schon, direkt reinmoven typ 2
    {
        //schauen ob noch platz im sektor ist
        $row2 = mysqli_fetch_array($result2);
        $zielsec = $row2["sec_id"];
        $result = mysqli_execute_query(
            $GLOBALS['dbi'],
            "SELECT user_id FROM de_user_data WHERE sector=?",
            [$zielsec]
        );
        $accanz = mysqli_num_rows($result);
        $result = mysqli_execute_query(
            $GLOBALS['dbi'],
            "SELECT user_id FROM de_sector_umzug WHERE typ=2 AND sector=?",
            [$zielsec]
        );
        $accanz += mysqli_num_rows($result);

        if ($accanz < $sv_max_user_per_regsector) {
            //es ist noch ein platz frei -> spieler zieht um
            $time = date("YmdHis");
            //account sperren
            mysqli_execute_query(
                $GLOBALS['dbi'],
                "UPDATE de_login SET status = ? WHERE user_id = ?",
                [4, $_SESSION['ums_user_id']]
            );

            //eintrag in der db machen, dass er umzieht
            mysqli_execute_query(
                $GLOBALS['dbi'],
                "INSERT INTO de_sector_umzug (user_id, typ, sector) VALUES (?, ?, ?)",
                [$_SESSION['ums_user_id'], 2, $zielsec]
            );

            //nachricht an den account schicken
            mysqli_execute_query(
                $GLOBALS['dbi'],
                "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, ?, ?, ?)",
                [$_SESSION['ums_user_id'], 3, $time, $politics_lang["msg_25"]]
            );

            mysqli_execute_query(
                $GLOBALS['dbi'],
                "UPDATE de_user_data SET newnews = ? WHERE user_id = ?",
                [1, $_SESSION['ums_user_id']]
            );
            die('<div class="mod pol-meldungen"><div class="mod-meldung mod-meldung-ok">' . $politics_lang["msg_5"] . '</div></div></body></html>');
        } else pol_meldung($politics_lang["msg_6"], 'fehler');
    } elseif ($anz1 > 0 and $anz2 == 0 and $anz3 == 0) //ein weiterer spieler der sich der beantragung anschlie�t -> typ 1
    {
        //schauen ob noch platz im sektor ist
        if ($anz1 < $sv_max_user_per_regsector) {
            //es ist noch ein platz frei -> spieler zieht um
            //eintrag in der db machen, dass er umzieht
            mysqli_execute_query(
                $GLOBALS['dbi'],
                "INSERT INTO de_sector_umzug (user_id, typ, pass, ticks) VALUES (?, 1, ?, 192)",
                [$_SESSION['ums_user_id'], $secpass]
            );
            pol_meldung('Du nimmst an der Sektorbeantragung teil.');
        } else pol_meldung($politics_lang["msg_6"], 'fehler');
    } else pol_meldung($politics_lang["msg_7"], 'fehler');
}


//laufende sektorbeantragung löschen
$cancelgetnewsec = isset($_POST['cancelgetnewsec']) ? $_POST['cancelgetnewsec'] : '';
if (!empty($cancelgetnewsec)) {
    mysqli_execute_query(
        $GLOBALS['dbi'],
        "DELETE FROM de_sector_umzug WHERE user_id=? AND typ=1",
        [$_SESSION['ums_user_id']]
    );
    if (mysqli_affected_rows($GLOBALS['dbi']) > 0) {
        pol_meldung('Du nimmst nicht mehr an der Sektorbeantragung teil.');
    }
}


$voteoutcancel = isset($_POST['voteoutcancel']) ? $_POST['voteoutcancel'] : '';
if (!empty($voteoutcancel) && $system == issectorcommander()) {
    mysqli_execute_query(
        $GLOBALS['dbi'],
        "DELETE FROM de_sector_voteout WHERE sector_id=?",
        [$sector]
    );
    pol_meldung('Die Abstimmung ist abgebrochen.');
    $time = date("YmdHis");
    //nachrichten an alle spieler schicken
    $db_daten = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT user_id FROM de_user_data WHERE sector=?",
        [$sector]
    );
    while ($row = mysqli_fetch_array($db_daten)) {
        mysqli_execute_query(
            $GLOBALS['dbi'],
            "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 3, ?, ?)",
            [$row['user_id'], $time, $politics_lang["msg_26"]]
        );
        mysqli_execute_query(
            $GLOBALS['dbi'],
            "UPDATE de_user_data SET newnews = 1 WHERE user_id = ?",
            [$row['user_id']]
        );
    }
}

//ein spieler gibt seine stimme zum rausvoten ab
$setvoteout = isset($_POST['setvoteout']) ? $_POST['setvoteout'] : '';
$vspielerwahl = isset($_POST['vspielerwahl']) ? $_POST['vspielerwahl'] : '';
if (!empty($setvoteout) && ($vspielerwahl == $politics_lang["ja"] || $vspielerwahl == $politics_lang["nein"] || $vspielerwahl == $politics_lang["egal"])) {
    //erstmal schauen ob ein vote l�uft
    $result1 = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT user_id, votes FROM de_sector_voteout WHERE sector_id=?",
        [$sector]
    );
    $anz1 = mysqli_num_rows($result1);
    if ($anz1 != 0) {
        $row = mysqli_fetch_array($result1);
        $vvotes = $row["votes"];
        $vuser_id = $row["user_id"];
        //wie hat der spieler abgestimmt?
        if ($vspielerwahl == $politics_lang["nein"]) $vsvote = 0;
        elseif ($vspielerwahl == $politics_lang["ja"]) $vsvote = 1;
        elseif ($vspielerwahl == $politics_lang["egal"]) $vsvote = 2;
        //stimme z�hlen
        $vvotes[$system] = $vsvote;
        //schaue ob evtl. schon das ziel des votes erreicht ist
        $jastimmen = 0;
        for ($i = 1; $i <= ($sv_maxsystem + 5); $i++) if ($vvotes[$i] == '1') $jastimmen++;
        //anzahl der aktiven spieler im sektor bestimmen
        //$result1 = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE sector=?", [$sector]);
        $result1 = mysqli_execute_query(
            $GLOBALS['dbi'],
            "SELECT de_user_data.`system` FROM de_login 
       LEFT JOIN de_user_data ON(de_login.user_id = de_user_data.user_id) 
       WHERE de_user_data.sector=? AND de_login.status=1",
            [$sector]
        );
        $anz1 = mysqli_num_rows($result1);
        $prozentwert = ($jastimmen * 100) / $anz1;
        if ($prozentwert >= $sv_voteoutgrenze) {
            $result1 = mysqli_execute_query(
                $GLOBALS['dbi'],
                "SELECT `system` FROM de_user_data WHERE user_id=?",
                [$vuser_id]
            );
            $row1 = mysqli_fetch_array($result1);
            $vsystem = $row1["system"];
            $time = date("YmdHis");
            //spieler wurde rausgevotet
            pol_meldung($politics_lang["msg_27"]);

            //gesperrte user und leute im umode kommen direkt in sektor 1
            //dazu erstmal accountstatus auslesen
            $result1 = mysqli_execute_query(
                $GLOBALS['dbi'],
                "SELECT status FROM de_login WHERE user_id=?",
                [$vuser_id]
            );
            $row1 = mysqli_fetch_array($result1);
            $accstatus = $row1["status"];
            if ($accstatus == 2 or $accstatus == 3) {
                //nachricht an den account schicken
                mysqli_execute_query(
                    $GLOBALS['dbi'],
                    "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 3, ?, ?)",
                    [$vuser_id, $time, $politics_lang["msg_25"]]
                );

                //auf 0:0 verschieben damit er in sektor 1 landet und einstellungen updaten
                mysqli_execute_query(
                    $GLOBALS['dbi'],
                    "UPDATE de_user_data 
			 SET sector=0, `system`=0, newnews=1, spend01=0, spend02=0, spend03=0, spend04=0, spend05=0, votefor=0, secstatdisable=0 
			 WHERE user_id = ?",
                    [$vuser_id]
                );

                //voteumfrage aus der db l�schen
                mysqli_execute_query(
                    $GLOBALS['dbi'],
                    "DELETE FROM de_sector_voteout WHERE sector_id=?",
                    [$sector]
                );
                //nachricht an alle im sektor schicken
                $db_daten = mysqli_execute_query(
                    $GLOBALS['dbi'],
                    "SELECT user_id FROM de_user_data WHERE sector=?",
                    [$sector]
                );
                while ($row = mysqli_fetch_array($db_daten)) {
                    mysqli_execute_query(
                        $GLOBALS['dbi'],
                        "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 3, ?, ?)",
                        [$row['user_id'], $time, $politics_lang["msg_27"]]
                    );
                    mysqli_execute_query(
                        $GLOBALS['dbi'],
                        "UPDATE de_user_data SET newnews = 1 WHERE user_id = ?",
                        [$row['user_id']]
                    );
                }

                //wenn er BK ist, den Posten auf 0 setzen
                mysqli_execute_query(
                    $GLOBALS['dbi'],
                    "UPDATE de_sector SET bk=0 WHERE sec_id=? AND bk=?",
                    [$sector, $vsystem]
                );

                //votetimer/votecounter f�r den sektor setzen
                $votetimer = mt_rand(20, 120);
                //$votetimer=0;
                $sv_sector_votetime_lock = 0;
                //if($accstatus!=2)
                mysqli_execute_query(
                    $GLOBALS['dbi'],
                    "UPDATE de_sector SET votetimer=?, votecounter=? WHERE sec_id=?",
                    [$votetimer, $sv_sector_votetime_lock, $sector]
                );
            } else {
                //accountstatus sichern und account sperren
                mysqli_execute_query(
                    $GLOBALS['dbi'],
                    "UPDATE de_login SET savestatus=status WHERE user_id = ?",
                    [$vuser_id]
                );
                mysqli_execute_query(
                    $GLOBALS['dbi'],
                    "UPDATE de_login SET status = 4 WHERE user_id = ?",
                    [$vuser_id]
                );
                mysqli_execute_query(
                    $GLOBALS['dbi'],
                    "UPDATE de_user_data SET spend01=0, spend02=0, spend03=0, spend04=0, spend05=0 WHERE user_id = ?",
                    [$vuser_id]
                );
                //eintrag in der db machen, dass er umzieht
                mysqli_execute_query(
                    $GLOBALS['dbi'],
                    "INSERT INTO de_sector_umzug (user_id, typ, sector, `system`) VALUES (?, 0, ?, ?)",
                    [$vuser_id, $sector, $vsystem]
                );
                //nachricht an den account schicken
                mysqli_execute_query(
                    $GLOBALS['dbi'],
                    "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 3, ?, ?)",
                    [$vuser_id, $time, $politics_lang["msg_25"]]
                );
                mysqli_execute_query(
                    $GLOBALS['dbi'],
                    "UPDATE de_user_data SET newnews = 1 WHERE user_id = ?",
                    [$vuser_id]
                );
                //voteumfrage aus der db l�schen
                mysqli_execute_query(
                    $GLOBALS['dbi'],
                    "DELETE FROM de_sector_voteout WHERE sector_id=?",
                    [$sector]
                );
                //nachricht an alle im sektor schicken
                $result = mysqli_execute_query(
                    $GLOBALS['dbi'],
                    "SELECT user_id FROM de_user_data WHERE sector = ?",
                    [$sector]
                );

                while ($row = $result->fetch_assoc()) {
                    mysqli_execute_query(
                        $GLOBALS['dbi'],
                        "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, ?, ?, ?)",
                        [$row["user_id"], 3, $time, $politics_lang["msg_27"]]
                    );

                    mysqli_execute_query(
                        $GLOBALS['dbi'],
                        "UPDATE de_user_data SET newnews = ? WHERE user_id = ?",
                        [1, $row["user_id"]]
                    );
                }
                //votetimer/votecounter f�r den sektor setzen
                $votetimer = mt_rand(16, 96);
                //$votetimer=0;
                //$sv_sector_votetime_lock=0;
                mysqli_execute_query(
                    $GLOBALS['dbi'],
                    "UPDATE de_sector SET votetimer=?, votecounter=? WHERE sec_id=?",
                    [$votetimer, $sv_sector_votetime_lock, $sector]
                );
            }
        } else {
            //spieler wurde noch nicht rausgevotet
            //db mit den stimmen updaten
            mysqli_execute_query(
                $GLOBALS['dbi'],
                "UPDATE de_sector_voteout SET votes = ? WHERE sector_id = ?",
                [$vvotes, $sector]
            );
            pol_meldung('Deine Stimme ist abgegeben.');
        }
        //echo $prozentwert;
    }
}

//startet ein rausvoten
$voteout = isset($_POST['voteout']) ? $_POST['voteout'] : '';
$voteoutlist = isset($_POST['voteoutlist']) ? $_POST['voteoutlist'] : '';
if (!empty($voteout) && $system == issectorcommander() && $sector > 1) {
    //beim reinsetzen des votes erstmal schauen ob nicht schon eins existiert
    $result1 = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT sector_id FROM de_sector_voteout WHERE sector_id = ?",
        [$sector]
    );
    $anz1 = $result1->num_rows;

    //anhand des spielernamens die user_id und den sector rausfinden
    $result2 = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT user_id, sector FROM de_user_data WHERE spielername = ?",
        [$voteoutlist]
    );
    $row = $result2->fetch_assoc();
    //id des zu votenden auslesen
    $vuser_id = $row["user_id"];
    //schaue ob der spieler auch wirklich in dem sektor ist
    $vsector = $row["sector"];
    $anz2 = $result2->num_rows;

    //überprüfen ob der spieler gesperrt ist, falls ja ist es kostenlos und es gibt keinen counter
    $db_daten = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT status, delmode FROM de_login WHERE user_id = ?",
        [$vuser_id]
    );
    $row = $db_daten->fetch_assoc();
    //beim Umbau auf mysqli (Juli 2025) verloren gegangen: gesperrte Spieler lassen sich rausvoten, ohne Zähler
    if (($row['status'] ?? 0) == 2) {
        $gesperrt = 1;
        $votecounter = 0;
    } else {
        $gesperrt = 0;
    }

    //�berpr�fen ob er l�nger als 7 tage offline war

    if ($gesperrt == 1) {
        if ($votecounter == 0) {
            if ($anz1 == 0 and $anz2 > 0) {

                if ($vsector != $sector) die($politics_lang["msg_8"]);
                $vticks = 96 * 3;
                //votes erstmal auf 3 setzen, d.h. noch nicht gew�hlt, daf�r 1 oder dagegen 0, 2 egal
                $vvotes = 's';
                for ($i = 1; $i <= ($sv_maxsystem + 5); $i++) $vvotes .= '3';

                //eintrag in die db packen
                mysqli_execute_query(
                    $GLOBALS['dbi'],
                    "INSERT INTO de_sector_voteout (sector_id, user_id, votes, ticks) VALUES (?, ?, ?, ?)",
                    [$sector, $vuser_id, $vvotes, $vticks]
                );
                //rohstoffe vom sektor abziehen

                //nachricht an alle im sektor schicken, dass es ein vote gibt
                $time = date("YmdHis");
                $result = mysqli_execute_query(
                    $GLOBALS['dbi'],
                    "SELECT user_id FROM de_user_data WHERE sector = ?",
                    [$sector]
                );

                while ($row = $result->fetch_assoc()) {
                    $message = $politics_lang["msg_24_1"] . " " . $voteoutlist . " " . $politics_lang["msg_24_2"];
                    mysqli_execute_query(
                        $GLOBALS['dbi'],
                        "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, ?, ?, ?)",
                        [$row["user_id"], 3, $time, $message]
                    );

                    mysqli_execute_query(
                        $GLOBALS['dbi'],
                        "UPDATE de_user_data SET newnews = ? WHERE user_id = ?",
                        [1, $row["user_id"]]
                    );
                }
                pol_meldung('Die Abstimmung &uuml;ber <b>' . htmlspecialchars($voteoutlist, ENT_QUOTES, 'UTF-8') . '</b> l&auml;uft.');
            } elseif ($anz1 > 0) {
                pol_meldung('Es l&auml;uft bereits eine Abstimmung.', 'fehler');
            }
        }
    } else pol_meldung($politics_lang["votedescription"], 'fehler');
}

$sec_btn = isset($_POST['sec_btn']) ? $_POST['sec_btn'] : '';
$newname = isset($_POST['newname']) ? $_POST['newname'] : '';
$seksteuer = isset($_POST['seksteuer']) ? intval($_POST['seksteuer']) : 0;
//nur die Werte aus dem Auswahlfeld (2-5) und nur mit Sektorhandelszentrum, sonst bleibt der bisherige Satz;
//ein freier Wert würde über Missionen und den Konverter Rohstoffe aus dem Nichts bzw. aus der Sektorkasse erzeugen.
//Ohne Handelszentrum gibt es das Auswahlfeld nicht, dann wurde früher beim Speichern des Namens 0 % gesetzt.
if (!isset($_POST['seksteuer']) || !in_array($seksteuer, array(2, 3, 4, 5), true) || $sectechs[4] != 1) {
    $seksteuer = $ssteuer;
}
if (!empty($sec_btn) && $system == issectorcommander()) {
    if (($name <> $newname) || ($ssteuer <> $seksteuer)) {

        $name = htmlspecialchars(stripslashes($newname), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');

        mysqli_execute_query(
            $GLOBALS['dbi'],
            "UPDATE de_sector SET name = ? WHERE sec_id = ?",
            [$name, $sector]
        );

        if (issectorcommander() && $sector != 1) //nur wenn man sk ist kann man die steuer ändern
        {
            if ($sv_deactivate_vsystems != 1)
                mysqli_execute_query(
                    $GLOBALS['dbi'],
                    "UPDATE de_sector SET ssteuer = ? WHERE sec_id = ?",
                    [$seksteuer, $sector]
                );
        }
        $ssteuer = $seksteuer;
        pol_meldung('Die Sektordaten sind gespeichert.');
    }
}

function pol_zahl($wert, $stellen = 0)
{
    return number_format($wert, $stellen, ',', '.');
}

//Zahl in einer Liste; Nullen treten zurück
function pol_wert($wert, $stellen = 0, $einheit = '')
{
    return '<span class="pol-zahl' . ($wert == 0 ? ' pol-null' : '') . '">' . number_format($wert, $stellen, ',', '.') . $einheit . '</span>';
}

//DX Botschaft: Einstellungen der Aliens (NPC) im Sektor, Daten kommen über die NPC-Schnittstelle
function pol_dx_botschaft()
{
    global $sectechs, $politics_lang, $sector;

    if ($sectechs[6] != 1) {
        echo '<div class="mod-leer">' . $politics_lang['npc_config_precondition_missing'] . '</div>';
        return;
    }
    $npc_mates = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT user_id, spielername FROM de_user_data WHERE sector = ? and npc = 2 ORDER BY `system` ASC",
        [$sector]
    );
    $npcControl = new NPCBuildControl();
    $fleetControl = new FleetControl();
    if (isset($_POST['action'])) {
        $npcId = (int)$_POST['npc_id'];
        // Überprüfen, ob $npcId in $npc_mates existiert
        $npcIdExists = false;
        while ($npc = $npc_mates->fetch_assoc()) {
            if ($npc['user_id'] == $npcId) {
                $npcIdExists = true;
                break;
            }
        }

        if (!$npcIdExists) {
            echo '<div class="mod-meldung mod-meldung-fehler">Ung&uuml;ltiges Alien.</div>';
            return;
        }
        $npc_mates->data_seek(0); // Ergebnis zurücksetzen
        if ($_POST['action'] === 'save_npc_preset') {
            $presetIdentifier = $_POST['fleet_preset'];
            $recycling = isset($_POST['recycle_fleet']) && $_POST['recycle_fleet'] == 1;
            try {
                // Execute local fleet recycling if enabled
                if ($recycling) {
                    if ($fleetControl->isFleetHome($npcId) === false) {
                        echo '<div class="mod-meldung mod-meldung-fehler">' . $politics_lang['npc_rec_fleet_home_error'] . '</div>';
                        return;
                    }
                    $fleetControl->moveAllShipsToHomeFleet($npcId);
                    if (setLock($npcId)) {
                        try {
                            $recycler = new FleetRecycler();
                            $presetConfig = $npcControl->getShipBuildPresets($npc['user_id']);
                            $presetArrayIndex = 0;
                            foreach ($presetConfig->getPresets() as $presetIndex => $preset) {
                                if ($preset->getId() === $presetIdentifier) {
                                    $presetArrayIndex = $presetIndex;
                                    break;
                                }
                            }
                            $targetPreset = $presetConfig->getPresets()[$presetArrayIndex];
                            $result = $recycler->recycleFleetToPreset($npcId, $targetPreset);
                        } catch (Exception $recycleException) {
                            error_log("Fleet recycling error for NPC $npcId: " . $recycleException->getMessage());
                            echo '<div class="mod-meldung mod-meldung-fehler">' . $politics_lang['npc_rec_error'] . '</div>';
                            return;
                        } finally {
                            $erg = releaseLock($npcId);
                            if (!$erg) {
                                echo '<div class="mod-meldung mod-meldung-fehler">Datensatz Nr. ' . $npcId . ' konnte nicht entsperrt werden!</div>';
                            }
                        }
                    } else {
                        echo '<div class="mod-meldung mod-meldung-fehler">Es ist zurzeit bereits eine Transaktion aktiv. Bitte warte, bis die Transaktion abgeschlossen ist.</div>';
                    }
                }

                // Call external NPC API to update preset (existing logic)
                $npcControl->setShipBuildPreset($npcId, $presetIdentifier);
                echo '<div class="mod-meldung mod-meldung-ok">' . $politics_lang['npc_fleet_config_success'] . '</div>';
            } catch (Exception $e) {
                error_log($e->getMessage());
                echo '<div class="mod-meldung mod-meldung-fehler">' . $politics_lang['npc_fleet_config_error'] . '</div>';
            }
        } else if ($_POST['action'] === 'save_npc_max_fp') {
            //Bereich wie im Schieberegler (-10 bis 200)
            $maxFp = max(-10, min(200, (int)$_POST['max_fp']));
            try {
                $npcControl->setMaxFleetPoints($npcId, $maxFp);
                echo '<div class="mod-meldung mod-meldung-ok">' . $politics_lang['npc_fleet_max_config_success'] . '</div>';
            } catch (Exception $e) {
                error_log($e->getMessage());
                echo '<div class="mod-meldung mod-meldung-fehler">' . $politics_lang['npc_fleet_max_config_error'] . '</div>';
            }
        }
    }

    if ($npc_mates->num_rows == 0) {
        echo '<div class="mod-leer">In deinem Sektor gibt es keine Aliens.</div>';
        return;
    }

    //Text neben dem Schieberegler, gleich wie im Skript unten
    $fp_text = function ($wert) use ($politics_lang) {
        return $wert < 0 ? $politics_lang['npc_fleet_max_no_limit'] : ($wert === 0 ? $politics_lang['npc_fleet_max_disabled'] : $wert . '%');
    };

    while ($npc = $npc_mates->fetch_assoc()) {
        $id = $npc['user_id'];
        echo '<div class="pol-npc">';
        echo '<div class="pol-npc-name">' . htmlspecialchars($npc['spielername']) . '</div>';

        $presetConfig = new FleetPresetConfig(null, []);
        try {
            $presetConfig = $npcControl->getShipBuildPresets($id);
        } catch (Exception $e) {
            echo '<div class="mod-meldung mod-meldung-fehler">' . $politics_lang['npc_fleet_config_load_error'] . '</div></div>';
            error_log($e->getMessage());
            return;
        }

        //verfügbare Voreinstellungen mit ihren Schiffsanteilen
        $voreinstellungen = '';
        foreach ($presetConfig->getPresets() as $preset) {
            $anteile = array();
            foreach ($preset->getShipRatiosByName() as $shipId => $ratio) {
                if ($ratio <= 0) {
                    continue;
                }
                $anteile[] = htmlentities($shipId) . ' <b>' . htmlentities($ratio * 100) . '&nbsp;%</b>';
            }
            $voreinstellungen .= '<div class="pol-preset"><b>' . htmlentities($preset->getName()) . '</b><span>' . implode(' &middot; ', $anteile) . '</span></div>';
        }

        //Flotten-Voreinstellung und Recycling
        echo '<form method="post" action="politics.php?s=3" class="pol-npc-zeile">';
        echo '<input type="hidden" name="npc_id" value="' . $id . '">';
        echo '<input type="hidden" name="action" value="save_npc_preset">';
        echo '<label for="fleet_preset_' . $id . '" class="pol-npc-label">' . rtrim($politics_lang['npc_fleet_preset_form_label'], ':') . '</label>';
        echo '<select name="fleet_preset" id="fleet_preset_' . $id . '" class="mod-eingabe">';
        echo '<option value="" disabled>' . $politics_lang['npc_fleet_preset_form_sel'] . '</option>';
        foreach ($presetConfig->getPresets() as $preset) {
            $selected = ($preset->getId() === $presetConfig->getCurrentPresetId()) ? ' selected="selected"' : '';
            echo '<option' . $selected . ' value="' . htmlspecialchars($preset->getId()) . '">' . htmlspecialchars($preset->getName()) . '</option>';
        }
        echo '</select>';
        if ($fleetControl->isFleetHome($id)) {
            echo '<label class="pol-haken"><input type="checkbox" value="1" name="recycle_fleet">' . $politics_lang['npc_rec_label'] . '</label>';
        } else {
            echo '<label class="pol-haken pol-haken-aus" title="' . $politics_lang['npc_rec_fleet_precondition'] . '"><input type="checkbox" value="1" name="recycle_fleet" disabled>' . $politics_lang['npc_rec_label'] . '</label>';
        }
        echo '<button type="submit" name="save_npc_preset_' . $id . '" value="Speichern" class="mod-btn mod-btn-leise">Speichern</button>';
        echo '</form>';

        //maximale Flottenpunkte
        $maxFp = null;
        try {
            $maxFp = $npcControl->getMaxFleetPoints($id);
        } catch (Exception $e) {
            echo '<div class="mod-meldung mod-meldung-fehler">' . $politics_lang['npc_fleet_max_load_error'] . '</div></div>';
            error_log($e->getMessage());
            return;
        }
        echo '<form method="post" action="politics.php?s=3" class="pol-npc-zeile">';
        echo '<input type="hidden" name="npc_id" value="' . $id . '">';
        echo '<input type="hidden" name="action" value="save_npc_max_fp">';
        echo '<label for="max_fp_' . $id . '" class="pol-npc-label">' . rtrim($politics_lang['npc_fleet_max_label'], ':') . '</label>';
        echo '<span class="pol-regler"><input type="range" min="-10" max="200" step="10" name="max_fp" id="max_fp_' . $id . '" value="' . $maxFp . '" class="pol-fp"><output>' . $fp_text($maxFp) . '</output></span>';
        echo '<button type="submit" name="save_npc_max_fp_' . $id . '" value="Speichern" class="mod-btn mod-btn-leise">Speichern</button>';
        echo '</form>';

        echo '<details class="pol-details"><summary>Voreinstellungen und Flottenpunkte erkl&auml;rt</summary>';
        echo '<div class="pol-details-text">' . $politics_lang['npc_fleet_preset_hint'] . '</div>' . $voreinstellungen;
        echo '<div class="pol-details-text">' . $politics_lang['npc_fleet_max_tooltip'] . '</div>';
        echo '</details>';
        echo '</div>';
    }
    ?>
    <script>
    //Schieberegler: Wert daneben anzeigen
    document.querySelectorAll('.pol-fp').forEach(function(r){
        r.addEventListener('input', function(){
            this.nextElementSibling.textContent = this.value < 0 ? '<?php echo $politics_lang['npc_fleet_max_no_limit'] ?>' :
                this.value === '0' ? '<?php echo $politics_lang['npc_fleet_max_disabled'] ?>' : this.value + '%';
        });
    });
    </script>
    <?php
}

//Anzeige
$ist_sk = ($system == issectorcommander());
$s = isset($_REQUEST['s']) ? $_REQUEST['s'] : 1;

//Reiter nur für den SK
if ($ist_sk) {
    $reiter = array(
        'politics.php?s=1' => array($politics_lang["allgemein"], $s == 1),
        'politics.php?s=2' => array('SK-Politik', $s == 2),
        'politics.php?s=3' => array($politics_lang['npc_config_page_btn'], $s == 3),
        'bkmenu.php' => array('SK-Bau/Flotte', false),
    );
    echo '<div class="mod ally-navi pol-navi">';
    foreach ($reiter as $ziel => $r) {
        echo '<a href="' . $ziel . '" class="ally-reiter' . ($r[1] ? ' ally-reiter-aktiv' : '') . '">' . $r[0] . '</a>';
    }
    echo '</div>';
}

if (count($meldungen) > 0) {
    echo '<div class="mod pol-meldungen">' . implode('', $meldungen) . '</div>';
}

if ($s == 2 && $ist_sk) {
    //Sektorname und Steuersatz
    rahmen_oben('Sektordaten');
    echo '<form method="post" action="politics.php" class="mod pol">';
    echo '<input type="hidden" name="s" value="2">';
    echo '<div class="ally-formular">';
    echo '<label class="ally-feld"><span class="mod-typ">' . $politics_lang["msg_14_1"] . '</span>';
    echo '<input type="text" name="newname" value="' . $name . '" maxlength="30" class="mod-eingabe">';
    echo '<span class="ally-feld-hinweis">Beachte bei der Namensvergabe bitte die Netiquette.</span></label>';
    echo '<div class="ally-feld"><span class="mod-typ">' . $politics_lang["sektorsteuersatz"] . '</span>';
    if ($sectechs[4] == 1) {
        echo '<span class="pol-steuer"><select name="seksteuer" class="mod-eingabe">';
        foreach (array(2, 3, 4, 5) as $satz) {
            echo '<option value="' . $satz . '"' . ("$ssteuer" == "$satz" ? ' selected' : '') . '>' . $satz . '</option>';
        }
        echo '</select>%</span>';
        echo '<span class="ally-feld-hinweis">2 bis 5 %</span>';
    } else {
        echo '<span class="mod-feld">' . $ssteuer . '&nbsp;%</span>';
        echo '<span class="ally-feld-hinweis">Zum &Auml;ndern wird das Sektorhandelszentrum ben&ouml;tigt.</span>';
    }
    echo '</div>';
    echo '</div>';
    echo '<div class="ally-aktionen"><button type="submit" name="sec_btn" value="' . $politics_lang["savesekinfos"] . '" class="mod-btn">' . $politics_lang["savesekinfos"] . '</button></div>';
    echo '</form>';
    rahmen_unten();

    //Rausvoten: starten oder laufende Abstimmung abbrechen
    rahmen_oben($politics_lang["markvoteout"]);
    echo '<div class="mod pol">';
    $result1 = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT sector_id, user_id, ticks FROM de_sector_voteout WHERE sector_id = ?",
        [$sector]
    );
    if ($result1->num_rows == 0) {
        //zur Auswahl stehen nur gesperrte Spieler, nur gegen sie lässt sich eine Abstimmung starten
        $result = mysqli_execute_query(
            $GLOBALS['dbi'],
            "SELECT de_user_data.spielername FROM de_user_data LEFT JOIN de_login ON (de_login.user_id = de_user_data.user_id)
             WHERE de_user_data.sector = ? AND de_login.status = 2 ORDER BY de_user_data.`system` ASC",
            [$sector]
        );
        echo '<div class="ally-hinweis">' . $politics_lang["votedescription"] . ' Die Abstimmung l&auml;uft 288 WT und ist erfolgreich, sobald ' . $sv_voteoutgrenze . ' % der aktiven Spieler im Sektor daf&uuml;r stimmen.</div>';
        if ($result->num_rows == 0) {
            echo '<div class="mod-leer pol-abstand">Zurzeit ist niemand in deinem Sektor gesperrt.</div>';
        } else {
            echo '<form method="post" action="politics.php" class="pol-zeilenformular pol-abstand">';
            echo '<input type="hidden" name="s" value="2">';
            echo '<select name="voteoutlist" class="mod-eingabe">';
            while ($row = $result->fetch_assoc()) {
                $vname = htmlspecialchars($row["spielername"], ENT_QUOTES, 'UTF-8');
                echo '<option value="' . $vname . '">' . $vname . '</option>';
            }
            echo '</select>';
            echo '<button type="submit" name="voteout" value="' . $politics_lang["startvote"] . '" class="mod-btn mod-btn-gefahr" data-bestaetigen="Wirklich starten?">' . $politics_lang["startvote"] . '</button>';
            echo '</form>';
        }
    } else {
        $row1 = $result1->fetch_assoc();
        $result2 = mysqli_execute_query(
            $GLOBALS['dbi'],
            "SELECT spielername FROM de_user_data WHERE user_id = ?",
            [$row1["user_id"]]
        );
        $row2 = $result2->fetch_assoc();
        echo '<form method="post" action="politics.php" class="pol-zeilenformular">';
        echo '<input type="hidden" name="s" value="2">';
        echo '<span class="pol-text">Es l&auml;uft eine Abstimmung &uuml;ber <b>' . ($row2["spielername"] ?? '?') . '</b>, noch <b>' . pol_zahl($row1["ticks"]) . '&nbsp;WT</b>.</span>';
        echo '<button type="submit" name="voteoutcancel" value="' . $politics_lang["cancelvote"] . '" class="mod-btn mod-btn-gefahr" data-bestaetigen="Wirklich abbrechen?">' . $politics_lang["cancelvote"] . '</button>';
        echo '</form>';
    }
    echo '</div>';
    rahmen_unten();
}

if ($s == 3 && $ist_sk) {
    rahmen_oben($politics_lang['npc_config_page_btn']);
    echo '<div class="mod pol">';
    pol_dx_botschaft();
    echo '</div>';
    rahmen_unten();
}

//menü für den sektor, wie sk-wahl und vote für exilanden
if ($s == 1 or !isset($s)) {
    rahmen_oben($politics_lang["wahldessk"]);
    echo '<div class="mod pol">';

    //alle menschlichen user des sektors auslesen
    $result = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT de_user_data.spielername, de_user_data.votefor, de_user_data.`system`
   FROM de_user_data
   WHERE de_user_data.npc = 0 AND de_user_data.sector = ?
   ORDER BY `system` ASC",
        [$sector]
    );
    $anz = $result->num_rows;
    while ($row = $result->fetch_assoc()) {

        //überprüfen, ob es den Account für den man votet noch gibt und es kein NPC ist
        $db_daten = mysqli_execute_query(
            $GLOBALS['dbi'],
            "SELECT user_id FROM de_user_data WHERE npc = 0 AND sector = ? AND `system` = ? ORDER BY `system` ASC",
            [$sector, $row["votefor"]]
        );
        $anzv = $db_daten->num_rows;
        if ($anzv == 0) {
            mysqli_execute_query(
                $GLOBALS['dbi'],
                "UPDATE de_user_data SET votefor = ? WHERE sector = ? AND votefor = ?",
                [0, $sector, $row["votefor"]]
            );
        }

        $su[$row["system"]][0] = $row["spielername"];
        $su[$row["system"]][1] = $row["votefor"];
        $su[$row["system"]][2] = $row["system"];
    }

    $ska = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
    //alle stimmen z&auml;hlen
    for ($i = 1; $i <= ($sv_maxsystem + 5); $i++) {
        if (!isset($su[$i][1])) {
            $su[$i][1] = 0;
        }

        if (!isset($ska[$su[$i][1]])) {
            $ska[$su[$i][1]] = 0;
        }

        $ska[$su[$i][1]]++;
    }

    //maximalwert suchen
    $mw = 0;
    for ($i = 1; $i <= ($sv_maxsystem + 5); $i++) {
        if (isset($ska[$i]) && $ska[$i] > $mw) {
            $mw = $ska[$i];
        }
    }

    //schauen ob wert doppelt vorhanden, wenn kleiner X
    $anzahl = 0;
    if ($mw < 8) {
        for ($i = 1; $i <= ($sv_maxsystem + 5); $i++) {
            if (isset($ska[$i]) && $ska[$i] == $mw) {
                $anzahl++;
            }
        }
    } else $anzahl = 1;

    //wenn nicht 1, dann gibts mehrere mit der stimmenanzahl
    if ($anzahl != 1) $mw = 0;

    $sksys = 0;
    echo '<div class="pol-sk"><span class="mod-typ">' . $politics_lang["actualsk"] . '</span>';
    if ($mw != 0) {
        //noch den namen des sk auslesen
        for ($i = 1; $i <= ($sv_maxsystem + 5); $i++) {
            if (isset($ska[$i]) && $ska[$i] == $mw) {
                $sksys = $i;
            }
        }
        for ($i = 1; $i <= ($sv_maxsystem + 5); $i++) {
            if (isset($su[$i][2]) && $su[$i][2] == $sksys) {
                $skname = $su[$i][0];
            }
        }
        echo '<div class="pol-sk-name"><b>' . $skname . '</b><span class="mod-chip">' . $mw . ' ' . ($mw == 1 ? 'Stimme' : $politics_lang["stimmen"]) . '</span></div>';
    } else {
        echo '<div class="pol-text">' . $politics_lang["msg_17"] . '</div>';
    }
    echo '</div>';

    //eigene Stimme
    echo '<form method="post" action="politics.php" class="pol-zeilenformular pol-abstand">';
    echo '<input type="hidden" name="s" value="1">';
    echo '<select name="userslist" class="mod-eingabe"><option value="0">f&uuml;r niemanden stimmen</option>';
    for ($i = 1; $i <= ($sv_maxsystem + 5); $i++) {
        if (isset($su[$i][0]) && $su[$i][0] != '') {
            $selected = '';
            if ($votefor == $su[$i][2]) {
                $selected = ' selected';
            }
            echo '<option value="' . $su[$i][2] . '"' . $selected . '>' . $su[$i][0] . '</option>';
        }
    }
    echo '</select>';
    echo '<button type="submit" name="letsgo" value="' . $politics_lang["fuerspielerstimmen"] . '" class="mod-btn">' . $politics_lang["fuerspielerstimmen"] . '</button>';
    echo '</form>';

    if ($anz == 0) exit;

    //alle namen und wofür sie gevotet haben ausgeben
    echo '<div class="ally-abschnitt"><div class="mod-typ">' . $politics_lang["wahlentscheidungen"] . '</div><div class="pol-liste">';
    for ($j = 1; $j <= ($sv_maxsystem + 5); $j++) {
        if (isset($su[$j][0]) && $su[$j][0] != '') {
            $votename = '';
            for ($i = 1; $i <= ($sv_maxsystem + 5); $i++) {
                if (isset($su[$i][2]) && isset($su[$j][1]) && $su[$i][2] == $su[$j][1]) {
                    $votename = $su[$i][0];
                }
            }
            echo '<div class="pol-zeile pol-stimme">';
            echo '<span class="pol-name">' . $su[$j][0] . ($su[$j][2] == $sksys ? ' <span class="mod-chip mod-chip-gruen">SK</span>' : '') . '</span>';
            echo '<span class="pol-leise">' . $politics_lang["votefor"] . '</span>';
            echo '<span' . ($votename == '' ? ' class="pol-leise">niemanden' : '>' . $votename) . '</span>';
            echo '</div>';
        }
    }
    echo '</div></div>';
    echo '</div>';
    rahmen_unten();

    //Allgemeine Infos
    rahmen_oben($politics_lang["allgemeineinfos"]);
    echo '<div class="mod pol">';
    echo '<div class="pol-kacheln">';
    echo '<div class="ov-wert"><span class="mod-typ">' . $politics_lang["sektorsteuersatz"] . '</span><b>' . $ssteuer . '&nbsp;%</b></div>';
    echo '<div class="ov-wert"><span class="mod-typ">' . $politics_lang["sektorkollektoren"] . '</span><b>' . pol_zahl($seccol) . '</b></div>';
    echo '<div class="ov-wert"><span class="mod-typ">Sektorflotte</span><b>' . pol_zahl($secfleet) . '</b><small>Schiffe</small></div>';
    //sektorpasswort für die sphäre benötigt
    if ($dbsecpass != '') {
        echo '<div class="ov-wert"><span class="mod-typ">' . $politics_lang["sektorpasswort"] . '</span><b class="pol-passwort">' . $dbsecpass . '</b></div>';
    }
    echo '</div>';

    //Kostenfaktor
    $avg_player = getAveragePlayerAmountInSectorOnServer();
    $kostenfaktor = 10 - $avg_player;

    //sektorgebäudekosten auslesen
    echo '<details class="pol-details"><summary>' . $politics_lang['sektorkosten'] . ': Geb&auml;ude und ' . $politics_lang['sektorraumschiff'] . '</summary>';
    echo '<div class="pol-zeile pol-kosten pol-kopfzeile"><span></span><span>M</span><span>D</span><span>I</span><span>E</span><span>T</span></div><div class="pol-liste">';
    $db_daten = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT tech_name, restyp01, restyp02, restyp03, restyp04, restyp05
   FROM de_tech_data1
   WHERE tech_id > ? AND tech_id < ?
   ORDER BY tech_id",
        [119, 130]
    );
    while ($row = $db_daten->fetch_assoc()) {
        echo '<div class="pol-zeile pol-kosten"><span class="pol-name">' . $row['tech_name'] . '</span>';
        for ($r = 1; $r <= 5; $r++) {
            echo pol_wert($row['restyp0' . $r] / $kostenfaktor);
        }
        echo '</div>';
    }
    //raumschiff
    echo '<div class="pol-zeile pol-kosten"><span class="pol-name">' . $politics_lang['sektorraumschiff'] . '</span>' . pol_wert(2000) . pol_wert(500) . pol_wert(500) . pol_wert(2000) . pol_wert(0) . '</div>';
    echo '</div></details>';
    echo '</div>';
    rahmen_unten();

    //Sektorlagereinzahlungen
    rahmen_oben($politics_lang["sektorlagereinzahlungen"]);
    echo '<div class="mod pol">';
    $result = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT spielername, spend01, spend02, spend03, spend04, spend05 FROM de_user_data WHERE sector = ? ORDER BY `system`",
        [$sector]
    );
    if ($result->num_rows > 0) {
        echo '<div class="pol-zeile pol-spende pol-kopfzeile"><span>' . $politics_lang["spieler"] . '</span>';
        echo '<span title="' . $politics_lang["multiplex"] . '">M</span><span title="' . $politics_lang["dyharra"] . '">D</span><span title="' . $politics_lang["iradium"] . '">I</span>';
        echo '<span title="' . $politics_lang["eternium"] . '">E</span><span title="' . $politics_lang["tronic"] . '">T</span><span>Punkte</span></div>';
        echo '<div class="pol-liste">';
        while ($row = $result->fetch_assoc()) {
            echo '<div class="pol-zeile pol-spende"><span class="pol-name">' . $row["spielername"] . '</span>';
            for ($r = 1; $r <= 5; $r++) {
                echo pol_wert($row['spend0' . $r]);
            }
            echo pol_wert(($row["spend01"] + $row["spend02"] * 2 + $row["spend03"] * 3 + $row["spend04"] * 4) / 10 + $row["spend05"] * 1000);
            echo '</div>';
        }
        echo '</div>';
        echo '<div class="pol-hinweis">' . $politics_lang['punktewertformel1'] . ': ' . $politics_lang['punktewertformel2'] . '</div>';
    }
    echo '</div>';
    rahmen_unten();

    //Spielerinformationen
    rahmen_oben($politics_lang["spielerinformationen"]);
    echo '<div class="mod pol">';
    $result = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT de_user_data.spielername, de_login.last_login, de_login.last_click,
          de_user_data.`system`, de_user_data.chatoff, de_user_data.secstatdisable
   FROM de_login
   LEFT JOIN de_user_data ON (de_login.user_id = de_user_data.user_id)
   WHERE de_user_data.sector = ?
   ORDER BY `system` ASC LIMIT 200",
        [$sector]
    );
    if ($result->num_rows > 0) {
        //als SK lässt sich der Sektorstatus je Spieler umschalten
        $sk_schalter = ($system == issectorcommander());
        echo '<div class="pol-zeile pol-info pol-kopfzeile"><span>' . $politics_lang["spieler"] . '</span><span>' . $politics_lang["on12h"] . '</span><span>' . $politics_lang["sektorstatuseinsicht"] . '</span></div>';
        echo '<div class="pol-liste">';
        while ($row = $result->fetch_assoc()) {
            echo '<div class="pol-zeile pol-info"><span class="pol-name">' . $row["spielername"] . '</span>';
            //online innerhalb der letzten 12 stunden
            if (strtotime($row["last_click"]) + 43200 > time()) {
                echo '<span><span class="mod-chip mod-chip-gruen">' . $politics_lang["ja"] . '</span></span>';
            } else {
                echo '<span><span class="mod-chip">' . $politics_lang["nein"] . '</span></span>';
            }
            //sektorstatus sichtbar, als sk kann man das umstellen
            $sichtbar = ($row["secstatdisable"] == 0) ? $politics_lang["ja"] : $politics_lang["nein"];
            if ($sk_schalter) {
                echo '<span><a href="politics.php?s=1&amp;do=2&amp;sys=' . $row["system"] . '" class="pol-schalter' . ($row["secstatdisable"] == 0 ? ' pol-schalter-an' : '') . '" title="Klicken zum Umschalten">' . $sichtbar . '</a></span>';
            } else {
                echo '<span><span class="mod-chip' . ($row["secstatdisable"] == 0 ? ' mod-chip-gruen' : '') . '">' . $sichtbar . '</span></span>';
            }
            echo '</div>';
        }
        echo '</div>';
    }
    echo '</div>';
    rahmen_unten();

    //wenn man die unendlichkeitssphäre hat, dann kann man umziehen
    if ($techs[26] == '1'  and $secmoves < $sv_max_secmoves) {
        rahmen_oben($politics_lang["umzugmsg"]);
        echo '<div class="mod pol">';
        //schauen ob derjenige schon einen sektor beantragt hat
        $result1 = mysqli_execute_query(
            $GLOBALS['dbi'],
            "SELECT user_id, pass, ticks FROM de_sector_umzug WHERE user_id = ? AND typ = 1",
            [$_SESSION['ums_user_id']]
        );
        if ($result1->num_rows > 0) {
            $row1 = $result1->fetch_assoc();
            echo '<div class="pol-kacheln">';
            echo '<div class="ov-wert"><span class="mod-typ">' . $politics_lang["sektorpasswort"] . '</span><b class="pol-passwort">' . $row1["pass"] . '</b></div>';
            echo '<div class="ov-wert"><span class="mod-typ">Laufzeit</span><b>' . pol_zahl($row1["ticks"]) . '</b><small>WT</small></div>';
            echo '</div>';
            //alle spieler ausgeben die dran teilnehmen
            echo '<div class="ally-abschnitt"><div class="mod-typ">' . $politics_lang["msg_19_2"] . '</div><div class="pol-liste">';
            $useranz = 0;
            $result = mysqli_execute_query(
                $GLOBALS['dbi'],
                "SELECT user_id FROM de_sector_umzug WHERE pass = ?",
                [$row1["pass"]]
            );
            while ($row = $result->fetch_assoc()) {
                $result2 = mysqli_execute_query(
                    $GLOBALS['dbi'],
                    "SELECT spielername, sector, `system` FROM de_user_data WHERE user_id = ?",
                    [$row["user_id"]]
                );
                $row2 = $result2->fetch_assoc();
                echo '<div class="pol-zeile"><span class="pol-name">' . ($row2["spielername"] ?? '?') . ' <small class="pol-leise">' . ($row2["sector"] ?? '') . ':' . ($row2["system"] ?? '') . '</small></span></div>';
                $useranz++;
            }
            echo '</div></div>';
            echo '<form method="post" action="politics.php" class="pol-zeilenformular pol-abstand">';
            echo '<input type="hidden" name="s" value="1">';
            echo '<span class="pol-text">' . $politics_lang["msg_20_1"] . ' <b>' . $useranz . '</b> ' . $politics_lang["msg_20_2"] . ' <b>' . $sv_min_user_per_regsector . '</b> ' . $politics_lang["msg_20_3"] . '.</span>';
            echo '<button type="submit" name="cancelgetnewsec" value="' . $politics_lang["cancelgetsek"] . '" class="mod-btn mod-btn-leise">' . $politics_lang["cancelgetsek"] . '</button>';
            echo '</form>';
        } else {
            echo '<form method="post" action="politics.php" class="pol-zeilenformular">';
            echo '<input type="hidden" name="s" value="1">';
            echo '<span class="pol-text">' . $politics_lang["msg_18_1"] . '</span>';
            echo '<button type="submit" name="getnewsec" value="' . $politics_lang["getsektor"] . '" class="mod-btn">' . $politics_lang["getsektor"] . '</button>';
            echo '</form>';
            echo '<div class="ally-abschnitt"><div class="mod-typ">' . $politics_lang["msg_18_2"] . '</div>';
            echo '<form method="post" action="politics.php" class="pol-zeilenformular">';
            echo '<input type="hidden" name="s" value="1">';
            echo '<input type="text" name="secpass" value="" class="mod-eingabe" placeholder="Passwort">';
            echo '<button type="submit" name="joinsec" value="' . $politics_lang["selsek"] . '" class="mod-btn" data-bestaetigen="Wirklich umziehen?">' . $politics_lang["selsek"] . '</button>';
            echo '</form></div>';
        }
        echo '</div>';
        rahmen_unten();
    } //ende umziehen

    //anfang rausvoten
    //schauen ob ein vote läuft
    $result1 = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT sector_id, user_id, votes, ticks FROM de_sector_voteout WHERE sector_id=?",
        [$sector]
    );
    $anz1 = $result1->num_rows;

    $showexilvote = 0;
    if ($anz1 != 0) //es gibt nen vote
    {
        $showexilvote = 1;
        $row = mysqli_fetch_array($result1);
        $vuser_id = $row["user_id"];
        $vvotes = $row["votes"];
        $vticks = $row["ticks"];

        //sollte anz2==0 sein, dann gibts den spieler nicht mehr, wahrscheinlich gelöscht, dann sofort den datensatz entfernen
        $result2 = mysqli_execute_query(
            $GLOBALS['dbi'],
            "SELECT spielername FROM de_user_data WHERE user_id=?",
            [$vuser_id]
        );
        $anz2 = $result2->num_rows;
        if ($anz2 == 0) {
            mysqli_execute_query(
                $GLOBALS['dbi'],
                "DELETE FROM de_sector_voteout where sector_id=?",
                [$sector]
            );
            $showexilvote = 0;
        } else {
            $row = $result2->fetch_assoc();
            $vspielername = $row["spielername"];
        }
    }

    if ($showexilvote == 1) {
        rahmen_oben($politics_lang["exilvote"]);
        echo '<div class="mod pol">';
        echo '<div class="pol-frage">' . $politics_lang["msg_21_1"] . ' <b>' . $vspielername . '</b> ' . $politics_lang["msg_21_2"] . '?</div>';
        //alle user des sektors auslesen die aktiv sind
        $result = mysqli_execute_query(
            $GLOBALS['dbi'],
            "SELECT de_user_data.spielername, de_user_data.`system` FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id)
  WHERE de_user_data.sector=? AND de_login.status=1 ORDER BY `system` ASC ",
            [$sector]
        );
        $vw = array(0, 0, 0, 0);
        while ($row = $result->fetch_assoc()) {
            if ($vvotes[$row["system"]] == 3) {
                $vw[3]++;
            } elseif ($vvotes[$row["system"]] == 2) {
                $vw[2]++;
            } elseif ($vvotes[$row["system"]] == 1) {
                $vw[1]++;
            } elseif ($vvotes[$row["system"]] == 0) {
                $vw[0]++;
            }
        }
        //zusammenfassung ausgeben
        echo '<div class="ally-chips pol-abstand">';
        echo '<span class="mod-chip mod-chip-gruen">' . $politics_lang["msg_22_3"] . ' <b>' . $vw[1] . '</b></span>';
        echo '<span class="mod-chip stat-chip-rot">' . $politics_lang["msg_22_4"] . ' <b>' . $vw[0] . '</b></span>';
        echo '<span class="mod-chip">' . $politics_lang["msg_22_2"] . ' <b>' . $vw[2] . '</b></span>';
        echo '<span class="mod-chip">' . trim($politics_lang["msg_22_1"]) . ' <b>' . $vw[3] . '</b></span>';
        echo '</div>';
        //prozentwert ausgeben
        $prozentwert = $vw[1] / ($vw[0] + $vw[1] + $vw[2] + $vw[3]) * 100;
        echo '<div class="pol-stand"><span class="pol-balken"><span style="width: ' . min(100, round($prozentwert, 1)) . '%;"></span><i style="left: ' . $sv_voteoutgrenze . '%;" title="n&ouml;tig: ' . $sv_voteoutgrenze . ' %"></i></span>';
        echo '<span class="pol-text">' . $politics_lang["msg_23_1"] . ' <b>' . pol_zahl($prozentwert, 2) . '&nbsp;%</b> ' . $politics_lang["msg_23_2"] . ' <b>' . $sv_voteoutgrenze . '&nbsp;%</b>, ' . $politics_lang["msg_23_3"] . '. Noch <b>' . pol_zahl($vticks) . '&nbsp;WT</b>.</span></div>';

        //eigene Stimme
        $veigenewahl = $vvotes[$system];
        echo '<form method="post" action="politics.php" class="pol-abstimmen">';
        echo '<input type="hidden" name="s" value="1">';
        echo '<span class="mod-typ">Deine Stimme</span>';
        echo '<div class="pol-optionen">';
        foreach (array('1' => array('ja', 'Ja'), '0' => array('nein', 'Nein'), '2' => array('egal', 'Enthaltung')) as $code => $o) {
            echo '<label class="pol-option"><input type="radio" name="vspielerwahl" value="' . $politics_lang[$o[0]] . '" required' . ("$veigenewahl" == "$code" ? ' checked' : '') . '><span>' . $o[1] . '</span></label>';
        }
        echo '</div>';
        echo '<button type="submit" name="setvoteout" value="' . $politics_lang["stimmeabgeben"] . '" class="mod-btn">' . $politics_lang["stimmeabgeben"] . '</button>';
        echo '</form>';
        echo '</div>';
        rahmen_unten();
    } //ende rausvoten

    //Bekannte Artefakte
    rahmen_oben($politics_lang["knownartis"]);
    echo '<div class="mod pol">';
    $artresult = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT id, artname, sector, color FROM de_artefakt WHERE sector > 0 ORDER BY id"
    );
    if ($artresult->num_rows > 0) {
        echo '<div class="pol-zeile pol-art pol-kopfzeile"><span>' . $politics_lang["artefakt"] . '</span><span>' . $politics_lang["sektor"] . '</span><span>' . $politics_lang["keob"] . '</span>';
        echo '<span>' . $politics_lang["m"] . '</span><span>' . $politics_lang["d"] . '</span><span>' . $politics_lang["i"] . '</span><span>' . $politics_lang["e"] . '</span><span>' . $politics_lang["w"] . '</span></div>';
        echo '<div class="pol-liste">';
        while ($row = $artresult->fetch_assoc()) {
            $wirkung = $sv_artefakt[$row["id"] - 1] ?? array(0, 0, 0, 0, 0, 0);
            echo '<div class="pol-zeile pol-art">';
            echo '<span class="pol-name"><i class="pol-punkt" style="background: #' . preg_replace('/[^0-9a-fA-F]/', '', $row["color"]) . ';"></i>' . $row["artname"] . '</span>';
            echo '<span class="pol-zahl">' . $row["sector"] . '</span>';
            echo pol_wert($wirkung[0], 2, '&nbsp;%') . pol_wert($wirkung[1]) . pol_wert($wirkung[2]) . pol_wert($wirkung[3]) . pol_wert($wirkung[4]) . pol_wert($wirkung[5], 2, '&nbsp;%');
            echo '</div>';
        }
        echo '</div>';
        echo '<div class="pol-hinweis">' . $politics_lang["msg_24"] . '</div>';
    }
    echo '</div>';
    rahmen_unten();
} //sektor ende
?>
</body>

</html>
