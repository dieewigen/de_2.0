<?php
include "inc/header.inc.php";
include "lib/transaction.lib.php";
include 'inc/lang/'.$sv_server_lang.'_userartefact.inc.lang.php';
include "inc/userartefact.inc.php";
include 'inc/lang/'.$sv_server_lang.'_artefacts.lang.php';
include "functions.php";

$pt = loadPlayerTechs($_SESSION['ums_user_id']);
$pd = loadPlayerData($_SESSION['ums_user_id']);
$row = $pd;
$restyp01 = $row['restyp01'];
$restyp02 = $row['restyp02'];
$restyp03 = $row['restyp03'];
$restyp04 = $row['restyp04'];
$restyp05 = $row['restyp05'];
$punkte = $row["score"];
$newtrans = $row["newtrans"];
$newnews = $row["newnews"];
$sector = $row["sector"];
$system = $row["system"];
$gr01 = $restyp01;
$gr02 = $restyp02;
$gr03 = $restyp03;
$gr04 = $restyp04;
$gr05 = $restyp05;
$artbldglevel = $row["artbldglevel"];

$maxlevel = 30;
$ausbaukosten = ($artbldglevel + 1) * 20000;
$ausbauzeit = $artbldglevel + 1;
$tcost1 = 5;
$tcost2 = 10;

$errmsg = '';

//Maximale Tickanzahl auslesen
$result  = mysqli_execute_query($GLOBALS['dbi'], "SELECT wt AS tick FROM de_system LIMIT 1", []);
$row     = mysqli_fetch_array($result);
$maxtick = $row["tick"];

//die anzahl von allianzgebäudeupgrades auslesen
$allyid = $allyid = get_player_allyid($_SESSION['ums_user_id']);
$ally_geb_bonus = 0;
if ($allyid > 0) {
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_allys WHERE id=?", [$allyid]);
    $row = mysqli_fetch_array($db_daten);
    $ally_geb_bonus = $row['bldg5'];
}

//Artefakt in ein Basisschiff verschieben
if (isset($_GET["a"]) && $_GET["a"] == 1) {
    //artefakt einf�gen
    //transaktionsbeginn
    if (setLock($_SESSION['ums_user_id'])) {
        //flotten id festlegen
        $flotte = 0;
        if ($_GET["fid"] == 1) {
            $flotte = 0;
        }
        if ($_GET["fid"] == 2) {
            $flotte = 1;
        }
        if ($_GET["fid"] == 3) {
            $flotte = 2;
        }
        if ($_GET["fid"] == 4) {
            $flotte = 3;
        }

        $id = (int)$_GET["id"];
        $lvl = (int)$_GET["lvl"];

        //ist das artefakt ein g�ltiges flottenartefakt?
        if ($id == 6 or $id == 7 or $id == 14 or $id == 15) {
            //schauen ob man das artefakte hat
            $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT id FROM de_user_artefact WHERE user_id=? AND id=? AND level=?", [$_SESSION['ums_user_id'], $id, $lvl]);
            $num = mysqli_num_rows($db_daten);

            if ($num >= 1) {
                //flottendaten laden
                $fleetid = $_SESSION['ums_user_id'].'-'.$flotte;
                $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_fleet WHERE user_id=?", [$fleetid]);
                $row = mysqli_fetch_array($result);

                //schauen ob die flotte daheim ist
                if ($row["aktion"] == 0) {
                    //schauen ob ein artefaktplatz frei ist
                    if (($row["artid1"] == 0 && hasTech($pt, 133)) or
                        ($row["artid2"] == 0 && hasTech($pt, 134)) or
                        ($row["artid3"] == 0 && hasTech($pt, 135)) or
                        ($row["artid4"] == 0 && hasTech($pt, 136)) or
                        ($row["artid5"] == 0 && hasTech($pt, 137)) or
                        ($row["artid6"] == 0 && hasTech($pt, 138))) {
                        //schauen welcher slot frei ist
                        $useslot = 6;
                        if ($row["artid6"] == 0) {
                            $useslot = 6;
                        }
                        if ($row["artid5"] == 0) {
                            $useslot = 5;
                        }
                        if ($row["artid4"] == 0) {
                            $useslot = 4;
                        }
                        if ($row["artid3"] == 0) {
                            $useslot = 3;
                        }
                        if ($row["artid2"] == 0) {
                            $useslot = 2;
                        }
                        if ($row["artid1"] == 0) {
                            $useslot = 1;
                        }

                        //flotte upadten
                        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_fleet SET artid$useslot=?, artlvl$useslot=? WHERE user_id=?", [$id, $lvl, $fleetid]);
                        //artefakt aus dem gebäude entfernen
                        mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_artefact WHERE user_id=? AND id=? AND level=? LIMIT 1", [$_SESSION['ums_user_id'], $id, $lvl]);
                        $errmsg .= '<span class="ccg">Das Artefakt wurde in das Basisschiff transferiert.</span>';
                    } else {
                        $errmsg .= '<span class="ccr">Es ist kein freier Slot vorhanden.</span>';
                    }
                } else {
                    $errmsg .= '<span class="ccr">Die Flotte befindet sich in einem Einsatz.</span>';
                }
            } else {
                $errmsg .= '<span class="ccr">Du hast kein Artefakte dieser Art.</span>';
            }
        } else {
            $errmsg .= '<span class="ccr">Dieses Artefakt kann nicht in einem Basisschiff verwendet werden.</span>';
        }

        //transaktionsende
        $erg = releaseLock($_SESSION['ums_user_id']); //Lösen des Locks und Ergebnisabfrage
        if ($erg) {
            //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
        } else {
            print('Datensatz Nr. '.$_SESSION['ums_user_id']." konnte nicht entsperrt werden!<br><br><br>");
        }
    }// if setlock-ende
    else {
        echo '<br><font color="#FF0000">Es ist zur Zeit bereits eine Transaktion aktiv. Bitte warte, bis die Transaktion abgeschlossen ist.</font><br><br>';
    }
} elseif (isset($_GET["a"]) && $_GET["a"] == 2) {
    //Artefakt aus einem Basisschiff entfernen
    //transaktionsbeginn
    if (setLock($_SESSION['ums_user_id'])) {
        //flotten id festlegen
        $flotte = 0;
        if ($_GET["fid"] == 1) {
            $flotte = 0;
        }
        if ($_GET["fid"] == 2) {
            $flotte = 1;
        }
        if ($_GET["fid"] == 3) {
            $flotte = 2;
        }
        if ($_GET["fid"] == 4) {
            $flotte = 3;
        }
        if ($_GET["fid"] == 5) {
            $flotte = 4;
        }
        if ($_GET["fid"] == 6) {
            $flotte = 5;
        }
        if ($_GET["fid"] == 7) {
            $flotte = 6;
        }

        $id = (int)$_GET["id"];
        if ($id < 1 || $id > 6) {
            $id = 1;
        }

        //flottendaten laden
        $fleetid = $_SESSION['ums_user_id'].'-'.$flotte;
        $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_fleet WHERE user_id=?", [$fleetid]);
        $row = mysqli_fetch_array($result);

        //ist das artefakt im basisschiff vorhanden
        if ($row["artid$id"] > 0) {
            //schauen ob im artefaktgebäude platz ist
            $db_datenx = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_artefact WHERE user_id=?", [$_SESSION['ums_user_id']]);
            $numx = mysqli_num_rows($db_datenx);
            if ($numx < $artbldglevel + $ally_geb_bonus) { //es gibt noch platz

                //schauen ob die flotte daheim ist
                if ($row["aktion"] == 0) {
                    //flotte upadten
                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_fleet SET artid$id=0, artlvl$id=0 WHERE user_id=?", [$fleetid]);
                    //artefakt in das gebäude transferieren
                    $artid = $row["artid$id"];
                    $artlvl = $row["artlvl$id"];
                    mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_artefact (user_id, id, level) VALUES (?, ?, ?)", [$_SESSION['ums_user_id'], $artid, $artlvl]);
                    $errmsg .= '<span class="ccg">Das Artefakt wurde in das Artefaktgeb&auml;ude transferiert.</span>';
                } else {
                    $errmsg .= '<span class="ccr">Die Flotte befindet sich in einem Einsatz.</span>';
                }
            } else {
                $errmsg .= '<span class="ccr">Im Artefaktgeb&auml;ude ist kein Platz mehr frei.</span>';
            }
        } else {
            $errmsg .= '<span class="ccr">Dieses Artefakt befindet sich nicht auf dem Basisschiff.</span>';
        }

        //transaktionsende
        $erg = releaseLock($_SESSION['ums_user_id']); //Lösen des Locks und Ergebnisabfrage
        if ($erg) {
            //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
        } else {
            print('Datensatz Nr. '.$_SESSION['ums_user_id']." konnte nicht entsperrt werden!<br><br><br>");
        }
    }// if setlock-ende
    else {
        echo '<br><font color="#FF0000">Es ist zur Zeit bereits eine Transaktion aktiv. Bitte warte, bis die Transaktion abgeschlossen ist.</font><br><br>';
    }
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Artefakte</title>
<?php include "cssinclude.php"; ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';
//artefakt zerst�ren
if (isset($_REQUEST['destroyartefact']) && $_REQUEST['destroyartefact'] == 1) {
    //transaktionsbeginn
    if (setLock($_SESSION['ums_user_id'])) {
        $lid = intval($_REQUEST['lid']);
        //schauen ob man das artefakte hat
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_artefact WHERE user_id=? AND lid=?", [$_SESSION['ums_user_id'], $lid]);
        $num = mysqli_num_rows($db_daten);

        if ($num > 0) {//man hat das artefakt
            //id auslesen
            $row = mysqli_fetch_array($db_daten);

            //ist jetzt immer zerstörbar
            //if($ua_useable[$row["id"]-1]!=1){
            //artefakt löschen
            mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_artefact WHERE lid=?", [$lid]);

            //palenium gutschreiben
            change_storage_amount($_SESSION['ums_user_id'], 1, 100 * $row['level']);

            //message ausgeben
            $errmsg .= $artefacts_lang['fehler8'];
            //}
        } else {
            $errmsg .= '<font color="#FF0000">'.$artefacts_lang['fehler6'].'</font><br><br>';
        }

        //transaktionsende
        $erg = releaseLock($_SESSION['ums_user_id']); //Lösen des Locks und Ergebnisabfrage
        if ($erg) {
            //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
        } else {
            print($artefacts_lang['error'].$_SESSION['ums_user_id'].$artefacts_lang['error2']."<br><br><br>");
        }
    }// if setlock-ende
    else {
        echo '<br><font color="#FF0000">'.$artefacts_lang['error3'].'</font><br><br>';
    }
}//ende submit1

//artefakt benutzen
if (isset($_REQUEST['useartefact']) && $_REQUEST['useartefact'] == 1) {
    //transaktionsbeginn
    if (setLock($_SESSION['ums_user_id'])) {
        $lid = intval($_REQUEST['lid']);
        //schauen ob man das artefakte hat
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT id FROM de_user_artefact WHERE user_id=? AND lid=?", [$_SESSION['ums_user_id'], $lid]);
        $num = mysqli_num_rows($db_daten);

        if ($num > 0) {
            $row = mysqli_fetch_array($db_daten);
            $id = $row['id'];
            //angriffserfahrungspunkte
            if ($id == 11) {
                /*
                $exp=10000;
                //exp verteilen
                //$fleet_id=$_SESSION['ums_user_id'].'-0';
                //mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_fleet SET komatt=komatt+? WHERE user_id=?", [$exp, $fleet_id]);

                $fleet_id=$_SESSION['ums_user_id'].'-1';
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_fleet SET komatt=komatt+? WHERE user_id=?", [$exp, $fleet_id]);

                $fleet_id=$_SESSION['ums_user_id'].'-2';
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_fleet SET komatt=komatt+? WHERE user_id=?", [$exp, $fleet_id]);

                $fleet_id=$_SESSION['ums_user_id'].'-3';
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_fleet SET komatt=komatt+? WHERE user_id=?", [$exp, $fleet_id]);

                //artefakt löschen
                mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_artefact WHERE lid=?", [$lid]);

                //message ausgeben
                $errmsg.=$artefacts_lang['fehler7'];
                */
            }
            //verteidigungserfahrungspunkte
            elseif ($id == 12) {
                /*
                $exp=10000;
                //exp verteilen
                $fleet_id=$_SESSION['ums_user_id'].'-0';
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_fleet SET komdef=komdef+? WHERE user_id=?", [$exp, $fleet_id]);

                $fleet_id=$_SESSION['ums_user_id'].'-1';
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_fleet SET komdef=komdef+? WHERE user_id=?", [$exp, $fleet_id]);

                $fleet_id=$_SESSION['ums_user_id'].'-2';
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_fleet SET komdef=komdef+? WHERE user_id=?", [$exp, $fleet_id]);

                $fleet_id=$_SESSION['ums_user_id'].'-3';
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_fleet SET komdef=komdef+? WHERE user_id=?", [$exp, $fleet_id]);

                //artefakt löschen
                mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_artefact WHERE lid=?", [$lid]);

                //message ausgeben
                $errmsg.=$artefacts_lang['fehler7'];
                */
            }
            //tronicar 15-20 tronic
            elseif ($id == 16) {
                $tronic = mt_rand(15, 20);

                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET restyp05=restyp05+? WHERE user_id=?", [$tronic, $_SESSION['ums_user_id']]);
                $restyp05 += $tronic;
                //artefakt löschen
                mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_artefact WHERE lid=?", [$lid]);

                //message ausgeben
                $errmsg .= $artefacts_lang['fehler7'].' '.$artefacts_lang['tronic'].': '.$tronic;
            }
            //Artefaktgebäudestufe erhöhen
            elseif ($id == 17) {
                //das artefakt kann nur verwendet werden, wenn aktuell kein ausbau l�uft
                $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id, verbzeit FROM de_user_build WHERE tech_id=1000 AND user_id=?", [$_SESSION['ums_user_id']]);
                $gebinbau = mysqli_num_rows($db_daten);
                if ($gebinbau == 0) {
                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET artbldglevel=artbldglevel+1 WHERE user_id=?", [$_SESSION['ums_user_id']]);
                    $artbldglevel++;
                    //artefakt löschen
                    mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_artefact WHERE lid=?", [$lid]);

                    //message ausgeben
                    $errmsg .= $artefacts_lang['fehler7'];
                } else {
                    $errmsg .= '<font color="FF0000">W&auml;hrend der Geb&auml;udeausbau l&auml;uft, kann dieses Artefakt nicht verwendet werden.</font>';
                }
            }
            //waringa 1-3 kriegsartefakte
            elseif ($id == 18) {
                $kartefakt = mt_rand(1, 3);
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET kartefakt=kartefakt+? WHERE user_id=?", [$kartefakt, $_SESSION['ums_user_id']]);
                //artefakt löschen
                mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_artefact WHERE lid=?", [$lid]);

                //message ausgeben
                $errmsg .= $artefacts_lang['fehler7'].' '.$artefacts_lang['kriegsartefakte'].': '.$kartefakt;
            }
            //kollimania 6-8 kollektoren, oder 200 Palenium
            elseif ($id == 19) {
                $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT id FROM de_user_artefact WHERE user_id=? AND id=19", [$_SESSION['ums_user_id']]);
                $num = mysqli_num_rows($result);
                if ($num <= 5) {
                    //Kollektoren
                    $value = mt_rand(6, 8);
                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET col=col+? WHERE user_id=?", [$value, $_SESSION['ums_user_id']]);

                    //message ausgeben
                    $errmsg .= $artefacts_lang['fehler7'].' '.$artefacts_lang['kollektoren'].': '.$value;
                } else {
                    //palenium gutschreiben
                    $value = 200;
                    change_storage_amount($_SESSION['ums_user_id'], 1, $value);

                    //message ausgeben
                    $errmsg .= $artefacts_lang['fehler7'].' Palenium: '.$value;
                }

                //artefakt löschen
                mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_artefact WHERE lid=?", [$lid]);
            }
            //sekkollus 1-2 sektorkollektoren
            elseif ($id == 20) {
                $value = mt_rand(1, 2);
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET col=col+? WHERE sec_id=?", [$value, $sector]);
                //artefakt löschen
                mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_artefact WHERE lid=?", [$lid]);

                //message ausgeben
                $errmsg .= $artefacts_lang['fehler7'].' '.$artefacts_lang['sektorkollektoren'].': '.$value;
            }
            //creditrüssel
            elseif ($id == 21) {
                //überprüfen wie viel credits man bekommt
                $amount = mt_rand(3, 5);

                //credit gutschreiben
                //mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET credits=credits+? WHERE user_id=?", [$amount, $_SESSION['ums_user_id']]);
                changeCredits($_SESSION['ums_user_id'], $amount, 'Creditruessel');
                $errmsg .= '<font color="#00FF00">Du hast dem gro&szlig;en Ishtarus '.$amount.' Credits wegger&uuml;sselt.</font>';

                //artefakt entfernen
                mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_artefact WHERE lid=?", [$lid]);
            } elseif ($id == 22) {
                ////////////////////////////////////////////////////////////
                // neue Auktion erstellen
                ////////////////////////////////////////////////////////////
                /*
                $errmsg.='<font color="#00FF00">Die Auktion wurde gestartet.</font>';

                createAuction($_SESSION['ums_user_id']);

                //artefakt entfernen
                mysqli_query($GLOBALS['dbi'], "DELETE FROM de_user_artefact WHERE lid='$lid'");
                */
            }


            /*
            //überprüfen ob dem spieler schonmal kollektoren gestohlen worden sind und ob davon noch jemand dabei ist
            unset($atter);
            $ac=0;
            $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_getcol WHERE zuser_id=?", [$_SESSION['ums_user_id']]);
            while($row = mysqli_fetch_array($db_daten))
            {
                //spielername des atters feststellen
                $duid=$row["user_id"];
                $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT spielername, col, sector, system FROM de_user_data WHERE user_id=?", [$duid]);
                $num = mysqli_num_rows($result);
                if($num==1)
                {
                    $rowx = mysqli_fetch_array($result);
                    if($rowx['col']>0)//nur ziele mit kollektor gehen
                    {
                        $atter[$ac]['user_id']=$duid;
                        $atter[$ac]['spielername']=$rowx["spielername"];
                        $ac++;
                    }
                }
            }

            if(count($atter)>0)//es gibt einen atter
            {
                //per zufall jemanden ausw�hlen
                $w=mt_rand(0,count($atter)-1);

                $zuid=$atter[$w]['user_id'];
                $zspielername=$atter[$w]['spielername'];
                $time=date("YmdHis");

                //dem ziel den kollektor entfernen
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET col=col-1, newnews=1, wurdegeruesselt=wurdegeruesselt+1 WHERE user_id=?", [$zuid]);

                //info an das ziel bzgl. r�sselung
                mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, '60', ?, 'Ein anderer Spieler hat Dir einen Kollektor wegger&uuml;sselt.')", [$zuid, $time]);

                //dem spieler das kriegsartefakt gutschreiben
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET kartefakt=kartefakt+1 WHERE user_id=?", [$_SESSION['ums_user_id']]);

                $errmsg.='<font color="#00FF00">Du hast '.$zspielername.' einen Kollektor wegger&uuml;sselt.</font>';
            }
            else //es trifft einen dx
            {
                //dem spieler das kriegsartefakt gutschreiben
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET kartefakt=kartefakt+1 WHERE user_id=?", [$_SESSION['ums_user_id']]);

                $errmsg.='<font color="#00FF00">Du hast einem DX61a23 einen Kollektor wegger&uuml;sselt.</font>';
            }

            //artefakt löschen
            mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_artefact WHERE lid=?", [$lid]);
        }
        */
        } else {
            $errmsg .= '<font color="#FF0000">'.$artefacts_lang['fehler6'].'</font><br><br>';
        }

        //transaktionsende
        $erg = releaseLock($_SESSION['ums_user_id']); //Lösen des Locks und Ergebnisabfrage
        if ($erg) {
            //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
        } else {
            print($artefacts_lang['error'].$_SESSION['ums_user_id'].$artefacts_lang['error2']."<br><br><br>");
        }
    }// if setlock-ende
    else {
        echo '<br><font color="#FF0000">'.$artefacts_lang['error3'].'</font><br><br>';
    }
}//ende submit1


//artefaktupgrade
if (isset($_REQUEST['mergeartefacts']) && $_REQUEST['mergeartefacts'] == 1) {
    //transaktionsbeginn
    if (setLock($_SESSION['ums_user_id'])) {
        //rohstoffe auslesen
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT restyp05 FROM de_user_data WHERE user_id=?", [$_SESSION['ums_user_id']]);
        $row = mysqli_fetch_array($db_daten);
        $restyp05 = $row['restyp05'];

        //artefakt-ids
        $lid1 = intval($_REQUEST['lid1']);
        $lid2 = intval($_REQUEST['lid2']);
        //schauen ob man beide artefakte hat
        $db_daten1 = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_artefact WHERE user_id=? AND lid=?", [$_SESSION['ums_user_id'], $lid1]);
        $num1 = mysqli_num_rows($db_daten1);

        $db_daten2 = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_artefact WHERE user_id=? AND lid=?", [$_SESSION['ums_user_id'], $lid2]);
        $num2 = mysqli_num_rows($db_daten2);
        if ($num1 == 1 and $num2 == 1) {//man hat beide artefakte
            //artefaktdaten auslesen
            $row1 = mysqli_fetch_array($db_daten1);
            $id1 = $row1['id'];
            // 'level' ist der korrekte Spaltenname in der DB (INSERT/UPDATE nutzen 'level')
            $lvl1 = isset($row1['level']) ? (int)$row1['level'] : (isset($row1['lvl']) ? (int)$row1['lvl'] : 0);
            $row2 = mysqli_fetch_array($db_daten2);
            $id2 = $row2['id'];
            $lvl2 = isset($row2['level']) ? (int)$row2['level'] : (isset($row2['lvl']) ? (int)$row2['lvl'] : 0);

            //überprüfen ob die artefakte unterschiedlicher art sind
            if ($id1 != $id2) {//wenn sie unterschiedlich sind, dann muss ein neues artefakt erzeugt werden und die beiden alten gel�scht werden
                //überprüfen ob man genug tronic hat
                if ($restyp05 >= $tcost2) {
                    //rohstoffe abziehen
                    $restyp05 = $restyp05 - $tcost2;
                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET restyp05=restyp05-? WHERE user_id=?", [$tcost2, $_SESSION['ums_user_id']]);

                    //neues artefakt erzeugen, muss anderer art als die quellartefakte sein
                    $artid = 0;
                    while ($artid == 0 or $artid == $id1 or $artid == $id2) {
                        $artid = mt_rand(1, $ua_index + 1);
                    }

                    //neues artefakt hinterlegen
                    mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_artefact (user_id, id, level) VALUES (?, ?, 1)", [$_SESSION['ums_user_id'], $artid]);

                    //alte löschen
                    mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_artefact WHERE user_id=? AND lid=?", [$_SESSION['ums_user_id'], $lid1]);
                    mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_artefact WHERE user_id=? AND lid=?", [$_SESSION['ums_user_id'], $lid2]);

                    //allyid auslesen
                    $allyid = get_player_allyid($_SESSION['ums_user_id']);

                    //allyaufgabe kriegsartefakte
                    if ($allyid > 0) {
                        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET questreach = questreach + 1 WHERE id=? AND questtyp=6", [$allyid]);
                    }


                    //info dass alles geklappt hat
                    $errmsg .= '<font color="#00FF00">Die Artefakte wurden zu einem neuen Artefakt verschmolzen: '.$ua_name[$artid - 1].'</font><br><br>';
                } else {
                    $errmsg .= '<font color="#FF0000">Du ben&ouml;tigst f&uuml;r den Vorgang '.$tcost2.' Tronic.</font><br><br>';
                }
            } else {
                //schauen ob die artefakte schon auf dem maxlevel sind
                if ($lvl1 == $lvl2 and $lvl1 < $ua_maxlvl[$id1 - 1]) {
                    if ($restyp05 >= $tcost1) {
                        //rohstoffe abziehen
                        $restyp05 = $restyp05 - $tcost1;
                        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET restyp05=restyp05-? WHERE user_id=?", [$tcost1, $_SESSION['ums_user_id']]);

                        $errmsg .= '<font color="#00FF00">'.$artefacts_lang['fehler'].'</font>';

                        //ein artefakt upgraden lid1
                        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_artefact SET level=level+1 WHERE user_id=? AND lid=?", [$_SESSION['ums_user_id'], $lid1]);

                        //alte löschen lid2
                        mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_artefact WHERE user_id=? AND lid=?", [$_SESSION['ums_user_id'], $lid2]);

                        //allyid auslesen
                        $allyid = get_player_allyid($_SESSION['ums_user_id']);

                        //allyaufgabe kriegsartefakte
                        if ($allyid > 0) {
                            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET questreach = questreach + 1 WHERE id=? AND questtyp=6", [$allyid]);
                        }

                    } else {
                        $errmsg .= '<font color="#FF0000">Du ben&ouml;tigst für den Vorgang '.$tcost1.' Tronic.</font><br><br>';
                    }
                } else {
                    $errmsg .= '<font color="#FF0000">'.$artefacts_lang['fehler2'].'</font><br><br>';
                }
            }
        } else {
            $errmsg .= '<font color="#FF0000">'.$artefacts_lang['fehler3'].'</font>';
        }

        //transaktionsende
        $erg = releaseLock($_SESSION['ums_user_id']); //Lösen des Locks und Ergebnisabfrage
        if ($erg) {
            //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
        } else {
            print($artefacts_lang['error'].$_SESSION['ums_user_id'].$artefacts_lang['error2']."<br><br><br>");
        }
    }// if setlock-ende
    else {
        echo '<br><font color="#FF0000">'.$artefacts_lang['error3'].'</font><br><br>';
    }
}//ende submit1


//gebäudeupgrade
$fehlermsg='';
if (isset($_REQUEST["bupgrade"]) and hasTech($pt, 28) and $artbldglevel < $maxlevel) {
    //transaktionsbeginn
    if (setLock($_SESSION['ums_user_id'])) {
        //Rohstoffe innerhalb der Sperre neu laden, sonst bezahlt eine parallele Anfrage mit veralteten Beständen
        $row_res = mysqli_fetch_assoc(mysqli_execute_query($GLOBALS['dbi'], "SELECT restyp01, restyp02, restyp03, restyp04, restyp05 FROM de_user_data WHERE user_id=?", [$_SESSION['ums_user_id']]));
        $gr01 = $restyp01 = $row_res['restyp01'];
        $gr02 = $restyp02 = $row_res['restyp02'];
        $gr03 = $restyp03 = $row_res['restyp03'];
        $gr04 = $restyp04 = $row_res['restyp04'];
        $gr05 = $restyp05 = $row_res['restyp05'];
        $benrestyp01 = 0;
        $benrestyp02 = 0;
        $benrestyp03 = $ausbaukosten;
        $benrestyp04 = 0;
        $benrestyp05 = 0;
        $tech_ticks = $ausbauzeit;

        //schauen ob man es bauen kann, oder ob schon ein upgrade läuft
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id, verbzeit FROM de_user_build WHERE tech_id=1000 AND user_id=?", [$_SESSION['ums_user_id']]);
        $gebinbau = mysqli_num_rows($db_daten);
        if ($gebinbau != 0) {
            $fehlermsg = '<font color="FF0000">'.$artefacts_lang['fehler4'];
        }

        //genug ressourcen vorhanden?
        if ($fehlermsg == '' && $errmsg == '' && $restyp01 >= $benrestyp01 && $restyp02 >= $benrestyp02 && $restyp03 >= $benrestyp03 && $restyp04 >= $benrestyp04 && $restyp05 >= $benrestyp05) {
            $restyp01 = $restyp01 - $benrestyp01;
            $restyp02 = $restyp02 - $benrestyp02;
            $restyp03 = $restyp03 - $benrestyp03;
            $restyp04 = $restyp04 - $benrestyp04;
            $restyp05 = $restyp05 - $benrestyp05;
            $gr01 = $gr01 - $restyp01;
            $gr02 = $gr02 - $restyp02;
            $gr03 = $gr03 - $restyp03;
            $gr04 = $gr04 - $restyp04;
            $gr05 = $gr05 - $restyp05;
            //rohstoffe abziehen
            mysqli_execute_query(
                $GLOBALS['dbi'],
                "UPDATE de_user_data SET restyp01=restyp01-?, restyp02=restyp02-?, restyp03=restyp03-?, restyp04=restyp04-?, restyp05=restyp05-? WHERE user_id=?",
                [$gr01, $gr02, $gr03, $gr04, $gr05, $_SESSION['ums_user_id']]
            );
            //upgrade in der db hinterlegen
            mysqli_execute_query(
                $GLOBALS['dbi'],
                "INSERT INTO de_user_build (user_id, tech_id, anzahl, verbzeit) VALUES (?, 1000, 1, ?)",
                [$_SESSION['ums_user_id'], $tech_ticks]
            );
            //meldung an den account �ber den ausbau
            $errmsg .= '<font color="#00FF00">Der Geb&auml;udeausbau wurde gestartet.</font>';
        } else {
            $errmsg .= '<font color="#FF0000">'.$artefacts_lang['fehler5'].'</font>';
        }

        //transaktionsende
        $erg = releaseLock($_SESSION['ums_user_id']); //Lösen des Locks und Ergebnisabfrage
        if ($erg) {
            //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
        } else {
            print($artefacts_lang['error'].$_SESSION['ums_user_id'].$artefacts_lang['error2']."<br><br><br>");
        }
    }// if setlock-ende
    else {
        echo '<br><font color="#FF0000">'.$artefacts_lang['error3'].'</font><br><br>';
    }
}


//stelle die ressourcenleiste dar
include "resline.php";

$artefacts = array();
$flotten = array();

//Ergebnis der letzten Aktion; die alten Texte bringen eigene Farben mit, hier zählt nur, ob es ein Fehler war
$meldung = '';
if ($errmsg != '') {
    $meldung_fehler = preg_match('/FF0000|ccr/i', $errmsg);
    $meldung = '<div class="mod-meldung '.($meldung_fehler ? 'mod-meldung-fehler' : 'mod-meldung-ok').'">'.trim(strip_tags($errmsg)).'</div>';
}

//Artefaktbild mit Stufenpunkten
function artefact_kachel($id, $level, $maxlevel, $attribute = '', $klasse = '', $tag = 'div')
{
    $punkte = str_repeat('<i class="an"></i>', $level).str_repeat('<i></i>', max(0, $maxlevel - $level));
    return '<'.$tag.' class="art-kachel'.$klasse.'"'.$attribute.'><img src="gp/g/arte'.$id.'.gif" alt=""><span class="art-pips">'.$punkte.'</span></'.$tag.'>';
}

//Bonuswerte je Stufe, leer bei Artefakten ohne Stufenbonus
function artefact_boni($id)
{
    global $ua_werte;
    $boni = array();
    foreach ($ua_werte[$id - 1] ?? array() as $werte) {
        if ($werte[0] > 0) {
            $boni[] = $werte[0];
        }
    }
    return $boni;
}

//Tooltip: Name, Beschreibung, Bonus je Stufe mit der aktuellen Stufe hervorgehoben
function artefact_tooltip($id, $level)
{
    global $ua_name, $ua_desc, $artefacts_lang;
    $title = $ua_name[$id - 1].'&'.$ua_desc[$id - 1];
    $boni = artefact_boni($id);
    if (count($boni) > 0) {
        $title .= '<br>'.$artefacts_lang['bonusderstufe'];
        foreach ($boni as $i => $wert) {
            $zeile = ($i + 1).': '.number_format($wert, 2, ",", ".").'%';
            $title .= '<br>'.($i == $level - 1 ? '<b>'.$zeile.' &#9664;</b>' : $zeile);
        }
    }
    return $title;
}

if (!hasTech($pt, 28)) {
    if ($errmsg != '') {
        echo '<div class="info_box">'.$errmsg.'</div><br>';
    }

    $techcheck = "SELECT tech_name FROM de_tech_data WHERE tech_id=28";
    $db_tech = mysqli_execute_query($GLOBALS['dbi'], $techcheck);
    $row_techcheck = mysqli_fetch_array($db_tech);


    echo '<br>';
    rahmen_oben('Fehlende Technologie');
    echo '<table width="572" border="0" cellpadding="0" cellspacing="0">';
    echo '<tr align="left" class="cell">
	<td width="100"><a href="'.$sv_link[0].'?r='.$_SESSION['ums_rasse'].'&t=28" target="_blank"><img src="'.'gp/'.'g/t/'.$_SESSION['ums_rasse'].'_28.jpg" border="0"></a></td>
	<td valign="top">Du ben&ouml;tigst folgende Technologie: <b>'.getTechNameByRasse($row_techcheck['tech_name'], $_SESSION['ums_rasse']).'</b><br><br>'.link_technologien('Zu den Technologien').'</td>
	</tr>';
    echo '</table>';
    rahmen_unten();
} else {
    //schauen ob schon ein gebäudeupgrade läuft
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id, verbzeit FROM de_user_build WHERE tech_id=1000 AND user_id=?", [$_SESSION['ums_user_id']]);
    $gebinbau = mysqli_num_rows($db_daten);

    $plaetze = $artbldglevel + $ally_geb_bonus;
    $palenium = get_storage_amount($_SESSION['ums_user_id'], 1);

    //artefakte aus der db holen
    $db_artefakte = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_artefact WHERE user_id=? ORDER BY id, level", [$_SESSION['ums_user_id']]);
    $anz_artefakte = mysqli_num_rows($db_artefakte);

    ////////////////////////////////////////////////////////////////////
    //Artefaktgebäude
    ////////////////////////////////////////////////////////////////////

    //Ausbau: Knopf mit Kosten, laufender Ausbau, fehlendes Iradium oder Höchststufe
    if ($gebinbau != 0) {
        $row = mysqli_fetch_array($db_daten);
        $ausbau = '<div class="mod-feld">Ausbau l&auml;uft<div class="art-klein">noch '.$row["verbzeit"].' WT</div></div>';
    } elseif ($artbldglevel >= $maxlevel) {
        $ausbau = '<div class="mod-feld">H&ouml;chststufe erreicht</div>';
    } elseif ($restyp03 < $ausbaukosten) {
        $ausbau = '<div class="mod-feld mod-feld-grund">Zu wenig Iradium<div class="art-klein">'.number_format($ausbaukosten, 0, ",", ".").' Iradium n&ouml;tig</div></div>';
    } else {
        $ausbau = '<a href="artefacts.php?bupgrade=1" class="mod-btn">Ausbauen</a><div class="art-klein">'.number_format($ausbaukosten, 0, ",", ".").' Iradium &middot; '.$ausbauzeit.' WT</div>';
    }

    $belegt_title = 'Artefaktpl&auml;tze&'.$artefacts_lang['geblevel2'];
    if ($allyid > 0) {
        $belegt_title .= '<br>Zus&auml;tzliche Artefaktpl&auml;tze durch Allianzprojekte: '.$ally_geb_bonus;
    }

    rahmen_oben($artefacts_lang['artefaktgebaeude']);
    echo '<div class="art mod">';
    echo $meldung;
    echo '<div class="art-kopf">
		<img src="gp/g/t/1_28.jpg" class="art-gebaeude" alt="">
		<div class="art-kopf-text">
			<div class="mod-typ">Geb&auml;udestufe</div>
			<div class="art-stufe-gross"><b>'.$artbldglevel.'</b> / '.$maxlevel.'</div>
			<div class="art-belegt'.($anz_artefakte >= $plaetze ? ' art-belegt-voll' : '').'" rel="tooltip" title="'.$belegt_title.'"><b>'.$anz_artefakte.'</b> von '.$plaetze.' Artefaktpl&auml;tzen belegt</div>
			<div class="mod-balken"><span style="width: '.($plaetze > 0 ? min(100, round($anz_artefakte / $plaetze * 100)) : 100).'%;"></span></div>
		</div>
		<div class="art-ausbau">'.$ausbau.'</div>
	</div>';

    //Palenium bekommt man beim Zerstören; es steht nicht in der Rohstoffleiste, Tronic schon
    echo '<div class="art-bestand">
		<span class="mod-chip"><img src="gp/g/item1.png" alt="">Palenium <b>'.number_format($palenium, 0, ",", ".").'</b></span>
	</div>';

    echo '<div class="art-raster">';
    $ac = 0;
    while ($row = mysqli_fetch_array($db_artefakte)) {
        $id = $row['id'];
        echo artefact_kachel($id, $row['level'], $ua_maxlvl[$id - 1], ' data-i="'.$ac.'" data-nr="" rel="tooltip" title="'.artefact_tooltip($id, $row['level']).'"');

        //daten für das Skript
        $artefacts[$ac] = array(
            'lid' => (int)$row['lid'],
            'id' => (int)$id,
            'level' => (int)$row['level'],
            'maxlevel' => (int)$ua_maxlvl[$id - 1],
            'useable' => (int)($ua_useable[$id - 1] ?? 0),
            'bs' => (int)($ua_bs[$id - 1] ?? 0),
            'name' => $ua_name[$id - 1],
            'desc' => $ua_desc[$id - 1],
            'bonus' => artefact_boni($id),
        );
        $ac++;
    }

    for ($i = $ac; $i < $plaetze; $i++) {
        echo '<div class="art-kachel art-kachel-frei" rel="tooltip" title="Freier Artefaktplatz&Dies ist ein freier Platz f&uuml;r ein Artefakt.">'.$artefacts_lang['frei'].'</div>';
    }
    echo '</div>';

    //Aktionsfeld, füllt das Skript unten je nach Auswahl; unter dem Raster und am unteren Rand haftend,
    //damit die Kacheln nicht springen, wenn es mit der Auswahl höher oder niedriger wird
    echo '<div class="art-panel" id="art-panel"></div>';

    //Regeln aufklappbar statt im Tooltip, damit man sie auch auf dem Handy lesen kann
    echo '<details class="art-regeln">
		<summary>So funktioniert das Artefaktgeb&auml;ude</summary>
		<ul>
			<li>'.$artefacts_lang['geblevel2'].'</li>
			<li>'.$artefacts_lang['upinfo1'].' Kosten: '.$tcost1.' Tronic.</li>
			<li>'.$artefacts_lang['upinfo2'].' Kosten: '.$tcost2.' Tronic.</li>
			<li>Das Artefakt kann auch zerst&ouml;rt und in Palenium (100 pro Artefaktstufe) umgewandelt werden.</li>
			<li>'.$artefacts_lang['upinfo5'].'</li>
		</ul>
	</details>';
    echo '</div>';
    rahmen_unten();

    ////////////////////////////////////////////////////////////////////
    //Basisschiffe
    ////////////////////////////////////////////////////////////////////
    rahmen_oben('Basisschiffartefakte');
    echo '<div class="art mod">';
    echo '<div class="mod-hinweis">Jede Flotte wird von einem Basisschiff angef&uuml;hrt. In diesem k&ouml;nnen je nach erforschten Artefaktpl&auml;tzen bis zu 6 Artefakte eingesetzt werden, um ihre Wirksamkeit zu verbessern. Ein Austausch der Artefakte ist nur im Heimatsystem m&ouml;glich. Ein Klick auf ein eingesetztes Artefakt bringt es zur&uuml;ck ins Artefaktgeb&auml;ude.</div>';

    $fleetnames = array('Heimatflotte', 'Flotte I', 'Flotte II', 'Flotte III');
    for ($flotte = 0; $flotte <= 3; $flotte++) {
        $fleetid = $_SESSION['ums_user_id'].'-'.$flotte;
        $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_fleet WHERE user_id=?", [$fleetid]);
        $row = mysqli_fetch_array($result);
        $daheim = ($row['aktion'] == 0);

        $frei = 0;
        $plaetze_html = '';
        for ($artplace = 1; $artplace <= 6; $artplace++) {
            $artid = $row['artid'.$artplace];
            $artlvl = $row['artlvl'.$artplace];
            if ($artid > 0 && !empty($ua_name[$artid - 1])) {
                $tooltip = artefact_tooltip($artid, $artlvl);
                if ($daheim) {
                    $plaetze_html .= artefact_kachel($artid, $artlvl, $ua_maxlvl[$artid - 1], ' href="artefacts.php?a=2&fid='.($flotte + 1).'&id='.$artplace.'" rel="tooltip" title="'.$tooltip.'<br><br>Anklicken, um das Artefakt ins Artefaktgeb&auml;ude zu transferieren."', ' art-kachel-zurueck', 'a');
                } else {
                    $plaetze_html .= artefact_kachel($artid, $artlvl, $ua_maxlvl[$artid - 1], ' rel="tooltip" title="'.$tooltip.'<br><br>Die Flotte ist im Einsatz, ein Austausch ist nur im Heimatsystem m&ouml;glich."');
                }
            } elseif (hasTech($pt, 132 + $artplace)) {
                //Artefaktplätze 1-6 brauchen die Technologien 133-138
                $frei++;
                $plaetze_html .= '<div class="art-kachel art-kachel-frei" rel="tooltip" title="Freier Artefaktplatz&Dies ist ein freier Platz f&uuml;r ein Artefakt.">frei</div>';
            } else {
                $plaetze_html .= '<div class="art-kachel art-kachel-gesperrt" rel="tooltip" title="Fehlende Technologie&Diese Technologie muss erst noch erschlossen werden.">gesperrt</div>';
            }
        }

        echo '<div class="art-flotte">
			<div class="art-flotte-name">'.$fleetnames[$flotte].'<span class="mod-chip'.($daheim ? '' : ' mod-chip-warn').'">'.($daheim ? 'im Heimatsystem' : 'im Einsatz').'</span></div>
			<div class="art-flotte-plaetze">'.$plaetze_html.'</div>
		</div>';

        $flotten[] = array('name' => $fleetnames[$flotte], 'daheim' => $daheim, 'frei' => $frei);
    }
    echo '</div>';
    rahmen_unten();

    ////////////////////////////////////////////////////////////////////
    //Übersicht aller Artefakte
    ////////////////////////////////////////////////////////////////////
    rahmen_oben('Informationen zu den Artefakten');
    echo '<div class="art mod">';
    //showinfo=1 öffnet die Übersicht gleich, so funktionieren alte Links weiter
    echo '<details class="art-lexikon"'.(($_REQUEST['showinfo'] ?? 0) == 1 ? ' open' : '').'>
		<summary>Alle Artefakte im &Uuml;berblick</summary>
		<div class="art-woher"><b>Woher bekomme ich Artefakte?</b>
			<ul>
				<li>Du kannst diese durch Angriffe auf NPC-Systeme der DX61a23 bekommen.</li>
				<li>Es gibt unter Missionen die Möglichkeit Artefakte zu erhalten.</li>
				<li>Beim täglichen Allianzgeschenk ist ein Artefakt enthalten.</li>
			</ul>
		</div>';
    for ($i = 0; $i <= $ua_index; $i++) {
        $merkmale = '';
        if (($ua_bs[$i] ?? 0) == 1) {
            $merkmale .= '<span class="mod-chip">Basisschiff</span>';
        }
        if (($ua_useable[$i] ?? 0) == 1) {
            $merkmale .= '<span class="mod-chip">benutzbar</span>';
        }
        if ($ua_maxlvl[$i] > 1) {
            $merkmale .= '<span class="mod-chip">Stufe 1&ndash;'.$ua_maxlvl[$i].'</span>';
        }

        $boni = artefact_boni($i + 1);
        $boni_str = '';
        if (count($boni) > 0) {
            $boni_str = '<div class="art-eintrag-werte">Bonus je Stufe: '.implode(' &middot; ', array_map(function ($wert) {
                return number_format($wert, 2, ",", ".").' %';
            }, $boni)).'</div>';
        }

        echo '<div class="art-eintrag">
			<div class="art-kachel"><img src="gp/g/arte'.($i + 1).'.gif" alt=""></div>
			<div>
				<div class="art-eintrag-name">'.$ua_name[$i].$merkmale.'</div>
				<div>'.$ua_desc[$i].'</div>
				'.$boni_str.'
			</div>
		</div>';
    }
    echo '</details>';
    echo '</div>';
    rahmen_unten();
}

echo '<script>';
echo 'var artefakte = '.json_encode($artefacts, JSON_HEX_TAG).';';
echo 'var artInfo = '.json_encode(array('tronic' => (int)$restyp05, 'kosten1' => $tcost1, 'kosten2' => $tcost2, 'flotten' => $flotten), JSON_HEX_TAG).';';
?>

//Auswahl im Artefaktgebäude: ein Artefakt benutzen, einsetzen oder zerstören, zwei Artefakte verschmelzen
(function(){
  var panel = document.getElementById('art-panel');
  if(!panel){
    return;
  }
  var gewaehlt = [];

  function zahl(wert){
    return wert.toLocaleString('de-DE', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' %';
  }

  function kachel(x, stufe){
    var punkte = '';
    for(var s = 1; s <= x.maxlevel; s++){
      punkte += '<i' + (s <= stufe ? ' class="an"' : '') + '></i>';
    }
    return '<div class="art-kachel"><img src="gp/g/arte' + x.id + '.gif" alt=""><span class="art-pips">' + punkte + '</span></div>';
  }

  function bonus(x, stufe){
    return x.bonus.length >= stufe ? zahl(x.bonus[stufe - 1]) : '';
  }

  //bestaetigen: Text nach dem ersten Klick, ausgeführt wird erst beim zweiten (siehe de_fn.js)
  function knopf(href, text, bestaetigen, gefahr){
    return '<a class="mod-btn' + (gefahr ? ' mod-btn-gefahr' : '') + '" href="' + href + '"'
      + (bestaetigen ? ' data-bestaetigen="' + bestaetigen + '"' : '') + '>' + text + '</a>';
  }

  function grund(text){
    return '<span class="mod-feld mod-feld-grund">' + text + '</span>';
  }

  function zeige(){
    document.querySelectorAll('.art-raster .art-kachel[data-i]').forEach(function(k){
      var pos = gewaehlt.indexOf(parseInt(k.getAttribute('data-i'), 10));
      k.classList.toggle('art-gewaehlt', pos >= 0);
      k.setAttribute('data-nr', pos >= 0 ? pos + 1 : '');
    });

    if(gewaehlt.length == 0){
      panel.innerHTML = '<div class="art-panel-leer">Tippe ein Artefakt an, um es zu benutzen, in ein Basisschiff zu setzen oder zu zerstören. Wähle zwei Artefakte aus, um sie zu verschmelzen.</div>';
      return;
    }
    if(gewaehlt.length > 2){
      panel.innerHTML = '<div class="art-wahl-fehler">Es können nicht mehr als 2 Artefakte kombiniert werden.</div>';
      return;
    }

    var x = artefakte[gewaehlt[0]], html = '', knoepfe = '';

    if(gewaehlt.length == 1){
      var b = bonus(x, x.level), wirkung = '';
      if(x.useable == 1){
        knoepfe += knopf('artefacts.php?useartefact=1&lid=' + x.lid, 'Benutzen');
      }else if(x.bs == 1){
        artInfo.flotten.forEach(function(f, i){
          if(f.daheim && f.frei > 0){
            knoepfe += knopf('artefacts.php?a=1&fid=' + (i + 1) + '&id=' + x.id + '&lvl=' + x.level, 'In ' + f.name);
          }else{
            knoepfe += grund(f.name + ': ' + (f.daheim ? 'kein Platz frei' : 'im Einsatz'));
          }
        });
      }else{
        wirkung = '<div class="art-wahl-desc">Dieses Artefakt wirkt, solange es im Artefaktgebäude liegt.</div>';
      }
      knoepfe += knopf('artefacts.php?destroyartefact=1&lid=' + x.lid, 'Zerstören · +' + (100 * x.level) + ' Palenium', 'Wirklich zerstören?', true);

      html = '<div class="art-wahl">' + kachel(x, x.level)
        + '<div class="art-wahl-text"><div class="mod-typ">Artefakt · Stufe ' + x.level + ' von ' + x.maxlevel + '</div>'
        + '<div class="art-wahl-name">' + x.name + '</div>'
        + '<div class="art-wahl-desc">' + x.desc + (b ? ' Bonus dieser Stufe: <b>' + b + '</b>' : '') + '</div>'
        + wirkung + '</div></div>';
    }else{
      var y = artefakte[gewaehlt[1]], kosten = 0, text = '', ziel = '';
      if(x.id != y.id){
        text = 'Diese beiden Artefakte können in ein zufälliges Artefakt der Stufe 1 verschmolzen werden. Das Zielartefakt wird anderer Art als die Quellartefakte sein.';
        ziel = '<div class="art-kachel art-kachel-frei">?</div>';
        kosten = artInfo.kosten2;
        knoepfe = knopf('artefacts.php?mergeartefacts=1&lid1=' + x.lid + '&lid2=' + y.lid, 'Neues Artefakt erzeugen · ' + kosten + ' Tronic', 'Wirklich verschmelzen?');
      }else if(x.level != y.level){
        text = '<span class="art-wahl-fehler">Artefakte der gleichen Art, aber mit unterschiedlicher Stufe können nicht verschmolzen werden.</span>';
      }else if(x.level >= x.maxlevel){
        text = '<span class="art-wahl-fehler">Diese Artefakte befinden sich bereits auf der höchsten Stufe.</span>';
      }else{
        var b1 = bonus(x, x.level), b2 = bonus(x, x.level + 1);
        text = 'Diese Artefakte können zu einem Artefakt der gleichen Art mit einer höheren Stufe verschmolzen werden.'
          + (b2 ? ' Bonus: 2 × ' + b1 + ' → <b>' + b2 + '</b>' : '');
        ziel = kachel(x, x.level + 1);
        kosten = artInfo.kosten1;
        knoepfe = knopf('artefacts.php?mergeartefacts=1&lid1=' + x.lid + '&lid2=' + y.lid, 'Verschmelzen · ' + kosten + ' Tronic');
      }
      if(kosten > artInfo.tronic){
        knoepfe = grund('Zu wenig Tronic (' + kosten + ' nötig)');
      }
      html = '<div class="art-wahl">' + kachel(x, x.level) + '<span class="art-plus">+</span>' + kachel(y, y.level)
        + (ziel ? '<span class="art-plus">→</span>' + ziel : '')
        + '<div class="art-wahl-desc">' + text + '</div></div>';
    }

    panel.innerHTML = html + (knoepfe ? '<div class="art-knoepfe">' + knoepfe + '</div>' : '');
  }

  document.querySelectorAll('.art-raster .art-kachel[data-i]').forEach(function(k){
    k.addEventListener('click', function(){
      var i = parseInt(k.getAttribute('data-i'), 10), pos = gewaehlt.indexOf(i);
      if(pos >= 0){
        gewaehlt.splice(pos, 1);
      }else{
        gewaehlt.push(i);
      }
      zeige();
    });
  });

  zeige();
})();
</script>
</body>
</html>
