<?php
if(!isset($sv_deactivate_vsystems)){
	$sv_deactivate_vsystems=0;
}

//verhältnis der belohnungen zwischen den servern
$ac_prozent=$sv_winscore/960/100*2.75;

//liste der belohnungen
$achievement_anz=0;
//maximal erreichbare errungenschaften
$max_achievement_points=0;

//besitze kollektoren
$achievement_anz++;
$stufen=round($sv_winscore/1440);
$mbelohnung=30000000*$ac_prozent;
for($index=0;$index<$stufen;$index++){
	$rewards1[$index][0]=($index+1)*20; //zu erreichender wert
	@$rewards1[$index][1]=round(($mbelohnung/($stufen+1))*($index+1)/($stufen/2)); //belohnung in M
	$max_achievement_points++;
}


//gestohlene kollektoren
$achievement_anz++;
$stufen=round($sv_winscore/1440);
$mbelohnung=30000000*$ac_prozent;
for($index=0;$index<$stufen;$index++)
{
	$rewards2[$index][0]=($index+1)*25; //zu erreichender wert
	@$rewards2[$index][1]=round(($mbelohnung/($stufen+1))*($index+1)/($stufen/2)); //belohnung in M
	$max_achievement_points++;
}


//agentenmenge
$achievement_anz++;
$stufen=round($sv_winscore/1440);
$mbelohnung=30000000*$ac_prozent;
for($index=0;$index<$stufen;$index++)
{
	$rewards3[$index][0]=($index+1)*1000; //zu erreichender wert
	@$rewards3[$index][1]=round(($mbelohnung/($stufen+1))*($index+1)/($stufen/2)); //belohnung in M
	$max_achievement_points++;
}


//sondenmenge
$achievement_anz++;
$stufen=round($sv_winscore/1440);
$mbelohnung=1000000*$ac_prozent;
for($index=0;$index<$stufen;$index++)
{
	$rewards4[$index][0]=($index+1)*10; //zu erreichender wert
	@$rewards4[$index][1]=round(($mbelohnung/($stufen+1))*($index+1)/($stufen/2)); //belohnung in M
	$max_achievement_points++;
}


//erfülle missionen
$achievement_anz++;
$stufen=round($sv_winscore/1440);
$mbelohnung=8000000*$ac_prozent;
for($index=0;$index<$stufen;$index++){
	$rewards5[$index][0]=1000/$stufen*($index+1); //zu erreichender wert
	@$rewards5[$index][1]=round(($mbelohnung/($stufen+1))*($index+1)/($stufen/2)); //belohnung in M
	$max_achievement_points++;
}
/*
$achievement_anz++;
$index=0;
$rewards5[$index][0]=1; //zu erreichender wert
$rewards5[$index][1]=30000; //belohnung in M
$index++;
$rewards5[$index][0]=2; //zu erreichender wert
$rewards5[$index][1]=450000; //belohnung in M
$index++;
$rewards5[$index][0]=3; //zu erreichender wert
$rewards5[$index][1]=675000; //belohnung in M
$index++;
$rewards5[$index][0]=4; //zu erreichender wert
$rewards5[$index][1]=1000000; //belohnung in M
$index++;
$rewards5[$index][0]=5; //zu erreichender wert
$rewards5[$index][1]=1500000; //belohnung in M
$index++;
$rewards5[$index][0]=6; //zu erreichender wert
$rewards5[$index][1]=2200000; //belohnung in M
$index++;
$rewards5[$index][0]=7; //zu erreichender wert
$rewards5[$index][1]=3500000; //belohnung in M
$index++;
$rewards5[$index][0]=8; //zu erreichender wert
$rewards5[$index][1]=5200000; //belohnung in M
$index++;
$rewards5[$index][0]=9; //zu erreichender wert
$rewards5[$index][1]=7500000; //belohnung in M
$index++;
$rewards5[$index][0]=10; //zu erreichender wert
$rewards5[$index][1]=9500000; //belohnung in M
$index++;
$rewards5[$index][0]=11; //zu erreichender wert
$rewards5[$index][1]=11500000; //belohnung in M
*/

//menge kriegsartefakte
$achievement_anz++;
$stufen=round($sv_winscore/1440);
$mbelohnung=30000000*$ac_prozent;
for($index=0;$index<$stufen;$index++)
{
	$rewards6[$index][0]=($index+1)*20; //zu erreichender wert
	@$rewards6[$index][1]=round(($mbelohnung/($stufen+1))*($index+1)/($stufen/2)); //belohnung in M
	$max_achievement_points++;
}



//menge efta-boni erhalten
$achievement_anz++;
$stufen=round($sv_winscore/1440);
$mbelohnung=10000000*$ac_prozent;
for($index=0;$index<$stufen;$index++){
	$rewards7[$index][0]=($index+1)*1; //zu erreichender wert
	@$rewards7[$index][1]=round(($mbelohnung/($stufen+1))*($index+1)/($stufen/2)); //belohnung in M
	if($sv_deactivate_efta==0)$max_achievement_points++;
}

//menge an artefakten im artefaktgebäude
$achievement_anz++;
$index=0;
$rewards8[$index][0]=1; //zu erreichender wert
$rewards8[$index][1]=20000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards8[$index][0]=2; //zu erreichender wert
$rewards8[$index][1]=80000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards8[$index][0]=3; //zu erreichender wert
$rewards8[$index][1]=95000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards8[$index][0]=4; //zu erreichender wert
$rewards8[$index][1]=120000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards8[$index][0]=5; //zu erreichender wert
$rewards8[$index][1]=135000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards8[$index][0]=6; //zu erreichender wert
$rewards8[$index][1]=170000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards8[$index][0]=7; //zu erreichender wert
$rewards8[$index][1]=200000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards8[$index][0]=8; //zu erreichender wert
$rewards8[$index][1]=230000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards8[$index][0]=9; //zu erreichender wert
$rewards8[$index][1]=280000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards8[$index][0]=10; //zu erreichender wert
$rewards8[$index][1]=330000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards8[$index][0]=11; //zu erreichender wert
$rewards8[$index][1]=415000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards8[$index][0]=12; //zu erreichender wert
$rewards8[$index][1]=500000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards8[$index][0]=13; //zu erreichender wert
$rewards8[$index][1]=600000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards8[$index][0]=14; //zu erreichender wert
$rewards8[$index][1]=700000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards8[$index][0]=15; //zu erreichender wert
$rewards8[$index][1]=800000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards8[$index][0]=17; //zu erreichender wert
$rewards8[$index][1]=1000000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards8[$index][0]=19; //zu erreichender wert
$rewards8[$index][1]=1200000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards8[$index][0]=21; //zu erreichender wert
$rewards8[$index][1]=1500000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards8[$index][0]=23; //zu erreichender wert
$rewards8[$index][1]=1800000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards8[$index][0]=25; //zu erreichender wert
$rewards8[$index][1]=2100000; //belohnung in M
$max_achievement_points++;

//menge an artefakten in den basisschiffen
$achievement_anz++;
$index=0;
$rewards9[$index][0]=1; //zu erreichender wert
$rewards9[$index][1]=10000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards9[$index][0]=2; //zu erreichender wert
$rewards9[$index][1]=20000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards9[$index][0]=3; //zu erreichender wert
$rewards9[$index][1]=40000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards9[$index][0]=4; //zu erreichender wert
$rewards9[$index][1]=80000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards9[$index][0]=5; //zu erreichender wert
$rewards9[$index][1]=160000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards9[$index][0]=6; //zu erreichender wert
$rewards9[$index][1]=320000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards9[$index][0]=7; //zu erreichender wert
$rewards9[$index][1]=500000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards9[$index][0]=8; //zu erreichender wert
$rewards9[$index][1]=700000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards9[$index][0]=9; //zu erreichender wert
$rewards9[$index][1]=900000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards9[$index][0]=10; //zu erreichender wert
$rewards9[$index][1]=1100000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards9[$index][0]=11; //zu erreichender wert
$rewards9[$index][1]=1300000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards9[$index][0]=12; //zu erreichender wert
$rewards9[$index][1]=1500000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards9[$index][0]=13; //zu erreichender wert
$rewards9[$index][1]=1700000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards9[$index][0]=14; //zu erreichender wert
$rewards9[$index][1]=1900000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards9[$index][0]=15; //zu erreichender wert
$rewards9[$index][1]=2100000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards9[$index][0]=16; //zu erreichender wert
$rewards9[$index][1]=2300000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards9[$index][0]=17; //zu erreichender wert
$rewards9[$index][1]=2500000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards9[$index][0]=18; //zu erreichender wert
$rewards9[$index][1]=2700000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards9[$index][0]=19; //zu erreichender wert
$rewards9[$index][1]=2900000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards9[$index][0]=20; //zu erreichender wert
$rewards9[$index][1]=3100000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards9[$index][0]=21; //zu erreichender wert
$rewards9[$index][1]=3300000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards9[$index][0]=22; //zu erreichender wert
$rewards9[$index][1]=3500000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards9[$index][0]=23; //zu erreichender wert
$rewards9[$index][1]=3700000; //belohnung in M
$max_achievement_points++;
$index++;
$rewards9[$index][0]=24; //zu erreichender wert
$rewards9[$index][1]=3900000; //belohnung in M
$max_achievement_points++;



//sektorspenden
$achievement_anz++;
$stufen=round($sv_winscore/1440);
$mbelohnung=30000000*$ac_prozent;
for($index=0;$index<$stufen;$index++){
	$rewards10[$index][0]=($index+1)*500000; //zu erreichender wert
	@$rewards10[$index][1]=round(($mbelohnung/($stufen+1))*($index+1)/($stufen/2)); //belohnung in M
	$max_achievement_points++;
}


//tronic der allianz spenden
$achievement_anz++;
$stufen=round($sv_winscore/1440);
$mbelohnung=10000000*$ac_prozent;
for($index=0;$index<$stufen;$index++){
	$rewards11[$index][0]=($index+1)*10; //zu erreichender wert
	@$rewards11[$index][1]=round(($mbelohnung/($stufen+1))*($index+1)/($stufen/2)); //belohnung in M
	$max_achievement_points++;
}


//kopfgeld erobert
$achievement_anz++;
$stufen=round($sv_winscore/1440);
$mbelohnung=10000000*$ac_prozent;
for($index=0;$index<$stufen;$index++){
  $rewards12[$index][0]=($index+1)*500000; //zu erreichender wert
  @$rewards12[$index][1]=round(($mbelohnung/($stufen+1))*($index+1)/($stufen/2)); //belohnung in M
}


//ea-aktivität
$achievement_anz++;
$stufen=round($sv_winscore/1440);
$mbelohnung=10000000*$ac_prozent;
for($index=0;$index<$stufen;$index++){
	$rewards13[$index][0]=($index+1)*1; //zu erreichender wert
	@$rewards13[$index][1]=round(($mbelohnung/($stufen+1))*($index+1)/($stufen/2)); //belohnung in M
	if($sv_deactivate_sou==0)$max_achievement_points++;
}


//handelspunkte
$achievement_anz++;
$stufen=round($sv_winscore/1440);
$mbelohnung=5000000*$ac_prozent;
for($index=0;$index<$stufen;$index++)
{
	$rewards14[$index][0]=floor(($index+1)*800000*$GLOBALS['tech_build_time_faktor']); //zu erreichender wert
	@$rewards14[$index][1]=round(($mbelohnung/($stufen+1))*($index+1)/($stufen/2)); //belohnung in M
	if($sv_deactivate_trade==0)$max_achievement_points++;
}


//sektorartefakthaltezeit
$achievement_anz++;
$stufen=round($sv_winscore/1440);
$mbelohnung=30000000*$ac_prozent;
for($index=0;$index<$stufen;$index++)
{
	$rewards15[$index][0]=($index+1)*1000; //zu erreichender wert
	@$rewards15[$index][1]=round(($mbelohnung/($stufen+1))*($index+1)/($stufen/2)); //belohnung in M
	$max_achievement_points++;
}

//spielerartefakte von den npc-sektoren erhalten
$achievement_anz++;
$stufen=round($sv_winscore/1440);
$mbelohnung=5000000*$ac_prozent;
for($index=0;$index<$stufen;$index++)
{
	$rewards16[$index][0]=($index+1)*5; //zu erreichender wert
	@$rewards16[$index][1]=round(($mbelohnung/($stufen+1))*($index+1)/($stufen/2)); //belohnung in M
	$max_achievement_points++;
}

//vergessene Systeme erkunden
$achievement_anz++;
if($sv_deactivate_vsystems==1){
	$stufen=0;
}else{
	$stufen=round($sv_winscore/1440);
}
$mbelohnung=1000000*$ac_prozent;
for($index=0;$index<$stufen;$index++){
	$rewards17[$index][0]=100/$stufen*($index+1); //zu erreichender wert
	@$rewards17[$index][1]=round(($mbelohnung/($stufen+1))*($index+1)/($stufen/2)); //belohnung in M
	$max_achievement_points++;
}

//Vergessene Systeme: Gebäude auf Stufe 5
$achievement_anz++;
if($sv_deactivate_vsystems==1){
	$stufen=0;
}else{
	$stufen=round($sv_winscore/1440);
}
$mbelohnung=20000000*$ac_prozent;
for($index=0;$index<$stufen;$index++){
	$rewards18[$index][0]=1000/$stufen*($index+1); //zu erreichender wert
	@$rewards18[$index][1]=round(($mbelohnung/($stufen+1))*($index+1)/($stufen/2)); //belohnung in M
	$max_achievement_points++;
}

//Vergessene Systeme: Gebäude auf Stufe 10
$achievement_anz++;
if($sv_deactivate_vsystems==1){
	$stufen=0;
}else{
	$stufen=round($sv_winscore/1440);
}
$mbelohnung=40000000*$ac_prozent;
for($index=0;$index<$stufen;$index++){
	$rewards19[$index][0]=500/$stufen*($index+1); //zu erreichender wert
	@$rewards19[$index][1]=round(($mbelohnung/($stufen+1))*($index+1)/($stufen/2)); //belohnung in M
	$max_achievement_points++;
}

?>