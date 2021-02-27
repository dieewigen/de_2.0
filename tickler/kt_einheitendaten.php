<?php
//Datum: 24.07.2006

/*
Changelog:
27.06.06  - Einfuegung der Zielauswahl ($zielmatrix)
23.07.06  - Entfernung der Zielmatrix
          - Neuberechnung der Laehmmatrix
            Starke Tuerme sind frueher geblockt
          - Neuberechnung und Vereinheitlichung der Kampfmatrix
            2 Ziele 100%, 2 Ziele 80%, 2 Ziele 60%, Rest 30%
            Bei Deffies jeweils nur ein Ziel, %Werte gleich
          - Neuberechnung der Jagdbootunterstuetzung auf Verhaeltnis der Kosten
            Multiplikatoren Rassen: 1, 1.1, 0,8, 1,2, 1,1
            Multiplikatoren Schiffe: 1, 1, 1.5
24.07.06  - Neuberechnung der Supportwerte bei Jaegern (allgemein gesenkt)
          - Formeln und Boniliste eingefuegt.
          - Neuberechnung der Werte
30.07.06  - Ueberarbeitung der Laehmwerte
            Erneute Aufteilung auf Feuerkraft und Laehmkraft
            Verhaeltnisse werden bei den Schiffen angegeben
08.08.06  - Formelaenderung der Laehmwerte (verdoppelt)
          - Panzerungsmalus der K auf -20% erhoeht
          - Feuerkraftmalus der Z auf -10% halbiert
          

Werte fuer Rassen:
Ewige:
Alle Multiplikatoren auf 1.

Ishtar:
+10% Support fuer Jagdboote
+10% mehr Laehmkraft
-20% Feuerkraft
+10% Turmpanzerung
+20% TurmFeuerkraft

K`Tharr:
+30% Feuerkraft
-20% Laehmkraft
-20% Support fuer Jagdboote
-20% Panzerung

Z'Tah-ara:
-10% Feuerkraft
+20% Panzerung
+20% Support fuer Jagdboote


Formeln:

Panzerung = Punkte / 10
Feuerkraft = Punkte / 40
Laehmkraft = Punkte / 40
Panzerung Tuerme = Punkte * 1,2 / 10

Schiffsmodifikatoren:
Bomber: Feuerkraft +50%
Traegerschiffe: Feuerkraft -50%



Merke wegen DX
81 Xinth-Xc 600 500 0 0 0 7 160 13;40;45;55;60 platzhalter 
82 Hunm-oc 3250 1500 0 0 0 15 625 13;41;45;46;56;61 platzhalter 
83 Ez-maC 12000 4000 1800 750 0 31 2840 13;42;47;51;57;62 platzhalter 
84 Zao-tuX 34000 7000 750 1250 0 62 5525 13;43;48;52;58;63;68 KAPAZIT�T BETR�GT 40 J�GER! 
85 Lor-ReX 55000 25000 3000 2500 2 93 14400 13;44;49;53;54;59;70;64;69 KAPAZIT�T BETR�GT 120 J�GER! 
86 Xor-L2R 750 750 0 0 0 9 225 13;41;53;56;61 platzhalter 
87 Os-mTz 2000 1000 0 0 0 11 400 13;23;56;61;65 platzhalter 
88 Bi-SoX 58000 27000 6000 4000 2 78 16600 13;44;45;59;64;69 KAPAZIT�T BETR�GT 400 J�GER! 

*/

//anzahl der schiffe/t�rme
$sv_anz_schiffe=10;
$sv_anz_tuerme=5;
$sv_anz_rassen=5;

//hier wird festgelegt in welche reihenfolge die schiffe den gegner blocken und wie stark die wirkung ist
//0  Hornisse $blockmatrix[j�ger] = array (j�ger,klassenprozenabz�ge,   jagdboote,klassenprozentabz�ge,
$blockmatrix[] = array (0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0);
//1  Jagdboot - Verlauf: 100 - 80 - 60 - 90 - 90 - 90 - 60 - 60 - 60 - 60 - 40 - 40 - 40
$blockmatrix[] = array (3,0,   4,20,  9,0,  2,25, 7,-50,  14,0,  13,0,   1,33,   5,0,  12,0,  11,0,  10,33,  6,0,   0,0,   8,0);
//2  Zerst�rer
$blockmatrix[] = array (0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0);
//3 Kreuzer
$blockmatrix[] = array (0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0);
//4 Schlachtschiff
$blockmatrix[] = array (0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0);
//5 Bomber - Verlauf: 100 - 100 - 100 - 100 - 20 - 20 - 20 - 5 - 5 - 5 - 5 - 5 - 5
$blockmatrix[] = array (14,0,  13,0, 12,0,  11,0,   3,80,   4,0,  9,0,   7,0,   2,75,  1,0,   0,0,  10,0,   5,0,   6,0,   8,0);
//6 Transmitterschiff
$blockmatrix[] = array (0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0);
//7 Tr�ger
$blockmatrix[] = array (0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0);
//8 Frachter
$blockmatrix[] = array (3,0,   9,0,   4,20, 2,25,  7,-50, 14,0,  13,0,  1,33,   5,0,  12,0,  11,0, 10,33,   0,0,   8,0,   6,0);
//9 Titan
$blockmatrix[] = array (3,0,   4,20, 2,25, 7,0,   9,-50,  14,50,  13,0,  1,33,  5,0,  12,0,  11,0,  10,33,  6,0,   0,0,   8,0);
//10 Brechergarnison
$blockmatrix[] = array (0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0);
//11 Balistenturm
$blockmatrix[] = array (0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0);
//12 Laserlanzenturm
$blockmatrix[] = array (0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0);
//13 Bolzenkanonenturm
$blockmatrix[] = array (0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0,   0,0);
//14 Plasmaturm - Verlauf: 100 - 100 - 50 - 50 - 25 - 25 - 25 - 25
$blockmatrix[] = array (9,0,   4,0,   3,0,  7,50,   2,0,   1,50,   5,0,   0,0,   8,0,   6,0,   0,0,   0,0,   0,0,   0,0,   0,0);


//0  Hornisse Verlauf: 100 - 100 - 80 - 80 - 60 - 60 - 30 - 30 - 30 - 30 - 30 - 30 - 30
$kampfmatrix[] = array ( 0,0,   5,0, 10,20,  3,0,  9,0,  4,25,  7,0,  2,50,  1,0,  11,0, 12,0, 13,0, 14,0,   8,0,   6,0);
//1  Guillotine Verlauf: 100 - 100 - 80 - 80 - 60 - 60 - 30 - 30 - 30 - 30 - 30 - 30 - 30
$kampfmatrix[] = array ( 0,0,  9,0,  10,0,  5,20,  11,0, 1,25, 13,0, 2,50,  3,0,  4,0,  7,0, 12,0, 14,0,  8,0,  6,0);
//2  Schakal Verlauf: 100 - 100 - 80 - 80 - 60 - 60 - 30 - 30 - 30 - 30 - 30 - 30 - 30
$kampfmatrix[] = array ( 1,0,   5,0,  7,20,  2,0,  9,0,  4,25, 14,0,   0,50,  10,0,  3,0,  11,0, 12,0, 13,0,  8,0,  6,0);
//3  Marauder Verlauf: 100 - 100 - 80 - 80 - 60 - 60 - 30 - 30 - 30 - 30 - 30 - 30 - 30
$kampfmatrix[] = array ( 2,0,   3,0, 11,20,  4,0,  9,0,  7,25, 12,0,  1,50,  0,0,  10,0,  5,0, 13,0, 14,0,  8,0,  6,0);
//4  Zerberus Verlauf: 100 - 100 - 80 - 80 - 60 - 60 - 30 - 30 - 30 - 30 - 30 - 30 - 30
$kampfmatrix[] = array ( 3,0,   4,0,  9,0, 2,20,  13,0,  14,25,  7,0, 12,50, 11,0,  0,0,  10,0,  1,0,  5,0,  8,0,  6,0);
//5  Nachtmar Verlauf: 100 - 100 - 80 - 80 - 60 - 60 - 30 - 30 - 30 - 30 - 30 - 30 - 30
$kampfmatrix[] = array (14,0,  13,0, 12,20,  11,0,  9,0,  4,25, 3,0, 7,50,  0,0,  10,0,  5,0,  1,0,  2,0,  8,0,  6,0);
//6  Transmitterschiff Verlauf: unwichtig
$kampfmatrix[] = array ( 0,0,   1,0,  2,20,  3,0,  4,25,  5,0,  9,0, 7,50,  10,0,  11,0, 12,0, 13,0, 14,0,  8,0,  6,0);
//7  Hydra Verlauf: 100 - 100 - 80 - 80 - 60 - 60 - 30 - 30 - 30 - 30 - 30 - 30 - 30
$kampfmatrix[] = array ( 0,0,  10,0,  5,20, 12,0,  11,25, 1,0,  9,0, 2,50,  3,0,  4,0,  7,0, 13,0, 14,0,  8,0,  6,0);
//8 Frachter
$kampfmatrix[] = array ( 2,0,   3,0, 11,20,  4,0,  7,25, 12,0,  9,0, 1,50,  0,0, 10,0,   5,0, 13,0, 14,0,  8,0,  6,0);
//9 Titan
$kampfmatrix[] = array ( 2,0,   3,5, 11,20,  4,0,  7,20, 12,0,  1,30,  0,0, 10,0,   5,0, 13,0, 14,0,  8,0,  9,0,  6,0);
//10  Jägergarnison Verlauf: 100 - 80 - 60 - 30 - 30 - 30 - 30 - 30
$kampfmatrix[] = array ( 0,0,  5,20,  9,0,  7,0,   4,50,  3,0,  1,0,  2,0,  8,0,  6,0,  0,0,   0,0,   0,0,   0,0,   0,0);
//11  Raketenturm Verlauf: 100 - 80 - 60 - 30 - 30 - 30 - 30 - 30
$kampfmatrix[] = array ( 1,0,  0,20,  9,0,  5,0,   2,0,  3,0,  4,0,  7,0,  8,0,  6,0,  0,0,   0,0,   0,0,   0,0,   0,0);
//12 Laserturm Verlauf: 100 - 80 - 60 - 30 - 30 - 30 - 30 - 30
$kampfmatrix[] = array ( 5,0,  1,20,  9,0,  0,0,  2,50,  3,0,  4,0,  7,0,  8,0,  6,0,  0,0,   0,0,   0,0,   0,0,   0,0);
//13 Autokanonenturm Verlauf: 100 - 80 - 60 - 30 - 30 - 30 - 30 - 30
$kampfmatrix[] = array ( 2,0,  3,0,   4,20,  9,0,  7,50,  1,0,  5,0,  0,0,  8,0,  6,0,  0,0,   0,0,   0,0,   0,0,   0,0);
//14 Plasmaturm Verlauf: 100 - 80 - 60 - 30 - 30 - 30 - 30 - 30
$kampfmatrix[] = array ( 3,0,  2,10,  4,0,  9,0,  1,50,    5,0,  0,0,  7,0,  8,0,  6,0,  0,0,   0,0,   0,0,   0,0,   0,0);


//daten f�r die unterst�tzung der kleinen einheiten durch die gro�en
//Reihenfolge: Kreuzer - Schlachter - Tr�ger
//Ewiger: 10 - 22 - 34
$umatrix[] = array (10, 22, 34);

//Ishtar: 12 - 28 - 43
$umatrix[] = array (12, 28, 43);

//K`Tharr: 6 - 14 - 26
$umatrix[] = array ( 6, 14, 26);

//Z`Tah-ara: 12 - 29 - 48
$umatrix[] = array (12, 29, 48);

//dxnochwas: 10 - 25 - 39
$umatrix[] = array (10, 25, 39);

//anzahl der schiffe die immmer unterst�tzt werden, unabh�ngig vom Support
$uanz=500;//rand(450,550);

// Unterstuetzungsmatrix fuer Schlachtschiffe und Kreuzer
// Berechnung: Punkte Kreuzer o. Schlachtschiff / (5 * Punkte Jaeger)
//Ewiger: 10 - 22
$smatrix[] = array ( 8, 18 );

//Ishtar: 10 - 22
$smatrix[] = array ( 8, 18 );

//K`Tharr: 8 - 18
$smatrix[] = array ( 7, 14 );

//Z`Tah-ara: 16 - 38
$smatrix[] = array ( 12, 30 );

//dxnochwas
$smatrix[] = array ( 7, 18 );

//vorbelegung der einheitendaten
//$unit[rasse 0-3][schiffname aus db][hp][aw]

//ewigen
$res = mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_tech_data where tech_id>80 and tech_id<110 order by tech_id");
while($row = mysqli_fetch_array($res)){//f�r jede flotte die daten auslesen
    $schiffsname[]   = $row["tech_name"];
    //$schiffspunkte[] = $row["score"];
    //$restypen[] = array ($row["restyp01"], $row["restyp02"], $row["restyp03"], $row["restyp04"], $row["restyp05"]);
}

$unit[0][0][0]=getTechNameByRasse($schiffsname[0],1);//j�ger
$unit[0][0][1]=15; //hitpoints
$unit[0][0][2]=4;  //angriffswert
$unit[0][0][3]=0;  //blockwert
$unit[0][0][4]=150;//Punkte
$unit[0][0][5]=array(1000,250,0,0,0);
$unit[0][0]['bz']=8;

$unit[0][1][0]=getTechNameByRasse($schiffsname[1],1);//jagdboot
$unit[0][1][1]=60;
// Verhaeltnis: 50/50
$unit[0][1][2]=8;
$unit[0][1][3]=8;
$unit[0][1][4]=600;
$unit[0][1][5]=array(4000,1000,0,0,0);
$unit[0][1]['bz']=16;

$unit[0][2][0]=getTechNameByRasse($schiffsname[2],1);//zerst�rer
$unit[0][2][1]=280;
$unit[0][2][2]=70;
$unit[0][2][3]=0;
$unit[0][2][4]=2800;
$unit[0][2][5]=array(15000,5000,1000,0,0);
$unit[0][2]['bz']=32;

$unit[0][3][0]=getTechNameByRasse($schiffsname[3],1);//kreuzer
$unit[0][3][1]=590;
$unit[0][3][2]=148;
$unit[0][3][3]=0;
$unit[0][3][4]=5900;
$unit[0][3][5]=array(30000,10000,1000,1500,0);
$unit[0][3]['bz']=64;

$unit[0][4][0]=getTechNameByRasse($schiffsname[4],1);//schlachtschiff
$unit[0][4][1]=1452;//1320
$unit[0][4][2]=330;
$unit[0][4][3]=0;
$unit[0][4][4]=13200;
$unit[0][4][5]=array(50000,20000,2000,4000,2);
$unit[0][4]['bz']=96;

$unit[0][5][0]=getTechNameByRasse($schiffsname[5],1);//bomber
$unit[0][5][1]=25;
$unit[0][5][2]=8;
$unit[0][5][3]=0;
$unit[0][5][4]=250;
$unit[0][5][5]=array(1500,500,0,0,0);
$unit[0][5]['bz']=10;

$unit[0][6][0]=getTechNameByRasse($schiffsname[6],1);//transmitterschiff
$unit[0][6][1]=40;
$unit[0][6][2]=0;
$unit[0][6][3]=0;
$unit[0][6][4]=400;
$unit[0][6][5]=array(2000,1000,0,0,0);
$unit[0][6]['bz']=12;

$unit[0][7][0]=getTechNameByRasse($schiffsname[7],1);//trägerschiff
$unit[0][7][1]=1550;
$unit[0][7][2]=194;
$unit[0][7][3]=0;
$unit[0][7][4]=15500;
$unit[0][7][5]=array(50000,30000,5000,5000,1);
$unit[0][7]['bz']=80;

$unit[0][8][0]=getTechNameByRasse($schiffsname[8],1);//Frachtschiff
$unit[0][8][1]=90;
$unit[0][8][2]=7/4;
$unit[0][8][3]=1;
$unit[0][8][4]=950;
$unit[0][8][5]=array(5000,1500,500,0,0);
$unit[0][8]['bz']=32;
$unit[0][8]['fk']=500;


$unit[0][9][0]=getTechNameByRasse($schiffsname[9],1);//Titan
$unit[0][9][1]=14520;//1320
$unit[0][9][2]=3300;
$unit[0][9][3]=165;
$unit[0][9][4]=115000;
$unit[0][9][5]=array(500000,200000,20000,40000,3);
$unit[0][9]['bz']=192;
$unit[0][9]['item_cost']='I2x1';

$unit[0][10][0]=getTechNameByRasse($schiffsname[10],1);//jägergarnison
$unit[0][10][1]=180;
$unit[0][10][2]=46;
$unit[0][10][3]=0;
$unit[0][10][4]=1500;
$unit[0][10][5]=array(10000,2500,0,0,0);
$unit[0][10]['bz']=8;

$unit[0][11][0]=getTechNameByRasse($schiffsname[11],1);//raketenturm
$unit[0][11][1]=23;
$unit[0][11][2]=6;
$unit[0][11][3]=0;
$unit[0][11][4]=190;
$unit[0][11][5]=array(800,550,0,0,0);
$unit[0][11]['bz']=10;

$unit[0][12][0]=getTechNameByRasse($schiffsname[12],1);//laserturm
$unit[0][12][1]=15;
$unit[0][12][2]=4;
$unit[0][12][3]=0;
$unit[0][12][4]=125;
$unit[0][12][5]=array(250,500,0,0,0);
$unit[0][12]['bz']=10;

$unit[0][13][0]=getTechNameByRasse($schiffsname[13],1);//autokanonenturm
$unit[0][13][1]=39;
$unit[0][13][2]=10;
$unit[0][13][3]=0;
$unit[0][13][4]=325;
$unit[0][13][5]=array(2500,300,50,0,0);
$unit[0][13]['bz']=28;

$unit[0][14][0]=getTechNameByRasse($schiffsname[14],1);//plasmaturm
$unit[0][14][1]=66;
$unit[0][14][2]=17;
$unit[0][14][3]=19;
$unit[0][14][4]=550;
$unit[0][14][5]=array(2000,1000,500,0,0);
$unit[0][14]['bz']=48;

////////////////////////////////////////////////////////////////////
//ishtar
////////////////////////////////////////////////////////////////////

$unit[1][0][0]=getTechNameByRasse($schiffsname[0],2);//j�ger
$unit[1][0][1]=16; //hitpoints
$unit[1][0][2]=3;  //angriffswert
$unit[1][0][3]=0;  //blockwert
$unit[1][0][4]=155;
$unit[1][0][5]=array(1250,150,0,0,0);
$unit[1][0]['bz']=8;

$unit[1][1][0]=getTechNameByRasse($schiffsname[1],2);//jagdboot
$unit[1][1][1]=55;
// Verhaeltnis: 30/70
$unit[1][1][2]=3;
$unit[1][1][3]=12;
$unit[1][1][4]=550;
$unit[1][1][5]=array(3500,1000,0,0,0);
$unit[1][1]['bz']=16;

$unit[1][2][0]=getTechNameByRasse($schiffsname[2],2);//zerst�rer
$unit[1][2][1]=290;
$unit[1][2][2]=58;
$unit[1][2][3]=0;
$unit[1][2][4]=2900;
$unit[1][2][5]=array(15500,4500,1500,0,0);
$unit[1][2]['bz']=32;

$unit[1][3][0]=getTechNameByRasse($schiffsname[3],2);//kreuzer
$unit[1][3][1]=590;
$unit[1][3][2]=118;
$unit[1][3][3]=0;
$unit[1][3][4]=5900;
$unit[1][3][5]=array(32000,8000,1000,2000,0);
$unit[1][3]['bz']=64;

$unit[1][4][0]=getTechNameByRasse($schiffsname[4],2);//schlachtschiff
$unit[1][4][1]=1198;//1089
$unit[1][4][2]=278;
$unit[1][4][3]=0;
$unit[1][4][4]=13900;
$unit[1][4][5]=array(45000,30000,2000,2000,2);
$unit[1][4]['bz']=96;

$unit[1][5][0]=getTechNameByRasse($schiffsname[5],2);//bomber
$unit[1][5][1]=20;
$unit[1][5][2]=0;
$unit[1][5][3]=5;
$unit[1][5][4]=200;
$unit[1][5][5]=array(1200,400,0,0,0);
$unit[1][5]['bz']=10;

$unit[1][6][0]=getTechNameByRasse($schiffsname[6],2);//transmitterschiff
$unit[1][6][1]=40;
$unit[1][6][2]=0;
$unit[1][6][3]=0;
$unit[1][6][4]=400;
$unit[1][6][5]=array(2000,1000,0,0,0);
$unit[1][6]['bz']=12;

$unit[1][7][0]=getTechNameByRasse($schiffsname[7],2);//tr�gerschiff
$unit[1][7][1]=1460;
$unit[1][7][2]=166;
$unit[1][7][3]=0;
$unit[1][7][4]=16600;
$unit[1][7][5]=array(55000,25000,7000,5000,2);
$unit[1][7]['bz']=80;

$unit[1][8][0]=getTechNameByRasse($schiffsname[8],2);//Frachtschiff
$unit[1][8][1]=90;
$unit[1][8][2]=8/4;
$unit[1][8][3]=4;
$unit[1][8][4]=850;
$unit[1][8][5]=array(4000,1500,500,0,0);
$unit[1][8]['bz']=32;
$unit[1][8]['fk']=550;

$unit[1][9][0]=getTechNameByRasse($schiffsname[9],2);//Titan
$unit[1][9][1]=11980;//1320
$unit[1][9][2]=2780;
$unit[1][9][3]=208;
$unit[1][9][4]=122000;
$unit[1][9][5]=array(450000,300000,20000,20000,3);
$unit[1][9]['bz']=192;
$unit[1][9]['item_cost']='I2x1';

$unit[1][10][0]=getTechNameByRasse($schiffsname[8],2);//j�gergarnison
$unit[1][10][1]=198;
$unit[1][10][2]=54;
$unit[1][10][3]=0;
$unit[1][10][4]=1500;
$unit[1][10][5]=array(12000,1500,0,0,0);
$unit[1][10]['bz']=8;

$unit[1][11][0]=getTechNameByRasse($schiffsname[9],2);//raketenturm
$unit[1][11][1]=18;
$unit[1][11][2]=5;
$unit[1][11][3]=0;
$unit[1][11][4]=135;
$unit[1][11][5]=array(450,450,0,0,0);
$unit[1][11]['bz']=16;

$unit[1][12][0]=getTechNameByRasse($schiffsname[10],2);//laserturm
$unit[1][12][1]=10;
$unit[1][12][2]=2.5;
$unit[1][12][3]=0;
$unit[1][12][4]=75;
$unit[1][12][5]=array(250,250,0,0,0);
$unit[1][12]['bz']=8;

$unit[1][13][0]=getTechNameByRasse($schiffsname[11],2);//autokanonenturm
$unit[1][13][1]=34;
$unit[1][13][2]=10;
$unit[1][13][3]=0;
$unit[1][13][4]=260;
$unit[1][13][5]=array(2500,50,0,0,0);
$unit[1][13]['bz']=20;

$unit[1][14][0]=getTechNameByRasse($schiffsname[12],2);//plasmaturm
$unit[1][14][1]=69;
$unit[1][14][2]=19;
$unit[1][14][3]=20;
$unit[1][14][4]=525;
$unit[1][14][5]=array(1500,1000,250,250,0);
$unit[1][14]['bz']=40;

////////////////////////////////////////////////////////////////////
//ktharr
////////////////////////////////////////////////////////////////////

$unit[2][0][0]=getTechNameByRasse($schiffsname[0],3);//j�ger
$unit[2][0][1]=14; //hitpoints
$unit[2][0][2]=6;  //angriffswert
$unit[2][0][3]=0;  //blockwert
$unit[2][0][4]=175;
$unit[2][0][5]=array(750,500,0,0,0);
$unit[2][0]['bz']=8;

$unit[2][1][0]=getTechNameByRasse($schiffsname[1],3);//jagdboot
$unit[2][1][1]=56;
// Verhaeltnis: 70/30
$unit[2][1][2]=16;
$unit[2][1][3]=4;
$unit[2][1][4]=700;
$unit[2][1][5]=array(4000,1500,0,0,0);
$unit[2][1]['bz']=16;

$unit[2][2][0]=getTechNameByRasse($schiffsname[2],3);//zerst�rer
$unit[2][2][1]=228;
$unit[2][2][2]=93;
$unit[2][2][3]=0;
$unit[2][2][4]=2850;
$unit[2][2][5]=array(15000,2500,1500,1000,0);
$unit[2][2]['bz']=32;

$unit[2][3][0]=getTechNameByRasse($schiffsname[3],3);//kreuzer
$unit[2][3][1]=462;
$unit[2][3][2]=188;
$unit[2][3][3]=0;
$unit[2][3][4]=5780;
$unit[2][3][5]=array(30000,6500,2000,2200,0);
$unit[2][3]['bz']=64;

$unit[2][4][0]=getTechNameByRasse($schiffsname[4],3);//schlachtschiff
$unit[2][4][1]=1083;//984
$unit[2][4][2]=400;
$unit[2][4][3]=0;
$unit[2][4][4]=12300;
$unit[2][4][5]=array(45000,25000,2000,3000,1);
$unit[2][4]['bz']=96;

$unit[2][5][0]=getTechNameByRasse($schiffsname[5],3);//bomber
$unit[2][5][1]=16;
$unit[2][5][2]=10;
$unit[2][5][3]=0;
$unit[2][5][4]=200;
$unit[2][5][5]=array(1000,500,0,0,0);
$unit[2][5]['bz']=10;

$unit[2][6][0]=getTechNameByRasse($schiffsname[6],3);//transmitterschiff
$unit[2][6][1]=40;
$unit[2][6][2]=0;
$unit[2][6][3]=0;
$unit[2][6][4]=400;
$unit[2][6][5]=array(2000,1000,0,0,0);
$unit[2][6]['bz']=12;

$unit[2][7][0]=getTechNameByRasse($schiffsname[7],3);//tr�gerschiff
$unit[2][7][1]=1376;
$unit[2][7][2]=280;
$unit[2][7][3]=0;
$unit[2][7][4]=17200;
$unit[2][7][5]=array(60000,30000,6000,6000,1);
$unit[2][7]['bz']=80;

$unit[2][8][0]=getTechNameByRasse($schiffsname[8],3);//Frachtschiff
$unit[2][8][1]=90;
$unit[2][8][2]=5/4;
$unit[2][8][3]=1;
$unit[2][8][4]=990;
$unit[2][8][5]=array(5000,1500,500,100,0);
$unit[2][8]['bz']=32;
$unit[2][8]['fk']=500;

$unit[2][9][0]=getTechNameByRasse($schiffsname[9],3);//Titan
$unit[2][9][1]=10830;//1320
$unit[2][9][2]=4000;
$unit[2][9][3]=100;
$unit[2][9][4]=116000;
$unit[2][9][5]=array(450000,250000,20000,30000,3);
$unit[2][9]['bz']=192;
$unit[2][9]['item_cost']='I2x1';

$unit[2][10][0]=getTechNameByRasse($schiffsname[8],3);//jägergarnison
$unit[2][10][1]=180;
$unit[2][10][2]=45;
$unit[2][10][3]=0;
$unit[2][10][4]=1750;
$unit[2][10][5]=array(7500,5000,0,0,0);
$unit[2][10]['bz']=8;

$unit[2][11][0]=getTechNameByRasse($schiffsname[9],3);//raketenturm
$unit[2][11][1]=16;
$unit[2][11][2]=4;
$unit[2][11][3]=0;
$unit[2][11][4]=160;
$unit[2][11][5]=array(1000,300,0,0,0);
$unit[2][11]['bz']=20;

$unit[2][12][0]=getTechNameByRasse($schiffsname[10],3);//laserturm
$unit[2][12][1]=9;
$unit[2][12][2]=2.5;
$unit[2][12][3]=0;
$unit[2][12][4]=150;
$unit[2][12][5]=array(500,500,0,0,0);
$unit[2][12]['bz']=10;

$unit[2][13][0]=getTechNameByRasse($schiffsname[11],3);//autokanonenturm
$unit[2][13][1]=31;
$unit[2][13][2]=8.5;
$unit[2][13][3]=0;
$unit[2][13][4]=340;
$unit[2][13][5]=array(3000,200,0,0,0);
$unit[2][13]['bz']=28;

$unit[2][14][0]=getTechNameByRasse($schiffsname[12],3);//plasmaturm
$unit[2][14][1]=63;
$unit[2][14][2]=14;
$unit[2][14][3]=18;
$unit[2][14][4]=615;
$unit[2][14][5]=array(2250,1500,300,0,0);
$unit[2][14]['bz']=48;

////////////////////////////////////////////////////////////////////
//zthahara
////////////////////////////////////////////////////////////////////
$unit[3][0][0]=getTechNameByRasse($schiffsname[0],4);//j�ger
$unit[3][0][1]=10; //hitpoints
$unit[3][0][2]=2;  //angriffswert
$unit[3][0][3]=0;  //blockwert
$unit[3][0][4]=80;
$unit[3][0][5]=array(500,150,0,0,0);
$unit[3][0]['bz']=6;

$unit[3][1][0]=getTechNameByRasse($schiffsname[1],4);//jagdboot
$unit[3][1][1]=60;
// Verhaeltnis: 40/60
$unit[3][1][2]=5;
$unit[3][1][3]=8;
$unit[3][1][4]=500;
$unit[3][1][5]=array(3000,1000,0,0,0);
$unit[3][1]['bz']=14;

$unit[3][2][0]=getTechNameByRasse($schiffsname[2],4);//zerst�rer
$unit[3][2][1]=336;
$unit[3][2][2]=63;
$unit[3][2][3]=0;
$unit[3][2][4]=2800;
$unit[3][2][5]=array(10000,5000,2000,500,0);
$unit[3][2]['bz']=30;

$unit[3][3][0]=getTechNameByRasse($schiffsname[3],4);//kreuzer
$unit[3][3][1]=600;
$unit[3][3][2]=113;
$unit[3][3][3]=0;
$unit[3][3][4]=5000;
$unit[3][3][5]=array(25000,10000,1000,500,0);
$unit[3][3]['bz']=60;

$unit[3][4][0]=getTechNameByRasse($schiffsname[4],4);//schlachtschiff
$unit[3][4][1]=1598;//1452
$unit[3][4][2]=272;
$unit[3][4][3]=0;
$unit[3][4][4]=12100;
$unit[3][4][5]=array(50000,15000,3000,3000,2);
$unit[3][4]['bz']=90;

$unit[3][5][0]=getTechNameByRasse($schiffsname[5],4);//bomber
$unit[3][5][1]=30;
$unit[3][5][2]=6;
$unit[3][5][3]=0;
$unit[3][5][4]=250;
$unit[3][5][5]=array(1000,750,0,0,0);
$unit[3][5]['bz']=8;

$unit[3][6][0]=getTechNameByRasse($schiffsname[6],4);//transmitterschiff
$unit[3][6][1]=40;
$unit[3][6][2]=0;
$unit[3][6][3]=0;
$unit[3][6][4]=400;
$unit[3][6][5]=array(2000,1000,0,0,0);
$unit[3][6]['bz']=6;

$unit[3][7][0]=getTechNameByRasse($schiffsname[7],4);//trägerschiff
$unit[3][7][1]=1860;
$unit[3][7][2]=174;
$unit[3][7][3]=0;
$unit[3][7][4]=15500;
$unit[3][7][5]=array(50000,25000,7000,6000,1);
$unit[3][7]['bz']=76;

$unit[3][8][0]=getTechNameByRasse($schiffsname[8],4);//Frachtschiff
$unit[3][8][1]=90;
$unit[3][8][2]=5/4;
$unit[3][8][3]=1;
$unit[3][8][4]=980;
$unit[3][8][5]=array(5000,1500,600,0,0);
$unit[3][8]['bz']=32;
$unit[3][8]['fk']=500;

$unit[3][9][0]=getTechNameByRasse($schiffsname[9],4);//Titan
$unit[3][9][1]=15980;//1320
$unit[3][9][2]=2720;
$unit[3][9][3]=136;
$unit[3][9][4]=104000;
$unit[3][9][5]=array(500000,150000,30000,30000,3);
$unit[3][9]['bz']=192;
$unit[3][9]['item_cost']='I2x1';

$unit[3][10][0]=getTechNameByRasse($schiffsname[8],4);//Jägergarnison
$unit[3][10][1]=96;
$unit[3][10][2]=24;
$unit[3][10][3]=0;
$unit[3][10][4]=800;
$unit[3][10][5]=array(5000,1500,0,0,0);
$unit[3][10]['bz']=6;

$unit[3][11][0]=getTechNameByRasse($schiffsname[9],4);//raketenturm
$unit[3][11][1]=20;
$unit[3][11][2]=5;
$unit[3][11][3]=0;
$unit[3][11][4]=170;
$unit[3][11][5]=array(600,400,100,0,0);
$unit[3][11]['bz']=20;

$unit[3][12][0]=getTechNameByRasse($schiffsname[10],4);//laserturm
$unit[3][12][1]=11;
$unit[3][12][2]=2.5;
$unit[3][12][3]=0;
$unit[3][12][4]=90;
$unit[3][12][5]=array(300,300,0,0,0);
$unit[3][12]['bz']=10;

$unit[3][13][0]=getTechNameByRasse($schiffsname[11],4);//autokanonenturm
$unit[3][13][1]=35;
$unit[3][13][2]=8;
$unit[3][13][3]=0;
$unit[3][13][4]=295;
$unit[3][13][5]=array(2000,400,50,0,0);
$unit[3][13]['bz']=28;

$unit[3][14][0]=getTechNameByRasse($schiffsname[12],4);//plasmaturm
$unit[3][14][1]=64;
$unit[3][14][2]=16;
$unit[3][14][3]=19;
$unit[3][14][4]=530;
$unit[3][14][5]=array(1000,1500,100,250,0);
$unit[3][14]['bz']=48;

////////////////////////////////////////////////////////////////////
//dxnochwas
////////////////////////////////////////////////////////////////////

$unit[4][0][0]=getTechNameByRasse($schiffsname[0],5);//j�ger
$unit[4][0][1]=16.2;//18 //hitpoints
$unit[4][0][2]=1.8;//2;  //angriffswert
$unit[4][0][3]=0;  //blockwert
$unit[4][0][4]=160;
$unit[4][0][5]=array(600,500,0,0,0);
$unit[4][0]['bz']=7;

$unit[4][1][0]=getTechNameByRasse($schiffsname[1],5);//jagdboot
$unit[4][1][1]=62;//69
// Verhaeltnis: 50/50
$unit[4][1][2]=2.5;//5;
$unit[4][1][3]=1;
$unit[4][1][4]=625;
$unit[4][1][5]=array(3250,1500,0,0,0);
$unit[4][1]['bz']=15;

$unit[4][2][0]=getTechNameByRasse($schiffsname[2],5);//zerstörer
$unit[4][2][1]=280;//312
$unit[4][2][2]=34;//38;
$unit[4][2][3]=0;
$unit[4][2][4]=2840;
$unit[4][2][5]=array(12000,4000,1800,750,0);
$unit[4][2]['bz']=31;

$unit[4][3][0]=getTechNameByRasse($schiffsname[3],5);//kreuzer
$unit[4][3][1]=548;//608
$unit[4][3][2]=68;//76;
$unit[4][3][3]=0;
$unit[4][3][4]=5525;
$unit[4][3][5]=array(34000,7000,750,1250,0);
$unit[4][3]['bz']=62;

$unit[4][4][0]=getTechNameByRasse($schiffsname[4],5);//schlachtschiff
$unit[4][4][1]=1560;//1743
$unit[4][4][2]=180;//200;
$unit[4][4][3]=0;
$unit[4][4][4]=12600;
$unit[4][4][5]=array(55000,25000,3000,2500,2);
$unit[4][4]['bz']=93;

$unit[4][5][0]=getTechNameByRasse($schiffsname[5],5);//bomber
$unit[4][5][1]=22;//25
$unit[4][5][2]=3.6;//4;
$unit[4][5][3]=0;
$unit[4][5][4]=225;
$unit[4][5][5]=array(750,750,0,0,0);
$unit[4][5]['bz']=9;

$unit[4][6][0]=getTechNameByRasse($schiffsname[6],5);//transmitterschiff
$unit[4][6][1]=36;//40
$unit[4][6][2]=0;
$unit[4][6][3]=0;
$unit[4][6][4]=400;
$unit[4][6][5]=array(2000,1000,0,0,0);
$unit[4][6]['bz']=11;

$unit[4][7][0]=getTechNameByRasse($schiffsname[0],5);//tr�gerschiff
$unit[4][7][1]=1650;//1826
$unit[4][7][2]=102;//114;
$unit[4][7][3]=0;
$unit[4][7][4]=16600;
$unit[4][7][5]=array(58000,27000,6000,4000,2);
$unit[4][7]['bz']=78;

$unit[4][8][0]=getTechNameByRasse($schiffsname[8],5);//Frachtschiff
$unit[4][8][1]=81;//90
$unit[4][8][2]=1.8;//2
$unit[4][8][3]=1;
$unit[4][8][4]=950;
$unit[4][8][5]=array(5000,1500,500,0,0);
$unit[4][8]['bz']=32;
$unit[4][8]['fk']=500;

$unit[4][9][0]=getTechNameByRasse($schiffsname[9],5);//Titan
$unit[4][9][1]=13000;//14520
$unit[4][9][2]=3000;//3300
$unit[4][9][3]=165;
$unit[4][9][4]=115000;
$unit[4][9][5]=array(500000,200000,20000,40000,3);
$unit[4][9]['bz']=192;
$unit[4][9]['item_cost']='I2x1';

$unit[4][10][0]=getTechNameByRasse($schiffsname[8],5);//jägergarnison
$unit[4][10][1]=96;//106
$unit[4][10][2]=10;//11;
$unit[4][10][3]=0;
$unit[4][10][4]=1550;
$unit[4][10][5]=array(8500,3500,0,0,0);
$unit[4][10]['bz']=7;

$unit[4][11][0]=getTechNameByRasse($schiffsname[9],5);//raketenturm
$unit[4][11][1]=20;//22
$unit[4][11][2]=1.8;//2;
$unit[4][11][3]=0;
$unit[4][11][4]=165;
$unit[4][11][5]=array(450,450,100,0,0);
$unit[4][11]['bz']=18;

$unit[4][12][0]=getTechNameByRasse($schiffsname[10],5);//laserturm
$unit[4][12][1]=10;//12
$unit[4][12][2]=0.9;//1;
$unit[4][12][3]=0;
$unit[4][12][4]=110;
$unit[4][12][5]=array(400,350,0,0,0);
$unit[4][12]['bz']=9;

$unit[4][13][0]=getTechNameByRasse($schiffsname[11],5);//autokanonenturm
$unit[4][13][1]=35;//39
$unit[4][13][2]=3.6;//4;
$unit[4][13][3]=0;
$unit[4][13][4]=300;
$unit[4][13][5]=array(2500,100,100,0,0);
$unit[4][13]['bz']=24;

$unit[4][14][0]=getTechNameByRasse($schiffsname[12],5);//plasmaturm
$unit[4][14][1]=63;//70
$unit[4][14][2]=7.2;//8;
$unit[4][14][3]=2;
$unit[4][14][4]=615;
$unit[4][14][5]=array(2000,1250,350,150,0);
$unit[4][14]['bz']=44;

$GLOBALS['unit']=$unit;

unset($schiffsname);
unset($schiffspunkte);
unset($restypen);
?>