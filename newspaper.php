<?php
include "inc/header.inc.php";
include 'functions.php';

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04,  restyp05, techs, sector, `system`, score, newtrans, newnews, secmoves FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];$techs=$row["techs"];
$secmoves=$row["secmoves"];

?>
<!doctype html>
<html>
<head>
<title>News</title>
<?php include "cssinclude.php"; ?>
</head>
<body>
<center>
<?php
include "resline.php";

$id=isset($id)?(int)$id:-1;
//news anzeigen
if($action!="archiv"){
	$sel_news_show = mysql_query("SELECT * FROM de_news_overview where id='$id'");

	$row=mysql_fetch_array($sel_news_show);

	mysql_query("UPDATE de_news_overview set klicks=klicks+1,time=time WHERE id='$id'");


	$nachricht = utf8_encode(nl2br(umlaut($row['nachricht'])));
	echo '<br>
	<table border="0" cellspacing="0" cellpadding="0" width="600px">
	<tr>
	<td width="13" height="25" class="rol"></td>
	<td align="center" height="35" class="ro"><div class="cellu">'.utf8_encode(umlaut($row['betreff'])).' (<a href="newspaper.php?action=archiv&typ='.utf8_encode($row['typ']).'">Archiv</a>)</td>
	<td width="13" height="25" class="ror"></td>
	</tr>
	<tr>
		<td width="13" class="rl" height="35"></td>
		<td><div class="cell">'.$nachricht.'</div></td>

		<td width="13" height="25" class="rr"></td>
	</tr>
	<tr>
	<td width="13" class="rul">&nbsp;</td>
	<td class="ru">&nbsp;</td>
	<td width="13" class="rur">&nbsp;</td>
	</tr>
	</table><br>';

//////////////////////////////////////
//////////////////////////////////////
// feedback formular
//////////////////////////////////////
//////////////////////////////////////

//e-mail senden
if($_REQUEST['feedback']){
	echo '<div class="info_box text3">Vielen Dank, das Feedback wurde gespeichert.</div><br>';
	$sendto=$GLOBALS['env_admin_email'];
	$betreff='Feedback: '.$row['betreff'].' '.$sv_server_tag.' '.$ums_user_id.' '.$ums_spielername;
	$text=str_replace('\r\n',"\r\n",$_REQUEST['feedback']);
	$sendfrom='FROM: '.$GLOBALS['env_admin_email'];
	@mail($sendto, $betreff, $text, $sendfrom);
}

echo '
<script>
function chkFeedback(){
if(document.newspaper.feedback.value =="Trage hier bitte Dein Feedback ein."){
alert("Gib bitte Dein Feedback ein, damit wir das Spiel weiter verbessern k�nnen!");
document.newspaper.feedback.focus();
return false;
}
}
</script>';

//echo '<form action="newspaper.php" method="POST">';
echo '<form action="newspaper.php" method="POST" name="newspaper" onSubmit="return chkFeedback()">';
echo '<input type="hidden" name="id" value="'.$id.'">';
rahmen_oben('Feedback zum Beitrag');
echo '<div class="cell" style="width: 575px;">';
echo 'Wenn Du gerne Feedback zu diesem Beitrag geben m&ouml;chtest, so empfehlen wir daf&uuml;r das Forum. 
Solltest Du Dich aber nicht trauen &ouml;ffentlich etwas zu schreiben, dann kannst Du auch dieses Feedback-Formular nutzen. Es werden auf jeden Fall alle Feedbacks gelesen.
<br><br><font style="font-size: 20px; color: #FF0000;">Beachte bitte, dass auf Fragen nicht geantwortet werden kann, diese kannst 
Du aber im Forum/IRC/Supportticket stellen.</font><br><br>Schreibe bitte m&ouml;glichst ausf&uuml;hrlich, damit man wei&szlig; was gemeint ist, ein einfaches "ist doof" wird zwar registriert, aber eine Begr&uuml;ndung fehlt. Des Weiteren werden keine Beleidigungen toleriert.
 <br><br><font color="#00FF00">Meldungen zu Verst&ouml;&szlig;e gegen die Nutzungsbedingungen kannst Du in der Accountverwaltung unter dem Punkt "Support" melden.
 </font>
 
 ';

//echo '<br><textarea cols="70" rows="10" name="feedback">Trage hier bitte Dein Feedback ein.</textarea>';
?>
<textarea cols="70" rows="10" name="feedback" onfocus="if(this.value == 'Trage hier bitte Dein Feedback ein.') this.value='';" onblur="if (this.value=='') this.value='Trage hier bitte Dein Feedback ein.';">Trage hier bitte Dein Feedback ein.</textarea>
<?php
echo '<br><input type="Submit" value="Feedback senden">';

echo '</div>';

rahmen_unten();
echo '</form>';

}
else  //archiv
{
?>

<br><br>
<table border="0" cellspacing="0" cellpadding="0" width="585">
<tr>
<td width="13" height="25" class="rol"></td>
<td align="center" height="35" class="ro"><div class="cellu">A r c h i v</div></td>
<td width="13" height="25" class="ror"></td>
</tr>
<tr>
    <td width="13" class="rl" height="35"></td>
    <td><div class="cell"><br>
    <?php
     $typ=(int)$typ;
     $sel_news=mysql_query("SELECT * FROM de_news_overview where typ='$typ' order by id desc");

     while($rew=mysql_fetch_array($sel_news))
     {
       $t=$rew['time'];
       $time=$t[8].$t[9].'.'.$t[5].$t[6].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[11].$t[12].':'.$t[14].$t[15].':'.$t[17].$t[18];
  echo '&nbsp;&nbsp;<a href="newspaper.php?id='.$rew['id'].'">'.$time.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.utf8_encode(umlaut($rew['betreff'])).'</a><br><br>';
     }
     ?>
     </div></td>
     <td width="13" height="25" class="rr"></td>
</tr>
<tr>
<td width="13" class="rul">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td width="13" class="rur">&nbsp;</td>
</tr>
</table>

<?php
}
?>
</center>
</body>
</html>