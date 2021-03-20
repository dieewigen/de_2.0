<?php
//include "inc/header.inc.php";
//include "format_sammlung.php";

//schauen wer der fraktionsvorsitzende
/*
    $query_welche_userid = @mysql_query("
      SELECT wahlstimme , count(wahlstimme) as anzahl 
      FROM sou_user_politics 
      WHERE fraction = '$_SESSION[sou_fraction]' 
      GROUP BY wahlstimme 
      ORDER BY anzahl DESC, wahlstimme ASC LIMIT 1
    ", $soudb);
    $data_gewinner = @mysql_fetch_array($query_welche_userid);
    $fraktionsvorsitzender_userid = $data_gewinner[wahlstimme];*/

$fraktionsvorsitzender_userid = -1;//get_fracleader_id($player_fraction);

$tbl_threads='sou_forum_threads';
$tbl_posts='sou_forum_posts';
$fraction=$player_fraction;

//echo '<script src="jssammlung.js" type="text/javascript"></script>';

$threadid=(int)$threadid;
//neuen thread erstellen?
if($newthread)
{
  $_POST[nachricht] = trim($_POST[nachricht]);
  $_POST[title] = trim($_POST[title]);

  if(!$_POST[nachricht])$error.="Keine Nachricht eingetragen.<br>";
  if(!$_POST[title])$error.="Leider ist ein Fehler aufgetreten [Kein Thema angegeben].<br>";

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
            
    		$_POST[title] = str_replace ("Ã¤", "&auml;", $_POST[title]);
    		$_POST[title] = str_replace ("Ã„", "&Auml;", $_POST[title]);
    		$_POST[title] = str_replace ("Ã¶", "&ouml;", $_POST[title]);
    		$_POST[title] = str_replace ("Ã–", "&Ouml;", $_POST[title]);
    		$_POST[title] = str_replace ("Ã¼", "&uuml;", $_POST[title]);
    		$_POST[title] = str_replace ("Ãœ", "&Uuml;", $_POST[title]);
    		$_POST[title] = str_replace ("ÃŸ", "&szlig;", $_POST[title]);
    		
    		$_POST[title] = str_replace ("Â³", "&sup3;", $_POST[title]);
    		$_POST[title] = str_replace ("Â²", "&sup2;", $_POST[title]);

    		$_POST[nachricht] = str_replace ("Ã¤", "&auml;", $_POST[nachricht]);
    		$_POST[nachricht] = str_replace ("Ã„", "&Auml;", $_POST[nachricht]);
    		$_POST[nachricht] = str_replace ("Ã¶", "&ouml;", $_POST[nachricht]);
    		$_POST[nachricht] = str_replace ("Ã–", "&Ouml;", $_POST[nachricht]);
    		$_POST[nachricht] = str_replace ("Ã¼", "&uuml;", $_POST[nachricht]);
    		$_POST[nachricht] = str_replace ("Ãœ", "&Uuml;", $_POST[nachricht]);
    		$_POST[nachricht] = str_replace ("ÃŸ", "&szlig;", $_POST[nachricht]);
    		
    		$_POST[nachricht] = str_replace ("Â³", "&sup3;", $_POST[nachricht]);
    		$_POST[nachricht] = str_replace ("Â²", "&sup2;", $_POST[nachricht]);

    //thread eintragen
    mysql_query("INSERT INTO $tbl_threads (threadname, creator, fraction, lastposter, lastactive, anzposts, hits, wichtig) VALUES ('$_POST[title]','$player_name','$fraction','$player_name','$now',0,0,'$wichtig')");
    $threadid=mysql_insert_id();

    //posting eintragen
    mysql_query("INSERT INTO $tbl_posts (poster,post,time,thread,title) VALUES ('$player_name','$_POST[nachricht]','$now','$threadid','$_POST[title]')");

   $nachricht="";
   $_POST[title]="";
  }else 
  {
  	echo '<br>';
  	rahmen0_oben();
    echo '<font color="#FF0000"><br>'.$error.'<br></font>';
    rahmen0_unten();
    echo '<br>';
  }
}

//close thread
elseif ($a=="c")
{
  if ($player_user_id==$fraktionsvorsitzender_userid)
  {
  //schaue ob die threadid und der sektor g&uuml;ltig sind
  $db_daten=mysql_query("SELECT fraction FROM $tbl_threads WHERE id='$threadid'");
  $num = mysql_num_rows($db_daten);
  if ($num==0)$error.="Dieser Thread existiert leider nicht.<br>";
  $row = mysql_fetch_array($db_daten);
  if ($fraction!=$row[fraction])$error.="Du hast keine Berechtigung diesen Thread zu schlie&szlig;en.<br>";

  if(!$error)
  {
    mysql_query("UPDATE $tbl_threads SET open=0 WHERE id='$threadid'");
    echo "Thread geschlossen.";
  }else echo $error;
  }
}
elseif ($a=="o")
{
  if ($player_user_id==$fraktionsvorsitzender_userid)
  {
  //schaue ob die threadid und der sektor g&uuml;ltig sind
  $db_daten=mysql_query("SELECT fraction FROM $tbl_threads WHERE id='$threadid'");
  $num = mysql_num_rows($db_daten);
  if ($num==0)$error.="Dieser Thread existiert leider nicht.<br>";
  $row = mysql_fetch_array($db_daten);
  if ($fraction!=$row[fraction])$error.="Du hast keine Berechtigung diesen Thread zu schlie&szlig;en.<br>";

  if(!$error)
  {
    mysql_query("UPDATE $tbl_threads SET open=1 WHERE id='$threadid'");
    echo "Thread geöffnet.";
  }else echo $error;
  }
}

elseif ($a=="ota")
{
  if ($player_user_id==$fraktionsvorsitzender_userid)
  {
  //schaue ob die threadid und der sektor g&uuml;ltig sind
  $db_daten=mysql_query("SELECT fraction FROM $tbl_threads WHERE id='$threadid'");
  $num = mysql_num_rows($db_daten);
  if ($num==0)$error.="Dieser Thread existiert leider nicht.<br>";
  $row = mysql_fetch_array($db_daten);
  if ($fraction!=$row[fraction])$error.="Du hast keine Berechtigung diesen Thread zu schlie&szlig;en.<br>";

  if(!$error)
  {
    mysql_query("UPDATE $tbl_threads SET wichtig=1 WHERE id='$threadid'");
    echo "OnTop für Thread aktiviert.";
  }else echo $error;
  }
}

elseif ($a=="otd")
{
  if ($player_user_id==$fraktionsvorsitzender_userid)
  {
  //schaue ob die threadid und der sektor g&uuml;ltig sind
  $db_daten=mysql_query("SELECT fraction FROM $tbl_threads WHERE id='$threadid'");
  $num = mysql_num_rows($db_daten);
  if ($num==0)$error.="Dieser Thread existiert leider nicht.<br>";
  $row = mysql_fetch_array($db_daten);
  if ($fraction!=$row[fraction])$error.="Du hast keine Berechtigung diesen Thread zu schlie&szlig;en.<br>";

  if(!$error)
  {
    mysql_query("UPDATE $tbl_threads SET wichtig=0 WHERE id='$threadid'");
    echo "OnTop für Thread deaktiviert.";
  }else echo $error;
  }
}

//delete thread
elseif ($a=="d")
{
  if ($player_user_id==$fraktionsvorsitzender_userid)
  {
  //schaue ob die threadid und der sektor g&uuml;ltig sind
  $db_daten=mysql_query("SELECT fraction FROM $tbl_threads WHERE id='$threadid'");
  $num = mysql_num_rows($db_daten);
  if ($num==0)$error.="Dieser Thread existiert leider nicht.<br>";
  $row = mysql_fetch_array($db_daten);
  if ($fraction!=$row[fraction])$error.="Du hast keine Berechtigung diesen Thread zu l&ouml;schen.<br>";

  if(!$error)
  {
    mysql_query("DELETE FROM $tbl_threads WHERE id='$threadid'");
    mysql_query("DELETE FROM $tbl_posts WHERE thread='$threadid'");
    echo "Thread gel&ouml;scht.";
  }else echo $error;
  }
}
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
//threadinhalt anzeigen
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
if($_REQUEST["id"]!=0)
{
  echo '<br>';
  rahmen0_oben();
  echo '<br>';
  
  $id=intval($_REQUEST["id"]);
  
  $temporary=mysql_query("SELECT threadname, fraction, open, anzposts FROM $tbl_threads WHERE id='$id'");
  $temporary = mysql_fetch_array($temporary);
  
  if($fraction<>$temporary["fraction"])$temporary[threadname]='';
  
  $routput='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
  <td width="15%"><a href="sou_main.php?action=systempage"><div class="b1">System</div></a></td>
  <td width="85%" align="left"><b><a href="sou_main.php?action=fracforumpage">Fraktionsforum</a></b> -> <b>THEMA: '.$temporary[threadname].'</b></font></td>
  </tr></table>';
  rahmen1_oben($routput);

        if($fraction<>$temporary["fraction"])
        {
          echo 'Leider haben Sie keinen Zugriff auf dieses Dokument.';
          exit();
        }

        if(!$reply && !$vorschau &&($actionf!="edit"))
        {
          mysql_query("UPDATE $tbl_threads set hits=hits+1 WHERE id='$id'");
        }

        if($savedit)
        {

            $_POST[title] = db_aufbereitung($_POST[title]);
            $_POST[title]=htmlspecialchars($_POST[title], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');

            $_POST[nachricht] = db_aufbereitung($_POST[nachricht]);
            $_POST[nachricht]=htmlspecialchars($_POST[nachricht], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
            $_POST[nachricht] = nl2br_pre($_POST[nachricht]);


          $db_editt=mysql_unbuffered_query("SELECT poster,edit FROM $tbl_posts WHERE postid='$pid'");
          $raw = mysql_fetch_array($db_editt);
          if($raw[poster]!=$player_name)
          {
            echo 'Sie sind nicht berechtigt, dieses Dokument zu editieren.';
            exit();
          }

          $ed = $raw[edit];
          $zeit=strftime("%Y%m%d%H%M%S");

          $ed=$zeit.'|'.$ed;
          mysql_query("UPDATE $tbl_posts set post='$_POST[nachricht]', title='$_POST[title]',edit='$ed' WHERE postid='$pid'");

          $_POST[nachricht]="";
          $_POST[title]="";
          $nachricht="";
          $title="";
        }


        if($reply)
        {
          $_POST[nachricht] = trim($_POST[nachricht]);
          $_POST[title] = trim($_POST[title]);

          if(!$_POST[nachricht])$error.="Keine Nachricht eingetragen.<br>";
          if(!$_POST[title])$error.="Leider ist ein Fehler aufgetreten[Kein Thema angegeben].<br>";
          if(!$ums_user_id)$error.="Leider ist ein Fehler aufgetreten[Kein gemeldeter User].<br>";

          //schaue ob die threadid und der sektor g&uuml;ltig sind
          $db_daten=mysql_query("SELECT fraction FROM $tbl_threads WHERE id='$id'");
          $num = mysql_num_rows($db_daten);
          if($num==0)$error.="Dieser Thread existiert leider nicht.<br>";
          $row = mysql_fetch_array($db_daten);
          if ($fraction!=$row[fraction])$error.="Du hast keine Berechtigung in diesem Sektor zu posten.<br>";

	//test auf com-sperre
	$akttime=date("Y-m-d H:i:s",time());
	$db_daten=mysql_query("SELECT com_sperre FROM de_login WHERE user_id='$ums_user_id'",$db);
	$row = mysql_fetch_array($db_daten);
	if($row['com_sperre']>$akttime){
		$sperrtime=strtotime($row['com_sperre']);
		echo('<div style="margin-bottom: 5px; font-size: 18px; font-weight: bold; color: #FF0000;">Account: Sperre f&uuml;r ausgehende Kommunikation bis: '.date("d.m.Y - G:i", $sperrtime).'</div>');
	}
	elseif(!$error)
          {

            $_POST[title] = db_aufbereitung($_POST[title]);
            $_POST[title]=htmlspecialchars($_POST[title], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');

            $_POST[nachricht] = db_aufbereitung($_POST[nachricht]);
            $_POST[nachricht]=htmlspecialchars($_POST[nachricht], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
            $_POST[nachricht] = nl2br_pre($_POST[nachricht]);

            $now=time();

            //posting eintragen
            mysql_query("INSERT INTO $tbl_posts (poster,post,time,thread,title) VALUES ('$player_name','$_POST[nachricht]','$now','$id','$_POST[title]')");

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

            //thread mit den neuen daten updaten
            mysql_query("UPDATE $tbl_threads set lastposter='$player_name',lastactive='$now', anzposts=anzposts+1 WHERE id='$id'");

            $nachricht="";
            $title="RE:";

          }
          else
            echo $error;
        }

      $temporary=mysql_query("SELECT anzposts, open, threadname, fraction, wichtig  FROM $tbl_threads WHERE id='$id'");
      $temporary = mysql_fetch_array($temporary);

      $downcounter=0;

      $db_daten=mysql_query("SELECT poster, post, time, title, postid, thread, edit FROM $tbl_posts WHERE thread='$id' ORDER BY  time ASC");
      while($row = mysql_fetch_array($db_daten))
      {
        $newstext=$row[post];
        $posttime =date ("d.m.Y \u\m H:i", $row[time]);
        $newstext = formatierte_anzeige($newstext,$gpfad);

        if($row[title]=="")
        {
          $row[title]="Re:";
          $row[title].=$temporary[threadname];
        }
        $downcounter++;
        if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';} 

        echo '<table border="0" width="100%" cellpadding="0" cellspacing="1">
      <tr>
          <td width="100%" class="'.$bg.'" align="left">
              <table border="0" width="100%">
              <tr>
                  <td>
                      <font color="#E0E0E0" face="Arial" size="2"><b>'.$row[title].'</b></font>
                      <font face="Arial" size="2">- gepostet von '.$row[poster].' am '.$posttime.' Uhr</font>
                  </td>
                  <td>';
                  if($row[poster]==$player_name)
                  echo '<a href="sou_main.php?action=fracforumpage&actionf=edit&id='.$row[thread].'&pid='.$row[postid].'">edit</a>';
                  else
                  echo '&nbsp;';
                  $newstext = preg_replace("/\[img\]([^[]*)\[\/img\]/","<img src=\"\\1\" border=0>",$newstext);


echo '            </td>
             </tr>
             </table>
           </td>
        </tr>
        <tr>
            <td width="100%" class="'.$bg.'" align="left"><font color="#E0E0E0" face="Arial" size="2">'.$newstext.'</font></td>
        </tr>';

        if($row[edit]!="")
        {
        $edits = explode("|",$row[edit]);

        $male = count($edits)-1;

        $t= $edits[0];
        $time=$t[6].$t[7].'.'.$t[4].$t[5].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[8].$t[9].':'.$t[10].$t[11].':'.$t[12].$t[13];



        echo '<tr><td class="'.$bg.'" align="left"><font size="-2">&nbsp;Dieser Beitrag wurde '.$male.' mal editiert, das letzte Mal am '.$time.'.</font></td></tr>';
        }
echo '</table><br>';
}

rahmen1_unten();

if($temporary[open]==1) {


if($vorschau or $vorschauedit)
{
$newstext = $nachricht;

$newstext=htmlspecialchars($newstext, ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
$newstext = nl2br_pre($newstext);

$newstext = formatierte_anzeige($newstext,$gpfad);
$newstext = preg_replace("/\[img\]([^[]*)\[\/img\]/","<img src=\"\\1\" border=0>",$newstext);

$newstext = stripslashes($newstext);
$nachricht = stripslashes($nachricht);

echo '<br>';
rahmen1_oben('<div align="center"><b>Vorschau</b></div>');
echo $newstext;
rahmen1_unten();

if($vorschauedit)
{
$actionf="edit";
$nachricht = str_replace("<br />"," ",$nachricht);
}
}

if($actionf=="edit")
{
if(!$vorschauedit)
{

$db_edit=mysql_unbuffered_query("SELECT poster, post, time, title, postid FROM $tbl_posts WHERE postid='$pid'");
$rew = mysql_fetch_array($db_edit);
}
else
{
$db_edit=mysql_unbuffered_query("SELECT poster FROM $tbl_posts WHERE postid='$pid'");
$rew = mysql_fetch_array($db_edit);
}
if($rew[poster]!=$player_name)
{
echo 'Sie sind nicht berechtigt, dieses Dokument zu editieren.';
exit();
}

}

echo '<br><form action="sou_main.php?action=fracforumpage&id='.$id.'" method="POST">';
?>


<?
if($actionf=="edit")
rahmen1_oben('<div align="center"><b>Beitrag editieren</b></div>');
else
rahmen1_oben('<div align="center"><b>Antwort auf '.$temporary[threadname].' erstellen</b></div>');

?>
<table border="0" cellspacing="0" cellpadding="0" width="600">
<tr align="left">
    <td>Titel:</td>
    <td colspan="2"><input type="text" name="title" size="50" maxlength="50"
<?
if($actionf=="edit")
{
  if($vorschauedit)
  {
    echo 'value="'.$title.'"';
  }
  else echo 'value="'.$rew[title].'"';
}
else echo 'value="Re:'.$temporary[threadname].'"';
?>


    ></td>
</tr>
<?=buttonpanel($gpfad);?>
<tr>
<td valign="top">Nachricht:</td>
<td align="center" colspan="3"><textarea rows="8" name="nachricht" id="nachricht" cols="63">
<?
if($actionf=="edit")
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
</tr>
<tr>
<td align="center" colspan="4"><div align="center">
<?
if($actionf=="edit")
echo '<input type="submit" value="Änderung speichern"  name="savedit"><input type="hidden" name="pid" value="'.$pid.'">&nbsp;<input type="submit" value="Vorschau"  name="vorschauedit"></div></td>';
else
echo '<input type="submit" value="Antworten"  name="reply">&nbsp;<input type="submit" value="Vorschau"  name="vorschau"></div></td>';
?>
</tr>
</table>

<?
if($player_user_id==$fraktionsvorsitzender_userid)
{
echo '<br><br><a href="sou_main.php?action=fracforumpage&a=c&threadid='.$id.'">Thread schließen</a> - <a href="sou_main.php?action=fracforumpage&a=d&threadid='.$id.'" onclick="return confirm(\'M&ouml;chten Sie diesen Thread wirklich löschen?\')">Thread löschen</a> - ';

if($temporary[wichtig]=="0")
echo '<a href="sou_main.php?action=fracforumpage&a=ota&threadid='.$id.'">OnTop aktivieren</a>';
else
echo '<a href="sou_main.php?action=fracforumpage&a=otd&threadid='.$id.'">OnTop deaktivieren</a>';
}
}
else
{

if($player_user_id==$fraktionsvorsitzender_userid)
{
echo '<br><br><a href="sou_main.php?action=fracforumpage&a=o&threadid='.$id.'">Thread öffnen</a> - <a href="sou_main.php?action=fracforumpage&a=d&threadid='.$id.'" onclick="return confirm(\'M&ouml;chten Sie diesen Thread wirklich löschen?\')">Thread löschen</a> - ';

if($temporary[wichtig]=="0")
echo '<a href="sou_main.php?action=fracforumpage&a=ota&threadid='.$id.'">OnTop aktivieren</a>';
else
echo '<a href="sou_main.php?action=fracforumpage&a=otd&threadid='.$id.'">OnTop deaktivieren</a>';
}
}
  
  rahmen1_unten();
  echo '</form>';
  echo '<br>';
  rahmen0_unten();  
  die('</body></html>');	
}

////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
//threadübersicht anzeigen
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
echo '<br>';
rahmen0_oben();
echo '<br>';

$db_daten=mysql_query("SELECT creator, id, lastposter, lastactive, threadname, anzposts, hits, wichtig, open FROM $tbl_threads WHERE fraction='$fraction' ORDER BY wichtig DESC, lastactive DESC");

$num = mysql_num_rows($db_daten);
if ($num!=0)
{
  $routput='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
  <td width="120"><a href="sou_main.php?action=systempage"><div class="b1">System</div></a></td>
  <td><b>Fraktionsforum</b></td>
  <td width="120">&nbsp;</td>
  </tr></table>';
  rahmen1_oben($routput);
?>


<table border="0" cellpadding="0" cellspacing="1">
<tr align="center">
<td width="1%" class="cell">&nbsp;</td>
<td width="39%" class="cell">Threadname</td>
<td width="15%" class="cell">gestartet von</td>
<td width="10%" class="cell">Antworten</td>
<td width="5%" class="cell">Hits</td>
<td width="30%" class="cell">Letzte Nachricht von</td>
<?
while($row = mysql_fetch_array($db_daten))
{
  $posttime =date ("d.m.Y \u\m H:i", $row[lastactive]);
  if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';} 

echo '<tr align="center">';
echo '<td>';

if($row[open]=="1")
{
  echo '<img src="'.$gpfad.'forum_off.gif" width="20" height="20" border="0" alt="alt">';
}
else
{
  echo '<img src="'.$gpfad.'offclosed.gif" width="20" height="20" border="0" alt="altclosed">';
}

echo '</td>';
echo '<td class="'.$bg.'" align="left">&nbsp;<a href="sou_main.php?action=fracforumpage&id='.$row[id].'">';

if($row[wichtig]=="1")echo "<u><b>Wichtig:</b></u> ";
echo '<b>'.$row[threadname].'</b></a></td>
<td class="'.$bg.'">'.$row[creator].'</td>
<td class="'.$bg.'">'.$row[anzposts].'</td>
<td class="'.$bg.'">'.$row[hits].'</td>
<td class="'.$bg.'"><font style="font-size: 8pt">gepostet von <i> '.$row[lastposter].'</i> am '.$posttime.' Uhr</font></td>
</tr>';

}

echo '</table>';

rahmen1_unten();

}//ende if $num!=0
if($vorschau)
{
$newstext = $nachricht;

$newstext=htmlspecialchars($newstext, ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
$newstext = nl2br_pre($newstext);

$newstext = formatierte_anzeige($newstext,$gpfad);
$newstext = preg_replace("/\[img\]([^[]*)\[\/img\]/","<img src=\"\\1\" border=0>",$newstext);

$newstext = stripslashes($newstext);
$nachricht = stripslashes($nachricht);

echo '<br>';
rahmen1_oben('<div align="center"><b>Vorschau</b></div>');
echo $newstext;
rahmen1_unten();
echo '<br>';

}
echo '<br><form action="sou_main.php?action=fracforumpage" method="POST">';

rahmen1_oben('<div align="center"><b>Neuen Thread erstellen</b></div>');

?>

<table border="0" cellspacing="0" cellpadding="0" width="600">
<tr align="left">
    <td>Titel:</td>
    <td align="left"><input type="text" name="title" size="50" maxlength="50" value="<? echo $_POST[title];?>"></td>
    <td><div align="right">Wichtig?&nbsp;&nbsp;</div></td>
    <td><input type="Checkbox" name="wichtig" value="1"
    <?
    if($_POST[wichtig]=="1")echo "checked";
    ?> ></td>
</tr>
<?=buttonpanel($gpfad);?>
<tr>
<td valign="top">Nachricht:</td>
<td align="center" colspan="3"><textarea rows="8" name="nachricht" id="nachricht" cols="63"><?echo $nachricht;?></textarea></td>
</tr>
<tr>
<td align="center" colspan="4"><div align="center"><input type="submit" value="Thread erstellen"  name="newthread">&nbsp;<input type="submit" value="Vorschau"  name="vorschau"></div></td>
</tr>
</table>

<?php
rahmen1_unten();
echo '</form>';
echo '<br>';
rahmen0_unten();
?>

</center>
</body>
</html>
<?php
function buttonpanel($gpfad)
{
echo '
   <tr>
              <td colspan="4" align="center" height=50><div align="center">
              <img src="'.$gpfad.'smilies/sm1.gif" onclick="init(\'smile1\')" alt="Smilie">
              <img src="'.$gpfad.'smilies/sm2.gif" onclick="init(\'smile2\')" alt="Smilie">
              <img src="'.$gpfad.'smilies/sm3.gif" onclick="init(\'smile3\')" alt="Smilie">
              <img src="'.$gpfad.'smilies/sm4.gif" onclick="init(\'smile4\')" alt="Smilie">
              <img src="'.$gpfad.'smilies/sm5.gif" onclick="init(\'smile5\')" alt="Smilie">
              <img src="'.$gpfad.'smilies/sm6.gif" onclick="init(\'smile6\')" alt="Smilie">
              <img src="'.$gpfad.'smilies/sm7.gif" onclick="init(\'smile7\')" alt="Smilie">
              <img src="'.$gpfad.'smilies/sm8.gif" onclick="init(\'smile8\')" alt="Smilie">
              <img src="'.$gpfad.'smilies/sm9.gif" onclick="init(\'smile9\')" alt="Smilie">
              <img src="'.$gpfad.'smilies/sm10.gif" onclick="init(\'smile10\')" alt="Smilie">
              <img src="'.$gpfad.'smilies/sm11.gif" onclick="init(\'smile11\')" alt="Smilie">
              <img src="'.$gpfad.'smilies/sm12.gif" onclick="init(\'smile12\')" alt="Smilie">
              <img src="'.$gpfad.'smilies/sm13.gif" onclick="init(\'smile13\')" alt="Smilie">
              <img src="'.$gpfad.'smilies/sm14.gif" onclick="init(\'smile14\')" alt="Smilie">
              <img src="'.$gpfad.'smilies/sm15.gif" onclick="init(\'smile15\')" alt="Smilie">
              <img src="'.$gpfad.'smilies/sm16.gif" onclick="init(\'smile16\')" alt="Smilie">
              <img src="'.$gpfad.'smilies/sm17.gif" onclick="init(\'smile17\')" alt="Smilie">
              <img src="'.$gpfad.'smilies/sm18.gif" onclick="init(\'smile18\')" alt="Smilie">
              <img src="'.$gpfad.'smilies/sm19.gif" onclick="init(\'smile19\')" alt="Smilie">
              <img src="'.$gpfad.'smilies/sm20.gif" onclick="init(\'smile20\')" alt="Smilie">
              <br>

              <input type="button" value="&nbsp;b&nbsp;"  onclick="init(\'fett\')">
              <input type="button" value="&nbsp;u&nbsp;"  onclick="init(\'under\')">
              <input type="button" value="&nbsp;i&nbsp;"  onclick="init(\'kursiv\')">
              <input type="button" value="Rot"  onclick="init(\'rot\')">
              <input type="button" value="Gelb"  onclick="init(\'gelb\')">
              <input type="button" value="Gr&uuml;n"  onclick="init(\'gruen\')">
              <input type="button" value="Weiss"  onclick="init(\'weiss\')">
              <input type="button" value="Blau"  onclick="init(\'blau\')">
              <input type="button" value="Farbe"  onclick="init(\'farbe\')">

              <input type="button" value="Größe"  onclick="init(\'size\')">
              <input type="button" value="center"  onclick="init(\'center\')">
              <input type="button" value="pre"  onclick="init(\'pre\')">
              <input type="button" value="Link"  onclick="init(\'www\')">
              <input type="button" value="@"  onclick="init(\'mail\')">
              <input type="button" value="Bild"  onclick="init(\'bild\')">
              <input type="button" value="&nbsp;?&nbsp;"  onclick="hilfe()">
              <input type="button" value="leeren"  onclick="leeren()">
              </div></td>
</tr>';
}

function db_aufbereitung($text)
{
    /*$text = str_replace('\"', '&quot;', $text);
    $text = str_replace('\'', '&acute;', $text); */
    $text = str_replace('script', 'skript', $text);
    $text = str_replace('Script', 'Skript', $text);

return $text;
}

function nl2br_pre($string) {

   $string = nl2br($string);

   $stat = preg_match("/\[pre[^\]]*?\](.|\n)*?\[\/pre\]/", $string, $ret);



   if ($stat != false) {

     $retr = preg_replace("/<br[^>]*?>/", "", $ret[0]);

     $retr = str_replace($ret[0], $retr, $string);


     return preg_replace("/\[\/pre\]<br[^>]*?>/", "[/pre]", $retr);
   }
   else {
     return $string;
   }
 }

function formatierte_anzeige($text,$gpfad)
{
    $text = str_replace(":)","<img src=\"" . $gpfad . "smilies/sm1.gif\" alt=\"lustiger Smilie\">",$text);
    $text = str_replace(":D","<img src=\"" . $gpfad . "smilies/sm2.gif\" alt=\"lachernder Smilie\">",$text);
    $text = str_replace(";)","<img src=\"" . $gpfad . "smilies/sm3.gif\" alt=\"zwinkernder Smilie\">",$text);
    $text = str_replace(":x","<img src=\"" . $gpfad . "smilies/sm4.gif\" alt=\"flamender Smilie\">",$text);
    $text = str_replace(":(","<img src=\"" . $gpfad . "smilies/sm5.gif\" alt=\"trauriger Smilie\">",$text);
    $text = str_replace("x(","<img src=\"" . $gpfad . "smilies/sm6.gif\" alt=\"Smilie\">",$text);
    $text = str_replace(":p","<img src=\"" . $gpfad . "smilies/sm7.gif\" alt=\"Zunge rausstreck Smilie\">",$text);
    $text = str_replace("(?)","<img src=\"" . $gpfad . "smilies/sm8.gif\" alt=\"Fragezeichen\">",$text);
    $text = str_replace("(!)","<img src=\"" . $gpfad . "smilies/sm9.gif\" alt=\"Ausrufezeichen\">",$text);
    $text = str_replace(":{","<img src=\"" . $gpfad . "smilies/sm10.gif\" alt=\"Smilie\">",$text);
    $text = str_replace(":}","<img src=\"" . $gpfad . "smilies/sm11.gif\" alt=\"Smilie\">",$text);
    $text = str_replace(":L","<img src=\"" . $gpfad . "smilies/sm12.gif\" alt=\"rauchender Smilie\">",$text);
    $text = str_replace(":nene:","<img src=\"" . $gpfad . "smilies/sm13.gif\" alt=\"nene\">",$text);
    $text = str_replace(":eek:","<img src=\"" . $gpfad . "smilies/sm14.gif\" alt=\"eek\">",$text);
    $text = str_replace(":applaus:","<img src=\"" . $gpfad . "smilies/sm15.gif\" alt=\"applaus\">",$text);
    $text = str_replace(":cry:","<img src=\"" . $gpfad . "smilies/sm16.gif\" alt=\"cry\">",$text);
    $text = str_replace(":sleep:","<img src=\"" . $gpfad . "smilies/sm17.gif\" alt=\"sleep\">",$text);
    $text = str_replace(":rolleyes:","<img src=\"" . $gpfad . "smilies/sm18.gif\" alt=\"Rolleyes\">",$text);
    $text = str_replace(":wand:","<img src=\"" . $gpfad . "smilies/sm19.gif\" alt=\"Wand\">",$text);
    $text = str_replace(":dead:","<img src=\"" . $gpfad . "smilies/sm20.gif\" alt=\"Dead\">",$text);
    
    $text = str_replace ("Ã¤", "&auml;", $text);
    $text = str_replace ("Ã„", "&Auml;", $text);
    $text = str_replace ("Ã¶", "&ouml;", $text);
    $text = str_replace ("Ã–", "&Ouml;", $text);
    $text = str_replace ("Ã¼", "&uuml;", $text);
    $text = str_replace ("Ãœ", "&Uuml;", $text);
    $text = str_replace ("ÃŸ", "&szlig;", $text);

    $text = preg_replace("/\[b\]/i", "<b>",$text);
    $text = preg_replace("/\[\/b\]/i", "</b>",$text);

    $text = preg_replace("/\[i\]/i", "<i>",$text);
    $text = preg_replace("/\[\/i]/i", "</i>",$text);

    $text = preg_replace("/\[u]/i", "<u>",$text);
    $text = preg_replace("/\[\/u]/i", "</u>",$text);

    $text = preg_replace("/\[center\]/i", "<center>",$text);
    $text = preg_replace("/\[\/center\]/i", "</center>",$text);

    $text = preg_replace("/\[pre]/i", "<pre>",$text);
    $text = preg_replace("/\[\/pre]/i", "</pre>",$text);

    $text = str_replace("[CGRUEN]","<font color=\"#28FF50\">",$text);
    $text = str_replace("[CROT]","<font color=\"#F10505\">",$text);
    $text = str_replace("[CW]","<font color=\"#FFFFFF\">",$text);
    $text = str_replace("[CGELB]","<font color=\"#FDFB59\">",$text);
    $text = str_replace("[CDE]","<font color=\"#3399FF\">",$text);

    $text = preg_replace("/\[email\]([^[]*)\[\/email\]/","<a href=\"mailto:\\1\">\\1</a>",$text);
    $text = preg_replace("/\[url\]([^[]*)\[\/url\]/i",'<a href="\\1" target="_blank">\\1</a>',$text);
    $text = preg_replace("/\[color=#([^[]+)\]([^[]*)\[\/color\]/","<font color=\"#\\1\" >\\2</font>",$text);
    $text = preg_replace("/\[size=([^[]+)\]([^[]*)\[\/size\]/","<font size=\"\\1\" >\\2</font>",$text);
    
	return $text;
}
?>