<?php

////////////////////////////////////////////////////////////
//Startwelt DER EINGANG
////////////////////////////////////////////////////////////

//Description
$content .= '<div class="ms-sonder-text">Dies ist die erste erreichbare Welt der vergessenen Systeme, wir nennen sie daher DER EINGANG, da sie uns den Zugriff auf viele weitere Systeme erm&ouml;glicht.</div>';
$content .= vs_flash_html();

//Gebäudedaten laden
$playerBldg = loadPlayerBuildings($_SESSION['ums_user_id'], $this->system_id);

//je Anlage eine Zeile: aktiv oder Knopf zum Aktivieren
$anlage = function ($name, $aktiv, $link) {
    return '<div class="ms-anlage"><span>'.$name.'</span>'.($aktiv ? '<span class="mod-chip mod-chip-gruen">aktiv</span>' : '<a href="'.$link.'" class="mod-btn ally-btn-klein">Aktivieren</a>').'</div>';
};

//gibt es schon einen Außenposten?
$fd = getBldgByFieldID($playerBldg, 0);
if ($fd['bldg_id'] == -1) {//Außenposten noch nicht vorhanden
    $fid = intval($_REQUEST['fid'] ?? 0);

    if (($_REQUEST['action'] ?? '') === 'activate') {
        if ($fid == 0 && $fd['bldg_id'] == -1) {
            //Gebäude in die DB packen, danach neu laden (PRG), dann zeigt die Seite die Minen
            setBldgByFieldID($_SESSION['ums_user_id'], $this->system_id, 0, 0, 1, 0);
            vs_flash_set(true, 'Der Au&szlig;enposten wurde aktiviert.');
            vs_redirect($this->system_id);
        }
    }

    $content .= '<div class="ms-abschnitt"><span class="mod-typ">Au&szlig;enposten</span>';
    $content .= '<p>Es gibt hier einen stillgelegten Au&szlig;enposten. Aktiviere ihn, um einen St&uuml;tzpunkt f&uuml;r weitere Aktionen zu haben.</p>';
    $content .= $anlage('Au&szlig;enposten', false, '?id='.$this->system_id.'&action=activate&fid=0');
    $content .= '</div>';

} else {//Minen

    $fid = intval($_REQUEST['fid'] ?? -1);
    $zeilen = '';

    //kostenlos: Eisen-Mine deaktiviert x 1
    for ($f = 1;$f <= 4;$f++) {
        $fd = getBldgByFieldID($playerBldg, $f);

        if (($_REQUEST['action'] ?? '') === 'activate') {
            if ($fid == $f && $fd['bldg_id'] == -1) {
                //Gebäude in die DB packen
                setBldgByFieldID($_SESSION['ums_user_id'], $this->system_id, $fid, 3, 1, 0);
                vs_flash_set(true, 'Die Eisen-Mine wurde aktiviert.');
                vs_redirect($this->system_id);
            }
        }

        $zeilen .= $anlage('Eisen-Mine', $fd['bldg_id'] != -1, '?id='.$this->system_id.'&action=activate&fid='.$f);
    }

    //kostenlos: Omega-Fabrik deaktiviert x 1
    for ($f = 5;$f <= 7;$f++) {
        $fd = getBldgByFieldID($playerBldg, $f);

        if (($_REQUEST['action'] ?? '') === 'activate') {
            if ($fid == $f && $fd['bldg_id'] == -1) {
                //Gebäude in die DB packen
                setBldgByFieldID($_SESSION['ums_user_id'], $this->system_id, $fid, 13, 10, 0);
                vs_flash_set(true, 'Die Omega-Fabrik wurde aktiviert.');
                vs_redirect($this->system_id);
            }
        }

        $zeilen .= $anlage('Omega-Fabrik', $fd['bldg_id'] != -1, '?id='.$this->system_id.'&action=activate&fid='.$f);
    }

    $content .= '<div class="ms-abschnitt"><span class="mod-typ">Minen und Fabriken</span>';
    $content .= '<p>Es gibt hier stillgelegte Minen und Fabriken. Aktiviere sie, um daraus Nutzen zu ziehen.</p>';
    $content .= $zeilen;
    $content .= '</div>';
}
