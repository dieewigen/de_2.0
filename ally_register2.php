<?php
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_ally.registerzwei.lang.php';
include_once 'functions.php';
include('outputlib.php');

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag, spielername FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row['score'];
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

$query="SELECT * FROM de_allys WHERE allytag='$clankuerzel'";
$result= mysql_query($query);



$nb= mysql_num_rows($result);

if($nb>0){
	$eintragung=0;
	$errormessage.=$allyregisterzwei_lang['msg_4']."<br>";
}


$query="SELECT * FROM de_allys WHERE allyname='$clanname'";
$result= mysql_query($query);

$nb= mysql_num_rows($result);
if($nb>0){
	$eintragung=0;
	$errormessage.=$allyregisterzwei_lang['msg_5']."<br>";
}

$query="SELECT * FROM de_allys WHERE leaderid='$ums_user_id'";
$result= mysql_query($query);

if(mysql_num_rows($result)>0){
	$eintragung=0;
	$errormessage.=$allyregisterzwei_lang['msg_6']."<br>";
}

if($eintragung==1){

	//Ally in der DB hinterlegen
	$clanname = utf8_decode($clanname);
	$clankuerzel = utf8_decode($clankuerzel);
	$bio = utf8_decode($bio);
	$query="INSERT INTO de_allys (allyname, allytag, regierungsform, allianzform, ausrichtung, leaderid, homepage, besonderheiten, leadermessage,bewerberinfo, discord_bot) VALUES ('$clanname', '$clankuerzel', '$regierungsform', '$allianzform', '$ausrichtung', '$ums_user_id', '$hp', '$bio', '', '', '')";
	//echo $query;
	mysql_query($query);
	$ally_id=mysql_insert_id();

	echo '<div class="info_box text3">'.$allyregisterzwei_lang['msg_7'].'</div><br><br>';

	//beim Allyleader die entsprechenden Daten hinterlegen
	$query="UPDATE de_user_data SET ally_id='$ally_id', allytag='".utf8_decode($clankuerzel)."', status=1 WHERE (user_id = '$ums_user_id')";
	//echo $query;
	mysql_query($query);
	
	include('ally/allyfunctions.inc.php');
	writeHistory($clankuerzel, $allyregisterzwei_lang['msg_8_1']." <i>".$clanname."</i> ".$allyregisterzwei_lang['msg_8_2']." <i>".$spielername."</i> ".$allyregisterzwei_lang['msg_8_3']);

	//aktueller Maximalwert
	$db_daten=mysqli_query($GLOBALS['dbi'], "SELECT MAX(memberlimit) AS max FROM de_allys;");
	$row = mysqli_fetch_array($db_daten);
	$max=$row['max'];

	$memberlimit=round(5+$user/20);
	if($memberlimit<$max){
		$memberlimit=$max;
	}

	//memberlimit in der DB hinterlegen
	mysqli_query($GLOBALS['dbi'], "UPDATE de_allys SET memberlimit='$memberlimit';");

	include('ally/ally.menu.inc.php');

}else{

	echo '<div class="info_box text2">'.$allyregisterzwei_lang['msg_9'].' '.$errormessage.'</div>';

	echo '<br><a href="allymain.php" class="btn">zur&uuml;ck</a>';

}

?>
<?php include('ally/ally.footer.inc.php'); ?>