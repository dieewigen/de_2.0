<?php
include "inc/header.inc.php";
include "format_sammlung.php";
include 'inc/lang/'.$sv_server_lang.'_ally_forum.lang.php';
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
<title><?php echo $allyforum_lang[title]?></title>
<script src="<?php echo $sv_server_lang?>_jssammlung.js" type="text/javascript"></script>
<?php include "cssinclude.php"; ?>
<meta charset="iso-8859-15">
</head>
<body>
<?php
include "resline.php";
include ("ally/ally.menu.inc.php");
include "issectork.php";

//echo foren_navi(basename($_SERVER['PHP_SELF']),$ums_user_id,$system);




$threadid=(int)$threadid;
//neuen thread erstellen?
if($newthread)
{
  $_POST[nachricht] = trim($_POST[nachricht]);
  $_POST[title] = trim($_POST[title]);

  if(!$_POST[nachricht])$error.=$allyforum_lang[msg_2];
  if(!$_POST[title])$error.=$allyforum_lang[msg_3];
  if(!$ums_user_id)$error.=$allyforum_lang[msg_4];

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

    if($_POST[wichtig]=="1")$wichtig=1; else $wichtig=0;


            $_POST[title] = db_aufbereitung($_POST[title]);
            $_POST[title]=htmlspecialchars($_POST[title], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');

            $_POST[nachricht] = db_aufbereitung($_POST[nachricht]);
            $_POST[nachricht]=htmlspecialchars($_POST[nachricht], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
            $_POST[nachricht] = nl2br_pre($_POST[nachricht]);

    //thread eintragen
    mysql_query("INSERT INTO de_alliforum_threads (threadname, creator, allytag, lastposter, lastactive, anzposts, hits, wichtig, gelesen) VALUES ('$_POST[title]','$ums_spielername','$allytag','$ums_spielername','$now',0,0,'$wichtig','')");
    $threadid=mysql_insert_id();

    //posting eintragen
    mysql_query("INSERT INTO de_alliforum_posts (poster,post,time,thread,title,edit) VALUES ('$ums_spielername','$_POST[nachricht]','$now','$threadid','$_POST[title]','')");
   
   $nachricht="";
   $_POST[title]="";
  }else echo $error;
}

//close thread
elseif ($a=="c")
{
  if ($isleader)
  {
  //schaue ob die threadid und der sektor g&uuml;ltig sind
  $db_daten=mysql_query("SELECT allytag FROM de_alliforum_threads WHERE id='$threadid'");
  $num = mysql_num_rows($db_daten);
  if ($num==0)$error.=$allyforum_lang[msg_5];
  $row = mysql_fetch_array($db_daten);
  if ($allytag!=$row[allytag])$error.=$allyforum_lang[msg_6];

  if(!$error)
  {
    mysql_query("UPDATE de_alliforum_threads SET open=0 WHERE id='$threadid'");
    echo $allyforum_lang[msg_7];
  }else echo $error;
  }
}
elseif ($a=="o")
{
  if ($isleader)
  {
  //schaue ob die threadid und der sektor g&uuml;ltig sind
  $db_daten=mysql_query("SELECT allytag FROM de_alliforum_threads WHERE id='$threadid'");
  $num = mysql_num_rows($db_daten);
  if ($num==0)$error.=$allyforum_lang[msg_5];
  $row = mysql_fetch_array($db_daten);
  if ($allytag!=$row[allytag])$error.=$allyforum_lang[msg_6];

  if(!$error)
  {
    mysql_query("UPDATE de_alliforum_threads SET open=1 WHERE id='$threadid'");
    echo $allyforum_lang[msg_8];
  }else echo $error;
  }
}

elseif ($a=="ota")
{
  if ($isleader)
  {
  //schaue ob die threadid und der sektor g&uuml;ltig sind
  $db_daten=mysql_query("SELECT allytag FROM de_alliforum_threads WHERE id='$threadid'");
  $num = mysql_num_rows($db_daten);
  if ($num==0)$error.=$allyforum_lang[msg_5];
  $row = mysql_fetch_array($db_daten);
  if ($allytag!=$row[allytag])$error.=$allyforum_lang[msg_6];

  if(!$error)
  {
    mysql_query("UPDATE de_alliforum_threads SET wichtig=1 WHERE id='$threadid'");
    echo $allyforum_lang[msg_9];
  }else echo $error;
  }
}

elseif ($a=="otd")
{
  if ($isleader)
  {
  //schaue ob die threadid und der sektor g&uuml;ltig sind
  $db_daten=mysql_query("SELECT allytag FROM de_alliforum_threads WHERE id='$threadid'");
  $num = mysql_num_rows($db_daten);
  if ($num==0)$error.=$allyforum_lang[msg_5];
  $row = mysql_fetch_array($db_daten);
  if ($allytag!=$row[allytag])$error.=$allyforum_lang[msg_6];

  if(!$error)
  {
    mysql_query("UPDATE de_alliforum_threads SET wichtig=0 WHERE id='$threadid'");
    echo $allyforum_lang[msg_10];
  }else echo $error;
  }
}

//delete thread
elseif ($a=="d")
{
  if ($isleader)
  {
  //schaue ob die threadid und der sektor g&uuml;ltig sind
  $db_daten=mysql_query("SELECT allytag FROM de_alliforum_threads WHERE id='$threadid'");
  $num = mysql_num_rows($db_daten);
  if ($num==0)$error.=$allyforum_lang[msg_5];
  $row = mysql_fetch_array($db_daten);
  if ($allytag!=$row[allytag])$error.=$allyforum_lang[msg_6];

  if(!$error)
  {
    mysql_query("DELETE FROM de_alliforum_threads WHERE id='$threadid'");
    mysql_query("DELETE FROM de_alliforum_posts WHERE thread='$threadid'");
    echo $allyforum_lang[msg_11];
  }else echo $error;
  }
}

$db_daten=mysql_query("SELECT creator, id, lastposter, lastactive, threadname, anzposts, hits, wichtig, open FROM de_alliforum_threads WHERE allytag='$allytag' ORDER BY wichtig DESC, lastactive DESC");
$num = mysql_num_rows($db_daten);
if ($num!=0)
{
?><br>


<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="25" class="ro">&nbsp;</td>
<td width="180" class="ro"><div class="cell"><?php echo $allyforum_lang[threadname]?></div></td>
<td width="120" class="ro"><div class="cell"><?php echo $allyforum_lang[gestartetvon]?></div></td>
<td width="60" class="ro"><div class="cell"><?php echo $allyforum_lang[antworten]?></div></td>
<td width="60" class="ro"><div class="cell"><?php echo $allyforum_lang[hits]?></div></td>
<td width="170" class="ro"><div class="cell"><?php echo $allyforum_lang[letztenachrichtvon]?></div></td>
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
<?php
while($row = mysql_fetch_array($db_daten))
{
$posttime =date ("d.m.Y $allyforum_lang[msg_12_4] H:i", $row[lastactive]);
?>
<tr>
<td class="tc">
<?php

if($row[open]=="1")
{
    echo '<img src="'.$ums_gpfad.'g/forum_off.gif" width="20" height="20" border="0" alt="'.$allyforum_lang[alt].'">';

}
else
{
    echo '<img src="'.$ums_gpfad.'g/offclosed.gif" width="20" height="20" border="0" alt="'.$allyforum_lang[altclosed].'">';
}

?> </td>
<td class="cl"><a href="ally_forumvt.php?id=<?php echo $row[id] ?>">
<?php
if($row[wichtig]=="1")echo "<u><b>".$allyforum_lang[wichtig].":</b></u> ";
?>
<b><?php echo $row[threadname] ?></b></a></td>
<td class="tc"><?php echo $row[creator] ?></td>
<td class="tc"><?php echo $row[anzposts] ?></td>
<td class="tc"><?php echo $row[hits] ?></td>
<td class="tr"><font style="font-size: 8pt"><?php echo $allyforum_lang[msg_12_1]?> <i> <?php echo $row[lastposter] ?></i><br><?php echo $allyforum_lang[msg_12_2]?> <?php echo $posttime ?> <?php echo $allyforum_lang[msg_12_3]?></font></td>
</tr>
<?php
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
<td class="ro" align="center"><div class="cellu"><?php echo $allyforum_lang[vorschau]?></div></td>
<td width="13" height="37" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" height="37" class="rl">&nbsp;</td>
<td width="500"><div class="cell"><?php echo $newstext; ?></div></td>
<td width="13" class="rr">&nbsp;</td>
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
<form action="ally_forum.php" method="POST">
<table border="0" cellspacing="0" cellpadding="0" width="475">
<tr>
    <td width="13" height="25" class="rol"></td>
    <td align="center" height="35" colspan="4" class="ro"><div class="cellu"><?php echo $allyforum_lang[neuenthreaderstellen]?></div></td>
    <td width="13" height="25" class="ror"></td>
</tr>
<tr>
    <td width="13" height="25" class="rl"></td>
    <td class="tl"><?php echo $allyforum_lang[titel]?>:</td>
    <td class="tl"><input type="text" name="title" size="25" maxlength="50" value="<?php echo $_POST[title];?>"></td>
    <td class="tl"><div align="right"><?php echo $allyforum_lang[wichtig]?>?</div></td>
    <td class="tl"><input type="Checkbox" name="wichtig" value="1"
    <?php
    if($_POST[wichtig]=="1")echo "checked";
    ?> ></td>
    <td width="13" height="25" class="rr"></td>
</tr>
<?php echo buttonpanel($ums_gpfad);?>
<tr>
<td width="13" height="25" class="rl"></td>
<td class="tl" valign="top"><?php echo $allyforum_lang[message]?>:</td>
<td class="tl" align="center" colspan="3"><textarea rows="8" name="nachricht" id="nachricht" cols="47"><?php echo $nachricht;?></textarea></td>
<td width="13" height="25" class="rr"></td>
</tr>
<tr>
<td width="13" height="25" class="rl"></td>
<td class="tl" align="center" colspan="4"><div align="center"><input type="submit" value="<?php echo $allyforum_lang[threaderstellen]?>"  name="newthread">&nbsp;<input type="submit" value="<?php echo $allyforum_lang[vorschau]?>"  name="vorschau"></div></td>
<td width="13" height="25" class="rr"></td>
</tr>
<tr>
<td width="13" class="rul">&nbsp;</td>
<td class="ru" colspan="4">&nbsp;</td>
<td width="13" class="rur">&nbsp;</td>
</tr>
</table>
</form>
<br>
<?php include("ally/ally.footer.inc.php") ?>
<?php include "fooban.php"; ?>
</body>
</html>