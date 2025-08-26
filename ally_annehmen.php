<?php
//	--------------------------------- ally_annehmen.php ---------------------------------
//	Funktion der Seite:		Annehmen eines Beitrittgesuchs oder eines Bündnisses
//  -------------------------------------------------------------------------------------
include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.annehmen.lang.php');
include_once('functions.php');

$result = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag 
     FROM de_user_data WHERE user_id = ?",
    [$_SESSION['ums_user_id']]
);
$row = mysqli_fetch_assoc($result);
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$allytag=$row["allytag"];

// Parameter aus GET/POST abrufen und validieren
$userid = $_REQUEST['userid'] ?? 0;
$allyid = $_REQUEST['allyid'] ?? 0;

?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allyablehnen_lang['title']?></title>
<?php include('cssinclude.php'); ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

include('resline.php');
include('ally/ally.menu.inc.php');
include('lib/basefunctions.lib.php');
//Pr�fung auf coleader hinzugef�gt von Ascendant (4.9.2002)
$allys = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT * FROM de_allys WHERE leaderid = ? OR coleaderid1 = ? OR coleaderid2 = ? OR coleaderid3 = ?",
    [$_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id']]
);

if(mysqli_num_rows($allys) < 1)
{
	echo $allyablehnen_lang['msg_1'];
}
else
{
	//Pr�fung auf coleader hinzugef�gt von Ascendant (4.9.2002)
	$result = mysqli_execute_query($GLOBALS['dbi'],
        "SELECT id, allytag, memberlimit FROM de_allys 
         WHERE leaderid = ? OR coleaderid1 = ? OR coleaderid2 = ? OR coleaderid3 = ?",
        [$_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id']]
    );
    $row = mysqli_fetch_assoc($result);
    $clanid = $row["id"];
    $clantag = $row["allytag"];
    $memberlimit = $row["memberlimit"];


	if ($userid)
	{
		$result = mysqli_execute_query($GLOBALS['dbi'],
            "SELECT allytag FROM de_user_data WHERE user_id = ?",
            [$userid]
        );
        $row = mysqli_fetch_assoc($result);
        $clan = $row["allytag"];
        
        $c_result = mysqli_execute_query($GLOBALS['dbi'],
            "SELECT COUNT(*) as count FROM de_user_data WHERE allytag = ? AND status = '1'",
            [$clantag]
        );
        $row = mysqli_fetch_assoc($c_result);
        $m_counter = $row["count"];

		if ($memberlimit > $m_counter)
		{
			if($clantag==$clan)
			{
				mysqli_execute_query($GLOBALS['dbi'],
                    "UPDATE de_user_data SET status = '1' WHERE user_id = ?",
                    [$userid]
                );

                $u_data = mysqli_execute_query($GLOBALS['dbi'],
                    "SELECT spielername FROM de_user_data WHERE user_id = ?",
                    [$userid]
                );
                $u_row = mysqli_fetch_assoc($u_data);
                $u_name = $u_row["spielername"];

                mysqli_execute_query($GLOBALS['dbi'],
                    "DELETE FROM de_ally_antrag WHERE user_id = ?",
                    [$userid]
                );
				$transaction_result = mysqli_execute_query($GLOBALS['dbi'],
                    "SELECT * FROM de_transactions 
                     WHERE user_id = ? AND type = 'C.A.R.S.' AND identifier = 'reg_fee' AND name = 'Tronic'",
                    [$userid]
                );
                if ($transaction_result)
                {
                    if (mysqli_num_rows($transaction_result) == 1)
                    {
                        $data = mysqli_fetch_assoc($transaction_result);
                        $sum = $data["amount"];
                        mysqli_execute_query($GLOBALS['dbi'],
                            "UPDATE de_allys SET t_depot = t_depot + ? WHERE id = ?",
                            [$sum, $clanid]
                        );
                        mysqli_execute_query($GLOBALS['dbi'],
                            "UPDATE de_transactions SET amount = '0' 
                             WHERE user_id = ? AND type = 'C.A.R.S.' AND identifier = 'reg_fee' AND name = 'Tronic'",
                            [$userid]
                        );
					}
				}
				notifyUser($userid, "Die Allianz <b>$clantag</b> hat Ihrem Antrag zugestimmt und Sie aufgenommen. Die Registrierungsgeb&uuml;hr von $sum Tronic wurde dem Allianzdepot gutgeschrieben. Bitte beachten Sie, das Registrierungsgeb&uuml;hren nicht steuerlich absetzbar sind. <br>Herzlich Willkommen!", 6);

				echo '<div class="info_box text3">'.$allyablehnen_lang['msg_2_1'].' '.$sum.' '.$allyablehnen_lang['msg_2_2'].'.</div>';
				include('ally/allyfunctions.inc.php');
				writeHistory($clantag, $allyablehnen_lang['msg_3'].' <i>'.$u_name.'</i>',true);
			}
			else
			{
				echo '<div class="info_box text3">'.$allyablehnen_lang['msg_4'].'</div>';
			}
		}
		else
		{
			print('<div class="info_box text3">'.$allyablehnen_lang['msg_5'].'</div>');
		}
	}
	elseif($allyid)
	{
		$result = mysqli_execute_query($GLOBALS['dbi'],
            "SELECT COUNT(*) as count FROM de_ally_partner WHERE ally_id_1 = ? OR ally_id_2 = ?",
            [$allyid, $clanid]
        );
        $row = mysqli_fetch_assoc($result);
        $alreadyinXallys = $row['count'];
        if ($alreadyinXallys >= 2)
            die ($allyablehnen_lang['msg_6_1'].' '.$alreadyinXallys.' '.$allyablehnen_lang['msg_6_2'].' '.$alreadyinXallys.''.$allyablehnen_lang['msg_6_3']);

        $result = mysqli_execute_query($GLOBALS['dbi'],
            "SELECT COUNT(*) as count FROM de_ally_buendniss_antrag WHERE ally_id_antragsteller = ?",
            [$allyid]
        );
        $row = mysqli_fetch_assoc($result);
        $antragexists = $row['count'];
		if ($antragexists == 0)
			die($allyablehnen_lang['msg_7']);
			
		//überprüfen ob man mit dem gewünschten bündnispartner evtl. im krieg ist
		$db_daten = mysqli_execute_query($GLOBALS['dbi'],
            "SELECT * FROM de_ally_war 
             WHERE (ally_id_angreifer = ? AND ally_id_angegriffener = ?) 
             OR (ally_id_angreifer = ? AND ally_id_angegriffener = ?)",
            [$allyid, $allyid_partner, $allyid_partner, $allyid]
        );
        $num = mysqli_num_rows($db_daten);
        if ($num > 0) {
            die ('<div class="info_box text2">Mit dieser Allianz herrscht Krieg und ein B&uuml;ndnis ist nicht m&ouml;glich.</div></body></html>');
        }

        //Test auf Diplomatiezentrum
        $ally_result = mysqli_execute_query($GLOBALS['dbi'],
            "SELECT bldg1 FROM de_allys WHERE allytag = ?",
            [$allytag]
        );
        if ($ally_result){
            $ally_data = mysqli_fetch_assoc($ally_result);
            //diplomatiezentrum
            $bldg = $ally_data['bldg1'];
        }

		//test auf vorhandenes allianzprojekt Diplomatiezentrum
		if($bldg<1){
		die('<br><div class="info_box text2">F&uuml;r ein Allianzb&uuml;ndnis wird ein Diplomatiezentrum ben&ouml;tigt.</div></body></html>');
		}

		mysqli_execute_query($GLOBALS['dbi'],
            "INSERT INTO de_ally_partner (ally_id_1, ally_id_2) VALUES (?, ?)",
            [$clanid, $allyid]
        );
        
        mysqli_execute_query($GLOBALS['dbi'],
            "DELETE FROM de_ally_buendniss_antrag 
             WHERE (ally_id_antragsteller = ? AND ally_id_partner = ?) 
             OR (ally_id_antragsteller = ? AND ally_id_partner = ?)",
            [$allyid, $clanid, $clanid, $allyid]
        );
		echo $allyablehnen_lang['msg_8'];
		include("ally/allyfunctions.inc.php");
		$delallyid1_tag = getAllyTag($clanid);
		$delallyid2_tag = getAllyTag($allyid);

		writeHistory($delallyid1_tag, $allyablehnen_lang['msg_9_1'].' <i>'.$delallyid2_tag.'</i> '.$allyablehnen_lang['msg_9_2'], true);
		writeHistory($delallyid2_tag, $allyablehnen_lang['msg_9_1'].' <i>'.$delallyid1_tag.'</i> '.$allyablehnen_lang['msg_9_2'], true);

	}
}

?>
<?php include('ally/ally.footer.inc.php'); ?>