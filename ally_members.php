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
include('inc/lang/'.$sv_server_lang.'_ally.members.lang.php');
include_once('functions.php');

$pd = loadPlayerData($_SESSION['ums_user_id']);
$row = $pd;
$restyp01 = $row['restyp01'];
$restyp02 = $row['restyp02'];
$restyp03 = $row['restyp03'];
$restyp04 = $row['restyp04'];
$restyp05 = $row['restyp05'];
$punkte = $row["score"];
$col = $row["col"];
$newtrans = $row["newtrans"];
$newnews = $row["newnews"];
$sector = $row["sector"];
$system = $row["system"];



?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allymembers_lang['title'];?></title>
<?php include('cssinclude.php'); ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

include('resline.php');
include('ally/ally.menu.inc.php');

$allyId = -1;
if ($pd['allytag'] != "" && $pd['ally_status'] == 1) {
    $allyId = $pd['ally_id'];
} else {
    // Wenn der Spieler keiner Allianz angehört, dann wird die Seite nicht angezeigt
    echo '<div class="error">'.$allymembers_lang['msg_3'].'</div>';
    exit;
}

rahmen_oben($allymembers_lang['mitgliederliste']);

echo '
<table border="0" width="574px" cellspacing="1" cellpadding="0">

    <tr>
        <td class="tc"><a href="ally_members.php?ordermode=name">'.$allymembers_lang['name'].'</a></td>
        <td class="tc"><a href="ally_members.php?ordermode=cols">'.$allymembers_lang['kollies'].'</a></td>
        <td class="tc"><a href="ally_members.php?ordermode=points">'.$allymembers_lang['punkte'].'</a></td>
        <td class="tc"><a href="ally_members.php?ordermode=koords">'.$allymembers_lang['koords'].'</a></td>
        <td class="tc"><a href="ally_members.php?ordermode=race" title="Rasse">R</a></td>';

if ($isleader) {
    echo '<td class="tc">'.$allymembers_lang['kicken'].'</td>
          <td class="tc">'.$allymembers_lang['leader'].'</td>';
}
if ($iscoleader) {
    echo '<td class="tc">'.$allymembers_lang['kicken'].'</td>';
}
echo '</tr>';

$ordermode = isset($_GET['ordermode']) ? $_GET['ordermode'] : '';

if (!empty($ordermode)) {

    if ($ordermode == "koords") {
        $orderstring = "sector, `system` ASC";
    } elseif ($ordermode == "name") {
        $orderstring = "spielername ASC";
    } elseif ($ordermode == "points") {
        $orderstring = "score DESC";
    } elseif ($ordermode == "cols") {
        $orderstring = "col DESC";
    } elseif ($ordermode == "race") {
        $orderstring = "rasse ASC";
    }

} else {
    $orderstring = "sector, `system` ASC";
}

$result = mysqli_execute_query(
    $GLOBALS['dbi'],
    "SELECT * FROM de_user_data WHERE status=1 AND ally_id= ? ORDER BY $orderstring",
    [$allyId]
);

while ($data = mysqli_fetch_assoc($result)) {

    $userid = $data['user_id'];
    $sector = $data['sector'];
    $system = $data['system'];
    $score = $data['score'];
    $kollies = $data['col'];

    $rasse='';
    if ($data['rasse'] == 1) {
        $rasse='<img src="gp/g/r/raceE.png" title="Die Ewigen" width="16px" height="16px">';
    } elseif ($data['rasse'] == 2) {
        $rasse='<img src="gp/g/r/raceI.png" title="Ishtar" width="16px" height="16px">';
    } elseif ($data['rasse'] == 3) {
        $rasse='<img src="gp/g/r/raceK.png" title="K&#180;Tharr" width="16px" height="16px">';
    } elseif ($data['rasse'] == 4) {
        $rasse='<img src="gp/g/r/raceZ.png" title="Z&#180;tah-ara" width="16px" height="16px">';
    }elseif ($data['rasse'] == 5) {
        $rasse='<img src="gp/g/r/raceD.png" title="DX61a23" width="16px" height="16px">';
    }


    $sectorjump = explode(":", $sector);
    $sectorjump = $sectorjump[0];

    $tquery = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT spielername FROM de_user_data WHERE user_id=?",
        [$userid]
    );
    $row = mysqli_fetch_assoc($tquery);
    $name = $row['spielername'];

    $de_login_result = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT status FROM de_login WHERE user_id=?",
        [$userid]
    );
    $de_login_data = mysqli_fetch_assoc($de_login_result);
    $de_login_status = $de_login_data["status"];
    if ($de_login_status != 1) {
        $name = "<i>(".$name.")</i>";
    }
    echo '
		<form name="f'.$sector.'x'.$system.'" action="sector.php?sf='.$sectorjump.'" method="POST">
			<tr>
				<td class="cl"><a href="details.php?a=s&se='.$sector.'&sy='.$system.'">'.utf8_encode_fix($name).'</a></td>
				<td class="cr">'.$kollies.'</td>
				<td class="cr">'.number_format($score, 0, '', '.').'</td>
				<td class="cc"><a href="javascript:document.f'.$sector.'x'.$system.'.submit()">'.$sector.':'.$system.'</a></font></a></td>
				<td class="cc">'.$rasse.'</td>
	    ';

    if ($isleader) {
        echo '
						<td class="cr"><a onClick="return confirm(\''.$allymembers_lang['msg_1_1'].' '.$name.' '.$allymembers_lang['msg_1_2'].'\');" href="ally_kick.php?userid='.$userid.'"><font face="tahoma" style="font-size:8pt;">'.$allymembers_lang['entlassen'].'</font></a></td>
						<td class="cr"><a onClick="return confirm(\''.$allymembers_lang['msg_2_1'].' '.$name.' '.$allymembers_lang['msg_2_2'].'\');" href="ally_leader.php?userid='.$userid.'"><font face="tahoma" style="font-size:8pt;">'.$allymembers_lang['toleader'].'</font></a></td>
				';
    }
    //Erzeugen der Adminlinks f&uuml;r Co-Leader
    if ($iscoleader) {
        echo '
						<td class="cr"><a onClick="return confirm(\''.$allymembers_lang['msg_1_1'].' '.$name.' '.$allymembers_lang['msg_1_2'].'\');" href="ally_kick.php?userid='.$userid.'"><font face="tahoma" style="font-size:8pt;">'.$allymembers_lang['entlassen'].'</font></a></td>';
    }

    echo '
    	</tr>
	</form>';


}



echo '
	</table>';

rahmen_unten();


?>
<br>
</body>
</html>