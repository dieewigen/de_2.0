<?php
//        --------------------------------- allymain.php ---------------------------------
//        Funktion der Seite:                Anzeige der Allianz&uuml;bersicht
//        Letzte &Auml;nderung:                05.09.2002
//        Letzte &Auml;nderung von:        Ascendant
//
//        &Auml;nderungshistorie:
//
//        05.02.2002 (Ascendant)        - Erweiterung der &Auml;nderungsbefugnis der Allianzdaten
//                                                          auf Coleader
//                                                        - Erweiterung der Seite um Anzeige des Leaders und der
//                                                          Coleader. Per Klick auf die Namen kann dem Leader und
//                                                          den Co-Leadern eine Nachricht gesendet werden.
//  --------------------------------------------------------------------------------
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_ally.detail.lang.php';
include_once 'functions.php';


$result = mysqli_execute_query($GLOBALS['dbi'], 
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag, status 
     FROM de_user_data WHERE user_id=?",
    [$ums_user_id]);
$row = mysqli_fetch_array($result);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$allytag=$row["allytag"];$status=$row["status"];
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allydetail_lang['title']?></title>
<?php include "cssinclude.php"; ?>
</head>
<body>



<?php
function formatString($string)
{
	$allowed_tags="<br><i></i><b></b><strong></strong><u></u><ul></ul><li></li><p></p><font></font>";
	$result = strip_tags($string, $allowed_tags);
	return $result;
}

include "resline.php";
include ("ally/ally.menu.inc.php");

$allytag=$_REQUEST['allytag'] ?? '';
$allyid=$_REQUEST['allyid'] ?? '';

$result = mysqli_execute_query($GLOBALS['dbi'], 
    "SELECT * FROM de_allys WHERE id=? OR allytag=? LIMIT 0,1",
    [$allyid, $allytag]);
$num = mysqli_num_rows($result);
if($num==1)
{
	$row = mysqli_fetch_assoc($result);
	
	$clanid 		= $row["id"];
	$clanname 		= $row["allyname"];
	$clankuerzel 	= $row["allytag"];
	$leaderid		= $row["leaderid"];
	$homepageurl 	= $row["homepage"];
	$memberlimit 	= $row["memberlimit"];
	$openirc	 	= $row["openirc"];
	$bewerberinfo 	= formatString($row["bewerberinfo"]);
	
	$result2 = mysqli_execute_query($GLOBALS['dbi'], 
	    "SELECT COUNT(*) as count FROM de_user_data WHERE allytag=? AND status=1",
	    [$clankuerzel]);
	$count_row = mysqli_fetch_assoc($result2);
	$membercount = $count_row['count'];
	
	$bio = formatString($row["besonderheiten"]);
	$ausrichtung = $row["ausrichtung"];
	$regierungsform = $row["regierungsform"];
	$allianzform = $row["allianzform"];
	
	
	
	print("<br>");
	rahmen_oben('Allianzinformationen');
	echo '<div align="center"><table width="574px">';
	//print("<tr class=\"cell\"><td><h2>$allydetail_lang[willkommen], $ums_spielername</h2></td></tr>");
	print("<tr><td>
			<table border=\"0\" width=\"100%\" cellspacing=\"1\" cellpadding=\"0\">
	    		<tr>
	      			<td width=100% height=21 colspan=2 class=\"cellu\"><h3>$allydetail_lang[info] ".utf8_encode($clanname)." ($clankuerzel):</h3></td>
	    		</tr>
	    		<tr class=cl>
	      			<td height=21>$allydetail_lang[allyname]:</td>
	      			<td height=21><b>".utf8_encode($clanname)."</b></td>
	    		</tr>
	    		<tr class=cl>
	      			<td height=21>$allydetail_lang[allytag]:</td>
	      			<td height=21><b>".utf8_encode($clankuerzel)."</b></td>
	    		</tr>");
	//allyleader inkl. hf-möglichkeit 
	$result = mysqli_execute_query($GLOBALS['dbi'], 
	    "SELECT spielername, sector, system FROM de_user_data WHERE user_id=?",
	    [$leaderid]);
	$num = mysqli_num_rows($result);
	if($num==1)
	{
		$row = mysqli_fetch_array($result);
		echo '	<tr class=cl>
	      			<td height=21>Allianzleader:</td>
	      			<td height=21><a href="details.php?se='.$row['sector'].'&sy='.$row['system'].'"><b>'.$row['spielername'].'</b></a></td>
	    		</tr>';	
		
	}
	
	$discord_open_link='';
	if(!empty(trim($openirc))){
		$discord_open_link='<a href="https://discord.gg/'.$openirc.'" target="_blank">zu Discord</a>';
	}	
	
	print("		<tr class=cl>
	      			<td height=21>$allydetail_lang[memberlimit]:</td>
	      			<td height=21><b>$memberlimit</b></td>
	    		</tr>
	 			<tr class=cl>
	      			<td height=21>$allydetail_lang[regierungsform]:</td>
	      			<td height=21><b>$regierungsform</b></td>
	    		</tr>
	 			<tr class=cl>
	      			<td height=21>$allydetail_lang[politischeausrichtung]:</td>
	      			<td height=21><b>".utf8_encode($ausrichtung)."</b></td>
	    		</tr>
	    		<tr class=cl>
	      			<td height=21>$allydetail_lang[mitglieder]:</td>
	      			<td height=21><b>$membercount</b></td>
	    		</tr>
	    		<tr class=cl>
	      			<td height=21>$allydetail_lang[ircchannel]:</td>
	      			<td height=21>".$discord_open_link."</td>
	    		</tr>
	    		<tr class=cl>
	      			<td height=21>$allydetail_lang[homepage]:</td>
	      			<td height=21><b><a href=\"$homepageurl\" target=_blank>$homepageurl</a></b></td>
	    		</tr>
	");
	print("
	    		<tr>
	      			<td height=21 colspan=2 class=cellu><h3>$allydetail_lang[allianzbiografie]:</h3></td>
	    		</tr>
	    		<tr>
	      			<td class=cl height=21 colspan=2>".utf8_encode($bio)."</td>
	    		</tr>
	    		<tr>
	      			<td height=21 colspan=2 class=cellu><h3>$allydetail_lang[bewerberinfo]:</h3></td>
	    		</tr>
	    		<tr>
	      			<td class=cl height=21 colspan=2>".utf8_encode($bewerberinfo)."</td>
	    		</tr>
	    		<tr>
	      			<td height=21 colspan=2><hr></td>
	    		</tr>
	    		");
	if ($status != 1 && $memberlimit>$membercount){
		$join_link="&middot; <a href=\"ally_join.php?ally_id=".$clanid."\">$allydetail_lang[msg_1]</a>";
		//$join_link="&middot; <a href=\"ally_join.php?a_id=$clanid\">$allydetail_lang[msg_1]</a>";
	}
	elseif ($status == 1)
	{
		$join_link = "&middot; $allydetail_lang[msg_2]";
	}
	elseif ($memberlimit<=$membercount)
	{
		$join_link = "&middot; $allydetail_lang[msg_3]";
	}
	print("
		    		<tr class=\"cell\">
		      			<td align=right height=21 colspan=2><a href=\"javascript:history.back()\">$allydetail_lang[msg_4]</a> $join_link</td>
		    		</tr>
		");
	print("</table></td></tr></table>");
	rahmen_unten();
}
else echo '<div class="info_box text2">Diese Allianz konnte nicht gefunden werden.</a>';
?>
<br>
<?php include("ally/ally.footer.inc.php") ?>
<?php include "fooban.php"; ?>
</body>
</html>