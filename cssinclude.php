<?php
echo '<meta charset="UTF-8">';

//behandlung außerhalb der session
if(!isset($_SESSION['ums_rasse'])){
	$_SESSION['ums_rasse']=1;
}

if(isset($_SESSION['ums_mobi']) && $_SESSION['ums_mobi']==1){
	echo '<link rel="stylesheet" type="text/css" href="gp/f'.$_SESSION['ums_rasse'].'_m.css">';
	echo '<meta name="viewport" content="width=620">';
}else{
	echo '<link rel="stylesheet" type="text/css" href="gp/f'.$_SESSION['ums_rasse'].'.css">';
}

if(isset($GLOBALS['deactivate_old_design']) && $GLOBALS['deactivate_old_design']==true){
	echo '<script type="text/javascript" src="js/jquery-3.7.1.min.js"></script>';
}else{
	echo '
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery-migrate.min.js"></script>
	<script type="text/javascript" src="js/jquery.dimensions.min.js"></script>
	<script type="text/javascript" src="js/jquery.tooltip.min.js"></script>
	';
}
