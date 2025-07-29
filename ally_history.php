<?php
//        --------------------------------- ally_members.php ---------------------------------
//        Funktion der Seite:                Anzeigen der Allianzmitglieder
//        Letzte &Auml;nderung:                05.09.2002
//        Letzte &Auml;nderung von:        Ascendant
//
//        &Auml;nderungshistorie:
//
//        05.02.2002 (Ascendant)        - Erweiterung der Adminrechte bis auf Leader ernennen
//                                                          auf Co-Leader
//  --------------------------------------------------------------------------------

include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.history.lang.php');
include_once('functions.php');

$result = mysqli_execute_query($GLOBALS['dbi'], 
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag, spielername 
     FROM de_user_data 
     WHERE user_id=?",
    [$ums_user_id]);
$row = mysqli_fetch_assoc($result);
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];$punkte=$row['score'];
$newtrans=$row['newtrans'];$newnews=$row['newnews'];$sector=$row['sector'];$system=$row['system'];
$spielername=$row['spielername'];$allytag=$row['allytag'];
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allyhistory_lang['title']?></title>
<?php include "cssinclude.php"; ?>
</head>
<body>

<?php
include('resline.php');
include('ally/ally.menu.inc.php');

print('<div align="center" class="cell" style="width: 600px;"><table width="100%">');
print('<tr><td><h2>'.$allyhistory_lang['msg_1'].', '.$spielername.'</h2></td></tr>');
print('<tr><td><hr></td></tr>');
print('</table>');
print('<table width="600">');

include('ally/allyfunctions.inc.php');

$ally_id = getAllyId($allytag);
$history_result = mysqli_execute_query($GLOBALS['dbi'], 
    "SELECT * FROM de_ally_history WHERE allyid = ? ORDER BY timestamp DESC",
    [$ally_id]);
if ($history_result){
	$numrows = mysqli_num_rows($history_result);
	for ($i=0;$i<$numrows;$i++)	{
		$data = mysqli_fetch_assoc($history_result);
		$datum = $data['displaydate'];
		$entry = $data['entry'];
		print('<tr><td width="110" valign="top">'.$datum.'</td><td valign="top">&nbsp; &nbsp;</td><td valign="top">'.$entry.'</td></tr>');
	}
}
print('</table>');



?>
<br>
<?php include('ally/ally.footer.inc.php'); ?>
<?php include('fooban.php'); ?>
</body>
</html>