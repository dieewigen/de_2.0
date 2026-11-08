<?php
//Formatierungen im Hyperfunk: je Marke ein Beispiel, wie hyperfunk.php es darstellt; öffnet aus dem Verfassen-Formular
//(src/View/Hyperfunk/ComposeForm.php) in einem neuen Tab
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_hfnlegende.lang.php';
include "functions.php";

$b = $legende_lang['eintrag_5'];
$abhier = ' ('.$legende_lang['eintrag_31'].')';
$zeilen = array(
    array('[b]'.$b.'[/b]', '<b>'.$b.'</b>', $legende_lang['eintrag_6']),
    array('[i]'.$b.'[/i]', '<i>'.$b.'</i>', $legende_lang['eintrag_8']),
    array('[u]'.$b.'[/u]', '<u>'.$b.'</u>', $legende_lang['eintrag_7']),
    array('[center]'.$b.'[/center]', '<div style="text-align: center;">'.$b.'</div>', $legende_lang['eintrag_9']),
    array('[CROT]'.$b, '<span style="color: #F10505;">'.$b.'</span>', $legende_lang['eintrag_17'].$abhier),
    array('[CGELB]'.$b, '<span style="color: #FDFB59;">'.$b.'</span>', $legende_lang['eintrag_18'].$abhier),
    array('[CGRUEN]'.$b, '<span style="color: #28FF50;">'.$b.'</span>', $legende_lang['eintrag_19'].$abhier),
    array('[CW]'.$b, '<span style="color: #FFFFFF;">'.$b.'</span>', $legende_lang['eintrag_20'].$abhier),
    array('[CDE]'.$b, '<span style="color: #3399FF;">'.$b.'</span>', $legende_lang['eintrag_29'].$abhier),
    array('[color=#33FFFF]'.$b.'[/color]', '<span style="color: #33FFFF;">'.$b.'</span>', $legende_lang['eintrag_21']),
    array('[size=4]'.$b.'[/size]', '<span style="font-size: 18px;">'.$b.'</span>', $legende_lang['eintrag_22']),
    array('[url]https://www.die-ewigen.com[/url]', '<a href="https://www.die-ewigen.com" target="_blank" rel="noopener">https://www.die-ewigen.com</a>', $legende_lang['eintrag_12']),
    array('[email]'.$legende_lang['eintrag_13'].'[/email]', '<a href="mailto:'.$legende_lang['eintrag_13'].'">'.$legende_lang['eintrag_13'].'</a>', $legende_lang['eintrag_14']),
);
?>
<!DOCTYPE html>
<html lang="de">
<head>
<title><?php echo $legende_lang['eintrag_28']; ?></title>
<?php include "cssinclude.php"; ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';
echo '<div class="hfl">';
rahmen_oben($legende_lang['eintrag_28']);
echo '<div class="mod hfl-karte"><table class="hfl-tabelle"><thead><tr><th>'.$legende_lang['eintrag_30'].'</th><th>'.$legende_lang['eintrag_3'].'</th><th>'.$legende_lang['eintrag_4'].'</th></tr></thead><tbody>';
foreach ($zeilen as $z) {
    echo '<tr><td><code class="hfl-code">'.htmlspecialchars($z[0], ENT_QUOTES, 'UTF-8').'</code></td><td>'.$z[1].'</td><td>'.$z[2].'</td></tr>';
}
echo '</tbody></table><div class="mod-hinweis">'.$legende_lang['eintrag_27'].'</div></div>';
rahmen_unten();
echo '</div></body></html>';
