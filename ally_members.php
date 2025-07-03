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
include('lib/religion.lib.php');
include_once('functions.php');

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allymembers_lang['title'];?></title>
<?php include('cssinclude.php'); ?>
</head>
<body>

<?php
include('resline.php');

include('ally/ally.menu.inc.php');

if ($isleader)
{
        $query = "SELECT * FROM de_allys where leaderid='$ums_user_id'";
        $result = mysql_query($query);

        $clankuerzel = mysql_result($result,0,"allytag");
}
//Abfraqe auf Co-Leader hinzugef&uuml;gt von Ascendant (03.09.2002)
elseif ($iscoleader)
{
        $query = "SELECT * FROM de_allys where coleaderid1='$ums_user_id' OR coleaderid2='$ums_user_id' OR coleaderid3='$ums_user_id'";
        $result = mysql_query($query);

        $clankuerzel = mysql_result($result,0,"allytag");
}
else
{
        $query = "SELECT allytag FROM de_user_data where user_id='$ums_user_id'";
        $result = mysql_query($query);

        $clankuerzel = mysql_result($result,0,"allytag");
}

$a_result = mysql_query("select public_activity from de_allys WHERE allytag='$clankuerzel'");
$showactivity = mysql_result($a_result,0,"public_activity");

if (!has_position("leaderid", $clankuerzel, $ums_user_id) && !has_position("coleaderid1", $clankuerzel, $ums_user_id) && !has_position("coleaderid2", $clankuerzel, $ums_user_id) && !has_position("coleaderid3", $clankuerzel, $ums_user_id) && !has_position("memberofficer1", $clankuerzel, $ums_user_id) && !has_position("memberofficer2", $clankuerzel, $ums_user_id))
{
	if (!$showactivity)
	{
		$hide_activity = true;
	}
}

echo '
<table width="600" border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="*" align="center" class="ro">'.$allymembers_lang['mitgliederliste'].'</td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr><td width="13" class="rl">&nbsp;</td><td>
<table border="0" width="100%" cellspacing="1" cellpadding="0">

  <tr>
    <td width="*" class="tc" colspan="8">'.$allymembers_lang['nameprofil'].'</font></td>';

echo '</tr>';

echo '<tr>'.
        '<td class="tc"><a href="ally_members.php?ordermode=name">'.$allymembers_lang['name'].'</a></td>'.
        '<td class="tc"><a href="ally_members.php?ordermode=cols">'.$allymembers_lang['kollies'].'</a></td>'.
        '<td class="tc"><a href="ally_members.php?ordermode=points">'.$allymembers_lang['punkte'].'</a></td>'.
        '<td class="tc"><a href="ally_members.php?ordermode=koords">'.$allymembers_lang['koords'].'</a></td>'.
        '<td class="tc"><a href="ally_members.php?ordermode=race">'.$allymembers_lang['rasse'].'</a></td>'.
		'<td class="tc" title="Bonuspunkterang">B</a></td>';
//echo    '<td class="tc">'.$allymembers_lang[aktiv].'</a></td>';

if ($isleader)
        echo '<td class="tc">'.$allymembers_lang['kicken'].'</td>'.
                '<td class="tc">'.$allymembers_lang['leader'].'</td>';
if ($iscoleader)
        echo '<td class="tc">'.$allymembers_lang['kicken'].'</td>';
echo '</tr>';

if(!empty($ordermode)) {
	
	if ($ordermode == "koords")
	{
		$orderstring = "sector, `system` ASC";
	}
	elseif ($ordermode=="name")
	{
		$orderstring = "spielername ASC";
	}
	elseif($ordermode=="points")
	{
		$orderstring = "score DESC";
	}
	elseif($ordermode=="cols")
	{
		$orderstring = "col DESC";
	}
	elseif($ordermode=="race")
	{
		$orderstring = "rasse ASC";
	}
	elseif($ordermode=="activity")
	{
		$orderstring = "activity DESC";
	}
	
}
else
{
	$orderstring = "sector, `system` ASC";
}

//$query = "SELECT *  FROM de_user_data WHERE status='1' AND allytag='$clankuerzel' ORDER BY sector, `system`";
$query = "SELECT *  FROM de_user_data WHERE status='1' AND allytag='$clankuerzel' ORDER BY $orderstring";


$result = mysql_query($query);

$nb = mysql_num_rows($result);

$row = 0;

while ($row < $nb){
        $userid = mysql_result($result,$row,"user_id");
        $sector = mysql_result($result,$row,"sector");
        $system = mysql_result($result,$row,"system");
        $score = mysql_result($result,$row,"score");
        $kollies = mysql_result($result,$row,"col");
        $m_rasse = mysql_result($result,$row,"rasse");
        $m_actpoints = mysql_result($result,$row,"actpoints");
        $m_tick = mysql_result($result,$row,"tick");

    	$activity=$m_actpoints/$m_tick*1000;
    	if (!empty($hide_activity))
    	{
    		$activity = "*****";
    	}
    	else
    	{
    		$activity = number_format($activity, 2,",",".");
    	}

        $r_text = "?";

        if ($m_rasse == "1")
        {
        	$r_text = "E";
        }
        elseif ($m_rasse == "2")
        {
        	$r_text = "I";
        }
        elseif ($m_rasse == "3")
        {
        	$r_text = "K";
        }
        elseif ($m_rasse == "4")
        {

        	$r_text = "Z";
        }

        $sectorjump = explode(":",$sector);
        $sectorjump = $sectorjump[0];

        $tquery= mysql_fetch_array(mysql_query("SELECT * FROM de_user_data where user_id='$userid'"));
        $name = $tquery['spielername'];
        $de_login_result = mysql_query("select status from de_login WHERE user_id='$userid'");
        $de_login_data = mysql_fetch_array($de_login_result);
        $de_login_status = $de_login_data["status"];
        if ($de_login_status != 1)
        {
        	$name = "<i>(".$name.")</i>";
        }
    echo '
		<form name="f'.$sector.'x'.$system.'" action="sector.php?sf='.$sectorjump.'" method="POST">
			<tr>
				<td class="cl"><a href="details.php?a=s&se='.$sector.'&sy='.$system.'">'.utf8_encode_fix($name).'</a></td>
				<td class="cr">'.$kollies.'</td>
				<td class="cr">'.number_format($score, 0,'','.').'</td>
				<td class="cc"><a href="javascript:document.f'.$sector.'x'.$system.'.submit()">['.$sector.':'.$system.']</a></font></a></td>
				<td class="cc">'.$r_text.'</td>
	';
    
    //religi�sen rang ausgeben
	$db_datenx=mysql_query("SELECT owner_id FROM de_login WHERE user_id='$userid'",$db);
	$rowx = mysql_fetch_array($db_datenx);
	$owner_id=intval($rowx["owner_id"]);
    $relrang=get_religion_level($owner_id);
    //echo '<td class="cc"><img src="'.$ums_gpfad.'g/r/rrang'.$relrang.'.png" title="'.$allymembers_lang[religionraenge][$relrang].'"></td>';
	echo '<td class="cc">'.($relrang+1).'</td>';
     
     //echo "<td class=\"cr\">".$activity."</td>\n";

        if ($isleader)
                echo '
						<td class="cr"><a onClick="return confirm(\''.$allymembers_lang['msg_1_1'].' '.$name.' '.$allymembers_lang['msg_1_2'].'\');" href="ally_kick.php?userid='.$userid.'"><font face="tahoma" style="font-size:8pt;">'.$allymembers_lang['entlassen'].'</font></a></td>
						<td class="cr"><a onClick="return confirm(\''.$allymembers_lang['msg_2_1'].' '.$name.' '.$allymembers_lang['msg_2_2'].'\');" href="ally_leader.php?userid='.$userid.'"><font face="tahoma" style="font-size:8pt;">'.$allymembers_lang['toleader'].'</font></a></td>
				';
        //Erzeugen der Adminlinks f&uuml;r Co-Leader
        if ($iscoleader)
                echo '
						<td class="cr"><a onClick="return confirm(\''.$allymembers_lang['msg_1_1'].' '.$name.' '.$allymembers_lang['msg_1_2'].'\');" href="ally_kick.php?userid='.$userid.'"><font face="tahoma" style="font-size:8pt;">'.$allymembers_lang['entlassen'].'</font></a></td>
					</tr>
				</form>
				';
        $row++;
}



echo '
			</table>
        </td>
		<td width="13" class="rr">&nbsp;</td>
	</tr>
	<tr>
		<td width="13" class="rul">&nbsp;</td>
		<td width="*" class="ru">&nbsp;</td>
        <td width="13" class="rur">&nbsp;</td>
	</tr>
</table>
';


?>
<br>
<?php include('ally/ally.footer.inc.php'); ?>
<?php include('fooban.php'); ?>
</body>
</html>