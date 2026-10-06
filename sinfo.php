<?php
include "inc/header.inc.php";
include "inc/lang/".$sv_server_lang."_sinfo.lang.php";
include 'functions.php';

$sql = "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, col, sector, `system`, newtrans, newnews, allytag, status, hide_secpics, platz, rang, secsort, secstatdisable FROM de_user_data WHERE user_id=?";
$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row["restyp01"];$restyp02=$row["restyp02"];$restyp03=$row["restyp03"];$restyp04=$row["restyp04"];
$restyp05=$row["restyp05"];$punkte=$row["score"];$newtrans=$row["newtrans"];$newnews=$row["newnews"];
$sector=$row["sector"];$system=$row["system"];

?>
<!doctype html>
<html>
<head>
<title><?php echo $sinfo_lang['title']?></title>
<?php include "cssinclude.php"; ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

//stelle die ressourcenleiste dar (lädt auch $deSystem mit dem Servertext)
include "resline.php";

//Stunden mit gleichen Minuten zu Zeilen zusammenfassen: array(array(von, bis, minuten), ...); Stunden ohne Tick fallen weg
function si_plan($ticks)
{
	$zeilen = array();
	for ($h = 0; $h <= 23; $h++) {
		$minuten = array_map('intval', $ticks[$h] ?? array());
		sort($minuten);
		$letzte = count($zeilen) - 1;
		if ($letzte >= 0 && $zeilen[$letzte][1] == $h - 1 && $zeilen[$letzte][2] === $minuten) {
			$zeilen[$letzte][1] = $h;
		} else {
			$zeilen[] = array($h, $h, $minuten);
		}
	}
	return array_values(array_filter($zeilen, fn($z) => count($z[2]) > 0));
}

//nächster planmäßiger Tick ab der kommenden Minute (Echtzeit)
function si_naechster($ticks)
{
	$start = (int)(floor(time() / 60) * 60) + 60;
	for ($i = 0; $i < 24 * 60; $i++) {
		$t = $start + $i * 60;
		if (in_array((int)date('i', $t), array_map('intval', $ticks[(int)date('G', $t)] ?? array()))) {
			return $t;
		}
	}
	return 0;
}

//Anzahl Ticks pro Tag
function si_anzahl($ticks)
{
	$n = 0;
	for ($h = 0; $h <= 23; $h++) {
		$n += count($ticks[$h] ?? array());
	}
	return $n;
}

function si_tabelle($ticks)
{
	$html = '<div class="si-plan">';
	foreach (si_plan($ticks) as [$von, $bis, $minuten]) {
		$html .= '<span class="si-stunden">'.($von == $bis ? sprintf('%02d', $von).' Uhr' : sprintf('%02d&ndash;%02d', $von, $bis).' Uhr').'</span>';
		$html .= '<span class="si-minuten">'.(count($minuten) > 6 && count(array_unique(array_map(fn($a, $b) => $b - $a, array_slice($minuten, 0, -1), array_slice($minuten, 1)))) == 1
			? 'alle '.($minuten[1] - $minuten[0]).' Minuten ab Minute '.$minuten[0]
			: 'Minute '.implode(', ', $minuten)).'</span>';
	}
	return $html.'</div>';
}

rahmen_oben('Informationen zum Server');
echo '<div class="mod si"><div class="si-text">'.$deSystem['server_information'].'</div></div>';
rahmen_unten();

rahmen_oben('Tickzeiten');
echo '<div class="mod si">';
$naechster_wt = si_naechster($GLOBALS['wts']);
$naechster_kt = si_naechster($GLOBALS['kts']);
echo '<div class="si-kacheln">';
echo '<div class="ov-wert"><span class="mod-typ">N&auml;chster Wirtschaftstick</span><b>'.($naechster_wt > 0 ? \DieEwigen\DE2\View\RealTime::at($naechster_wt) : '&ndash;').'</b><small>zuletzt '.\DieEwigen\DE2\View\RealTime::at(strtotime($deSystem['lasttick'])).' &middot; '.si_anzahl($GLOBALS['wts']).' WT am Tag</small></div>';
echo '<div class="ov-wert"><span class="mod-typ">N&auml;chster Kampftick</span><b>'.($naechster_kt > 0 ? \DieEwigen\DE2\View\RealTime::at($naechster_kt) : '&ndash;').'</b><small>zuletzt '.\DieEwigen\DE2\View\RealTime::at(strtotime($deSystem['lastmtick'])).' &middot; '.si_anzahl($GLOBALS['kts']).' KT am Tag</small></div>';
echo '</div>';
echo '<div class="si-abschnitt"><span class="mod-typ">Wirtschaftsticks (WT)</span>'.si_tabelle($GLOBALS['wts']).'</div>';
echo '<div class="si-abschnitt"><span class="mod-typ">Kampfticks (KT)</span>'.si_tabelle($GLOBALS['kts']).'</div>';
echo '<div class="si-klein">Planm&auml;&szlig;ige Zeiten nach der Serveruhr.</div>';
echo '</div>';
rahmen_unten();
?>

</body>
</html>
