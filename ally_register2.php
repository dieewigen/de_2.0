<?php
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_ally.registerzwei.lang.php';
include 'functions.php';
include('outputlib.php');

$db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag, spielername 
     FROM de_user_data 
     WHERE user_id=?",
    [$ums_user_id]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];$punkte=$row['score'];
$newtrans=$row['newtrans'];$newnews=$row['newnews'];$sector=$row['sector'];$system=$row['system'];
$spielername=$row['spielername'];$allytag=$row['allytag'];
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allyregisterzwei_lang['title']?></title>
<?php include "cssinclude.php"; ?>
</head>
<body>
<?php
include "resline.php";

if($allytag!=''){
	echo '<div class="info_box text2">Du kannst keine Allianz gr&uuml;nden, da Du Dich bereits in einer Allianz befindest bzw. beworben hast.</div></body></html>';
	exit;	
}

//leerzeichen entfernen
$clanname = trim($_POST['clanname']);
$clankuerzel = trim($_POST['clankuerzel']);
$regierungsform = trim($_POST['regierungsform']);
$allianzform = trim($_POST['allianzform']);
$ausrichtung = trim($_POST['ausrichtung']);
$hp = trim($_POST['hp']);
$bio = trim($_POST['bio']);

$eintragung=1;

if($clanname==""){
	$eintragung=0;
	$errormessage.=$allyregisterzwei_lang['msg_1']."<br>";
}

if($clankuerzel==""){
	$eintragung=0;
	$errormessage.=$allyregisterzwei_lang['msg_2']."<br>";

}

if( strlen($clankuerzel) > 8 ){
	$eintragung=0;
	$errormessage.=$allyregisterzwei_lang['msg_3']."<br>";
}

if (preg_match("/[^0-9a-zA-Z]/", $clankuerzel))
{
    $eintragung=0;
    $errormessage.="Kürzel darf keine Sonderzeichen enthalten!<br>";
}

$result = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT * FROM de_allys WHERE allytag=?",
    [$clankuerzel]);

$nb = mysqli_num_rows($result);

if($nb>0){
	$eintragung=0;
	$errormessage.=$allyregisterzwei_lang['msg_4']."<br>";
}


$result = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT * FROM de_allys WHERE allyname=?",
    [$clanname]);

$nb = mysqli_num_rows($result);
if($nb>0){
	$eintragung=0;
	$errormessage.=$allyregisterzwei_lang['msg_5']."<br>";
}

$result = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT * FROM de_allys WHERE leaderid=?",
    [$ums_user_id]);

if(mysqli_num_rows($result)>0){
	$eintragung=0;
	$errormessage.=$allyregisterzwei_lang['msg_6']."<br>";
}

if($eintragung==1){

	//Ally in der DB hinterlegen
	$clanname = utf8_decode($clanname);
	$clankuerzel = utf8_decode($clankuerzel);
	$bio = utf8_decode($bio);
	$result = mysqli_execute_query($GLOBALS['dbi'],
		"INSERT INTO de_allys (allyname, allytag, regierungsform, allianzform, ausrichtung, leaderid, homepage, besonderheiten, leadermessage, bewerberinfo, discord_bot) 
		 VALUES (?, ?, ?, ?, ?, ?, ?, ?, '', '', '')",
		[$clanname, $clankuerzel, $regierungsform, $allianzform, $ausrichtung, $ums_user_id, $hp, $bio]);
	$ally_id = mysqli_insert_id($GLOBALS['dbi']);

	echo '<div class="info_box text3">'.$allyregisterzwei_lang['msg_7'].'</div><br><br>';

	//beim Allyleader die entsprechenden Daten hinterlegen
	mysqli_execute_query($GLOBALS['dbi'],
		"UPDATE de_user_data SET ally_id=?, allytag=?, status=1 WHERE user_id=?",
		[$ally_id, utf8_decode($clankuerzel), $ums_user_id]);
	
	include('ally/allyfunctions.inc.php');
	writeHistory($clankuerzel, $allyregisterzwei_lang['msg_8_1']." <i>".$clanname."</i> ".$allyregisterzwei_lang['msg_8_2']." <i>".$spielername."</i> ".$allyregisterzwei_lang['msg_8_3']);

	//aktueller Maximalwert
	$db_daten = mysqli_execute_query($GLOBALS['dbi'], 
		"SELECT MAX(memberlimit) AS max FROM de_allys");
	$row = mysqli_fetch_assoc($db_daten);
	$max=$row['max'];

	$memberlimit=round(5+$user/20);
	if($memberlimit<$max){
		$memberlimit=$max;
	}

	//memberlimit in der DB hinterlegen
	mysqli_execute_query($GLOBALS['dbi'], 
		"UPDATE de_allys SET memberlimit=?", 
		[$memberlimit]);

	include('ally/ally.menu.inc.php');

}else{

	echo '<div class="info_box text2">'.$allyregisterzwei_lang['msg_9'].' '.$errormessage.'</div>';

	echo '<br><a href="allymain.php" class="btn">zur&uuml;ck</a>';

}

?>
<?php include('ally/ally.footer.inc.php'); ?>