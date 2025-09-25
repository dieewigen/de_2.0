<?php
function xecho($str)
{
        global $cachefile;
        //echo $str;
        if ($cachefile) fwrite ($cachefile, $str);
}

////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
// generell benötigte Daten auslesen
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////

//Gesamtpunktezahl aller Spieler
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT SUM(score) AS value FROM  de_user_data WHERE sector > 1 AND npc=0;", []);
$row = mysqli_fetch_array($result);
$server_gesamt_score=$row['value'];

//Gesamtzahl aller Spielerkollektoren
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT SUM(col) AS value FROM  de_user_data WHERE sector > 1 AND npc=0;", []);
$row = mysqli_fetch_array($result);
$server_gesamt_col=$row['value'];

////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
//spieler - punkte
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
$filename = $directory."cache/toplist/top1a.tmp";
$cachefile = fopen ($filename, 'w');
xecho('
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37" align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="25" class="ro"><div class="cellu">'.$wt_lang['platz'].'</div></td>
<td width="60" class="ro"><div class="cellu">'.$wt_lang['rang'].'</div></td>
<td width="50" class="ro"><div class="cellu">'.$wt_lang['system1'].'</div></td>
<td width="200" class="ro"><div class="cellu">'.$wt_lang['name'].'</div></td>
<td width="110" class="ro"><div class="cellu">'.$wt_lang['kollektoren'].'</div></td>
<td width="100" class="ro"><div class="cellu">'.$wt_lang['punkte'].'</div></td>
<td width="13" class="ro">&nbsp;</td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="7">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="25">
<col width="60">
<col width="50">
<col width="200">
<col width="110">
<col width="100">
<col width="13">
</colgroup>
<script type="text/javascript">
<!--
');

$varsector='';
$varsystem='';
$varname='';
$varlevel='';
$varscore='';
$varquestpunkte='';
$varfame='';
$varrang='';
$varcol='';
$varalterplatz='';

$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.spielername, de_user_data.score, de_user_data.col, de_user_data.sector, de_user_data.`system`, de_user_data.allytag, de_user_data.status, de_user_data.platz_last_day,de_user_data.platz, de_user_data.rang  FROM de_user_data WHERE sector > 0 AND npc=0 ORDER BY score DESC LIMIT 100");
$platz_i=1;
$time=date("YmdHis");
while($row = mysqli_fetch_array($result)) //jeder gefundene datensatz wird geprueft
{
  $rang_nr=$row["rang"];
  if($rang_nr==0)
  {
    $rang='<font color=\"#FF0000\">'.$rangnamen[$rang_nr].' ('.$winticks.')';
  }
  else $rang=$rangnamen[$rang_nr];

  if ($varrang!='')$varrang=$varrang.',';
  $varrang=$varrang.'"'.$rang.'"';

  if ($varsector!='')$varsector=$varsector.',';
  $varsector=$varsector.$row["sector"];

  if ($varsystem!='')$varsystem=$varsystem.',';
  $varsystem=$varsystem.$row["system"];

  if ($varname!='')$varname=$varname.',';
  $varname=$varname.'"'.$row["spielername"].'"';

  if ($varcol!='')$varcol=$varcol.',';
  $varcol=$varcol.'"'.number_format($row["col"], 0,"",".").'"';

  if ($varscore!='')$varscore=$varscore.',';
  $varscore=$varscore.'"'.number_format($row["score"], 0,"",".").'"';

  if ($varalterplatz!='')$varalterplatz=$varalterplatz.',';



  if($row["platz_last_day"]==$row["platz"])
       $varalterplatz=$varalterplatz.'"g"';
  if($row["platz_last_day"]>$row["platz"])
       $varalterplatz=$varalterplatz.'"u|'.($row["platz_last_day"]-$row["platz"]).'"';
  if($row["platz_last_day"]<$row["platz"])
       $varalterplatz=$varalterplatz.'"d|'.($row["platz"]-$row["platz_last_day"]).'"';

  $platz_i++;
}
$varrang='var r=new Array('.$varrang.');';
$varsector='var s=new Array('.$varsector.');';
$varsystem='var t=new Array('.$varsystem.');';
$varname='var n=new Array('.$varname.');';
$varcol='var c=new Array('.$varcol.');';
$varscore='var p=new Array('.$varscore.');';
$varalterplatz='var o=new Array('.$varalterplatz.');';
xecho($varrang);
xecho($varsector);
xecho($varsystem);
xecho($varname);
xecho($varcol);
xecho($varscore);
xecho($varalterplatz);
xecho('for (i=0; i<r.length; i++){
var temp=o[i].split("|");
if(temp[0]=="u")
grafik="<img border=\"0\" src=\""+gpfad+"g/tl_up.gif\" width=\"11\" height=\"11\" title=\"+"+temp[1]+"\" alt=\"+"+temp[1]+"\">";
if(temp[0]=="d")
grafik="<img border=\"0\" src=\""+gpfad+"g/tl_down.gif\" width=\"11\" height=\"11\" title=\"-"+temp[1]+"\" alt=\"-"+temp[1]+"\">";
if(temp[0]=="g")
grafik="<img border=\"0\" src=\""+gpfad+"g/tl_constant.gif\" width=\"11\" height=\"11\" title=\"'.$wt_lang['unveraendert'].'\" alt=\"'.$wt_lang['unveraendert'].'\">";
var j=i+1;document.write("<tr><td class=\"cc\">"+j+"</td><td class=\"cc\">"+r[i]+"</td><td class=\"cc\">"+s[i]+":"+t[i]+"</td><td class=\"cc\"><a href=\"details.php?se="+s[i]+"&sy="+t[i]+"\">"+n[i]+"</a></td><td class=\"cc\">"+c[i]+"</td><td class=\"cr\">"+p[i]+"</td><td class=\"cr\">"+grafik+"</td></tr>");}');
xecho('
// -->
</script>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<br>');

////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
//spieler - kollektoren
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
$filename = $directory."cache/toplist/top1b.tmp";
$cachefile = fopen ($filename, 'w');
xecho('
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37" align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="25" class="ro"><div class="cellu">'.$wt_lang['platz'].'</div></td>
<td width="60" class="ro"><div class="cellu">'.$wt_lang['rang'].'</div></td>
<td width="50" class="ro"><div class="cellu">'.$wt_lang['system1'].'</div></td>
<td width="200" class="ro"><div class="cellu">'.$wt_lang['name'].'</div></td>
<td width="110" class="ro"><div class="cellu">'.$wt_lang['kollektoren'].'</div></td>
<td width="100" class="ro"><div class="cellu">'.$wt_lang['punkte'].'</div></td>
<td width="13" class="ro">&nbsp;</td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="7">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="25">
<col width="60">
<col width="50">
<col width="200">
<col width="110">
<col width="100">
<col width="13">
</colgroup>
<script type="text/javascript">
<!--
');


$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.spielername, de_user_data.score, de_user_data.col, de_user_data.sector, de_user_data.`system`, de_user_data.allytag, de_user_data.status, de_user_data.rang  FROM de_user_data WHERE sector > 0 AND npc=0 ORDER BY col DESC LIMIT 200");
$platz_i=1;
$time=date("YmdHis");
$varrang='';
$varsector='';
$varsystem='';
$varname='';
$varcol='';
$varscore='';

while($row = mysqli_fetch_array($result)) //jeder gefundene datensatz wird geprueft
{
  $rang_nr=$row["rang"];
  if($rang_nr==0)
  {
    $rang='<font color=\"#FF0000\">'.$rangnamen[$rang_nr].' ('.$winticks.')';
  }
  else $rang=$rangnamen[$rang_nr];

  if ($varrang!='')$varrang=$varrang.',';
  $varrang=$varrang.'"'.$rang.'"';

  if ($varsector!='')$varsector=$varsector.',';
  $varsector=$varsector.$row["sector"];

  if ($varsystem!='')$varsystem=$varsystem.',';
  $varsystem=$varsystem.$row["system"];

  if ($varname!='')$varname=$varname.',';
  $varname=$varname.'"'.$row["spielername"].'"';

  if ($varcol!='')$varcol=$varcol.',';
  $varcol=$varcol.'"'.number_format($row["col"], 0,"",".").'"';

  if ($varscore!='')$varscore=$varscore.',';
  $varscore=$varscore.'"'.number_format($row["score"], 0,"",".").'"';

  $platz_i++;
}
$varrang='var r=new Array('.$varrang.');';
$varsector='var s=new Array('.$varsector.');';
$varsystem='var t=new Array('.$varsystem.');';
$varname='var n=new Array('.$varname.');';
$varcol='var c=new Array('.$varcol.');';
$varscore='var p=new Array('.$varscore.');';
xecho($varrang);
xecho($varsector);
xecho($varsystem);
xecho($varname);
xecho($varcol);
xecho($varscore);
xecho($varalterplatz);
xecho('for (i=0; i<r.length; i++){var j=i+1;var colag=c[i].replace(".","")/c[0].replace(".","")*'.$sv_max_col_attgrenze.';if(colag<'.$sv_min_col_attgrenze.')colag='.$sv_min_col_attgrenze.';document.write("<tr><td class=\"cc\">"+j+"</td><td class=\"cc\">"+r[i]+"</td><td class=\"cc\">"+s[i]+":"+t[i]+"</td><td class=\"cc\"><a href=\"details.php?se="+s[i]+"&sy="+t[i]+"\">"+n[i]+"</a></td><td class=\"cc\" title=\""+(Math.round(colag*c[i].replace(".","")))+"\">"+c[i]+"</td><td class=\"cr\">"+p[i]+"</td></tr>");}');
xecho('
// -->
</script>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<br>');

////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
//spieler - Türme
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////

$filename = $directory."cache/toplist/top1c.tmp";
$cachefile = fopen ($filename, 'w');
xecho('
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37" align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="25" class="ro"><div class="cellu">'.$wt_lang['platz'].'</div></td>
<td width="60" class="ro"><div class="cellu">'.$wt_lang['rang'].'</div></td>
<td width="50" class="ro"><div class="cellu">'.$wt_lang['system1'].'</div></td>
<td width="200" class="ro"><div class="cellu">'.$wt_lang['name'].'</div></td>
<td width="110" class="ro"><div class="cellu">'.$wt_lang['tuerme'].'</div></td>
<td width="100" class="ro"><div class="cellu">'.$wt_lang['punkte'].'</div></td>
<td width="13" class="ro">&nbsp;</td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="7">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="25">
<col width="60">
<col width="50">
<col width="200">
<col width="110">
<col width="100">
<col width="13">
</colgroup>
<script type="text/javascript">
<!--
');
?>
<?php


$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.spielername, de_user_data.score, de_user_data.col, de_user_data.sector, de_user_data.`system`, de_user_data.allytag, de_user_data.status, de_user_data.rang, de_user_data.e100+de_user_data.e101+de_user_data.e102+de_user_data.e103+de_user_data.e104 AS tower FROM de_user_data WHERE sector > 0 AND npc=0 ORDER BY tower DESC LIMIT 100");
$platz_i=1;
$time=date("YmdHis");
$varrang='';
$varsector='';
$varsystem='';
$varname='';
$varcol='';
$varscore='';

while($row = mysqli_fetch_array($result)) //jeder gefundene datensatz wird geprueft
{
  $rang_nr=$row["rang"];
  if($rang_nr==0)
  {
    $rang='<font color=\"#FF0000\">'.$rangnamen[$rang_nr].' ('.$winticks.')';
  }
  else $rang=$rangnamen[$rang_nr];

  if ($varrang!='')$varrang=$varrang.',';
  $varrang=$varrang.'"'.$rang.'"';

  if ($varsector!='')$varsector=$varsector.',';
  $varsector=$varsector.$row["sector"];

  if ($varsystem!='')$varsystem=$varsystem.',';
  $varsystem=$varsystem.$row["system"];

  if ($varname!='')$varname=$varname.',';
  $varname=$varname.'"'.$row["spielername"].'"';

  if ($varcol!='')$varcol=$varcol.',';
  $varcol=$varcol.'"'.number_format($row["tower"], 0,"",".").'"';

  if ($varscore!='')$varscore=$varscore.',';
  $varscore=$varscore.'"'.number_format($row["score"], 0,"",".").'"';

  $platz_i++;
}
$varrang='var r=new Array('.$varrang.');';
$varsector='var s=new Array('.$varsector.');';
$varsystem='var t=new Array('.$varsystem.');';
$varname='var n=new Array('.$varname.');';
$varcol='var c=new Array('.$varcol.');';
$varscore='var p=new Array('.$varscore.');';
xecho($varrang);
xecho($varsector);
xecho($varsystem);
xecho($varname);
xecho($varcol);
xecho($varscore);
xecho($varalterplatz);
xecho('for (i=0; i<r.length; i++){var j=i+1;document.write("<tr><td class=\"cc\">"+j+"</td><td class=\"cc\">"+r[i]+"</td><td class=\"cc\">"+s[i]+":"+t[i]+"</td><td class=\"cc\"><a href=\"details.php?se="+s[i]+"&sy="+t[i]+"\">"+n[i]+"</a></td><td class=\"cc\">"+c[i]+"</td><td class=\"cr\">"+p[i]+"</td></tr>");}');
xecho('
// -->
</script>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<br>');

////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
//spieler - rundenpunkte
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
$filename = $directory."cache/toplist/top1d.tmp";
$cachefile = fopen ($filename, 'w');
xecho('
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37" align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="25" class="ro"><div class="cellu">'.$wt_lang['platz'].'</div></td>
<td width="60" class="ro"><div class="cellu">'.$wt_lang['rang'].'</div></td>
<td width="50" class="ro"><div class="cellu">'.$wt_lang['system1'].'</div></td>
<td width="200" class="ro"><div class="cellu">'.$wt_lang['name'].'</div></td>
<td width="110" class="ro"><div class="cellu">'.$wt_lang['rundenpunkte'].'</div></td>
<td width="100" class="ro"><div class="cellu">'.$wt_lang['punkte'].'</div></td>
<td width="13" class="ro">&nbsp;</td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="7">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="25">
<col width="60">
<col width="50">
<col width="200">
<col width="110">
<col width="100">
<col width="13">
</colgroup>
<script type="text/javascript">
<!--
');

$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.spielername, de_user_data.score, de_user_data.col, de_user_data.sector, de_user_data.`system`, de_user_data.allytag, de_user_data.status, de_user_data.rang, de_user_data.roundpoints FROM de_user_data WHERE sector > 0 AND npc=0 ORDER BY roundpoints DESC LIMIT 100");
$platz_i=1;
$time=date("YmdHis");
$varrang='';
$varsector='';
$varsystem='';
$varname='';
$varcol='';
$varscore='';

while($row = mysqli_fetch_array($result)) //jeder gefundene datensatz wird geprueft
{
  $rang_nr=$row["rang"];
  if($rang_nr==0)
  {
    $rang='<font color=\"#FF0000\">'.$rangnamen[$rang_nr].' ('.$winticks.')';
  }
  else $rang=$rangnamen[$rang_nr];

  if ($varrang!='')$varrang=$varrang.',';
  $varrang=$varrang.'"'.$rang.'"';

  if ($varsector!='')$varsector=$varsector.',';
  $varsector=$varsector.$row["sector"];

  if ($varsystem!='')$varsystem=$varsystem.',';
  $varsystem=$varsystem.$row["system"];

  if ($varname!='')$varname=$varname.',';
  $varname=$varname.'"'.$row["spielername"].'"';

  if ($varcol!='')$varcol=$varcol.',';
  $varcol=$varcol.'"'.number_format($row["roundpoints"], 0,"",".").'"';

  if ($varscore!='')$varscore=$varscore.',';
  $varscore=$varscore.'"'.number_format($row["score"], 0,"",".").'"';

  $platz_i++;
}
$varrang='var r=new Array('.$varrang.');';
$varsector='var s=new Array('.$varsector.');';
$varsystem='var t=new Array('.$varsystem.');';
$varname='var n=new Array('.$varname.');';
$varcol='var c=new Array('.$varcol.');';
$varscore='var p=new Array('.$varscore.');';
xecho($varrang);
xecho($varsector);
xecho($varsystem);
xecho($varname);
xecho($varcol);
xecho($varscore);
xecho($varalterplatz);
xecho('for (i=0; i<r.length; i++){var j=i+1;document.write("<tr><td class=\"cc\">"+j+"</td><td class=\"cc\">"+r[i]+"</td><td class=\"cc\">"+s[i]+":"+t[i]+"</td><td class=\"cc\"><a href=\"details.php?se="+s[i]+"&sy="+t[i]+"\">"+n[i]+"</a></td><td class=\"cc\">"+c[i]+"</td><td class=\"cr\">"+p[i]+"</td></tr>");}');
xecho('
// -->
</script>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<br>');

//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
//spieler - kopfgeldjäger
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
$filename = $directory."cache/toplist/top1f.tmp";
$cachefile = fopen ($filename, 'w');
xecho('
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37" align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="25" class="ro"><div class="cellu">'.$wt_lang['platz'].'</div></td>
<td width="60" class="ro"><div class="cellu">'.$wt_lang['rang'].'</div></td>
<td width="50" class="ro"><div class="cellu">'.$wt_lang['system1'].'</div></td>
<td width="190" class="ro"><div class="cellu">'.$wt_lang['name'].'</div></td>
<td width="120" class="ro"><div class="cellu">'.$wt_lang['gesamtenergie'].'&nbsp;in&nbsp;M</div></td>
<td width="100" class="ro"><div class="cellu">'.$wt_lang['punkte'].'</div></td>
<td width="13" class="ro">&nbsp;</td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="7">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="25">
<col width="60">
<col width="50">
<col width="200">
<col width="110">
<col width="100">
<col width="13">
</colgroup>
<script type="text/javascript">
<!--
');

$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.spielername, de_user_data.score, de_user_data.col, de_user_data.sector, de_user_data.`system`, de_user_data.rang, de_user_data.kgget FROM de_user_data WHERE sector > 1 AND npc=0 ORDER BY kgget DESC LIMIT 100");
$gesamtuser = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data");
$gesamtuser = mysqli_num_rows($gesamtuser);
$rang_schritt = $gesamtuser*0.042;
$platz_i=1;
$time=date("YmdHis");
$varrang='';
$varsector='';
$varsystem='';
$varname='';
$varcol='';
$varscore='';

while($row = mysqli_fetch_array($result)) //jeder gefundene datensatz wird geprueft
{
  $rang_nr=$row["rang"];
  //if ($winid==$row["user_id"])
  if($rang_nr==0)
  {
    //$rang_nr=0;
    $rang='<font color=\"#FF0000\">'.$rangnamen[$rang_nr].' ('.$winticks.')';
  }
  else $rang=$rangnamen[$rang_nr];

  if ($varrang!='')$varrang=$varrang.',';
  $varrang=$varrang.'"'.$rang.'"';

  if ($varsector!='')$varsector=$varsector.',';
  $varsector=$varsector.$row["sector"];

  if ($varsystem!='')$varsystem=$varsystem.',';
  $varsystem=$varsystem.$row["system"];

  if ($varname!='')$varname=$varname.',';
  $varname=$varname.'"'.$row["spielername"].'"';

  if ($varcol!='')$varcol=$varcol.',';
  $varcol=$varcol.'"'.number_format($row["kgget"], 0,"",".").'"';

  if ($varscore!='')$varscore=$varscore.',';
  $varscore=$varscore.'"'.number_format($row["score"], 0,"",".").'"';

  $platz_i++;
}
$varrang='var r=new Array('.$varrang.');';
$varsector='var s=new Array('.$varsector.');';
$varsystem='var t=new Array('.$varsystem.');';
$varname='var n=new Array('.$varname.');';
$varcol='var c=new Array('.$varcol.');';
$varscore='var p=new Array('.$varscore.');';
xecho($varrang);
xecho($varsector);
xecho($varsystem);
xecho($varname);
xecho($varcol);
xecho($varscore);
xecho($varalterplatz);
xecho('for (i=0; i<r.length; i++){var j=i+1;document.write("<tr><td class=\"cc\">"+j+"</td><td class=\"cc\">"+r[i]+"</td><td class=\"cc\">"+s[i]+":"+t[i]+"</td><td class=\"cc\"><a href=\"details.php?se="+s[i]+"&sy="+t[i]+"\">"+n[i]+"</a></td><td class=\"cr\">"+c[i]+"</td><td class=\"cr\">"+p[i]+"</td></tr>");}');
xecho('
// -->
</script>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<br>');

//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
//spieler - kopfgeld
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

$filename = $directory."cache/toplist/top1e.tmp";
$cachefile = fopen ($filename, 'w');
xecho('
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37" align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="25" class="ro"><div class="cellu">'.$wt_lang['platz'].'</div></td>
<td width="60" class="ro"><div class="cellu">'.$wt_lang['rang'].'</div></td>
<td width="50" class="ro"><div class="cellu">'.$wt_lang['system1'].'</div></td>
<td width="190" class="ro"><div class="cellu">'.$wt_lang['name'].'</div></td>
<td width="120" class="ro"><div class="cellu">'.$wt_lang['gesamtenergie'].'&nbsp;in&nbsp;M</div></td>
<td width="100" class="ro"><div class="cellu">'.$wt_lang['punkte'].'</div></td>
<td width="13" class="ro">&nbsp;</td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="7">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="25">
<col width="60">
<col width="50">
<col width="200">
<col width="110">
<col width="100">
<col width="13">
</colgroup>
<script type="text/javascript">
<!--
');
?>
<?php


$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.spielername, de_user_data.score, de_user_data.col, de_user_data.sector, de_user_data.`system`, de_user_data.rang, (de_user_data.kg01+de_user_data.kg02*2+de_user_data.kg03*3+de_user_data.kg04*4) AS gesamtenergie FROM de_user_data WHERE sector > 1 AND npc=0 ORDER BY gesamtenergie DESC LIMIT 100");
$gesamtuser = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data");
$gesamtuser = mysqli_num_rows($gesamtuser);
$rang_schritt = $gesamtuser*0.042;
$platz_i=1;
$time=date("YmdHis");
$varrang='';
$varsector='';
$varsystem='';
$varname='';
$varcol='';
$varscore='';

while($row = mysqli_fetch_array($result)) //jeder gefundene datensatz wird geprueft
{
  $rang_nr=$row["rang"];
  //if ($winid==$row["user_id"])
  if($rang_nr==0)
  {
    //$rang_nr=0;
    $rang='<font color=\"#FF0000\">'.$rangnamen[$rang_nr].' ('.$winticks.')';
  }
  else $rang=$rangnamen[$rang_nr];

  if ($varrang!='')$varrang=$varrang.',';
  $varrang=$varrang.'"'.$rang.'"';

  if ($varsector!='')$varsector=$varsector.',';
  $varsector=$varsector.$row["sector"];

  if ($varsystem!='')$varsystem=$varsystem.',';
  $varsystem=$varsystem.$row["system"];

  if ($varname!='')$varname=$varname.',';
  $varname=$varname.'"'.$row["spielername"].'"';

  if ($varcol!='')$varcol=$varcol.',';
  $varcol=$varcol.'"'.number_format($row["gesamtenergie"], 0,"",".").'"';

  if ($varscore!='')$varscore=$varscore.',';
  $varscore=$varscore.'"'.number_format($row["score"], 0,"",".").'"';

  $platz_i++;
}
$varrang='var r=new Array('.$varrang.');';
$varsector='var s=new Array('.$varsector.');';
$varsystem='var t=new Array('.$varsystem.');';
$varname='var n=new Array('.$varname.');';
$varcol='var c=new Array('.$varcol.');';
$varscore='var p=new Array('.$varscore.');';
xecho($varrang);
xecho($varsector);
xecho($varsystem);
xecho($varname);
xecho($varcol);
xecho($varscore);
xecho($varalterplatz);
xecho('for (i=0; i<r.length; i++){var j=i+1;document.write("<tr><td class=\"cc\">"+j+"</td><td class=\"cc\">"+r[i]+"</td><td class=\"cc\">"+s[i]+":"+t[i]+"</td><td class=\"cc\"><a href=\"details.php?se="+s[i]+"&sy="+t[i]+"\">"+n[i]+"</a></td><td class=\"cr\">"+c[i]+"</td><td class=\"cr\">"+p[i]+"</td></tr>");}');
xecho('
// -->
</script>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<br>');

//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
//spieler - errungenschaften
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

$filename = $directory."cache/toplist/top1g.tmp";
$cachefile = fopen ($filename, 'w');

xecho('<table border="0" cellpadding="0" cellspacing="0">
<tr height="37" align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="25" class="ro"><div class="cellu">'.$wt_lang['platz'].'</div></td>
<td width="60" class="ro"><div class="cellu">'.$wt_lang['rang'].'</div></td>
<td width="50" class="ro"><div class="cellu">'.$wt_lang['system1'].'</div></td>
<td width="200" class="ro"><div class="cellu">'.$wt_lang['name'].'</div></td>
<td width="110" class="ro"><div class="cellu">'.$wt_lang['errungenschaften'].'</div></td>
<td width="100" class="ro"><div class="cellu">'.$wt_lang['punkte'].'</div></td>
<td width="13" class="ro">&nbsp;</td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="7">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="25">
<col width="60">
<col width="50">
<col width="200">
<col width="110">
<col width="100">
<col width="13">
</colgroup>
<script type="text/javascript">
<!--
');
$varsector='';
$varsystem='';
$varname='';
$varlevel='';
$varscore='';
$varquestpunkte='';
$varfame='';

$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.spielername, de_user_data.sector, de_user_data.`system`, de_user_data.rang, de_user_data.score, de_user_data.platz, (de_user_achievement.ac1+de_user_achievement.ac2+de_user_achievement.ac3+de_user_achievement.ac4+de_user_achievement.ac5+de_user_achievement.ac6+de_user_achievement.ac7+de_user_achievement.ac8+de_user_achievement.ac9+de_user_achievement.ac10+de_user_achievement.ac11+de_user_achievement.ac12+de_user_achievement.ac13+de_user_achievement.ac14+de_user_achievement.ac15+de_user_achievement.ac16+de_user_achievement.ac17+de_user_achievement.ac18+de_user_achievement.ac19+de_user_achievement.ac20+de_user_achievement.ac21+de_user_achievement.ac22+de_user_achievement.ac23+de_user_achievement.ac24+de_user_achievement.ac25+de_user_achievement.ac999) AS wert FROM de_user_data LEFT JOIN de_user_achievement on(de_user_data.user_id = de_user_achievement.user_id) ORDER BY wert DESC LIMIT 100");
$gesamtuser = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data");
$gesamtuser = mysqli_num_rows($gesamtuser);
$rang_schritt = $gesamtuser*0.042;
$platz_i=1;
$time=date("YmdHis");
$varrang='';
$varsector='';
$varsystem='';
$varname='';
$varcol='';
$varscore='';

while($row = mysqli_fetch_array($result)) //jeder gefundene datensatz wird geprueft
{
  /*$rang_nr=1;
  $rang_zaehler=$rang_schritt;
  while ($platz_i>$rang_zaehler) //rang wird gesucht
  {
    $rang_nr++;
    $rang_zaehler=$rang_zaehler+$rang_schritt;
  }
  //if ($platz_i==1 and $row["score"]>=$winscore)*/
  $rang_nr=$row["rang"];
  //if ($winid==$row["user_id"])
  if($rang_nr==0)
  {
    //$rang_nr=0;
    $rang='<font color=\"#FF0000\">'.$rangnamen[$rang_nr].' ('.$winticks.')';
  }
  else $rang=$rangnamen[$rang_nr];

  if ($varrang!='')$varrang=$varrang.',';
  $varrang=$varrang.'"'.$rang.'"';

  if ($varsector!='')$varsector=$varsector.',';
  $varsector=$varsector.$row["sector"];

  if ($varsystem!='')$varsystem=$varsystem.',';
  $varsystem=$varsystem.$row["system"];

  if ($varname!='')$varname=$varname.',';
  $varname=$varname.'"'.$row["spielername"].'"';

  if ($varcol!='')$varcol=$varcol.',';
  $varcol=$varcol.'"'.number_format($row["wert"] ?? 0, 0,"",".").'"';

  if ($varscore!='')$varscore=$varscore.',';
  $varscore=$varscore.'"'.number_format($row["score"], 0,"",".").'"';

  $platz_i++;
}
$varrang='var r=new Array('.$varrang.');';
$varsector='var s=new Array('.$varsector.');';
$varsystem='var t=new Array('.$varsystem.');';
$varname='var n=new Array('.$varname.');';
$varcol='var c=new Array('.$varcol.');';
$varscore='var p=new Array('.$varscore.');';
xecho($varrang);
xecho($varsector);
xecho($varsystem);
xecho($varname);
xecho($varcol);
xecho($varscore);
xecho($varalterplatz);
xecho('for (i=0; i<r.length; i++){var j=i+1;document.write("<tr><td class=\"cc\">"+j+"</td><td class=\"cc\">"+r[i]+"</td><td class=\"cc\">"+s[i]+":"+t[i]+"</td><td class=\"cc\"><a href=\"details.php?se="+s[i]+"&sy="+t[i]+"\">"+n[i]+"</a></td><td class=\"cr\">"+c[i]+"</td><td class=\"cr\">"+p[i]+"</td></tr>");}');
xecho('
// -->
</script>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<br>');

//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//spieler - ehpunkte
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
$filename = $directory."cache/toplist/top1h.tmp";
$cachefile = fopen ($filename, 'w');
xecho('
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37" align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="25" class="ro"><div class="cellu">'.$wt_lang['platz'].'</div></td>
<td width="60" class="ro"><div class="cellu">'.$wt_lang['rang'].'</div></td>
<td width="50" class="ro"><div class="cellu">'.$wt_lang['system1'].'</div></td>
<td width="200" class="ro"><div class="cellu">'.$wt_lang['name'].'</div></td>
<td width="110" class="ro"><div class="cellu">'.$wt_lang['ehpunkte'].'</div></td>
<td width="100" class="ro"><div class="cellu">'.$wt_lang['punkte'].'</div></td>
<td width="13" class="ro">&nbsp;</td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="7">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="25">
<col width="60">
<col width="50">
<col width="200">
<col width="110">
<col width="100">
<col width="13">
</colgroup>
<script type="text/javascript">
<!--
');

$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.spielername, de_user_data.score, de_user_data.ehscore, de_user_data.sector, de_user_data.`system`, de_user_data.allytag, de_user_data.status, de_user_data.rang  FROM de_user_data WHERE sector > 0 AND npc=0 ORDER BY ehscore DESC LIMIT 100");
$platz_i=1;
$time=date("YmdHis");
$varrang='';
$varsector='';
$varsystem='';
$varname='';
$varcol='';
$varscore='';

while($row = mysqli_fetch_array($result)) //jeder gefundene datensatz wird geprueft
{
  $rang_nr=$row["rang"];
  if($rang_nr==0)
  {
    $rang='<font color=\"#FF0000\">'.$rangnamen[$rang_nr].' ('.$winticks.')';
  }
  else $rang=$rangnamen[$rang_nr];

  if ($varrang!='')$varrang=$varrang.',';
  $varrang=$varrang.'"'.$rang.'"';

  if ($varsector!='')$varsector=$varsector.',';
  $varsector=$varsector.$row["sector"];

  if ($varsystem!='')$varsystem=$varsystem.',';
  $varsystem=$varsystem.$row["system"];

  if ($varname!='')$varname=$varname.',';
  $varname=$varname.'"'.$row["spielername"].'"';

  if ($varcol!='')$varcol=$varcol.',';
  $varcol=$varcol.'"'.number_format($row["ehscore"], 0,"",".").'"';

  if ($varscore!='')$varscore=$varscore.',';
  $varscore=$varscore.'"'.number_format($row["score"], 0,"",".").'"';

  $platz_i++;
}
$varrang='var r=new Array('.$varrang.');';
$varsector='var s=new Array('.$varsector.');';
$varsystem='var t=new Array('.$varsystem.');';
$varname='var n=new Array('.$varname.');';
$varcol='var c=new Array('.$varcol.');';
$varscore='var p=new Array('.$varscore.');';
xecho($varrang);
xecho($varsector);
xecho($varsystem);
xecho($varname);
xecho($varcol);
xecho($varscore);
xecho($varalterplatz);
xecho('for (i=0; i<r.length; i++){var j=i+1;document.write("<tr><td class=\"cc\">"+j+"</td><td class=\"cc\">"+r[i]+"</td><td class=\"cc\">"+s[i]+":"+t[i]+"</td><td class=\"cc\"><a href=\"details.php?se="+s[i]+"&sy="+t[i]+"\">"+n[i]+"</a></td><td class=\"cc\">"+c[i]+"</td><td class=\"cr\">"+p[i]+"</td></tr>");}');
xecho('
// -->
</script>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<br>');

//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//spieler - eh_counter
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
$filename = $directory."cache/toplist/top1i.tmp";
$cachefile = fopen ($filename, 'w');
xecho('
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37" align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="25" class="ro"><div class="cellu">'.$wt_lang['platz'].'</div></td>
<td width="60" class="ro"><div class="cellu">'.$wt_lang['rang'].'</div></td>
<td width="50" class="ro"><div class="cellu">'.$wt_lang['system1'].'</div></td>
<td width="200" class="ro"><div class="cellu">'.$wt_lang['name'].'</div></td>
<td width="110" class="ro"><div class="cellu">EH-Counter</div></td>
<td width="100" class="ro"><div class="cellu">EH-Siege</div></td>
<td width="13" class="ro">&nbsp;</td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="7">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="25">
<col width="60">
<col width="50">
<col width="200">
<col width="110">
<col width="100">
<col width="13">
</colgroup>
<script type="text/javascript">
<!--
');

$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.spielername, de_user_data.score, de_user_data.ehscore, de_user_data.sector, de_user_data.`system`, de_user_data.allytag, de_user_data.status, de_user_data.rang, de_user_data.eh_counter, de_user_data.eh_siege FROM de_user_data WHERE sector > 0 AND npc=0 ORDER BY eh_counter DESC LIMIT 100");
$platz_i=1;
$time=date("YmdHis");
$varrang='';
$varsector='';
$varsystem='';
$varname='';
$varcol='';
$varscore='';

while($row = mysqli_fetch_array($result)) //jeder gefundene datensatz wird geprueft
{
  $rang_nr=$row["rang"];
  if($rang_nr==0)
  {
    $rang='<font color=\"#FF0000\">'.$rangnamen[$rang_nr].' ('.$winticks.')';
  }
  else $rang=$rangnamen[$rang_nr];

  if ($varrang!='')$varrang=$varrang.',';
  $varrang=$varrang.'"'.$rang.'"';

  if ($varsector!='')$varsector=$varsector.',';
  $varsector=$varsector.$row["sector"];

  if ($varsystem!='')$varsystem=$varsystem.',';
  $varsystem=$varsystem.$row["system"];

  if ($varname!='')$varname=$varname.',';
  $varname=$varname.'"'.$row["spielername"].'"';

  if ($varcol!='')$varcol=$varcol.',';
  $varcol=$varcol.'"'.number_format($row["eh_counter"], 0,"",".").'"';

  if ($varscore!='')$varscore=$varscore.',';
  $varscore=$varscore.'"'.number_format($row["eh_siege"], 0,"",".").'"';

  $platz_i++;
}
$varrang='var r=new Array('.$varrang.');';
$varsector='var s=new Array('.$varsector.');';
$varsystem='var t=new Array('.$varsystem.');';
$varname='var n=new Array('.$varname.');';
$varcol='var c=new Array('.$varcol.');';
$varscore='var p=new Array('.$varscore.');';
xecho($varrang);
xecho($varsector);
xecho($varsystem);
xecho($varname);
xecho($varcol);
xecho($varscore);
xecho($varalterplatz);
xecho('for (i=0; i<r.length; i++){var j=i+1;document.write("<tr><td class=\"cc\">"+j+"</td><td class=\"cc\">"+r[i]+"</td><td class=\"cc\">"+s[i]+":"+t[i]+"</td><td class=\"cc\"><a href=\"details.php?se="+s[i]+"&sy="+t[i]+"\">"+n[i]+"</a></td><td class=\"cc\">"+c[i]+"</td><td class=\"cr\">"+p[i]+"</td></tr>");}');
xecho('
// -->
</script>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<br>');

//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//spieler - eh_siege
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
$filename = $directory."cache/toplist/top1j.tmp";
$cachefile = fopen ($filename, 'w');
xecho('
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37" align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="25" class="ro"><div class="cellu">'.$wt_lang['platz'].'</div></td>
<td width="60" class="ro"><div class="cellu">'.$wt_lang['rang'].'</div></td>
<td width="50" class="ro"><div class="cellu">'.$wt_lang['system1'].'</div></td>
<td width="200" class="ro"><div class="cellu">'.$wt_lang['name'].'</div></td>
<td width="110" class="ro"><div class="cellu">EH-Counter</div></td>
<td width="100" class="ro"><div class="cellu">EH-Siege</div></td>
<td width="13" class="ro">&nbsp;</td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="7">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="25">
<col width="60">
<col width="50">
<col width="200">
<col width="110">
<col width="100">
<col width="13">
</colgroup>
<script type="text/javascript">
<!--
');

$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.spielername, de_user_data.score, de_user_data.ehscore, de_user_data.sector, de_user_data.`system`, de_user_data.allytag, de_user_data.status, de_user_data.rang, de_user_data.eh_counter, de_user_data.eh_siege FROM de_user_data WHERE sector > 0 AND npc=0 ORDER BY eh_siege DESC LIMIT 100");
$platz_i=1;
$time=date("YmdHis");
$varrang='';
$varsector='';
$varsystem='';
$varname='';
$varcol='';
$varscore='';

while($row = mysqli_fetch_array($result)) //jeder gefundene datensatz wird geprueft
{
  $rang_nr=$row["rang"];
  if($rang_nr==0)
  {
    $rang='<font color=\"#FF0000\">'.$rangnamen[$rang_nr].' ('.$winticks.')';
  }
  else $rang=$rangnamen[$rang_nr];

  if ($varrang!='')$varrang=$varrang.',';
  $varrang=$varrang.'"'.$rang.'"';

  if ($varsector!='')$varsector=$varsector.',';
  $varsector=$varsector.$row["sector"];

  if ($varsystem!='')$varsystem=$varsystem.',';
  $varsystem=$varsystem.$row["system"];

  if ($varname!='')$varname=$varname.',';
  $varname=$varname.'"'.$row["spielername"].'"';

  if ($varcol!='')$varcol=$varcol.',';
  $varcol=$varcol.'"'.number_format($row["eh_counter"], 0,"",".").'"';

  if ($varscore!='')$varscore=$varscore.',';
  $varscore=$varscore.'"'.number_format($row["eh_siege"], 0,"",".").'"';

  $platz_i++;
}
$varrang='var r=new Array('.$varrang.');';
$varsector='var s=new Array('.$varsector.');';
$varsystem='var t=new Array('.$varsystem.');';
$varname='var n=new Array('.$varname.');';
$varcol='var c=new Array('.$varcol.');';
$varscore='var p=new Array('.$varscore.');';
xecho($varrang);
xecho($varsector);
xecho($varsystem);
xecho($varname);
xecho($varcol);
xecho($varscore);
xecho($varalterplatz);
xecho('for (i=0; i<r.length; i++){var j=i+1;document.write("<tr><td class=\"cc\">"+j+"</td><td class=\"cc\">"+r[i]+"</td><td class=\"cc\">"+s[i]+":"+t[i]+"</td><td class=\"cc\"><a href=\"details.php?se="+s[i]+"&sy="+t[i]+"\">"+n[i]+"</a></td><td class=\"cc\">"+c[i]+"</td><td class=\"cr\">"+p[i]+"</td></tr>");}');
xecho('
// -->
</script>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<br>');

//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//spieler - ehpunkte
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
$filename = $directory."cache/toplist/top1k.tmp";
$cachefile = fopen ($filename, 'w');
xecho('
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37" align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="25" class="ro"><div class="cellu">'.$wt_lang['platz'].'</div></td>
<td width="60" class="ro"><div class="cellu">'.$wt_lang['rang'].'</div></td>
<td width="50" class="ro"><div class="cellu">'.$wt_lang['system1'].'</div></td>
<td width="200" class="ro"><div class="cellu">'.$wt_lang['name'].'</div></td>
<td width="110" class="ro"><div class="cellu">Executorpunkte</div></td>
<td width="100" class="ro"><div class="cellu">'.$wt_lang['punkte'].'</div></td>
<td width="13" class="ro">&nbsp;</td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="7">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="25">
<col width="60">
<col width="50">
<col width="200">
<col width="110">
<col width="100">
<col width="13">
</colgroup>
<script type="text/javascript">
<!--
');

$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.spielername, de_user_data.score, de_user_data.pve_score, de_user_data.sector, de_user_data.`system`, de_user_data.allytag, de_user_data.status, de_user_data.rang  FROM de_user_data WHERE sector > 0 AND npc=0 ORDER BY pve_score DESC LIMIT 100");
$platz_i=1;
$time=date("YmdHis");
$varrang='';
$varsector='';
$varsystem='';
$varname='';
$varcol='';
$varscore='';

while($row = mysqli_fetch_array($result)) //jeder gefundene datensatz wird geprueft
{
  $rang_nr=$row["rang"];
  if($rang_nr==0)
  {
    $rang='<font color=\"#FF0000\">'.$rangnamen[$rang_nr].' ('.$winticks.')';
  }
  else $rang=$rangnamen[$rang_nr];

  if ($varrang!='')$varrang=$varrang.',';
  $varrang=$varrang.'"'.$rang.'"';

  if ($varsector!='')$varsector=$varsector.',';
  $varsector=$varsector.$row["sector"];

  if ($varsystem!='')$varsystem=$varsystem.',';
  $varsystem=$varsystem.$row["system"];

  if ($varname!='')$varname=$varname.',';
  $varname=$varname.'"'.$row["spielername"].'"';

  if ($varcol!='')$varcol=$varcol.',';
  $varcol=$varcol.'"'.number_format($row["pve_score"], 0,"",".").'"';

  if ($varscore!='')$varscore=$varscore.',';
  $varscore=$varscore.'"'.number_format($row["score"], 0,"",".").'"';

  $platz_i++;
}
$varrang='var r=new Array('.$varrang.');';
$varsector='var s=new Array('.$varsector.');';
$varsystem='var t=new Array('.$varsystem.');';
$varname='var n=new Array('.$varname.');';
$varcol='var c=new Array('.$varcol.');';
$varscore='var p=new Array('.$varscore.');';
xecho($varrang);
xecho($varsector);
xecho($varsystem);
xecho($varname);
xecho($varcol);
xecho($varscore);
xecho($varalterplatz);
xecho('for (i=0; i<r.length; i++){var j=i+1;document.write("<tr><td class=\"cc\">"+j+"</td><td class=\"cc\">"+r[i]+"</td><td class=\"cc\">"+s[i]+":"+t[i]+"</td><td class=\"cc\"><a href=\"details.php?se="+s[i]+"&sy="+t[i]+"\">"+n[i]+"</a></td><td class=\"cc\">"+c[i]+"</td><td class=\"cr\">"+p[i]+"</td></tr>");}');
xecho('
// -->
</script>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<br>');



////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
//sektoren
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////

//in der de_sector die Plätze der sektoren eintragen
mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector set platz=0, tempcol=0");
$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT sector, sum(score) as score, sum(col) AS col FROM de_user_data WHERE (npc=0 OR npc=2) AND sector > 1 AND sector < 666 GROUP BY sector ORDER BY score DESC");
$platz=1;
while($row = mysqli_fetch_array($db_daten)){
    $sec=$row["sector"];
    $col=$row["col"];
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector set platz='$platz', tempcol='$col' WHERE sec_id='$sec'");
    $platz++;
}
//inzwischen leere sektoren vom platz her auf null setzen
mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector set platz=0 where platz>='$platz'");

$filename = $directory."cache/toplist/top2.tmp";
$cachefile = fopen ($filename, 'w');

xecho('
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37" align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="25" class="ro"><div class="cellu">'.$wt_lang['platz'].'</div></td>
<td width="50" class="ro"><div class="cellu">'.$wt_lang['sektor'].'</div></td>
<td width="305" class="ro"><div class="cellu">'.$wt_lang['name'].'</div></td>
<td width="70" class="ro"><div class="cellu">'.$wt_lang['kollektoren'].'</div></td>
<td width="100" class="ro"><div class="cellu">'.$wt_lang['punkte'].'</div></td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="5">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="25">
<col width="50">
<col width="325">
<col width="40">
<col width="100">
</colgroup>');

$db_daten=mysqli_query($GLOBALS['dbi'], "SELECT sec_id, name, platz, platz_last_day FROM de_sector WHERE platz>0 order by platz  LIMIT 100");
while($row = mysqli_fetch_array($db_daten)){
  $get_score=mysqli_query($GLOBALS['dbi'],"SELECT SUM(score) AS score, SUM(col) AS col FROM de_user_data WHERE sector='$row[sec_id]'");
  $row_score=mysqli_fetch_array($get_score);

  if($row["platz_last_day"]==$row["platz"])
$grafik='<img border="0" src="gp/g/tl_constant.gif" width="11" height="11" title="unver&auml;ndert" alt="unver&auml;ndert">';
  if($row["platz_last_day"]>$row["platz"])
$grafik='<img border="0" src="gp/g/tl_up.gif" width="11" height="11" title="+'.($row['platz_last_day']-$row['platz']).'" alt="+'.($row['platz_last_day']-$row['platz']).'" >';
  if($row["platz_last_day"]<$row["platz"])
$grafik='<img border="0" src="gp/g/tl_down.gif" width="11" height="11" title="-'.($row['platz']-$row['platz_last_day']).'" alt="-'.($row['platz']-$row['platz_last_day']).'">';

  if ($row['name']=='') $row['name']='&nbsp';
  xecho ("<tr>");
  xecho ('<td class="cc">'.$row['platz'].'</td>');
  xecho ('<td class="cc">'.$row['sec_id'].'</td>');
  xecho ('<td class="cc">'.$row['name'].'</td>');
  xecho ('<td class="cr">'.number_format($row_score["col"], 0,"",".").'</td>');
  xecho ('<td class="cr">'.number_format($row_score["score"], 0,"",".").'</td>');
  xecho ('<td class="cc">'.$grafik.'</td>');
  xecho ("</tr>");
}
xecho('
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru" colspan="5">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<br>');

//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//allianz - punkte
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
$filename = $directory."cache/toplist/top3.tmp";
$cachefile = fopen ($filename, 'w');

xecho('
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37" align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="25" class="ro"><div class="cellu" title="'.$wt_lang['platz'].'">P</div></td>
<td width="75" class="ro"><div class="cellu">'.$wt_lang['allianz'].'</div></td>
<td width="25" class="ro"><div class="cellu" title="'.$wt_lang['mitglieder'].'">M</div></td>
<td width="55" class="ro"><div class="cellu" title="Rundensiegartefakte">RSA</div></td>
<td width="150" class="ro"><div class="cellu">'.$wt_lang['punkte'].'</div></td>
<td width="100" class="ro"><div class="cellu">'.$wt_lang['schnitt'].'</div></td>
<td width="95" class="ro"><div class="cellu">'.$wt_lang['kollies'].'</div></td>
<td width="50" class="ro"><div class="cellu">'.$wt_lang['schnitt'].'</div></td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="8">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="25">
<col width="75">
<col width="25">
<col width="55">
<col width="150">
<col width="100">
<col width="95">
<col width="50">
</colgroup>');

$db_daten=mysqli_query($GLOBALS['dbi'], "SELECT allytag, sum(score) as score, sum(col) as col, count(allytag) as am FROM de_user_data where allytag<>'' AND status=1 group by allytag order by score DESC LIMIT 100");
$platz=1;
while($row = mysqli_fetch_array($db_daten)){
	//allianzdaten nachladen
  $wt_a_tag = $row["allytag"];
  $sql="SELECT * FROM de_allys WHERE allytag='$wt_a_tag'";
  $wt_a_result=mysqli_fetch_array(mysqli_query($GLOBALS['dbi'],$sql));
  //print_r($wt_a_result);
	$wt_a_id=$wt_a_result["id"];
	$siegartefakte=$wt_a_result["questpoints"];
	$target_url = "ally_detail.php?allyid=$wt_a_id";
	$col_erobert = $wt_a_result["colstolen"];
	$col_verloren = $wt_a_result["collost"];
	//Anderung Ende

  $ally_tag=$row['allytag'];
  $ally_tag=str_replace("²", "&sup2;", $ally_tag);
  $ally_tag=str_replace("Â²", "&sup2;", $ally_tag);

  //bei den allianzen den maxmembercount, maxcolcount und maxscorecount aktualisieren
  $newmaxmembercount=$row['am'];
  $newmaxcolcount=$row['col'];
  $newmaxscorecount=$row['score'];
  mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET maxmembercount='$newmaxmembercount' WHERE id='$wt_a_id' AND maxmembercount<'$newmaxmembercount'");
  mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET maxcolcount='$newmaxcolcount' WHERE id='$wt_a_id' AND maxcolcount<'$newmaxcolcount'");
  mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET maxscorecount='$newmaxscorecount' WHERE id='$wt_a_id' AND maxscorecount<'$newmaxscorecount'");
  
  //bei den aufgaben ggf. die erreiche memberzahl setzen	
  //mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET questreach='$newmaxmembercount' WHERE id='$wt_a_id' AND questtyp=2");
  mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET questreach='$col_erobert' WHERE id='$wt_a_id' AND questtyp=1");
  mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET questreach='$newmaxscorecount' WHERE id='$wt_a_id' AND questtyp=2");

  //die Punkte-Prozentzahl der Allianz
  $p_allianz_score= $server_gesamt_score!=0 ? $row['score']*100/$server_gesamt_score : 0;

  //die Kollektoren-Prozentzahl der Allian
  $p_allianz_col= $server_gesamt_col!=0 ? $row['col']*100/$server_gesamt_col : 0;
	
  $schnitt=$row["score"]/$row["am"];
  $schnitt=round($schnitt);
  $schnitt2=$row["col"]/$row["am"];
  $schnitt2=round($schnitt2);
  $tooltip='Eroberte Kollektoren: '.number_format($col_erobert, 0,"",".").' 
  <br>Verlorene Kollektoren: '.number_format($col_verloren, 0,"",".");
  xecho ('<tr title="'.$tooltip.'">');
  xecho ('<td class="cc">'.$platz.'</td>');
  xecho ('<td class="cc" nowrap><a href="'.$target_url.'">'.$ally_tag.'</a></td>');
  xecho ('<td class="cc">'.$row["am"].'</td>');
  xecho ('<td class="cr">'.number_format($siegartefakte, 0,"",".").'</td>');
  xecho ('<td class="cr">'.number_format($row["score"], 0,"",".").'&nbsp;('.number_format($p_allianz_score, 2,",",".").'%)</td>');
  xecho ('<td class="cr">'.number_format($schnitt, 0,"",".").'</td>');
  xecho ('<td class="cr">'.number_format($row["col"], 0,"",".").'&nbsp;('.number_format($p_allianz_col, 2,",",".").'%)</td>');
  xecho ('<td class="cr">'.number_format($schnitt2, 0,"",".").'</td>');
  xecho ("</tr>");
  $platz++;
}

xecho('
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru" colspan="8">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<br>');

//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//allianz - siegartefakte
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
$filename = $directory."cache/toplist/top3a.tmp";
$cachefile = fopen ($filename, 'w');

xecho('
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37" align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="100" class="ro"><div class="cellu">'.$wt_lang['platz'].'</div></td>
<td width="250" class="ro"><div class="cellu">'.$wt_lang['allianz'].'</div></td>
<td width="225" class="ro"><div class="cellu">Rundensiegartefakte</div></td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="3">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="100">
<col width="250">
<col width="225">
</colgroup>
');

$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT id, allytag, questpoints FROM de_allys ORDER BY questpoints DESC, id ASC LIMIT 100");

$platz=1;
while($row = mysqli_fetch_array($db_daten))
{
  $target_url = 'ally_detail.php?allyid='.$row['id'];
  xecho ("<tr>");
  xecho ('<td class="cc">'.$platz.'</td>');
  xecho ('<td class="cc" nowrap><a href="'.$target_url.'">'.$row["allytag"].'</a></td>');
  xecho ('<td class="cr">'.number_format($row['questpoints'], 0,"",".").'</td>');
  xecho ("</tr>");
  $platz++;
}

xecho('
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">

<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru" colspan="3">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>

</tr>
</table>
<br>');

//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//allianz - bündnisse
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
$filename = $directory."cache/toplist/top3b.tmp";
$cachefile = fopen ($filename, 'w');

xecho('
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37" align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="60" class="ro"><div class="cellu">'.$wt_lang['platz'].'</div></td>
<td width="180" class="ro"><div class="cellu">'.$wt_lang['buendnis'].'</div></td>
<td width="205" class="ro"><div class="cellu">'.$wt_lang['punkte'].'</div></td>
<td width="110" class="ro"><div class="cellu">'.$wt_lang['kollektoren'].'</div></td>
<td width="25" class="ro"><div class="cellu" title="'.$wt_lang['mitglieder'].'">M</div></td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="5">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="60">
<col width="180">
<col width="205">
<col width="110">
<col width="25">
</colgroup>
');

//b�ndnisse laden
$platz=1;
$allydata=array();
$db_datenx=mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_ally_partner");
while($rowx = mysqli_fetch_array($db_datenx)){
  $allyid1=$rowx['ally_id_1'];
  $allyid2=$rowx['ally_id_2'];
  //allytags laden
  $db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT allytag FROM de_allys WHERE id='$allyid1'");
  $row = mysqli_fetch_array($db_daten);
  $allytag1=$row['allytag'];

  //ggf. doppelte partnerschaften l�schen
  mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_ally_partner WHERE ally_id_2='$allyid1'");  
  
  $db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT allytag FROM de_allys WHERE id='$allyid2'");
  $row = mysqli_fetch_array($db_daten);
  $allytag2=$row['allytag'];  
  


  $db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT SUM(score) AS score, SUM(col) AS col, COUNT(user_id) AS am FROM de_user_data 
  WHERE (allytag='$allytag1' OR allytag='$allytag2') AND status=1");
  
  $row = mysqli_fetch_array($db_daten);
  
  $allydata[$platz-1]['allytag']=$allytag1.' & '.$allytag2;
  $allydata[$platz-1]['score']=$row['score'];
  $allydata[$platz-1]['col']=$row['col'];
  $allydata[$platz-1]['am']=$row['am'];
  
  $allydata[$platz-1]['p_allianz_score']=$row['score']*100/$server_gesamt_score;
  $allydata[$platz-1]['p_allianz_col']=$row['col']*100/$server_gesamt_col;
  
  
  $platz++;
}

  //daten sortieren
  $score=array();
  foreach ($allydata as $key => $row) {
      $score[$key]    = $row['score'];
  }

  array_multisort($score, SORT_DESC, $allydata);
  
  //daten ausgeben
  $platz=1;
  for($i=0;$i<count($allydata);$i++)
  {
    xecho ("<tr>");
    xecho ('<td class="cc">'.$platz.'</td>');
    xecho ('<td class="cc" nowrap>'.$allydata[$i]['allytag'].'</td>');
    xecho ('<td class="cr">'.number_format($allydata[$i]['score'], 0,"",".").'&nbsp;('.number_format($allydata[$i]['p_allianz_score'], 2,",",".").'%)</td>');
    xecho ('<td class="cr">'.number_format($allydata[$i]['col'], 0,"",".").'&nbsp;('.number_format($allydata[$i]['p_allianz_col'], 2,",",".").'%)</td>');
    xecho ('<td class="cr">'.number_format($allydata[$i]['am'], 0,"",".").'</td>');
    xecho ("</tr>");
    $platz++;
  }


xecho('
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">

<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru" colspan="5">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>

</tr>
</table>
<br>');

//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//allianz - erhabene gestellt
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
if($sv_ewige_runde==1){
	$filename = $directory."cache/toplist/top3c.tmp";
	$cachefile = fopen ($filename, 'w');

	xecho('
	<table border="0" cellpadding="0" cellspacing="0">
	<tr height="37" align="center">
	<td width="13" height="37" class="rol">&nbsp;</td>
	<td width="100" class="ro"><div class="cellu">'.$wt_lang['platz'].'</div></td>
	<td width="250" class="ro"><div class="cellu">'.$wt_lang['allianz'].'</div></td>
	<td width="225" class="ro"><div class="cellu">Erhabene gestellt</div></td>
	<td width="13" class="ror">&nbsp;</td>
	</tr>
	<tr>
	<td width="13" class="rl">&nbsp;</td>
	<td colspan="3">
	<table border="0" cellpadding="0" cellspacing="1" width="100%">
	<colgroup>
	<col width="100">
	<col width="250">
	<col width="225">
	</colgroup>
	');

	$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT id, allytag, eh_gestellt_anz FROM de_allys ORDER BY eh_gestellt_anz DESC, id ASC LIMIT 100");

	$platz=1;
	while($row = mysqli_fetch_array($db_daten))
	{
	  $target_url = 'ally_detail.php?allyid='.$row['id'];
	  xecho ("<tr>");
	  xecho ('<td class="cc">'.$platz.'</td>');
	  xecho ('<td class="cc" nowrap><a href="'.$target_url.'">'.$row["allytag"].'</a></td>');
	  xecho ('<td class="cr">'.number_format($row['eh_gestellt_anz'], 0,"",".").'</td>');
	  xecho ("</tr>");
	  $platz++;
	}

	xecho('
	</table>
	</td>
	<td width="13" class="rr">&nbsp;</td>
	</tr>
	<tr height="20">

	<td height="20" class="rul" width="13">&nbsp;</td>
	<td class="ru" colspan="3">&nbsp;</td>
	<td class="rur" width="13">&nbsp;</td>

	</tr>
	</table>
	<br>');
}

//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
// handel neu
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////


$filename = $directory."cache/toplist/top4a.tmp";
$cachefile = fopen ($filename, 'w');

xecho('<table border="0" cellpadding="0" cellspacing="0">
<tr height="37" align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="100" class="ro"><div class="cellu">'.$wt_lang['platz'].'</div></td>
<td width="100" class="ro"><div class="cellu">'.$wt_lang['name'].'</div></td>
<td width="160" class="ro"><div class="cellu">Handelsaktionen</div></td>
<td width="160" class="ro"><div class="cellu">'.$wt_lang['handelspunkte'].'</div></td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="4">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="100">
<col width="100">
<col width="160">
<col width="160">
</colgroup>');
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT spielername, sector, `system`, tradesystemscore, tradesystemtrades FROM de_user_data ORDER BY tradesystemscore DESC LIMIT 100 ");
$platz=1;
while($row = mysqli_fetch_array($result)) //jeder gefundene datensatz wird geprueft
{

  xecho ('<tr>');
  xecho ('<td class="cc">'.$platz.'</td>');
  xecho ('<td class="cc"><a href="details.php?se='.$row["sector"].'&sy='.$row["system"].'&a=s">'.$row["spielername"].'</td>');
  xecho ('<td class="cc">'.number_format($row["tradesystemtrades"], 0,"",".").'</td>');
  xecho ('<td class="cr">'.number_format($row["tradesystemscore"], 0,"",".").'</td>');
  xecho ('</tr>');
  $platz++;
}

xecho('</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<br>');


//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//handel
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
/*
$filename = $directory."cache/toplist/top4.tmp";
$cachefile = fopen ($filename, 'w');

xecho('<table border="0" cellpadding="0" cellspacing="0">
<tr height="37" align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="100" class="ro"><div class="cellu">'.$wt_lang[platz].'</div></td>
<td width="100" class="ro"><div class="cellu">'.$wt_lang[name].'</div></td>
<td width="100" class="ro"><div class="cellu">'.$wt_lang[verkaeufe].'</div></td>
<td width="100" class="ro"><div class="cellu">'.$wt_lang[handelspunkte].'</div></td>
<td width="100" class="ro"><div class="cellu">'.$wt_lang[durchschnitt].'</div></td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="5">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="100">
<col width="100">
<col width="100">
<col width="100">
<col width="100">
</colgroup>');
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT spielername, sells, tradescore, sector, `system` FROM de_user_data ORDER BY tradescore DESC LIMIT 100 ");
$platz=1;
//$tschnitt=$row["tradescore"]/$row["sells"];
//$tschnitt=round($schnitt);
//$time=strftime("%Y%m%d%H%M%S");
while($row = mysqli_fetch_array($result)) //jeder gefundene datensatz wird geprueft
{

  if ($row["sells"]>0)$tschnitt=$row["tradescore"]/$row["sells"];else $tschnitt=0;
  xecho ('<tr>');
  xecho ('<td class="cc">'.$platz.'</td>');
  xecho ('<td class="cc"><a href="details.php?se='.$row["sector"].'&sy='.$row["system"].'&a=s">'.$row["spielername"].'</td>');
  xecho ('<td class="cc">'.number_format($row["sells"], 0,"",".").'</td>');
  xecho ('<td class="cr">'.number_format($row["tradescore"], 0,"",".").'</td>');
  xecho ('<td class="cc">'.number_format($tschnitt, 0,"",".").'</td>');
  xecho ('</tr>');
  $platz++;
}

xecho('</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<br>');
*/
//efta - punkte
/*
$filename = $directory."cache/toplist/top5a.tmp";
$cachefile = fopen ($filename, 'w');

xecho('<table border="0" cellpadding="0" cellspacing="0">
<tr height="37" align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="50" class="ro">'.$wt_lang[platz].'</td>
<td width="50" class="ro">'.$wt_lang[level].'</td>
<td width="200" class="ro">'.$wt_lang[name].'</td>
<td width="75" class="ro">'.$wt_lang[ruhm].'</td>
<td width="100" class="ro">'.$wt_lang[questpunkte].'</td>
<td width="75" class="ro">'.$wt_lang[punkte].'</td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="6">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="50">
<col width="50">
<col width="200">
<col width="75">
<col width="100">
<col width="75">
</colgroup>
<script type="text/javascript">
<!--
');
$varsector='';
$varsystem='';
$varname='';
$varlevel='';
$varscore='';
$varquestpunkte='';
$varfame='';

$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.spielername, de_cyborg_data.level, de_cyborg_data.exp, de_cyborg_data.questpoints, de_cyborg_data.fame, de_user_data.sector, de_user_data.`system` FROM de_user_data left join de_cyborg_data on(de_user_data.user_id = de_cyborg_data.user_id) ORDER BY exp DESC LIMIT 100");
while($row = mysqli_fetch_array($result)) //jeder gefundene datensatz wird geprueft
{
  if ($varname!='')$varname=$varname.',';
  $varname=$varname.'"'.$row["spielername"].'"';

  if ($varlevel!='')$varlevel=$varlevel.',';
  if ($row["level"]!='')
  $varlevel=$varlevel.$row["level"];
  else $varlevel=$varlevel.'0';

  if ($varscore!='')$varscore=$varscore.',';
  $varscore=$varscore.'"'.number_format($row["exp"], 0,"",".").'"';

  if ($varquestpunkte!='')$varquestpunkte=$varquestpunkte.',';
  $varquestpunkte=$varquestpunkte.'"'.number_format($row["questpoints"], 0,"",".").'"';

  if ($varfame!='')$varfame=$varfame.',';
  $varfame=$varfame.'"'.number_format($row["fame"], 0,"",".").'"';

  if ($varsector!='')$varsector=$varsector.',';
  $varsector=$varsector.$row["sector"];

  if ($varsystem!='')$varsystem=$varsystem.',';
  $varsystem=$varsystem.$row["system"];
}
$varname='var n=new Array('.$varname.');';
$varlevel='var l=new Array('.$varlevel.');';
$varscore='var s=new Array('.$varscore.');';
$varquestpunkte='var q=new Array('.$varquestpunkte.');';
$varfame='var f=new Array('.$varfame.');';

$varsector='var a=new Array('.$varsector.');';
$varsystem='var b=new Array('.$varsystem.');';
xecho($varsector);
xecho($varsystem);
xecho($varname);
xecho($varlevel);
xecho($varscore);
xecho($varquestpunkte);
xecho($varfame);
xecho('for (i=0; i<n.length; i++){var j=i+1;document.write("<tr><td class=\"cc\">"+j+"</td><td class=\"cc\">"+l[i]+"</td><td class=\"cc\"><a href=\"details.php?se="+a[i]+"&sy="+b[i]+"\">"+n[i]+"</a></td><td class=\"cr\">"+f[i]+"</td><td class=\"cr\">"+q[i]+"</td><td class=\"cr\">"+s[i]+"</td></tr>");}');
xecho('
// -->
</script>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<br>');

//efta - questpunkte
$filename = $directory."cache/toplist/top5b.tmp";
$cachefile = fopen ($filename, 'w');

xecho('<table border="0" cellpadding="0" cellspacing="0">
<tr height="37" align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="50" class="ro">'.$wt_lang[platz].'</td>
<td width="50" class="ro">'.$wt_lang[level].'</td>
<td width="200" class="ro">'.$wt_lang[name].'</td>
<td width="75" class="ro">'.$wt_lang[ruhm].'</td>
<td width="100" class="ro">'.$wt_lang[questpunkte].'</td>
<td width="75" class="ro">'.$wt_lang[punkte].'</td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="6">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="50">
<col width="50">
<col width="200">
<col width="75">
<col width="100">
<col width="75">
</colgroup>
<script type="text/javascript">
<!--
');
$varsector='';
$varsystem='';
$varname='';
$varlevel='';
$varscore='';
$varquestpunkte='';
$varfame='';

$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.spielername, de_cyborg_data.level, de_cyborg_data.exp, de_cyborg_data.questpoints, de_cyborg_data.fame, de_user_data.sector, de_user_data.`system` FROM de_user_data left join de_cyborg_data on(de_user_data.user_id = de_cyborg_data.user_id) ORDER BY questpoints DESC LIMIT 100");
while($row = mysqli_fetch_array($result)) //jeder gefundene datensatz wird geprueft
{
  if ($varname!='')$varname=$varname.',';
  $varname=$varname.'"'.$row["spielername"].'"';

  if ($varlevel!='')$varlevel=$varlevel.',';
  if ($row["level"]!='')
  $varlevel=$varlevel.$row["level"];
  else $varlevel=$varlevel.'0';

  if ($varscore!='')$varscore=$varscore.',';
  $varscore=$varscore.'"'.number_format($row["exp"], 0,"",".").'"';

  if ($varquestpunkte!='')$varquestpunkte=$varquestpunkte.',';
  $varquestpunkte=$varquestpunkte.'"'.number_format($row["questpoints"], 0,"",".").'"';

  if ($varfame!='')$varfame=$varfame.',';
  $varfame=$varfame.'"'.number_format($row["fame"], 0,"",".").'"';

  if ($varsector!='')$varsector=$varsector.',';
  $varsector=$varsector.$row["sector"];

  if ($varsystem!='')$varsystem=$varsystem.',';
  $varsystem=$varsystem.$row["system"];
}
$varname='var n=new Array('.$varname.');';
$varlevel='var l=new Array('.$varlevel.');';
$varscore='var s=new Array('.$varscore.');';
$varquestpunkte='var q=new Array('.$varquestpunkte.');';
$varfame='var f=new Array('.$varfame.');';

$varsector='var a=new Array('.$varsector.');';
$varsystem='var b=new Array('.$varsystem.');';
xecho($varsector);
xecho($varsystem);
xecho($varname);
xecho($varlevel);
xecho($varscore);
xecho($varquestpunkte);
xecho($varfame);
xecho('for (i=0; i<n.length; i++){var j=i+1;document.write("<tr><td class=\"cc\">"+j+"</td><td class=\"cc\">"+l[i]+"</td><td class=\"cc\"><a href=\"details.php?se="+a[i]+"&sy="+b[i]+"\">"+n[i]+"</a></td><td class=\"cr\">"+f[i]+"</td><td class=\"cr\">"+q[i]+"</td><td class=\"cr\">"+s[i]+"</td></tr>");}');
xecho('
// -->
</script>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<br>');

//efta - ruhm
$filename = $directory."cache/toplist/top5c.tmp";
$cachefile = fopen ($filename, 'w');

xecho('<table border="0" cellpadding="0" cellspacing="0">
<tr height="37" align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="50" class="ro">'.$wt_lang[platz].'</td>
<td width="50" class="ro">'.$wt_lang[level].'</td>
<td width="200" class="ro">'.$wt_lang[name].'</td>
<td width="75" class="ro">'.$wt_lang[ruhm].'</td>
<td width="100" class="ro">'.$wt_lang[questpunkte].'</td>
<td width="75" class="ro">'.$wt_lang[punkte].'</td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="6">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="50">
<col width="50">
<col width="200">
<col width="75">
<col width="100">
<col width="75">
</colgroup>
<script type="text/javascript">
<!--
');

$varsector='';
$varsystem='';
$varname='';
$varlevel='';
$varscore='';
$varquestpunkte='';
$varfame='';

$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.spielername, de_cyborg_data.level, de_cyborg_data.exp, de_cyborg_data.questpoints, de_cyborg_data.fame, de_user_data.sector, de_user_data.`system` FROM de_user_data left join de_cyborg_data on(de_user_data.user_id = de_cyborg_data.user_id) ORDER BY fame DESC LIMIT 100");
while($row = mysqli_fetch_array($result)) //jeder gefundene datensatz wird geprueft
{
  if ($varname!='')$varname=$varname.',';
  $varname=$varname.'"'.$row["spielername"].'"';

  if ($varlevel!='')$varlevel=$varlevel.',';
  if ($row["level"]!='')
  $varlevel=$varlevel.$row["level"];
  else $varlevel=$varlevel.'0';

  if ($varscore!='')$varscore=$varscore.',';
  $varscore=$varscore.'"'.number_format($row["exp"], 0,"",".").'"';

  if ($varquestpunkte!='')$varquestpunkte=$varquestpunkte.',';
  $varquestpunkte=$varquestpunkte.'"'.number_format($row["questpoints"], 0,"",".").'"';

  if ($varfame!='')$varfame=$varfame.',';
  $varfame=$varfame.'"'.number_format($row["fame"], 0,"",".").'"';

  if ($varsector!='')$varsector=$varsector.',';
  $varsector=$varsector.$row["sector"];

  if ($varsystem!='')$varsystem=$varsystem.',';
  $varsystem=$varsystem.$row["system"];
}
$varname='var n=new Array('.$varname.');';
$varlevel='var l=new Array('.$varlevel.');';
$varscore='var s=new Array('.$varscore.');';
$varquestpunkte='var q=new Array('.$varquestpunkte.');';
$varfame='var f=new Array('.$varfame.');';

$varsector='var a=new Array('.$varsector.');';
$varsystem='var b=new Array('.$varsystem.');';
xecho($varsector);
xecho($varsystem);
xecho($varname);
xecho($varlevel);
xecho($varscore);
xecho($varquestpunkte);
xecho($varfame);
xecho('for (i=0; i<n.length; i++){var j=i+1;document.write("<tr><td class=\"cc\">"+j+"</td><td class=\"cc\">"+l[i]+"</td><td class=\"cc\"><a href=\"details.php?se="+a[i]+"&sy="+b[i]+"\">"+n[i]+"</a></td><td class=\"cr\">"+f[i]+"</td><td class=\"cr\">"+q[i]+"</td><td class=\"cr\">"+s[i]+"</td></tr>");}');
xecho('
// -->
</script>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<br>');
*/
?>
