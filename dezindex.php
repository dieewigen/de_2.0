<?php
	include "inc/header.inc.php";
         include 'inc/lang/'.$sv_server_lang.'_dezindex.lang.php';

	include "dez/dez_db.inc.php";
	include "dez/dez_set.inc.php";

	$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, tick, score, sector, system, newtrans, newnews FROM de_user_data WHERE user_id='$ums_user_id'",$db);
	$row = mysql_fetch_array($db_daten);
	$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];
	$punkte=$row["score"];$newtrans=$row["newtrans"];$newnews=$row["newnews"];
	$sector=$row["sector"];$system=$row["system"];$accticks=$row["tick"];

?>
<!doctype html>
<html>
<head>
	<title><?=$dezindex_lang[deztitle]?></title>
	<?php include "cssinclude.php"; ?>

	<script language="JavaScript" type="text/javascript">
		var myAgent = navigator.userAgent.toLowerCase();
		var isIE = ((myAgent.indexOf("msie") != -1) && (myAgent.indexOf("opera") == -1));

		function sov(obj_id, obj_typ, obj_typ_ie) {
			if (document.getElementById(obj_id).style.display == "none") {
				if (isIE) { document.getElementById(obj_id).style.display = obj_typ_ie; }
				else { document.getElementById(obj_id).style.display = obj_typ; }
			}
			else { document.getElementById(obj_id).style.display = "none"; }
		}
	</script>
</head>
<body>

<?php
	include "resline.php";

	echo '<div class="cell" style="width: 600px;">';
	
	switch ($_GET['site']) {
		case 'my':
			include('dez/dez_my.inc.php');
			break;
		case 'create':
			include('dez/dez_create.inc.php');
			break;
		case 'new':
			include('dez/dez_new.inc.php');
			break;
		case 'archiv':
			include('dez/dez_archiv.inc.php');
			break;
		case 'hf':
			include('dez/dez_hf.inc.php');
			break;
		default:
			include('dez/dez_idx.inc.php');
			break;
	}

	include('dez/dez_footer.inc.php');
?>