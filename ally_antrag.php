<?php
//        --------------------------------- ally_antrag.php ---------------------------------
//        Funktion der Seite:                B&uuml;ndnisantr&auml;ge stellen
//        Letzte &Auml;nderung:                05.09.2002
//        Letzte &Auml;nderung von:        Ascendant
//
//        &Auml;nderungshistorie:
//
//        05.02.2002 (Ascendant)        - Erweiterung der &Auml;nderungsbefugnis der B&uuml;ndnisantr&auml;ge
//                                                          auf Coleader
//  --------------------------------------------------------------------------------
include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.antrag.lang.php');
include_once('functions.php');

$db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag 
     FROM de_user_data WHERE user_id = ?",
    [$ums_user_id]
);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allyantrag_lang['title'];?></title>
<?php include('cssinclude.php'); ?>
</head>
<body>
<?php
include('resline.php');
include('ally/ally.menu.inc.php');
?>

<table border="0" width="600" cellspacing="0" cellpadding="0">
<tr>
<td width="13" height="37" class="rol">&nbsp;</td>
<td  align="center" class="ro"><?=$allyantrag_lang['memberantraege']?></td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<?php
//Pr&uuml;fung auf coleader hinzugef&uuml;gt von Ascendant (4.9.2002)
$result = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT allytag FROM de_allys 
     WHERE leaderid = ? OR coleaderid1 = ? OR coleaderid2 = ? OR coleaderid3 = ?",
    [$ums_user_id, $ums_user_id, $ums_user_id, $ums_user_id]
);
$row = mysqli_fetch_assoc($result);
$clankuerzel = $row["allytag"] ?? '';

$result = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT user_id, sector, `system` FROM de_user_data WHERE status = '0' AND allytag = ?",
    [$clankuerzel]
);

$nb = mysqli_num_rows($result);

$row = 0;

//fix gegen das anzeigen von allen allylosen
if ($clankuerzel=='') $row=$nb;

while ($row < $nb){
        $userData = mysqli_fetch_assoc($result);
        $userid = $userData["user_id"];
        $se = $userData["sector"];
        $sy = $userData["system"];

        $result2 = mysqli_execute_query($GLOBALS['dbi'],
            "SELECT spielername, tick, col, score, sector, `system`, rasse, actpoints, tick 
             FROM de_user_data WHERE user_id = ?",
            [$userid]
        );
        $playerData = mysqli_fetch_assoc($result2);
        $name = $playerData["spielername"];
        $b_score = $playerData["score"];
        $b_sector = $playerData["sector"];
        $b_system = $playerData["system"];
        $b_actpoints = $playerData["actpoints"];
        $b_cols = $playerData["col"];
        $b_race = $playerData["rasse"];
        $m_actpoints = $playerData["actpoints"];
        $m_tick = $playerData["tick"];

    	$activity=$m_actpoints/$m_tick*1000;

        $r_text = "?";

        if ($b_race == "1")
        {
        	$r_text = "E";
        }
        elseif ($b_race == "2")
        {
        	$r_text = "I";
        }
        elseif ($b_race == "3")
        {
        	$r_text = "K";
        }
        elseif ($b_race == "4")
        {
        	$r_text = "Z";
        }

        $result2 = mysqli_execute_query($GLOBALS['dbi'],
            "SELECT antrag FROM de_ally_antrag WHERE user_id = ?",
            [$userid]
        );
        $antragData = mysqli_fetch_assoc($result2);
        $antragstext = $antragData["antrag"] ?? '';

        echo '
			<tr>
				<td width="13" class="rl">&nbsp;</td>
				<td>
				
					<table border="0" cellspacing="1" cellpadding="0" width="100%">
						<tr>
							<td colspan="4" class="cl">
								<div align="left">
									<strong>'.$name.' ('.$b_sector.':'.$b_system.')</strong>
									<br>'.$allyantrag_lang['punkte'].': '. number_format($b_score, 0,'','.') .' - '.$allyantrag_lang['kollektoren'].': '.$b_cols.' - '.$allyantrag_lang['rasse'].': '.$r_text.'
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="4" class="cl">'.$antragstext.'</td>
						</tr>
						<tr>
							<td class="tc"><a href="ally_annehmen.php?userid='.$userid.'"><font face="tahoma" style="font-size:8pt;">'.$allyantrag_lang['annehmen'].'!</font></a></td>
							<td class="tc"><a href="ally_ablehnen.php?userid='.$userid.'"><font face="tahoma" style="font-size:8pt;">'.$allyantrag_lang['anablehnen'].'!</font></a></td>
							<td class="tc"><a href="details.php?se='.$se.'&sy='.$sy.'"><font face="tahoma" style="font-size:8pt;">'.$allyantrag_lang['sendhfn'].'</font></a></td>
							<td class="tc">
								<a href="javascript:document.f'.$b_sector.'x'.$b_system.'.submit()">
									<font face="tahoma" style="font-size:8pt;">'.$allyantrag_lang['showsec'].'</font>
								</a></td>
						</tr>
					</table>

				</td>
				<td width="13" class="rr">&nbsp;</td>
			</tr>
		';
		
        $row++;
}
?>
<tr>
<td width="13" height="37" class="rml">&nbsp;</td>
<td  align="center" class="ro"><?=$allyantrag_lang['allyantraege']?></td>
<td width="13" class="rmr">&nbsp;</td>
</tr>
<?php
//Pr&uuml;fung auf coleader hinzugef&uuml;gt von Ascendant (4.9.2002)
$result = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT id FROM de_allys 
     WHERE leaderid = ? OR coleaderid1 = ? OR coleaderid2 = ? OR coleaderid3 = ?",
    [$ums_user_id, $ums_user_id, $ums_user_id, $ums_user_id]
);
$row = mysqli_fetch_assoc($result);
$allyid = $row["id"] ?? 0;

$result = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT allytag, antrag, ally_id_antragsteller 
     FROM de_allys, de_ally_buendniss_antrag 
     WHERE ally_id_antragsteller = id AND ally_id_partner = ?",
    [$allyid]
);

while($row = mysqli_fetch_assoc($result)) {
        $name = $row['allytag'];
        $antragstext = $row['antrag'];
        $ally_id_antragsteller = $row['ally_id_antragsteller'];
		echo '<tr>\n
				<td width="13" class="rl">&nbsp;</td>
					<td>
					  <table border="0" cellspacing="1" cellpadding="0" width="100%">
							<tr>
									<td colspan="3" class="tc">'.$name.'</font></a></td></tr>\n
									<tr><td colspan=3 class="cl">'.$antragstext.'</td></tr>\n
									<td class="tc"><a href="ally_annehmen.php?allyid='.$ally_id_antragsteller.'"><font face="tahoma" style="font-size:8pt;">'.$allyantrag_lang['annehmen'].' !</font></a></td>\n
									<td class="tc"><a href="ally_ablehnen.php?allyid='.$ally_id_antragsteller.'"><font face="tahoma" style="font-size:8pt;">'.$allyantrag_lang['anablehnen'].' !</font></a></td>\n
									<td class="tc"><a href="ally_message_leader.php?select='.urlencode($name).'"><font face="tahoma" style="font-size:8pt;">'.$allyantrag_lang['sendhfn'].'</font></a></td>\n
								</tr>
						</tr>
				  </table>
				</td>
				<td width="13" class="rr">&nbsp;</td>
			</tr>';
}


echo         '<tr>'.
                '<td width="13" class="rul">&nbsp;</td>'.
                '<td class="ru">&nbsp;</td>'.
                '<td width="13" class="rur">&nbsp;</td>'.
        '</tr>'.
        "</table>";




?>
<br>
<?php include('ally/ally.footer.inc.php'); ?>
<?php include('fooban.php'); ?>
</body>
</html>