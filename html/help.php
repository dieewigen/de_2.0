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

//Art einer Technologie: Einheiten anhand ihres tech_id-Bereichs, sonst der Reiter der Technologieseite (tech_typ)
function hlp_art($tech_id, $tech_typ)
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
    $arten = array(0 => 'Geb&auml;ude', 1 => 'Forschung', 2 => 'Basisschiff', 3 => 'V-Systeme');
    return $arten[$tech_typ] ?? '';
}

//eine Beschreibung mit Art, Text und Voraussetzungen (Liste fertiger Links/Chips) ausgeben
function hlp_eintrag($titel, $art, $text, $voraussetzungen)
{
    global $help_lang;
    rahmen_oben($titel);
    echo '<div class="mod hlp">';
    if ($art != '') {
        echo '<div class="mod-typ">'.$art.'</div>';
    }
    echo '<div class="hlp-text">'.$text.'</div>';
    if (count($voraussetzungen) > 0) {
        echo '<div class="hlp-abschnitt"><div class="mod-typ">Voraussetzungen</div><div class="hlp-chips">'.implode('', $voraussetzungen).'</div></div>';
    }
    echo '<div class="hlp-fuss"><a href="javascript:history.back();" class="mod-btn mod-btn-leise ally-btn-klein">'.ucfirst($help_lang['zurueck']).'</a></div>';
    echo '</div>';
    rahmen_unten();
}

function hlp_leer()
{
    global $help_lang;
    rahmen_oben($help_lang['title']);
    echo '<div class="mod hlp"><div class="mod-leer">Zu diesem Eintrag gibt es keine Beschreibung.</div></div>';
    rahmen_unten();
}

//Technologien: Namen und Beschreibungen stehen in de_tech_data je Rasse durch ";" getrennt, wie auf der Technologieseite
//(früher las diese Seite die alten Tabellen de_tech_data1-4, deren Voraussetzungen nicht mehr stimmten)
if (isset($_GET["t"])) {
    $t = intval($_GET["t"]);
    $db_daten = mysqli_execute_query($GLOBALS['dbi'],
      "SELECT tech_name, tech_desc, tech_vor, tech_typ FROM de_tech_data WHERE tech_id=?",
      [$t]);
    $row = mysqli_fetch_assoc($db_daten);

    if ($row) {
        //Voraussetzungen: Technologien (T…) als Link auf deren Beschreibung, im Hardcore-Modus auch EH-Teilsiege (B1x…)
        $voraussetzungen = array();
        foreach (explode(';', (string)$row['tech_vor']) as $vor) {
            if ($vor === '') {
                continue;
            }
            if ($vor[0] == 'T') {
                $vor_id = (int)substr($vor, 1);
                $db_vor = mysqli_execute_query($GLOBALS['dbi'], "SELECT tech_name FROM de_tech_data WHERE tech_id=?", [$vor_id]);
                $row_vor = mysqli_fetch_assoc($db_vor);
                if ($row_vor) {
                    $voraussetzungen[] = '<a href="help.php?t='.$vor_id.'" class="mod-chip hlp-chip">'.getTechNameByRasse($row_vor['tech_name'], $_SESSION['ums_rasse']).'</a>';
                }
            } elseif ($vor[0] == 'B' && ($vor[1] ?? '') == '1' && $sv_hardcore == 1) {
                $parts = explode('x', $vor);
                $voraussetzungen[] = '<span class="mod-chip hlp-chip">'.(int)($parts[1] ?? 0).' EH-Teilsieg(e)</span>';
            }
        }
        $beschreibungen = explode(';', (string)$row['tech_desc']);
        hlp_eintrag(getTechNameByRasse($row["tech_name"], $_SESSION['ums_rasse']), hlp_art($t, (int)$row['tech_typ']),
            nl2br(trim($beschreibungen[$_SESSION['ums_rasse'] - 1] ?? '')), $voraussetzungen);
    } else {
        hlp_leer();
    }
}

//Sektorgebäude: stehen weiterhin in de_tech_data1 (IDs 120-129, für alle Rassen gleich), wie im SK-Bau (bkmenu.php);
//in de_tech_data sind dieselben IDs Forschungszentren und Raumwerft, darum ein eigener Parameter
if (isset($_GET["s"])) {
    $s = intval($_GET["s"]);
    $db_daten = mysqli_execute_query($GLOBALS['dbi'],
      "SELECT tech_name, des, tech_vor FROM de_tech_data1 WHERE tech_id=? AND tech_id>119 AND tech_id<130",
      [$s]);
    $row = mysqli_fetch_assoc($db_daten);

    if ($row) {
        $voraussetzungen = array();
        foreach (explode(';', (string)$row['tech_vor']) as $vor) {
            $vor = (int)$vor;
            if ($vor > 119 && $vor < 130) {
                $db_vor = mysqli_execute_query($GLOBALS['dbi'], "SELECT tech_name FROM de_tech_data1 WHERE tech_id=?", [$vor]);
                $row_vor = mysqli_fetch_assoc($db_vor);
                if ($row_vor) {
                    $voraussetzungen[] = '<a href="help.php?s='.$vor.'" class="mod-chip hlp-chip">'.$row_vor['tech_name'].'</a>';
                }
            }
        }
        hlp_eintrag($row['tech_name'], 'Sektorgeb&auml;ude', $row['des'], $voraussetzungen);
    } else {
        hlp_leer();
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
