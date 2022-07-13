<?php
if(!empty($_REQUEST['id'])) { $id = $_REQUEST['id']; }else $id = '';
if(!empty($_REQUEST['bar'])) { $bar = $_REQUEST['bar']; }else $bar = '';
if(!empty($_REQUEST['action'])) { $action = $_REQUEST['action']; }else $action = '';

if($id !='' or $bar == "yes") { include ('inc/header.inc.php'); }

include('inc/lang/'.$sv_server_lang.'_vote.lang.php');

$db_daten_vote=mysql_query("SELECT submit FROM de_user_info WHERE user_id='$ums_user_id'",$db);
$row_vote = mysql_fetch_array($db_daten_vote);
$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, system, newtrans, newnews, tick FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);

$restyp01 = $row[0];
$restyp02 = $row[1];
$restyp03 = $row[2];
$restyp04 = $row[3];
$restyp05 = $row[4];
$punkte = $row["score"];
$newtrans = $row['newtrans'];
$newnews = $row['newnews'];
$gespielteticks = $row['tick'];
$sector = $row['sector'];
$system = $row['system'];

if ($newtrans == 1) { //wenn einen neue nachricht vorlag, den indikator wieder auf 0 setzen
  mysql_query("UPDATE de_user_data SET newtrans = 0 WHERE user_id='$ums_user_id'",$db);
}
$newtrans=0;

if($id !='' or $bar == "yes") { include('functions.php'); }

?>
<!doctype html>
<html>
<head>
<title><?php echo $vote_lang['title']; ?></title>
<?php include('cssinclude.php');

//ein bisschen CSS f&uuml;r die Buttons
?>
<body>
<center>
<?php
if($bar == "yes"){
	include('resline.php');
}


if(!empty($subform)) {

	$db_check = mysql_query("SELECT vote_id FROM de_vote_stimmen where user_id='$ums_user_id' and vote_id='$id'");
	$vote_aktiv = mysql_query("SELECT id,status FROM de_vote_umfragen where id='$id'");

	$aktiv = mysql_fetch_array($vote_aktiv);

	$menge = mysql_num_rows($db_check);

	if($menge == "0" && $aktiv['status'] == "1"){
		if($vote != "0" && $vote != ""){
			echo '<h2>'.$vote_lang['msg_3'].'</h2>';
			mysql_query("INSERT INTO de_vote_stimmen (user_id, vote_id, votefor) VALUES ('$ums_user_id','$id', '$vote')",$db);
			$_SESSION['ums_vote']=0;
		}else{
			echo $vote_lang['msg_4'];
		}
	}else{
		echo '<h1>'.$vote_lang['msg_5'].'</h1>';
	}
}

if($action == "" or $action == "uebersicht")
{
?>
<br><br>
<table border="0" cellpadding="0" cellspacing="0" width="500">
<tr height="37" align="center">
<td class="rol">&nbsp;</td>
<td class="ro"><div class="cellu"><?php echo $vote_lang['aktuelleumfragen']; ?></div></td>
<td class="ror">&nbsp;</td>
</tr>

<?php
$schonabgestimmt = mysql_query("SELECT vote_id FROM de_vote_stimmen where user_id='$ums_user_id'");

$i=0;
$gevotetevotes = array();
while($rew = mysql_fetch_array($schonabgestimmt))
{
$gevotetevotes[$i] = $rew['vote_id'];
$i++;
}
$i=0;
$votevorhanden=0;


$db_umfrage=mysql_query("SELECT de_vote_umfragen.id, de_vote_umfragen.frage,de_vote_umfragen.startdatum FROM de_vote_umfragen, de_login where de_vote_umfragen.status=1 and UNIX_TIMESTAMP(de_login.register)<UNIX_TIMESTAMP(de_vote_umfragen.startdatum) and de_login.user_id='$ums_user_id' order by de_vote_umfragen.id");
while($row = mysql_fetch_array($db_umfrage)){

    $i=0;
	while($i<=count($gevotetevotes)+1){
		if($gevotetevotes[$i] == $row['id'])
		{
		$schongestimmt=1;
		}
		$i++;
	}

	if($schongestimmt != "1"){
		echo '<tr align="center"><td class="rl" width="13">&nbsp;</td>';
		echo '<td class="cell"><a href="vote.php?action=abstimmen&id='.$row['id'].'">'.utf8_encode($row['frage']).'</a></td><td class="rr" width="13">&nbsp;</td></tr>';
		$votevorhanden=1;
	}

     $schongestimmt=0;
}

if($votevorhanden == "0"){
	echo '<tr align="center"><td class="rl" width="13">&nbsp;</td><td><div class="cell">'.$vote_lang['msg_2'].'<br><a href="overview.php">weiter</a></div></td><td class="rr" width="13">&nbsp;</td></tr>';
}
?>
<tr height="20">
<td class="rul" width="13">&nbsp;</td>
<td class="ru" colspan="1">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<br><br><br>
<table border="0" cellpadding="0" cellspacing="0" width="500">
<tr height="37" align="center">
<td class="rol">&nbsp;</td>
<td class="ro"><div class="cellu"><?php echo $vote_lang['alteumfragen']; ?></div></td>
<td class="ror">&nbsp;</td>
</tr>


<?php
$alteumfragenvorhanden = 0;
$db_alteumfragen = mysql_query("Select id,frage from de_vote_umfragen where status=2");
while($row = mysql_fetch_array($db_alteumfragen))
{
echo '<tr align="center"><td class="rl" width="13">&nbsp;</td><td><div class="cellu"><a href="vote.php?action=show&id='.$row['id'].'">'.utf8_encode($row['frage']).'</a></div></td><td class="rr" width="13">&nbsp;</td></tr>';
$alteumfragenvorhanden++;
}

if($alteumfragenvorhanden == "0")
{
echo '<tr align="center"><td class="rl" width="13">&nbsp;</td><td>'.$vote_lang['msg_1'].'</td><td class="rr" width="13">&nbsp;</td></tr>';
}
?>
<tr height="20">
<td class="rul" width="13">&nbsp;</td>
<td class="ru" colspan="1">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>


<?php
}
elseif($action == "show")
{
$db_checkobende = mysql_query("Select frage, antworten, hinweis, stimmen, status, startdatum, enddatum, ergebnisse from de_vote_umfragen where status=2 and id='$id'");

              if(mysql_num_rows($db_checkobende)>0)
              {

                      $row=mysql_fetch_array($db_checkobende);
                      ?>
                      <br><br>
                      <a href="javascript:history.back()"><div class="cellu"><b><?php echo $vote_lang['zzu']; ?></b></div></a>
                      <br><br>
                      <table border="0" cellpadding="0" cellspacing="0" width="600">
                      <tr height="37" align="center">
                      <td class="rol">&nbsp;</td>
                      <td class="ro" colspan="1"><div class="cellu"><?php echo utf8_encode($row['frage']); ?></div></td>
                      <td class="ror">&nbsp;</td>
                      </tr>
                      <tr height="20">
                      <td class="rl" width="13">&nbsp;</td>
                      <td>


                               <table border="0" width="100%"  cellspacing="2" cellpadding="0">

                               <tr height="20">
                               <td class="cell"  cellspacing="2">&nbsp;<?php $vote_lang['start']; ?>: <? echo str_replace(" ","&nbsp;&nbsp;/&nbsp;&nbsp;",$row['startdatum']); ?></td>
                               <td class="cell"  cellspacing="2" align="center">
                               <?php
                               //echo $vote_lang[msg_6_1].' ';
                               $stimmen = explode("|",$row['stimmen']);
                               //echo number_format(($stimmen[0]*100)/$stimmen[1],2,",",".")."%";
                               //echo ' '.$vote_lang[msg_6_2];

                               ?>
                               </td>
                               </tr>

                               <tr height="25">
                               <td class="cell">&nbsp;<?php echo $vote_lang['ende']; ?>: <? echo str_replace(" ","&nbsp;&nbsp;/&nbsp;&nbsp;",$row['enddatum']); ?></td>
                               <td  cellspacing="2"  class="cell">&nbsp;</td>
                               </tr>

                               <tr height="25">
                               <td colspan="4" class="cell">&nbsp;<?php echo utf8_encode($vote_lang['hinweis']); ?>: <?php echo nl2br(utf8_encode($row['hinweis'])); ?></td>
                               </tr>
                               <tr>
                               <td colspan="4">
                               <table border="0" width="100%" cellspacing="2" cellpadding="0">
                               <?php

                               $antworten = explode("|",$row['antworte'n]);
                               $ergebnisse = explode("|",$row['ergebnisse']);
                               $i=0;
                               $farbe=0;
                               while($i<count($antworten))
                               {

                               echo '
                               <tr  class="cell">
                               <td height="25">&nbsp;'.utf8_encode($antworten[$i]).'</td><td>&nbsp;';
                               $prozente = number_format(($ergebnisse[$i]*100)/$stimmen['0'],2,",",".");
                               ?>
                               <img src="<?php echo $ums_gpfad; ?>g/vote/l<?php echo $farbe; ?>.gif" border="0"><img src="<?php echo $ums_gpfad; ?>g/vote/m<?php echo $farbe; ?>.gif" border="0" width="<?php echo $prozente; ?>" height="9"><img src="<?php echo $ums_gpfad; ?>g/vote/r<?php echo $farbe; ?>.gif">
                               <?
                               echo '</td><td width="40" nowrap>&nbsp;'.$ergebnisse[$i].'</td><td width="50" nowrap>';



                               echo '&nbsp;'.$prozente.'%</td></tr>';

                               $stimmengesamt = $stimmengesamt + $ergebnisse[$i];

                               $prozentegesamt = $prozentegesamt + (($ergebnisse[$i]*100)/$stimmen['0']);

                               if($farbe == 0)
                               {
                               $farbe = 1;
                               }
                               else
                               {
                               $farbe = 0;
                               }

                               $i++;
                               }

                               echo '<tr class="cell"  height="25"><td colspan="2" align="right"><b>'.$vote_lang['insgesamt'].':</b>&nbsp;&nbsp;</td><td>&nbsp;'.$stimmengesamt.'</td><td>&nbsp;'.$prozentegesamt.'%</td></tr>';

                                 ?>
                                 </table>
                                 </td>
                                 </tr>

                               </table>


                      </td>
                      <td class="rr" width="13">&nbsp;</td>
                      </tr>
                      <tr height="20">
                      <td class="rul" width="13">&nbsp;</td>
                      <td class="ru" colspan="1">&nbsp;</td>
                      <td class="rur" width="13">&nbsp;</td>
                      </tr>
                      </table>
                      <?
              }
              else
              {
              echo "<h1>".$vote_lang['msg_7']."</h1>";
              @$time=strftime("%Y-%m-%d %H:%M:%S");
              @$para = "Master Guardian, \n Der folgende Spieler $ums_spielername ($asec:$asys)[UserID:$ums_user_id] hat am $zeit versucht, sich noch andere inaktive Umfragen anzeigen zulassen";
              #@mail("ThE_GuArDiAn@Die-Ewigen.com","Mogler am Votescript entdeckt.",$para);

              }

}
elseif($action == "abstimmen")
{

$db_umfrage=mysql_query("SELECT id, frage, hinweis, antworten FROM de_vote_umfragen where id='$id' and status=1");
$row = mysql_fetch_array($db_umfrage);
$vorhanden = mysql_num_rows($db_umfrage);

if($vorhanden != "0")
{

echo '
<a href="javascript:history.back()">
   <h4>'.$vote_lang['zzu'].'</h4>
</a>
<table width="400">
   <tr>
     <td class="cell">
       <fieldset>
          <legend><b><font class="text" size="+2">'.utf8_encode($vote_lang['hinweis']).'</font></b></legend>
          '. utf8_encode(nl2br($row['hinweis'])).'
       </fieldset>
     </td>
   </tr>
</table>
<br><br>
<form action="vote.php" method="post">
<table border="0" width="400" cellspacing="0" cellpadding="0">
<tr>
<td width="13" height="35" class="rol">&nbsp;</td>
<td align="center" class="ro">'.utf8_encode($row['frage']).'</td>
<td width="13" height="25" class="ror"></td></tr>';

$antworten = explode("|",$row['antworten']);
$i=0;
while($i < count($antworten))
{
echo '<tr><td width="13" class="rl" height="35"></td><td align="left" class="cell"><input type="Radio" name="vote" value="'. ($i+1).'">&nbsp;&nbsp;&nbsp;'.utf8_encode($antworten[$i]).'</td><td width="13" class="rr" height="35"></td>';
$i++;
}
echo '<tr>
<td width="13" class="rl">&nbsp;</td>
<td align="center" class="cell"><input type="submit" name="subform" value="'.$vote_lang['stimmeabgeben'].'" class="buttons"></td>
<td width="13" class="rr">&nbsp;</td>
</tr>';
echo '<tr>
<td width="13" class="rul">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td width="13" class="rur">&nbsp;</td>
</tr>';
echo '</table><input type="hidden" name="id" value="'.$row['id'].'"></form>';
}
else
{
              echo "<h1>".$vote_lang['msg_7']."</h1>";
              @$time=strftime("%Y-%m-%d %H:%M:%S");
              @$para = "Master Guardian, \n Der folgende Spieler $ums_spielername ($asec:$asys)[UserID:$ums_user_id] hat am $zeit versucht, sich noch andere Umfragen anzeigen zulassen";
              #@mail("ThE_GuArDiAn@Die-Ewigen.com","Mogler am Votescript entdeckt.",$para);

}
}
?>
<br><br>
</center>
<?php include('fooban.php'); ?>
</body>
</html>
