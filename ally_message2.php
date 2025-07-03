<?php
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_ally.messagezwei.lang.php';
include_once 'functions.php';

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$allytag=$row["allytag"];

/*$db_daten=mysql_query("SELECT nic FROM de_login WHERE user_id='$ums_user_id'");
$row = mysql_fetch_array($db_daten);
$nic=$row["nic"];*/

?>
<!DOCTYPE HTML>
<html>
<head>
<title><?=$allymessagezwei_lang[title]?></title>
<?php include "cssinclude.php"; ?>
</head>
<body>

<font face="tahoma" style="font-size:8pt;">

<?
include "resline.php";

if($text) {

$allys=mysql_query("SELECT id FROM de_allys where leaderid='$ums_user_id'");


if(mysql_num_rows($allys)<1)



$resource=mysql_query("SELECT user_id FROM de_user_data WHERE allytag='FG�' AND status=1");

while($row=mysql_fetch_array($resource)) {
mysql_query("update de_user_data set newtrans = 1 where user_id = $row[user_id]");

$text=nl2br($text);
mysql_query("INSERT INTO de_user_trans (user_id,fromsec,fromsys,fromnic,time,betreff,text) VALUES ('$row[user_id]','$sector','$system', '$ums_spielername',NOW(),'$betreff','$text')");
}

echo $allymessagezwei_lang[msg_1];



}


else {

?>


<p><font face="Verdana" size="7"><?=$allymessagezwei_lang[allymultimsg]?></font></p>

<form name="register" method="POST" action="ally_message2.php?SID=<?=$SID ?>">

  <table border="0" width="400" cellspacing="0" cellpadding="0">

    <tr>

      <td width="50%"><font face="Verdana" size="2"><?=$allymessagezwei_lang[betreff]?>:</font></td>

      <td width="50%"><font face="Verdana" size="2"><input name="betreff" size="20"></font></td>

    </tr>

    <tr>

      <td width="50%"><font face="Verdana" size="2"><?=$allymessagezwei_lang[nachricht]?>:</font></td>

      <td width="50%"><textarea rows="5" name="text" cols="30"></textarea></td>

    </tr>

  </table>

  <p><font face="MS Sans Serif" size="1"><?=$allymessagezwei_lang[msg_2]?>.</font></p>

  <p><input type="submit" value="<?=$allymessagezwei_lang[abschicken]?>" name="B1"><input type="reset" value="<?=$allymessagezwei_lang[zuruecksetzen]?>" name="B2"></p>

</form>

<?
}
?>
<?php include("ally/ally.footer.inc.php") ?>