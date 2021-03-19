<?php
switch ($inbldg)
{
  case 1:
	include "eftadata/source/efta_city.php";
  break;
  case 8:
	include "eftadata/source/efta_ht.php";
  break;
  case 15:
	include "eftadata/src_bldg/efta_bldg_15.php";
  break;
  case 16:
  	include "eftadata/src_bldg/efta_bldg_16.php";
  break;
  case 17:
  	include "eftadata/src_bldg/efta_bldg_17.php";
  break;  
  case 21:
  	include "eftadata/src_bldg/efta_bldg_21.php";
  break;  
  case 22:
  	include "eftadata/src_bldg/efta_bldg_22.php";
  break;  
}
?>
