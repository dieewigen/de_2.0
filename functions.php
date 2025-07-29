<?php

function createTitleForUser($ownerId, $title){

    //Titel in der DB hinterlegen
    $sql = "INSERT INTO ls_title SET title='$title';";
    echo $sql;
    mysqli_query($GLOBALS['dbi_ls'], $sql);
    $titleId=mysqli_insert_id($GLOBALS['dbi_ls']);

    //den Titel dem Spieler im Hauptaccount zuweisen
    $sql = "INSERT INTO ls_user_title SET user_id='$ownerId', title_id='$titleId';";
    echo $sql;
    mysqli_query($GLOBALS['dbi_ls'], $sql);

}

function write2agentlog($uid, $reason, $change_amount)
{
    /*
    //zuerst auslesen wieviel man hat
    $db_daten=mysqli_query($GLOBALS['dbi'],"SELECT agent, agent_lost FROM de_user_data WHERE user_id='$uid'");
    $row = mysqli_fetch_array($db_daten);
    $agent=$row["agent"];
    $agent_lost=$row["agent_lost"];

    //die creditausgabe im billing-logfile hinterlegen
    $datum=date("Y-m-d H:i:s",time());
    $clog="Zeit: $datum A: $agent AL: $agent_lost CA: $change_amount - ".$reason."\n";
    $fp=fopen("cache/logs/agentlog_$uid.txt", "a");
    fputs($fp, $clog);
    fclose($fp);
    */
}

function getInfocenter()
{
    //mögliche Punkte: Flotten, Rohstofflieferung, Spezialisierung,  Sabotage, Missionen/VS-Missionen, Anzahl erforschte VS, Technologien(erforsch/offen)
    $content = '';
    $pt = loadPlayerTechs($_SESSION['ums_user_id']);

    //$content.='<div class="red mb10">under construction</div>';

    /////////////////////////////////////////////////
    // Missionen
    /////////////////////////////////////////////////
    $content .= '<div class="header">Missionen</div>';
    if (!hasTech($pt, 29)) {
        $techcheck = "SELECT tech_name FROM de_tech_data WHERE tech_id=29";
        $db_tech = mysqli_query($GLOBALS['dbi'], $techcheck);
        $row_techcheck = mysqli_fetch_array($db_tech);

        $content .= '<div>Fehlende Technologie: '.getTechNameByRasse($row_techcheck['tech_name'], $_SESSION['ums_rasse']).'<div>';

    } else {
        //die Missionsdatensätze auslesen
        $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT * FROM de_user_mission WHERE user_id=".$_SESSION['ums_user_id'].";");
        $m_abholbereit = 0;
        $m_laufen = 0;
        while ($row = mysqli_fetch_array($db_daten)) {
            if ($row['end_time'] <= time() && $row['get_reward'] == 0) {
                $m_abholbereit++;
            }

            if ($row['end_time'] > time()) {
                $m_laufen++;
            }

        }

        if ($m_abholbereit > 0) {
            $content .= '<div class="green">abholbereit: '.$m_abholbereit.'</div>';
        } else {
            if ($m_laufen == 0) {
                $content .= '<div class="red">aktiv: 0</div>';
            } else {
                $content .= '<div>aktiv: '.$m_laufen.'</div>';
            }

        }
    }

    /////////////////////////////////////////////////
    // Technologien
    /////////////////////////////////////////////////
    //zuerst checken ob man evtl. schon alle Techs hat, dann braucht man den Punkt gar nicht mehr anzeigen
    $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT COUNT(*) AS anzahl FROM de_tech_data WHERE tech_sort_id < 1000");
    $row = mysqli_fetch_array($db_daten);
    $tech_max = $row['anzahl'];
    $tech_anzahl = count($pt);

    if ($tech_anzahl < $tech_max) {
        $content .= '<div class="header mt10">Technologien</div>';
        //wird aktuell etwas erforscht?
        $t_aktiv = 0;
        foreach ($pt as $tech) {


            if (is_array($tech) && $tech['time_finished'] > time()) {
                $t_aktiv++;
            }
        }

        if ($t_aktiv > 0) {
            $content .= '<div>aktiv: '.$t_aktiv.'</div>';
        } else {
            $content .= '<div class="red">aktiv: 0</div>';
        }

        //$content.=count($pt).'/'.$tech_max;

        //$content.=print_r($pt,true);

    }


    return $content;
}

function getStolenColByUID($user_id)
{
    $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT SUM(colanz) AS colanz FROM de_user_getcol WHERE user_id='$user_id' AND colanz>0;");
    $row = mysqli_fetch_array($db_daten);

    return $row['colanz'];
}

function getItemBuildAmount($user_id, $item_id)
{
    $tech_id = $item_id + 10000;
    $sql = "SELECT SUM(anzahl) AS anzahl FROM de_user_build WHERE user_id='$user_id' AND tech_id='$tech_id';";
    $db_daten = mysqli_query($GLOBALS['dbi'], $sql);
    $row = mysqli_fetch_array($db_daten);

    return $row['anzahl'];
}

function getUsedFactoryCapacity($user_id, $factory_id)
{
    $sql = "SELECT SUM(factory_used_capacity) AS anzahl FROM de_user_build WHERE user_id='$user_id' AND factory_id='$factory_id';";
    $db_daten = mysqli_query($GLOBALS['dbi'], $sql);
    $row = mysqli_fetch_array($db_daten);

    return $row['anzahl'];
}

function getAveragePlayerAmountInSectorOnServer()
{
    global $sv_maxsector;
    $sql = "SELECT COUNT(user_id) AS anzahl FROM de_user_data WHERE sector>2 AND npc=0;";
    $db_daten = mysqli_query($GLOBALS['dbi'], $sql);
    $row = mysqli_fetch_array($db_daten);

    //echo $row['anzahl'].'/'.($sv_maxsector-2);
    //echo '<br><br>';

    $avg = $row['anzahl'] / ($sv_maxsector - 2);

    return($avg);
}

function getAnzahlGeworbeneSpielerByOwnerid($owner_id)
{
    $sql = "SELECT COUNT(user_id) AS anzahl FROM de_user_data WHERE sector>1 AND werberid='$owner_id';";
    $db_daten = mysqli_query($GLOBALS['dbi'], $sql);
    $row = mysqli_fetch_array($db_daten);

    return intval($row['anzahl']);
}

function createAuction($uid)
{
    global $sv_deactivate_vsystems, $ua_name, $db;
    //Maximale Tickanzahl auslesen
    $result  = mysqli_execute_query($GLOBALS['dbi'], "SELECT wt AS tick FROM de_system LIMIT 1", []);
    $row     = mysqli_fetch_array($result);
    $maxtick = $row["tick"];

    unset($reward);
    unset($cost);

    // Liste der möglichen Auktionen
    // was kann man bekommen: Artefakt, Titanen-Energiekerne, Palenium, Tronic
    // womit kann man bezahlen: Restyp1-4, neue Rohstoffe (1,3-12)

    //Belohnung
    $get = mt_rand(1, 100);
    if ($get <= 66) {
        //Artefakt
        //$not_allowed=array(11,12,19);
        $not_allowed = array(19);

        $artid = -1;

        while ($artid == -1) {
            $artid = mt_rand(1, count($ua_name));
            if (in_array($artid, $not_allowed)) {
                $artid = -1;
            }
        }

        $reward = array('A', $artid);
    } elseif ($get <= 77) {
        //Titanen-Energiekern
        $reward = array('I', 2 , 2);
    } elseif ($get <= 88) {
        //Palenium
        $reward = array('I', 1 , 500);
        $is_paleniuem = true;
    } else {
        //Tronic
        $reward = array('R', 5 , 25);
    }

    //////////////////////////////////////////////////////////////////////////////////
    //Kostenliste erstellen und aus dieser dann per Zufall eine auswählen
    //////////////////////////////////////////////////////////////////////////////////
    //die Maximalwerte aus der DB holen
    $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT MAX(restyp01) AS restyp01, MAX(restyp02) AS restyp02, MAX(restyp03) AS restyp03, MAX(restyp04) AS restyp04, MAX(credits) AS credits FROM de_user_data WHERE sector>1 AND npc=0");
    $restyp_x = mysqli_fetch_array($db_daten);

    //Standardrohstoffe
    for ($r = 1;$r <= 4;$r++) {
        $amount = $restyp_x['restyp0'.$r] / 5;
        if ($amount < 1000) {
            $amount = 1000;
        }
        $cost_list[] = array('R', $r, $amount);
    }

    //neue Rohstoffe 3-12
    if ($sv_deactivate_vsystems != 1 && $maxtick > 3000) {
        for ($r = 3;$r <= 12;$r++) {
            $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT MAX(item_amount) AS amount FROM de_user_storage WHERE item_id='".$r."'");
            $res = mysqli_fetch_array($db_daten);

            $amount = $res['amount'] / 5;
            if ($amount < 1000) {
                $amount = 1000;
            }
            $cost_list[] = array('I', $r, $amount);
        }
    }


    /*
    //Credits
    $amount = $restyp_x['credits'] / 10;
    if ($amount < 1000) {
        $amount = 1000;
    }

    if ($amount > 2000) {
        $amount = 1000;
    }

    $cost_list[] = array('C', 0, $amount);
    */

    //print_r($cost_list);
    //eine Wählen
    $cost = $cost_list[mt_rand(0, count($cost_list) - 1)];

    //in die DB einfügen
    $sql = "INSERT INTO de_auction SET 
	creator='".$uid."',
	start_wt='".$maxtick."',
	cost='".serialize($cost)."',
	reward='".serialize($reward)."'
	";

    error_log($sql, 0);

    mysqli_query($GLOBALS['dbi'], $sql);
}

function formatTime($seconds)
{

    $seconds = round($seconds);

    $days = floor($seconds / 60 / 60 / 24);
    $hours = floor($seconds / 60 / 60) % 24;
    $minutes = floor($seconds / 60) % 60;
    $seconds = ceil($seconds % 60);

    if ($days == 0) {
        $days = '';
    } else {
        $days = $days.':';
    }

    if ($hours == 0 && $days == 0) {
        $hours = '';
    } else {
        if ($hours < 10) {
            $hours = '0'.$hours;
        }
        $hours = $hours.':';
    }

    if ($minutes < 10) {
        $minutes = '0'.$minutes;
    }
    $minutes = $minutes.':';

    if ($seconds < 10) {
        $seconds = '0'.$seconds;
    }

    return $days.$hours.$minutes.$seconds;
}

function loadSpecialShip($user_id)
{
    //Schiffsdaten aus der DB holen
    $sql = "SELECT * FROM `de_user_special_ship` WHERE user_id='".$user_id."' LIMIT 1;";
    $db_data = mysqli_query($GLOBALS['dbi'], $sql);
    $num = mysqli_num_rows($db_data);
    if ($num == 1) {
        $row = mysqli_fetch_array($db_data);
        $data = unserialize($row['data']);

    } else {
        $data = new special_ship($user_id);

        $sql = "INSERT INTO `de_user_special_ship` SET user_id='".$user_id."', data='".serialize($data)."';";
        $db_data = mysqli_query($GLOBALS['dbi'], $sql);
    }

    return $data;
}

function saveSpecialShip($user_id, $ship)
{
    //Schiffsdaten aus der DB holen
    $ship_data = serialize($ship);

    $sql = "UPDATE `de_user_special_ship` SET data='$ship_data' WHERE user_id='".$user_id."' LIMIT 1;";
    //echo $sql;
    mysqli_query($GLOBALS['dbi'], $sql);
}


function calcMissionStorageCapacity($reward, $cost)
{
    $amount_reward = 0;
    $amount_cost = 0;
    for ($i = 0;$i < count($reward);$i++) {
        switch ($reward[$i][0]) {
            //Standardrohstoffe
            case 'R':
                $amount_reward += $reward[$i][2];
                break;
                //Items
            case 'I':
                $amount_reward += $reward[$i][2];
                break;
        }
    }

    for ($i = 0;$i < count($cost);$i++) {
        switch ($cost[$i][0]) {
            //Standardrohstoffe
            case 'R':
                $amount_cost += $cost[$i][2];
                break;
                //Items
            case 'I':
                $amount_cost += $cost[$i][2];
                break;
        }
    }

    $value = $amount_reward;
    if ($amount_cost > $amount_reward) {
        $value = $amount_cost;
    }

    return $value;
}

function hasMissionNeeds($cost)
{
    $has_all = true;

    for ($i = 0;$i < count($cost);$i++) {

        switch ($cost[$i][0]) {
            //Standardrohstoffe
            /*
            case 'R':
                $restyp=$cost[$i][1];
                $amount=floor($cost[$i][2]*$percent);

                $sql="UPDATE de_user_data SET restyp0".$restyp."=restyp0".$restyp."-'".$amount."' WHERE user_id='".$_SESSION['ums_user_id']."';";
                mysqli_query($GLOBALS['dbi'],$sql);
            break;
            */
            //Items
            case 'I':
                $item_id = $cost[$i][1];
                $amount = $cost[$i][2];

                //echo 'A: '.$GLOBALS['ps'][$item_id]['item_amount'].'/'.$amount.'/'.$item_id;
                if ($GLOBALS['ps'][$item_id]['item_amount'] < $amount) {
                    $has_all = false;
                }
                break;
        }
    }

    return $has_all;
}

function generateMissionReward($reward, $percent = 100)
{
    if ($percent > 100) {
        $percent = 100;
    }
    $percent = $percent / 100;
    $content = '';
    if (is_array($reward)) {
        for ($i = 0;$i < count($reward);$i++) {

            switch ($reward[$i][0]) {
                //Artefakt
                case 'A':
                    if ($reward[$i][1] == '?') {
                        $content .= 'zuf&auml;lliges Artefakt';
                    } else {
                        $content .= 'Folgendes Artefakt:'.$reward[$i][1];
                    }

                    break;

                    //Standardrohstoffe
                case 'R':
                    $restyp = $reward[$i][1];
                    $amount = number_format(floor($reward[$i][2] * $percent), 0, "", ".");
                    $resnamen = array('Multiplex','Dyharra','Iradium','Eternium','Tronic');
                    $resimages = array('icon1.png', 'icon2.png', 'icon3.png', 'icon4.png', 'icon5.png');

                    //Erfolgs-Nachricht
                    //$content.=$amount.' '.$resnamen[$restyp-1].'<br>';
                    $content .= '<img src="g/'.$resimages[$restyp - 1].'" style="height: 20px; width: auto; margin-bottom: -5px;" rel="tooltip" title="'.$resnamen[$restyp - 1].'"> <div style="display: inline-block; margin-bottom: 5px;">'.$amount.'</div><br>';

                    break;
                    //Items
                case 'I':
                    $item_id = $reward[$i][1];
                    $amount = number_format(floor($reward[$i][2] * $percent), 0, "", ".");

                    //Erfolgs-Nachricht, Design je nach Existenz einer passenden Bilddatei
                    if (file_exists('g/item'.$item_id.'.png')) {
                        $content .= '<img src="g/item'.$item_id.'.png" style="height: 20px; width: auto; margin-bottom: -5px;" rel="tooltip" title="'.$GLOBALS['ps'][$item_id]['item_name'].'"> <div style="display: inline-block; margin-bottom: 5px;">'.$amount.'</div><br>';
                    } else {
                        $content .= $amount.' '.$GLOBALS['ps'][$item_id]['item_name'].'<br>';
                    }


                    break;
            }
        }
    }

    return $content;
}

function substractMissionCost($cost, $percent)
{
    if ($percent > 100) {
        $percent = 100;
    }
    $percent = $percent / 100;
    $content = '';
    for ($i = 0;$i < count($cost);$i++) {

        switch ($cost[$i][0]) {
            //Standardrohstoffe
            case 'R':
                $restyp = $cost[$i][1];
                $amount = floor($cost[$i][2] * $percent);

                $sql = "UPDATE de_user_data SET restyp0".$restyp."=restyp0".$restyp."-'".$amount."' WHERE user_id='".$_SESSION['ums_user_id']."';";
                mysqli_query($GLOBALS['dbi'], $sql);
                break;
                //Items
            case 'I':
                $item_id = $cost[$i][1];
                $amount = $cost[$i][2] * -1;

                change_storage_amount($_SESSION['ums_user_id'], $item_id, $amount, false);
                break;
        }
    }
}

function generate_vsystem_kopfzeile($system_id, $sytem_name)
{
    //////////////////////////////////////////////////////////////
    //Wechselmöglichkeit erzeugen, zur niedrigeren/höheren ID
    //////////////////////////////////////////////////////////////
    $link_lower = '';
    $link_higher = '';

    //ggf. Tooltip für Hotkey anzeigen
    $tooltip_lower = '';
    $tooltip_higher = '';
    $tooltip_map = '';
    if ($_SESSION['ums_mobi'] != 1) {
        $tooltip_lower = ' title="Hotkeys: a, &larr;"';
        $tooltip_higher = ' title="Hotkeys: d, &rarr;"';
        $tooltip_map = ' title="Hotkeys: w, &uarr;"';

    }

    //erste
    $link_first = '';
    $sql = "SELECT MIN(map_id) AS map_id FROM de_user_map WHERE user_id='".$_SESSION['ums_user_id']."' LIMIT 1;";
    $db_daten = mysqli_query($GLOBALS['dbi'], $sql);
    $row = mysqli_fetch_array($db_daten);
    $id_first = $row['map_id'];
    if ($id_first > 0) {
        $link_first = '<a href="?id='.$id_first.'" style="display: inline-block; width: 40px; background-color: #FFFFFF; color: #000000; text-decoration: none; text-align: center; border: 1px solid #888888; box-sizing: border-box;">&lt;&lt;</a>';
    }

    //letzte
    $link_last = '';
    $sql = "SELECT MAX(map_id) AS map_id FROM de_user_map WHERE user_id='".$_SESSION['ums_user_id']."' LIMIT 1;";
    $db_daten = mysqli_query($GLOBALS['dbi'], $sql);
    $row = mysqli_fetch_array($db_daten);
    $id_last = $row['map_id'];
    if ($id_last > 0) {
        $link_last = '<a href="?id='.$id_last.'" style="display: inline-block; width: 40px; background-color: #FFFFFF; color: #000000; text-decoration: none; text-align: center; border: 1px solid #888888; box-sizing: border-box;">&gt;&gt;</a>';
    }

    //niedriger
    $sql = "SELECT map_id FROM de_user_map WHERE user_id='".$_SESSION['ums_user_id']."' AND map_id < '".$system_id."' ORDER BY map_id DESC LIMIT 1;";
    $db_daten = mysqli_query($GLOBALS['dbi'], $sql);
    $row = mysqli_fetch_array($db_daten);
    $id_lower = $row['map_id'] ?? -1;
    if ($id_lower > 0) {
        $link_lower = '<a id="link_lower" href="?id='.$id_lower.'" style="margin-left: 8px; display: inline-block; width: 40px; background-color: #FFFFFF; color: #000000; text-decoration: none; text-align: center; border: 1px solid #888888; box-sizing: border-box;"'.$tooltip_lower.'>&lt;</a>';
    }

    //höher
    $sql = "SELECT map_id FROM de_user_map WHERE user_id='".$_SESSION['ums_user_id']."' AND map_id > '".$system_id."' ORDER BY map_id ASC LIMIT 1;";
    $db_daten = mysqli_query($GLOBALS['dbi'], $sql);
    $row = mysqli_fetch_array($db_daten);
    $id_higher = $row['map_id'] ?? -1;
    if ($id_higher > 0) {
        $link_higher = '<a id="link_higher" href="?id='.$id_higher.'" style="margin-right: 8px; display: inline-block; width: 40px; background-color: #FFFFFF; color: #000000; text-decoration: none; text-align: center; border: 1px solid #888888; box-sizing: border-box;"'.$tooltip_higher.'>&gt;</a>';
    }

    //////////////////////////////////////////////////////////////
    // Kopfzeile
    //////////////////////////////////////////////////////////////
    // Kopfzeile zusammenbauen
    $kopfzeile = '';

    $kopfzeile .= '<div style="display: flex;">';
    $kopfzeile .= '<div style="width: 90px; text-align: left;">'.$link_first.$link_lower.'</div>';
    $kopfzeile .= '
	<div style="flex-grow: 1;">'.$sytem_name.' 
		(#<input id="input_system_id"type="text" style="height: 12px; width: 24px; text-align: center;" value="'.$system_id.'">&nbsp;
		<span style="display: inline-block; width: 30px; cursor: pointer; height: 18px; background-color: #FFFFFF; color: #000000; text-decoration: none; text-align: center; border: 1px solid #888888; box-sizing: border-box;" onclick="location.href=\'map_system.php?id=\'+$(\'#input_system_id\').val()">OK</span>)
		<a id="link_map" href="map_mobile.php#sysid'.$system_id.'" style="margin-right: 8px; display: inline-block; width: 40px; background-color: #FFFFFF; color: #000000; text-decoration: none; text-align: center; border: 1px solid #888888; box-sizing: border-box;"'.$tooltip_map.'>&there4;</a>
	</div>';



    $kopfzeile .= '<div style="width: 90px; text-align: right;">'.$link_higher.$link_last.'</div>';
    $kopfzeile .= '</div>';//close flex

    $kopfzeile .= '
	<script type="text/javascript">
		$("#input_system_id").keyup(function(event) {
			if (event.keyCode === 13) {
				location.href=\'map_system.php?id=\'+$(\'#input_system_id\').val();
			}
		});
	</script>
	';

    return $kopfzeile;
}

function changeCredits($uid, $amount, $reason)
{
    //zuerst auslesen wieviel man hat
    $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT credits FROM de_user_data WHERE user_id='$uid'");
    $row = mysqli_fetch_array($db_daten);
    $hascredits = $row["credits"];
    //wert in der db ändern
    $db_daten = mysqli_query($GLOBALS['dbi'], "UPDATE de_user_data SET credits=credits+'$amount' WHERE user_id='$uid'");

    //creditanzahl ändern
    $hascredits = $hascredits + $amount;

    //die creditausgabe im billing-logfile hinterlegen
    $datum = date("Y-m-d H:i:s", time());
    $ip = getenv("REMOTE_ADDR");
    $clog = "Zeit: $datum\nIP: $ip\n".$reason."- Neuer Creditstand: $hascredits ($amount)\n--------------------------------------\n";
    $fp = fopen("cache/creditlogs/$uid.txt", "a");
    fputs($fp, $clog);
    fclose($fp);
}


function setUserLoot($uid, $map_id, $field_id)
{
    mysqli_query($GLOBALS['dbi'], "INSERT INTO de_user_map_loot(user_id, map_id, field_id) VALUES ('$uid', '$map_id', '$field_id')");
}

function getUserLootByMapID($uid, $map_id)
{
    $sql = "SELECT * FROM de_user_map_loot WHERE user_id='$uid' AND map_id='$map_id';";
    //echo $sql;
    $data = array();
    $db_daten = mysqli_query($GLOBALS['dbi'], $sql);
    while ($row = mysqli_fetch_array($db_daten)) {
        $data[] = $row['field_id'];
    }

    return $data;
}

function getMapIDBySpecialsystemID($id)
{
    $sql = "SELECT id FROM de_map_objects WHERE system_typ=5 AND system_subtyp='$id'";
    $db_daten = mysqli_query($GLOBALS['dbi'], $sql);
    $row = mysqli_fetch_array($db_daten);

    return intval($row['id']);
}

function getUserSpecialsystemDataByMapID($uid, $map_id)
{
    $sql = "SELECT specialsystem_data FROM de_user_map WHERE user_id='$uid' AND map_id='$map_id';";
    $db_daten = mysqli_query($GLOBALS['dbi'], $sql);
    $row = mysqli_fetch_array($db_daten);
    if (!empty($row['specialsystem_data'])) {
        $data = unserialize($row['specialsystem_data']);
    } else {
        $data = array();
    }

    return $data;
}

function setUserSpecialsystemDataByMapID($uid, $map_id, $data)
{
    $data = serialize($data);
    $sql = "UPDATE de_user_map SET specialsystem_data='$data' WHERE user_id='$uid' AND map_id='$map_id';";
    mysqli_query($GLOBALS['dbi'], $sql);

    return $data;
}

function hasSpecialsystemNeeds($cost)
{
    $has_all = true;

    for ($i = 0;$i < count($cost);$i++) {

        switch ($cost[$i][0]) {
            //Einheit
            case 'U':
                $unit_typ = $cost[$i][1];

                switch ($unit_typ) {
                    case 'A':
                        $amount = $cost[$i][2];
                        if ($GLOBALS['pd']['agent'] < $amount) {
                            $has_all = false;
                        }
                        break;
                }

                break;

                //Item
            case 'I':
                $item_id = $cost[$i][1];
                $amount = $cost[$i][2];

                //echo 'A: '.$GLOBALS['ps'][$item_id]['item_amount'].'/'.$amount.'/'.$item_id;
                if ($GLOBALS['ps'][$item_id]['item_amount'] < $amount) {
                    $has_all = false;
                }
                break;
        }
    }

    return $has_all;
}

function substractSpecialsystemNeeds($cost)
{
    $has_all = true;

    for ($i = 0;$i < count($cost);$i++) {

        switch ($cost[$i][0]) {
            //Einheit
            case 'U':
                $unit_typ = $cost[$i][1];

                switch ($unit_typ) {
                    case 'A':
                        $amount = $cost[$i][2];

                        $sql = "UPDATE de_user_data SET agent=agent-$amount WHERE user_id='".$_SESSION['ums_user_id']."';";
                        write2agentlog($_SESSION['ums_user_id'], 'shaker', $amount);
                        //echo $sql;
                        mysqli_query($GLOBALS['dbi'], $sql);

                        break;
                }

                break;

                //Item
            case 'I':
                $item_id = $cost[$i][1];
                $amount = $cost[$i][2] * -1;

                change_storage_amount($_SESSION['ums_user_id'], $item_id, $amount, false);
                break;
        }
    }
}

function showSpecialsystemCost($cost)
{
    $content = '';
    for ($i = 0;$i < count($cost);$i++) {

        switch ($cost[$i][0]) {
            //Artefakt
            case 'A':
                if ($cost[$i][1] == '?') {
                    $content .= 'zuf&auml;lliges Artefakt';
                } else {
                    $content .= 'Folgendes Artefakt:'.$cost[$i][1];
                }

                break;

                //Standardrohstoffe
            case 'R':
                $restyp = $cost[$i][1];
                $amount = number_format(floor($cost[$i][2]), 0, "", ".");
                $resnamen = array('Multiplex','Dyharra','Iradium','Eternium','Tronic');

                $content .= $amount.' '.$resnamen[$restyp - 1].'<br>';
                break;

                //Einheit
            case 'U':
                $unit_typ = $cost[$i][1];

                switch ($unit_typ) {
                    case 'A':
                        $amount = number_format(floor($cost[$i][2]), 0, "", ".");
                        $content .= $amount.' Agenten (vorhanden: '.number_format($GLOBALS['pd']['agent'], 0, "", ".").')<br>';

                        break;
                }

                break;

                //Items
            case 'I':
                $item_id = $cost[$i][1];
                $amount = number_format(floor($cost[$i][2]), 0, "", ".");

                $content .= $amount.' '.$GLOBALS['ps'][$item_id]['item_name'].' (Lager: '.number_format($GLOBALS['ps'][$item_id]['item_amount'], 0, "", ".").')<br>';
                break;
        }
    }

    return $content;
}


function startFleetMission($fleet_id, $time, $mission_data)
{
    $sql = "UPDATE de_user_fleet SET aktion=4, mission_time='$time', mission_data='".serialize($mission_data)."' WHERE user_id='$fleet_id';";
    //echo $sql;
    mysqli_query($GLOBALS['dbi'], $sql);
}

function getFleetData($user_id)
{
    checkMissionEnd();

    //Flotten auslesen
    $sql = "SELECT * FROM de_user_fleet WHERE user_id LIKE '".$user_id."-%' ORDER BY user_id ASC;";
    $db_daten = mysqli_query($GLOBALS['dbi'], $sql);

    while ($row = mysqli_fetch_array($db_daten)) {
        $data[] = $row;
    }

    return $data;
}

function getFleetFK($user_id)
{
    global $sv_anz_schiffe;

    $rasse = getRasseByUID($user_id);

    //Flotten auslesen
    $sql = "SELECT * FROM de_user_fleet WHERE user_id LIKE '".$user_id."-%' ORDER BY user_id ASC;";
    //echo $sql;
    $db_daten = mysqli_query($GLOBALS['dbi'], $sql);

    while ($row = mysqli_fetch_array($db_daten)) {
        $fk = 0;
        //jedes Schiff der Flotte auf Frachtkapazität überpüfen
        for ($i = 0;$i < $sv_anz_schiffe;$i++) {
            if (isset($GLOBALS['unit'][$rasse - 1][$i]['fk'])) {
                $fk += $row['e'.($i + 81)] * $GLOBALS['unit'][$rasse - 1][$i]['fk'];
            }

            //echo '<br>'.$i.': '.$GLOBALS['unit'][$rasse-1][$i]['fk'];

        }

        $data[] = $fk;
    }

    return $data;

}

function getRasseByUID($user_id)
{
    $sql = "SELECT rasse FROM de_user_data WHERE user_id ='$user_id';";
    $db_daten = mysqli_query($GLOBALS['dbi'], $sql);

    $row = mysqli_fetch_array($db_daten);

    return $row['rasse'];
}

function checkMissionEnd()
{
    $sql = "UPDATE de_user_fleet SET aktion=0, mission_data='' WHERE aktion=4 AND mission_time<'".time()."';";
    //echo $sql;
    mysqli_query($GLOBALS['dbi'], $sql);
}

function setBldgByFieldID($user_id, $map_id, $field_id, $bldg_id, $bldg_level, $bldg_time)
{

    $sql = "
	INSERT INTO de_user_map_bldg (
		user_id,
		map_id,
		field_id,
		bldg_id,
		bldg_level,
		bldg_time
	)VALUES(
		'".$user_id."',
		'".$map_id."',
		'".$field_id."',
		'".$bldg_id."',
		'".$bldg_level."',
		'".$bldg_time."'
	) ON DUPLICATE KEY UPDATE 
		bldg_id='".$bldg_id."',
		bldg_level='".$bldg_level."',
		bldg_time='".$bldg_time."'
	";

    //echo $sql;

    mysqli_query($GLOBALS['dbi'], $sql);
}

function removeBldgByFieldID($user_id, $map_id, $field_id)
{
    $sql = "DELETE FROM de_user_map_bldg WHERE user_id='".$user_id."' AND map_id='".$map_id."' AND field_id='".$field_id."';";

    //echo $sql;

    mysqli_query($GLOBALS['dbi'], $sql);
}

function getBldgByFieldID($pb, $field_id)
{
    $data['bldg_id'] = -1;
    $data['bldg_level'] = -1;

    for ($i = 0;$i < count($pb);$i++) {
        //gibt es auf dem Feld ein Gebäude?
        if ($pb[$i]['field_id'] == $field_id) {
            //ist das Gebäude bereits fertig, oder noch im Bau?
            if ($pb[$i]['bldg_time'] <= time()) {
                //fertig
                $data['bldg_id'] = $pb[$i]['bldg_id'];
                $data['bldg_level'] = $pb[$i]['bldg_level'];
            } else {
                //noch nicht fertig, daher ggf. den Gebäudelevel um eins verringern
                if ($pb[$i]['bldg_level'] > 1) {
                    $data['bldg_id'] = $pb[$i]['bldg_id'];
                    $data['bldg_level'] = $pb[$i]['bldg_level'] - 1;
                }
            }
        }
    }

    return $data;
}

function loadPlayerBuildings($uid, $map_id)
{
    $data = array();
    $sql = "SELECT * FROM de_user_map_bldg WHERE user_id='$uid' AND map_id='$map_id';";
    $db_daten = mysqli_query($GLOBALS['dbi'], $sql);
    while ($row = mysqli_fetch_array($db_daten)) {
        $data[] = $row;
    }

    return $data;
}

function loadPlayerStorage($uid)
{
    $data = array();

    //$sql="SELECT * FROM de_user_storage LEFT JOIN de_item_data ON(de_user_storage.item_id=de_item_data.item_id) WHERE de_user_storage.user_id='".$uid."';";
    //Namen auslesen
    $sql = "SELECT * FROM de_item_data ORDER BY item_id;";
    $db_daten = mysqli_query($GLOBALS['dbi'], $sql);
    while ($row = mysqli_fetch_array($db_daten)) {
        $data[$row['item_id']]['item_name'] = $row['item_name'];
        $data[$row['item_id']]['item_amount'] = 0;
        $data[$row['item_id']]['item_blueprint'] = $row['item_blueprint'];
    }

    //Anzahl auslesen
    $sql = "SELECT * FROM de_user_storage WHERE user_id='".$uid."';";
    $db_daten = mysqli_query($GLOBALS['dbi'], $sql);
    while ($row = mysqli_fetch_array($db_daten)) {
        $data[$row['item_id']]['item_amount'] = $row['item_amount'];
    }

    return $data;
}

function loadAllyStorage($ally_id)
{
    $data = array();

    //$sql="SELECT * FROM de_user_storage LEFT JOIN de_item_data ON(de_user_storage.item_id=de_item_data.item_id) WHERE de_user_storage.user_id='".$uid."';";
    //Namen auslesen
    $sql = "SELECT * FROM de_item_data ORDER BY item_id;";
    $db_daten = mysqli_query($GLOBALS['dbi'], $sql);
    while ($row = mysqli_fetch_array($db_daten)) {
        $data[$row['item_id']]['item_name'] = $row['item_name'];
        $data[$row['item_id']]['item_amount'] = 0;
        $data[$row['item_id']]['item_blueprint'] = $row['item_blueprint'];
    }

    //Anzahl auslesen
    $sql = "SELECT * FROM de_ally_storage WHERE ally_id='".$ally_id."';";
    $db_daten = mysqli_query($GLOBALS['dbi'], $sql);
    while ($row = mysqli_fetch_array($db_daten)) {
        $data[$row['item_id']]['item_amount'] = $row['item_amount'];
    }

    return $data;
}

function get_storage_amount($uid, $item_id)
{
    //überprüfen ob es bereits einen datensatz mit der rohstoff-id gibt
    $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT * FROM `de_user_storage` WHERE user_id='$uid' AND item_id='$item_id'");
    $num = mysqli_num_rows($db_daten);
    if ($num == 1) { //es gibt einen datensatz
        //datensatz auslesen
        $row = mysqli_fetch_array($db_daten);
        $return = $row["item_amount"];
    } else { //es gibt keinen datensatz
        $return = 0;
    }
    return($return);
}

//lagerkomplexbestand �ndern
//function change_systemhold_amount($owner_id, $uid, $restyp, $amount)
function change_storage_amount($uid, $item_id, $item_amount, $is_wt = false)
{
    //überprüfen ob es bereits einen datensatz mit der rohstoff-id gibt
    $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT * FROM `de_user_storage` WHERE user_id='$uid' AND item_id='$item_id'");
    $num = mysqli_num_rows($db_daten);
    if ($num == 0) { //es gibt noch keinen datensatz

        //neuen datensatz anelgen
        if (!$is_wt) {
            mysqli_query($GLOBALS['dbi'], "INSERT INTO de_user_storage (user_id, item_id, item_amount) VALUES ('$uid', '$item_id', '$item_amount')");
        } else {
            mysqli_query($GLOBALS['dbi'], "INSERT INTO de_user_storage (user_id, item_id, item_amount, item_wt_change) VALUES ('$uid', '$item_id', '$item_amount', $item_amount)");
        }

    } else { //es gibt bereits einen datensatz
        //db updaten
        if (!$is_wt) {
            mysqli_query($GLOBALS['dbi'], "UPDATE de_user_storage SET item_amount=item_amount+'$item_amount' WHERE user_id='$uid' AND item_id='$item_id'");
        } else {
            mysqli_query($GLOBALS['dbi'], "UPDATE de_user_storage SET item_amount=item_amount+'$item_amount', item_wt_change='$item_amount' WHERE user_id='$uid' AND item_id='$item_id'");
        }
    }
}

function changeAllyStorageAmount($ally_id, $item_id, $item_amount, $is_wt = false)
{
    //überprüfen ob es bereits einen datensatz mit der rohstoff-id gibt
    $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT * FROM `de_ally_storage` WHERE ally_id='$ally_id' AND item_id='$item_id'");
    $num = mysqli_num_rows($db_daten);
    if ($num == 0) { //es gibt noch keinen datensatz

        //neuen datensatz anelgen
        if (!$is_wt) {
            mysqli_query($GLOBALS['dbi'], "INSERT INTO de_ally_storage (ally_id, item_id, item_amount) VALUES ('$ally_id', '$item_id', '$item_amount')");
        } else {
            mysqli_query($GLOBALS['dbi'], "INSERT INTO de_ally_storage (ally_id, item_id, item_amount, item_wt_change) VALUES ('$ally_id', '$item_id', '$item_amount', $item_amount)");
        }

    } else { //es gibt bereits einen datensatz
        //db updaten
        if (!$is_wt) {
            mysqli_query($GLOBALS['dbi'], "UPDATE de_ally_storage SET item_amount=item_amount+'$item_amount' WHERE ally_id='$ally_id' AND item_id='$item_id'");
        } else {
            mysqli_query($GLOBALS['dbi'], "UPDATE de_ally_storage SET item_amount=item_amount+'$item_amount', item_wt_change='$item_amount' WHERE ally_id='$ally_id' AND item_id='$item_id'");
        }
    }
}

function loadPlayerTechs($uid)
{
    $data = array();
    if ($uid > 0) {
        $sql = "SELECT * FROM de_user_techs WHERE user_id='$uid';";
        $db_daten = mysqli_query($GLOBALS['dbi'], $sql);
        while ($row = mysqli_fetch_array($db_daten)) {
            $data[$row['tech_id']]['time_finished'] = $row['time_finished'];
        }

        //die Daten um den NPC-Status erweitern
        $sql = "SELECT npc FROM de_user_data WHERE user_id='$uid';";
        $db_daten = mysqli_query($GLOBALS['dbi'], $sql);
        $row = mysqli_fetch_array($db_daten);
        $data['npc'] = $row['npc'];
    }

    return $data;
}

function hasTech($pt, $tech_id)
{
    global $sv_comserver_roundtyp;

    //Test auf Comserver-BR
    if ($sv_comserver_roundtyp == 1 || $pt['npc'] == 2) {
        return true;
    }

    if (isset($pt[$tech_id]) && $pt[$tech_id]['time_finished'] <= time()) {
        return true;
    } else {
        return false;
    }
}

function loadPlayerData($uid)
{
    $data = array();
    if ($uid > 0) {
        $sql = "SELECT *, de_user_data.status AS ally_status FROM de_user_data LEFT JOIN de_login ON (de_login.user_id=de_user_data.user_id) WHERE de_login.user_id='$uid';";
        $db_daten = mysqli_query($GLOBALS['dbi'], $sql);
        $num = mysqli_num_rows($db_daten);
        if ($num == 1) {
            $data = mysqli_fetch_array($db_daten);
        }
    }

    return $data;
}

function sf($name, $arrOpt, $selected, $class = "", $jsHandler = '')
{
    $field = "<select name=\"$name\" id=\"$name\" class=\"$class\" $jsHandler>\n";
    foreach ($arrOpt as $val => $show) {
        $field .= "  <option value=\"$val\" ";
        if ($val == $selected) {
            $field .= "selected=\"selected\"";
        }
        $field .= ">$show</option>\n";
    }
    $field .= "</select>\n";

    return $field;
}

function showeinheit_ang($techname, $tech_id, $rt01, $rt02, $rt03, $rt04, $rt05, $tech_ticks, $vorhanden, $bg, $tooltipid, $has_tech, $item_kosten = '')
{
    global $ums_gpfad, $ums_rasse, $tooltips;

    $tooltip = '<img src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="'.$tooltips[$tooltipid].'">';

    echo '<tr valign="middle" align="center" height="25">';
    //echo '<td width="115" height="25" bgcolor="'.$bg.'"><div align="left"><b><font color="white">&nbsp;</font></b><a href="help.php?SID='.$SID.'&t='.$tech_id.'"><FONT COLOR="#FFFFFF" style="font-size:8pt">'.$techname."</div></td>";
    echo '<td class="'.$bg.'" height="25"><div align="left">&nbsp;'.$tooltip.'<b>&nbsp;&nbsp;</b>'.$techname.$item_kosten.'</div></td>';
    echo '<td class="'.$bg.'">'.number_format($rt01, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.number_format($rt02, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.number_format($rt03, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.number_format($rt04, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.number_format($rt05, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.$tech_ticks."</td>";
    if ($tech_id == 111) {
        $va = 'id="va"';
    } else {
        $va = '';
    }
    echo '<td class="'.$bg.'" '.$va.'>'.$vorhanden."</td>";
    //hat er die Technologie?
    if ($has_tech) {
        echo '<td class="'.$bg.'"><input type="text" name="b'.$tech_id.'" id="b'.$tech_id.'" value="" size="3" maxlength="9" onKeyUp="berechnepreise();"></td>';
    } else {
        echo '<td class="'.$bg.'">fehlende<br>Technologie</td>';
    }
    echo '</tr>';
}

function getTechNameByRasse($names, $rasse)
{
    $tech_names = explode(";", $names);
    return $tech_names[$rasse - 1];
}


function formatMasseinheit($number, $precision = 2)
{
    $units = array('', 'K', 'M', 'G', 'T');

    $number = max($number, 0);
    $pow = floor(($number ? log($number) : 0) / log(1000));
    $pow = min($pow, count($units) - 1);

    // Uncomment one of the following alternatives
    $number /= pow(1000, $pow);
    // $number /= (1 << (10 * $pow));

    $result = round($number, $precision) . '' . $units[$pow];
    $result = str_replace('.', ',', $result);

    return $result;
}

function array_msort($array, $cols)
{
    $colarr = array();
    foreach ($cols as $col => $order) {
        $colarr[$col] = array();
        foreach ($array as $k => $row) {
            $colarr[$col]['_'.$k] = strtolower($row[$col]);
        }
    }
    $eval = 'array_multisort(';
    foreach ($cols as $col => $order) {
        $eval .= '$colarr[\''.$col.'\'],'.$order.',';
    }
    $eval = substr($eval, 0, -1).');';
    eval($eval);
    $ret = array();
    foreach ($colarr as $col => $arr) {
        foreach ($arr as $k => $v) {
            $k = substr($k, 1);
            if (!isset($ret[$k])) {
                $ret[$k] = $array[$k];
            }
            $ret[$k][$col] = $array[$k][$col];
        }
    }
    return $ret;

}

function array_orderby()
{
    //$sorted = array_orderby($data, 'volume', SORT_DESC, 'edition', SORT_ASC);
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row) {
                $tmp[$key] = $row[$field];
            }
            $args[$n] = $tmp;
        }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}

function SecureValue($value)
{
    $value = htmlspecialchars(stripslashes($value), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
    // mysqli_real_escape_string no longer needed with prepared statements

    return ($value);
}

function median($zahlen_array = array())
{
    $anzahl = count($zahlen_array);
    if ($anzahl == 0) {
        return false;
    }
    sort($zahlen_array);
    if ($anzahl % 2 == 0) {
        //gerade Anzahl => der Median ist das arithmetische Mittel der beiden mittleren Zahlen
        return ($zahlen_array[ ($anzahl / 2) - 1 ] + $zahlen_array[ $anzahl / 2 ]) / 2 ;
    } else {
        //ungerade Anzahl => der mittlere Wert ist der Median
        return $zahlen_array[$anzahl / 2];
    }
}

function get_allyid_partner($allyid)
{
    $allyidpartner = 0;

    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_ally_partner WHERE ally_id_1=? OR ally_id_2=?", [$allyid, $allyid]);
    $num = mysqli_num_rows($db_daten);

    if ($num == 1) {
        $row = mysqli_fetch_array($db_daten);
        if ($row['ally_id_1'] == $allyid) {
            $allyidpartner = $row['ally_id_2'];
        } else {
            $allyidpartner = $row['ally_id_1'];
        }
    }

    return($allyidpartner);
}

function checkForKriegsgegner($allyid1, $allyid2)
{
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_ally_war WHERE 
		(ally_id_angreifer=? AND ally_id_angegriffener=?) OR 
		(ally_id_angegriffener=? AND ally_id_angreifer=?)", [$allyid1, $allyid2, $allyid1, $allyid2]);
    $num = mysqli_num_rows($db_daten);

    if ($num > 0) {
        return true;
    } else {
        return false;
    }
}

function checkForCounter($atter_uid, $target_uid)
{
    //Counter möglich wenn das Ziel Angriffsflotten draußen hat, gilt nicht bei Angriffen auf Aliens
    $player_att = false;

    if ($player_att == false) {
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_fleet WHERE user_id=? AND aktion=1", [$target_uid."-1"]);
        $num = mysqli_num_rows($db_daten);
        if ($num > 0) {
            $row = mysqli_fetch_array($db_daten);
            if ($row['zielsec'] > 0) {
                if (checkForNPCbyKoord($row['zielsec'], $row['zielsys']) == false) {
                    //echo '<br>'.$row['zielsec'].'/'.$row['zielsys'];
                    $player_att = true;
                }
            }
        }
    }

    if ($player_att == false) {
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_fleet WHERE user_id=? AND aktion=1", [$target_uid."-2"]);
        $num = mysqli_num_rows($db_daten);
        if ($num > 0) {
            $row = mysqli_fetch_array($db_daten);
            if ($row['zielsec'] > 0) {
                if (checkForNPCbyKoord($row['zielsec'], $row['zielsys']) == false) {
                    //echo '<br>'.$row['zielsec'].'/'.$row['zielsys'];
                    $player_att = true;
                }
            }
        }
    }

    if ($player_att == false) {
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_fleet WHERE user_id=? AND aktion=1", [$target_uid."-3"]);
        $num = mysqli_num_rows($db_daten);
        if ($num > 0) {
            $row = mysqli_fetch_array($db_daten);
            if ($row['zielsec'] > 0) {
                if (checkForNPCbyKoord($row['zielsec'], $row['zielsys']) == false) {
                    //echo '<br>'.$row['zielsec'].'/'.$row['zielsys'];
                    $player_att = true;
                }
            }
        }
    }

    return $player_att;
}

function checkForNPCbyKoord($sector, $system)
{
    //ist an den Koordinaten ein NPC?
    $npc = false;
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT npc FROM de_user_data WHERE sector=? AND `system`=?", [$sector, $system]);
    $num = mysqli_num_rows($db_daten);
    if ($num > 0) {
        $row = mysqli_fetch_array($db_daten);
        if ($row['npc'] == 1) {
            $npc = true;
        }
    }

    return $npc;
}

function get_allybldg($allyid)
{
    for ($i = 0;$i <= 30;$i++) {
        $bldg[$i] = 0;
    }

    if ($allyid > 0) {
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_allys WHERE id=?", [$allyid]);
        $row = mysqli_fetch_array($db_daten);
        for ($i = 0;$i <= 30;$i++) {
            if (isset($row["bldg$i"]) && $row["bldg$i"] != '') {
                $bldg[$i] = $row["bldg$i"];
            }
        }
    }
    return($bldg);
}

function get_free_artefact_places($user_id)
{
    //gebäudestufe und test auf geb�ude
    $pt = loadPlayerTechs($user_id);
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT artbldglevel FROM de_user_data WHERE user_id=?", [$user_id]);
    $row = mysqli_fetch_assoc($db_daten);
    $artbldglevel = $row["artbldglevel"];

    //allianz-bonus
    $allyid = $allyid = get_player_allyid($user_id);
    $ally_geb_bonus = 0;
    if ($allyid > 0) {
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_allys WHERE id=?", [$allyid]);
        $row = mysqli_fetch_array($db_daten);
        $ally_geb_bonus = $row['bldg5'];
    }

    //wenn das Geb�ude nicht vorhanden ist, dann ist der Wert generell 0
    if (!hasTech($pt, 28)) {
        $artbldglevel = 0;
        $ally_geb_bonus = 0;
    }

    //artefakte vorhanden
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT id FROM de_user_artefact WHERE user_id=?", [$user_id]);
    $num = mysqli_num_rows($db_daten);
    $freeartefactplaces = $artbldglevel + $ally_geb_bonus - $num;

    return($freeartefactplaces);
}

function getArtefactAmountByUserId($user_id, $artefact_id)
{
    $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT COUNT(user_id) AS amount FROM de_user_artefact WHERE user_id='$user_id' AND id='$artefact_id'");
    $row = mysqli_fetch_array($db_daten);
    return($row['amount']);
}

function get_col_amount($user_id)
{
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT col FROM de_user_data WHERE user_id=?", [$user_id]);
    $row = mysqli_fetch_array($db_daten);
    return($row['col']);
}

function get_user_id_from_coord($zsec, $zsys)
{
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE sector=? AND `system`=?", [$zsec, $zsys]);
    $row = mysqli_fetch_array($db_daten);
    return($row['user_id']);
}

function get_player_allyid($user_id)
{
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT allytag, status FROM de_user_data WHERE user_id=?", [$user_id]);
    $row = mysqli_fetch_array($db_daten);
    $allytag = $row['allytag'];
    $allystatus = $row['status'];
    //�berpr�fen ob man in einer allianz ist
    if ($allytag != '' and $allystatus == 1) {
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT id FROM de_allys WHERE allytag=?", [$allytag]);
        $row = mysqli_fetch_array($db_daten);
        $allyid = $row['id'];
        return($allyid);
    }
    return(0);
}

function getAllytagByAllyid($id)
{
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT allytag FROM de_allys WHERE id=?", [$id]);
    $row = mysqli_fetch_array($db_daten);

    return $row['allytag'];
}

function getAllyByID($ally_id)
{
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_allys WHERE id=?", [$ally_id]);
    $row = mysqli_fetch_array($db_daten);
    return $row;
}

function getAllyIDByAllytag($allytag)
{
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT id FROM de_allys WHERE allytag=?", [$allytag]);
    $row = mysqli_fetch_array($db_daten);

    return $row['id'];
}


function getAllyBGScore($ally_id, $bg)
{
    $sql = "SELECT bgscore".$bg." AS value FROM de_allys WHERE id='$ally_id'";
    $db_data = mysqli_query($GLOBALS['dbi'], $sql);
    $row = mysqli_fetch_array($db_data);


    return $row['value'];
}

function insert_chat_msg($channel, $channeltyp, $spielername, $chat_message)
{
    global $sv_server_tag;

    $spielername = htmlspecialchars($spielername);

    $owner_id = $_SESSION["ums_owner_id"] ?? 0;
    if (empty($owner_id)) {
        $owner_id = 0;
    }

    $time = time();

    if ($channeltyp == 3) {//gloabler Chat
        $sql="INSERT INTO de_chat_msg (channel, channeltyp, server_tag, spielername, message, timestamp, owner_id) VALUES 
		('$channel', '$channeltyp', '$sv_server_tag', ?, ? '$time', '$owner_id')";
        mysqli_execute_query($GLOBALS['dbi_ls'], $sql, [$spielername, $chat_message]);
    } else {
        //die verschiedenen Chats auf dem Server
        $sql="INSERT INTO de_chat_msg (channel, channeltyp, spielername, message, timestamp, owner_id) VALUES 
		('$channel', '$channeltyp', ?, ?, '$time', '$owner_id')";

        mysqli_execute_query($GLOBALS['dbi'], $sql, [$spielername, $chat_message]);
    }

    ////////////////////////////////////////////////////////////
    //DISCORD
    ////////////////////////////////////////////////////////////
    $webhooks = $GLOBALS['webhooks'];
    $webhook = array();

    //Global
    if ($channeltyp == 3) {
        $webhook[] = $webhooks['global'];
        $message = $sv_server_tag.' '.$spielername.': '.$chat_message;
    }

    //Allgemeiner Serverchat
    if ($channeltyp == 2) {
        $webhook[] = $webhooks[$sv_server_tag];
        $message = $spielername.': '.$chat_message;
    }

    //Allianz-Chat
    if ($channeltyp == 1) {
        $message = $spielername.': '.$chat_message;

        $ally_id = $channel;
        //überprüfen ob man einen Discord-Bot hinterlegt hat
        $ally_data = getAllyByID($ally_id);
        if (!empty($ally_data['discord_bot'])) {
            $webhook[] = 'https://discordapp.com/api/webhooks/'.$ally_data['discord_bot'];
        }

        //hat die Partner-Allianz einen Bot hinterlegt, dann dort auch posten
        $ally_id_partner = get_allyid_partner($ally_id);
        if ($ally_id_partner > 0) {
            $ally_data = getAllyByID($ally_id_partner);
            if (!empty($ally_data['discord_bot'])) {
                $webhook[] = 'https://discordapp.com/api/webhooks/'.$ally_data['discord_bot'];
            }
        }
    }

    if (count($webhook) > 0) {

        foreach ($webhook as $webhook_send) {
            $message = html_entity_decode($message);
            $message = strip_tags($message);

            $data = array("content" => $message, "username" => "Der Reporter");
            $curl = curl_init($webhook_send);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);


            if (curl_exec($curl) === false) {
                echo 'Curl-Fehler: ' . curl_error($curl);
            } else {
                //echo 'Operation ohne Fehler vollständig ausgeführt';
            }
        }
    }
}

function insert_chat_msg_admin($channel, $channeltyp, $spielername, $chat_message, $owner_id, $server_tag)
{
    $time = time();

    if ($channeltyp == 3) {//gloabler Chat
        mysqli_query($GLOBALS['dbi_ls'], "INSERT INTO de_chat_msg (channel, channeltyp, server_tag, spielername, message, timestamp, owner_id) VALUES 
		('$channel', '$channeltyp', '$server_tag', '$spielername', '$chat_message', '$time', '$owner_id')");
    } else {
        mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_chat_msg (channel, channeltyp, spielername, message, timestamp, owner_id) VALUES (?, ?, ?, ?, ?, ?)", [$channel, $channeltyp, $spielername, $chat_message, $time, $owner_id]);
    }
}

function validDigit($digit)
{
    return is_int($digit);
}

function getfleetlevel($exp)
{
    $counter = 0;
    for ($i = 0;$i < 24;$i++) {
        $counter = ($i * ($i - 1) * 30000) + 30000;
        if ($i == 0) {
            $counter = 0;
        }
        if ($exp < $counter) {
            break;
        }
    }
    $i = 24 - $i;
    return($i + 1);
}

function showtech($techname, $gebnr, $rt01, $rt02, $rt03, $rt04, $rt05, $buildgtime, $buildgnr, $techs, $typ, $verbtime, $bg, $cancancel)
{
    global $ums_rasse, $functions, $_SESSION;

    echo '<tr valign="middle" align="center" height="25">';
    echo '<td class="'.$bg.'" height="25"><div align="left"><b>&nbsp;</b><a href="help.php?t='.$gebnr.'" class="link">'.$techname."</a></div></td>";
    echo '<td class="'.$bg.'">'.number_format($rt01, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.number_format($rt02, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.number_format($rt03, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.number_format($rt04, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.number_format($rt05, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.$buildgtime."</td>";
    if ($buildgnr == $gebnr) {  //wird es gerade gebaut/geforscht?
        if ($typ == 0) { //Geb�ude
            if ($verbtime <= 1) {
                echo  '<td class="'.$bg.'">'.$functions['imbau'].' ('.$verbtime.')</td>';
            } else {
                echo '<td class="'.$bg.'">'.$functions['imbau'].' ('.$verbtime.')
			 <a href="buildings.php?cancel='.$gebnr.'" onclick="return confirm(unescape(\'Soll der Geb�udebau wirklich abgebrochen werden? Die Rohstoffkosten werden erstattet.\'))" class="btn2" style="margin: 4px; display: inline-block;">Abbruch</a></td>';
            }
        } elseif ($typ == 1) { //Forschungen
            if ($verbtime <= 1) {
                echo  '<td class="'.$bg.'">'.$functions['wirderforscht'].' ('.$verbtime.')</td>';
            } else {
                echo '<td class="'.$bg.'">'.$functions['wirderforscht'].' ('.$verbtime.')
			 <a href="research.php?cancel='.$gebnr.'" onclick="return confirm(unescape(\'Soll die Forschung wirklich abgebrochen werden? Die Rohstoffkosten werden erstattet.\'))" class="btn2" style="margin: 4px; display: inline-block;">Abbruch</a></td>';
            }
        } else { //Sektorgeb�ude
            echo  '<td class="'.$bg.'">'.$functions['imbau'].' ('.$verbtime.')</td>';
        }
    } elseif ($techs[$gebnr] == 1) { //schon gebaut/geforscht
        if ($typ == 0) {
            echo '<td class="'.$bg.'">'.$functions['gebaut'].'</td>';
        } elseif ($typ == 1) {
            echo '<td class="'.$bg.'">'.$functions['erforscht'].'</td>';
        } else {
            echo '<td class="'.$bg.'">'.$functions['gebaut'].'</td>';
        }
    } elseif ($buildgnr > 0) {//es l�uft bereits etwas, keine freien Kapazitäten
        echo '<td class="'.$bg.'">'.$functions['ausgelastet'].'</td>';
    } else { //es ist erforschbar/baubar
        //if ($typ==0)echo  '<td class="'.$bg.'"><input type="Submit" name="i'.$gebnr.'" value="'.$functions[bauen].'"></td>';
        //else echo '<td class="'.$bg.'"><input type="Submit" name="i'.$gebnr.'" value="'.$functions[forschen].'"></td>';
        if ($typ == 0) {
            echo '<td class="'.$bg.'"><a href="buildings.php?ida='.$gebnr.'&st='.$_SESSION['build_sec_token'].'" class="btn2">'.$functions['bauen'].'</a></td>';
        } elseif ($typ == 1) {
            echo '<td class="'.$bg.'"><a href="research.php?ida='.$gebnr.'&st='.$_SESSION['research_sec_token'].'" class="btn2">'.$functions['forschen'].'</a></td>';
        } else {
            echo '<td class="'.$bg.'"><a href="bkmenu.php?ida='.$gebnr.'" class="btn2">'.$functions['bauen'].'</a></td>';
        }
    }
    echo "</tr>";
}

function showtech2($techname, $gebnr, $rt01, $rt02, $rt03, $rt04, $rt05, $buildgtime, $buildgnr, $techs, $typ, $verbtime, $bg, $cancancel)
{
    global $ums_rasse, $functions, $sv_link, $ums_gpfad;

    $tooltip = '<img src="'.$ums_gpfad.'g/t/'.$ums_rasse.'_'.$gebnr.'.jpg" border="0">';

    echo '<tr valign="middle" align="center" height="25">';

    echo '<td class="'.$bg.'" align="left">';

    echo '
	<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="75"><a href="'.$sv_link[0].'?r='.$ums_rasse.'&t='.$gebnr.'" target="_blank">'.$tooltip.'</a></td>
		<td width="145" align="center"><a href="help.php?t='.$gebnr.'" class="link">'.$techname.'</a></td>
	</tr>
	</table>';
    echo '</td>';

    //echo '<td class="'.$bg.'" height="25"><div align="left"><b>&nbsp;</b><a href="help.php?t='.$gebnr.'" class="link">'.$techname."</a></div></td>";
    echo '<td class="'.$bg.'">'.number_format($rt01, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.number_format($rt02, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.number_format($rt03, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.number_format($rt04, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.number_format($rt05, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.$buildgtime."</td>";
    if ($buildgnr == $gebnr) {  //wird es gerade gebaut?
        if ($typ == 0) { //Geb�ude
            if ($verbtime <= 1) {
                echo  '<td class="'.$bg.'">'.$functions[imbau].' ('.$verbtime.')</td>';
            } else {
                echo '<td class="'.$bg.'">'.$functions[imbau].' ('.$verbtime.')
			 <a href="buildings.php?cancel='.$gebnr.'" onclick="return confirm(unescape(\'Soll der Geb�udebau wirklich abgebrochen werden? Die Rohstoffkosten werden erstattet.\'))" class="btn2" style="margin: 4px; display: inline-block;">Abbruch</a></td>';
            }
        } elseif ($typ == 1) { //Forschungen
            if ($verbtime <= 1) {
                echo  '<td class="'.$bg.'">'.$functions[wirderforscht].' ('.$verbtime.')</td>';
            } else {
                echo '<td class="'.$bg.'">'.$functions[wirderforscht].' ('.$verbtime.')
			 <a href="research.php?cancel='.$gebnr.'" onclick="return confirm(unescape(\'Soll die Forschung wirklich abgebrochen werden? Die Rohstoffkosten werden erstattet.\'))" class="btn2" style="margin: 4px; display: inline-block;">Abbruch</a></td>';
            }
        } else { //Sektorgeb�ude
            echo  '<td class="'.$bg.'">'.$functions[imbau].' ('.$verbtime.')</td>';
        }
    } elseif //wenn nein, dann
    ($techs[$gebnr] == 1) { //schon fertig?
        if ($typ == 0) {
            echo '<td class="'.$bg.'">'.$functions[gebaut].'</td>';
        } elseif ($typ == 1) {
            echo '<td class="'.$bg.'">'.$functions[erforscht].'</td>';
        } else {
            echo '<td class="'.$bg.'">'.$functions[gebaut].'</td>';
        }
    } elseif ($buildgnr > 0) {
        echo '<td class="'.$bg.'">'.$functions[ausgelastet].'</td>';
    } else {
        //if ($typ==0)echo  '<td class="'.$bg.'"><input type="Submit" name="i'.$gebnr.'" value="'.$functions[bauen].'"></td>';
        //else echo '<td class="'.$bg.'"><input type="Submit" name="i'.$gebnr.'" value="'.$functions[forschen].'"></td>';
        if ($typ == 0) {
            echo '<td class="'.$bg.'"><a href="buildings.php?ida='.$gebnr.'&st='.$_SESSION['build_sec_token'].'" class="btn2">'.$functions['bauen'].'</a></td>';
        } else {
            echo '<td class="'.$bg.'"><a href="research.php?ida='.$gebnr.'&st='.$_SESSION['research_sec_token'].'" class="btn2">'.$functions['forschen'].'</a></td>';
        }
    }
    echo "</tr>";
}

function showeinheit($techname, $tech_id, $rt01, $rt02, $rt03, $rt04, $rt05, $tech_ticks, $vorhanden, $bg, $tooltipid)
{
    global $ums_gpfad, $ums_rasse, $tooltips;

    $tooltip = '<img src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="'.$tooltips[$tooltipid].'">';

    echo '<tr valign="middle" align="center" height="25">';
    //echo '<td width="115" height="25" bgcolor="'.$bg.'"><div align="left"><b><font color="white">&nbsp;</font></b><a href="help.php?SID='.$SID.'&t='.$tech_id.'"><FONT COLOR="#FFFFFF" style="font-size:8pt">'.$techname."</div></td>";
    echo '<td class="'.$bg.'" height="25"><div align="left">&nbsp;'.$tooltip.'<b>&nbsp;&nbsp;</b><a href="help.php?t='.$tech_id.'" class="link">'.$techname."</a></div></td>";
    echo '<td class="'.$bg.'">'.number_format($rt01, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.number_format($rt02, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.number_format($rt03, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.number_format($rt04, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.number_format($rt05, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.$tech_ticks."</td>";
    if ($tech_id == 111) {
        $va = 'id="va"';
    } else {
        $va = '';
    }
    echo '<td class="'.$bg.'" '.$va.'>'.$vorhanden."</td>";
    echo '<td class="'.$bg.'"><input type="text" name="b'.$tech_id.'" id="b'.$tech_id.'" value="" size="3" maxlength="9" onKeyUp="berechnepreise();"></td>';
    echo '</tr>';
}

function showeinheit2($techname, $tech_id, $rt01, $rt02, $rt03, $rt04, $rt05, $tech_ticks, $vorhanden, $bg, $tooltipid)
{
    global $ums_gpfad, $ums_rasse, $sv_link, $tooltips;

    $tooltip = '<img src="'.$ums_gpfad.'g/t/'.$ums_rasse.'_'.$tech_id.'.jpg" border="0" title="'.$tooltips[$tooltipid].'">';

    echo '<tr valign="middle" align="center" height="25">';
    echo '<td align="left">';

    echo '
	<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="75"><a href="'.$sv_link[0].'?r='.$ums_rasse.'&t='.$tech_id.'" target="_blank">'.$tooltip.'</a></td>
		<td class="'.$bg.'" width="108" align="center"><a href="help.php?t='.$tech_id.'" class="link">'.$techname.'</a></td>
	</tr>
	</table>';
    echo '</td>';
    echo '<td class="'.$bg.'">'.number_format($rt01, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.number_format($rt02, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.number_format($rt03, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.number_format($rt04, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.number_format($rt05, 0, "", ".")."</td>";
    echo '<td class="'.$bg.'">'.$tech_ticks."</td>";
    if ($tech_id == 111) {
        $va = 'id="va"';
    } else {
        $va = '';
    }
    echo '<td class="'.$bg.'" '.$va.'>'.$vorhanden."</td>";
    echo '<td class="'.$bg.'"><input type="text" name="b'.$tech_id.'" id="b'.$tech_id.'" value="" size="3" maxlength="9" onKeyUp="berechnepreise();"></td>';
    echo '</tr>';
}

function rahmen_oben($text, $echo = true)
{
    $content = '<table border="0" cellpadding="0" cellspacing="0">
				<tr>
				<td width="13" height="37" class="rol">&nbsp;</td>
				<td align="center" class="ro"><div class="cellu">'.$text.'</div></td>
				<td width="13" class="ror">&nbsp;</td>
				</tr>
				<tr>
				<td class="rl">&nbsp;</td><td>';

    if ($echo) {
        echo $content;
    } else {
        return $content;
    }
}

function rahmen_unten($echo = true)
{
    $content = '</td><td width="13" class="rr">&nbsp;</td>
				</tr>
				<tr>
				<td width="13" class="rul">&nbsp;</td>
				<td class="ru">&nbsp;</td>
				<td width="13" class="rur">&nbsp;</td>
				</tr>
				</table><br>';

    if ($echo) {
        echo $content;
    } else {
        return $content;
    }
}



function get_fleet_ground_speed($ez, $rasse, $uid)
{
    global $sv_schiffsdaten, $sv_anz_schiffe, $sv_bs_speedup;

    $schiffsdaten = $sv_schiffsdaten;

    //spezialisierung tr�gerkapazit�t
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT spec3 FROM de_user_data WHERE user_id=?", [$uid]);
    $row = mysqli_fetch_array($db_daten);
    $spec3 = $row['spec3'];

    if ($spec3 == 2) {
        $ums_rasse = $rasse;
        for ($i = 0;$i < count($schiffsdaten[$ums_rasse]);$i++) {
            $schiffsdaten[$ums_rasse - 1][$i][1] = floor($schiffsdaten[$ums_rasse - 1][$i][1] * 1.2);
        }
    }

    //reisezeitberechnung grundreisezeit
    $rz1 = 0;
    //0=jäger
    if ($ez[1] > 0 && $rz1 < $schiffsdaten[$rasse - 1][1][0]) {
        $rz1 = $schiffsdaten[$rasse - 1][1][0];
    }
    if ($ez[2] > 0 && $rz1 < $schiffsdaten[$rasse - 1][2][0]) {
        $rz1 = $schiffsdaten[$rasse - 1][2][0];
    }
    if ($ez[3] > 0 && $rz1 < $schiffsdaten[$rasse - 1][3][0]) {
        $rz1 = $schiffsdaten[$rasse - 1][3][0];
    }

    //schlachterbeschleunigung durch kreuzer und zerst�rer
    $bs_speedup = 0;
    if ($ez[2] >= $ez[4] * $sv_bs_speedup[$rasse - 1][0] and $ez[3] >= $ez[4] * $sv_bs_speedup[$rasse - 1][1]) {
        $bs_speedup = 1;
    }
    if ($ez[4] > 0 && $rz1 < $schiffsdaten[$rasse - 1][4][0] - $bs_speedup) {
        $rz1 = $schiffsdaten[$rasse - 1][4][0] - $bs_speedup;
    }

    //5=bomber
    if ($ez[6] > 0 && $rz1 < $schiffsdaten[$rasse - 1][6][0]) {
        $rz1 = $schiffsdaten[$rasse - 1][6][0];
    }
    if ($ez[7] > 0 && $rz1 < $schiffsdaten[$rasse - 1][7][0]) {
        $rz1 = $schiffsdaten[$rasse - 1][7][0];
    }
    if ($ez[8] > 0 && $rz1 < $schiffsdaten[$rasse - 1][8][0]) {
        $rz1 = $schiffsdaten[$rasse - 1][8][0];
    }
    if ($ez[9] > 0 && $rz1 < $schiffsdaten[$rasse - 1][9][0]) {
        $rz1 = $schiffsdaten[$rasse - 1][9][0];
    }


    //trägerschifffe einbeziehen
    $schiffe_btk[0][0] = $ez[0] * $schiffsdaten[$rasse - 1][0][2];//ben�tigte transportkapazit�t
    $schiffe_btk[0][1] = $ez[5] * $schiffsdaten[$rasse - 1][5][2];//ben�tigte transportkapazit�t

    $schiffe_tk[0][0] = $ez[3] * $schiffsdaten[$rasse - 1][3][1];//vorhandene transportkapazit�t
    $schiffe_tk[0][1] = $ez[4] * $schiffsdaten[$rasse - 1][4][1];//vorhandene transportkapazit�t
    $schiffe_tk[0][2] = $ez[7] * $schiffsdaten[$rasse - 1][7][1];//vorhandene transportkapazit�t

    $schiffe_tkrest = $schiffe_tk;
    $schiffe_btkrest = $schiffe_btk;
    //zuerst bomber bearbeiten, denn die haben die l�ngste reisezeit
    if ($schiffe_btkrest[0][1] >= $schiffe_tkrest[0][0]) {
        //mehr bomber als kreuzer
        $schiffe_btkrest[0][1] = $schiffe_btkrest[0][1] - $schiffe_tkrest[0][0];
        $schiffe_tkrest[0][0] = 0;
    } else {
        //genug transportkapazit�t vorhanden
        $schiffe_tkrest[0][0] = $schiffe_tkrest[0][0] - $schiffe_btkrest[0][1];
        $schiffe_btkrest[0][1] = 0;
    }



    if ($schiffe_btkrest[0][1] >= $schiffe_tkrest[0][1]) {
        //mehr bomber als schlachtschffe
        $schiffe_btkrest[0][1] = $schiffe_btkrest[0][1] - $schiffe_tkrest[0][1];
        $schiffe_tkrest[0][1] = 0;
    } else {
        //genug transportkapazit�t vorhanden
        $schiffe_tkrest[0][1] = $schiffe_tkrest[0][1] - $schiffe_btkrest[0][1];
        $schiffe_btkrest[0][1] = 0;
    }



    if ($schiffe_btkrest[0][1] >= $schiffe_tkrest[0][2]) {
        //mehr bomber als tr�ger
        $schiffe_btkrest[0][1] = $schiffe_btkrest[0][1] - $schiffe_tkrest[0][2];
        $schiffe_tkrest[0][2] = 0;
    } else {
        //genug transportkapazit�t vorhanden
        $schiffe_tkrest[0][2] = $schiffe_tkrest[0][2] - $schiffe_btkrest[0][1];
        $schiffe_btkrest[0][1] = 0;
    }

    //dann die j�ger
    if ($schiffe_btkrest[0][0] >= $schiffe_tkrest[0][0]) {
        //mehr j�ger als kreuzer
        $schiffe_btkrest[0][0] = $schiffe_btkrest[0][0] - $schiffe_tkrest[0][0];
        $schiffe_tkrest[0][0] = 0;
    } else {
        //genug transportkapazit�t vorhanden
        $schiffe_tkrest[0][0] = $schiffe_tkrest[0][0] - $schiffe_btkrest[0][0];
        $schiffe_btkrest[0][0] = 0;
    }



    if ($schiffe_btkrest[0][0] >= $schiffe_tkrest[0][1]) {
        //mehr j�ger als schlachtschffe
        $schiffe_btkrest[0][0] = $schiffe_btkrest[0][0] - $schiffe_tkrest[0][1];
        $schiffe_tkrest[0][1] = 0;
    } else {
        //genug transportkapazit�t vorhanden
        $schiffe_tkrest[0][1] = $schiffe_tkrest[0][1] - $schiffe_btkrest[0][0];
        $schiffe_btkrest[0][0] = 0;
    }



    if ($schiffe_btkrest[0][0] >= $schiffe_tkrest[0][2]) {
        //mehr j�ger als tr�ger
        $schiffe_btkrest[0][0] = $schiffe_btkrest[0][0] - $schiffe_tkrest[0][2];
        $schiffe_tkrest[0][2] = 0;
    } else {
        //genug transportkapazit�t vorhanden
        $schiffe_tkrest[0][2] = $schiffe_tkrest[0][2] - $schiffe_btkrest[0][0];
        $schiffe_btkrest[0][0] = 0;
    }

    //�berpr�fen ob zu transportierende einheiten �brig sind
    if ($schiffe_btkrest[0][0] > 0) {
        $rz1 = $schiffsdaten[$rasse - 1][0][0];
    }//j�ger
    if ($schiffe_btkrest[0][1] > 0) {
        $rz1 = $schiffsdaten[$rasse - 1][5][0];
    }//bomber

    if ($rz1 < 0) {
        $rz1 = 0;
    }

    return($rz1);


    /*
    //anzahl der einheiten
    $anzs=$sv_anz_schiffe;
    //reisezeiten laden
    for($x=0;$x<$sv_anz_schiffe;$x++)
    {
        //reisezeit aus dem schiffsdaten-array holen
        $reisez[$x] = $sv_schiffsdaten[$ums_rasse][$x][0];
    }
    //reisezeitberechnung grundreisezeit
    $z1 = 0;
    $z2 = 0;
    $z3 = 0;

    //ben�tigte transportkapazit�t
    // [0] = ID des Feldes f�r Nissen [1] = ID des Feldes f�r Bomber [2] = N�tiger Platz f�r Nissen [3] = N�tige Platz f�r Bomber
    $tragbars[0] = 0;
    $tragbars[1] = 5;
    $tragbars[2] = $sv_schiffsdaten[$ums_rasse-1][0][2];
    $tragbars[3] = $sv_schiffsdaten[$ums_rasse-1][5][2];

    //vorhandene transportkapazit�t
    $tragers[0] = 3;
    $tragers[0] = 4;
    $tragers[0] = 7;
    $tragers[0] = $sv_schiffsdaten[$ums_rasse-1][3][1];
    $tragers[0] = $sv_schiffsdaten[$ums_rasse-1][4][1];
    $tragers[0] = $sv_schiffsdaten[$ums_rasse-1][7][1];

     for ($x=0; $x <= $anzs; $x++)
     {
         for ($y=0; $y <= 2; $y++) {
             if ($x == $tragers[$y])
             {
                 $trager[0] = $trager[0] + ($aktf[$x][0] * $tragers[$y+3]);
                 $trager[1] = $trager[1] + ($aktf[$x][1] * $tragers[$y+3]);
                 $trager[2] = $trager[2] + ($aktf[$x][2] * $tragers[$y+3]);
             }

             if ($y <= 1) {
                 if ($x == $tragbars[y]) {
                     $tragbar[0][$y] = $tragbar[0][$y] + ($aktf[$x][0] * $tragbars[$y+2]);
                     $tragbar[1][$y] = $tragbar[1][$y] + ($aktf[$x][1] * $tragbars[$y+2]);
                     $tragbar[2][$y] = $tragbar[2][$y] + ($aktf[$x][2] * $tragbars[$y+2]);
                 }
             }
         }


        //schauen ob man j�ger/bomber dabei hat
        if (($tragbar[0] + $tragbar[1]) > 0)
        {
            if (($tragbar[0] + $tragbar[1]) <= $trager)
            {
                for ($x=0; $x <= $anzs; $x++)
                {
                    if ($aktf[$x] != 0)
                    {
                        if (($x != $tragbars[0]) && ($x != $tragbars[1]))
                        {
                            if ($reisez[$x][0] > $z1) { $z1 = $reisez[$x][0]; }
                            //if ($reisez[$x][1] > $z2) { $z2 = $reisez[$x][1]; }
                            //if ($reisez[$x][2] > $z3) { $z3 = $reisez[$x][2]; }
                        }
                    }
                }
            }
            else
            {
                // Bomber werden als erstes in Tr�ger gesetzt, da l�ngere Reisezeit
                if ($tragbar[1] > $trager) {
                    // Bomber-Reisezeit, da mehr Bomber als Platz vorhanden
                    $z1 = $reisez[$tragbars[1]][0];
                    //$z2 = $reisez[$tragbars[1]][1];
                    //$z3 = $reisez[$tragbars[1]][2];
                }
                else
                {
                    if (($tragbar[0] + $tragbar[1]) > $trager)
                    {
                        // Hornissen-Reisezeit, da mehr Hornissen als Platz vorhanden
                        $z1 = $reisez[$tragbars[0]][0];
                        //$z2 = $reisez[$tragbars[0]][1];
                        //$z3 = $reisez[$tragbars[0]][2];
                    }
                }
            }
        }
        else
        {
            for ($x=0; $x <= $anzs; $x++)
            {
                if ($aktf[$x] != 0)
                {
                    if (($x != $tragbars[0]) && ($x != $tragbars[1]))
                    {
                        if ($reisez[$x][0] > $z1) { $z1 = $reisez[$x][0]; }
                        //if ($reisez[$x][1] > $z2) { $z2 = $reisez[$x][1]; }
                        //if ($reisez[$x][2] > $z3) { $z3 = $reisez[$x][2]; }
                    }
                }
            }
        }

    echo $z1;
    $z1='-10';*/
    return($z1);
}

function umlaut($fieldname)
{
    $fieldname = str_replace("ä", "&auml;", $fieldname);
    $fieldname = str_replace("Ä", "&Auml;", $fieldname);
    $fieldname = str_replace("ö", "&ouml;", $fieldname);
    $fieldname = str_replace("Ö", "&Ouml;", $fieldname);
    $fieldname = str_replace("ü", "&uuml;", $fieldname);
    $fieldname = str_replace("Ü", "&Uuml;", $fieldname);
    $fieldname = str_replace("ß", "&szlig;", $fieldname);
    $fieldname = str_replace("Ã¤", "&auml;", $fieldname);
    $fieldname = str_replace("Ã„", "&Auml;", $fieldname);
    $fieldname = str_replace("Ã¶", "&ouml;", $fieldname);
    $fieldname = str_replace("Ã–", "&Ouml;", $fieldname);
    $fieldname = str_replace("Ã¼", "&uuml;", $fieldname);
    $fieldname = str_replace("Ãœ", "&Uuml;", $fieldname);
    $fieldname = str_replace("ÃŸ", "&szlig;", $fieldname);
    $fieldname = str_replace("Â³", "&sup3;", $fieldname);
    $fieldname = str_replace("Â²", "&sup2;", $fieldname);
    $fieldname = str_replace("^", "&#94;", $fieldname);
    $fieldname = str_replace("?", "&#063;", $fieldname);
    $fieldname = str_replace("+", "&#043;", $fieldname);

    return $fieldname;
}

function reumlaut($fieldname)
{
    $fieldname = str_replace("&auml;", "ä", $fieldname);
    $fieldname = str_replace("&Auml;", "Ä", $fieldname);
    $fieldname = str_replace("&ouml;", "ö", $fieldname);
    $fieldname = str_replace("&Ouml;", "Ö", $fieldname);
    $fieldname = str_replace("&uuml;", "ü", $fieldname);
    $fieldname = str_replace("&Uuml;", "Ü", $fieldname);
    $fieldname = str_replace("&szlig;", "ß", $fieldname);
    return $fieldname;
}

function mail_smtp($empfaenger, $subject, $body, $absender = 'noreply@die-ewigen.com')
{
    //Create a new PHPMailer instance
    $mail = new PHPMailer();
    //Tell PHPMailer to use SMTP
    $mail->isSMTP();
    $mail->smtpConnect([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ]);
    //Enable SMTP debugging
    // 0 = off (for production use)
    // 1 = client messages
    // 2 = client and server messages
    $mail->SMTPDebug = 0;
    //Ask for HTML-friendly debug output
    $mail->Debugoutput = 'html';
    //Set the hostname of the mail server
    $mail->Host = $GLOBALS['env_mail_server'];
    //Set the SMTP port number - likely to be 25, 465 or 587
    $mail->Port = 587;
    //Whether to use SMTP authentication
    $mail->SMTPAuth = true;
    //Username to use for SMTP authentication
    $mail->Username = $GLOBALS['env_mail_user'];
    //Password to use for SMTP authentication
    $mail->Password = $GLOBALS['env_mail_password'];
    //Set who the message is to be sent from
    $mail->setFrom('noreply@die-ewigen.com', 'Die Ewigen');
    //Set an alternative reply-to address
    $mail->addReplyTo('noreply@die-ewigen.com', 'Die Ewigen');
    //Set who the message is to be sent to
    $mail->addAddress($empfaenger, '');
    //Set the subject line
    $mail->Subject = $subject;
    $mail->IsHTML(true);
    //Read an HTML message body from an external file, convert referenced images to embedded,
    //convert HTML into a basic plain-text alternative body
    //$mail->msgHTML($text);
    //Replace the plain text body with one created manually
    //$mail->AltBody = 'This is a plain-text message body';
    //Attach an image file
    //$mail->addAttachment('images/phpmailer_mini.png');
    $mail->Body = $body;

    //$mail->AddStringAttachment($att_content, $dateiname, 'base64', 'text/html');


    $mail->send();
}
function utf8_encode_fix($string)
{
    return mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
}

function utf8_decode_fix($string)
{
    return mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8');
}
