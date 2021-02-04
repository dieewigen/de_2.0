<?php
include "inc/header.inc.php";
include "format_sammlung.php";
include 'inc/lang/'.$sv_server_lang.'_secforum.lang.php';

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, system, newtrans, newnews FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];
$sector=$row["sector"];$system=$row["system"];

?>
<!doctype html>
<html>
<head>
<title><?=$secforum_lang[title]?></title>
<?php include "cssinclude.php"; ?>
<script src="<?=$sv_server_lang?>_jssammlung.js" type="text/javascript"></script>
<meta charset="iso-8859-15">
</head>
<body>
<?//stelle die ressourcenleiste dar
include "resline.php";

include "inc/sv.inc.php";
include "issectork.php";


if($sector==1)
{

echo '<br><br>
  <table border="0" cellpadding="0" cellspacing="0" width="550">
  <tr>
  <td width="13" height="35" class="rol"></td>
  <td align=center height="35" class="ro"><font size=3><div class=fett>'.$secforum_lang[systemnachricht].'</div></font></td>
  <td width="13" height="35" class="ror"></td>
  </tr>
  <tr><td width="13" class="rl" height=35></td>
  <td align="center" nowrap class="c"><font color="#00FF00">'.$secforum_lang[msg_1].'</font></td>
  <td width="13" class="rr" height=35></td></tr>
  <tr>
  <td width="13" class="rul">&nbsp;</td>
  <td class="ru">&nbsp;</td>
  <td width="13" class="rur">&nbsp;</td>
  </tr>
  </table><br><br>';

          exit();
}

echo foren_navi(basename($_SERVER['PHP_SELF']),$ums_user_id,$system,$naviarray_lang);

$threadid=(int)$threadid;
//neuen thread erstellen?
if($newthread){
	
	$_POST[nachricht] = trim($_POST[nachricht]);
	$_POST[title] = trim($_POST[title]);

	if(!$_POST[nachricht])$error.=$secforum_lang[msg_2];
	if(!$_POST[title])$error.=$secforum_lang[msg_3];
	if(!$ums_user_id)$error.=$secforum_lang[msg_4];

	//test auf com-sperre
	$akttime=date("Y-m-d H:i:s",time());
	$db_daten=mysql_query("SELECT com_sperre FROM de_login WHERE user_id='$ums_user_id'",$db);
	$row = mysql_fetch_array($db_daten);
	if($row['com_sperre']>$akttime){
		$sperrtime=strtotime($row['com_sperre']);
		echo('<div class="info_box text2" style="margin-bottom: 5px; font-size: 14px;">Account: Sperre f&uuml;r ausgehende Kommunikation bis: '.date("d.m.Y - G:i", $sperrtime).'</div>');
	}
	elseif(!$error)
  {
    $now=time();

    $i=1;
    $seen="s";
    while($i<$sv_maxsystem+1)
    {
    if($i==$system)
    $seen=$seen.'1';
    else
    $seen=$seen.'0';
    $i++;
    }
    if($_POST[wichtig]=="1")$wichtig=1; else $wichtig=0;


	$_POST[title] = db_aufbereitung($_POST[title]);
	$_POST[title]=htmlspecialchars($_POST[title], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');

	$_POST[nachricht] = db_aufbereitung($_POST[nachricht]);
	$_POST[nachricht]=htmlspecialchars($_POST[nachricht], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
	$_POST[nachricht] = nl2br_pre($_POST[nachricht]);

    //thread eintragen
    mysql_query("INSERT INTO de_sectorforum_threads (threadname, creator, sector, lastposter, lastactive, anzposts, hits, wichtig, gelesen) VALUES ('$_POST[title]','$ums_spielername','$sector','$ums_spielername','$now',0,0,'$wichtig','$seen')");
    $threadid=mysql_insert_id();

    //posting eintragen
    mysql_query("INSERT INTO de_sectorforum_posts (poster,post,time,thread,title,edit) VALUES ('$ums_spielername','$_POST[nachricht]','$now','$threadid','$_POST[title]','')");

   $nachricht="";
   $_POST[title]="";
  }else echo $error;
}

//close thread
elseif ($a=="c")
{
  if ($system==issectorcommander())
  {
  //schaue ob die threadid und der sektor g&uuml;ltig sind
  $db_daten=mysql_query("SELECT sector FROM de_sectorforum_threads WHERE id='$threadid'");
  $num = mysql_num_rows($db_daten);
  if ($num==0)$error.=$secforum_lang[msg_5];
  $row = mysql_fetch_array($db_daten);
  if ($sector!=$row[sector])$error.=$secforum_lang[msg_6];

  if(!$error)
  {
    mysql_query("UPDATE de_sectorforum_threads SET open=0 WHERE id='$threadid'");
    echo $secforum_lang[msg_7];
  }else echo $error;
  }
}
elseif ($a=="o")
{
  if ($system==issectorcommander())
  {
  //schaue ob die threadid und der sektor g&uuml;ltig sind
  $db_daten=mysql_query("SELECT sector FROM de_sectorforum_threads WHERE id='$threadid'");
  $num = mysql_num_rows($db_daten);
  if ($num==0)$error.=$secforum_lang[msg_5];
  $row = mysql_fetch_array($db_daten);
  if ($sector!=$row[sector])$error.=$secforum_lang[msg_6];

  if(!$error)
  {
    mysql_query("UPDATE de_sectorforum_threads SET open=1 WHERE id='$threadid'");
    echo $secforum_lang[msg_8];
  }else echo $error;
  }
}

elseif ($a=="ota")
{
  if ($system==issectorcommander())
  {
  //schaue ob die threadid und der sektor g&uuml;ltig sind
  $db_daten=mysql_query("SELECT sector FROM de_sectorforum_threads WHERE id='$threadid'");
  $num = mysql_num_rows($db_daten);
  if ($num==0)$error.=$secforum_lang[msg_5];
  $row = mysql_fetch_array($db_daten);
  if ($sector!=$row[sector])$error.=$secforum_lang[msg_6];

  if(!$error)
  {
    mysql_query("UPDATE de_sectorforum_threads SET wichtig=1 WHERE id='$threadid'");
    echo $secforum_lang[msg_9];
  }else echo $error;
  }
}

elseif ($a=="otd")
{
  if ($system==issectorcommander())
  {
  //schaue ob die threadid und der sektor g&uuml;ltig sind
  $db_daten=mysql_query("SELECT sector FROM de_sectorforum_threads WHERE id='$threadid'");
  $num = mysql_num_rows($db_daten);
  if ($num==0)$error.=$secforum_lang[msg_5];
  $row = mysql_fetch_array($db_daten);
  if ($sector!=$row[sector])$error.=$secforum_lang[msg_6];

  if(!$error)
  {
    mysql_query("UPDATE de_sectorforum_threads SET wichtig=0 WHERE id='$threadid'");
    echo $secforum_lang[msg_10];
  }else echo $error;
  }
}

//delete thread
elseif ($a=="d")
{
  if ($system==issectorcommander())
  {
  //schaue ob die threadid und der sektor g&uuml;ltig sind
  $db_daten=mysql_query("SELECT sector FROM de_sectorforum_threads WHERE id='$threadid'");
  $num = mysql_num_rows($db_daten);
  if ($num==0)$error.=$secforum_lang[msg_5];
  $row = mysql_fetch_array($db_daten);
  if ($sector!=$row[sector])$error.=$secforum_lang[msg_6];

  if(!$error)
  {
    mysql_query("DELETE FROM de_sectorforum_threads WHERE id='$threadid'");
    mysql_query("DELETE FROM de_sectorforum_posts WHERE thread='$threadid'");
    echo $secforum_lang[msg_11];
  }else echo $error;
  }
}

$db_daten=mysql_query("SELECT creator, id, lastposter, lastactive, threadname, anzposts, hits, wichtig, gelesen, open FROM de_sectorforum_threads WHERE sector='$sector' ORDER BY wichtig DESC, lastactive DESC");
$num = mysql_num_rows($db_daten);
if ($num!=0)
{
?><br>


<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="25" class="ro">&nbsp;</td>
<td width="180" class="ro"><div class="cell"><?=$secforum_lang[threadname]?></div></td>
<td width="120" class="ro"><div class="cell"><?=$secforum_lang[gestartetvon]?></div></td>
<td width="60" class="ro"><div class="cell"><?=$secforum_lang[antworten]?></div></td>
<td width="60" class="ro"><div class="cell"><?=$secforum_lang[hits]?></div></td>
<td width="170" class="ro"><div class="cell"><?=$secforum_lang[letztenachrichtvon]?></div></td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="6">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="25">
<col width="180">
<col width="120">
<col width="60">
<col width="60">
<col width="170">
</colgroup>
<?
while($row = mysql_fetch_array($db_daten))
{
$posttime =date ("d.m.Y $secforum_lang[msg_12_4] H:i", $row[lastactive]);
?>
<tr>
<td class="tc">
<?

if($row[open]=="1")
{
    if(substr($row[gelesen], $system,1)=="1")
    echo '<img src="'.$ums_gpfad.'g/forum_off.gif" width="20" height="20" border="0" alt="'.$secforum_lang[alt].'">';
    else
    echo '<img src="'.$ums_gpfad.'g/forum_on.gif" width="20" height="20" border="0" alt="'.$secforum_lang[neu].'">';

}
else
{
    if(substr($row[gelesen], $system,1)=="1")
    echo '<img src="'.$ums_gpfad.'g/offclosed.gif" width="20" height="20" border="0" alt="'.$secforum_lang[altclosed].'">';
    else
    echo '<img src="'.$ums_gpfad.'g/onclosed.gif" width="20" height="20" border="0" alt="'.$secforum_lang[neuclosed].'">';
}

?> </td>
<td class="cl"><a href="secforumvt.php?id=<?=$row[id] ?>">
<?
if($row[wichtig]=="1")echo '<u><b>'.$secforum_lang[wichtig].':</b></u> ';
?>
<b><?=$row[threadname] ?></b></a></td>
<td class="tc"><?=$row[creator] ?></td>
<td class="tc"><?=$row[anzposts] ?></td>
<td class="tc"><?=$row[hits] ?></td>
<td class="tr"><font style="font-size: 8pt"><?=$secforum_lang[msg_12_1]?> <i> <?=$row[lastposter] ?></i><br><?=$secforum_lang[msg_12_2]?> <?=$posttime ?> <?=$secforum_lang[msg_12_3]?></font></td>
</tr>
<?
}
?>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr>
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru" colspan="6">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<br>
<?php
}//ende if $num!=0
if($vorschau)
{
$newstext = $nachricht;

$newstext=htmlspecialchars($newstext, ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
$newstext = nl2br_pre($newstext);

$newstext = formatierte_anzeige($newstext,$ums_gpfad);
$newstext = preg_replace("/\[img\]([^[]*)\[\/img\]/","<img src=\"\\1\" border=0>",$newstext);

$newstext = stripslashes($newstext);
$nachricht = stripslashes($nachricht);
?>
<table width="475" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="13" height="37" class="rol">&nbsp;</td>
<td class="ro" align="center"><div class="cellu"><?=$secforum_lang[vorschau]?></div></td>
<td width="13" height="37" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" height="37" class="rl">&nbsp;</td>
<td width="500"><div class="cellu"><? echo $newstext; ?></div></td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rul">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td width="13" class="rur">&nbsp;</td>
</tr>
</table>
<?
}
?>
<form action="secforum.php" method="POST">
<table border="0" cellspacing="0" cellpadding="0" width="485">
<tr>
    <td width="13" height="25" class="rol"></td>
    <td align="center" height="35" colspan="4" class="ro"><div class="cellu"><?=$secforum_lang[neuenthreaderstellen]?></div></td>
    <td width="13" height="25" class="ror"></td>
</tr>
<tr>
    <td width="13" height="25" class="rl"></td>
    <td class="tl"><?=$secforum_lang[titel]?>:</td>
    <td class="tl"><input type="text" name="title" size="25" maxlength="50" value="<? echo $_POST[title];?>"></td>
    <td class="tl"><div align="right"><?=$secforum_lang[wichtig]?>?</div></td>
    <td class="tl"><input type="Checkbox" name="wichtig" value="1"
    <?
    if($_POST[wichtig]=="1")echo "checked";
    ?> ></td>
    <td width="13" height="25" class="rr"></td>
</tr>
<?=buttonpanel($ums_gpfad,$buttonpanel_lang);?>
<tr>
<td width="13" height="25" class="rl"></td>
<td class="tl" valign="top"><?=$secforum_lang[message]?>:</td>
<td class="tl" align="center" colspan="3"><textarea rows="8" name="nachricht" id="nachricht" cols="47"><?echo $nachricht;?></textarea></td>
<td width="13" height="25" class="rr"></td>
</tr>
<tr>
<td width="13" height="25" class="rl"></td>
<td class="tl" align="center" colspan="4"><div align="center"><input type="submit" value="<?=$secforum_lang[threaderstellen]?>"  name="newthread">&nbsp;<input type="submit" value="<?=$secforum_lang[vorschau]?>" name="vorschau"></div></td>
<td width="13" height="25" class="rr"></td>
</tr>
<tr>
<td width="13" class="rul">&nbsp;</td>
<td class="ru" colspan="4">&nbsp;</td>
<td width="13" class="rur">&nbsp;</td>
</tr>
</table>
</form>
<?php include "fooban.php"; ?>
</div>
</center>
</body>
</html>