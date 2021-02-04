<?php
include "inc/header.inc.php";
include "inc/sv.inc.php";
include "format_sammlung.php";
include 'inc/lang/'.$sv_server_lang.'_skforumvt.lang.php';

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, system, newnews, newtrans FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];
$sector=$row["sector"];$system=$row["system"];
/*#################################### oberhalb editieren ##############*/
?>
<!doctype html>
<html>
<head>
<title><?=$skforumvt_lang[title]?></title>
<?php include "cssinclude.php"; ?>
<script src="<?=$sv_server_lang?>_jssammlung.js" type="text/javascript"></script>
<meta charset="iso-8859-15">
</head>
<body><br>
<?//stelle die ressourcenleiste dar
include "resline.php";
include "issectork.php";

echo '<div class="cell">';

if($system!=issectorcommander())
{
  echo $skforumvt_lang[msg_0];
  exit;
}
echo foren_navi(basename($_SERVER['PHP_SELF']),$ums_user_id,$system);
$id=(int)$id;
$threadid=(int)$threadid;

$temporary=mysql_query("SELECT threadname, sector, open, anzposts,gelesen FROM de_sectorforum_threads WHERE id='$id'");
$temporary = mysql_fetch_array($temporary);

$temporary[gelesen] = substr_replace($temporary[gelesen], "1", $sector, 1);
mysql_query("UPDATE de_sectorforum_threads set gelesen='$temporary[gelesen]' WHERE id='$id'");


if("0"<>"$temporary[sector]")
{
echo $skforumvt_lang[msg_1];
exit();
}

echo '<a name="oben">&nbsp;</a><br>

<a href="javascript:history.back()">
  <img src="'.$ums_gpfad.'g/'.$ums_rasse.'_goback.gif" border="0" alt="'.$skforumvt_lang[zurueck].'">
</a>
<a href="#unten">
  <img src="'.$ums_gpfad.'g/'.$ums_rasse.'_godown.gif" border="0" alt="'.$skforumvt_lang[tolastpost].'">
</a>
<br><br>
<font size="2" face="arial"><b><a href="skforum.php">'.$skforumvt_lang[title].'</a>
</b> -> <b>'.$skforumvt_lang[thema].': '.$temporary[threadname].'</b></font><br><br>';



if(!$reply && !$vorschau &&($action!="edit"))
{
mysql_query("UPDATE de_sectorforum_threads set hits=hits+1 WHERE id='$id'");
}

if($savedit)
{



            $_POST[title] = db_aufbereitung($_POST[title]);
            $_POST[title]=htmlspecialchars($_POST[title], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');

            $_POST[nachricht] = db_aufbereitung($_POST[nachricht]);
            $_POST[nachricht]=htmlspecialchars($_POST[nachricht], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
            $_POST[nachricht] = nl2br_pre($_POST[nachricht]);


$db_editt=mysql_unbuffered_query("SELECT poster,edit FROM de_sectorforum_posts WHERE postid='$pid'");
$raw = mysql_fetch_array($db_editt);
if($raw[poster]!=$ums_spielername)
{
echo $skforumvt_lang[msg_2];
exit();
}
$ed = $raw[edit];
$zeit=strftime("%Y%m%d%H%M%S");

$ed=$zeit.'|'.$ed;

mysql_query("UPDATE de_sectorforum_posts set post='$_POST[nachricht]', title='$_POST[title]',edit='$ed' WHERE postid='$pid'");

$_POST[nachricht]="";
$_POST[title]="";
$nachricht="";
$title="";
echo '<a name="unten">&nbsp;</a>';
}

if($reply)
{
$_POST[nachricht] = trim($_POST[nachricht]);
$_POST[title] = trim($_POST[title]);

  if(!$_POST[nachricht])$error.=$skforumvt_lang[msg_3];
  if(!$_POST[title])$error.=$skforumvt_lang[msg_4];
  if(!$ums_user_id)$error.=$skforumvt_lang[msg_5];

  //schaue ob die threadid und der sektor g&uuml;ltig sind
  $db_daten=mysql_query("SELECT sector FROM de_sectorforum_threads WHERE id='$id'");
  $num = mysql_num_rows($db_daten);
  if ($num==0)$error.=$skforumvt_lang[msg_6];
  $row = mysql_fetch_array($db_daten);
  if ("0"!=$row[sector])$error.=$skforumvt_lang[msg_7];

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
    include('outputlib.php');

            $_POST[title] = db_aufbereitung($_POST[title]);
            $_POST[title]=htmlspecialchars($_POST[title], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');

            $_POST[nachricht] = db_aufbereitung($_POST[nachricht]);
            $_POST[nachricht]=htmlspecialchars($_POST[nachricht], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
            $_POST[nachricht] = nl2br_pre($_POST[nachricht]);

    $now=time();

    //posting eintragen
    mysql_query("INSERT INTO de_sectorforum_posts (poster,post,time,thread,title,edit) VALUES ('$ums_spielername','$_POST[nachricht]','$now','$id','$_POST[title]','')");

    $i=1;
    $seen="s";
    while($i<$sv_maxsector+1)
    {
    if($i==$sector)
    $seen=$seen.'1';
    else
    $seen=$seen.'0';
    $i++;
    }

    if($_POST[wichtig]=="1")$wichtig=1; else $wichtig=0;

    //thread mit den neuen daten updaten
    mysql_query("UPDATE de_sectorforum_threads set lastposter='$ums_spielername',lastactive='$now', anzposts=anzposts+1, gelesen='$seen' WHERE id='$id'");

    $nachricht="";
    $title=$skforumvt_lang[re].':';

  }else echo $error;
}

$temporary=mysql_query("SELECT anzposts, open,threadname, sector, wichtig  FROM de_sectorforum_threads WHERE id='$id'");
$temporary = mysql_fetch_array($temporary);

$downcounter=0;

$db_daten=mysql_query("SELECT poster, post, time, title, postid, thread, edit FROM de_sectorforum_posts WHERE thread='$id' ORDER BY  time ASC");
while($row = mysql_fetch_array($db_daten))
{
$newstext=$row[post];
$posttime =date ("d.m.Y $skforumvt_lang[msg_8_4] H:i", $row[time]);

        $newstext = formatierte_anzeige($newstext,$ums_gpfad);

if($row[title]=="")
{
$row[title]=$skforumvt_lang[re].':';
$row[title].=$temporary[threadname];
}
  if(($downcounter==$temporary[anzposts]+1)&& !$vorschau)
  {
  echo '<a name="unten">&nbsp;</a>';
  }
  $downcounter++;

$result = mysql_query("SELECT sector FROM de_user_data WHERE spielername='$row[poster]'");
$result = mysql_fetch_array($result);

echo '<table border="0" width="600" bgcolor="black" cellpadding="0" cellspacing="1">
      <tr>
          <td width="100%" bgcolor="#202020" align="left">
              <table border="0" width="100%">
              <tr>
                  <td>
                      <font color="#E0E0E0" face="Arial" size="2"><b>'.$row[title].'</b></font>
                      <font face="Arial" size="2">- '.$skforumvt_lang[msg_8_1].' '.$row[poster].' ('.$skforumvt_lang[sektor].': '.$result[sector].') '.$skforumvt_lang[msg_8_2].' '.$posttime.' '.$skforumvt_lang[msg_8_3].'</font>
                  </td>
                  <td>';
                  if($row[poster]==$ums_spielername)
                  echo '<a href="skforumvt.php?action=edit&id='.$row[thread].'&pid='.$row[postid].'&#unten">'.$skforumvt_lang[edit].'</a>';
                  else
                  echo '&nbsp;';

 $newstext = preg_replace("/\[img\]([^[]*)\[\/img\]/","<img src=\"\\1\" border=0>",$newstext);
echo '            </td>
             </tr>
             </table>
           </td>
        </tr>
        <tr>
            <td width="100%" bgcolor="#202020" align="left"><font color="#E0E0E0" face="Arial" size="2">'.$newstext.'</font></td>
        </tr>';

        if($row[edit]!="")
        {
        $edits = explode("|",$row[edit]);

        $male = count($edits)-1;

        $t= $edits[0];
        $time=$t[6].$t[7].'.'.$t[4].$t[5].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[8].$t[9].':'.$t[10].$t[11].':'.$t[12].$t[13];



        echo '<tr><td bgcolor="#202020" align="left"><font size="-2">&nbsp;'.$skforumvt_lang[msg_9_1].' '.$male.' '.$skforumvt_lang[msg_9_1].' '.$time.'.</font></td></tr>';
        }
echo '</table><br>';
}
echo '<a name="unten">&nbsp;</a><font size="2" face="arial"><b><a href="skforum.php">'.$skforumvt_lang[title].'</a></b> -> <b>'.$skforumvt_lang[thema].': '.$temporary[threadname].'</b></font><br><br>

<a href="javascript:history.back()"><img src="'.$ums_gpfad.'g/'.$ums_rasse.'_goback.gif" border="0" alt="'.$skforumvt_lang[zurueck].'"></a>
<a href="#oben"><img src="'.$ums_gpfad.'g/'.$ums_rasse.'_goup.gif" border="0" alt="'.$skforumvt_lang[tofirstpost].'"></a>';

if($temporary[open]==1) {


if($vorschau or $vorschauedit)
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
<td class="ro" align="center"><?=$skforumvt_lang[vorschau]?></td>
<td width="13" height="37" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" height="37" class="rl">&nbsp;</td>
<td width="500"><? echo $newstext; ?></td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rul">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td width="13" class="rur">&nbsp;</td>
</tr>
</table>
<?


if($vorschauedit)
{
     $action="edit";
     $nachricht=$nachricht;
     $nachricht = str_replace("<br />"," ",$nachricht);


}
}
if($action=="edit")
{
     $db_edit=mysql_unbuffered_query("SELECT poster, post, time, title, postid FROM de_sectorforum_posts WHERE postid='$pid'");
     $rew = mysql_fetch_array($db_edit);
     if($rew[poster]!=$ums_spielername)
     {
            echo $skforumvt_lang[msg_2];
            exit();
     }

}

?>
<form method="POST" action="skforumvt.php?id=<?=$id?>&#unten">
<table border="0" cellspacing="0" cellpadding="0" width="475">
<tr>
    <td width="13" height="25" class="rol"></td>
    <td align="center" height="35" colspan="4" class="ro"><div class="fett">
<?
if($action=="edit")
echo $skforumvt_lang[beitrageditieren].'</div></td>';
else
echo $skforumvt_lang[antwortauf].' '.$temporary[threadname].' '.$skforumvt_lang[erstellen].'</div></td>';
?>
    <td width="13" height="25" class="ror"></td>
</tr>
<tr>
    <td width="13" height="25" class="rl"></td>
    <td class="tl"><?=$skforumvt_lang[titel]?>:</td>
    <td class="tl" colspan="3"><input type="text" name="title" size="25" maxlength="50"
<?
if($action=="edit")
{
if($vorschauedit)
{
echo 'value="'.$title.'"';
}
else
echo 'value="'.$rew[title].'"';
}
else
echo 'value="Re:'.$temporary[threadname].'"';
?>


    ></td>
    <td width="13" height="25" class="rr"></td>
</tr>
<?=buttonpanel($ums_gpfad,$buttonpanel_lang);?>
<tr>
<td width="13" height="25" class="rl"></td>
<td class="tl" valign="top"><?=$skforumvt_lang[nachricht]?>:</td>
<td class="tl" align="center" colspan="3"><textarea rows="8" name="nachricht" id="nachricht" cols="47">
<?
if($action=="edit")
{
    if($vorschauedit)
    {
    echo $nachricht;
    }
    else
    {
    $rew[post] = str_replace("<br />"," ",$rew[post]);
    echo $rew[post];

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
echo '<input type="submit" value="'.$skforumvt_lang[aenderungspeichern].'"  name="savedit"><input type="hidden" name="pid" value="'.$pid.'">&nbsp;<input type="submit" value="'.$skforumvt_lang[vorschau].'"  name="vorschauedit"></div></td>';
else
echo '<input type="submit" value="'.$skforumvt_lang[antworten].'"  name="reply">&nbsp;<input type="submit" value="'.$skforumvt_lang[vorschau].'"  name="vorschau"></div></td>';

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
if(in_array($ums_user_id, $mods))
{
echo '<a href="skforum.php?a=c&threadid='.$id.'">'.$skforumvt_lang[threadschliessen].'</a> - <a href="skforum.php?a=d&threadid='.$id.'" onclick="return confirm(\''.$skforumvt_lang[msg_10].'\')">'.$skforumvt_lang[threadloeschen].'</a> - ';
if($temporary[wichtig]=="0")
echo '<a href="skforum.php?a=ota&threadid='.$id.'">'.$skforumvt_lang[ontopaktivieren].'</a>';
else
echo '<a href="skforum.php?a=otd&threadid='.$id.'">'.$skforumvt_lang[ontopdeaktivieren].'</a>';
}
}
else
{
if(in_array($ums_user_id, $mods))
{
echo '<a href="skforum.php?a=o&threadid='.$id.'">'.$skforumvt_lang[threadoeffnen].'</a> - <a href="skforum.php?a=d&threadid='.$id.'"  onclick="return confirm(\''.$skforumvt_lang[msg_10].'\')">'.$skforumvt_lang[threadloeschen].'</a> - ';
if($temporary[wichtig]=="0")
echo '<a href="skforum.php?a=ota&threadid='.$id.'">'.$skforumvt_lang[ontopaktivieren].'</a>';
else
echo '<a href="skforum.php?a=otd&threadid='.$id.'">'.$skforumvt_lang[ontopdeaktivieren].'</a>';
}
}
?>
</div>
<br><br>
<?php include "fooban.php"; ?>
</div>
</center>
</body>
</html>