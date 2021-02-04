<?php
$thisissou=1;
$soucss=1;
$eftachatbotdefensedisable=1;
include "soudata/lib/sou_functions.inc.php";
include "inc/header.inc.php";
include "soudata/lib/sou_dbconnect.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>EA - <?php echo $sv_server_tag; ?></title>
</head>
<?php
///////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////
// überprüfen ob es eine aktive umfrage gibt, auf die man noch nicht geantwortet hat
///////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////

if($_SESSION["ums_owner_id"]>0){
  //aktive umfrage
  $db_daten=mysql_query("SELECT * FROM sou_vote_umfragen WHERE status=1",$soudb);
  $num = mysql_num_rows($db_daten);
  if($num>0)  {
    $rowumfrage = mysql_fetch_array($db_daten);
    $voteid=$rowumfrage['id'];
    //f�r die umfrage bereits eine stimme abgegeben?
    $db_daten=mysql_query("SELECT * FROM sou_vote_stimmen WHERE vote_id='$voteid' AND user_id=".$_SESSION["ums_owner_id"].";",$soudb);
    $num = mysql_num_rows($db_daten);
    if($num==0)
    {
      include 'soudata/source/sou_vote.php';
    }
  }
}

/*
echo '
<frameset framespacing="0" border="0" frameborder="0" rows="*,140">
  <frame name="soumain" src="sou_main.php" target="e" marginwidth=0 marginheight=0>  
  <frameset ID="adchat" framespacing="0" border="0" frameborder="0" cols="468,*">
    <frame name="aframe" src="sou_topban.php" marginwidth=0 marginheight=0>  
    <frame name="souchat" src="sou_chat.php?frame=1" marginwidth=0 marginheight=0 noresize>
  </frameset>
</frameset>';
*/

echo '
<frameset framespacing="0" border="0" frameborder="0" rows="*,125">
  <frame name="soumain" src="sou_main.php" target="e" marginwidth=0 marginheight=0>  
  <frame name="souchat" src="sou_chat.php" marginwidth=0 marginheight=0 noresize>
</frameset>';

?>
<noframes>
<body>
<p>Diese Seite verwendet Frames. Frames werden von Ihrem Browser aber nicht unterst&uuml;tzt.</p>
</body>
</noframes>
</html>
