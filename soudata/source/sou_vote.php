<?php
//der user stimmt ab
if($_REQUEST['votebutton'])
{
  $vote=intval($_REQUEST['vote']);
  if($vote>0)mysql_query("INSERT INTO sou_vote_stimmen (user_id, vote_id, votefor) VALUES ('".$_SESSION["ums_owner_id"]."','$voteid', '$vote')",$soudb);
  header("Location: sou_index.php");
}

echo '<body>';
include "cssinclude.php";

echo '<div align="center">';

echo '<br>';

rahmen0_oben();


include 'inc/lang/'.$sv_server_lang.'_vote.lang.php';

///////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////
// die umfrage optisch darstellen
///////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////
echo '<br>';
echo '<form action="sou_index.php" method="POST">';
rahmen1_oben('<div align="center"><b>Umfrage</b></div>');

echo '<table border="0" cellpadding="0" cellspacing="0" width="100%">';
echo '<tr><td>Frage: '.$rowumfrage['frage'].'</td></tr>';
echo '<tr><td>Hinweis: '.$rowumfrage['hinweis'].'</td></tr>';

$antworten = explode("|",$rowumfrage['antworten']);
$i=0;
while($i<count($antworten))
{
  echo '<tr><td align="left"><input type="Radio" name="vote" value="'. ($i+1).'">&nbsp;&nbsp;&nbsp;'.$antworten[$i].'</td></tr>';
  $i++;
}
echo '<tr><td align="center"><input type="submit" name="votebutton" value="Stimme abgeben"></td></tr>';
echo '</table>';

rahmen1_unten();
echo '</form>';
echo '<br>';
rahmen0_unten();

echo '</div>';//div align center

die('</body></html>');
?>