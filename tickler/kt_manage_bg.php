<?php

echo '<hr>Battlegrounds<br><br>';

$bg_debug = 0;

//print_r($sv_bg);

//alle BGs durchgehen und schauen ob sie aktiv sind
for ($bg = 0;$bg < 3;$bg++) {
    //tickt das BG?
    if ($max_kt % $sv_bg[$bg]['start_interval'] == 0) {
        echo '<hr><br>Starte BG '.$bg.'<br>';

        doBattleGround($bg);

    }
}

echo '<hr>';

function doBattleGround($bg)
{
    global $bg_debug;

    $play_typ = 0; //Spieler-BG

    if ($bg == 2) {
        $play_typ = 1; //Ally-BG
    }

    $player_id = 0;
    $ally_id = 0;

    $player = array();
    $player_ids = array();

    $allys = array();
    $ally_ids = array();

    $fightresult = array();



    //die map_id zu dem BG aus der DB holen
    $sql = "SELECT * FROM `de_map_objects` WHERE system_typ=4 AND system_subtyp=?;";
    $db_data = mysqli_execute_query($GLOBALS['dbi'], $sql, [$bg]);
    $row = mysqli_fetch_array($db_data);
    $map_id = $row['id'];

    //$techcheck="SELECT tech_name FROM de_tech_data WHERE tech_id=159";


    ////////////////////////////////////////////////////////////////
    //teilnehmende Spieler laden / special ships laden
    ////////////////////////////////////////////////////////////////
    $time_now = time();
    $sql = "SELECT * FROM de_login LEFT JOIN de_user_data ON (de_login.user_id=de_user_data.user_id) LEFT JOIN de_user_techs ON (de_user_techs.user_id=de_user_data.user_id) LEFT JOIN de_user_map_bldg ON (de_user_map_bldg.user_id=de_user_data.user_id) WHERE de_login.status=1 AND de_user_techs.tech_id=159 AND de_user_techs.time_finished<=? AND de_user_map_bldg.map_id=? AND de_user_map_bldg.bldg_time<=?;";
    $params = [$time_now, $map_id, $time_now];

    $db_data = mysqli_execute_query($GLOBALS['dbi'], $sql, $params);
    while ($row = mysqli_fetch_array($db_data)) {
        //Schiff laden
        $player[$player_id]['user_id'] = $row['user_id'];
        $player[$player_id]['ship'] = loadSpecialShip($row['user_id']);
        $player[$player_id]['score'] = $row['bgscore'.$bg];
        $player[$player_id]['spielername'] = $row['spielername'];

        $player[$player_id]['ally_id'] = get_player_allyid($row['user_id']);
        $player[$player_id]['ally_tag'] = getAllytagByAllyid($player[$player_id]['ally_id']);
        $player[$player_id]['runde'] = -1;

        //alle user_id in ein array packen
        $player_ids[] = $row['user_id'];



        $player_id++;
    }

    //wenn es ein Ally-BG ist, dann die Spielerdaten in Allydaten "umbauen"
    if ($play_typ == 1) {
        for ($p = 0;$p < count($player);$p++) {

            //ist der Spieler in einer Allianz?
            if ($player[$p]['ally_id'] > 0) {

                //gibt es schon ein Schiff von der Ally?
                $ship_exist = false;
                for ($a = 0;$a < count($allys);$a++) {
                    if ($allys[$a]['ally_id'] == $player[$p]['ally_id']) {
                        $ship_exist = true;
                        $ship_exist_id = $a;
                    }
                }

                //neuer allyeintrag oder draufaddieren
                if ($ship_exist) {
                    //draufaddieren
                    echo 'add';

                    $allys[$ship_exist_id]['ship']->ship_level += $player[$p]['ship']->ship_level;

                } else {
                    echo 'neu';
                    //neues Schiff anlegen
                    $allys[$ally_id]['ally_id'] =		$player[$p]['ally_id'];
                    $allys[$ally_id]['spielername'] =	$player[$p]['ally_tag'];
                    $allys[$ally_id]['ship'] =		$player[$p]['ship'];
                    $allys[$ally_id]['score'] =		getAllyBGScore($allys[$ally_id]['ally_id'], $bg);
                    $allys[$ally_id]['runde'] =		-1;

                    $ally_ids[] = $allys[$ally_id]['ally_id'];
                    $ally_id++;
                }
            }
        }
    }

    print_r($allys);

    //wenn es Teilnehmer gibt, den Kampf starten
    //die Kampfpartner werden per bgscore zugeteilt
    if (count($player) > 0) {
        //////////////////////////////////////////////
        // Spieler BG
        //////////////////////////////////////////////

        if ($play_typ == 0) {

            for ($runde = 0;$runde < 1;$runde++) {
                echo '<br>RUNDE: '.$runde;

                //alle Spieler durchgehen
                for ($p = 0;$p < count($player);$p++) {
                    echo '<br>PLAYER: '.$p;

                    //größten spieler finden, der noch nicht gekämpft hat
                    $player_id1 = -1;
                    $max_score = -1;
                    for ($i = 0;$i < count($player);$i++) {
                        if ($player[$i]['score'] > $max_score && $player[$i]['runde'] < $runde) {
                            $player_id1 = $i;
                            $max_score = $player[$i]['score'];
                        }
                    }

                    //den gefundenen Spieler für diese Runde als verwendet markieren
                    if ($player_id1 > -1) {
                        $player[$player_id1]['runde'] = $runde;
                    }

                    //zweitgrößten spieler finden, der noch nicht gekämpft hat
                    $player_id2 = -1;
                    $max_score = -1;
                    for ($i = 0;$i < count($player);$i++) {
                        if ($player[$i]['score'] > $max_score && $player[$i]['runde'] < $runde) {
                            $player_id2 = $i;
                            $max_score = $player[$i]['score'];
                        }
                    }

                    if ($player_id2 > -1) {
                        $player[$player_id2]['runde'] = $runde;
                    }


                    //spieler kämpfen lassen
                    if ($player_id1 != -1 || $player_id2 != -1) {
                        $winner_id = letSpecialShipFight($player_id1, $player_id2, $player);

                        echo '<br>P1: '.$player[$player_id1]['spielername'];
                        if($player_id2 > 0){
                            echo '<br>P2: '.$player[$player_id2]['spielername'];
                        }else{
                            echo '<br>P2: kein Gegner';
                        }

                        echo '<br>GE: '.$player[$winner_id]['spielername'];

                        echo '<br><br>';

                        $p1_ship_level = '';
                        $p2_ship_level = '';

                        if ($player_id1 > -1) {
                            $p1_ship_level = '&nbsp;(Stufe '.$player[$player_id1]['ship']->ship_level.')';
                        }

                        if ($player_id2 > -1) {
                            $p2_ship_level = '&nbsp;(Stufe '.$player[$player_id2]['ship']->ship_level.')';
                        }


                        if ($bg == 0) {
                            $gewinn_text = '1 Tronic';
                        } elseif ($bg == 1) {
                            $gewinn_text = '1 Kriegsartefakt';
                        }

                        $spielername1='';
                        if($player_id1 > -1){
                            $spielername1 = $player[$player_id1]['spielername'];
                        }

                        $spielername2='';
                        if($player_id2 > -1){
                            $spielername2 = $player[$player_id2]['spielername'];
                        }

                        //ergebnis speichern
                        $fightresult[$runde][] = array(
                            'winner_user_id' => $player[$winner_id]['user_id'],
                            'user_id1' => $player[$player_id1]['user_id'],
                            'user_id2' => $player[$player_id2]['user_id'],
                            'spielername1' => $spielername1.$p1_ship_level,
                            'spielername2' => $spielername2.$p2_ship_level,
                            'gewinn' => $gewinn_text
                        );

                        //echo '<br>A:'.$winner_id;

                        if ($player[$winner_id]['user_id'] > 0) {
                            if ($bg == 0) {
                                $sql = "UPDATE `de_user_data` SET restyp05=restyp05+1, bgscore$bg=bgscore$bg+1 WHERE user_id=?;";
                            } elseif ($bg == 1) {
                                $sql = "UPDATE `de_user_data` SET kartefakt=kartefakt+1, bgscore$bg=bgscore$bg+1 WHERE user_id=?;";
                            }

                            //echo $sql;
                            mysqli_execute_query($GLOBALS['dbi'], $sql, [$player[$winner_id]['user_id']]);
                        }
                    }
                }
            }

            //DB updaten
            $kb = base64_encode(serialize($fightresult));
            $time = strftime("%Y%m%d%H%M%S");
            for ($p = 0;$p < count($player);$p++) {
                $uid = $player[$p]['user_id'];
                //Kampfbericht
                $sql = "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 70, ?, ?)";
                //echo $sql;
                mysqli_execute_query($GLOBALS['dbi'], $sql, [$uid, $time, $kb]);

                //neue News vorhanden
                $sql = "UPDATE `de_user_data` SET newnews=1 WHERE user_id=?;";
                //echo $sql;
                mysqli_execute_query($GLOBALS['dbi'], $sql, [$uid]);

            }

            print_r($fightresult);
        } elseif ($play_typ == 1) {
            /////////////////////////////////////////////
            // Ally BG
            /////////////////////////////////////////////

            for ($runde = 0;$runde < 1;$runde++) {
                echo '<br>RUNDE: '.$runde;

                //alle Spieler durchgehen
                for ($p = 0;$p < count($allys);$p++) {
                    echo '<br>PLAYER: '.$p;

                    //größten spieler finden, der noch nicht gekämpft hat
                    $player_id1 = -1;
                    $max_score = -1;
                    for ($i = 0;$i < count($player);$i++) {
                        if(!isset($allys[$i]['score'])){
                            $allys[$i]['score'] = 0;
                        }

                        if(!isset($allys[$i]['runde'])){
                            $allys[$i]['runde'] = -1;
                        }

                        if ($allys[$i]['score'] > $max_score && $allys[$i]['runde'] < $runde) {
                            $player_id1 = $i;
                            $max_score = $allys[$i]['score'];
                        }
                    }

                    //den gefundenen Spieler für diese Runde als verwendet markieren
                    if ($player_id1 > -1) {
                        $allys[$player_id1]['runde'] = $runde;
                    }

                    //zweitgrößten spieler finden, der noch nicht gekämpft hat
                    $player_id2 = -1;
                    $max_score = -1;
                    for ($i = 0;$i < count($allys);$i++) {
                        if ($allys[$i]['score'] > $max_score && $allys[$i]['runde'] < $runde) {
                            $player_id2 = $i;
                            $max_score = $allys[$i]['score'];
                        }
                    }

                    if ($player_id2 > -1) {
                        $allys[$player_id2]['runde'] = $runde;
                    }


                    //spieler kämpfen lassen
                    if ($player_id1 != -1 || $player_id2 != -1) {
                        $winner_id = letSpecialShipFight($player_id1, $player_id2, $allys);

                        echo '<br>P1: '.(isset($allys[$player_id1]['spielername']) ? $allys[$player_id1]['spielername'] : 'Kein Gegner');
                        echo '<br>P2: '.(isset($allys[$player_id2]['spielername']) ? $allys[$player_id2]['spielername'] : 'Kein Gegner');
                        echo '<br>GE: '.(isset($allys[$winner_id]['spielername']) ? $allys[$winner_id]['spielername'] : 'Kein Gegner');

                        echo '<br><br>';

                        $p1_ship_level = '';
                        $p2_ship_level = '';

                        if ($player_id1 > -1) {
                            if (isset($allys[$player_id1]['ship']) && $allys[$player_id1]['ship'] != null) {
                                $p1_ship_level = '&nbsp;(Gesamtstufe '.$allys[$player_id1]['ship']->ship_level.')';
                            }
                        }

                        if ($player_id2 > -1) {
                            if (isset($allys[$player_id2]['ship']) && $allys[$player_id2]['ship'] != null) {
                                $p2_ship_level = '&nbsp;(Gesamtstufe '.$allys[$player_id2]['ship']->ship_level.')';
                            }
                        }


                        if ($bg == 2) {
                            $gewinn_text = '1 Quantenglimmer';
                        }

                        //ergebnis speichern
                        $spielername1 = (isset($allys[$player_id1]['spielername']) ? $allys[$player_id1]['spielername'] : '').$p1_ship_level;
                        $spielername2 = (isset($allys[$player_id2]['spielername']) ? $allys[$player_id2]['spielername'] : '').$p2_ship_level;

                        if(!empty($spielername1) || !empty($spielername2)){
                            $fightresult[$runde][] = array(
                                'winner_user_id' => isset($allys[$winner_id]['ally_id']) ? $allys[$winner_id]['ally_id'] : 0,
                                'user_id1' => isset($allys[$player_id1]['ally_id']) ? $allys[$player_id1]['ally_id'] : 0,
                                'user_id2' => isset($allys[$player_id2]['ally_id']) ? $allys[$player_id2]['ally_id'] : 0,
                                'spielername1' => $spielername1,
                                'spielername2' => $spielername2,
                                'gewinn' => $gewinn_text
                            );
                        }

                        if (isset($allys[$winner_id]['ally_id']) && $allys[$winner_id]['ally_id'] > 0) {

                            if ($bg == 2) {
                                echo '<br>C: BG2';
                                $sql = "UPDATE de_allys SET bgscore$bg=bgscore$bg+1 WHERE id=?;";
                                echo $sql;
                                mysqli_execute_query($GLOBALS['dbi'], $sql, [$allys[$winner_id]['ally_id']]);

                                //Quantenglimmer gutschreiben
                                changeAllyStorageAmount($allys[$winner_id]['ally_id'], 13, 1, false);
                            }

                        }
                    }
                }
            }

            //DB updaten
            $kb = base64_encode(serialize($fightresult));
            $time = strftime("%Y%m%d%H%M%S");
            for ($p = 0;$p < count($player);$p++) {
                //nur Spieler mit einer Ally erhalten den KB
                if ($player[$p]['ally_id'] > 0) {
                    $uid = $player[$p]['user_id'];
                    //Kampfbericht
                    $sql = "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 70, ?, ?)";
                    //echo $sql;
                    mysqli_execute_query($GLOBALS['dbi'], $sql, [$uid, $time, $kb]);

                    //neue News vorhanden
                    $sql = "UPDATE `de_user_data` SET newnews=1 WHERE user_id=?;";
                    //echo $sql;
                    mysqli_execute_query($GLOBALS['dbi'], $sql, [$uid]);
                }

            }

            print_r($fightresult);
        }

    }//es gibt spieler
}

function letSpecialShipFight($player_id1, $player_id2, $player)
{

    $fighlog = '';

    //wenn ein Spieler keinen Gegner hat, dann hat er automatisch gewonnen
    if ($player_id1 == -1) {
        return $player_id2;
    } elseif ($player_id2 == -1) {
        return $player_id1;
    }

    //print_r($player);

    //die();

    // Überprüfung, ob die Spieler und ihre Schiffe existieren
    $enm[0] = isset($player[$player_id1]['ship']) ? $player[$player_id1]['ship'] : null;
    $enm[1] = isset($player[$player_id2]['ship']) ? $player[$player_id2]['ship'] : null;

    // Überprüfung, ob die Schiffe existieren
    if ($enm[0] == null || $enm[1] == null) {
        // Falls ein Schiff null ist, automatisch den anderen Spieler gewinnen lassen
        if ($enm[0] == null && $enm[1] != null) {
            return $player_id2;
        } elseif ($enm[1] == null && $enm[0] != null) {
            return $player_id1;
        } else {
            // Beide Schiffe sind null - ersten Spieler gewinnen lassen (oder anderen Fallback)
            return $player_id1;
        }
    }

    //////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////
    //  kampf berechnen
    //////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////

    $trefferwahrscheinlichkeit[0] = 80;
    $trefferwahrscheinlichkeit[1] = 80;

    $schadenssenkung[0] = 0;
    $schadenssenkung[1] = 0;

    $critchance[0] = 10;
    $critchance[1] = 10;

    $schaden_min[0] = $enm[0]->get_wp_min();
    $schaden_max[0] = $enm[0]->get_wp_max();

    $schaden_min[1] = $enm[1]->get_wp_min();
    $schaden_max[1] = $enm[1]->get_wp_max();


    //die daten der gegner anzeigen
    $fightlog =
    '<table>
	<tr><td>Einheit</td><td width="30%" class="c2">'.$player[$player_id1]['spielername'].'</td><td width="30%" class="c2">'.$player[$player_id2]['spielername'].'</td></tr>
	<tr><td>Schiffstufe</td><td class="c2">'.number_format($enm[0]->ship_level, 0, "", ".").'</td><td class="c2">'.number_format($enm[1]->ship_level, 0, "", ".").'</td></tr>
	<tr><td>H&uuml;llenstruktur</td><td class="c2">'.number_format($enm[0]->get_hp_max(), 0, "", ".").'</td><td class="c2">'.number_format($enm[1]->get_hp_max(), 0, "", ".").'</td></tr>
	<tr><td>Schilde</td><td class="c2">'.number_format($enm[0]->get_shield_max(), 0, "", ".").'</td><td class="c2">'.number_format($enm[1]->get_shield_max(), 0, "", ".").'</td></tr>
	<tr><td>Waffen</td><td class="c2">'.number_format($enm[0]->get_wp_min(), 0, "", ".").' - '.number_format($enm[0]->get_wp_max(), 0, "", ".").'</td><td class="c2">'.number_format($enm[1]->get_wp_min(), 0, "", ".").' - '.number_format($enm[1]->get_wp_max(), 0, "", ".").'</td></tr>
	<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
	';

    $hp[0] = $enm[0]->get_hp_max() + $enm[0]->get_shield_max();
    $hp[1] = $enm[1]->get_hp_max() + $enm[1]->get_shield_max();

    //$shield[0]=$enm[0]->get_shield_max();
    //$shield[1]=$enm[1]->get_shield_max();

    $fightlog .=
    '<tr align="center" class="bg2"><td width="10%">Runde</td><td width="45%">'.$player[$player_id1]['spielername'].'</td><td width="45%">'.$player[$player_id2]['spielername'].'</td></tr>
	<tr align="center" class="bg2"><td>1</td><td title="&Aufstellung">'.number_format($hp[0], 0, "", ".").'</td><td title="&Aufstellung">'.number_format($hp[1], 0, "", ".").'</td></tr>';


    $maxrunden = 60;
    $haswon = 0;
    for ($r = 1;$r < $maxrunden;$r++) {
        //echo '<br>'.$r;
        //gegnerschaden berechnen
        $critflag[0] = 0;
        $critflag[1] = 0;

        $ausweichflag[0] = 0;
        $ausweichflag[1] = 0;

        $schaden[0] = round(mt_rand($enm[0]->get_wp_min(), $enm[0]->get_wp_max()));
        $schaden[1] = round(mt_rand($enm[1]->get_wp_min(), $enm[1]->get_wp_max()));

        for ($c = 0;$c <= 1;$c++) {
            //trefferwahrscheinlichkeit
            //zum testen auf 50% gesetzt
            if ($trefferwahrscheinlichkeit[$c] >= mt_rand(0, 100)) {
                //waffenschaden
                $schadenssenkung_enm = 0;
                if (isset($schadenssenkung[0]) && isset($schadenssenkung[1])) {
                    $schadenssenkung_enm = ($c == 0) ? $schadenssenkung[1] : $schadenssenkung[0];
                }
                $schaden[$c] = round($schaden[$c] * (100 - $schadenssenkung_enm) / 100);

                //$schaden[$c]+=$eschaden[$c];

                //test auf kritischen treffer
                if ($critchance[$c] > mt_rand(1, 100)) {
                    $schaden[$c] = $schaden[$c] * 2;
                    $critflag[$c] = 1;
                }

                if ($schaden[$c] < 0) {
                    $schaden[$c] = 0;
                }
                //echo 'treffer'.$schaden[$c].'<br>';
            } else {
                $schaden[$c] = 0;
                $ausweichflag[$c] = 1;
            }
        }

        //echo '<br>Schaden 0: '.$schaden[0];
        //echo '<br>Schaden 1: '.$schaden[1];


        //player 1 schlägt zu
        if ($hp[1] - $schaden[0] <= 0) {
            //player 2 hat verloren
            $haswon = 1;
            $hp[1] -= $schaden[0];
            $ausweichflag[1] = 1;
            $critflag[1] = 0;
        } else {
            //player 2 hp abziehen
            $hp[1] -= $schaden[0];
        }


        //player 2 schlägt zu
        if ($hp[0] - $schaden[1] <= 0 and $haswon == 0) {
            //player 1 hat verloren
            $hp[0] -= $schaden[1];
            $haswon = 2;
            $ausweichflag[0] = 1;
            $critflag[0] = 0;
        } else {
            //player 1 hp abziehen
            if ($haswon == 0) {
                $hp[0] -= $schaden[1];
            }
        }

        //die einzelnen kampfphasen mitloggen
        $title[0] = '&';
        $title[1] = '&';
        //crit
        $format[0] = '';
        $format[1] = '';
        $format[2] = '';
        $format[3] = '';
        if ($critflag[0] == 1) {
            $title[0] .= 'Spieler 2 hat einen kritischen Treffer erhalten.';
            $format[2] = '<b>';
            $format[3] = '</b>';
        }
        if ($critflag[1] == 1) {
            $title[1] .= 'Spieler 1 hat einen kritischen Treffer erhalten.';
            $format[0] = '<b>';
            $format[1] = '</b>';
        }

        //verfehlt
        if ($ausweichflag[0] == 1) {
            $title[0] .= 'verfehlt';
        }
        if ($ausweichflag[1] == 1) {
            $title[1] .= 'verfehlt';
        }
        //normaler treffer
        if ($title[0] == '&') {
            $title[0] .= 'getroffen';
        }
        if ($title[1] == '&') {
            $title[1] .= 'getroffen';
        }
        //$title='';

        $fightlog .= '<tr align="center" class="bg2"><td>'.($r + 1).'</td><td title="'.$title[1].'">'.$format[0].number_format($hp[0], 0, "", ".").$format[1].'</td><td title="'.$title[0].'">'.$format[2].number_format($hp[1], 0, "", ".").$format[3].'</td></tr>';




        //wenn jemand gewonnen hat kampf abbrechen
        if ($haswon > 0) {
            $r = $maxrunden;
        }



        //nach maxrunden runden hat der gewonnen der mehr hp hat, wenn beide gleichviel haben, dann wird gelost
        if ($r == ($maxrunden - 1) and $haswon == 0) {//unentschieden, gewinner wird ausgelost
            //zufall
            if ($hp[0] == $hp[1]) {
                $haswon = mt_rand(1, 2);
            } else {
                if ($hp[0] > $hp[1]) {
                    $haswon = 1;
                } else {
                    $haswon = 2;
                }
            }
        }

        //echo '<br>HP1: '.$userhp[0].'<br>';
        //echo 'HP2: '.$userhp[1].'<br>';
    }//ende maxrunden

    $fightlog .= '</table>';


    if ($haswon == 1) {
        $winner_id = $player_id1;
    } else {
        $winner_id = $player_id2;
    }

    echo $fightlog;

    return $winner_id;
}
