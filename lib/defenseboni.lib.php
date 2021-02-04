<?php
//je mehr exp die t�rme haben, desto mehr boni kann man erhalten
//definition der boni nach rang

//erstmal alles zur�cksetzen
unset($defense_bonus_feuerkraft);

//definitionen die immer aktiv sind
//feuerkraftbonus nach stufe
$defense_bonus_feuerkraft = array ($defense_level+1, $defense_level+1);//chance, gr��e in prozent
//st�rkung des planetaren schildes
$defense_bonus_ps=$defense_level; //bonus in prozent

//definitionen nach rang
switch ($defense_level){
	case 0:
	$defense_bonus_buildtime = 0;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;
	case 1:
	$defense_bonus_buildtime = 10;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;
	case 2:
	$defense_bonus_buildtime = 20;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;
	case 3:
	$defense_bonus_buildtime = 25;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;
	case 4:
	$defense_bonus_buildtime = 30;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;
	case 5:
	$defense_bonus_buildtime = 35;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;
	case 6:
	$defense_bonus_buildtime = 40;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;
	case 7:
	$defense_bonus_buildtime = 43;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;
	case 8:
	$defense_bonus_buildtime = 46;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;
	case 9:
	$defense_bonus_buildtime = 49;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;
	case 10:
	$defense_bonus_buildtime = 52;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;
	case 11:
	$defense_bonus_buildtime = 55;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;
	case 12:
	$defense_bonus_buildtime = 58;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;
	case 13:
	$defense_bonus_buildtime = 61;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;
	case 14:
	$defense_bonus_buildtime = 64;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;
	case 15:
	$defense_bonus_buildtime = 66;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;
	case 16:
	$defense_bonus_buildtime = 68;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;
	case 17:
	$defense_bonus_buildtime = 70;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;
	case 18:
	$defense_bonus_buildtime = 72;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;
	case 19:
	$defense_bonus_buildtime = 74;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;
	case 20:
	$defense_bonus_buildtime = 76;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;
	case 21:
	$defense_bonus_buildtime = 78;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;
	case 22:
	$defense_bonus_buildtime = 80;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;
	case 23:
	$defense_bonus_buildtime = 82;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;
	case 24:
	$defense_bonus_buildtime = 84;//bauzeitreduzierung nach stufe, gr��e in prozent
	break;

}

?>