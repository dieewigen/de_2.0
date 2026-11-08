<?php
$GLOBALS['greek_chars']=array('&infin;', "&Alpha;","&Beta;","&Gamma;","&Delta;","&Epsilon;","&Zeta;","&Eta;","&Theta;","&Iota;","&Kappa;","&Lambda;","&Mu;","&Nu;","&Xi;","&Omicron;","&Pi;","&Rho;","&Sigma;","&Tau;","&Upsilon;","&Phi;","&Chi;","&Psi;","&Omega;");

//////////////////////////////////////////////////////////////////
// Systemdefinitionen
//////////////////////////////////////////////////////////////////
$map_system_typen=array('Bewohnter Planet', 'Unbewohnter Planet', 'Asteroid', 'k&uuml;nstliches Objekt', 'Battleground');
//'Zwergstern', 'schwarzes Loch', 'Plasmawolke', 'Antimateriewolke'
$GLOBALS['map_system_typen']=$map_system_typen;

//Subtypen
$map_system_subtypen=array(
	//Typ 0 - Bewohnter Planet
	array(),
	//Typ 1unbewohnter Planet
	array('Gaia', 'Eiswelt','W&uuml;stenwelt', 'Vulkanwelt', 'Eiswelt'),
	//Typ 2 - Asteroid
	array(),
	//Typ 3 - künstliches Objekt
	array('Raumschiff', 'Raumwerft', 'Sonde', 'Forschungsstation'),
	//Typ 4 - Battleground
	array('Alpha', 'Beta', 'Gamma', 'Delta','Epsilon')
	//Typ 5 - Special System
);

$GLOBALS['map_system_subtypen']=$map_system_subtypen;


//////////////////////////////////////////////////////////////////
// Gebäudedefinitionen
//////////////////////////////////////////////////////////////////
$map_buildings=array();

//Weltraumhafen
$bid=0;
$map_buildings[$bid]['name']='Weltraumhafen';
$map_buildings[$bid]['bldg_time']=1800;
$map_buildings[$bid]['bldg_cost'][0]='R1x50000;R2x10000';
$map_buildings[$bid]['bldg_need_fk']=70000;
$map_buildings[$bid]['bldg_in_type']=array(2,3,4,5,6,7); //Welttyp
//$map_buildings[$bid]['bldg_filter_tag']='weha';

//Außenposten
$bid++;
$map_buildings[$bid]['name']='Planetarer Au&szlig;enposten';
$map_buildings[$bid]['bldg_time']=1800;
$map_buildings[$bid]['bldg_cost'][0]='R1x50000;R2x10000';
$map_buildings[$bid]['bldg_cost'][1]='I3x1000';
$map_buildings[$bid]['bldg_cost'][2]='I4x1000';
$map_buildings[$bid]['bldg_cost'][3]='I5x1000';
$map_buildings[$bid]['bldg_cost'][4]='I6x1000';
$map_buildings[$bid]['bldg_cost'][5]='I7x1000';
$map_buildings[$bid]['bldg_cost'][6]='I8x1000';
$map_buildings[$bid]['bldg_cost'][7]='I9x1000';
$map_buildings[$bid]['bldg_cost'][8]='I10x1000';
$map_buildings[$bid]['bldg_cost'][9]='I11x1000';
$map_buildings[$bid]['bldg_need_fk']=70000;
$map_buildings[$bid]['bldg_in_type']=array(1); //Welttyp
$map_buildings[$bid]['bldg_filter_tag']='f_plau';

//Botschaft
$bid++;
$map_buildings[$bid]['name']='Botschaft';
$map_buildings[$bid]['bldg_time']=1800;
$map_buildings[$bid]['bldg_cost'][0]='R1x50000;R2x10000';
$map_buildings[$bid]['bldg_need_fk']=70000;
$map_buildings[$bid]['bldg_in_type']=array(0); //Welttyp
//$map_buildings[$bid]['bldg_filter_tag']='bots';

//Eisen-Industrie
$bid++;
$map_buildings[$bid]['name']='Eisen-Industrie';
$map_buildings[$bid]['bldg_time']=1000;
$map_buildings[$bid]['bldg_cost'][0]='R1x50000;R2x10000';
$map_buildings[$bid]['bldg_cost'][1]='I3x1000';
$map_buildings[$bid]['bldg_cost'][2]='I3x2000';
$map_buildings[$bid]['bldg_cost'][3]='I3x3000';
$map_buildings[$bid]['bldg_cost'][4]='I3x4000';
$map_buildings[$bid]['bldg_cost'][5]='I3x5000';
$map_buildings[$bid]['bldg_cost'][6]='I3x6000';
$map_buildings[$bid]['bldg_cost'][7]='I3x7000';
$map_buildings[$bid]['bldg_cost'][8]='I3x8000';
$map_buildings[$bid]['bldg_cost'][9]='I3x9000';
$map_buildings[$bid]['bldg_in_type']=array(1); //Welttyp
$map_buildings[$bid]['need_tech']=146;
$map_buildings[$bid]['need_field_typ']=array(1);
$map_buildings[$bid]['production_amount']=array(4,5,6,8,10,12,14,16,18,20);
$map_buildings[$bid]['bldg_filter_tag']='f_eiin';

//Titan-Industrie
$bid++;
$map_buildings[$bid]['name']='Titan-Industrie';
$map_buildings[$bid]['bldg_time']=2000;
$map_buildings[$bid]['bldg_cost'][0]='I3x1000';
$map_buildings[$bid]['bldg_cost'][1]='I3x1000';
$map_buildings[$bid]['bldg_cost'][2]='I3x2000';
$map_buildings[$bid]['bldg_cost'][3]='I3x3000';
$map_buildings[$bid]['bldg_cost'][4]='I3x4000';
$map_buildings[$bid]['bldg_cost'][5]='I3x5000';
$map_buildings[$bid]['bldg_cost'][6]='I3x6000';
$map_buildings[$bid]['bldg_cost'][7]='I3x7000';
$map_buildings[$bid]['bldg_cost'][8]='I3x8000';
$map_buildings[$bid]['bldg_cost'][9]='I3x9000';
$map_buildings[$bid]['bldg_in_type']=array(1); //Welttyp
$map_buildings[$bid]['need_tech']=147;
$map_buildings[$bid]['need_field_typ']=array(2);
$map_buildings[$bid]['production_amount']=array(4,5,6,8,10,12,14,16,18,20);
$map_buildings[$bid]['bldg_filter_tag']='f_tiin';

//Mexit-Industrie
$bid++;
$map_buildings[$bid]['name']='Mexit-Industrie';
$map_buildings[$bid]['bldg_time']=3000;
$map_buildings[$bid]['bldg_cost'][0]='I4x2000';
$map_buildings[$bid]['bldg_cost'][1]='I4x1000';
$map_buildings[$bid]['bldg_cost'][2]='I4x2000';
$map_buildings[$bid]['bldg_cost'][3]='I4x3000';
$map_buildings[$bid]['bldg_cost'][4]='I4x4000';
$map_buildings[$bid]['bldg_cost'][5]='I4x5000';
$map_buildings[$bid]['bldg_cost'][6]='I4x6000';
$map_buildings[$bid]['bldg_cost'][7]='I4x7000';
$map_buildings[$bid]['bldg_cost'][8]='I4x8000';
$map_buildings[$bid]['bldg_cost'][9]='I4x9000';
$map_buildings[$bid]['bldg_in_type']=array(1); //Welttyp
$map_buildings[$bid]['need_tech']=148;
$map_buildings[$bid]['need_field_typ']=array(3);
$map_buildings[$bid]['production_amount']=array(4,5,6,8,10,12,14,16,18,20);
$map_buildings[$bid]['bldg_filter_tag']='f_mein';

//Dulexit-Industrie
$bid++;
$map_buildings[$bid]['name']='Dulexit-Industrie';
$map_buildings[$bid]['bldg_time']=4000;
$map_buildings[$bid]['bldg_cost'][0]='I5x3000';
$map_buildings[$bid]['bldg_cost'][1]='I5x1000';
$map_buildings[$bid]['bldg_cost'][2]='I5x2000';
$map_buildings[$bid]['bldg_cost'][3]='I5x3000';
$map_buildings[$bid]['bldg_cost'][4]='I5x4000';
$map_buildings[$bid]['bldg_cost'][5]='I5x5000';
$map_buildings[$bid]['bldg_cost'][6]='I5x6000';
$map_buildings[$bid]['bldg_cost'][7]='I5x7000';
$map_buildings[$bid]['bldg_cost'][8]='I5x8000';
$map_buildings[$bid]['bldg_cost'][9]='I5x9000';
$map_buildings[$bid]['bldg_in_type']=array(1); //Welttyp
$map_buildings[$bid]['need_tech']=149;
$map_buildings[$bid]['need_field_typ']=array(4);
$map_buildings[$bid]['production_amount']=array(4,5,6,8,10,12,14,16,18,20);
$map_buildings[$bid]['bldg_filter_tag']='f_duin';

//Tekranit-Industrie
$bid++;
$map_buildings[$bid]['name']='Tekranit-Industrie';
$map_buildings[$bid]['bldg_time']=5000;
$map_buildings[$bid]['bldg_cost'][0]='I6x4000';
$map_buildings[$bid]['bldg_cost'][1]='I6x1000';
$map_buildings[$bid]['bldg_cost'][2]='I6x2000';
$map_buildings[$bid]['bldg_cost'][3]='I6x3000';
$map_buildings[$bid]['bldg_cost'][4]='I6x4000';
$map_buildings[$bid]['bldg_cost'][5]='I6x5000';
$map_buildings[$bid]['bldg_cost'][6]='I6x6000';
$map_buildings[$bid]['bldg_cost'][7]='I6x7000';
$map_buildings[$bid]['bldg_cost'][8]='I6x8000';
$map_buildings[$bid]['bldg_cost'][9]='I6x9000';
$map_buildings[$bid]['bldg_in_type']=array(1); //Welttyp
$map_buildings[$bid]['need_tech']=150;
$map_buildings[$bid]['need_field_typ']=array(5);
$map_buildings[$bid]['production_amount']=array(4,5,6,8,10,12,14,16,18,20);
$map_buildings[$bid]['bldg_filter_tag']='f_tein';


//Ylesenium-Industrie
$bid++;
$map_buildings[$bid]['name']='Ylesenium-Industrie';
$map_buildings[$bid]['bldg_time']=6000;
$map_buildings[$bid]['bldg_cost'][0]='I7x5000';
$map_buildings[$bid]['bldg_cost'][1]='I7x1000';
$map_buildings[$bid]['bldg_cost'][2]='I7x2000';
$map_buildings[$bid]['bldg_cost'][3]='I7x3000';
$map_buildings[$bid]['bldg_cost'][4]='I7x4000';
$map_buildings[$bid]['bldg_cost'][5]='I7x5000';
$map_buildings[$bid]['bldg_cost'][6]='I7x6000';
$map_buildings[$bid]['bldg_cost'][7]='I7x7000';
$map_buildings[$bid]['bldg_cost'][8]='I7x8000';
$map_buildings[$bid]['bldg_cost'][9]='I7x9000';
$map_buildings[$bid]['bldg_in_type']=array(1); //Welttyp
$map_buildings[$bid]['need_tech']=151;
$map_buildings[$bid]['need_field_typ']=array(6);
$map_buildings[$bid]['production_amount']=array(4,5,6,8,10,12,14,16,18,20);
$map_buildings[$bid]['bldg_filter_tag']='f_ylin';

//Serodium-Industrie
$bid++;
$map_buildings[$bid]['name']='Serodium-Industrie';
$map_buildings[$bid]['bldg_time']=7000;
$map_buildings[$bid]['bldg_cost'][0]='I8x6000';
$map_buildings[$bid]['bldg_cost'][1]='I8x1000';
$map_buildings[$bid]['bldg_cost'][2]='I8x2000';
$map_buildings[$bid]['bldg_cost'][3]='I8x3000';
$map_buildings[$bid]['bldg_cost'][4]='I8x4000';
$map_buildings[$bid]['bldg_cost'][5]='I8x5000';
$map_buildings[$bid]['bldg_cost'][6]='I8x6000';
$map_buildings[$bid]['bldg_cost'][7]='I8x7000';
$map_buildings[$bid]['bldg_cost'][8]='I8x8000';
$map_buildings[$bid]['bldg_cost'][9]='I8x9000';
$map_buildings[$bid]['bldg_in_type']=array(1); //Welttyp
$map_buildings[$bid]['need_tech']=152;
$map_buildings[$bid]['need_field_typ']=array(7);
$map_buildings[$bid]['production_amount']=array(4,5,6,8,10,12,14,16,18,20);
$map_buildings[$bid]['bldg_filter_tag']='f_sein';

//Rowalganium-Industrie
$bid++;
$map_buildings[$bid]['name']='Rowalganium-Industrie';
$map_buildings[$bid]['bldg_time']=8000;
$map_buildings[$bid]['bldg_cost'][0]='I9x7000';
$map_buildings[$bid]['bldg_cost'][1]='I9x1000';
$map_buildings[$bid]['bldg_cost'][2]='I9x2000';
$map_buildings[$bid]['bldg_cost'][3]='I9x3000';
$map_buildings[$bid]['bldg_cost'][4]='I9x4000';
$map_buildings[$bid]['bldg_cost'][5]='I9x5000';
$map_buildings[$bid]['bldg_cost'][6]='I9x6000';
$map_buildings[$bid]['bldg_cost'][7]='I9x7000';
$map_buildings[$bid]['bldg_cost'][8]='I9x8000';
$map_buildings[$bid]['bldg_cost'][9]='I9x9000';
$map_buildings[$bid]['bldg_in_type']=array(1); //Welttyp
$map_buildings[$bid]['need_tech']=153;
$map_buildings[$bid]['need_field_typ']=array(8);
$map_buildings[$bid]['production_amount']=array(4,5,6,8,10,12,14,16,18,20);
$map_buildings[$bid]['bldg_filter_tag']='f_roin';

//Sextagit-Industrie
$bid++;
$map_buildings[$bid]['name']='Sextagit-Industrie';
$map_buildings[$bid]['bldg_time']=9000;
$map_buildings[$bid]['bldg_cost'][0]='I10x8000';
$map_buildings[$bid]['bldg_cost'][1]='I10x1000';
$map_buildings[$bid]['bldg_cost'][2]='I10x2000';
$map_buildings[$bid]['bldg_cost'][3]='I10x3000';
$map_buildings[$bid]['bldg_cost'][4]='I10x4000';
$map_buildings[$bid]['bldg_cost'][5]='I10x5000';
$map_buildings[$bid]['bldg_cost'][6]='I10x6000';
$map_buildings[$bid]['bldg_cost'][7]='I10x7000';
$map_buildings[$bid]['bldg_cost'][8]='I10x8000';
$map_buildings[$bid]['bldg_cost'][9]='I10x9000';
$map_buildings[$bid]['bldg_in_type']=array(1); //Welttyp
$map_buildings[$bid]['need_tech']=154;
$map_buildings[$bid]['need_field_typ']=array(9);
$map_buildings[$bid]['production_amount']=array(4,5,6,8,10,12,14,16,18,20);
$map_buildings[$bid]['bldg_filter_tag']='f_sexin';

//Octagium-Industrie
$bid++;
$map_buildings[$bid]['name']='Octagium-Industrie';
$map_buildings[$bid]['bldg_time']=10000;
$map_buildings[$bid]['bldg_cost'][0]='I11x9000';
$map_buildings[$bid]['bldg_cost'][1]='I11x1000';
$map_buildings[$bid]['bldg_cost'][2]='I11x2000';
$map_buildings[$bid]['bldg_cost'][3]='I11x3000';
$map_buildings[$bid]['bldg_cost'][4]='I11x4000';
$map_buildings[$bid]['bldg_cost'][5]='I11x5000';
$map_buildings[$bid]['bldg_cost'][6]='I11x6000';
$map_buildings[$bid]['bldg_cost'][7]='I11x7000';
$map_buildings[$bid]['bldg_cost'][8]='I11x8000';
$map_buildings[$bid]['bldg_cost'][9]='I11x9000';
$map_buildings[$bid]['bldg_in_type']=array(1); //Welttyp
$map_buildings[$bid]['need_tech']=155;
$map_buildings[$bid]['need_field_typ']=array(10);
$map_buildings[$bid]['production_amount']=array(4,5,6,8,10,12,14,16,18,20);
$map_buildings[$bid]['bldg_filter_tag']='f_ocin';

//Omega-Fabrik
$bid++;
$map_buildings[$bid]['name']='Omega-Fabrik';
$map_buildings[$bid]['bldg_time']=2000;
$map_buildings[$bid]['bldg_cost'][0]='I4x10000';
$map_buildings[$bid]['bldg_cost'][1]='I4x10000';
$map_buildings[$bid]['bldg_cost'][2]='I4x20000';
$map_buildings[$bid]['bldg_cost'][3]='I4x30000';
$map_buildings[$bid]['bldg_cost'][4]='I4x40000';
$map_buildings[$bid]['bldg_cost'][5]='I4x50000';
$map_buildings[$bid]['bldg_cost'][6]='I4x60000';
$map_buildings[$bid]['bldg_cost'][7]='I4x70000';
$map_buildings[$bid]['bldg_cost'][8]='I4x80000';
$map_buildings[$bid]['bldg_cost'][9]='I4x90000';
$map_buildings[$bid]['bldg_in_type']=array(1); //Welttyp
//$map_buildings[$bid]['need_tech']=147;
$map_buildings[$bid]['need_field_typ']=array(0);
$map_buildings[$bid]['production_capacity']=array(1,2,3,4,5,6,7,8,9,10);
$map_buildings[$bid]['factory_id']=24;
$map_buildings[$bid]['bldg_filter_tag']='f_fab24';

//Psi-Fabrik
$bid++;
$map_buildings[$bid]['name']='Psi-Fabrik';
$map_buildings[$bid]['bldg_time']=4000;
$map_buildings[$bid]['bldg_cost'][0]='I6x10000';
$map_buildings[$bid]['bldg_cost'][1]='I6x10000';
$map_buildings[$bid]['bldg_cost'][2]='I6x20000';
$map_buildings[$bid]['bldg_cost'][3]='I6x30000';
$map_buildings[$bid]['bldg_cost'][4]='I6x40000';
$map_buildings[$bid]['bldg_cost'][5]='I6x50000';
$map_buildings[$bid]['bldg_cost'][6]='I6x60000';
$map_buildings[$bid]['bldg_cost'][7]='I6x70000';
$map_buildings[$bid]['bldg_cost'][8]='I6x80000';
$map_buildings[$bid]['bldg_cost'][9]='I6x90000';
$map_buildings[$bid]['bldg_in_type']=array(1); //Welttyp
//$map_buildings[$bid]['need_tech']=147;
$map_buildings[$bid]['need_field_typ']=array(0);
$map_buildings[$bid]['production_capacity']=array(1,2,3,4,5,6,7,8,9,10);
$map_buildings[$bid]['factory_id']=23;
$map_buildings[$bid]['bldg_filter_tag']='f_fab23';

//Chi-Fabrik
$bid++;
$map_buildings[$bid]['name']='Chi-Fabrik';
$map_buildings[$bid]['bldg_time']=6000;
$map_buildings[$bid]['bldg_cost'][0]='I8x10000';
$map_buildings[$bid]['bldg_cost'][1]='I8x10000';
$map_buildings[$bid]['bldg_cost'][2]='I8x20000';
$map_buildings[$bid]['bldg_cost'][3]='I8x30000';
$map_buildings[$bid]['bldg_cost'][4]='I8x40000';
$map_buildings[$bid]['bldg_cost'][5]='I8x50000';
$map_buildings[$bid]['bldg_cost'][6]='I8x60000';
$map_buildings[$bid]['bldg_cost'][7]='I8x70000';
$map_buildings[$bid]['bldg_cost'][8]='I8x80000';
$map_buildings[$bid]['bldg_cost'][9]='I8x90000';
$map_buildings[$bid]['bldg_in_type']=array(1); //Welttyp
//$map_buildings[$bid]['need_tech']=147;
$map_buildings[$bid]['need_field_typ']=array(0);
$map_buildings[$bid]['production_capacity']=array(1,2,3,4,5,6,7,8,9,10);
$map_buildings[$bid]['factory_id']=22;
$map_buildings[$bid]['bldg_filter_tag']='f_fab22';

//Phi-Fabrik
$bid++;
$map_buildings[$bid]['name']='Phi-Fabrik';
$map_buildings[$bid]['bldg_time']=8000;
$map_buildings[$bid]['bldg_cost'][0]='I10x10000';
$map_buildings[$bid]['bldg_cost'][1]='I10x10000';
$map_buildings[$bid]['bldg_cost'][2]='I10x20000';
$map_buildings[$bid]['bldg_cost'][3]='I10x30000';
$map_buildings[$bid]['bldg_cost'][4]='I10x40000';
$map_buildings[$bid]['bldg_cost'][5]='I10x50000';
$map_buildings[$bid]['bldg_cost'][6]='I10x60000';
$map_buildings[$bid]['bldg_cost'][7]='I10x70000';
$map_buildings[$bid]['bldg_cost'][8]='I10x80000';
$map_buildings[$bid]['bldg_cost'][9]='I10x90000';
$map_buildings[$bid]['bldg_in_type']=array(1); //Welttyp
//$map_buildings[$bid]['need_tech']=147;
$map_buildings[$bid]['need_field_typ']=array(0);
$map_buildings[$bid]['production_capacity']=array(1,2,3,4,5,6,7,8,9,10);
$map_buildings[$bid]['factory_id']=21;
$map_buildings[$bid]['bldg_filter_tag']='f_fab21';

//Ypsilon-Fabrik
$bid++;
$map_buildings[$bid]['name']='Ypsilon-Fabrik';
$map_buildings[$bid]['bldg_time']=10000;
$map_buildings[$bid]['bldg_cost'][0]='I12x10000';
$map_buildings[$bid]['bldg_cost'][1]='I12x10000';
$map_buildings[$bid]['bldg_cost'][2]='I12x20000';
$map_buildings[$bid]['bldg_cost'][3]='I12x30000';
$map_buildings[$bid]['bldg_cost'][4]='I12x40000';
$map_buildings[$bid]['bldg_cost'][5]='I12x50000';
$map_buildings[$bid]['bldg_cost'][6]='I12x60000';
$map_buildings[$bid]['bldg_cost'][7]='I12x70000';
$map_buildings[$bid]['bldg_cost'][8]='I12x80000';
$map_buildings[$bid]['bldg_cost'][9]='I12x90000';
$map_buildings[$bid]['bldg_in_type']=array(1); //Welttyp
//$map_buildings[$bid]['need_tech']=147;
$map_buildings[$bid]['need_field_typ']=array(0);
$map_buildings[$bid]['production_capacity']=array(1,2,3,4,5,6,7,8,9,10);
$map_buildings[$bid]['factory_id']=20;
$map_buildings[$bid]['bldg_filter_tag']='f_fab20';


//die Daten global verfügbar machen
$GLOBALS['map_buildings']=$map_buildings;

//////////////////////////////////////////////////////////////////
// Feldarten
//////////////////////////////////////////////////////////////////
$map_field_typ=array();

$map_field_typ[0]['name']='-';
$map_field_typ[1]['name']='Eisen';
$map_field_typ[1]['filter_tag']='f_eiin';
$map_field_typ[2]['name']='Titan';
$map_field_typ[2]['filter_tag']='f_tiin';
$map_field_typ[3]['name']='Mexit';
$map_field_typ[3]['filter_tag']='f_mein';
$map_field_typ[4]['name']='Dulexit';
$map_field_typ[4]['filter_tag']='f_duin';
$map_field_typ[5]['name']='Tekranit';
$map_field_typ[5]['filter_tag']='f_tein';
$map_field_typ[6]['name']='Ylesenium';
$map_field_typ[6]['filter_tag']='f_ylin';
$map_field_typ[7]['name']='Serodium';
$map_field_typ[7]['filter_tag']='f_sein';
$map_field_typ[8]['name']='Rowalganium';
$map_field_typ[8]['filter_tag']='f_roin';
$map_field_typ[9]['name']='Sextagit';
$map_field_typ[9]['filter_tag']='f_sexin';
$map_field_typ[10]['name']='Octagium';
$map_field_typ[10]['filter_tag']='f_ocin';


$GLOBALS['map_field_typ']=$map_field_typ;

//////////////////////////////////////////////////////////////////
// Feldblocker
//////////////////////////////////////////////////////////////////
$map_field_blocker=array();

$map_field_blocker[0]['name']='keine';
$map_field_blocker[1]['name']='Strahlung';
$map_field_blocker[2]['name']='Viren';
$map_field_blocker[3]['name']='Bakterien';
$map_field_blocker[4]['name']='wilde Tiere';
$map_field_blocker[5]['name']='Schlingpflanzen';

$GLOBALS['map_field_blocker']=$map_field_blocker;

?>