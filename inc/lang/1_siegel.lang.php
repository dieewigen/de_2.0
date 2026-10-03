<?php
//Das Siegel von Basranur: gemeinsames Serverprojekt in den Vergessenen Systemen
//Platzhalter in geschweiften Klammern werden ersetzt, Spielernamen werden vorher escaped

$siegel_lang['systemname']='Siegel von Basranur';

$siegel_lang['geschichte']='Jahrtausendelang lag dieses System hinter einem undurchdringlichen Schutzschirm. Nun ist der Schirm gefallen, und dahinter liegt das Siegel von Basranur: eine Anlage der Erbauer, die einst die Energie der Planeten verstärkte. Die DX61a23 hatten sie zu einer Waffe umgebaut, deren Schockwellen Kollektoren zerstörten. Ihre Eingriffe sind verblasst, doch die Anlage ruht.<br><br>Um sie wieder in Gang zu setzen, braucht sie Resonanzkristalle als Stützmasse. Keine Rasse kann sie allein aufladen: Je mehr Kommandanten ihren Anteil beitragen, desto stärker wirkt das Siegel auf die Planeten aller Systeme.';

//Freischaltung
$siegel_lang['verbindung_frage']='Die Anlage reagiert auf die Annäherung Deiner Sonden. Soll eine Verbindung zum Siegel hergestellt werden?';
$siegel_lang['verbindung_link']='Verbindung zum Siegel herstellen';
$siegel_lang['verbindung_ok']='Die Verbindung zum Siegel steht. Weiter.';
$siegel_lang['mission_hinweis']='Unter <a href="missions.php" style="font-size: inherit;">Missionen</a> steht der Agenteneinsatz (BASRANUR) zur Verfügung. Deine Agenten bergen dabei Resonanzkristalle aus den Ruinen rund um das Siegel.';
$siegel_lang['kein_zugang_npc']='Das Siegel reagiert nicht auf Dich.';

//Status
$siegel_lang['titel_status']='Zustand des Siegels';
$siegel_lang['status_aktiv']='Aktive Stufe: <b>{LEVEL}</b>, das sind <b>+{PCT} % planetarer Grundertrag</b> für alle Kommandanten. Die Periode endet in {WT} Wirtschaftsticks.';
$siegel_lang['status_ruht']='Das Siegel ruht derzeit (Stufe 0). Die Periode endet in {WT} Wirtschaftsticks.';
$siegel_lang['aufladung']='Aufladung dieser Periode: <b>{N} Mitwirkende</b>, das ergibt Stufe {NEXT} (+{NEXTPCT} %) für die nächste Periode.';
$siegel_lang['noch_bis']='Noch {MISSING} Mitwirkende bis Stufe {STEP}.';
$siegel_lang['max_erreicht']='Die höchste Stufe ist erreicht.';
$siegel_lang['regel']='Wer in einer Periode {SHARE} Resonanzkristalle einsetzt, zählt als Mitwirkender. Je {STEP} Mitwirkende ergeben eine Stufe, jede Stufe bringt allen Kommandanten in der nächsten Periode +{PCT} % planetaren Grundertrag.';
$siegel_lang['balken']='{N} Mitwirkende, Stufe {NEXT}';

//Einsetzen
$siegel_lang['titel_einsetzen']='Resonanzkristalle einsetzen';
$siegel_lang['lager']='Resonanzkristalle im Lager: {STOCK}';
$siegel_lang['eigener_beitrag']='Dein Beitrag in dieser Periode: {OWN} von {SHARE} Resonanzkristallen.';
$siegel_lang['mitwirkender']='Du bist Mitwirkender dieser Periode.';
$siegel_lang['button']='Einsetzen';
$siegel_lang['max']='Max';
$siegel_lang['erfolg']='Du hast {AMOUNT} Resonanzkristall(e) in das Siegel eingesetzt.';
$siegel_lang['fehler_gesperrt']='Das Siegel nimmt gerade keine Kristalle an. Versuche es nach dem nächsten Wirtschaftstick erneut.';
$siegel_lang['fehler_sektor1']='Aus Sektor 1 heraus ist kein Zugriff auf das Siegel möglich.';
$siegel_lang['fehler_verbindung']='Zuerst muss eine Verbindung zum Siegel hergestellt werden.';
$siegel_lang['fehler_anteil']='Du hast Deinen Anteil für diese Periode bereits eingesetzt.';
$siegel_lang['fehler_lager']='Du hast keine Resonanzkristalle.';
$siegel_lang['fehler_menge']='Bitte gib eine gültige Menge an.';
$siegel_lang['fehler_token']='Die Anfrage war ungültig, bitte versuche es erneut.';

//Listen
$siegel_lang['titel_mitwirkende']='Mitwirkende dieser Periode';
$siegel_lang['keine_mitwirkenden']='Bisher hat noch niemand seinen Anteil eingesetzt.';
$siegel_lang['titel_verlauf']='Bisherige Perioden dieser Runde';
$siegel_lang['verlauf_zeile']='{DATE} (WT {WT}): {N} Mitwirkende, Stufe {LEVEL}';

//Serverchat
$siegel_lang['chat_periode']='Das Siegel von Basranur wurde von {N} Kommandanten aufgeladen: Stufe {LEVEL}, +{PCT} % planetarer Grundertrag für die nächsten {DAUER} Wirtschaftsticks.';
$siegel_lang['chat_periode_null']='Das Siegel von Basranur wurde nicht ausreichend aufgeladen ({N} Mitwirkende) und ruht für die nächsten {DAUER} Wirtschaftsticks.';
$siegel_lang['chat_stufe']='Durch den Beitrag von {NAME} erreicht das Siegel von Basranur Stufe {LEVEL} für die nächste Periode.';

//Übersicht und Ertrag
$siegel_lang['ov_titel']='Siegel von Basranur';
$siegel_lang['ov_zeile']='Stufe {LEVEL} (+{PCT} % planetarer Grundertrag), Aufladung: {N} Mitwirkende';
$siegel_lang['ov_noch_bis']=', noch {MISSING} bis Stufe {STEP}';
$siegel_lang['ov_hinweis']='Das Siegel liegt in den Vergessenen Systemen.';
$siegel_lang['resource_tooltip']='Siegel von Basranur (+{PCT} %)';
