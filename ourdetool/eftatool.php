<?php
include "../inccon.php";
include "../inc/sv.inc.php";
include "../eftadata/lib/efta_dbconnect.php"; 
?>

<html>
<head>
<title>EFTA Tool</title>
<?php include "cssinclude.php";?>
</head>
<body>
<div align="center">
<h1>EFTA Tools</h1>
<?
include "det_userdata.inc.php";

$uid=(int)$uid;
$cg=(int)$cg;

if($aktion>0)
{
  switch($aktion)  
  {	
    case 1://msg
	  //nachricht in der db eintragen
	  $time=time();
	  $text='/me '.$_REQUEST["message"];
      mysql_query("INSERT INTO de_cyborg_chat_msg (spielername, message, timestamp) VALUES ('', '$text', '$time')",$eftadb);
    break;
    case 2://der herold
	  //nachricht in der db eintragen
	  $time=time();
	  $text=$_REQUEST["message"];
      mysql_query("INSERT INTO de_cyborg_chat_msg (spielername, message, timestamp) VALUES ('^Der Herold^', '$text', '$time')",$eftadb);
    break;
  }
}

//msg
echo '<form action="eftatool.php" method="post">';
//aktion im hiddenfeld
echo '<input type="hidden" name="aktion" value="1">';

echo '/msg: <input type="text" name="message" size="50" value="">&nbsp;&nbsp;&nbsp;&nbsp;';
echo '<input type="Submit" name="b1" value="eintragen">';
echo '</form><br><br><br>';

//der herold
echo '<form action="eftatool.php" method="post">';
//aktion im hiddenfeld
echo '<input type="hidden" name="aktion" value="2">';

echo 'Der Herold: <input type="text" name="message" size="50" value="">&nbsp;&nbsp;&nbsp;&nbsp;';
echo '<input type="Submit" name="b1" value="eintragen">';
echo '</form>';
?>
</div>
</body>
</html>

