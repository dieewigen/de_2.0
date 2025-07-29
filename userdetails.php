<?php
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_userdetails.lang.php';
include 'functions.php';

$sql = "SELECT restyp01, restyp02, restyp03, restyp04,  restyp05, score, sector, `system`, newtrans, newnews FROM de_user_data WHERE user_id=?";
$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$ums_user_id]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row["restyp01"];$restyp02=$row["restyp02"];$restyp03=$row["restyp03"];$restyp04=$row["restyp04"];$restyp05=$row["restyp05"];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];
$sector=$row["sector"];$system=$row["system"];

//include "outputlib.php";

//***************************************
?>
<!doctype html>
<html>
<head>
<title><?php echo $userdetails_lang['title']?></title>
<?php include "cssinclude.php"; ?>
<style type="text/css">
<!--
 .auswahl
   { width:176px;}

 .input
   { width:176px; }

 .inp
   { width:88px; }
-->
</style>
</head>
<body>
<?php
include "resline.php";

if($_REQUEST['save']){
	//Schreiben in die DB
	$ud_all=$_REQUEST['ud_all'];
	$ud_all = str_replace('\r\n', "\r\n", $ud_all);
	$ud_all = htmlspecialchars(stripslashes($ud_all), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
	$ud_all = str_replace('\"', '&quot;', $ud_all);
	$ud_all = str_replace('\'', '&acute;', $ud_all);
	//$ud_all = nl2br($ud_all);
	
	$ud_sector=$_REQUEST['ud_sector'];
	$ud_sector = str_replace('\r\n', "\r\n", $ud_sector);
	$ud_sector = htmlspecialchars(stripslashes($ud_sector), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
	$ud_sector = str_replace('\"', '&quot;', $ud_sector);
	$ud_sector = str_replace('\'', '&acute;', $ud_sector);
	//$ud_sector = nl2br($ud_sector);	

	$ud_ally=$_REQUEST['ud_ally'];
	$ud_ally = str_replace('\r\n', "\r\n", $ud_ally);
	$ud_ally = htmlspecialchars(stripslashes($ud_ally), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
	$ud_ally = str_replace('\"', '&quot;', $ud_ally);
	$ud_ally = str_replace('\'', '&acute;', $ud_ally);
	//$ud_ally = nl2br($ud_ally);	
	
	
	$sql = "UPDATE de_user_info SET ud_all=?, ud_sector=?, ud_ally=? WHERE user_id=?";
	mysqli_execute_query($GLOBALS['dbi'], $sql, [$ud_all, $ud_sector, $ud_ally, $ums_user_id]);

	echo '<div class="info_box text3">'.$userdetails_lang['msg_3'].'</div><br><br>';
}

//Lesen aus der DB
$sql = "SELECT * FROM de_user_info WHERE user_id=?";
$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$ums_user_id]);
$row = mysqli_fetch_assoc($db_daten);

$ud_all=$row['ud_all'];
$ud_sector=$row['ud_sector'];
$ud_ally=$row['ud_ally'];


echo '<div class="cell" style="width: 580px;">';
echo $userdetails_lang['msg_5'];
echo '</div><br>';

echo "<form action='userdetails.php' method='post' name='details'>";
echo '<input type="hidden" name="save" value="1">';
rahmen_oben('Deine Spielerdetails');

echo '<div class="cell" style="width: 550px;">';

echo 'Hier kannst Du Informationen (z.B. Onlinezeiten, Kontaktm&ouml;glichkeit) f&uuml;r andere Spieler hinterlegen (es sind jeweils maximal 10.000 Zeichen erlaubt).<br>';
//alle
echo 'Informationen die alle sehen k&ouml;nnen:<br>';
echo '<textarea name="ud_all" maxlength="10000" style="width: 100%; height: 200px;">'.$ud_all.'</textarea>';
//sektor
echo 'Informationen die Dein Sektor sehen kann:<br>';
echo '<textarea name="ud_sector" maxlength="10000" style="width: 100%; height: 200px;">'.$ud_sector.'</textarea>';
//allianz/allianzpartner
echo 'Informationen die Deine Allianz sehen kann:<br>';
echo '<textarea name="ud_ally" maxlength="10000" style="width: 100%; height: 200px;">'.$ud_ally.'</textarea>';



echo '<br><br><br><a style="margin-left: auto; margin-right: auto;" href="javascript:document.details.submit();" name="btnclick" class="btn">'.$userdetails_lang['detailsaendern'].'</a>';

echo '</div>';
rahmen_unten();

echo '</form>';

//Anzeigen der Daten in den Inputfeldern zum Editieren
?>
<?php include "fooban.php"; ?>
</body>
</html>