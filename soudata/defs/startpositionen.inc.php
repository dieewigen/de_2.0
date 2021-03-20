<?php
//startpositionen der fraktionen: x,y 
$sv_sou_startposition[0]=array(     0,   1500);//20033

$sv_sou_startposition[1]=array(  1245,    825);//21041

$sv_sou_startposition[2]=array(  1245,   -825);//22051

$sv_sou_startposition[3]=array(     0,  -1500);//23066

$sv_sou_startposition[4]=array( -1245,   -825);//24076

$sv_sou_startposition[5]=array( -1245,    825);//616


//positionen der omega-brücken
//heimatsystem
$sv_omega_position[0][0]=array(     0,   1500);//20033
$sv_omega_position[0][1]=array(  1245,    825);//21041
$sv_omega_position[0][2]=array(  1245,   -825);//22051
$sv_omega_position[0][3]=array(     0,  -1500);//23066
$sv_omega_position[0][4]=array( -1245,   -825);//24076
$sv_omega_position[0][5]=array( -1245,    825);//616

//deep fraction
$sv_omega_position[1][0]=array(     0,  45000);//20033
$sv_omega_position[1][1]=array( 37350,  24750);//21041
$sv_omega_position[1][2]=array( 37350, -24750);//22051
$sv_omega_position[1][3]=array(     0, -45000);//23066
$sv_omega_position[1][4]=array(-37350, -24750);//24076
$sv_omega_position[1][5]=array(-37350,  24750);//616


/*
Deep Fraction 
F1      0,  45000
F2  37350,  24750
F3  37350, -24750
F4      0, -45000
F5 -37350, -24750
F6 -37350,  24750

INSERT INTO sou_map SET x=0,y=45000, pic=1, sysname='Deep Fraction 1', prestige1=1, fraction=1;
INSERT INTO sou_map SET x=37350,y=24750, pic=2, sysname='Deep Fraction 2', prestige2=1, fraction=2;
INSERT INTO sou_map SET x=37350,y=-24750, pic=3, sysname='Deep Fraction 3', prestige3=1, fraction=3;
INSERT INTO sou_map SET x=0,y=-45000, pic=4, sysname='Deep Fraction 4', prestige4=1, fraction=4;
INSERT INTO sou_map SET x=-37350,y=-24750, pic=5, sysname='Deep Fraction 5', prestige5=1, fraction=5;
INSERT INTO sou_map SET x=-37350,y=24750, pic=6, sysname='Deep Fraction 6', prestige6=1, fraction=6;

INSERT INTO sou_map_known SET x=0,y=45000, expltime=1, fraction=1;
INSERT INTO sou_map_known SET x=37350,y=24750, expltime=1, fraction=2;
INSERT INTO sou_map_known SET x=37350,y=-24750, expltime=1, fraction=3;
INSERT INTO sou_map_known SET x=0,y=-45000, expltime=1, fraction=4;
INSERT INTO sou_map_known SET x=-37350,y=-24750, expltime=1, fraction=5;
INSERT INTO sou_map_known SET x=-37350,y=24750, expltime=1, fraction=6;

INSERT INTO `sou_server_main`.`sou_frac_techs` (`tech_id`, `tech_name`, `tech_vor`, `need_tech`, `bldg_build`, `bldg_level`, `modulcost`, `sort_id`, `f1lvl`, `f2lvl`, `f3lvl`, `f4lvl`, `f5lvl`, `f6lvl`, `f1b1`, `f1b2`, `f1b3`, `f1b4`, `f1b5`, `f1b6`, `f1b7`, `f1b8`, `f1b9`, `f1b10`, `f2b1`, `f2b2`, `f2b3`, `f2b4`, `f2b5`, `f2b6`, `f2b7`, `f2b8`, `f2b9`, `f2b10`, `f3b1`, `f3b2`, `f3b3`, `f3b4`, `f3b5`, `f3b6`, `f3b7`, `f3b8`, `f3b9`, `f3b10`, `f4b1`, `f4b2`, `f4b3`, `f4b4`, `f4b5`, `f4b6`, `f4b7`, `f4b8`, `f4b9`, `f4b10`, `f5b1`, `f5b2`, `f5b3`, `f5b4`, `f5b5`, `f5b6`, `f5b7`, `f5b8`, `f5b9`, `f5b10`, `f6b1`, `f6b2`, `f6b3`, `f6b4`, `f6b5`, `f6b6`, `f6b7`, `f6b8`, `f6b9`, `f6b10`, `needspace`, `hasspace`, `needenergy`, `giveenergy`, `canmine`, `givelife`, `givesubspace`, `givecenter`, `givehyperdrive`, `giveresearch`, `giveweapon`, `giveshield`, `canrecover`) VALUES ('60010', 'Virtuelle Omega-Brücke Deep Fraction', 'B1x17;Zx50000000;Dx500;Cx250;10000000x1;1000000x2', '60008', '0', '0', '', '11', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0');

SELECT * FROM `sou_user_data` WHERE fraction=1 and (x>700 or y<300);
SELECT * FROM `sou_user_data` WHERE fraction=5 and (x>-700 or y>200);
SELECT * FROM `sou_user_data` WHERE fraction=6 and (x > -700 OR y < -300);

UPDATE `sou_user_data` SET timer1time=1, x=0,y=45000 WHERE fraction=1 and (x>700 or y<300);
UPDATE `sou_user_data` SET timer1time=1, x=-37350,y=-24750 WHERE fraction=5 and (x>-700 or y>200);
UPDATE `sou_user_data` SET timer1time=1, x=-37350,y=24750 WHERE fraction=6 and (x > -700 OR y < -300);


 */

/*
update sou_map set population = 100000000 where x= 0 and y = 1500;
update sou_map set population = 100000000 where x= 1245 and y = 825;
update sou_map set population = 100000000 where x= 1245 and y = -825;
update sou_map set population = 100000000 where x= 0 and y = -1500;
update sou_map set population = 100000000 where x= -1245 and y = -825;
update sou_map set population = 100000000 where x= -1245 and y = 825;
*/

//galaxiezentren und größe definieren
//x,y, size
$sv_sou_galcenter[0]=array(0,0, 2250);

?>