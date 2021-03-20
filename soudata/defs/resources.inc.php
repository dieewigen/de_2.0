<?php
/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
//definitionen der spezial rohstoffe
/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
$index=0;
$specialres_def[$index][0]='specialres1';//db spaltenname
$specialres_def[$index][1]='Darnkristall';//name des rohstoff
$index++;
$specialres_def[$index][0]='specialres2';
$specialres_def[$index][1]='Zalussplitter';
$index++;
$specialres_def[$index][0]='specialres3';
$specialres_def[$index][1]='Melagitessenz';
$index++;
$specialres_def[$index][0]='specialres4';
$specialres_def[$index][1]='Hyveelement';
$index++;
$specialres_def[$index][0]='specialres5';
$specialres_def[$index][1]='Ishnartefakt';
$index++;
$specialres_def[$index][0]='specialres6';
$specialres_def[$index][1]='Granarkristall';
$index++;
$specialres_def[$index][0]='specialres7';
$specialres_def[$index][1]='Rieluxsplitter';
$index++;
$specialres_def[$index][0]='specialres8';
$specialres_def[$index][1]='Eledaressenz';
$index++;
$specialres_def[$index][0]='specialres9';
$specialres_def[$index][1]='Uldaraelement';
$index++;
$specialres_def[$index][0]='specialres10';
$specialres_def[$index][1]='Belarartefakt';

/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
//definition der spezialrohstofffundorte im raummüll
/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
// definition x,y,r wobei r für radius steht, der von x/y ausgeht
//eisen
$index=0;
$srv_def[$index][]= array(0,0, 1000000);

//
$index++;
$srv_def[$index][]= array(0,0, 1200);

//
$index++;
$srv_def[$index][]= array(0,0, 1100);

//
$index++;
$srv_def[$index][]= array(0,0, 1000);

//
$index++;
$srv_def[$index][]= array(0,0, 900);

//
$index++;
$srv_def[$index][]= array(0,0, 750);

//
$index++;
$srv_def[$index][]= array(0,0, 600);

//
$index++;
$srv_def[$index][]= array(0,0, 450);

//
$index++;
$srv_def[$index][]= array(0,0, 300);

//
$index++;
$srv_def[$index][]= array(0,0, 150);



/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
//definitionen der rohstoffe
/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
$index=0;
$r_def[$index][0]='Eisen'; //name
$r_def[$index][1]=1; //förderverhältnis beim mining
$r_def[$index][2]=1; //verkaufspreis im handelskontor
$r_def[$index][3]=1; //benötigter handelskontorlevel
$r_def[$index][4]=1; //benötigter lagerkomplexlevel
$r_def[$index][5]=1; //benötigter raumwerftlevel

$index++;
$r_def[$index][0]='Titan'; //name
$r_def[$index][1]=2; //förderverhältnis beim mining
$r_def[$index][2]=2.05; //verkaufspreis im handelskontor
$r_def[$index][3]=2; //benötigter handelskontorlevel
$r_def[$index][4]=2; //benötigter lagerkomplexlevel
$r_def[$index][5]=2; //benötigter raumwerftlevel

$index++;
$r_def[$index][0]='Mexit'; //name
$r_def[$index][1]=5; //förderverhältnis beim mining
$r_def[$index][2]=5.10; //verkaufspreis im handelskontor
$r_def[$index][3]=15; //benötigter handelskontorlevel
$r_def[$index][4]=15; //benötigter lagerkomplexlevel
$r_def[$index][5]=15; //benötigter raumwerftlevel

$index++;
$r_def[$index][0]='Dulexit'; //name
$r_def[$index][1]=10; //förderverhältnis beim mining
$r_def[$index][2]=10.15; //verkaufspreis im handelskontor
$r_def[$index][3]=20; //benötigter handelskontorlevel
$r_def[$index][4]=20; //benötigter lagerkomplexlevel
$r_def[$index][5]=20; //benötigter raumwerftlevel

$index++;
$r_def[$index][0]='Tekranit'; //name
$r_def[$index][1]=15; //förderverhältnis beim mining
$r_def[$index][2]=15.20; //verkaufspreis im handelskontor
$r_def[$index][3]=25; //benötigter handelskontorlevel
$r_def[$index][4]=25; //benötigter lagerkomplexlevel
$r_def[$index][5]=25; //benötigter raumwerftlevel

$index++;
$r_def[$index][0]='Ylesenium'; //name
$r_def[$index][1]=20; //förderverhältnis beim mining
$r_def[$index][2]=20.25; //verkaufspreis im handelskontor
$r_def[$index][3]=30; //benötigter handelskontorlevel
$r_def[$index][4]=30; //benötigter lagerkomplexlevel
$r_def[$index][5]=30; //benötigter raumwerftlevel

$index++;
$r_def[$index][0]='Serodium'; //name
$r_def[$index][1]=25; //förderverhältnis beim mining
$r_def[$index][2]=25.30; //verkaufspreis im handelskontor
$r_def[$index][3]=35; //benötigter handelskontorlevel
$r_def[$index][4]=35; //benötigter lagerkomplexlevel
$r_def[$index][5]=35; //benötigter raumwerftlevel

$index++;
$r_def[$index][0]='Rowalganium'; //name
$r_def[$index][1]=30; //förderverhältnis beim mining
$r_def[$index][2]=30.35; //verkaufspreis im handelskontor
$r_def[$index][3]=40; //benötigter handelskontorlevel
$r_def[$index][4]=40; //benötigter lagerkomplexlevel
$r_def[$index][5]=40; //benötigter raumwerftlevel

$index++;
$r_def[$index][0]='Sextagit'; //name
$r_def[$index][1]=35; //förderverhältnis beim mining
$r_def[$index][2]=35.40; //verkaufspreis im handelskontor
$r_def[$index][3]=45; //benötigter handelskontorlevel
$r_def[$index][4]=45; //benötigter lagerkomplexlevel
$r_def[$index][5]=45; //benötigter raumwerftlevel

$index++;
$r_def[$index][0]='Octagium'; //name
$r_def[$index][1]=40; //förderverhältnis beim mining
$r_def[$index][2]=40.45; //verkaufspreis im handelskontor
$r_def[$index][3]=50; //benötigter handelskontorlevel
$r_def[$index][4]=50; //benötigter lagerkomplexlevel
$r_def[$index][5]=50; //benötigter raumwerftlevel

/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
//definition der rohstofffundorte
/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
// definition x,y,r wobei r für radius steht, der von x/y ausgeht
//eisen
$index=0;
$rv_def[$index][]= array(0,0, 10000000);

//titan
$index++;
$rv_def[$index][]= array(0,0, 10000000);

//mexit
$index++;
$rv_def[$index][]= array(0,0, 1300);

//dulexit
$index++;
$rv_def[$index][]= array(0,0, 1050);

//tekranit
$index++;
$rv_def[$index][]= array(0,0, 900);

//ylesenium
$index++;
$rv_def[$index][]= array(0,0, 750);

//serodium
$index++;
$rv_def[$index][]= array(0,0, 600);

//rowalganium
$index++;
$rv_def[$index][]= array(0,0, 450);

//sextagit
$index++;
$rv_def[$index][]= array(0,0, 300);

//octagium
$index++;
$rv_def[$index][]= array(0,0, 150);

/*
/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
leveldefinitionen für den forschungsrohstoffverbrauch
/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
1	Eisen	Titan
15	Titan	Mexit 
20	Mexit 	Dulexit 
25	Dulexit 	Tekranit 
30	Tekranit 	Ylesenium 
35	Ylesenium 	Serodium 
40	Serodium 	Rowalganium 
45	Rowalganium 	Sextagit 
50	Sextagit 	Octagium
*/

?>