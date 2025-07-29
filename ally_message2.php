<?php
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_ally.messagezwei.lang.php';
include_once 'functions.php';

$db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag 
     FROM de_user_data 
     WHERE user_id=?",
    [$ums_user_id]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01 = $row['restyp01'];
$restyp02 = $row['restyp02'];
$restyp03 = $row['restyp03'];
$restyp04 = $row['restyp04'];
$restyp05 = $row['restyp05'];
$punkte = $row["score"];
$newtrans = $row["newtrans"];
$newnews = $row["newnews"];
$sector = $row["sector"];
$system = $row["system"];
$allytag = $row["allytag"];

?>
<!DOCTYPE HTML>
<html>
<head>
<title><?=$allymessagezwei_lang[title]?></title>
<?php include "cssinclude.php"; ?>
</head>
<body>
<?php
include "resline.php";

if ($text) {

    $allys = mysqli_execute_query($GLOBALS['dbi'],
        "SELECT id FROM de_allys WHERE leaderid=?",
        [$ums_user_id]);

    if (mysqli_num_rows($allys) < 1) {
        $resource = mysqli_execute_query($GLOBALS['dbi'],
            "SELECT user_id FROM de_user_data WHERE allytag='FG�' AND status=1");
    }

    while ($row = mysqli_fetch_assoc($resource)) {
        mysqli_execute_query($GLOBALS['dbi'],
            "UPDATE de_user_data SET newtrans=1 WHERE user_id=?",
            [$row['user_id']]);

        $text = nl2br($text);
        mysqli_execute_query($GLOBALS['dbi'],
            "INSERT INTO de_user_trans (user_id, fromsec, fromsys, fromnic, time, betreff, text) 
             VALUES (?, ?, ?, ?, NOW(), ?, ?)",
            [$row['user_id'], $sector, $system, $ums_spielername, $betreff, $text]);
    }

    echo $allymessagezwei_lang[msg_1];



} else {

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

<?php
}
?>
<?php include("ally/ally.footer.inc.php") ?>