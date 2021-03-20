<?php 
include "soudata/defs/resources.inc.php";

//auslesen wieviele spieler geworben worden sind
$geworben=get_player_advertised($ums_owner_id);

echo '<br>';

rahmen0_oben();
echo '<br>';


if($_REQUEST['getgift']==1)
{

  //transaktionsbeginn
  if (setLock($_SESSION["sou_user_id"]))
  {
	//auslesen ob er das geschenk schon bekommen hat
	$db_daten=mysql_query("SELECT * FROM sou_user_data WHERE user_id='$_SESSION[sou_user_id]'",$soudb);  	
    $row = mysql_fetch_array($db_daten);
    if($row[dailygift]==1)
    {
      //in der db und session das geschenk für den tag deaktivieren
      $player_dailygift=0;  
      mysql_query("UPDATE sou_user_data SET dailygift=0 WHERE user_id='$player_user_id'",$soudb);
    
	  //feststellen welches geschenk er bekommt
	  change_credits($ums_user_id, 1, 'EA-dailygift');
	  $baosin=1+$geworben;
	  change_baosin($player_user_id, $baosin);
  
      
      
      //info an den spieler
      rahmen2_oben();
      echo '<table width="100%" class="cell"><tr><td align="left"><font color="#00FF00">Du hast das Geschenk erhalten:</font><br>1 <img src="'.$gpfad.'a8.gif" alt="Credits" title="Credits"><br>'.$baosin.' <img src="'.$gpfad.'a28.gif" alt="Baosin" title="Baosin"></a></td></tr></table>';
      rahmen2_unten();
      echo '<br>';
    }

    //lock wieder entfernen
    $erg = releaseLock($_SESSION["sou_user_id"]); //Lösen des Locks und Ergebnisabfrage
    if ($erg)
    {
      //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
    }
    else
    {
      print("Datensatz Nr. ".$_SESSION["sou_user_id"]." konnte nicht entsperrt werden!<br><br><br>");
    }
  }//lock ende
}      

$routput='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
<td width="120"><a href="sou_main.php?action=systempage"><div class="b1">System</div></a></td>
<td><b>Dein t&auml;gliches Geschenk</b></td>
<td width="120">&nbsp;</td>
</tr></table>';
rahmen1_oben($routput);


echo '<table width="100%" border="0" cellpadding="0" cellspacing="0">';
if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
echo '<tr><td class="'.$bg.'" align="center">
Du bekommst jeden Tag 1 Credit und 1 Baosin geschenkt. Die Anzahl der Baosin erh&ouml;ht sich f&uuml;r jeden geworbenen Spieler, der in den letzten 3 Tagen aktiv war, um einen weiteren Punkt. Ein aktiver Premiumaccount gilt als ein geworbener Spieler.  
Von Dir geworbene aktive Spieler (inkl. Premiumaccount): '.$geworben.'<br><br>';
if($player_dailygift==1)
echo '<a href="sou_main.php?action=dailygiftpage&getgift=1"><div class="b1">annehmen</div></a></td></tr>';
else echo 'Du hast heute schon Dein Geschenk erhalten. Morgen kannst Du Dir ein neues Geschenk abholen kommen.';
echo '</table>';

rahmen1_unten();
echo '<br>';
rahmen0_unten();
echo '<br>';


//boni: modul, charbergbaubonus, zastari, dunkle materie, spezialrohstoffe, credits
/*
$advertised_need = array (0, 1, 2, 3, 5, 10, 15, 25);

//geschenk 0

$gname[0]='Kleines Geschenk';
$gname[1]='Mittleres Geschenk';
$gname[2]='Gr&ouml;ßeres Geschenk';
$gname[3]='Großes Geschenk';
$gname[4]='Riesiges Geschenk';
$gname[5]='M&auml;chtiges Geschenk';
$gname[6]='Ph&auml;nomenales Geschenk';
$gname[7]='Unglaubliches Geschenk';

//echo '<a href="sou_main.php?action=dailygiftpage"><img border="0" src="'.$gpfad.'geschenkbg.png" onMouseOver="stm(atip['.$ttip.'],Style[0])" onMouseOut="htold()" width="448" height="448"></a>';
//echo '<a href="sou_main.php?action=dailygiftpage"><img border="0" src="'.$gpfad.'geschenkbg.png" onMouseOver="stm(atip['.$ttip.'],Style[0])" onMouseOut="htold()" width="448" height="448"></a>';



$gbeschreibung[0]='
<br>+1.000 Zastari
<br>10 Minuten Charakterbergbauerfahrung Eisen';
$gbeschreibung[1]='
<br>+1 Credit
<br>+2.000 Zastari
<br>+20 Minuten Charakterbergbauerfahrung Eisen
<br>+10 Minuten Charakterbergbauerfahrung Titan';
$gbeschreibung[2]='
<br>+2 Credit
<br>+4.000 Zastari
<br>30 Minuten Charakterbergbauerfahrung Eisen
<br>20 Minuten Charakterbergbauerfahrung Titan
<br>10 Minuten Charakterbergbauerfahrung Mexit';
$gbeschreibung[3]='
<br>+3 Credit
<br>+8.000 Zastari
<br>+100 Dunkle Materie
<br>40 Minuten Charakterbergbauerfahrung Eisen
<br>30 Minuten Charakterbergbauerfahrung Titan
<br>20 Minuten Charakterbergbauerfahrung Mexit
<br>10 Minuten Charakterbergbauerfahrung Dulexit';
$gbeschreibung[4]='
<br>+4 Credit
<br>+16.000 Zastari
<br>+200 Dunkle Materie
<br>+1x '.$specialres_def[0][1].'
<br>50 Minuten Charakterbergbauerfahrung Eisen
<br>40 Minuten Charakterbergbauerfahrung Titan
<br>30 Minuten Charakterbergbauerfahrung Mexit
<br>20 Minuten Charakterbergbauerfahrung Dulexit
<br>10 Minuten Charakterbergbauerfahrung Tekranit';
$gbeschreibung[5]='
<br>+5 Credit
<br>+32.000 Zastari
<br>+400 Dunkle Materie
<br>+2x '.$specialres_def[0][1].'
<br>+1x '.$specialres_def[1][1].'
<br>60 Minuten Charakterbergbauerfahrung Eisen
<br>50 Minuten Charakterbergbauerfahrung Titan
<br>40 Minuten Charakterbergbauerfahrung Mexit
<br>30 Minuten Charakterbergbauerfahrung Dulexit
<br>20 Minuten Charakterbergbauerfahrung Tekranit
<br>10 Minuten Charakterbergbauerfahrung Ylesenium';
$gbeschreibung[6]='
<br>+6 Credit
<br>+64.000 Zastari
<br>+800 Dunkle Materie
<br>+3x '.$specialres_def[0][1].'
<br>+2x '.$specialres_def[1][1].'
<br>+1x '.$specialres_def[2][1].'
<br>70 Minuten Charakterbergbauerfahrung Eisen
<br>60 Minuten Charakterbergbauerfahrung Titan
<br>50 Minuten Charakterbergbauerfahrung Mexit
<br>40 Minuten Charakterbergbauerfahrung Dulexit
<br>30 Minuten Charakterbergbauerfahrung Tekranit
<br>20 Minuten Charakterbergbauerfahrung Ylesenium
<br>10 Minuten Charakterbergbauerfahrung Serodium
<br>10 Minuten Charakterbergbauerfahrung Rowalganium';
$gbeschreibung[7]='
<br>7 Credit
<br>128.000 Zastari
<br>1.600 Dunkle Materie
<br>4x '.$specialres_def[0][1].'
<br>3x '.$specialres_def[1][1].'
<br>2x '.$specialres_def[2][1].'
<br>1x '.$specialres_def[3][1].'
<br>80 Minuten Charakterbergbauerfahrung Eisen
<br>70 Minuten Charakterbergbauerfahrung Titan
<br>60 Minuten Charakterbergbauerfahrung Mexit
<br>50 Minuten Charakterbergbauerfahrung Dulexit
<br>40 Minuten Charakterbergbauerfahrung Tekranit
<br>30 Minuten Charakterbergbauerfahrung Ylesenium
<br>20 Minuten Charakterbergbauerfahrung Serodium
<br>20 Minuten Charakterbergbauerfahrung Rowalganium
<br>10 Minuten Charakterbergbauerfahrung Sextagit
<br>10 Minuten Charakterbergbauerfahrung Octagium';


echo '<br>';

rahmen0_oben();
echo '<br>';

///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
// der spieler fordert das geschenk an
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
if($_REQUEST[getgift]==1)
{
  //transaktionsbeginn
  if (setLock($_SESSION["sou_user_id"]))
  {
	//auslesen ob er das geschenk schon bekommen hat
	$db_daten=mysql_query("SELECT * FROM sou_user_data WHERE user_id='$_SESSION[sou_user_id]'",$soudb);  	
    $row = mysql_fetch_array($db_daten);
    if($row[dailygift]==1)
    {
      //in der db und session das geschenk für den tag deaktivieren
      $player_dailygift=0;  
      mysql_query("UPDATE sou_user_data SET dailygift=0 WHERE user_id='$player_user_id'",$soudb);
    
	  //feststellen welches geschenk er bekommt
	  for($i=0;$i<count($gname);$i++)
	  {
  		if($geworben>=$advertised_need[$i])
  		{
    	  if($geworben>=$advertised_need[$i+1] AND $i<count($gname)-1) //grau
    	  {
    	  }
    	  else //grün
    	  {
    	    //schleife beenden, da das ziel gefunden worden ist
    	    break;
          } 
        }
        else  //rot 
        {
        }
      }

      //geschenk hinterlegen
      
      switch($i)
      {
        case 0:
		change_money($player_user_id, 1000);      
		change_skill(0, 10);
        break;

        case 1:
        change_credits($ums_user_id, 1, 'EA-dailygift');
		change_money($player_user_id, 2000);      
		change_skill(0, 20);
		change_skill(1, 10);
        break;
        
        case 2:
        change_credits($ums_user_id, 2, 'EA-dailygift');
		change_money($player_user_id, 4000);      
		change_skill(0, 30);
		change_skill(1, 20);
		change_skill(2, 10);
        break;

        case 3:
        change_credits($ums_user_id, 3, 'EA-dailygift');
		change_money($player_user_id, 8000);
		change_darkmatter($player_user_id, 100);
		change_skill(0, 40);
		change_skill(1, 30);
		change_skill(2, 20);
		change_skill(3, 10);
        break;

        case 4:
        change_credits($ums_user_id, 4, 'EA-dailygift');
		change_money($player_user_id, 16000);
		change_darkmatter($player_user_id, 200);
		change_specialres($player_user_id, 0, 1);
		change_skill(0, 50);
		change_skill(1, 40);
		change_skill(2, 30);
		change_skill(3, 20);
		change_skill(4, 10);
        break;

        case 5:
        change_credits($ums_user_id, 5, 'EA-dailygift');
		change_money($player_user_id, 32000);
		change_darkmatter($player_user_id, 400);
		change_specialres($player_user_id, 0, 2);
		change_specialres($player_user_id, 1, 1);
		change_skill(0, 60);
		change_skill(1, 50);
		change_skill(2, 40);
		change_skill(3, 30);
		change_skill(4, 20);
		change_skill(5, 10);
        break;

        case 6:
        change_credits($ums_user_id, 6, 'EA-dailygift');
		change_money($player_user_id, 64000);
		change_darkmatter($player_user_id, 800);
		change_specialres($player_user_id, 0, 3);
		change_specialres($player_user_id, 1, 2);
		change_specialres($player_user_id, 2, 1);
		change_skill(0, 70);
		change_skill(1, 60);
		change_skill(2, 50);
		change_skill(3, 40);
		change_skill(4, 30);
		change_skill(5, 20);
		change_skill(6, 10);
		change_skill(7, 10);
        break;

        case 7:
        change_credits($ums_user_id, 7, 'EA-dailygift');
		change_money($player_user_id, 128000);
		change_darkmatter($player_user_id, 1600);
		change_specialres($player_user_id, 0, 4);
		change_specialres($player_user_id, 1, 3);
		change_specialres($player_user_id, 2, 2);
		change_specialres($player_user_id, 3, 1);
		change_skill(0, 80);
		change_skill(1, 70);
		change_skill(2, 60);
		change_skill(3, 50);
		change_skill(4, 40);
		change_skill(5, 30);
		change_skill(6, 20);
		change_skill(7, 20);
		change_skill(8, 10);
		change_skill(9, 10);
        break;        
		
        default:
          echo 'Error 1';
        break;
      }

    
      //info an den spieler
      rahmen2_oben();
      echo '<table width="100%" class="cell"><tr><td align="center"><font color="#00FF00">Du hast das Geschenk erhalten.</font></a></td></tr></table>';
      rahmen2_unten();
      echo '<br>';
    }

    //lock wieder entfernen
    $erg = releaseLock($_SESSION["sou_user_id"]); //Lösen des Locks und Ergebnisabfrage
    if ($erg)
    {
      //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
    }
    else
    {
      print("Datensatz Nr. ".$_SESSION["sou_user_id"]." konnte nicht entsperrt werden!<br><br><br>");
    }
  }//lock ende
}

///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
// die geschenke auflisten
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////

$routput='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
<td width="120"><a href="sou_main.php?action=systempage"><div class="b1">System</div></a></td>
<td><b>Dein t&auml;gliches Geschenk</b></td>
<td width="120">&nbsp;</td>
</tr></table>';
rahmen1_oben($routput);


echo '<table width="100%" border="0" cellpadding="0" cellspacing="0">';
if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
echo '<tr><td class="'.$bg.'" align="center">Die Gr&ouml;ße des Geschenkes h&auml;ngt von der Anzahl der von Dir geworbenen aktiven Spieler ab. Von Dir geworbene aktive Spieler: '.$geworben.'</td></tr>';
echo '</table>';

for($i=0;$i<count($gname);$i++)
{
  $link1='';$link2='';
  if($geworben>=$advertised_need[$i])
  {
    if($geworben>=$advertised_need[$i+1] AND $i<count($gname)-1) //grau
    {
      $grafik='bgpic4.png';
      $title=$gname[$i].'§Dir steht ein besseres Geschenk zu.';
    }
    else //grün
    {
      $grafik='bgpic5.png';
      //überprüfen ob man es evtl. schon geholt hatte
      if($player_dailygift==1)
      {
        $title=$gname[$i].'§Dieses Geschenk geh&ouml;rt Dir. Klicke es einfach an, um es zu erhalten.';
        $link1='<a href="sou_main.php?action=dailygiftpage&getgift=1">';$link2='</a>';
      }
      else $title=$gname[$i].'§Du hast heute schon Dein Geschenk erhalten. Morgen kannst Du Dir ein neues Geschenk abholen kommen.';
    } 
  }
  else  //rot 
  {
    $grafik='bgpic6.png';
    $title=$gname[$i].'§F&uuml;r dieses Geschenk hast Du noch nicht genug Spieler geworben.';
  }
   
  echo $link1.'<div id="g'.$i.'" title="'.$title.'" style="background-image: url('.$gpfad.$grafik.'); width: 366px; height: 366px; color: #000000;
  padding: 52px; position: relative; float: left;"><b>'.$gname[$i].'</b> (Ben&ouml;tigte Spieler: '.$advertised_need[$i].')
  <div align="left">'.$gbeschreibung[$i].'</div></div>'.$link2;
}


rahmen1_unten();
echo '<br>';
rahmen0_unten();
echo '<br>';


?>
<script language="javascript">
  $('#g0,#g1,#g2,#g3,#g4,#g5,#g6,#g7').tooltip({ 
      track: true, 
      delay: 0, 
      showURL: false, 
      showBody: "§",
      extraClass: "design1", 
      fixPNG: true,
      opacity: 0.15,
      left: -200
	  });  
</script>
*/
?>