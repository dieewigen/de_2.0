<?php
//Sektor 666 erwacht: Server-Boss für die Sektorflotten (src/Model/Sektor666/Sektor666Service.php)
//Platzhalter in geschweiften Klammern werden ersetzt; Zeiten nur in WT/KT

$s666_lang['titel']='Sektor 666';
$s666_lang['name']='die Schläfer';

$s666_lang['kulisse']='In Sektor 666 regten sich vor Äonen die größten Übel der Zeit. Heute schlafen sie und man kann nur hoffen, dass sie nie wieder erwachen.';
$s666_lang['geschichte_wach']='Die Schläfer sind erwacht. In Sektor 666 hat sich eine Macht aus der Zeit vor den Erbauern erhoben. Sie greift niemanden an, doch keine Rasse kann sie allein bezwingen. Nur wenn die Sektoren gemeinsam ihre Flotten schicken, sinken die Schläfer zurück in ihren Schlaf.';

//Zustand
$s666_lang['kachel_stufe']='Stufe';
$s666_lang['kachel_huelle']='Hülle';
$s666_lang['kachel_eigener']='Dein Sektor';
$s666_lang['kachel_eigener_platz']='Platz {PLATZ} &middot; {ANTEIL} %';
$s666_lang['kachel_eigener_keiner']='noch kein Angriff';
$s666_lang['balken']='{HP} von {HPMAX}';

$s666_lang['anleitung_titel']='So greift Dein Sektor an';
$s666_lang['anleitung']='Der Sektorkommandant schickt unter <a href="bkmenu.php">SK-Bau/Flotte</a> die Sektorflotte mit dem Befehl „Angreifen“ nach Sektor 666, die Reisezeit beträgt {RZ} KT. Jedes ankommende Sektorschiff richtet 1 Schaden an, das Abwehrfeuer vernichtet {ABWEHR} % der angreifenden Schiffe. Die übrigen kehren zurück und können erneut angreifen.';

$s666_lang['beute_titel']='Beute';
$s666_lang['beute_regel']='Ist die Hülle zerstört, bekommt jedes Mitglied eines Sektors mit mindestens {MINANTEIL} % des Schadens Beute, sofern es schon beim Erwachen in diesem Sektor war. Die drei Sektoren mit dem meisten Schaden bekommen zusätzlich einen Bonus. Außerdem erhält jeder Sektor {AUSGLEICH} % der Kosten seiner verlorenen Sektorschiffe zurück ins Sektorlager.';
$s666_lang['beute_grund']='alle ab {MINANTEIL} %';
$s666_lang['beute_platz']='zusätzlich Platz {PLATZ}';
$s666_lang['beute_artefakt_hinweis']='Artefakte kommen per Zufall, auch ein Sekkollus ist möglich. Ohne freien Artefaktplatz gibt es stattdessen einen Kriegsartefakt.';
$s666_lang['eigene_dabei']='Du warst beim Erwachen in Sektor {SEK} und bekommst Beute, wenn Dein Sektor mindestens {MINANTEIL} % des Schadens anrichtet.';
$s666_lang['eigene_nicht_dabei']='Du warst beim Erwachen nicht in Deinem jetzigen Sektor und bekommst bei diesem Erwachen keine Beute.';

$s666_lang['rang_titel']='Schaden der Sektoren';
$s666_lang['rang_platz']='Platz';
$s666_lang['rang_sektor']='Sektor';
$s666_lang['rang_schaden']='Schaden';
$s666_lang['rang_anteil']='Anteil';
$s666_lang['rang_leer']='Noch hat kein Sektor angegriffen.';

$s666_lang['schlaf_erwacht_in']='Sie erwachen wieder in etwa {WT} WT, stärker als zuvor.';
$s666_lang['schlaf_erstes_mal']='Etwa in {WT} WT könnten sie erwachen.';
$s666_lang['schlaf_runde_vorbei']='In dieser Runde erwachen sie nicht mehr.';
$s666_lang['verlauf_titel']='Verlauf dieser Runde';

//Verlauf (je Zeile, neueste zuerst)
$s666_lang['verlauf_erwacht']='WT {WT}: Stufe {STUFE} erwacht, Hülle {HUELLE}';
$s666_lang['verlauf_besiegt']='WT {WT}: Stufe {STUFE} besiegt, den meisten Schaden machte Sektor {SEK}';
$s666_lang['verlauf_eingeschlafen']='WT {WT}: Stufe {STUFE} unbesiegt eingeschlafen';

//Übersicht und SK-Menü
$s666_lang['ov_zeile']='<b><a href="sector.php?sf=666">Sektor 666 ist erwacht</a></b> (Stufe {STUFE}): Hülle {PCT} %';
$s666_lang['ov_eigener']=', Dein Sektor Platz {PLATZ} ({ANTEIL} %)';
$s666_lang['bk_hinweis']='<b>Sektor 666 ist erwacht</b> (Stufe {STUFE}, Hülle {PCT} %). Greife mit Zielsektor 666 an, die Reisezeit beträgt {RZ} KT. <a href="sector.php?sf=666">Mehr zu Sektor 666</a>';
$s666_lang['fehler_verteidigen']='Sektor 666 kann nicht verteidigt werden, solange die Schläfer wach sind.';

//Meldungen
$s666_lang['chat_erwacht']='<b>Sektor 666 ist erwacht!</b> Die Schläfer (Stufe {STUFE}) haben eine Hülle von {HUELLE}. Schickt Eure Sektorflotten, die Beute geht an alle beteiligten Sektoren.';
$s666_lang['chat_schwelle']='Sektor 666: Die Hülle der Schläfer ist auf {PCT} % gefallen.';
$s666_lang['chat_besiegt']='<b>Sektor 666 ist besiegt!</b> Die Schläfer (Stufe {STUFE}) sinken zurück in den Schlaf. Den meisten Schaden machten: {TOP}. Sie werden stärker zurückkehren.';
$s666_lang['chat_eingeschlafen']='Sektor 666: Die Schläfer (Stufe {STUFE}) sind unbesiegt wieder eingeschlafen, ihre Hülle stand noch bei {PCT} %.';
$s666_lang['sektorchat_angriff']='Unsere Sektorflotte hat die Schläfer in Sektor 666 angegriffen: {SCHADEN} Schaden, {VERLUSTE} Schiffe verloren. Ihre Hülle steht noch bei {PCT} %.';
$s666_lang['top_eintrag']='Sektor {SEK} ({ANTEIL} %)';

$s666_lang['news_erwacht']='<b>Sektor 666 ist erwacht!</b><br><br>Die Schläfer (Stufe {STUFE}) haben eine Hülle von {HUELLE}. Sie greifen niemanden an, doch nur gemeinsam lassen sie sich bezwingen: Dein Sektorkommandant kann die Sektorflotte nach Sektor 666 schicken. Ist die Hülle zerstört, bekommen die Mitglieder aller beteiligten Sektoren Beute.<br><br>Mehr dazu unter <a href="sector.php?sf=666">Sektor 666</a>.';
$s666_lang['news_beute']='<b>Die Schläfer sind besiegt!</b><br><br>Dein Sektor {SEK} hat {ANTEIL} % des Schadens angerichtet und belegt Platz {PLATZ}. Deine Beute:{LISTE}';
$s666_lang['news_ausgleich']='<br><br>Das Sektorlager erhält als Ausgleich für {VERLUSTE} verlorene Sektorschiffe: {RES}.';
$s666_lang['bericht']='-- Angriff auf Sektor 666 --<br>Eingesetzte Sektorschiffe: {SCHIFFE}<br>Schaden an den Schläfern: {SCHADEN}<br>Durch Abwehrfeuer verloren: {VERLUSTE}<br>Hülle der Schläfer: {HP} von {HPMAX} ({PCT} %)';
$s666_lang['bericht_besiegt']='<br><br><b>Die Schläfer sind besiegt.</b>';

//Beute-Posten
$s666_lang['posten_tronic']='{N} Tronic';
$s666_lang['posten_kerne']='{N} Titanen-Energiekerne';
$s666_lang['posten_palenium']='{N} Palenium';
$s666_lang['posten_kriegsartefakte']='{N} Kriegsartefakt(e)';
$s666_lang['posten_artefakt']='1 {NAME}-Artefakt';
$s666_lang['posten_artefakte']='{N} Artefakt(e)';
$s666_lang['posten_ersatz']='{N} Kriegsartefakt(e) statt Artefakten (kein freier Platz)';
