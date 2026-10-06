<?php
use DieEwigen\DE2\Model\Toplist\ToplistCache;

////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
// Ranglisten erstellen (eingebunden in wt.php)
// je Liste werden nur die Daten abgelegt (cache/toplist/<liste>.json),
// die Darstellung übernimmt toplist.php
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////

$toplistCache = new ToplistCache($directory.'cache/toplist');

//Zahl aus der Datenbank; fehlende Werte (NULL) wie bisher als 0
function toplist_zahl($wert)
{
    return is_numeric($wert) ? $wert + 0 : 0;
}

//Spielerrangliste: Rang, Koordinaten, Name und die beiden Werte der Liste ($spalte1/$spalte2);
//beim Erhabenen (rang 0) zeigt toplist.php zusätzlich die verbleibenden WT ($winticks)
function toplist_spielerliste($result, $spalte1, $spalte2, $rangnamen, $winticks, $mit_trend = false)
{
    $zeilen = array();
    while ($row = mysqli_fetch_array($result)) {
        $zeile = array(
            'rang' => $rangnamen[$row['rang']] ?? '',
            'erhaben' => ($row['rang'] == 0),
            'sector' => (int)$row['sector'],
            'system' => (int)$row['system'],
            'name' => $row['spielername'],
            'wert' => toplist_zahl($row[$spalte1]),
            'wert2' => toplist_zahl($row[$spalte2]),
        );
        //Platzveränderung seit dem Vortag: positiv = aufgestiegen
        if ($mit_trend) {
            $zeile['trend'] = $row['platz_last_day'] - $row['platz'];
        }
        $zeilen[] = $zeile;
    }
    return array('winticks' => (int)$winticks, 'zeilen' => $zeilen);
}

////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
// generell benötigte Daten auslesen
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////

//Gesamtpunktezahl aller Spieler
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT SUM(score) AS value FROM  de_user_data WHERE sector > 1 AND npc=0;", []);
$row = mysqli_fetch_array($result);
$server_gesamt_score=$row['value'];

//Gesamtzahl aller Spielerkollektoren
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT SUM(col) AS value FROM  de_user_data WHERE sector > 1 AND npc=0;", []);
$row = mysqli_fetch_array($result);
$server_gesamt_col=$row['value'];

////////////////////////////////////////////////////////////
// spieler
////////////////////////////////////////////////////////////

//punkte, mit Platzveränderung
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.spielername, de_user_data.score, de_user_data.col, de_user_data.sector, de_user_data.`system`, de_user_data.allytag, de_user_data.status, de_user_data.platz_last_day,de_user_data.platz, de_user_data.rang  FROM de_user_data WHERE sector > 0 AND npc=0 ORDER BY score DESC LIMIT 100");
$toplistCache->write('top1a', toplist_spielerliste($result, 'col', 'score', $rangnamen, $winticks, true));

//kollektoren
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.spielername, de_user_data.score, de_user_data.col, de_user_data.sector, de_user_data.`system`, de_user_data.allytag, de_user_data.status, de_user_data.rang  FROM de_user_data WHERE sector > 0 AND npc=0 ORDER BY col DESC LIMIT 200");
$toplistCache->write('top1b', toplist_spielerliste($result, 'col', 'score', $rangnamen, $winticks));

//türme
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.spielername, de_user_data.score, de_user_data.col, de_user_data.sector, de_user_data.`system`, de_user_data.allytag, de_user_data.status, de_user_data.rang, de_user_data.e100+de_user_data.e101+de_user_data.e102+de_user_data.e103+de_user_data.e104 AS tower FROM de_user_data WHERE sector > 0 AND npc=0 ORDER BY tower DESC LIMIT 100");
$toplistCache->write('top1c', toplist_spielerliste($result, 'tower', 'score', $rangnamen, $winticks));

//rundenpunkte
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.spielername, de_user_data.score, de_user_data.col, de_user_data.sector, de_user_data.`system`, de_user_data.allytag, de_user_data.status, de_user_data.rang, de_user_data.roundpoints FROM de_user_data WHERE sector > 0 AND npc=0 ORDER BY roundpoints DESC LIMIT 100");
$toplistCache->write('top1d', toplist_spielerliste($result, 'roundpoints', 'score', $rangnamen, $winticks));

//kopfgeldjäger
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.spielername, de_user_data.score, de_user_data.col, de_user_data.sector, de_user_data.`system`, de_user_data.rang, de_user_data.kgget FROM de_user_data WHERE sector > 1 AND npc=0 ORDER BY kgget DESC LIMIT 100");
$toplistCache->write('top1f', toplist_spielerliste($result, 'kgget', 'score', $rangnamen, $winticks));

//kopfgeld
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.spielername, de_user_data.score, de_user_data.col, de_user_data.sector, de_user_data.`system`, de_user_data.rang, (de_user_data.kg01+de_user_data.kg02*2+de_user_data.kg03*3+de_user_data.kg04*4) AS gesamtenergie FROM de_user_data WHERE sector > 1 AND npc=0 ORDER BY gesamtenergie DESC LIMIT 100");
$toplistCache->write('top1e', toplist_spielerliste($result, 'gesamtenergie', 'score', $rangnamen, $winticks));

//errungenschaften
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.spielername, de_user_data.sector, de_user_data.`system`, de_user_data.rang, de_user_data.score, de_user_data.platz, (de_user_achievement.ac1+de_user_achievement.ac2+de_user_achievement.ac3+de_user_achievement.ac4+de_user_achievement.ac5+de_user_achievement.ac6+de_user_achievement.ac7+de_user_achievement.ac8+de_user_achievement.ac9+de_user_achievement.ac10+de_user_achievement.ac11+de_user_achievement.ac12+de_user_achievement.ac13+de_user_achievement.ac14+de_user_achievement.ac15+de_user_achievement.ac16+de_user_achievement.ac17+de_user_achievement.ac18+de_user_achievement.ac19+de_user_achievement.ac20+de_user_achievement.ac21+de_user_achievement.ac22+de_user_achievement.ac23+de_user_achievement.ac24+de_user_achievement.ac25+de_user_achievement.ac999) AS wert FROM de_user_data LEFT JOIN de_user_achievement on(de_user_data.user_id = de_user_achievement.user_id) ORDER BY wert DESC LIMIT 100");
$toplistCache->write('top1g', toplist_spielerliste($result, 'wert', 'score', $rangnamen, $winticks));

//ehpunkte
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.spielername, de_user_data.score, de_user_data.ehscore, de_user_data.sector, de_user_data.`system`, de_user_data.allytag, de_user_data.status, de_user_data.rang  FROM de_user_data WHERE sector > 0 AND npc=0 ORDER BY ehscore DESC LIMIT 100");
$toplistCache->write('top1h', toplist_spielerliste($result, 'ehscore', 'score', $rangnamen, $winticks));

//eh_counter
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.spielername, de_user_data.score, de_user_data.ehscore, de_user_data.sector, de_user_data.`system`, de_user_data.allytag, de_user_data.status, de_user_data.rang, de_user_data.eh_counter, de_user_data.eh_siege FROM de_user_data WHERE sector > 0 AND npc=0 ORDER BY eh_counter DESC LIMIT 100");
$toplistCache->write('top1i', toplist_spielerliste($result, 'eh_counter', 'eh_siege', $rangnamen, $winticks));

//eh_siege
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.spielername, de_user_data.score, de_user_data.ehscore, de_user_data.sector, de_user_data.`system`, de_user_data.allytag, de_user_data.status, de_user_data.rang, de_user_data.eh_counter, de_user_data.eh_siege FROM de_user_data WHERE sector > 0 AND npc=0 ORDER BY eh_siege DESC LIMIT 100");
$toplistCache->write('top1j', toplist_spielerliste($result, 'eh_counter', 'eh_siege', $rangnamen, $winticks));

//executorpunkte
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.spielername, de_user_data.score, de_user_data.pve_score, de_user_data.sector, de_user_data.`system`, de_user_data.allytag, de_user_data.status, de_user_data.rang  FROM de_user_data WHERE sector > 0 AND npc=0 ORDER BY pve_score DESC LIMIT 100");
$toplistCache->write('top1k', toplist_spielerliste($result, 'pve_score', 'score', $rangnamen, $winticks));

////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
//sektoren
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////

//in der de_sector die Plätze der sektoren eintragen
mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector set platz=0, tempcol=0");
$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT sector, sum(score) as score, sum(col) AS col, sum(CASE WHEN npc=0 THEN col ELSE 0 END) AS col_player FROM de_user_data WHERE (npc=0 OR npc=2) AND sector > 1 AND sector < 666 GROUP BY sector ORDER BY score DESC");
$platz=1;
while($row = mysqli_fetch_array($db_daten)){
    $sec=$row["sector"];
    $col=$row["col"];
    $col_player=$row["col_player"];
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector set platz='$platz', tempcol='$col', tempcol_player='$col_player' WHERE sec_id='$sec'");
    $platz++;
}
//inzwischen leere sektoren vom platz her auf null setzen
mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector set platz=0 where platz>='$platz'");

//Rangliste: Platz, Name, Kollektoren und Punkte aller Spieler im Sektor, Platzveränderung seit dem Vortag
$zeilen = array();
$db_daten=mysqli_query($GLOBALS['dbi'], "SELECT sec_id, name, platz, platz_last_day FROM de_sector WHERE platz>0 order by platz  LIMIT 100");
while($row = mysqli_fetch_array($db_daten)){
  $get_score=mysqli_execute_query($GLOBALS['dbi'], "SELECT SUM(score) AS score, SUM(col) AS col FROM de_user_data WHERE sector=?", [$row['sec_id']]);
  $row_score=mysqli_fetch_array($get_score);

  $zeilen[] = array(
    'platz' => (int)$row['platz'],
    'sector' => (int)$row['sec_id'],
    'name' => (string)$row['name'],
    'col' => toplist_zahl($row_score['col']),
    'score' => toplist_zahl($row_score['score']),
    'trend' => $row['platz_last_day'] - $row['platz'],
  );
}
$toplistCache->write('top2', array('zeilen' => $zeilen));

//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//allianz - punkte
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
$zeilen = array();
$db_daten=mysqli_query($GLOBALS['dbi'], "SELECT allytag, sum(score) as score, sum(col) as col, count(allytag) as am FROM de_user_data where allytag<>'' AND status=1 group by allytag order by score DESC LIMIT 100");
$platz=1;
while($row = mysqli_fetch_array($db_daten)){
	//allianzdaten nachladen
  $wt_a_tag = $row["allytag"];
  $sql="SELECT * FROM de_allys WHERE allytag='$wt_a_tag'";
  $wt_a_result=mysqli_fetch_array(mysqli_query($GLOBALS['dbi'],$sql));
	$wt_a_id=$wt_a_result["id"];
	$siegartefakte=$wt_a_result["questpoints"];
	$col_erobert = $wt_a_result["colstolen"];
	$col_verloren = $wt_a_result["collost"];

  //bei den allianzen den maxmembercount, maxcolcount und maxscorecount aktualisieren
  $newmaxmembercount=$row['am'];
  $newmaxcolcount=$row['col'];
  $newmaxscorecount=$row['score'];
  mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET maxmembercount='$newmaxmembercount' WHERE id='$wt_a_id' AND maxmembercount<'$newmaxmembercount'");
  mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET maxcolcount='$newmaxcolcount' WHERE id='$wt_a_id' AND maxcolcount<'$newmaxcolcount'");
  mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET maxscorecount='$newmaxscorecount' WHERE id='$wt_a_id' AND maxscorecount<'$newmaxscorecount'");

  //bei den aufgaben ggf. die erreiche memberzahl setzen
  //mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET questreach='$newmaxmembercount' WHERE id='$wt_a_id' AND questtyp=2");
  mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET questreach='$col_erobert' WHERE id='$wt_a_id' AND questtyp=1");
  mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET questreach='$newmaxscorecount' WHERE id='$wt_a_id' AND questtyp=2");

  //die Punkte-Prozentzahl der Allianz
  $p_allianz_score= $server_gesamt_score!=0 ? $row['score']*100/$server_gesamt_score : 0;

  //die Kollektoren-Prozentzahl der Allian
  $p_allianz_col= $server_gesamt_col!=0 ? $row['col']*100/$server_gesamt_col : 0;

  $zeilen[] = array(
    'id' => (int)$wt_a_id,
    'tag' => $row['allytag'],
    'mitglieder' => (int)$row['am'],
    'siegartefakte' => toplist_zahl($siegartefakte),
    'score' => toplist_zahl($row['score']),
    'score_anteil' => $p_allianz_score,
    'schnitt' => round($row['score'] / $row['am']),
    'col' => toplist_zahl($row['col']),
    'col_anteil' => $p_allianz_col,
    'schnitt_col' => round($row['col'] / $row['am']),
    'col_erobert' => toplist_zahl($col_erobert),
    'col_verloren' => toplist_zahl($col_verloren),
  );
  $platz++;
}
$toplistCache->write('top3', array('zeilen' => $zeilen));

//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//allianz - siegartefakte
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
$zeilen = array();
$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT id, allytag, questpoints FROM de_allys ORDER BY questpoints DESC, id ASC LIMIT 100");
while($row = mysqli_fetch_array($db_daten)){
  $zeilen[] = array('id' => (int)$row['id'], 'tag' => $row['allytag'], 'wert' => toplist_zahl($row['questpoints']));
}
$toplistCache->write('top3a', array('zeilen' => $zeilen));

//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//allianz - bündnisse
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////

//bündnisse laden
$platz=1;
$allydata=array();
$paare=array();
$db_datenx=mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_ally_partner");
while($rowx = mysqli_fetch_array($db_datenx)){
  $allyid1=$rowx['ally_id_1'];
  $allyid2=$rowx['ally_id_2'];

  //doppelte partnerschaft (dasselbe Paar noch einmal, auch umgekehrt): nur diese eine Zeile löschen, das Bündnis bleibt;
  //früher wurden alle Zeilen mit ally_id_2 = ally_id_1 gelöscht und beim Gegenstück dann auch das Original
  $paar=min((int)$allyid1, (int)$allyid2).'-'.max((int)$allyid1, (int)$allyid2);
  if(isset($paare[$paar])){
    mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_ally_partner WHERE ally_id_1=? AND ally_id_2=? LIMIT 1", [$allyid1, $allyid2]);
    continue;
  }
  $paare[$paar]=true;

  //allytags laden
  $db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT allytag FROM de_allys WHERE id='$allyid1'");
  $row = mysqli_fetch_array($db_daten);
  $allytag1=$row['allytag'];

  $db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT allytag FROM de_allys WHERE id='$allyid2'");
  $row = mysqli_fetch_array($db_daten);
  $allytag2=$row['allytag'];

  $db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT SUM(score) AS score, SUM(col) AS col, COUNT(user_id) AS am FROM de_user_data
  WHERE (allytag='$allytag1' OR allytag='$allytag2') AND status=1");

  $row = mysqli_fetch_array($db_daten);

  $allydata[$platz-1]['allytag']=$allytag1.' & '.$allytag2;
  $allydata[$platz-1]['score']=$row['score'];
  $allydata[$platz-1]['col']=$row['col'];
  $allydata[$platz-1]['am']=$row['am'];

  //Anteile wie bei den Allianzen; ohne Spielerpunkte/-kollektoren 0 statt Division durch 0
  $allydata[$platz-1]['p_allianz_score']=$server_gesamt_score!=0 ? $row['score']*100/$server_gesamt_score : 0;
  $allydata[$platz-1]['p_allianz_col']=$server_gesamt_col!=0 ? $row['col']*100/$server_gesamt_col : 0;

  //die beiden Allianzen einzeln (eigenes Bündnis hervorheben)
  $allydata[$platz-1]['tags']=array($allytag1, $allytag2);

  $platz++;
}

  //daten sortieren
  $score=array();
  foreach ($allydata as $key => $row) {
      $score[$key]    = $row['score'];
  }

  array_multisort($score, SORT_DESC, $allydata);

$zeilen = array();
foreach ($allydata as $bund) {
  $zeilen[] = array(
    'tags' => $bund['tags'],
    'score' => toplist_zahl($bund['score']),
    'score_anteil' => $bund['p_allianz_score'],
    'col' => toplist_zahl($bund['col']),
    'col_anteil' => $bund['p_allianz_col'],
    'mitglieder' => (int)$bund['am'],
  );
}
$toplistCache->write('top3b', array('zeilen' => $zeilen));

//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//allianz - erhabene gestellt
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
if($sv_ewige_runde==1){
	$zeilen = array();
	$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT id, allytag, eh_gestellt_anz FROM de_allys ORDER BY eh_gestellt_anz DESC, id ASC LIMIT 100");
	while($row = mysqli_fetch_array($db_daten)){
	  $zeilen[] = array('id' => (int)$row['id'], 'tag' => $row['allytag'], 'wert' => toplist_zahl($row['eh_gestellt_anz']));
	}
	$toplistCache->write('top3c', array('zeilen' => $zeilen));
}

//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
// handel
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
$zeilen = array();
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT spielername, sector, `system`, tradesystemscore, tradesystemtrades FROM de_user_data ORDER BY tradesystemscore DESC LIMIT 100 ");
while($row = mysqli_fetch_array($result)){
  $zeilen[] = array(
    'sector' => (int)$row['sector'],
    'system' => (int)$row['system'],
    'name' => $row['spielername'],
    'wert' => toplist_zahl($row['tradesystemtrades']),
    'wert2' => toplist_zahl($row['tradesystemscore']),
  );
}
$toplistCache->write('top4a', array('zeilen' => $zeilen));
