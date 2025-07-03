<?php
if(isset($_SESSION['ums_gpfad']))$ums_gpfad=$_SESSION['ums_gpfad'];
if(isset($_SESSION['ums_rasse']))$ums_rasse=$_SESSION['ums_rasse'];
//behandlung außerhalb der session
if(!isset($ums_gpfad)){
	$ums_gpfad=$sv_image_server_list[0];
}
//check auf SSL
if(!empty($_SERVER['HTTPS'])){
	$ums_gpfad=str_replace('http://','https://',$ums_gpfad);
}

if(!isset($_SESSION['ums_rasse'])){
	$_SESSION['ums_rasse']=1;
}

if(isset($eftacss) && $eftacss==1){
	echo '<link rel="stylesheet" type="text/css" href="'.$ums_gpfad.'e.css">';
}elseif(isset($soucss) && $soucss==1){
	echo '<link rel="stylesheet" type="text/css" href="'.$ums_gpfad.'s.css">';
}elseif(isset($newcss) && $newcss==1){
	//echo '<link rel="stylesheet" type="text/css" href="'.$ums_gpfad.'d.css">';
	echo '<link rel="stylesheet" type="text/css" href="g/d.css">';
	echo '<script type="text/javascript" src="js/jquery.min.js"></script>';
}else{ //de-css laden
	//unterscheiden ob spielbereich, chat oder menu und das jeweils mobil/nicht mobil
	if(isset($loadcssmenu) && $loadcssmenu==1)	{
		echo '<link rel="stylesheet" type="text/css" href="'.$ums_gpfad.'m'.$_SESSION['ums_rasse'].'.css"><link rel="stylesheet" type="text/css" href="'.$ums_gpfad.'f'.$_SESSION['ums_rasse'].'.css">';
	}
  	elseif(isset($loadcsschat) && $loadcsschat==1)
  	{
  		if(isset($_SESSION['ums_mobi']) && $_SESSION['ums_mobi']==1){
			echo '<link rel="stylesheet" type="text/css" href="'.$ums_gpfad.'c'.$_SESSION['ums_rasse'].'_m.css">';
			echo '<meta name="viewport" content="width=620">';
		}else{
			echo '<link rel="stylesheet" type="text/css" href="'.$ums_gpfad.'c'.$_SESSION['ums_rasse'].'.css">';
		}
  	}
	else //spielbereich
	{
  		if(isset($_SESSION['ums_mobi']) && $_SESSION['ums_mobi']==1){
			echo '<link rel="stylesheet" type="text/css" href="'.$ums_gpfad.'f'.$_SESSION['ums_rasse'].'_m.css">';
			echo '<meta name="viewport" content="width=620">';
		}else{
			echo '<link rel="stylesheet" type="text/css" href="'.$ums_gpfad.'f'.$_SESSION['ums_rasse'].'.css">';
		}
  	}
	

	if(isset($GLOBALS['deactivate_old_design']) && $GLOBALS['deactivate_old_design']==true){
		echo '<script type="text/javascript" src="js/jquery.min.js"></script>';
	}else{
		echo '
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript" src="js/jquery-migrate.min.js"></script>
		<script type="text/javascript" src="js/jquery.dimensions.min.js"></script>
		<script type="text/javascript" src="js/jquery.tooltip.min.js"></script>
		';
	}
}
echo '<meta charset="UTF-8">';
?>
