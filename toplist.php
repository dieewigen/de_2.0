<?php
use DieEwigen\DE2\Model\Toplist\ToplistCache;

include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_toplist.lang.php';
include "functions.php";

$sql = "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, `system`, newtrans, newnews, allytag, status FROM de_user_data WHERE user_id=?";
$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row["restyp01"];$restyp02=$row["restyp02"];$restyp03=$row["restyp03"];$restyp04=$row["restyp04"];$restyp05=$row["restyp05"];
$punkte=$row["score"];$newtrans=$row["newtrans"];$newnews=$row["newnews"];
$sector=$row["sector"];$system=$row["system"];
if ($row["status"]==1) $ownally = $row["allytag"];
?>
<!doctype html>
<html>
<head>
<title>Rangliste</title>
<?php include "cssinclude.php";?>
</head>

<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

include "resline.php";

//Die Listen erstellt der Wirtschaftstick (tickler/wt_create_toplist.php)
$toplistCache = new ToplistCache();

//Zahl im Spielformat
function tl_zahl($wert)
{
    return number_format((float)$wert, 0, "", ".");
}

//Zahl mit Anteil in Prozent darunter
function tl_mit_anteil($wert, $anteil)
{
    return tl_zahl($wert).'<small>'.number_format((float)$anteil, 2, ",", ".").' %</small>';
}

//Kopf einer Liste: je Spalte array(Überschrift, Klasse, Tooltip)
function tl_kopf($spalten)
{
    $html = '<table class="tl-tabelle"><thead><tr>';
    foreach ($spalten as $spalte) {
        $html .= '<th'.(!empty($spalte[1]) ? ' class="'.$spalte[1].'"' : '').(!empty($spalte[2]) ? ' title="'.$spalte[2].'"' : '').'>'.$spalte[0].'</th>';
    }
    return $html.'</tr></thead><tbody>';
}

//Zeilenbeginn; die eigene Zeile hervorgehoben und Sprungziel
function tl_zeile($eigen, $title = '')
{
    return '<tr'.($eigen ? ' class="tl-eigen" id="tl-eigen"' : '').($title != '' ? ' title="'.$title.'"' : '').'>';
}

//Platz; die ersten drei hervorgehoben
function tl_platz($platz)
{
    return '<td class="tl-platz"><span'.($platz <= 3 ? ' class="tl-podest tl-podest'.$platz.'"' : '').'>'.$platz.'</span></td>';
}

//Platzveränderung seit der letzten Tageswertung: positiv = aufgestiegen
function tl_trend($trend)
{
    if ($trend > 0) {
        return '<td class="tl-trend tl-trend-auf" title="Platzver&auml;nderung&+'.$trend.'">&#9650;'.$trend.'</td>';
    }
    if ($trend < 0) {
        return '<td class="tl-trend tl-trend-ab" title="Platzver&auml;nderung&'.$trend.'">&#9660;'.abs($trend).'</td>';
    }
    return '<td class="tl-trend" title="Platzver&auml;nderung&unver&auml;ndert">&ndash;</td>';
}

//Allianzkürzel als Link auf die Allianzseite
function tl_allianz($id, $tag)
{
    if ($id > 0) {
        return '<a href="ally_detail.php?allyid='.$id.'">'.$tag.'</a>';
    }
    return $tag;
}

//eigene Allianz (nur als Mitglied, nicht als Bewerber)
function tl_eigene_allianz($tag)
{
    global $ownally;
    return isset($ownally) && $ownally !== '' && $tag === $ownally;
}

//Spielerrangliste
function tl_spielerliste($liste, $menu)
{
    global $sector, $system, $sv_max_col_attgrenze, $sv_min_col_attgrenze;

    $spalten = array(
        array('Platz', 'tl-platz'),
        array('Rang'),
        array('System', 'tl-mitte'),
        array('Name'),
        array($menu['spalte'], 'tl-zahl'.($menu['sortiert'] == 1 ? ' tl-sortiert' : '')),
        array($menu['spalte2'], 'tl-zahl'.($menu['sortiert'] == 2 ? ' tl-sortiert' : '')),
    );
    if (!empty($menu['trend'])) {
        $spalten[] = array('', 'tl-trend');
    }
    $html = tl_kopf($spalten);

    $eigen_platz = 0;
    //Kollektorliste: Angriffsgrenze wie im Militär, bezogen auf den ersten Platz der Liste
    $maxcol = $liste['zeilen'][0]['wert'] ?? 0;
    foreach ($liste['zeilen'] as $i => $z) {
        $platz = $i + 1;
        $eigen = ($z['sector'] == $sector && $z['system'] == $system);
        if ($eigen) {
            $eigen_platz = $platz;
        }

        $html .= tl_zeile($eigen).tl_platz($platz);
        if ($z['erhaben']) {
            $html .= '<td><span class="tl-erhaben">'.$z['rang'].'<small>noch '.$liste['winticks'].' WT</small></span></td>';
        } else {
            $html .= '<td class="tl-leise">'.$z['rang'].'</td>';
        }
        $html .= '<td class="tl-mitte tl-leise">'.$z['sector'].':'.$z['system'].'</td>';
        $html .= '<td class="tl-name"><a href="details.php?se='.$z['sector'].'&amp;sy='.$z['system'].'">'.$z['name'].'</a></td>';

        $title = '';
        if (!empty($menu['angriffsgrenze']) && $maxcol > 0) {
            $colag = $z['wert'] / $maxcol * $sv_max_col_attgrenze;
            if ($colag < $sv_min_col_attgrenze) {
                $colag = $sv_min_col_attgrenze;
            }
            $title = ' title="Kollektor-Angriffsgrenze&Kann Ziele ab '.tl_zahl(round($colag * $z['wert'])).' Kollektoren angreifen."';
        }
        $html .= '<td class="tl-zahl'.($menu['sortiert'] == 1 ? ' tl-sortiert' : '').'"'.$title.'>'.tl_zahl($z['wert']).'</td>';
        $html .= '<td class="tl-zahl'.($menu['sortiert'] == 2 ? ' tl-sortiert' : '').'">'.tl_zahl($z['wert2']).'</td>';
        if (!empty($menu['trend'])) {
            $html .= tl_trend($z['trend']);
        }
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';

    return array($html, $eigen_platz);
}

//Sektorrangliste
function tl_sektorliste($liste)
{
    global $sector;

    $html = tl_kopf(array(
        array('Platz', 'tl-platz'),
        array('Sektor', 'tl-mitte'),
        array('Name'),
        array('Kollektoren', 'tl-zahl'),
        array('Punkte', 'tl-zahl tl-sortiert'),
        array('', 'tl-trend'),
    ));

    $eigen_platz = 0;
    foreach ($liste['zeilen'] as $z) {
        $eigen = ($z['sector'] == $sector);
        if ($eigen) {
            $eigen_platz = $z['platz'];
        }
        $html .= tl_zeile($eigen).tl_platz($z['platz']);
        $html .= '<td class="tl-mitte"><a href="sector.php?sf='.$z['sector'].'">'.$z['sector'].'</a></td>';
        $html .= '<td class="tl-name">'.$z['name'].'</td>';
        $html .= '<td class="tl-zahl">'.tl_zahl($z['col']).'</td>';
        $html .= '<td class="tl-zahl tl-sortiert">'.tl_zahl($z['score']).'</td>';
        $html .= tl_trend($z['trend']).'</tr>';
    }
    $html .= '</tbody></table>';

    return array($html, $eigen_platz);
}

//Allianzrangliste nach Punkten
function tl_allianzliste($liste)
{
    $html = tl_kopf(array(
        array('Platz', 'tl-platz'),
        array('Allianz'),
        array('M', 'tl-mitte', 'Mitglieder'),
        array('RSA', 'tl-zahl', 'Rundensiegartefakte'),
        array('Punkte', 'tl-zahl tl-sortiert'),
        array('Schnitt', 'tl-zahl', 'Punkte je Mitglied'),
        array('Kollektoren', 'tl-zahl'),
        array('Schnitt', 'tl-zahl', 'Kollektoren je Mitglied'),
    ));

    $eigen_platz = 0;
    foreach ($liste['zeilen'] as $i => $z) {
        $platz = $i + 1;
        $eigen = tl_eigene_allianz($z['tag']);
        if ($eigen) {
            $eigen_platz = $platz;
        }
        $title = 'Kollektoren&Erobert: '.tl_zahl($z['col_erobert']).'<br>Verloren: '.tl_zahl($z['col_verloren']);
        $html .= tl_zeile($eigen, $title).tl_platz($platz);
        $html .= '<td class="tl-name">'.tl_allianz($z['id'], $z['tag']).'</td>';
        $html .= '<td class="tl-mitte">'.$z['mitglieder'].'</td>';
        $html .= '<td class="tl-zahl">'.tl_zahl($z['siegartefakte']).'</td>';
        $html .= '<td class="tl-zahl tl-sortiert">'.tl_mit_anteil($z['score'], $z['score_anteil']).'</td>';
        $html .= '<td class="tl-zahl tl-leise">'.tl_zahl($z['schnitt']).'</td>';
        $html .= '<td class="tl-zahl">'.tl_mit_anteil($z['col'], $z['col_anteil']).'</td>';
        $html .= '<td class="tl-zahl tl-leise">'.tl_zahl($z['schnitt_col']).'</td>';
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';

    return array($html, $eigen_platz);
}

//Allianzen nach einem Wert (Siegartefakte, gestellte Erhabene)
function tl_allianzwertliste($liste, $menu)
{
    $html = tl_kopf(array(
        array('Platz', 'tl-platz'),
        array('Allianz'),
        array($menu['spalte'], 'tl-zahl tl-sortiert'),
    ));

    $eigen_platz = 0;
    foreach ($liste['zeilen'] as $i => $z) {
        $platz = $i + 1;
        $eigen = tl_eigene_allianz($z['tag']);
        if ($eigen) {
            $eigen_platz = $platz;
        }
        $html .= tl_zeile($eigen).tl_platz($platz);
        $html .= '<td class="tl-name">'.tl_allianz($z['id'], $z['tag']).'</td>';
        $html .= '<td class="tl-zahl tl-sortiert">'.tl_zahl($z['wert']).'</td>';
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';

    return array($html, $eigen_platz);
}

//Bündnisse: je zwei Partnerallianzen zusammen
function tl_buendnisliste($liste)
{
    $html = tl_kopf(array(
        array('Platz', 'tl-platz'),
        array('B&uuml;ndnis'),
        array('Punkte', 'tl-zahl tl-sortiert'),
        array('Kollektoren', 'tl-zahl'),
        array('M', 'tl-mitte', 'Mitglieder'),
    ));

    $eigen_platz = 0;
    foreach ($liste['zeilen'] as $i => $z) {
        $platz = $i + 1;
        $eigen = tl_eigene_allianz($z['tags'][0]) || tl_eigene_allianz($z['tags'][1]);
        if ($eigen) {
            $eigen_platz = $platz;
        }
        $html .= tl_zeile($eigen).tl_platz($platz);
        $html .= '<td class="tl-name">'.$z['tags'][0].' &amp; '.$z['tags'][1].'</td>';
        $html .= '<td class="tl-zahl tl-sortiert">'.tl_mit_anteil($z['score'], $z['score_anteil']).'</td>';
        $html .= '<td class="tl-zahl">'.tl_mit_anteil($z['col'], $z['col_anteil']).'</td>';
        $html .= '<td class="tl-mitte">'.tl_zahl($z['mitglieder']).'</td>';
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';

    return array($html, $eigen_platz);
}

//Handel (Handelssystem)
function tl_handelsliste($liste)
{
    global $sector, $system;

    $html = tl_kopf(array(
        array('Platz', 'tl-platz'),
        array('Name'),
        array('Handelsaktionen', 'tl-zahl'),
        array('Handelspunkte', 'tl-zahl tl-sortiert'),
    ));

    $eigen_platz = 0;
    foreach ($liste['zeilen'] as $i => $z) {
        $platz = $i + 1;
        $eigen = ($z['sector'] == $sector && $z['system'] == $system);
        if ($eigen) {
            $eigen_platz = $platz;
        }
        $html .= tl_zeile($eigen).tl_platz($platz);
        $html .= '<td class="tl-name"><a href="details.php?se='.$z['sector'].'&amp;sy='.$z['system'].'&amp;a=s">'.$z['name'].'</a></td>';
        $html .= '<td class="tl-zahl">'.tl_zahl($z['wert']).'</td>';
        $html .= '<td class="tl-zahl tl-sortiert">'.tl_zahl($z['wert2']).'</td>';
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';

    return array($html, $eigen_platz);
}

//eine Liste im Rahmen: Hinweis, Sprung zum eigenen Platz, Tabelle; ohne Datei (vor dem ersten WT) ein Hinweis
function tl_ausgeben($titel, $liste, $hinweis, $darstellung)
{
    rahmen_oben($titel);
    echo '<div class="mod tl">';
    if ($hinweis != '') {
        echo '<div class="mod-hinweis tl-hinweis">'.$hinweis.'</div>';
    }
    if ($liste === null) {
        echo '<div class="mod-leer">Die Rangliste wird beim n&auml;chsten Wirtschaftstick erstellt.</div>';
    } elseif (count($liste['zeilen'] ?? array()) == 0) {
        echo '<div class="mod-leer">Diese Rangliste ist noch leer.</div>';
    } else {
        [$tabelle, $eigen_platz] = $darstellung($liste);
        if ($eigen_platz > 0) {
            echo '<div class="tl-oben"><a href="#tl-eigen" class="mod-btn mod-btn-leise ally-btn-klein">Dein Platz: '.$eigen_platz.'</a></div>';
        }
        echo $tabelle;
    }
    echo '</div>';
    rahmen_unten();
}

function showmenu($menuid, $menupos){
	global $toplist_lang, $sv_ewige_runde, $sv_oscar, $sv_hardcore;
	//menüs definieren; spalte/spalte2 = Überschriften der beiden Werte, sortiert = Spalte, nach der die Liste geordnet ist

	/////////////////////////////
	//spieler
	/////////////////////////////

	//ewige runde?
	if($sv_ewige_runde==1){
		$index=0;
		//punkte
		$menudata[0][$index]=array('name' => $toplist_lang['punkte'], 'datei' => 'top1a', 'spalte' => $toplist_lang['kollektoren'], 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 2, 'trend' => true);
		//Kollektoren
		$index++;
		$menudata[0][$index]=array('name' => $toplist_lang['kollektoren'], 'datei' => 'top1b', 'spalte' => $toplist_lang['kollektoren'], 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 1, 'angriffsgrenze' => true);
		//Türme
		$index++;
		$menudata[0][$index]=array('name' => $toplist_lang['tuerme'], 'datei' => 'top1c', 'spalte' => $toplist_lang['tuerme'], 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 1);
		//Errungenschaften
		$index++;
		$menudata[0][$index]=array('name' => $toplist_lang['errungenschaften'], 'datei' => 'top1g', 'spalte' => $toplist_lang['errungenschaften'], 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 1);
		if($sv_oscar!=1){
			//Kopfgeld
			$index++;
			$menudata[0][$index]=array('name' => 'Kopfgeld', 'datei' => 'top1e', 'spalte' => $toplist_lang['gesamtwert'].' in M', 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 1);
			//Kopfgeldjäger
			$index++;
			$menudata[0][$index]=array('name' => 'Kopfgeldj&auml;ger', 'datei' => 'top1f', 'spalte' => $toplist_lang['gesamtwert'].' in M', 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 1);
		}
		//Erhabenenpunkte
		$index++;
		$menudata[0][$index]=array('name' => $toplist_lang['erhabenenpunkte'], 'datei' => 'top1h', 'spalte' => $toplist_lang['ehpunkte'], 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 1);
		$menudata[0][$index]['hinweis']='Die Erhabenenpunkte berechnen sich nach folgender Formel: Kollektoren * 0,75 + Einheitenpunkte / 250.000 + Errungenschaften + Flottenangriffserfahrung / 12.500 + Flottenverteidigungserfahrung / 10.000 + Z&ouml;llner / 10.000';
		//Erhabenencounter
		$index++;
		$menudata[0][$index]=array('name' => 'Erhabenencounter', 'datei' => 'top1i', 'spalte' => 'EH-Counter', 'spalte2' => 'EH-Siege', 'sortiert' => 1);
		//Erhabenensiege
		$index++;
		$menudata[0][$index]=array('name' => 'Erhabenensiege', 'datei' => 'top1j', 'spalte' => 'EH-Counter', 'spalte2' => 'EH-Siege', 'sortiert' => 2);
		//Executor-Punkte
		$index++;
		$menudata[0][$index]=array('name' => 'Executorpunkte', 'datei' => 'top1k', 'spalte' => 'Executorpunkte', 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 1);
		$menudata[0][$index]['hinweis']='Die Executorpunkte berechnen sich nach folgender Formel: Geb&auml;udepunkte (Vergessene-Systeme) + Handelspunkte / 100 (vorl&auml;ufig, wird noch ge&auml;ndert)';
	}elseif($sv_hardcore==1){
		$index=0;
		//punkte
		$menudata[0][$index]=array('name' => $toplist_lang['punkte'], 'datei' => 'top1a', 'spalte' => $toplist_lang['kollektoren'], 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 2, 'trend' => true);
		//Kollektoren
		$index++;
		$menudata[0][$index]=array('name' => $toplist_lang['kollektoren'], 'datei' => 'top1b', 'spalte' => $toplist_lang['kollektoren'], 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 1, 'angriffsgrenze' => true);
		//Türme
		$index++;
		$menudata[0][$index]=array('name' => $toplist_lang['tuerme'], 'datei' => 'top1c', 'spalte' => $toplist_lang['tuerme'], 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 1);
		//Rundenpunkte
		$index++;
		$menudata[0][$index]=array('name' => $toplist_lang['rundenpunkte'], 'datei' => 'top1d', 'spalte' => $toplist_lang['rundenpunkte'], 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 1);
		//Errungenschaften
		$index++;
		$menudata[0][$index]=array('name' => $toplist_lang['errungenschaften'], 'datei' => 'top1g', 'spalte' => $toplist_lang['errungenschaften'], 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 1);
		if($sv_oscar!=1){
			//Kopfgeld
			$index++;
			$menudata[0][$index]=array('name' => 'Kopfgeld', 'datei' => 'top1e', 'spalte' => $toplist_lang['gesamtwert'].' in M', 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 1);
			//Kopfgeldjäger
			$index++;
			$menudata[0][$index]=array('name' => 'Kopfgeldj&auml;ger', 'datei' => 'top1f', 'spalte' => $toplist_lang['gesamtwert'].' in M', 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 1);
		}
		//Erhabenenpunkte
		$index++;
		$menudata[0][$index]=array('name' => $toplist_lang['erhabenenpunkte'], 'datei' => 'top1h', 'spalte' => $toplist_lang['ehpunkte'], 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 1);
		$menudata[0][$index]['hinweis']='Die Erhabenenpunkte berechnen sich nach folgender Formel: Kollektoren + Einheitenpunkte / 250.000 + Errungenschaften + Rundenpunkte + Flottenangriffserfahrung / 12.500 + Flottenverteidigungserfahrung / 10.000';
		//Erhabenencounter
		$index++;
		$menudata[0][$index]=array('name' => 'Erhabenencounter', 'datei' => 'top1i', 'spalte' => 'EH-Counter', 'spalte2' => 'EH-Siege', 'sortiert' => 1);
		//Erhabenensiege
		$index++;
		$menudata[0][$index]=array('name' => 'Erhabenenteilsiege', 'datei' => 'top1j', 'spalte' => 'EH-Counter', 'spalte2' => 'EH-Siege', 'sortiert' => 2);
		//Executor-Punkte
		$index++;
		$menudata[0][$index]=array('name' => 'Executorpunkte', 'datei' => 'top1k', 'spalte' => 'Executorpunkte', 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 1);
		$menudata[0][$index]['hinweis']='Die Executorpunkte berechnen sich nach folgender Formel: Geb&auml;udepunkte (Vergessene-Systeme) + Handelspunkte / 100 (vorl&auml;ufig, wird noch ge&auml;ndert)';
	}else{ //normale runde
		$index=0;
		//punkte
		$menudata[0][$index]=array('name' => $toplist_lang['punkte'], 'datei' => 'top1a', 'spalte' => $toplist_lang['kollektoren'], 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 2, 'trend' => true);
		//Kollektoren
		$index++;
		$menudata[0][$index]=array('name' => $toplist_lang['kollektoren'], 'datei' => 'top1b', 'spalte' => $toplist_lang['kollektoren'], 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 1, 'angriffsgrenze' => true);
		//Türme
		$index++;
		$menudata[0][$index]=array('name' => $toplist_lang['tuerme'], 'datei' => 'top1c', 'spalte' => $toplist_lang['tuerme'], 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 1);
		//Rundenpunkte
		$index++;
		$menudata[0][$index]=array('name' => $toplist_lang['rundenpunkte'], 'datei' => 'top1d', 'spalte' => $toplist_lang['rundenpunkte'], 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 1);
		//Errungenschaften
		$index++;
		$menudata[0][$index]=array('name' => $toplist_lang['errungenschaften'], 'datei' => 'top1g', 'spalte' => $toplist_lang['errungenschaften'], 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 1);
		if($sv_oscar!=1){
			//Kopfgeld
			$index++;
			$menudata[0][$index]=array('name' => 'Kopfgeld', 'datei' => 'top1e', 'spalte' => $toplist_lang['gesamtwert'].' in M', 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 1);
			//Kopfgeldjäger
			$index++;
			$menudata[0][$index]=array('name' => 'Kopfgeldj&auml;ger', 'datei' => 'top1f', 'spalte' => $toplist_lang['gesamtwert'].' in M', 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 1);
		}
		//Erhabenenpunkte
		$index++;
		$menudata[0][$index]=array('name' => $toplist_lang['erhabenenpunkte'], 'datei' => 'top1h', 'spalte' => $toplist_lang['ehpunkte'], 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 1);
		$menudata[0][$index]['hinweis']='Die Erhabenenpunkte berechnen sich nach folgender Formel: Kollektoren + Einheitenpunkte / 250.000 + Errungenschaften + Rundenpunkte + Flottenangriffserfahrung / 12.500 + Flottenverteidigungserfahrung / 10.000';
		//Executor-Punkte
		$index++;
		$menudata[0][$index]=array('name' => 'Executorpunkte', 'datei' => 'top1k', 'spalte' => 'Executorpunkte', 'spalte2' => $toplist_lang['punkte'], 'sortiert' => 1);
		$menudata[0][$index]['hinweis']='Die Executorpunkte berechnen sich nach folgender Formel: Geb&auml;udepunkte (Vergessene-Systeme) + Handelspunkte / 100 (vorl&auml;ufig, wird noch ge&auml;ndert)';
	}

	/////////////////////////////
	//allianz
	/////////////////////////////
	$index=0;
	//punkte
	$menudata[2][$index]=array('name' => $toplist_lang['punkte'], 'datei' => 'top3');
	//Siegartefakte
	$index++;
	$menudata[2][$index]=array('name' => $toplist_lang['siegartefakte'], 'datei' => 'top3a', 'spalte' => 'Rundensiegartefakte');
	//Bündnisse
	$index++;
	$menudata[2][$index]=array('name' => $toplist_lang['buendnisse'], 'datei' => 'top3b');
	if($sv_ewige_runde==1){
		//Erhabene
		$index++;
		$menudata[2][$index]=array('name' => 'Erhabene', 'datei' => 'top3c', 'spalte' => 'Erhabene gestellt');
	}

	//unbekannte Unterliste: die erste
	if(!isset($menudata[$menuid][$menupos])){
		$menupos=0;
	}

	//////////////////////////////////////////////////////////
	//die einzelnen menüpunkte ausgeben
	//////////////////////////////////////////////////////////
	echo '<div class="mod tl-unternavi">';
	for($i=0;$i<count($menudata[$menuid]);$i++){
		echo '<a href="toplist.php?s='.($menuid+1).'&amp;mp='.$i.'" class="ally-reiter'.($menupos==$i ? ' ally-reiter-aktiv' : '').'">'.$menudata[$menuid][$i]['name'].'</a>';
	}
	echo '</div>';

	return $menudata[$menuid][$menupos];
}

if(!isset($_REQUEST["mp"])){
	$_REQUEST["mp"]=0;
}

$s = $_REQUEST["s"] ?? 1;
$historie = (isset($_GET['show_history']) && $_GET['show_history']==1);

//Reiter: Spieler, Sektoren, Allianzen, Handel und die Sieger der vergangenen Runden
$reiter = array(
	'toplist.php?s=1' => array($toplist_lang['spieler'], !$historie && $s==1),
	'toplist.php?s=2' => array('Sektoren', !$historie && $s==2),
	'toplist.php?s=3' => array('Allianzen', !$historie && $s==3),
	'toplist.php?s=4' => array($toplist_lang['handel'], !$historie && $s==4),
	'toplist.php?show_history=1' => array('Rundensieger', $historie),
);
echo '<div class="mod ally-navi tl-navi">';
foreach ($reiter as $link => $r) {
	echo '<a href="'.$link.'" class="ally-reiter'.($r[1] ? ' ally-reiter-aktiv' : '').'">'.$r[0].'</a>';
}
echo '</div>';

//Die Gewinner der alten Runden ansehen
if($historie){
	rahmen_oben('Gewinner der vergangenen Runden');
	echo '<div class="mod tl">';

	$sql = "SELECT * FROM de_server_round_toplist ORDER BY round_id DESC";
	$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql);
	$runden = 0;
	while($row = mysqli_fetch_assoc($db_daten)){
		switch($row['player_rasse']){
			case 1:
				$rasse='Die Ewigen';
			break;

			case 2:
				$rasse='Ishtar';
			break;

			case 3:
				$rasse='K&#180;Tharr';
			break;

			case 4:
				$rasse='Z&#180;tah-ara';
			break;

			default:
				$rasse='N/A';
			break;
		}

		echo '<div class="tl-runde">';
		echo '<div class="tl-runde-kopf">Runde '.$row['round_id'].'<span>'.number_format($row['round_wt'], 0,"",".").' Wirtschaftsticks</span></div>';
		echo '<div class="tl-runde-daten">';
		echo '<span class="mod-typ">Erhabene/Erhabener</span><span><b>'.$row['player_spielername'].'</b> ('.$row['player_sector'].':'.$row['player_system'].') &middot; '.$rasse.' &middot; '.number_format($row['player_score'], 0,"",".").' Punkte</span>';
		echo '<span class="mod-typ">Sektor</span><span><b>'.$row['sector_id'].'</b>'.($row['sector_name'] != '' ? ' &middot; '.$row['sector_name'] : '').' &middot; '.number_format($row['sector_score'], 0,"",".").' Punkte</span>';
		if(!empty($row['ally_tag'])){
			echo '<span class="mod-typ">Allianz</span><span><b>'.$row['ally_tag'].'</b> &middot; '.number_format($row['ally_roundpoints'], 0,"",".").' Rundensiegartefakte</span>';
		}
		echo '</div></div>';
		$runden++;
	}
	if ($runden == 0) {
		echo '<div class="mod-leer">Es gibt noch keine abgeschlossene Runde.</div>';
	}

	echo '</div>';
	rahmen_unten();

	die('</body></html>');
}

///////////////////////////////////
//spieler
///////////////////////////////////
if ($s==1){
	$menu = showmenu(0, (int)$_REQUEST["mp"]);
	tl_ausgeben($toplist_lang['spieler'].' &middot; '.$menu['name'], $toplistCache->read($menu['datei']), $menu['hinweis'] ?? '',
		fn($liste) => tl_spielerliste($liste, $menu));
}

///////////////////////////////////
//sektorenrangliste
///////////////////////////////////
if ($s==2){
	tl_ausgeben('Sektoren', $toplistCache->read('top2'), '', 'tl_sektorliste');
}

///////////////////////////////////
//allianz
///////////////////////////////////
if ($s==3){
	$menu = showmenu(2, (int)$_REQUEST["mp"]);
	if ($menu['datei'] == 'top3') {
		$darstellung = 'tl_allianzliste';
	} elseif ($menu['datei'] == 'top3b') {
		$darstellung = 'tl_buendnisliste';
	} else {
		$darstellung = fn($liste) => tl_allianzwertliste($liste, $menu);
	}
	tl_ausgeben('Allianzen &middot; '.$menu['name'], $toplistCache->read($menu['datei']), '', $darstellung);
}

///////////////////////////////////
//handel
///////////////////////////////////
if ($s==4){
	tl_ausgeben($toplist_lang['handel'], $toplistCache->read('top4a'), '', 'tl_handelsliste');
}
?>

</body>
</html>
