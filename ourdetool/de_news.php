<?php
include "../inccon.php";
?>
<html>
<head>
<title>Admin - DE - News</title>
<?php include "cssinclude.php";?>
</head>
<body text="#000000" bgcolor="#FFFFFF" link="#FF0000" alink="#FF0000" vlink="#FF0000">
<center>
<br><br><br><br><br>
<?
include "det_userdata.inc.php";

if($absenden){
	$time=strftime("%Y%m%d%H%M%S");
	$betreff=utf8_decode($betreff);
	$nachricht=utf8_decode($nachricht);
	mysql_query("INSERT INTO de_news_overview (typ, betreff, nachricht, time) VALUES (1,'$betreff','$nachricht','$time')");
	echo '<br><br><h1>Nachricht erfolgreich eingetragen</h1><br><br>';
}

if($edit){
	$betreff=utf8_decode($betreff);
	$nachricht=utf8_decode($nachricht);

	mysql_query("Update de_news_overview set betreff='$betreff', nachricht='$nachricht', time=time where id='$id'");
	echo '<br><br><h1>Nachricht erfolgreich editiert</h1><br><br>';
}

if($action=="del")
{
mysql_query("DELETE FROM de_news_overview WHERE id='$id'");
echo '<br><br><h1>Nachricht erfolgreich gel&ouml;scht</h1><br><br>';
}

if($action=="aendern")
{

  $sel_news_edit = mysql_query("SELECT * FROM de_news_overview where id='$id'");

  $row=mysql_fetch_array($sel_news_edit);

echo '<form action="de_news.php?id='.$id.'" method="post" target="Hauptframe">
  <table border="1">
  <tr>
    <td colspan="2" align="center"><b>News bearbeiten</b></td>
  </tr>
  <tr>
    <td>Betreff:</td>
    <td><input type="Text" name="betreff" maxlength="50" size="40" value="'.$row[betreff].'"></td>
  </tr>
  <tr>
    <td valign="top">Nachricht:</td>
    <td><textarea name="nachricht" cols="50" rows="10">'.$row[nachricht].'</textarea></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="Submit" name="edit" value="Nachricht speichern">&nbsp;&nbsp;&nbsp;<input type="reset" value="Felder leeren"></td>
  </tr>
</table>
<input type="hidden" name="time" value="'.$row[time].'">
</form>';

}

if($action!="aendern")
{
?>


<form action="de_news.php" method="post" target="Hauptframe">
<table border="1">
  <tr>
    <td colspan="2" align="center"><b>News eintragen</b></td>
  </tr>
  <tr>
    <td>Betreff:</td>
    <td><input type="Text" name="betreff" maxlength="50" size="40"></td>
  </tr>
  <tr>
    <td valign="top">Nachricht:</td>
    <td><textarea name="nachricht" cols="100" rows="10"></textarea></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="Submit" name="absenden" value="Nachricht eintragen">&nbsp;&nbsp;&nbsp;<input type="reset" value="Felder leeren"></td>
  </tr>
</form>
</table>
<br><br><br><br>
<table border="0" width="750">
  <tr><td align="center"><h1>N a c h r i c h t e n</h1></td></tr>
  <?

  $sel_news=mysql_query("SELECT * FROM de_news_overview where typ=1 order by id desc");

  while($row=mysql_fetch_array($sel_news)){

  $t=$row[time];
  $time=$t[8].$t[9].'.'.$t[5].$t[6].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[11].$t[12].':'.$t[14].$t[15].':'.$t[17].$t[18];


  $nachricht = utf8_encode(nl2br($row[nachricht]));
  echo '<tr><td>
  <fieldset><table border="0" width="100%">
  <tr><td><b>Betreff:</b> '.utf8_encode($row[betreff]).'</td><td align="center" width="170"><b>Zeit:</b> '.$time.'</td><td align="center" width="90">Klicks: '.$row[klicks].'</td><td align="center" width="100"><b><a href="de_news.php?id='.$row[id].'&action=del" onclick="return confirm(\'M&ouml;chtest du die Nachricht wirklich l&ouml;schen?\')">l&ouml;schen</a>&nbsp;&nbsp;&nbsp;<a href="de_news.php?id='.$row[id].'&action=aendern">bearbeiten</a></b></td></tr>
  <tr><td colspan="4"><hr>'.$nachricht.'</td></tr></table></fieldset><br>';
  }


  ?>

</table>
<?
}
?>
</center>
</body>
</html>