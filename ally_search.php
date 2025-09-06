<?php
include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.search.lang.php');
include('functions.php');

$db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag, ally_tronic 
     FROM de_user_data 
     WHERE user_id=?",
    [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];$punkte=$row['score'];
$newtrans=$row['newtrans'];$newnews=$row['newnews'];$sector=$row['sector'];$system=$row['system'];
$allytag=$row['allytag'];
$t_level = $row['ally_tronic'];

$allys = mysqli_execute_query($GLOBALS['dbi'], 
    "SELECT * FROM de_allys WHERE leaderid=?", 
    [$_SESSION['ums_user_id']]);
if(mysqli_num_rows($allys)>=1)
{
	$isleader = true;
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allysearch_lang['title']?></title>
<?php include ('cssinclude.php'); ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

$message='';

$searchterm=$_POST['searchterm'] ?? '';
if(empty($searchterm)){
	$searchterm='*';
}

include('resline.php');
include('ally/ally.menu.inc.php');
if (strlen($message) > 0)
{
	print('<table width=600 class="cellu"><tr>');
	print('<td width="30" align="left" valign="top"><img src="'.'gp/'.'g/trade/'.$_SESSION['ums_rasse'].'_arz.gif" alt="Information" border="0"> </td><td align="left"><font size="1">'.$message.'</font><br>');
	print('</td></tr></table>');
}

print('<div align=center class="cellu" style="width: 600px;"><table width="100%">');
print('<tr><td><h2>'.$allysearch_lang['msg_1'].', '.$_SESSION['ums_spielername'].'</h2></td></tr>');
print('<tr><td><hr></td></tr>');

print('
	<tr><td>
	<form name="search" action="ally_search.php" method="post">
		'.$allysearch_lang['suchbegriffe'].' <input size=60 maxlength=100 type=text name=searchterm value="'.$searchterm.'"> <input type=submit name=submit value="'.$allysearch_lang['startsuche'].'">
		<br><br>'.$allysearch_lang['msg_3'].'
	</form>
	</td></tr>
');

if (isset($searchterm) && strlen($searchterm) > 0 && !($searchterm == " ")){
	if ($searchterm == "*")	{
		$query = "SELECT * FROM de_allys WHERE 1";
	}else{
		$searcharray = explode(" ", $searchterm);
		$count_searchwords = count($searcharray);
		$fieldnamearray = array("allyname", "allytag", "regierungsform", "besonderheiten", "keywords");
		$numfields = count($fieldnamearray);
		$query = "SELECT * FROM de_allys WHERE ";
		//Äussere Schleife durchläuft die Suchbegriffe
		for ($i=0;$i<$count_searchwords;$i++)
		{
			//Bedingungsblock anlegen
			$query.="(";
			//Suchwort für Bedingungsblock ermitteln
			$searchword = $searcharray[$i];
			//Innere Schleife durchläuft die Tabellenspaltennamen
			for ($j=0;$j<$numfields;$j++)
			{
				//Bedingung in Query schreiben
				$query.="`".$fieldnamearray[$j]."` LIKE '%".mysqli_real_escape_string($GLOBALS['dbi'], $searchword)."%'";
			 	//Prüfen, ob die Schleife noch nicht am Ende angelangt ist
				if ($j+1<$numfields)
				{
					//Wenn die Schleife noch nicht am Ende ist, ein OR anfügen
					$query.=" OR ";
				}
			}
			//Bedingungsblock für das aktuelle Suchwort schliessen
			$query.=")";
			//Prüfen, ob noch Suchworte folgen
			if ($i+1<$count_searchwords)
			{
				//Wenn es noch folgende Suchworte gibt, Verknüpfungsoperator für die Suchworte (AND oder OR) anfügen
				$query.=" AND ";
			}
		}
	}
	$query.=" ORDER BY allyname";
	
	// Bei komplexen dynamischen Abfragen wie dieser verwenden wir einen direkten Aufruf
	// anstelle von Prepared Statements, da die Query dynamisch erstellt wird
	$result = mysqli_query($GLOBALS['dbi'], $query);
	if ($result){
		$datalines = mysqli_num_rows($result);
		if ($datalines > 0){
			print('<tr><td><h3>'.$allysearch_lang['suchergebnisse'].'</h3></td></tr>');
			print('<tr><td><hr></td></tr>');
			print('<tr><td><table width="100%">');
			print('<tr><td align="center" bgcolor="#1c1c1c"><strong>'.$allysearch_lang['allianztag'].'</strong></td><td align="center" bgcolor="#1c1c1c"><strong>'.$allysearch_lang['allianzname'].'</strong><td align="center" bgcolor="#1c1c1c"><strong>'.$allysearch_lang['regierungsform'].'</strong></td><td align="center" bgcolor="#1c1c1c"><strong>'.$allysearch_lang['limit'].'</strong></td></td><td align="center" bgcolor="#1c1c1c">&nbsp;</td></tr>');
			for ($i=0;$i<$datalines;$i++)
			{
				$dataline = mysqli_fetch_assoc($result);
				$a_id = $dataline['id'];
				$a_tag = $dataline['allytag'];
				$a_name = html_entity_decode($dataline['allyname']);
				$a_form = $dataline['regierungsform'];
				$a_memberlimit = $dataline['memberlimit'];
				if (strlen($a_name) > 30)
				{
					$a_name = substr($a_name, 0, 27)."...";
				}

				$a_id_safe = htmlspecialchars($a_id, ENT_QUOTES, 'UTF-8');
				$detaillink = '<a href="ally_detail.php?allyid='.$a_id_safe.'">'.$allysearch_lang['detailsbewerben'].'</a>';
				print("<tr><td align=center bgcolor=#222222>".utf8_encode_fix($a_tag)."</td>
					<td align=center bgcolor=#222222>".utf8_encode_fix($a_name)."</td>
					<td align=center bgcolor=#222222>".utf8_encode_fix($a_form)."</td>
					<td align=center bgcolor=#222222>".$a_memberlimit."</td>
					<td align=center bgcolor=#222222>".$detaillink."</td></tr>");
			}
			print('</table></td></tr>');
		}
		else
		{
			print('<tr><td>'.$allysearch_lang['msg_2_1'].' <i>'.$searchterm.'</i> '.$allysearch_lang['msg_2_2'].'</td></tr>');
		}
	}
}else{
	print('<tr><td>'.$allysearch_lang['msg_3'].'</td></tr>');
}

?>
<br>
<?php
	include('ally/ally.footer.inc.php');
	include('fooban.php');
?>
</body>
</html>