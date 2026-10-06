<?php
use DieEwigen\DE2\View\RealTime;

include "inc/header.inc.php";
include 'functions.php';

$db_daten = mysqli_execute_query($GLOBALS['dbi'],
  "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, techs, sector, `system`, score, newtrans, newnews, secmoves FROM de_user_data WHERE user_id=?",
  [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];$techs=$row["techs"];
$secmoves=$row["secmoves"];

?>
<!doctype html>
<html>
<head>
<title>News</title>
<?php include "cssinclude.php"; ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';
include "resline.php";

$id=intval($_REQUEST['id'] ?? -1);
$typ=$_REQUEST['typ'] ?? 0;
$action=$_REQUEST['action'] ?? '';

//Arten der Meldungen (de_news_overview.typ); bisher gibt es nur die DET-Meldungen aus der Übersicht
$np_arten = array(1 => 'DET-Meldungen');

//Datum einer Meldung wie in der Übersicht
function np_datum($zeit)
{
	return date('d.m.Y - H:i', strtotime($zeit));
}

//news anzeigen
if($action!="archiv"){
	$sel_news_show = mysqli_execute_query($GLOBALS['dbi'],
	  "SELECT * FROM de_news_overview WHERE id=?",
	  [$id]);

	$row=mysqli_fetch_assoc($sel_news_show);

	mysqli_execute_query($GLOBALS['dbi'],
	  "UPDATE de_news_overview SET klicks=klicks+1, time=time WHERE id=?",
	  [$id]);

	//////////////////////////////////////
	// feedback formular
	//////////////////////////////////////

	//e-mail senden
	//nur per Formular (POST) und höchstens alle 5 Minuten, sonst ließe sich das Admin-Postfach fluten
	$np_meldung = '';
	$np_entwurf = '';
	if(isset($_POST['feedback']) && time() - ($_SESSION['newspaper_feedback_time'] ?? 0) > 300){
		$_SESSION['newspaper_feedback_time']=time();
		$np_meldung = '<div class="mod-meldung mod-meldung-ok">Vielen Dank, das Feedback wurde gespeichert.</div>';
		$sendto=$GLOBALS['env_admin_email'];
		$betreff='Feedback: '.($row['betreff'] ?? '').' '.$sv_server_tag.' '.$_SESSION['ums_user_id'].' '.$_SESSION['ums_spielername'];
		$text=str_replace('\r\n',"\r\n",$_REQUEST['feedback']);
		$sendfrom='FROM: '.$GLOBALS['env_admin_email'];
		@mail($sendto, $betreff, $text, $sendfrom);
	} elseif (isset($_POST['feedback'])) {
		//zu früh: nicht gesendet, der Text bleibt im Feld stehen
		$np_meldung = '<div class="mod-meldung mod-meldung-warn">Du hast gerade erst Feedback gesendet. Weiteres Feedback ist ab '.RealTime::at($_SESSION['newspaper_feedback_time'] + 301).' m&ouml;glich.</div>';
		$np_entwurf = (string)$_POST['feedback'];
	}

	if ($np_meldung != '') {
		echo '<div class="mod pol-meldungen">'.$np_meldung.'</div>';
	}

	if (!$row) {
		rahmen_oben('News');
		echo '<div class="mod np"><div class="mod-leer">Diese Meldung gibt es nicht.</div>';
		echo '<div class="np-fuss"><a href="newspaper.php?action=archiv&amp;typ=1" class="mod-btn mod-btn-leise ally-btn-klein">Zum Archiv</a></div></div>';
		rahmen_unten();
	} else {
		$nachricht = nl2br($row['nachricht']);
		rahmen_oben($row['betreff']);
		echo '<div class="mod np">';
		echo '<div class="ov-kopf"><span class="mod-typ">'.($np_arten[$row['typ']] ?? 'Meldung').' &middot; '.np_datum($row['time']).'</span>';
		echo '<a href="newspaper.php?action=archiv&amp;typ='.$row['typ'].'" class="mod-btn mod-btn-leise ally-btn-klein">Archiv</a></div>';
		echo '<div class="np-text">'.$nachricht.'</div>';
		echo '</div>';
		rahmen_unten();

		rahmen_oben('Feedback zum Beitrag');
		echo '<form action="newspaper.php" method="POST" name="newspaper" class="mod np">';
		echo '<input type="hidden" name="id" value="'.$id.'">';
		echo '<div class="np-info">Wenn Du gerne Feedback zu diesem Beitrag geben m&ouml;chtest, so empfehlen wir daf&uuml;r das Forum.
			Solltest Du Dich aber nicht trauen &ouml;ffentlich etwas zu schreiben, dann kannst Du auch dieses Feedback-Formular nutzen. Es werden auf jeden Fall alle Feedbacks gelesen.</div>';
		echo '<div class="mod-meldung mod-meldung-warn np-wichtig">Beachte bitte, dass auf Fragen nicht geantwortet werden kann, diese kannst Du aber im Discord stellen.</div>';
		echo '<div class="np-info">Schreibe bitte m&ouml;glichst ausf&uuml;hrlich, damit man wei&szlig; was gemeint ist, ein einfaches "ist doof" wird zwar registriert, aber eine Begr&uuml;ndung fehlt. Des Weiteren werden keine Beleidigungen toleriert.</div>';
		echo '<div class="mod-hinweis np-hinweis">Meldungen zu Verst&ouml;&szlig;en gegen die Nutzungsbedingungen kannst Du im Discord melden.</div>';
		echo '<textarea name="feedback" rows="8" required placeholder="Trage hier bitte Dein Feedback ein." class="mod-eingabe np-feedback">'.htmlspecialchars($np_entwurf, ENT_QUOTES, 'UTF-8').'</textarea>';
		echo '<div class="np-fuss"><button type="submit" class="mod-btn">Feedback senden</button></div>';
		echo '</form>';
		rahmen_unten();
	}
}
else  //archiv
{
	$typ=(int)$typ;
	$sel_news=mysqli_execute_query($GLOBALS['dbi'],
	  "SELECT * FROM de_news_overview WHERE typ=? ORDER BY id DESC",
	  [$typ]);

	rahmen_oben('Archiv'.(isset($np_arten[$typ]) ? ' &middot; '.$np_arten[$typ] : ''));
	echo '<div class="mod np">';
	$anzahl = 0;
	while($rew=mysqli_fetch_assoc($sel_news))
	{
		echo '<a href="newspaper.php?id='.$rew['id'].'" class="ov-news-zeile"><span class="ov-news-datum">'.np_datum($rew['time']).'</span><span class="ov-news-betreff">'.$rew['betreff'].'</span></a>';
		$anzahl++;
	}
	if ($anzahl == 0) {
		echo '<div class="mod-leer">Hier gibt es noch keine Meldungen.</div>';
	}
	echo '<div class="np-fuss"><a href="overview.php" class="mod-btn mod-btn-leise ally-btn-klein">Zur &Uuml;bersicht</a></div>';
	echo '</div>';
	rahmen_unten();
}
?>
</body>
</html>
