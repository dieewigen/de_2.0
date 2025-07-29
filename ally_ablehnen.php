<?php
//	--------------------------------- ally_ablehnen.php ---------------------------------
//	Funktion der Seite:		Ablehnen eines Beitrittgesuchs
//	Letzte �nderung:		05.09.2002
//	Letzte �nderung von:	Ascendant
//
//	�nderungshistorie:
//
//	05.02.2002 (Ascendant)	- Erweiterung der �nderungsbefugnis
//							  auf Coleader
//  -------------------------------------------------------------------------------------
include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.ablehnen.lang.php');
include_once('functions.php');

$db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag 
     FROM de_user_data WHERE user_id = ?",
    [$ums_user_id]
);
$row = $db_daten->fetch_assoc();
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allyablehnen_lang['title']; ?></title>
<?php include('cssinclude.php'); ?>
</head>
<body>

<?php
include('resline.php');
include('ally/ally.menu.inc.php');
include('lib/basefunctions.lib.php');
//Pr�fung auf coleader hinzugef�gt von Ascendant (4.9.2002)
$allys = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT * FROM de_allys WHERE leaderid = ? OR coleaderid1 = ? OR coleaderid2 = ? OR coleaderid3 = ?",
    [$ums_user_id, $ums_user_id, $ums_user_id, $ums_user_id]
);

if($allys->num_rows < 1)
{
	echo $allyablehnen_lang['msg_1'];
}
else
{
	//Pr�fung auf coleader hinzugef�gt von Ascendant (4.9.2002)
	$result = mysqli_execute_query($GLOBALS['dbi'],
        "SELECT id, allytag FROM de_allys WHERE leaderid = ? OR coleaderid1 = ? OR coleaderid2 = ? OR coleaderid3 = ?",
        [$ums_user_id, $ums_user_id, $ums_user_id, $ums_user_id]
    );
    $row = $result->fetch_assoc();
    $clanid = $row['id'];
    $clantag = $row['allytag'];

	if($userid)
	{
		$result = mysqli_execute_query($GLOBALS['dbi'],
            "SELECT allytag FROM de_user_data WHERE user_id = ?",
            [$userid]
        );
        $row = $result->fetch_assoc();
        $clan = $row['allytag'];

		if($clantag==$clan)
		{
			mysqli_execute_query($GLOBALS['dbi'],
                "UPDATE de_user_data SET allytag = '' WHERE user_id = ?",
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
			echo $allyablehnen_lang['msg_3'];
		}
		else
		{
			echo $allyablehnen_lang['msg_4'];
		}
	}
	elseif($allyid)
	{
		$result = mysqli_execute_query($GLOBALS['dbi'],
            "SELECT COUNT(*) as count FROM de_ally_buendniss_antrag WHERE ally_id_antragsteller = ?",
            [$allyid]
        );
        $row = $result->fetch_assoc();
        $antragexists = $row['count'];
        if ($antragexists == 0)
            die($allyablehnen_lang['msg_5']);

        mysqli_execute_query($GLOBALS['dbi'],
            "DELETE FROM de_ally_buendniss_antrag WHERE ally_id_antragsteller = ? AND ally_id_partner = ?",
            [$allyid, $clanid]
        );
		echo $allyablehnen_lang['msg_6'];
	}

}



?>
<?php include('ally/ally.footer.inc.php'); ?>