<?php
//m�gliche aufgaben
// kollektoren von einer bestimmten allianz erobern
// angriff mit artefakt-unterst�tzung
// kollektoren von einem bestimmten spieler holen
// allianzbonus abholen
// artefakte im artefaktzentrum
// kopfgeld erbeuten
// tronic in die allianzkasse einzahlen
//aufgabenzeit: 480 WT, wenn nicht geschafft, kommt eine neue aufgabe
//wenn eine aufgabe erf�llt wurde, dann kommt direkt eine neue aufgabe

//f�r dynamische Aufgaben den maximalen Tick auslesen
//maximalen tick auslesen
$result_allyjobs  = mysql_query("SELECT MAX(tick) AS tick FROM de_user_data",$db);
$row_allyjobs     = mysql_fetch_array($result_allyjobs);
$maxtick_allyjobs = $row_allyjobs["tick"];

unset($allyjobs);
$index=0;
$allyjobs[$index][0]='Erteilt den Bauauftrag f&uuml;r Kollektoren.';//beschreibung
$allyjobs[$index][1]=0;//questreach
$allyjobs[$index][2]=500;//questgoal
$allyjobs[$index][3]=480;//questtime dauer in WT
$allyjobs[$index][4]=100;//questpoints
$index=1;
$allyjobs[$index][0]='Erobert Kollektoren von anderen Spielern.';//beschreibung
$allyjobs[$index][1]=0;//questreach
//$allyjobs[$index][2]=100;//questgoal
$allyjobs[$index][2]=round(200+$maxtick_allyjobs/60);//questgoal
$allyjobs[$index][3]=480;//questtime dauer in WT
$allyjobs[$index][4]=100;//questpoints
$index=2;
$allyjobs[$index][0]='Erh&ouml;ht Eure Punktezahl.';//beschreibung
$allyjobs[$index][1]=0;//questreach
$allyjobs[$index][2]=2;//questgoal
$allyjobs[$index][3]=480;//questtime dauer in WT
$allyjobs[$index][4]=100;//questpoints
$index=3;
$allyjobs[$index][0]='Erobert Kollektoren von den DX61a23 aus den Protektorats-Sektoren.';//beschreibung
$allyjobs[$index][1]=0;//questreach
$allyjobs[$index][2]=100;//questgoal
$allyjobs[$index][3]=480;//questtime dauer in WT
$allyjobs[$index][4]=100;//questpoints
$index=4;
$allyjobs[$index][0]='Startet Missionen unter dem Men&uuml;punkt Missionen.';//beschreibung
$allyjobs[$index][1]=0;//questreach
$allyjobs[$index][2]=350;//questgoal
$allyjobs[$index][3]=480;//questtime dauer in WT
$allyjobs[$index][4]=100;//questpoints
$index=5;
$allyjobs[$index][0]='Erhaltet Kriegsartefakte durch K&auml;mpfe.';//beschreibung
$allyjobs[$index][1]=0;//questreach
$allyjobs[$index][2]=round(sqrt($maxtick_allyjobs));//questgoal
$allyjobs[$index][3]=480;//questtime dauer in WT
$allyjobs[$index][4]=100;//questpoints
$index=6;
$allyjobs[$index][0]='F&uuml;hrt Artefaktverschmelzungen und Artefaktupgrades durch.';//beschreibung
$allyjobs[$index][1]=0;//questreach
$allyjobs[$index][2]=100;//questgoal
$allyjobs[$index][3]=480;//questtime dauer in WT
$allyjobs[$index][4]=100;//questpoints

?>