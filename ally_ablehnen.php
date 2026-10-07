<?php
//	--------------------------------- ally_ablehnen.php ---------------------------------
//	Funktion der Seite:		Ablehnen eines Beitrittgesuchs
//	Letzte Änderung:		05.09.2002
//	Letzte Änderung von:	Ascendant
//
//	Änderungshistorie:
//
//	05.02.2002 (Ascendant)	- Erweiterung der Änderungsbefugnis
//							  auf Coleader
//  -------------------------------------------------------------------------------------
include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.ablehnen.lang.php');
include_once('functions.php');

$db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag 
     FROM de_user_data WHERE user_id = ?",
    [$_SESSION['ums_user_id']]
);
$row = $db_daten->fetch_assoc();
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];

// Parameter aus GET/POST abrufen und validieren
$userid = $_REQUEST['userid'] ?? 0;
$allyid = $_REQUEST['allyid'] ?? 0;

?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allyablehnen_lang['title']; ?></title>
<?php include('cssinclude.php'); ?>
</head>

<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

include('resline.php');
include('ally/ally.menu.inc.php');
include('lib/basefunctions.lib.php');

//Ergebnis als Meldung unter den Reitern, darunter zurück zu den Anträgen
$zurueck = '<div class="ally-aktionen"><a href="ally_antrag.php" class="mod-btn mod-btn-leise">Zur&uuml;ck zu den Antr&auml;gen</a></div>';
echo '<div class="mod ally-meldung">';

//Prüfung auf coleader hinzugefügt von Ascendant (4.9.2002)
$allys = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT * FROM de_allys WHERE leaderid = ? OR coleaderid1 = ? OR coleaderid2 = ? OR coleaderid3 = ?",
    [$_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id']]
);

if($allys->num_rows < 1)
{
	echo '<div class="mod-meldung mod-meldung-fehler">'.$allyablehnen_lang['msg_1'].'</div>';
}
else
{
	//Prüfung auf coleader hinzugefügt von Ascendant (4.9.2002)
	$result = mysqli_execute_query($GLOBALS['dbi'],
        "SELECT id, allytag FROM de_allys WHERE leaderid = ? OR coleaderid1 = ? OR coleaderid2 = ? OR coleaderid3 = ?",
        [$_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id']]
    );
    $row = $result->fetch_assoc();
    $clanid = $row['id'];
    $clantag = $row['allytag'];

	if($userid)
	{
		$result = mysqli_execute_query($GLOBALS['dbi'],
            "SELECT allytag, status FROM de_user_data WHERE user_id = ?",
            [$userid]
        );
        $row = $result->fetch_assoc();
        $clan = $row['allytag'] ?? '';

		//nur Bewerber mit offenem Antrag bei dieser Allianz; Mitglieder (auch der Leader) gehen nur über ally_kick.php
		$antrag_result = mysqli_execute_query($GLOBALS['dbi'],
            "SELECT user_id FROM de_ally_antrag WHERE user_id = ? AND ally_id = ?",
            [$userid, $clanid]
        );

		if($clantag==$clan && ($row['status'] ?? 1)==0 && mysqli_num_rows($antrag_result)>0)
		{
			mysqli_execute_query($GLOBALS['dbi'],
                "UPDATE de_user_data SET allytag = '', ally_id = 0 WHERE user_id = ? AND status = 0",
                [$userid]
            );

            mysqli_execute_query($GLOBALS['dbi'],
                "DELETE FROM de_ally_antrag WHERE user_id = ?",
                [$userid]
            );
			$transaction_result = mysqli_execute_query($GLOBALS['dbi'],
                "SELECT * FROM de_transactions WHERE user_id = ? AND type = 'C.A.R.S.' AND identifier = 'reg_fee' AND name = 'Tronic'",
                [$userid]
            );
            if ($transaction_result)
            {
                if ($transaction_result->num_rows == 1)
                {
                    $data = $transaction_result->fetch_assoc();
                    $sum = $data["amount"];
                    mysqli_execute_query($GLOBALS['dbi'],
                        "UPDATE de_user_data SET restyp05 = restyp05 + ? WHERE user_id = ?",
                        [$sum, $userid]
                    );
                    mysqli_execute_query($GLOBALS['dbi'],
                        "UPDATE de_transactions SET amount = '0' WHERE user_id = ? AND type = 'C.A.R.S.' AND identifier = 'reg_fee' AND name = 'Tronic'",
                        [$userid]
                    );
				}
			}
			notifyUser($userid, $allyablehnen_lang['msg_2_1'].' '.$clantag.' '.$allyablehnen_lang['msg_2_2'].' '.$sum.' '.$allyablehnen_lang['msg_2_3'], 6);
			echo '<div class="mod-meldung mod-meldung-ok">'.$allyablehnen_lang['msg_3'].'</div>';
		}
		else
		{
			echo '<div class="mod-meldung mod-meldung-fehler">'.$allyablehnen_lang['msg_4'].'</div>';
		}
	}
	elseif($allyid)
	{
		//nur ein Angebot, das noch an diese Allianz geht: ein neueres Angebot an eine andere Allianz ersetzt es (ally_partner.php)
		$result = mysqli_execute_query($GLOBALS['dbi'],
            "SELECT COUNT(*) as count FROM de_ally_buendniss_antrag WHERE ally_id_antragsteller = ? AND ally_id_partner = ?",
            [$allyid, $clanid]
        );
        $row = $result->fetch_assoc();
        $antragexists = $row['count'];
        if ($antragexists == 0)
            die('<div class="mod-meldung mod-meldung-fehler">'.$allyablehnen_lang['msg_5'].'</div>'.$zurueck.'</div>');

        mysqli_execute_query($GLOBALS['dbi'],
            "DELETE FROM de_ally_buendniss_antrag WHERE ally_id_antragsteller = ? AND ally_id_partner = ?",
            [$allyid, $clanid]
        );
		echo '<div class="mod-meldung mod-meldung-ok">'.$allyablehnen_lang['msg_6'].'</div>';

		//die anbietende Allianz erfährt die Ablehnung, bisher verschwand das Angebot bei ihr kommentarlos
		include_once('ally/allyfunctions.inc.php');
		$antragsteller_tag = getAllyTag($allyid);
		writeHistory($clantag, $allyablehnen_lang['msg_7_1'].' <i>'.$antragsteller_tag.'</i> '.$allyablehnen_lang['msg_7_2'], true);
		writeHistory($antragsteller_tag, $allyablehnen_lang['msg_8_1'].' <i>'.$clantag.'</i> '.$allyablehnen_lang['msg_8_2'], true);
	}

}
echo $zurueck.'</div>';



?>
<?php include('ally/ally.footer.inc.php'); ?>