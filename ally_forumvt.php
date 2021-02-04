<?php
include "inc/header.inc.php";
include "format_sammlung.php";
include 'inc/lang/'.$sv_server_lang.'_ally_forumvt.lang.php'; 
include_once 'functions.php';

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, system, newtrans, newnews, allytag, ally_tronic FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$allytag=$row["allytag"];
$t_level = $row["ally_tronic"];

$allys=mysql_query("SELECT * FROM de_allys where leaderid='$ums_user_id'");
if(mysql_num_rows($allys)>=1)
{
        $isleader = true;
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?=$allyforumvt_lang[title]?></title>
<script src="<?=$sv_server_lang?>_jssammlung.js" type="text/javascript"></script>
<?php include "cssinclude.php"; ?>
<meta charset="iso-8859-15">
</head>
<body>
<?php
include "resline.php";
include ("ally/ally.menu.inc.php");
include "issectork.php";

if(isset($nachricht)){
	$nachricht=str_replace('\r\n', "\r\n", $nachricht);
}

/*
if($_SESSION['ums_user_id']==1){
	echo json_encode($_REQUEST);
}
*/

echo '<div class="cell">';

//echo foren_navi(basename($_SERVER['PHP_SELF']),$ums_user_id,$system);

$id=(int)$id;

$temporary=mysql_query("SELECT threadname, open, anzposts, allytag  FROM de_alliforum_threads WHERE id='$id'", $db);
$temporary = mysql_fetch_array($temporary);
if("$allytag"<>"$temporary[allytag]"){
	echo $allyforumvt_lang[msg_1];
	exit();
}

echo '<a name="oben">&nbsp;</a><br>
		<a href="javascript:history.back()"><img src="'.$ums_gpfad.'g/'.$ums_rasse.'_goback.gif" border="0" alt="'.$allyforumvt_lang[zurueck].'"></a>
		<a href="#unten"><img src="'.$ums_gpfad.'g/'.$ums_rasse.'_godown.gif" border="0" alt="'.$allyforumvt_lang[tolastpost].'"></a><br><br>
		<font size="2" face="arial"><b><a href="ally_forum.php">'.$allytag.'-'.$allyforumvt_lang[title].'</a></b> -> <b>'.$allyforumvt_lang[thema].': '.$temporary[threadname].'</b></font><br><br>';


if(!$reply && !$vorschau &&($action!="edit")){
	mysql_query("UPDATE de_alliforum_threads set hits=hits+1 WHERE id='$id'", $db);
}

if($savedit){
	$_POST[title] = db_aufbereitung($_POST[title]);
	$_POST[title]=htmlspecialchars($_POST[title], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');

	$_POST[nachricht] = db_aufbereitung($_POST[nachricht]);
	$_POST[nachricht]=htmlspecialchars($_POST[nachricht], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
	$_POST[nachricht] = nl2br_pre($_POST[nachricht]);


	$db_editt=mysql_unbuffered_query("SELECT poster,edit FROM de_alliforum_posts WHERE postid='$pid'", $db);
	$raw = mysql_fetch_array($db_editt);
	if($raw[poster]!=$ums_spielername){
	echo $allyforumvt_lang[msg_2];
	exit();
	}

	$ed = $raw[edit];
	$zeit=strftime("%Y%m%d%H%M%S");

	$ed=$zeit.'|'.$ed;
	mysqli_query($GLOBALS['dbi'], "UPDATE de_alliforum_posts set post='".($_POST[nachricht])."', title='".($_POST[title])."',edit='$ed' WHERE postid='$pid'");

	$_POST[nachricht]="";
	$_POST[title]="";
	$nachricht="";
	$title="";
	echo '<a name="unten">&nbsp;</a>';
}


if($reply){
	$_POST[nachricht] = trim($_POST[nachricht]);
	$_POST[title] = trim($_POST[title]);

	if(!$_POST[nachricht])$error.=$allyforumvt_lang[msg_3];
	if(!$_POST[title])$error.=$allyforumvt_lang[msg_4];
	if(!$ums_user_id)$error.=$allyforumvt_lang[msg_5];

	//schaue ob die threadid und der sektor g&uuml;ltig sind
	$db_daten=mysql_query("SELECT allytag FROM de_alliforum_threads WHERE id='$id'", $db);
	$num = mysql_num_rows($db_daten);
	if($num==0)$error.=$allyforumvt_lang[msg_6];
	$row = mysql_fetch_array($db_daten);
	if ("$allytag"<>"$temporary[allytag]")$error.=$allyforumvt_lang[msg_7];

	//test auf com-sperre
	$akttime=date("Y-m-d H:i:s",time());
	$db_daten=mysql_query("SELECT com_sperre FROM de_login WHERE user_id='$ums_user_id'",$db);
	$row = mysql_fetch_array($db_daten);
	if($row['com_sperre']>$akttime){
		$sperrtime=strtotime($row['com_sperre']);
		echo('<div class="info_box text2" style="margin-bottom: 5px; font-size: 14px;">Account: Sperre f&uuml;r ausgehende Kommunikation bis: '.date("d.m.Y - G:i", $sperrtime).'</div>');
	}
	elseif(!$error){

		$_POST[title] = db_aufbereitung($_POST[title]);
		$_POST[title]=htmlspecialchars($_POST[title], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');

		$_POST[nachricht] = db_aufbereitung($_POST[nachricht]);
		$_POST[nachricht]=htmlspecialchars($_POST[nachricht], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
		$_POST[nachricht] = nl2br_pre($_POST[nachricht]);

		$now=time();

		//posting eintragen
		mysql_query("INSERT INTO de_alliforum_posts (poster,post,time,thread,title,edit) VALUES ('$ums_spielername','".($_POST[nachricht])."','$now','$id','".($_POST[title])."','')", $db);


		if($_POST[wichtig]=="1")$wichtig=1; else $wichtig=0;

		//thread mit den neuen daten updaten
		mysql_query("UPDATE de_alliforum_threads set lastposter='$ums_spielername',lastactive='$now', anzposts=anzposts+1 WHERE id='$id'", $db);

		$nachricht="";
		$title=$allyforumvt_lang[re].":";

	}else{
		echo $error;
	}
}

        $sql="SELECT anzposts, open, threadname, allytag, wichtig FROM de_alliforum_threads WHERE id='$id'";
      $temporary=mysqli_query($GLOBALS['dbi'], $sql);
      $temporary = mysqli_fetch_array($temporary);

      $downcounter=0;

      $db_daten=mysqli_query($GLOBALS['dbi'], "SELECT poster, post, time, title, postid, thread, edit FROM de_alliforum_posts WHERE thread='$id' ORDER BY  time ASC");
      while($row = mysqli_fetch_array($db_daten)){
        $newstext=$row['post'];
        $posttime =date ("d.m.Y $allyforumvt_lang[msg_8_4] H:i", $row[time]);
        $newstext = formatierte_anzeige($newstext,$ums_gpfad);

        if($row[title]==""){
          $row[title]=$allyforumvt_lang[re].":";
          $row[title].=$temporary[threadname];
        }

        if(($downcounter==$temporary[anzposts]+1)&& !$vorschau){
          echo '<a name="unten">&nbsp;</a>';
        }
        $downcounter++;

        echo '<table border="0" width="500" bgcolor="black" cellpadding="0" cellspacing="1">
      <tr>
          <td width="100%" bgcolor="#202020" align="left">
              <table border="0" width="100%">
              <tr>
                  <td>
                      <font color="#E0E0E0" face="Arial" size="2"><b>'.utf8_encode(umlaut($row[title])).'</b></font>
                      <font face="Arial" size="2">- '.$allyforumvt_lang[msg_8_1].' '.$row[poster].' '.$allyforumvt_lang[msg_8_2].' '.$posttime.' '.$allyforumvt_lang[msg_8_3].'</font>
                  </td>
                  <td>';
                  if($row[poster]==$ums_spielername)
                  echo '<a href="ally_forumvt.php?action=edit&id='.$row[thread].'&pid='.$row[postid].'&#unten">'.$allyforumvt_lang[edit].'</a>';
                  else
                  echo '&nbsp;';

                  //$newstext = preg_replace("/\[img\]([^[]*)\[\/img\]/","<img src=\"\\1\" border=0>",$newstext);


echo '            </td>
             </tr>
             </table>
           </td>
        </tr>
        <tr>
            <td width="100%" bgcolor="#202020" align="left"><font color="#E0E0E0" face="Arial" size="2">'.utf8_encode(umlaut($newstext)).'</font></td>
        </tr>';

        if($row[edit]!="")
        {
        $edits = explode("|",$row[edit]);

        $male = count($edits)-1;

        $t= $edits[0];
        $time=$t[6].$t[7].'.'.$t[4].$t[5].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[8].$t[9].':'.$t[10].$t[11].':'.$t[12].$t[13];



        echo '<tr><td bgcolor="#202020" align="left"><font size="-2">&nbsp;'.$allyforumvt_lang[msg_9_1].' '.$male.' '.$allyforumvt_lang[msg_9_2].' '.$time.'.</font></td></tr>';
        }
echo '</table><br>';
}
echo '<a name="unten">&nbsp;</a><font size="2" face="arial"><b><a href="ally_forum.php">'.$allytag.'-'.$allyforumvt_lang[title].'</a></b> -> <b>'.$allyforumvt_lang[thema].': '.$temporary[threadname].'</b></font><br><br>

<a href="javascript:history.back()"><img src="'.$ums_gpfad.'g/'.$ums_rasse.'_goback.gif" border="0" alt="'.$allyforumvt_lang[zurueck].'"></a>
<a href="#oben"><img src="'.$ums_gpfad.'g/'.$ums_rasse.'_goup.gif" border="0" alt="'.$allyforumvt_lang[tofirstpost].'"></a><br><br>';

if($temporary[open]==1) {


if($vorschau or $vorschauedit){
	$newstext = $nachricht;

	$newstext = htmlspecialchars($newstext, ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
	$newstext = nl2br_pre($newstext);

	$newstext = formatierte_anzeige($newstext,$ums_gpfad);
	$newstext = preg_replace("/\[img\]([^[]*)\[\/img\]/","<img src=\"\\1\" border=0>",$newstext);

	$newstext = stripslashes($newstext);
	$nachricht = stripslashes($nachricht);

	?>
	<table width="475" border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td width="13" height="37" class="rol">&nbsp;</td>
	<td class="ro" align="center"><?=$allyforumvt_lang[vorschau]?></td>
	<td width="13" height="37" class="ror">&nbsp;</td>
	</tr>
	<tr>
	<td width="13" height="37" class="rl">&nbsp;</td>
	<td width="500"><?php echo $newstext; ?></td>
	<td width="13" class="rr">&nbsp;</td>
	</tr>
	<tr>
	<td width="13" class="rul">&nbsp;</td>
	<td class="ru">&nbsp;</td>
	<td width="13" class="rur">&nbsp;</td>
	</tr>
	</table>
	<?php
	if($vorschauedit){
		$action="edit";
		$nachricht = str_replace("<br />"," ",$nachricht);
	}
}

if($action=="edit"){
  if(!$vorschauedit){

  $db_edit=mysql_unbuffered_query("SELECT poster, post, time, title, postid FROM de_alliforum_posts WHERE postid='$pid'");
  $rew = mysql_fetch_array($db_edit);
  }
  else
  {
  $db_edit=mysql_unbuffered_query("SELECT poster FROM de_alliforum_posts WHERE postid='$pid'");
  $rew = mysql_fetch_array($db_edit);
  }
  if($rew[poster]!=$ums_spielername)
  {
  echo $allyforumvt_lang[msg_2];
  exit();
  }

}

?>
<form method="POST" action="ally_forumvt.php?id=<?=$id?>&#unten">
<table border="0" cellspacing="0" cellpadding="0" width="475">
<tr>
    <td width="13" height="25" class="rol"></td>
    <td align="center" height="35" colspan="4" class="ro"><div class="fett">
<?
if($action=="edit")
echo $allyforumvt_lang[beitrageditieren].'</div></td>';
else
echo $allyforumvt_lang[antwortauf].' '.utf8_encode(umlaut($temporary[threadname])).' '.$allyforumvt_lang[erstellen].'</div></td>';
?>
    <td width="13" height="25" class="ror"></td>
</tr>
<tr>
    <td width="13" height="25" class="rl"></td>
    <td class="tl"><?=$allyforumvt_lang[titel]?>:</td>
    <td class="tl" colspan="3"><input type="text" name="title" size="25" maxlength="50"
<?
if($action=="edit")
{
if($vorschauedit)
{
echo 'value="'.utf8_encode(umlaut($title)).'"';
}
else
echo 'value="'.utf8_encode(umlaut($rew[title])).'"';
}
else
echo 'value="'.$allyforumvt_lang[re].':'.utf8_encode(umlaut($temporary[threadname])).'"';
?>


    ></td>
    <td width="13" height="25" class="rr"></td>
</tr>
<?=buttonpanel($ums_gpfad);?>
<tr>
<td width="13" height="25" class="rl"></td>
<td class="tl" valign="top"><?=$allyforumvt_lang[nachricht]?>:</td>
<td class="tl" align="center" colspan="3"><textarea rows="8" name="nachricht" id="nachricht" cols="47">
<?
if($action=="edit"){
	if($vorschauedit){
		echo utf8_encode(umlaut($nachricht));
	}else{
		$rew['post'] = str_replace("<br />"," ",$rew['post']);
		echo utf8_encode(umlaut($rew['post']));
	}
}
else
echo $nachricht;
?>
</textarea></td>
<td width="13" height="25" class="rr"></td>
</tr>
<tr>
<td width="13" height="25" class="rl"></td>
<td class="tl" align="center" colspan="4"><div align="center">
<?
if($action=="edit")
echo '<input type="submit" value="'.$allyforumvt_lang[aenderungspeichern].'"  name="savedit"><input type="hidden" name="pid" value="'.$pid.'">&nbsp;<input type="submit" value="'.$allyforumvt_lang[vorschau].'"  name="vorschauedit"></div></td>';
else
echo '<input type="submit" value="'.$allyforumvt_lang[antworten].'"  name="reply">&nbsp;<input type="submit" value="'.$allyforumvt_lang[vorschau].'"  name="vorschau"></div></td>';
?>

<td width="13" height="25" class="rr"></td>
</tr>
<tr>
<td width="13" class="rul">&nbsp;</td>
<td class="ru" colspan="4">&nbsp;</td>
<td width="13" class="rur">&nbsp;</td>
</tr>
</table>
</form>


<?
if($isleader){
	echo '<br><br><a href="ally_forum.php?a=c&threadid='.$id.'">'.$allyforumvt_lang[threadschliessen].'</a> - <a href="ally_forum.php?a=d&threadid='.$id.'" onclick="return confirm(\'M&ouml;chten Sie diesen Thread wirklich l�schen?\')">'.$allyforumvt_lang[threadloeschen].'</a> - ';

	if($temporary[wichtig]=="0")
	echo '<a href="ally_forum.php?a=ota&threadid='.$id.'">'.$allyforumvt_lang[ontopaktivieren].'</a>';
	else
	echo '<a href="ally_forum.php?a=otd&threadid='.$id.'">'.$allyforumvt_lang[ontopdeaktivieren].'</a>';
}
}
else
{

if($isleader){
	echo '<br><br><a href="ally_forum.php?a=o&threadid='.$id.'">'.$allyforumvt_lang[threadoeffnen].'</a> - <a href="ally_forum.php?a=d&threadid='.$id.'" onclick="return confirm(\'M&ouml;chten Sie diesen Thread wirklich l�schen?\')">'.$allyforumvt_lang[threadloeschen].'</a> - ';

	if($temporary[wichtig]=="0")
	echo '<a href="ally_forum.php?a=ota&threadid='.$id.'">'.$allyforumvt_lang[ontopaktivieren].'</a>';
	else
	echo '<a href="ally_forum.php?a=otd&threadid='.$id.'">'.$allyforumvt_lang[ontopdeaktivieren].'</a>';
	}
}

?>
</div>
<br><br>
<?php include("ally/ally.footer.inc.php") ?>
<?php include "fooban.php"; ?>
</body>
</html>