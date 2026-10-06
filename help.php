<?php
include "inc/header.inc.php";
include "inc/artefakt.inc.php";
include "inc/lang/".$sv_server_lang."_help.lang.php";

$db_daten = mysqli_execute_query($GLOBALS['dbi'],
  "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, `system`, newtrans FROM de_user_data WHERE user_id=?",
  [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01 = $row['restyp01'];
$restyp02 = $row['restyp02'];
$restyp03 = $row['restyp03'];
$restyp04 = $row['restyp04'];
$restyp05 = $row['restyp05'];
$punkte = $row["score"];
$newtrans = $row["newtrans"];
$sector = $row["sector"];
$system = $row["system"];

include "functions.php";
?>
<!doctype html>
<html>
<head>
<title><?php echo $help_lang['title']?></title>
<?php include "cssinclude.php"; ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';
include "resline.php";

//Art einer Technologie anhand der bekannten Bereiche der tech_id
function hlp_art($tech_id)
{
    if ($tech_id == 80) {
        return 'Kollektor';
    }
    if ($tech_id >= 81 && $tech_id <= 99) {
        return 'Raumschiff';
    }
    if ($tech_id >= 100 && $tech_id <= 109) {
        return 'Verteidigungsanlage';
    }
    if ($tech_id == 110) {
        return 'Sonde';
    }
    if ($tech_id == 111) {
        return 'Agent';
    }
    if ($tech_id >= 120 && $tech_id <= 129) {
        return 'Sektorgeb&auml;ude';
    }
    return '';
}

if (isset($_GET["t"])) {
    $t = intval($_GET["t"]);
    $db_daten = mysqli_execute_query($GLOBALS['dbi'],
      "SELECT tech_name, des, tech_vor FROM de_tech_data".$_SESSION['ums_rasse']." WHERE tech_id=?",
      [$t]);
    $row = mysqli_fetch_assoc($db_daten);

    if ($row) {
        rahmen_oben($row["tech_name"]);
        echo '<div class="mod hlp">';
        $art = hlp_art($t);
        if ($art != '') {
            echo '<div class="mod-typ">'.$art.'</div>';
        }
        echo '<div class="hlp-text">'.$row["des"].'</div>';

        //Voraussetzungen, je als Link auf deren Beschreibung
        $voraussetzungen = array();
        foreach (explode(';', (string)$row['tech_vor']) as $vor) {
            $vor = (int)$vor;
            if ($vor > 0) {
                $db_vor = mysqli_execute_query($GLOBALS['dbi'], "SELECT tech_name FROM de_tech_data".$_SESSION['ums_rasse']." WHERE tech_id=?", [$vor]);
                $row_vor = mysqli_fetch_assoc($db_vor);
                if ($row_vor) {
                    $voraussetzungen[] = '<a href="help.php?t='.$vor.'" class="mod-chip hlp-chip">'.$row_vor['tech_name'].'</a>';
                }
            }
        }
        if (count($voraussetzungen) > 0) {
            echo '<div class="hlp-abschnitt"><div class="mod-typ">Voraussetzungen</div><div class="hlp-chips">'.implode('', $voraussetzungen).'</div></div>';
        }

        echo '<div class="hlp-fuss"><a href="javascript:history.back();" class="mod-btn mod-btn-leise ally-btn-klein">'.ucfirst($help_lang['zurueck']).'</a></div>';
        echo '</div>';
        rahmen_unten();
    } else {
        rahmen_oben($help_lang['title']);
        echo '<div class="mod hlp"><div class="mod-leer">Zu diesem Eintrag gibt es keine Beschreibung.</div></div>';
        rahmen_unten();
    }
}

if (!empty($_GET["a"])) {
    $a = (int)$_GET["a"];
    $artresult = mysqli_execute_query($GLOBALS['dbi'],
      "SELECT id, artname, artdesc, color FROM de_artefakt ORDER by id");

    rahmen_oben('Artefakte');
    echo '<div class="mod hlp">';
    while ($row = mysqli_fetch_assoc($artresult)) {

        $desc = $row["artdesc"];
        $desc = str_replace("{WERT1}", number_format($sv_artefakt[$row["id"] - 1][0], 2, ",", "."), $desc);
        $desc = str_replace("{WERT2}", number_format($sv_artefakt[$row["id"] - 1][1], 0, "", "."), $desc);
        $desc = str_replace("{WERT3}", number_format($sv_artefakt[$row["id"] - 1][2], 0, "", "."), $desc);
        $desc = str_replace("{WERT4}", number_format($sv_artefakt[$row["id"] - 1][3], 0, "", "."), $desc);
        $desc = str_replace("{WERT5}", number_format($sv_artefakt[$row["id"] - 1][4], 0, "", "."), $desc);
        $desc = str_replace("{WERT6}", number_format($sv_artefakt[$row["id"] - 1][5], 2, ",", "."), $desc);

        //Artefaktfarbe nur als Randmarkierung und Punkt, der Text selbst hell
        $farbe = preg_match('/^[0-9a-fA-F]{6}$/', $row["color"]) ? $row["color"] : '777777';

        echo '<div class="hlp-art" id="art'.$row["id"].'" style="border-left-color: #'.$farbe.';">';
        echo '<div class="hlp-art-name"><i style="background: #'.$farbe.';"></i>'.$row["artname"].'</div>';
        echo '<div class="hlp-text">'.$desc.'</div>';
        echo '</div>';
    }
    echo '</div>';
    rahmen_unten();
}
?>

</body>
</html>
