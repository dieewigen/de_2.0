<?php
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_userdetails.lang.php';
include 'functions.php';

$sql = "SELECT restyp01, restyp02, restyp03, restyp04,  restyp05, score, sector, `system`, newtrans, newnews FROM de_user_data WHERE user_id=?";
$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row["restyp01"];$restyp02=$row["restyp02"];$restyp03=$row["restyp03"];$restyp04=$row["restyp04"];$restyp05=$row["restyp05"];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];
$sector=$row["sector"];$system=$row["system"];

?>
<!doctype html>
<html>
<head>
<title><?php echo $userdetails_lang['title']?></title>
<?php include "cssinclude.php"; ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

include "resline.php";

if(isset($_REQUEST['save'])){
	//Schreiben in die DB
	$ud_all=$_REQUEST['ud_all'];
	$ud_all = str_replace('\r\n', "\r\n", $ud_all);
	$ud_all = htmlspecialchars(stripslashes($ud_all), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
	$ud_all = str_replace('\"', '&quot;', $ud_all);
	$ud_all = str_replace('\'', '&acute;', $ud_all);

	$ud_sector=$_REQUEST['ud_sector'];
	$ud_sector = str_replace('\r\n', "\r\n", $ud_sector);
	$ud_sector = htmlspecialchars(stripslashes($ud_sector), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
	$ud_sector = str_replace('\"', '&quot;', $ud_sector);
	$ud_sector = str_replace('\'', '&acute;', $ud_sector);

	$ud_ally=$_REQUEST['ud_ally'];
	$ud_ally = str_replace('\r\n', "\r\n", $ud_ally);
	$ud_ally = htmlspecialchars(stripslashes($ud_ally), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
	$ud_ally = str_replace('\"', '&quot;', $ud_ally);
	$ud_ally = str_replace('\'', '&acute;', $ud_ally);

	$sql = "UPDATE de_user_info SET ud_all=?, ud_sector=?, ud_ally=? WHERE user_id=?";
	mysqli_execute_query($GLOBALS['dbi'], $sql, [$ud_all, $ud_sector, $ud_ally, $_SESSION['ums_user_id']]);

	echo '<div class="mod pol-meldungen"><div class="mod-meldung mod-meldung-ok">'.$userdetails_lang['msg_3'].'.</div></div>';
}

//Lesen aus der DB
$sql = "SELECT * FROM de_user_info WHERE user_id=?";
$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($db_daten);

$ud_all=$row['ud_all'];
$ud_sector=$row['ud_sector'];
$ud_ally=$row['ud_ally'];

//ein Textfeld mit Überschrift (wer es sieht) und Zeichenzähler
function ud_feld($name, $titel, $wer, $text)
{
	return '<div class="ud-feld"><div class="ud-kopf"><label for="ud_'.$name.'">'.$titel.'</label><span class="mod-chip">'.$wer.'</span>'
		.'<span class="ud-zaehler" data-feld="ud_'.$name.'"></span></div>'
		.'<textarea name="ud_'.$name.'" id="ud_'.$name.'" maxlength="10000" class="mod-eingabe ud-text">'.$text.'</textarea></div>';
}

echo '<form action="userdetails.php" method="post" name="details">';
echo '<input type="hidden" name="save" value="1">';
rahmen_oben('Deine Spielerdetails');
echo '<div class="mod ud">';

echo '<div class="ud-text-oben">Hier kannst Du Informationen f&uuml;r andere Spieler hinterlegen, z.&nbsp;B. Onlinezeiten oder Kontaktm&ouml;glichkeiten. Sie stehen in den Spielerdetails, die man &uuml;ber die Karte, den Sektor und die Ranglisten erreicht.</div>';
echo '<div class="mod-hinweis ud-regeln">'.$userdetails_lang['msg_5'].'</div>';

echo ud_feld('all', 'F&uuml;r alle', 'alle Spieler', $ud_all);
echo ud_feld('sector', 'F&uuml;r Deinen Sektor', 'nur Dein Sektor', $ud_sector);
echo ud_feld('ally', 'F&uuml;r Deine Allianz', 'nur Deine Allianz', $ud_ally);

echo '<div class="ud-fuss"><a href="details.php?se='.$sector.'&amp;sy='.$system.'" class="mod-btn mod-btn-leise">So sehen andere Deine Details</a>';
echo '<button type="submit" name="btnclick" value="1" class="mod-btn">'.$userdetails_lang['detailsaendern'].'</button></div>';

echo '</div>';
rahmen_unten();
echo '</form>';
?>
<script>
//Zeichen je Feld (höchstens 10.000)
document.querySelectorAll('.ud-zaehler').forEach(function(z){
	var feld = document.getElementById(z.getAttribute('data-feld'));
	var zeigen = function(){ z.textContent = feld.value.length.toLocaleString('de-DE') + ' / 10.000'; };
	feld.addEventListener('input', zeigen);
	zeigen();
});
</script>
</body>
</html>
