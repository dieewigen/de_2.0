<?php
//        --------------------------------- ally_message.php ---------------------------------
//        Funktion der Seite:                Versenden/weiterleiten einer Mitteilung an alle Allianzmember
//        Letzte &Auml;nderung:                27.02.2003
//        Letzte &Auml;nderung von:        Guardian
//
//        &Auml;nderungshistorie:
//
//        05.02.2002 (Ascendant)        - Erweiterung der Befugnis zum Versenden
//                                                          auf Coleader
//        27.02.2003 (Guardian)         - Interface zum weiterleiten von Userhfns eingebaut und
//                                        Insertanweisung des vorhandenen Codes angepasst.
//  --------------------------------------------------------------------------------
include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.message.lang.php');
include_once('functions.php');

$db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag 
     FROM de_user_data 
     WHERE user_id=?",
    [$ums_user_id]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];$punkte=$row['score'];
$newtrans=$row['newtrans'];$newnews=$row['newnews'];$sector=$row['sector']; $system=$row['system'];
$allytag=$row['allytag'];

function insertmessage($message,$color){
  global $allymessage_lang;
  if($color=="r")$col = "FF0000";
  if($color=="g")$col = "00FF00";
  if($color=="b")$col = "3399FF";

  $nachricht = '<br><br>
    <table border="0" cellpadding="0" cellspacing="0" width="550">
    <tr>
    <td width="13" height="35" class="rol"></td>
    <td align=center height="35" class="ro"><font size=3><b>'.$allymessage_lang['systemnachricht'].'</b></font></td>
    <td width="13" height="35" class="ror"></td>
    </tr>
    <tr><td width="13" class="rl" height=35></td>
    <td align="center" nowrap class="c"><font color='. $col .'>'. $message.'</font></td>
    <td width="13" class="rr" height=35></td></tr>
    <tr>
    <td width="13" class="rul">&nbsp;</td>
    <td class="ru">&nbsp;</td>
    <td width="13" class="rur">&nbsp;</td>
    </tr>
    </table><br><br>';

  return $nachricht;
}



?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allymessage_lang['title']?></title>
<?php include "cssinclude.php"; ?>
<META http-equiv=Content-Type content="text/html; charset=windows-1252">
</head>
<body>

<font face="tahoma" style="font-size:8pt;">

<?php
include('resline.php');
$leaderpage = true;
include('ally/ally.menu.inc.php');

$action=$_REQUEST['action'] ?? '';

if($action=="del"){//nachricht löschen
	$se=(int)$se;
	$sy=(int)$sy;
	if(!preg_match("/^[0-9]*$/i", $t))$t='';

	mysqli_execute_query($GLOBALS['dbi'],
		"DELETE FROM de_hfn_usr_ally WHERE allytag=? AND fromsec=? AND fromsys=? AND time=?",
		[$allytag, $se, $sy, $t]);

	echo insertmessage($allymessage_lang['msg_1'],"g");
}

$ak=$_POST['ak'] ?? false;
$deak=$_POST['deak'] ?? false;

if ($ak || $deak){//HFNs direkt weiterleiten?
	if($ak)mysqli_execute_query($GLOBALS['dbi'],
		"UPDATE de_allys SET hfn_forwarding=1 WHERE allytag=?",
		[$allytag]);
	if($deak)mysqli_execute_query($GLOBALS['dbi'],
		"UPDATE de_allys SET hfn_forwarding=0 WHERE allytag=?",
		[$allytag]);

	if($ak)echo insertmessage($allymessage_lang['msg_2'],"g");
	if($deak)echo insertmessage($allymessage_lang['msg_3'],"r");
}

if($action=="fw"){
	//$text=nl2br($_POST['text']);

	$db_tfn = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT absender, fromsec, fromsys, fromnic, betreff, text 
		 FROM de_hfn_usr_ally 
		 WHERE allytag=? AND fromsec=? AND fromsys=? AND time=? 
		 ORDER BY time DESC",
		[$allytag, $se, $sy, $t]);
	$rowtfn = mysqli_fetch_assoc($db_tfn);

	$time=strftime("%Y%m%d%H%M%S");

	$absender = $rowtfn['absender'];
	$fromsec = $rowtfn['fromsec'];
	$fromsys = $rowtfn['fromsys'];
	$fromnic = $rowtfn['fromnic'];
	$time=strftime("%Y%m%d%H%M%S");
	$betreff = $rowtfn['betreff'];
	$text = $rowtfn['text'];


	$resource = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT user_id FROM de_user_data WHERE allytag=? AND status=1",
		[$allytag]);

	while($row = mysqli_fetch_assoc($resource)){
		mysqli_execute_query($GLOBALS['dbi'],
			"UPDATE de_user_data SET newtrans=1 WHERE user_id=?",
			[$row['user_id']]);
		mysqli_execute_query($GLOBALS['dbi'],
			"INSERT INTO de_user_hyper (empfaenger, absender, fromsec, fromsys, fromnic, time, betreff, text, sender) 
			 VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)",
			[$row['user_id'], $absender, $fromsec, $fromsys, $fromnic, $time, $betreff, $text]);
	}

	mysqli_execute_query($GLOBALS['dbi'],
		"DELETE FROM de_hfn_usr_ally WHERE allytag=? AND fromsec=? AND fromsys=? AND time=?",
		[$allytag, $se, $sy, $t]);
	echo insertmessage($allymessage_lang['msg_4'],"g");

}

$send=$_POST['send'] ?? false;
if($send){
	$text=nl2br($_POST['text']);
	$betreff=$_POST['betreff'];

	$resource = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT user_id FROM de_user_data WHERE allytag=? AND status=1",
		[$allytag]);
	$time=strftime("%Y%m%d%H%M%S");
	while($row = mysqli_fetch_assoc($resource)) {
		mysqli_execute_query($GLOBALS['dbi'],
			"UPDATE de_user_data SET newtrans=1 WHERE user_id=?",
			[$row['user_id']]);
		mysqli_execute_query($GLOBALS['dbi'],
			"INSERT INTO de_user_hyper (empfaenger, absender, fromsec, fromsys, fromnic, time, betreff, text, sender) 
			 VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)",
			[$row['user_id'], $ums_user_id, $sector, $system, $ums_spielername, $time, 'Allianzrundmail: '.$betreff, $text]);
	}
	echo insertmessage($allymessage_lang['msg_5'],"g");

}else{

?>




<form method="POST" action="ally_message.php">

<table border="0" width="600px" cellspacing="1" cellpadding="0">

<tr class="tc">
<td width="50%" height="21" colspan="2"><?php echo $allymessage_lang['allymultimessage'];?></td>
</tr>

    <tr class="cl">

      <td width="50%"><?php echo $allymessage_lang['betreff'];?>:</td>

      <td width="50%"><input name="betreff" size="20"></td>

    </tr>

    <tr class="cl">

      <td width="50%"><?php echo $allymessage_lang['nachricht'];?>:</td>

      <td width="50%"><textarea rows="5" name="text" cols="30"></textarea></td>

    </tr>

<tr class="tc">
<td width="50%" height="21" colspan=2><?php echo $allymessage_lang['msg_6'];?>.</td>
</tr>

<tr class="tc">
<td width="50%" height="21" colspan=2><input type="submit" value="<?php echo $allymessage_lang['abschicken'];?>" name="send"><input type="reset" value="<?php echo $allymessage_lang['zurueck'];?>" name="B2"></td>
</tr>
</table>
</form>
<BR>


<form method="POST" action="ally_message.php">

  <table border="0" width="400" cellspacing="1" cellpadding="0">

<tr class="tc">
<td width="50%" height="21" colspan="2"><b><?php echo $allymessage_lang['hfnmode'];?></b></td>
</tr>

<tr class="tc">
<td width="50%" align="center" colspan="2">
<?php
$hfnforward = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT hfn_forwarding FROM de_allys WHERE allytag=?",
    [$allytag]);
$rowhfnforward = mysqli_fetch_assoc($hfnforward);

if($rowhfnforward['hfn_forwarding']=="1")
{
echo '<input type=submit style="background-color:#000000;border:1;border-color:#FF0000;border-style:solid;color:#FF0000" name="deak" value="'.$allymessage_lang['msg_7'].'">';
}
else
{
echo '<input type=submit  style="background-color:#000000;border:1;border-color:#00FF00;border-style:solid;color:#00FF00" name="ak" value="'.$allymessage_lang['msg_8'].'">';
}
?>
</td>
</tr>
</table>
</form>
<BR><BR><BR>




  <table border="0" width="400" cellspacing="2" cellpadding="0" >

<tr class="tc">
<td height="21" colspan="2"><b><?php echo $allymessage_lang['msg_9']?></b></td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>



      <?php
      $db_memhfnmem = mysqli_execute_query($GLOBALS['dbi'],
          "SELECT absender, fromsec, fromsys, fromnic, time, betreff, text 
           FROM de_hfn_usr_ally 
           WHERE allytag=?",
          [$allytag]);

      $num = mysqli_num_rows($db_memhfnmem);

      if($num==0)
      {
          echo '<tr class="cell"><td colspan="2" align="center"> '.$allymessage_lang['msg_10'].'</td></tr><tr><td colspan="2">&nbsp;</td></tr>';
      }
      else
      {
          while($hfnrow = mysqli_fetch_assoc($db_memhfnmem))
      {
      $t=$hfnrow['time'];
      $time=$t[6].$t[7].'.'.$t[4].$t[5].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[8].$t[9].':'.$t[10].$t[11].':'.$t[12].$t[13];
      ?>

		<tr><td width="50" class="cell">&nbsp;<b><?php echo $allymessage_lang['absender']; ?></b></td><td class="cell1"><font face="Times New Roman" size="2">&nbsp;<?php echo $hfnrow['fromsec'].':'.$hfnrow['fromsys'].' ('.$hfnrow['fromnic'].')'; ?></font></td></tr>
		<tr><td class="cell1">&nbsp;<b><?php echo $allymessage_lang['datum']; ?></b></td><td class="cell">&nbsp;<?php echo $time; ?></td></tr>
		<tr><td class="cell">&nbsp;<b><?php echo $allymessage_lang['betreff']; ?></b></td><td class="cell1">&nbsp;<?php echo $hfnrow['betreff']; ?></td></tr>
		<tr><td valign="top"  class="cell1">&nbsp;<b><?php echo $allymessage_lang['nachricht']; ?></b></td><td class="cell">&nbsp;<?php echo $hfnrow['text']; ?></td></tr>
		<?php echo '<tr><td colspan="2" align="center" class="cell" nowrap><a href="ally_message.php?action=fw&se='.$hfnrow['fromsec'].'&sy='.$hfnrow['fromsys'].'&t='.$hfnrow['time'].'"><b><font color="#00FF00">'.$allymessage_lang['msg_11'].'</font></b></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="ally_message.php?action=del&se='.$hfnrow['fromsec'].'&sy='.$hfnrow['fromsys'].'&t='.$hfnrow['time'].'"><b><font color="#FF0000">'.$allymessage_lang['loeschen'].'</font></b></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="details.php?se='.$hfnrow['fromsec'].'&sy='.$hfnrow['fromsys'].'><b>'.$hfnrow['fromnic'].' '.$allymessage_lang['antworten'].'"</b></a></td></tr>'; ?>
		<tr><td colspan="2">&nbsp;</td></tr>
      <?php
      }
      }


      ?>


<tr class="cell"><td colspan="2">&nbsp;</td></tr>
</table>

<?php
}
?>
<br>
<?php include('ally/ally.footer.inc.php'); ?>
<?php include('fooban.php'); ?>
</body>
</html>